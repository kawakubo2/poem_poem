<?php
require_once '../Encode.php';

session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
<style>
body {
    background-image: url('../images/gazou060.JPG');
    background-size: cover;
}
#p1 {
    color: white;
}
</style>
<title></title>
</head>
<body>
	<div id="p1">
		<h3>詩の投稿</h3>
		<p><a href="list.php">詩の一覧へ戻る</a></p>
		<p>
		<?php
		if (isset($_SESSION['insert_success'])) {
		    print($_SESSION['insert_success']);
		    unset($_SESSION['insert_success']);
		}
		?>
		</p>
		<ul id="error_summary">
			<?php
			if (isset($_SESSION['insert_errors'])) {
			    foreach ($_SESSION['insert_errors'] as $error) {
			        print("<li>{$error}</li>");
			    }
			    unset($_SESSION['insert_errors']);
			}
			?>
		</ul>
		<form method="POST" action="insert_process.php">
			<div class="container">
				<label for="title">タイトル</label><br>
				<input type="text" id="title" name="title"
	    			value="<?=e($_SESSION['insert_title']) ?>" />
	        </div>
	        <div class="container">
	        	<label for="body">詩</label><br>
	        	<textarea id="body" name="body"
	        			rows="20" cols="400"><?=e($_SESSION['insert_body']) ?></textarea>
			</div>
    		<input type="submit" value="登録" />
    	</form>
	</div>
</body>
</html>