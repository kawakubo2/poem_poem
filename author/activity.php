<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>作家活動編集 | Poem World</title>
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="/css/main.css" />
	<script type="text/javascript" src="/jquery-3.6.0.min.js"></script>
</head>
<body>
    <a href="../index.php"><img src="/images/poem_world.png" /></a>
	<h3>作家活動編集</h3>
	<p id="message">
		<?php
		if (isset($_SESSION['activity_success_message'])) {
			print($_SESSION['activity_success_message']);
			unset($_SESSION['activity_success_message']);
		}
		?>
	</p>
	<ul id="error_summary">
	<?php
	if (isset($_SESSION['activity_errors'])) {
       foreach ($_SESSION['activity_errors'] as $error) {
           print("<li>{$error}</li>");
	   }
	   unset($_SESSION['activity_errors']);
	}
	?>
	</ul>    
</body>
</html>