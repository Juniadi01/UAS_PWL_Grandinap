<?php
class Review
{
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function create($data)
    {
        $this->db->query('INSERT INTO reviews (reservation_id, user_id, room_id, rating, comment)
                          VALUES (:res, :user, :room, :rating, :comment)')
                 ->bind(':res',    (int)$data['reservation_id'])
                 ->bind(':user',   (int)$data['user_id'])
                 ->bind(':room',   (int)$data['room_id'])
                 ->bind(':rating', (int)$data['rating'])
                 ->bind(':comment', $data['comment']);
        return $this->db->execute();
    }

    // Sudahkah reservasi ini diulas?
    public function existsForReservation($reservationId)
    {
        $r = $this->db->query('SELECT COUNT(*) AS total FROM reviews WHERE reservation_id = :id')
                      ->bind(':id', (int)$reservationId)->single();
        return $r->total > 0;
    }

    // Ulasan untuk satu kamar (beserta nama penulis)
    public function byRoom($roomId)
    {
        return $this->db->query('SELECT rv.*, u.name AS author, u.photo AS author_photo
                                 FROM reviews rv JOIN users u ON rv.user_id = u.id
                                 WHERE rv.room_id = :id ORDER BY rv.created_at DESC')
                        ->bind(':id', (int)$roomId)->resultSet();
    }

    // Rata-rata & jumlah ulasan untuk satu kamar
    public function summary($roomId)
    {
        return $this->db->query('SELECT ROUND(AVG(rating),1) AS avg_rating, COUNT(*) AS total
                                 FROM reviews WHERE room_id = :id')
                        ->bind(':id', (int)$roomId)->single();
    }
}
