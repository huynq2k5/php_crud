<?php
require_once __DIR__ . '/../../config/db.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = new KetNoi();
    }

    // Lấy thông tin user + tên Role dựa vào username
    public function getUserByUsername($username) {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.username = ?";
        // Lưu ý: Class KetNoi của bạn ép kiểu params thành string ('s')
        // Username là string nên OK.
        $result = $this->db->truyVan($sql, [$username]);
        return $result->fetch_assoc();
    }

    // Lấy User dựa vào Token (Dùng cho Splash Screen)
    public function getUserByToken($token) {
        // SỬA: Thêm u.token_created_at vào SELECT
        $sql = "SELECT u.id, u.username, u.full_name, u.role_id, u.token_created_at, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.api_token = ?";
        $result = $this->db->truyVan($sql, [$token]);
        return $result->fetch_assoc();
    }

    // Cập nhật Token mới sau khi đăng nhập
    public function updateToken($userId, $token) {
        // SỬA: Thêm cập nhật cột token_created_at bằng thời gian hiện tại (NOW())
        $sql = "UPDATE users SET api_token = ?, token_created_at = NOW() WHERE id = ?";
    
        return $this->db->capNhat($sql, [$token, $userId]);
    }

    // Lấy danh sách Quyền (Permissions) của user
    // App Android sẽ dùng list này để ẩn/hiện nút
    public function getPermissionsByRoleId($roleId) {
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