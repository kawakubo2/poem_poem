<?php
require_once '../Encode.php';
require_once '../DbManager.php';

session_start();

if (!isset($_SESSION['update_errors'])) {
    $db = getDb();
    $sql = "SELECT id, title, body
            FROM poems
            WHERE id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_GET['id']);
    $stt->execute();
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['update_id'] = $row['id'];
        $_SESSION['update_title'] = $row['title'];
        $_SESSION['update_body'] = $row['body'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
<style>
body {
    // background-image: url('../images/gazou060.JPG');
    background-size: cover;
}
#p1 {
    color: white;
}
</style>
<title>詩の編集 | Poem World</title>
</head>
<body>
	<div id="p1">
		<h3>詩の編集</h3>
		<p><a href="list.php">詩の一覧へ戻る</a></p>
		<p>
		<?php
		if (isset($_SESSION['update_success'])) {
		    print($_SESSION['update_success']);
		    unset($_SESSION['update_success']);
		}
		?>
		</p>
		<ul id="error_summary">
			<?php
			if (isset($_SESSION['update_errors'])) {
			    foreach ($_SESSION['update_errors'] as $error) {
			        print("<li>{$error}</li>");
			    }
			    unset($_SESSION['update_errors']);
			}
			?>
		</ul>
		<form method="POST" action="update_process.php">
			<div class="container">
				<label for="title">タイトル</label><br>
				<input type="text" id="title" name="title"
	    			value="<?=e($_SESSION['update_title']) ?>" />
	        </div>
	        <div class="container">
	        	<label for="body">詩</label><br>
	        	<textarea id="body" name="body"
	        			rows="20" cols="400"><?=e($_SESSION['update_body']) ?></textarea>
			</div>
    		<input type="submit" value="更新" />
    	</form>
	</div>
</body>
</html>