<?php
// Cho phép mọi domain truy cập (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// --- QUAN TRỌNG: NẠP AUTOLOAD ---
// Thay vì require model thủ công, ta gọi Autoload để nó tự nạp cả Model lẫn Config
require_once __DIR__ . '/../vendor/autoload.php';

// Khai báo sử dụng namespace của TaskModel
use App\Models\TaskModel;

// Khởi tạo Model
$model = new TaskModel();
$tasks = $model->getAll();

$response = [];

foreach ($tasks as $row) {
    // Xử lý dữ liệu trả về cho Android
    $response[] = [
        "id" => (int)$row['id'],
        "title" => $row['content'], // Đổi tên 'content' thành 'title' cho Android
        // Kiểm tra xem cột isCompleted có tồn tại không, nếu không thì mặc định là false
        "isCompleted" => isset($row['isCompleted']) ? (bool)$row['isCompleted'] : false
    ];
}

echo json_encode($response);
?>