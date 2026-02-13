<?php
// Khởi động Session (Bắt buộc để nhớ đăng nhập)
session_start();

// Router đơn giản
$url = $_GET['url'] ?? 'list';
$url = rtrim($url, '/');
$url = explode('/', $url);

$action = $url[0];
$id = $url[1] ?? null;

// Kiểm tra Action để gọi Controller tương ứng
switch ($action) {
    case 'login':   // Đăng nhập Web
    case 'logout':  // Đăng xuất Web
        require_once '../app/controllers/AuthController.php';
        $app = new AuthController();
        if ($action == 'login') {
            $app->webLogin();
        } elseif ($action == 'logout') {
            $app->logout();
        }
        break;

    default: // Các chức năng Task (list, add, delete...)
        // Kiểm tra: Nếu chưa đăng nhập thì đá về trang Login ngay
        if (!isset($_SESSION['user_id'])) {
            header("Location: ./login");
            exit();
        }

        require_once '../app/controllers/TaskController.php';
        $app = new TaskController();
        $app->run($action, $id);
        break;
}
?>