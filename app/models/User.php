<?php
class User
{
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function findByEmail($email)
    {
        return $this->db->query('SELECT * FROM users WHERE email = :email')
                        ->bind(':email', $email)
                        ->single();
    }

    public function findById($id)
    {
        return $this->db->query('SELECT * FROM users WHERE id = :id')
                        ->bind(':id', (int)$id)
                        ->single();
    }

    // Pendaftaran -> password WAJIB di-hash (Keamanan Fundamental)
    public function create($data)
    {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->db->query('INSERT INTO users (name, email, password, role, phone)
                          VALUES (:name, :email, :password, :role, :phone)')
                 ->bind(':name',  $data['name'])
                 ->bind(':email', $data['email'])
                 ->bind(':password', $hash)
                 ->bind(':role',  $data['role'] ?? 'customer')
                 ->bind(':phone', $data['phone'] ?? null);
        return $this->db->execute();
    }

    public function all()
    {
        return $this->db->query('SELECT * FROM users ORDER BY created_at DESC')->resultSet();
    }

    public function updateProfile($id, $data)
    {
        $this->db->query('UPDATE users SET name = :name, phone = :phone, photo = :photo WHERE id = :id')
                 ->bind(':name',  $data['name'])
                 ->bind(':phone', $data['phone'])
                 ->bind(':photo', $data['photo'])
                 ->bind(':id',    (int)$id);
        return $this->db->execute();
    }
}
