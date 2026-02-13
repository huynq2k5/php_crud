<?php
// Bật hiển thị lỗi để debug (tắt khi chạy thật)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/controllers/AuthController.php';

$auth = new AuthController();
// Chỉ nhận method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->login();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>