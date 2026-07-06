<?php
class Reservation
{
    private $db;
    public function __construct() { $this->db = new Database(); }

    /**
     * Cek ketersediaan kamar (ANTI DOUBLE-BOOKING).
     * Kamar tersedia bila tidak ada reservasi aktif yang tanggalnya bertumpuk.
     * Aturan tumpang tindih: (existing.check_in < new.check_out) AND (existing.check_out > new.check_in)
     */
    public function isAvailable($roomId, $checkIn, $checkOut, $excludeId = null)
    {
        $sql = 'SELECT COUNT(*) AS total FROM reservations
                WHERE room_id = :room_id
                  AND status NOT IN ("cancelled")
                  AND check_in  < :check_out
                  AND check_out > :check_in';
        $this->db->query($sql)
                 ->bind(':room_id', (int)$roomId)
                 ->bind(':check_out', $checkOut)
                 ->bind(':check_in', $checkIn);
        if ($excludeId) {
            // (tidak dipakai untuk pembuatan baru, disediakan untuk edit)
        }
        $row = $this->db->single();
        return $row->total == 0;
    }

    public function create($data)
    {
        $this->db->query('INSERT INTO reservations
                          (user_id, room_id, check_in, check_out, guests, nights, total_price, status)
                          VALUES (:user_id, :room_id, :check_in, :check_out, :guests, :nights, :total_price, :status)')
                 ->bind(':user_id', (int)$data['user_id'])
                 ->bind(':room_id', (int)$data['room_id'])
                 ->bind(':check_in', $data['check_in'])
                 ->bind(':check_out', $data['check_out'])
                 ->bind(':guests', (int)$data['guests'])
                 ->bind(':nights', (int)$data['nights'])
                 ->bind(':total_price', $data['total_price'])
                 ->bind(':status', 'pending');
        $this->db->execute();
        return $this->db->lastId();
    }

    public function find($id)
    {
        return $this->db->query('SELECT res.*, r.name AS room_name, r.type, r.photo AS room_photo,
                                        h.name AS hotel_name, h.city, u.name AS customer_name, u.email
                                 FROM reservations res
                                 JOIN rooms r  ON res.room_id = r.id
                                 JOIN hotels h ON r.hotel_id = h.id
                                 JOIN users u  ON res.user_id = u.id
                                 WHERE res.id = :id')
                        ->bind(':id', (int)$id)->single();
    }

    public function byUser($userId)
    {
        return $this->db->query('SELECT res.*, r.name AS room_name, r.type, r.photo AS room_photo,
                                        h.name AS hotel_name, h.city,
                                        p.status AS payment_status, p.id AS payment_id
                                 FROM reservations res
                                 JOIN rooms r  ON res.room_id = r.id
                                 JOIN hotels h ON r.hotel_id = h.id
                                 LEFT JOIN payments p ON p.reservation_id = res.id
                                 WHERE res.user_id = :uid
                                 ORDER BY res.created_at DESC')
                        ->bind(':uid', (int)$userId)->resultSet();
    }

    public function all()
    {
        return $this->db->query('SELECT res.*, r.name AS room_name, h.name AS hotel_name,
                                        u.name AS customer_name, p.status AS payment_status
                                 FROM reservations res
                                 JOIN rooms r  ON res.room_id = r.id
                                 JOIN hotels h ON r.hotel_id = h.id
                                 JOIN users u  ON res.user_id = u.id
                                 LEFT JOIN payments p ON p.reservation_id = res.id
                                 ORDER BY res.created_at DESC')->resultSet();
    }

    public function updateStatus($id, $status)
    {
        return $this->db->query('UPDATE reservations SET status = :status WHERE id = :id')
                        ->bind(':status', $status)
                        ->bind(':id', (int)$id)->execute();
    }

    // ---------------- LAPORAN ----------------
    public function totalRevenue()
    {
        $r = $this->db->query('SELECT COALESCE(SUM(res.total_price),0) AS total
                               FROM reservations res
                               JOIN payments p ON p.reservation_id = res.id
                               WHERE p.status = "verified"')->single();
        return $r->total;
    }

    public function count()
    {
        $r = $this->db->query('SELECT COUNT(*) AS total FROM reservations')->single();
        return $r->total;
    }

    public function countByStatus($status)
    {
        $r = $this->db->query('SELECT COUNT(*) AS total FROM reservations WHERE status = :s')
                      ->bind(':s', $status)->single();
        return $r->total;
    }

    // Laporan kamar terlaris
    public function bestSellingRooms($limit = 5)
    {
        return $this->db->query('SELECT r.name AS room_name, h.name AS hotel_name,
                                        COUNT(res.id) AS total_booking,
                                        COALESCE(SUM(res.total_price),0) AS total_revenue
                                 FROM reservations res
                                 JOIN rooms r  ON res.room_id = r.id
                                 JOIN hotels h ON r.hotel_id = h.id
                                 WHERE res.status != "cancelled"
                                 GROUP BY res.room_id
                                 ORDER BY total_booking DESC
                                 LIMIT ' . (int)$limit)->resultSet();
    }

    // Laporan pendapatan per bulan (untuk tabel/grafik)
    public function revenuePerMonth()
    {
        return $this->db->query('SELECT DATE_FORMAT(res.check_in, "%Y-%m") AS bulan,
                                        COUNT(res.id) AS total_reservasi,
                                        COALESCE(SUM(res.total_price),0) AS pendapatan
                                 FROM reservations res
                                 JOIN payments p ON p.reservation_id = res.id
                                 WHERE p.status = "verified"
                                 GROUP BY bulan ORDER BY bulan DESC')->resultSet();
    }
}
