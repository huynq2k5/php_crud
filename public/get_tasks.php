<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../app/models/TaskModel.php';

$model = new TaskModel();
$tasks = $model->getAll();

$response = [];

foreach ($tasks as $row) {
    $response[] = [
        "id" => (int)$row['id'],
        "title" => $row['content'], // Ánh xạ 'content' từ DB sang 'title' của Android
        "isCompleted" => (bool)$row['isCompleted']
    ];
}

echo json_encode($response);