<?php
require_once 'db.php';

class ProjectModel {
    private $db;

    public function __construct() {
        try {
            $this->db = new DbHelper();
        } catch (Exception $e) {
            die("Lỗi kết nối CSDL: " . $e->getMessage());
        }
    }


    public function getAllByUser($user_id) {
        $sql = "SELECT * FROM projects WHERE user_id = ? ORDER BY created_at DESC";
        return $this->db->selectAll($sql, [$user_id]);
    }


    public function getById($id) {
        $sql = "SELECT * FROM projects WHERE id = ?";
      return $this->db->select($sql, [$id], false);
    }


    public function insert($user_id, $name, $description) {
        $sql = "INSERT INTO projects (user_id, name, description, created_at) VALUES (?, ?, ?, NOW())";
        return $this->db->insert($sql, [$user_id, $name, $description]);
    }


    public function update($id, $name, $description) {
        $sql = "UPDATE projects SET name = ?, description = ? WHERE id = ?";
        return $this->db->update($sql, [$name, $description, $id]);
    }


    public function delete($id) {
        $sql = "DELETE FROM projects WHERE id = ?";
        return $this->db->delete($sql, [$id]);
    }
}
