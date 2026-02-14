<?php
namespace App\Repositories;

use Config\KetNoi;       // Sử dụng class kết nối DB
use App\Models\User;     // Sử dụng Entity User

class UserRepository implements IUserRepository {
    private $db;

    public function __construct() {
        $this->db = new KetNoi();
    }

    public function findByUsername($username) {
        // SQL Join để lấy luôn tên role
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.username = ?";
        
        // Gọi hàm truyVan từ class KetNoi của bạn
        $result = $this->db->truyVan($sql, [$username]);
        $row = $result->fetch_assoc();

        if ($row) {
            // Quan trọng: Chuyển mảng thành Object User
            return new User($row);
        }
        return null;
    }

    public function findByToken($token) {
        $sql = "SELECT u.id, u.username, u.full_name, u.role_id, u.token_created_at, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.api_token = ?";
        $result = $this->db->truyVan($sql, [$token]);
        return $result->fetch_assoc();
    }

    public function updateToken($userId, $token) {
        // Cập nhật cả thời gian tạo token (NOW())
        $sql = "UPDATE users SET api_token = ?, token_created_at = NOW() WHERE id = ?";
        return $this->db->capNhat($sql, [$token, $userId]);
    }

    public function getPermissions($roleId) {
        $sql = "SELECT p.code 
                FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?";
        $result = $this->db->truyVan($sql, [$roleId]);
        
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row['code'];
        }
        return $permissions;
    }
}
?>