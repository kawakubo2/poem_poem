<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

if (!is_admin()) {
    die('権限がありません');
}

if (isset($_GET['page']) && $_GET['page'] === 'list.php') {
    try {
        $db = getDb();
        $sql = "SELECT id, name, email, role, active
            FROM users
            WHERE
                active = 1
                and
                id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_GET['id']);
        $stt->execute();

        $user = $stt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['update_user_id'] = $user['id'];
        $_SESSION['update_user_name'] = $user['name'];
        $_SESSION['update_user_email'] = $user['email'];
        $_SESSION['update_user_role'] = $user['role'];

    } catch (PDOException $e) {
        die('エラーメッセージ:' . $e->getMessage());
    }
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
	<ul id="error_summary">
	<?php
	if (isset($_SESSION['update_user_erros'])) {
	    foreach($_SESSION['update_user_errors'] as $error) {
	?>
			<li><?=$error ?></li>
	<?php
	    }
	    unset($_SESSION['update_user_errors']);
	}
	?>
	</ul>
	<form method="POST" action="update_process.php">
    	<div class="container">
    		<label for="name">名前:</label><br>
    		<input type="text" id="name" name="name"
    					value="<?=e($_SESSION['update_user_name']) ?>" />
    	</div>
    	<div class="container">
    		<label for="email">Eメールアドレス:</label><br>
    		<input type="text" id="email" name="email"
    					value="<?=e($_SESSION['update_user_email']) ?>" readonly />
    	</div>
    	<div class="container">
    		<label for="role">ロール:</label><br>
    		<select id="role" name="role">
    		<?php
    		  $roles = ['admin', 'user'];
    		  foreach ($roles as $role) {
    		      $prop = ($role === $_SESSION['update_user_role']) ? 'selected': '';
    	   ?>
    	   		<option value="<?=e($role) ?>" <?=$prop ?>><?=e($role) ?></option>
    	   <?php
    		  }
    	   ?>
    	   </select>
    	</div>
    	<div class="container">
    		<input type="submit" value="更新" />
    	</div>
	</form>
</body>
</html>