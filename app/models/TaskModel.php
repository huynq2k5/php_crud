<?php
require_once __DIR__ . '/../../config/db.php';

class TaskModel {
    private $db;

    public function __construct() {
        $this->db = new KetNoi();
    }

    public function getAll() {
        $sql = "SELECT * FROM tasks ORDER BY id DESC";
        $result = $this->db->truyVan($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function add($content) {
        $sql = "INSERT INTO tasks (content) VALUES (?)";
        return $this->db->capNhat($sql, [$content]);
    }

    public function delete($id) {
        $sql = "DELETE FROM tasks WHERE id = ?";
        return $this->db->capNhat($sql, [$id]);
    }
}
?>