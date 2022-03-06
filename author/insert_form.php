<?php
require_once '../Encode.php';
require_once '../common/auth.php';
session_start();

if (!is_login()) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/poem_poem/login/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>作家新規登録 | Poem World</title>
	<link type="text/css" rel="stylesheet" href="../css/main.css" />
	<script type="text/javascript" src="../jquery-3.6.0.min.js"></script>
	<script type="text/javascript" src="insert_form.js"></script>
</head>
<body>
	<h3>作家新規登録</h3>
	<ul id="error_summary"></ul>
	<form id="fm" method="POST" action="insert_process.php"
		enctype="multipart/form-data">
		<div class="container">
    		<label for="penname">ペンネーム:</label><br>
    		<input type="text" id="penname" name="penname"
    			value="<?=e(isset($_SESSION['penname'])? $_SESSION['penname']: '') ?>" />
		</div>
		<div class="container">
			<label for="profile_image">プロフィール写真:</label><br>
			<input type="file" id="profile_image" name="profile_image"
				value="<?=e(isset($_SESSION['profile_image']) ? $_SESSION['profile_image']: '') ?>" />
		</div>
		<div class="container">
			<input type="hidden" name="max_file_size" value="1000000" />
			<input type="submit" value="登録" />
		</div>
	</form>
</body>
</html>