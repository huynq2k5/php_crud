<?php
namespace App\Repositories;

use App\Models\User;

interface IUserRepository {
    // Tìm user theo username -> Trả về Object User hoặc null
    public function findByUsername($username);

    // Tìm user theo token -> Trả về mảng (chứa cả created_at để check hạn)
    public function findByToken($token);

    // Cập nhật token -> Trả về true/false
    public function updateToken($userId, $token);

    // Lấy danh sách quyền -> Trả về mảng string
    public function getPermissions($roleId);
}
?>