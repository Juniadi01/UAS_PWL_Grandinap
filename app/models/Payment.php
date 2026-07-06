<?php
class Payment
{
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function create($data)
    {
        $this->db->query('INSERT INTO payments (reservation_id, amount, method, status)
                          VALUES (:rid, :amount, :method, :status)')
                 ->bind(':rid', (int)$data['reservation_id'])
                 ->bind(':amount', $data['amount'])
                 ->bind(':method', $data['method'] ?? 'Transfer Bank')
                 ->bind(':status', 'unpaid');
        $this->db->execute();
        return $this->db->lastId();
    }

    public function findByReservation($reservationId)
    {
        return $this->db->query('SELECT * FROM payments WHERE reservation_id = :rid')
                        ->bind(':rid', (int)$reservationId)->single();
    }

    public function find($id)
    {
        return $this->db->query('SELECT p.*, res.user_id, res.check_in, res.check_out,
                                        r.name AS room_name, u.name AS customer_name
                                 FROM payments p
                                 JOIN reservations res ON p.reservation_id = res.id
                                 JOIN rooms r ON res.room_id = r.id
                                 JOIN users u ON res.user_id = u.id
                                 WHERE p.id = :id')
                        ->bind(':id', (int)$id)->single();
    }

    // Pelanggan upload bukti transfer -> status menjadi 'waiting'
    public function uploadProof($id, $proof)
    {
        return $this->db->query('UPDATE payments SET proof = :proof, status = "waiting", paid_at = NOW()
                                 WHERE id = :id')
                        ->bind(':proof', $proof)
                        ->bind(':id', (int)$id)->execute();
    }

    // Admin verifikasi / tolak
    public function setStatus($id, $status, $note = null)
    {
        return $this->db->query('UPDATE payments SET status = :status, note = :note, verified_at = NOW()
                                 WHERE id = :id')
                        ->bind(':status', $status)
                        ->bind(':note', $note)
                        ->bind(':id', (int)$id)->execute();
    }

    public function all()
    {
        return $this->db->query('SELECT p.*, res.check_in, res.check_out,
                                        r.name AS room_name, u.name AS customer_name
                                 FROM payments p
                                 JOIN reservations res ON p.reservation_id = res.id
                                 JOIN rooms r ON res.room_id = r.id
                                 JOIN users u ON res.user_id = u.id
                                 ORDER BY p.created_at DESC')->resultSet();
    }

    public function countWaiting()
    {
        $r = $this->db->query('SELECT COUNT(*) AS total FROM payments WHERE status = "waiting"')->single();
        return $r->total;
    }
}
