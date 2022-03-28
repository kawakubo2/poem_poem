<<<<<<< HEAD
<?php
require_once '../DbManager';
require_once '../Encode.php';
require_once '../common/auth.php';

=======
<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

if (!is_admin()) {
    die('権限がありません。');
}

try {
    $db = getDb();
    $sql = "SELECT id, name, email, role
            FROM users";
    $stt = $db->prepare($sql);
    $stt->execute();
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>ユーザ一覧 | Poem World</title>
</head>
<body>
	<h3>ユーザ一覧</h3>
	<table class="table">
		<tr>
			<th>ID</th><th>名前</th><th>Email</th><th>ロール</th>
		</tr>
	<?php
	while ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
	?>
		<tr>
			<td><?=e($row['id']) ?></td>
			<td><?=e($row['name']) ?></td>
			<td><?=e($row['email']) ?></td>
			<td><?=e($row['role']) ?></td>
		</tr>
	<?php
	}
	?>
	</table>
</body>
</html>
>>>>>>> branch 'master' of https://github.com/kawakubo2/poem_poem.git
