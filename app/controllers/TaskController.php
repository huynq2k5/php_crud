<?php
require_once __DIR__ . '/../models/TaskModel.php';

class TaskController {
    private $model;

    public function __construct() {
        $this->model = new TaskModel();
    }

    public function run($action, $id) {
        if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content'] ?? '');
            if ($content !== '') {
                $this->model->add($content);
            }
            header("Location: ./");
            exit();
        }

        if ($action === 'delete' && $id) {
            $this->model->delete($id);
            header("Location: ../");
            exit();
        }

        $tasks = $this->model->getAll();
        include __DIR__ . '/../../views/tasks/index.php';
    }
}
?>