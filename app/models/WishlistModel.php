<?php
class WishlistModel
{
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function isWishlisted($userId, $roomId)
    {
        $r = $this->db->query('SELECT COUNT(*) AS total FROM wishlists WHERE user_id=:u AND room_id=:r')
                      ->bind(':u', (int)$userId)->bind(':r', (int)$roomId)->single();
        return $r->total > 0;
    }

    // Toggle: kembalikan true jika menjadi favorit, false jika dihapus
    public function toggle($userId, $roomId)
    {
        if ($this->isWishlisted($userId, $roomId)) {
            $this->db->query('DELETE FROM wishlists WHERE user_id=:u AND room_id=:r')
                     ->bind(':u', (int)$userId)->bind(':r', (int)$roomId)->execute();
            return false;
        }
        $this->db->query('INSERT INTO wishlists (user_id, room_id) VALUES (:u, :r)')
                 ->bind(':u', (int)$userId)->bind(':r', (int)$roomId)->execute();
        return true;
    }

    // Daftar id kamar favorit milik user (untuk menandai ikon hati)
    public function idsByUser($userId)
    {
        $rows = $this->db->query('SELECT room_id FROM wishlists WHERE user_id=:u')
                         ->bind(':u', (int)$userId)->resultSet();
        return array_map(fn($x) => (int)$x->room_id, $rows);
    }

    // Daftar kamar favorit lengkap (untuk halaman Favorit)
    public function byUser($userId)
    {
        return $this->db->query('SELECT r.*, h.name AS hotel_name, h.city, h.star,
                                        (SELECT ROUND(AVG(rating),1) FROM reviews WHERE room_id=r.id) AS avg_rating,
                                        (SELECT COUNT(*) FROM reviews WHERE room_id=r.id) AS review_count
                                 FROM wishlists w
                                 JOIN rooms r  ON w.room_id = r.id
                                 JOIN hotels h ON r.hotel_id = h.id
                                 WHERE w.user_id = :u ORDER BY w.created_at DESC')
                        ->bind(':u', (int)$userId)->resultSet();
    }

    public function countByUser($userId)
    {
        $r = $this->db->query('SELECT COUNT(*) AS total FROM wishlists WHERE user_id=:u')
                      ->bind(':u', (int)$userId)->single();
        return $r->total;
    }
}
