<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

if (!is_admin()) {
    die('権限がありません');
}

try {
    $db = getDb();
    $sql = "SELECT id, name, email, role, active
            FROM users
            WHERE id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_GET['id']);
    $stt->execute();

    $user = $stt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('エラーメッセージ:' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>ユーザ編集 | Poem World</title>
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="css/main.css" />
</head>
<body>
	<a href="list.php">一覧へ戻る</a>
	<h2>ユーザ編集</h2>
	<div class="container">
		<label for="name">名前:</label><br>
		<input type="text" id="name" name="name"
					value="<?=e($user['name']) ?>" readonly />
	</div>
	<div class="container">
		<label for="email">Eメールアドレス:</label><br>
		<input type="text" id="email" name="email"
					value="<?=e($user['email']) ?>" readonly />
	</div>
	<div class="container">
		<label for="role">ロール:</label><br>
		<select id="role" name="role">
		<?php
		  $roles = ['admin', 'user'];
		  foreach ($roles as $role) {
		      $prop = ($role === $user['role']) ? 'selected': '';
	   ?>
	   		<option value="<?=e($role) ?>" <?=$prop ?>><?=e($role) ?></option>
	   <?php
		  }
	   ?>
	   </select>
	</div>
</body>
</html>