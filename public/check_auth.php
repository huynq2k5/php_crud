<?php
// 1. BẬT HIỂN THỊ LỖI (Bắt buộc để debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Header JSON (Để trình duyệt hiểu)
header('Content-Type: application/json');

// 3. Kiểm tra file Controller có tồn tại không
$controllerPath = __DIR__ . '/../app/controllers/AuthController.php';

if (!file_exists($controllerPath)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi đường dẫn: Không tìm thấy file AuthController.php tại ' . realpath(__DIR__ . '/../../app/controllers/')
    ]);
    exit();
}

try {
    require_once $controllerPath;

    $auth = new AuthController();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $auth->checkToken();
    } else {
        echo json_encode(['success' => false, 'message' => 'Method not allowed (Phải dùng GET)']);
    }

} catch (Exception $e) {
    // Bắt mọi lỗi logic khác
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi Server: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    // Bắt lỗi cú pháp/fatal error
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi Nghiêm trọng: ' . $e->getMessage() . ' ở dòng ' . $e->getLine()
    ]);
}
?>