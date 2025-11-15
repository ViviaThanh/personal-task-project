<?php

require_once __DIR__ . 'db.php'; 


class UserRepository 
{
    private DbHelper $db;

    public function __construct()
    {
        $this->db = new DbHelper();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        return $this->db->select($sql);
    }

    public function getById(int $id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->select($sql, [$id], false);
    }

    public function findByUsername(string $username)
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        return $this->db->select($sql, [$username], false);
    }


    public function insert(string $username, string $password, ?string $email = null): int
    {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        return $this->db->insert($sql, [$username, $hashedPassword, $email]);
    }


    public function updateEmail(int $id, ?string $email): int
    {
        $sql = "UPDATE users SET email = ? WHERE id = ?";
        return $this->db->update($sql, [$email, $id]);
    }
    

    public function updatePassword(int $id, string $newPassword): int
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->db->update($sql, [$hashedPassword, $id]);
    }


    public function delete(int $id): int
    {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->delete($sql, [$id]);
    }
}
?>