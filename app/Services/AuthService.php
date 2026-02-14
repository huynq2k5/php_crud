<?php
namespace App\Services;

use App\Repositories\UserRepository;

class AuthService {
    private $userRepo;

    public function __construct() {
        // Khởi tạo Repository để làm việc với DB
        $this->userRepo = new UserRepository();
    }

    // Xử lý đăng nhập (Dùng chung cho cả Web và Mobile)
    public function authenticate($username, $password) {
        $user = $this->userRepo->findByUsername($username);

        // Logic nghiệp vụ: Kiểm tra user có tồn tại và khớp password không
        if ($user && password_verify($password, $user->password)) {
            return $user; // Trả về Entity User
        }
        return null;
    }

    // Logic tạo token (Dành riêng cho Mobile)
    public function generateMobileSession($user) {
        // 1. Tạo token ngẫu nhiên
        $token = bin2hex(random_bytes(32));

        // 2. Lưu xuống DB qua Repository
        $this->userRepo->updateToken($user->id, $token);

        // 3. Lấy danh sách quyền
        $permissions = $this->userRepo->getPermissions($user->role_id);

        return [
            'token' => $token,
            'user' => $user,
            'permissions' => $permissions
        ];
    }

    // Logic kiểm tra Token và Hạn sử dụng (Cho Splash Screen)
    public function validateToken($token) {
        if (empty($token)) return ['valid' => false, 'message' => 'Token trống'];

        // Lấy thông tin từ Repo (trả về mảng chứa token_created_at)
        $data = $this->userRepo->findByToken($token);

        if (!$data) {
            return ['valid' => false, 'message' => 'Token không tồn tại'];
        }

        // Logic nghiệp vụ: Kiểm tra hạn 30 ngày
        $createdTime = strtotime($data['token_created_at']);
        $currentTime = time();
        $expiryTime = 30 * 24 * 60 * 60; // 30 ngày

        if (($currentTime - $createdTime) > $expiryTime) {
            return ['valid' => false, 'message' => 'Token hết hạn'];
        }

        // Nếu còn hạn, lấy thêm quyền để trả về
        $permissions = $this->userRepo->getPermissions($data['role_id']);

        return [
            'valid' => true,
            'user_data' => $data,
            'permissions' => $permissions
        ];
    }
}
?>