<?php
require_once '../app/controllers/TaskController.php';

$url = $_GET['url'] ?? 'list';
$url = rtrim($url, '/');
$url = explode('/', $url);

$action = $url[0];
$id = $url[1] ?? null;

$app = new TaskController();
$app->run($action, $id);
?>