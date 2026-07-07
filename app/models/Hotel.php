<?php
class Hotel
{
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function all()
    {
        return $this->db->query('SELECT * FROM hotels ORDER BY name ASC')->resultSet();
    }

    // Daftar hotel beserta jumlah kamar, harga termurah, dan rata-rata rating (halaman publik).
    public function allWithStats($filters = [])
    {
        $sql = 'SELECT h.*,
                       (SELECT COUNT(*) FROM rooms WHERE hotel_id = h.id) AS room_count,
                       (SELECT MIN(price) FROM rooms WHERE hotel_id = h.id) AS min_price,
                       (SELECT ROUND(AVG(rv.rating),1) FROM reviews rv
                            JOIN rooms r ON rv.room_id = r.id
                            WHERE r.hotel_id = h.id) AS avg_rating
                FROM hotels h
                WHERE 1=1';
        $binds = [];

        if (!empty($filters['city'])) {
            $sql .= ' AND h.city = :city';
            $binds[':city'] = $filters['city'];
        }
        if (!empty($filters['star'])) {
            $sql .= ' AND h.star >= :star';
            $binds[':star'] = (int)$filters['star'];
        }

        // Urutkan (whitelist -> aman dari SQL injection)
        $sortMap = [
            'star'      => 'h.star DESC, h.name ASC',
            'name'      => 'h.name ASC',
            'price_asc' => 'min_price ASC',
            'rating'    => 'avg_rating DESC',
        ];
        $sortKey = $filters['sort'] ?? 'star';
        $sql .= ' ORDER BY ' . ($sortMap[$sortKey] ?? $sortMap['star']);

        $this->db->query($sql);
        foreach ($binds as $k => $v) $this->db->bind($k, $v);
        return $this->db->resultSet();
    }

    public function find($id)
    {
        return $this->db->query('SELECT * FROM hotels WHERE id = :id')
                        ->bind(':id', (int)$id)->single();
    }

    public function create($data)
    {
        $this->db->query('INSERT INTO hotels (name, city, address, star, description, photo)
                          VALUES (:name, :city, :address, :star, :description, :photo)')
                 ->bind(':name', $data['name'])
                 ->bind(':city', $data['city'])
                 ->bind(':address', $data['address'])
                 ->bind(':star', (int)$data['star'])
                 ->bind(':description', $data['description'])
                 ->bind(':photo', $data['photo']);
        return $this->db->execute();
    }

    public function update($id, $data)
    {
        $this->db->query('UPDATE hotels SET name=:name, city=:city, address=:address,
                          star=:star, description=:description, photo=:photo WHERE id=:id')
                 ->bind(':name', $data['name'])
                 ->bind(':city', $data['city'])
                 ->bind(':address', $data['address'])
                 ->bind(':star', (int)$data['star'])
                 ->bind(':description', $data['description'])
                 ->bind(':photo', $data['photo'])
                 ->bind(':id', (int)$id);
        return $this->db->execute();
    }

    public function delete($id)
    {
        return $this->db->query('DELETE FROM hotels WHERE id = :id')
                        ->bind(':id', (int)$id)->execute();
    }

    public function count() {
        $r = $this->db->query('SELECT COUNT(*) AS total FROM hotels')->single();
        return $r->total;
    }
}
