<?php
class Facility
{
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function all()
    {
        return $this->db->query('SELECT * FROM facilities ORDER BY name ASC')->resultSet();
    }

    public function find($id)
    {
        return $this->db->query('SELECT * FROM facilities WHERE id = :id')
                        ->bind(':id', (int)$id)->single();
    }

    public function create($data)
    {
        $this->db->query('INSERT INTO facilities (name, icon) VALUES (:name, :icon)')
                 ->bind(':name', $data['name'])
                 ->bind(':icon', $data['icon']);
        return $this->db->execute();
    }

    public function update($id, $data)
    {
        $this->db->query('UPDATE facilities SET name=:name, icon=:icon WHERE id=:id')
                 ->bind(':name', $data['name'])
                 ->bind(':icon', $data['icon'])
                 ->bind(':id', (int)$id);
        return $this->db->execute();
    }

    public function delete($id)
    {
        return $this->db->query('DELETE FROM facilities WHERE id=:id')
                        ->bind(':id', (int)$id)->execute();
    }
}
