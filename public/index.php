<?php
// Bật hiển thị lỗi để nếu có chết thì nó báo ngay chứ không treo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Nạp Autoload
require_once __DIR__ . '/../vendor/autoload.php';

// --- KHAI BÁO SỬ DỤNG CLASS ---
use App\Controllers\AuthController;
use App\Controllers\TaskController; 
// (Lưu ý: Bạn phải chắc chắn file TaskController.php đã thêm 'namespace App\Controllers;' ở dòng đầu)

$url = $_GET['url'] ?? 'list';
$url = rtrim($url, '/');
$url = explode('/', $url);

$action = $url[0];
$id = $url[1] ?? null;

switch ($action) {
    // --- DÀNH CHO API (MOBILE / TEST) ---
    case 'api_login':   // URL: index.php?url=api_login
        $app = new AuthController();
        $app->login();  // Gọi hàm trả về JSON
        break;

    case 'check_auth':  // URL: index.php?url=check_auth
        $app = new AuthController();
        $app->checkToken();
        break;

    // --- DÀNH CHO WEB ---
    case 'login':       // URL: index.php?url=login
        $app = new AuthController();
        $app->webLogin(); // Gọi hàm trả về HTML
        break;

    case 'logout':
        $app = new AuthController();
        $app->logout();
        break;

    default:
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: login"); // Chuyển hướng rõ ràng hơn
            exit();
        }

        $app = new TaskController();
        $app->run($action, $id);
        break;
}
?>