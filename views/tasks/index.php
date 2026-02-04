<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVC CRUD MySQLi</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; max-width: 600px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn-delete { color: red; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Danh sách công việc</h1>
    <form action="add" method="POST">
		<input type="text" name="content" required placeholder="Nhập công việc...">
		<button type="submit">Gửi</button>
	</form>
    <br>
    <table>
        <tr>
            <th>ID</th>
            <th>Nội dung</th>
            <th>Xử lý</th>
        </tr>
        <?php foreach ($tasks as $task): ?>
        <tr>
            <td><?= $task['id'] ?></td>
            <td><?= htmlspecialchars($task['content']) ?></td>
            <td>
                <a href="delete/<?= $task['id'] ?>" class="btn-delete" onclick="return confirm('Bạn chắc chứ?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>