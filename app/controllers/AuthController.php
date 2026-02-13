<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // --- DÀNH CHO WEB ---
    public function webLogin() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->getUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role_name'];
                header("Location: ./list");
                exit();
            } else {
                $error = "Sai tài khoản hoặc mật khẩu!";
            }
        }
        include __DIR__ . '/../../views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header("Location: ./login");
        exit();
    }

    // --- API: ĐĂNG NHẬP MOBILE ---
    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->sendJson(['success' => false, 'message' => 'Vui lòng nhập đủ thông tin']);
            return;
        }

        $user = $this->userModel->getUserByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            // Tạo token mới
            $token = bin2hex(random_bytes(32)); 
            
            // Lưu vào DB
            $this->userModel->updateToken($user['id'], $token);

            // Lấy quyền
            $permissions = $this->userModel->getPermissionsByRoleId($user['role_id']);

            $this->sendJson([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role_name'],
                    'permissions' => $permissions
                ]
            ]);
        } else {
            $this->sendJson(['success' => false, 'message' => 'Sai tài khoản hoặc mật khẩu']);
        }
    }

    // --- API: KIỂM TRA TOKEN ---
    public function checkToken() {
        // 1. Lấy Header một cách an toàn (Fix lỗi apache_request_headers)
        $headers = [];
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            // Tự lấy header từ $_SERVER nếu hàm apache không có
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == 'HTTP_') {
                    $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                    $headers[$header] = $value;
                }
            }
        }

        $token = '';

        // 2. Tìm token trong Header (Chấp nhận cả Authorization và authorization)
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                $token = $matches[1];
            }
        } elseif (isset($headers['authorization'])) { // Phòng hờ server gửi chữ thường
            if (preg_match('/Bearer\s(\S+)/', $headers['authorization'], $matches)) {
                $token = $matches[1];
            }
        } elseif (isset($_GET['token'])) {
            $token = $_GET['token'];
        }

        // 3. Kiểm tra token có rỗng không
        if (empty($token)) {
            $this->sendJson(['success' => false, 'message' => 'Không tìm thấy token']);
            return;
        }

        // 4. Tìm User trong DB
        $user = $this->userModel->getUserByToken($token);

        if ($user) {
            // 5. Kiểm tra hạn sử dụng (30 ngày)
            $createdTime = strtotime($user['token_created_at']);
            $currentTime = time();
            $expiryTime = 30 * 24 * 60 * 60; 

            if (($currentTime - $createdTime) > $expiryTime) {
                $this->sendJson([
                    'success' => false, 
                    'message' => 'Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.'
                ]);
                return;
            }

            // 6. Token ngon -> Trả về Info
            $permissions = $this->userModel->getPermissionsByRoleId($user['role_id']);
            
            $this->sendJson([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role_name'],
                    'permissions' => $permissions
                ]
            ]);
        } else {
            $this->sendJson(['success' => false, 'message' => 'Token không hợp lệ']);
        }
    }

    private function sendJson($data) {
        // Xóa mọi output thừa trước đó để đảm bảo JSON sạch
        if (ob_get_length()) ob_clean(); 
        
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
?>