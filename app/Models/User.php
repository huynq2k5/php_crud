<?php
namespace App\Models;

class User {
    public $id;
    public $username;
    public $password;
    public $full_name;
    public $role_id;
    public $role_name;     // Thuộc tính lấy từ bảng roles
    public $permissions;   // Mảng quyền hạn

    // Constructor giúp nạp dữ liệu từ mảng (do Database trả về) vào Object
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->full_name = $data['full_name'] ?? null;
        $this->role_id = $data['role_id'] ?? null;
        $this->role_name = $data['role_name'] ?? null;
        $this->permissions = $data['permissions'] ?? [];
    }
}
?>