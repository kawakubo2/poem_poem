<?php
require_once '../Encode.php';
require_once '../common/auth.php';
session_start();

authenticate();
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
	<ul id="error_summary">
	<?php
	if (isset($_SESSION['author_insert_errors'])) {
       foreach ($_SESSION['author_insert_errors'] as $error) {
           print("<li>{$error}</li>");
	   }
	   unset($_SESSION['author_insert_errors']);
	}
	?>
	</ul>
	<form id="fm" method="POST" action="insert_process.php"
		enctype="multipart/form-data">
		<div class="container">
    		<label for="penname">ペンネーム:</label><br>
    		<input type="text" id="penname" name="penname"
    			value="<?=e(isset($_SESSION['insert_penname'])? $_SESSION['insert_penname']: '') ?>" />
		</div>
		<div class="container">
			<label for="profile_image">プロフィール写真:</label><br>
			<input type="file" id="profile_image" name="profile_image"
				value="<?=e(isset($_SESSION['insert_profile_image']) ? $_SESSION['insert_profile_image']: '') ?>" />
		</div>
		<div class="container">
			<input type="hidden" name="max_file_size" value="1000000" />
			<input type="submit" value="登録" />
		</div>
	</form>
</body>
</html>