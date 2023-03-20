<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

if (!(is_admin() || ($_GET['id'] == $_SESSION['user']['id']
    || $_SESSION['update_id'] == $_SESSION['user']['id']))) {
    die('権限がありません');
}

if (isset($_GET['page']) && 
		(
			$_GET['page'] === 'list.php' || $_GET['page'] === 'index.php')) {
    try {
        $db = getDb();
        $sql = "SELECT id, username, name, email, profile_filepath, role, active
            FROM users
            WHERE
                active = 1
                and
                id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_GET['id']);
        $stt->execute();

        $user = $stt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['update_id'] = $user['id'];
        $_SESSION['update_username'] = $user['username'];
        $_SESSION['update_name'] = $user['name'];
        $_SESSION['update_email'] = $user['email'];
        $_SESSION['update_old_email'] = $user['email'];
        $_SESSION['update_profile_filepath'] = $user['profile_filepath'];
        $_SESSION['update_role'] = $user['role'];

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
	<a href="../index.php"><img src="/images/poem_world.png" /></a>
	<?php if (is_admin()) { ?>
		<a href="list.php">一覧へ戻る</a>
	<?php } ?>

	<h2>ユーザ編集</h2>
	<ul id="error_summary">
	<?php
	if (isset($_SESSION['update_user_errors'])) {
	    foreach($_SESSION['update_user_errors'] as $error) {
	?>
			<li><?=$error ?></li>
	<?php
	    }
	    unset($_SESSION['update_user_errors']);
	}
	?>
	</ul>
	<div>
		<img src="/images/<?=(mb_convert_encoding($_SESSION['update_profile_filepath'], 'SJIS-WIN', 'UTF-8')) ?>" /><br>
		<a href="image_form.php?id=<?=e($_SESSION['update_id']) ?>&page=/user/update_form.php">画像差替え</a>
	</div>
	<form method="POST" action="update_process.php">
		<div class="container">
			<label for="username">ユーザ名:</label><br>
			<input type="text" id="username" name="username"
						value="<?=e($_SESSION['update_username']) ?>" readonly />
		</div>
    	<div class="container">
    		<label for="name">名前:</label><br>
    		<input type="text" id="name" name="name"
    					value="<?=e($_SESSION['update_name']) ?>" />
    	</div>
    	<div class="container">
    		<label for="email">Eメールアドレス:</label><br>
    		<input type="text" id="email" name="email"
    					value="<?=e($_SESSION['update_email']) ?>" />
    	</div>
    	<div class="container">
    		<input type="submit" value="更新" />
    	</div>
	</form>
</body>
</html>