<?php
namespace App\Controllers; // Thêm Namespace cho Controller

use App\Services\AuthService;

class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    // --- 1. ĐĂNG NHẬP TRÊN WEB ---
    public function webLogin() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Gọi Service để check
            $user = $this->authService->authenticate($username, $password);

            if ($user) {
                // Login thành công -> Lưu Session
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->full_name;
                $_SESSION['user_role'] = $user->role_name;
                header("Location: ./list");
                exit();
            } else {
                $error = "Sai tài khoản hoặc mật khẩu!";
            }
        }
        // Gọi View (Lưu ý đường dẫn view giờ tính từ public/index.php)
        include __DIR__ . '/../../views/auth/login.php';
    }

    // --- XỬ LÝ ĐĂNG XUẤT ---
    public function logout() {
        // Hủy toàn bộ session
        session_destroy();
        
        // Chuyển hướng về trang đăng nhập
        // Dùng đường dẫn đầy đủ để đảm bảo chạy đúng trên mọi server
        header("Location: ./index.php?url=login");
        exit();
    }

    // --- 2. ĐĂNG NHẬP API (MOBILE) ---
    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        // Gọi Service
        $user = $this->authService->authenticate($username, $password);

        if ($user) {
            // Nếu đúng pass -> Nhờ Service tạo token và lấy quyền
            $sessionData = $this->authService->generateMobileSession($user);

            $this->sendJson([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'token' => $sessionData['token'],
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'role' => $user->role_name,
                    'permissions' => $sessionData['permissions']
                ]
            ]);
        } else {
            $this->sendJson(['success' => false, 'message' => 'Sai tài khoản hoặc mật khẩu']);
        }
    }

    // --- 3. KIỂM TRA TOKEN (SPLASH SCREEN) ---
    public function checkToken() {
        // Lấy token từ header (Logic lấy header giữ nguyên như cũ hoặc gom vào Helper)
        $token = $this->getBearerToken();

        // Gọi Service kiểm tra hạn
        $result = $this->authService->validateToken($token);

        if ($result['valid']) {
            $data = $result['user_data'];
            $this->sendJson([
                'success' => true,
                'user' => [
                    'id' => $data['id'],
                    'full_name' => $data['full_name'],
                    'role' => $data['role_name'],
                    'permissions' => $result['permissions']
                ]
            ]);
        } else {
            $this->sendJson(['success' => false, 'message' => $result['message']]);
        }
    }

    // Hàm phụ trợ: Trả về JSON
    private function sendJson($data) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    // Hàm phụ trợ: Lấy token từ Header
    private function getBearerToken() {
        $headers = [];
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == 'HTTP_') {
                    $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                    $headers[$header] = $value;
                }
            }
        }
        
        if (isset($headers['Authorization']) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
        return $_GET['token'] ?? '';
    }
}
?>