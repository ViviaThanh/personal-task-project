<?php
require_once 'db.php';

class TaskModel
{
    private DbHelper $db;

    public function __construct()
    {
        $this->db = new DbHelper();
    }

    public function getById(int $id)
    {
        $sql = "SELECT * FROM tasks WHERE id = ?";
        return $this->db->select($sql, [$id], false);
    }

    public function getAllByUser($user_id, $filter_status = 'all', $sort_by = 'created_at_desc')
    {
        $params = [$user_id];
        $sql = "SELECT * FROM tasks WHERE user_id = ?";

        if ($filter_status !== 'all') {
            $sql .= " AND status = ?";
            $params[] = $filter_status;
        }

        switch ($sort_by) {
            case 'due_date_asc':
                $sql .= " ORDER BY due_date ASC";
                break;
            case 'created_at_desc':
            default:
                $sql .= " ORDER BY created_at DESC";
                break;
        }

        return $this->db->select($sql, $params);
    }

    public function getByProject($project_id)
    {
        $sql = "SELECT * FROM tasks WHERE project_id = ? ORDER BY created_at DESC";
        return $this->db->select($sql, [$project_id]);
    }


    public function insert(int $user_id, int $project_id, string $title, ?string $description = null, ?string $due_date = null)
    {
        if ($due_date === '') $due_date = null;

        $sql = "INSERT INTO tasks (user_id, project_id, title, description, due_date) 
                VALUES (?, ?, ?, ?, ?)";
        return $this->db->insert($sql, [$user_id, $project_id, $title, $description, $due_date]);
    }

    public function update(int $id, string $title, ?string $description, ?string $due_date, string $status)
    {
        if ($due_date === '') $due_date = null;

        $sql = "UPDATE tasks 
                SET title = ?, description = ?, due_date = ?, status = ?
                WHERE id = ?";
        return $this->db->update($sql, [$title, $description, $due_date, $status, $id]);
    }

    public function updateStatus(int $id, string $status)
    {
        $allowed = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $allowed)) return 0;

        $sql = "UPDATE tasks SET status = ? WHERE id = ?";
        return $this->db->update($sql, [$status, $id]);
    }

    public function delete(int $id)
    {
        $sql = "DELETE FROM tasks WHERE id = ?";
        return $this->db->delete($sql, [$id]);
    }
}
?>
