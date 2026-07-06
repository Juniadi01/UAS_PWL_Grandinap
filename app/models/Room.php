<?php
class Room
{
    private $db;
    public function __construct() { $this->db = new Database(); }

    // Semua kamar + nama hotel (untuk admin)
    public function all()
    {
        return $this->db->query('SELECT r.*, h.name AS hotel_name, h.city
                                 FROM rooms r JOIN hotels h ON r.hotel_id = h.id
                                 ORDER BY r.created_at DESC')->resultSet();
    }

    public function find($id)
    {
        return $this->db->query('SELECT r.*, h.name AS hotel_name, h.city, h.star,
                                        (SELECT ROUND(AVG(rating),1) FROM reviews WHERE room_id=r.id) AS avg_rating,
                                        (SELECT COUNT(*) FROM reviews WHERE room_id=r.id) AS review_count
                                 FROM rooms r JOIN hotels h ON r.hotel_id = h.id
                                 WHERE r.id = :id')
                        ->bind(':id', (int)$id)->single();
    }

    // Pencarian / filter katalog publik
    public function search($filters = [])
    {
        $sql = 'SELECT r.*, h.name AS hotel_name, h.city, h.star,
                       (SELECT ROUND(AVG(rating),1) FROM reviews WHERE room_id=r.id) AS avg_rating,
                       (SELECT COUNT(*) FROM reviews WHERE room_id=r.id) AS review_count
                FROM rooms r JOIN hotels h ON r.hotel_id = h.id
                WHERE r.status = "available"';
        $binds = [];

        if (!empty($filters['city'])) {
            $sql .= ' AND h.city = :city';
            $binds[':city'] = $filters['city'];
        }
        if (!empty($filters['type'])) {
            $sql .= ' AND r.type = :type';
            $binds[':type'] = $filters['type'];
        }
        if (!empty($filters['guests'])) {
            $sql .= ' AND r.capacity >= :guests';
            $binds[':guests'] = (int)$filters['guests'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= ' AND r.price <= :max_price';
            $binds[':max_price'] = (float)$filters['max_price'];
        }
        if (!empty($filters['keyword'])) {
            $sql .= ' AND (r.name LIKE :kw1 OR h.name LIKE :kw2 OR h.city LIKE :kw3)';
            $kw = '%' . $filters['keyword'] . '%';
            $binds[':kw1'] = $kw;
            $binds[':kw2'] = $kw;
            $binds[':kw3'] = $kw;
        }
        // Urutkan (whitelist -> aman dari SQL injection; ORDER BY tak bisa di-bind)
        $sortMap = [
            'price_asc'  => 'r.price ASC',
            'price_desc' => 'r.price DESC',
            'rating'     => 'avg_rating DESC, r.price ASC',
            'newest'     => 'r.id DESC',
        ];
        $sortKey = $filters['sort'] ?? 'price_asc';
        $sql .= ' ORDER BY ' . ($sortMap[$sortKey] ?? $sortMap['price_asc']);

        $this->db->query($sql);
        foreach ($binds as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }

    // Ambil semua kamar milik satu hotel (dengan rating, untuk halaman detail hotel)
    public function byHotel($hotelId)
    {
        $sql = 'SELECT r.*, h.name AS hotel_name, h.city, h.star,
                       (SELECT ROUND(AVG(rating),1) FROM reviews WHERE room_id=r.id) AS avg_rating,
                       (SELECT COUNT(*) FROM reviews WHERE room_id=r.id) AS review_count
                FROM rooms r JOIN hotels h ON r.hotel_id = h.id
                WHERE r.hotel_id = :hid
                ORDER BY r.price ASC';
        return $this->db->query($sql)->bind(':hid', (int)$hotelId)->resultSet();
    }

    public function create($data)
    {
        $this->db->query('INSERT INTO rooms (hotel_id, name, type, price, capacity, description, photo, status)
                          VALUES (:hotel_id, :name, :type, :price, :capacity, :description, :photo, :status)')
                 ->bind(':hotel_id', (int)$data['hotel_id'])
                 ->bind(':name', $data['name'])
                 ->bind(':type', $data['type'])
                 ->bind(':price', $data['price'])
                 ->bind(':capacity', (int)$data['capacity'])
                 ->bind(':description', $data['description'])
                 ->bind(':photo', $data['photo'])
                 ->bind(':status', $data['status']);
        $this->db->execute();
        return $this->db->lastId();
    }

    public function update($id, $data)
    {
        $this->db->query('UPDATE rooms SET hotel_id=:hotel_id, name=:name, type=:type, price=:price,
                          capacity=:capacity, description=:description, photo=:photo, status=:status
                          WHERE id=:id')
                 ->bind(':hotel_id', (int)$data['hotel_id'])
                 ->bind(':name', $data['name'])
                 ->bind(':type', $data['type'])
                 ->bind(':price', $data['price'])
                 ->bind(':capacity', (int)$data['capacity'])
                 ->bind(':description', $data['description'])
                 ->bind(':photo', $data['photo'])
                 ->bind(':status', $data['status'])
                 ->bind(':id', (int)$id);
        return $this->db->execute();
    }

    public function delete($id)
    {
        return $this->db->query('DELETE FROM rooms WHERE id=:id')
                        ->bind(':id', (int)$id)->execute();
    }

    // ---- Fasilitas kamar (many-to-many) ----
    public function getFacilities($roomId)
    {
        return $this->db->query('SELECT f.* FROM facilities f
                                 JOIN room_facility rf ON f.id = rf.facility_id
                                 WHERE rf.room_id = :id')
                        ->bind(':id', (int)$roomId)->resultSet();
    }

    public function getFacilityIds($roomId)
    {
        $rows = $this->db->query('SELECT facility_id FROM room_facility WHERE room_id = :id')
                         ->bind(':id', (int)$roomId)->resultSet();
        return array_map(fn($r) => (int)$r->facility_id, $rows);
    }

    public function syncFacilities($roomId, $facilityIds = [])
    {
        $this->db->query('DELETE FROM room_facility WHERE room_id = :id')
                 ->bind(':id', (int)$roomId)->execute();
        foreach ($facilityIds as $fid) {
            $this->db->query('INSERT INTO room_facility (room_id, facility_id) VALUES (:r, :f)')
                     ->bind(':r', (int)$roomId)->bind(':f', (int)$fid)->execute();
        }
    }

    public function count() {
        $r = $this->db->query('SELECT COUNT(*) AS total FROM rooms')->single();
        return $r->total;
    }

    // Daftar kota unik untuk dropdown filter
    public function cities() {
        return $this->db->query('SELECT DISTINCT city FROM hotels ORDER BY city')->resultSet();
    }
}
