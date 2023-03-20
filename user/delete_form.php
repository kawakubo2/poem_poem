<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

if (!is_admin()) {
    die('権限がありません');
}

try {
    $db = getDb();
    $sql = "SELECT id, username, name, email, role
            FROM users
            WHERE
                active = 1
                and
                id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_GET['id']);
    $stt->execute();

    $user = $stt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>ユーザ削除 | Poem World</title>
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="css/main.css" />
</head>
<body>
	<a href="../index.php"><img src="/images/poem_world.png" /></a>
	<?php if (is_admin()) { ?>
		<a href="list.php">一覧へ戻る</a>
	<?php } ?>
	<h2>ユーザ削除</h2>
	<table class="table">
		<tr><th>id</th><td><?=e($user['id']) ?></td></tr>
		<tr><th>ユーザ名<td><?=e($user['username']) ?></td></tr>
		<tr><th>名前</th><td><?=e($user['name']) ?></td></tr>
		<tr><th>Eメールアドレス</th><td><?=e($user['email']) ?></td></tr>
		<tr><th>ロール</th><td><?=e($user['role']) ?></td></tr>
	</table>
	<form method="POST" action="delete_process.php">
		<input type="hidden" name="id" value="<?=e($user['id']) ?>" />
		<input type="submit" name="delete" value="削除" />
		<input type="submit" name="cancel" value="中止" />
	</form>
</body>
</html>