<?php
require_once '../Encode.php';

session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>ログイン | Poem World</title>
	<link type="text/css" rel="stylesheet" href="../css/main.css" />
</head>
<body>
	<h3>ログイン</h3>
	<ul id="error_summary">
	<?php
	if (isset($_SESSION['login_errors'])) {
	    foreach ($_SESSION['login_errors'] as $error) {
	        print("<li>{$error}</li>");
	    }
	    unset($_SESSION['login_errors']);
	}
	?>
	</ul>
	<form method="POST" action="login_process.php">
		<div class="container">
    		<label for="email">メールアドレス:</label><br>
    		<input type="text" id="email" name="email" />
		</div>
		<div class="container">
			<label for="password">パスワード:</label><br>
			<input type="password" id="password" name="password" />
		</div>
		<div class="container">
			<input type="submit" value="ログイン" />
		</div>
	</form>
</body>
</html>
