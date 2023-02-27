<?php
require_once '../Encode.php';

session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>ユーザ新規登録 | Poem World</title>
	<link type="text/css" rel="stylesheet" href="/css/main.css" />
	<script type="text/javascript" src="../jquery-3.6.0.min.js"></script>
	<script type="text/javascript" src="insert_form.js"></script>
</head>
<body>
	<h3>ユーザ新規登録</h3>
	<ul id="error_summary">
	<?php
	if (isset($_SESSION['user_insert_errors'])) {
	    foreach ($_SESSION['user_insert_errors'] as $error) {
	        print("<li>{$error}</li>");
	    }
	    unset($_SESSION['user_insert_errors']);
	}
	?>
	</ul>
	<form id="fm" method="POST" action="insert_process.php">
		<div class="container">
			<label for="username">ユーザ名:</label><br>
			<input type="text" id="username" name="username"
				value="<?=e($_SESSION['insert_username']) ?>" />
		</div>
		<div class="container">
    		<label for="name">名前:</label><br>
    		<input type="text" id="name" name="name"
    			value="<?=e($_SESSION['insert_name']) ?>" />
		</div>
		<div class="container">
			<label for="email">Eメールアドレス:</label><br>
			<input type="email" id="email" name="email"
				value="<?=e($_SESSION['insert_email']) ?>" />
		</div>
		<div class="container">
			<label for="password">パスワード:</label><br>
			<input type="password" id="password" name="password"
				value="" />
		</div>
		<div class="container">
			<input type="submit" value="登録" />
		</div>
	</form>
</body>
</html>
