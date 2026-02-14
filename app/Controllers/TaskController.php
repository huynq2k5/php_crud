<?php
namespace App\Controllers;

use App\Models\TaskModel;

class TaskController {
    private $model;

    public function __construct() {
        $this->model = new TaskModel();
    }

    public function run($action, $id) {
        // --- XỬ LÝ THÊM MỚI ---
        if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content'] ?? '');
            if ($content !== '') {
                $this->model->add($content);
            }
            // Chuyển hướng về danh sách (dùng route list)
            header("Location: ./list");
            exit();
        }

        // --- XỬ LÝ XÓA ---
        if ($action === 'delete' && $id) {
            $this->model->delete($id);
            header("Location: ./list");
            exit();
        }

        // --- MẶC ĐỊNH: HIỆN DANH SÁCH ---
        $tasks = $this->model->getAll();
        
        // Gọi View
        // Lưu ý: File này chạy từ public/index.php nên đường dẫn view phải lùi 2 cấp
        if (file_exists(__DIR__ . '/../../views/tasks/index.php')) {
            include __DIR__ . '/../../views/tasks/index.php';
        } else {
            echo "Lỗi: Không tìm thấy file view tại views/tasks/index.php";
        }
    }
}
?>