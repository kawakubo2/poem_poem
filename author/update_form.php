<?php
require_once '../Encode.php';
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

authenticate();

try {
    $db = getDb();
    $sql = "SELECT id, user_id, penname, profile_filepath
            FROM authors
            WHERE user_id = :user_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_GET['id']);
    $stt->execute();

    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['update_author_id'] = $row['id'];
        $_SESSION['update_user_id'] = $row['user_id'];
        $_SESSION['update_penname'] = $row['penname'];
        $_SESSION['update_old_penname'] = $row['penname'];
        $_SESSION['update_profile_filepath'] = $row['profile_filepath'];
    }
    authorize($_SESSION['update_user_id']);
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>作家プロフィール編集 | Poem World</title>
	<link type="text/css" rel="stylesheet" href="/css/main.css" />
	<script type="text/javascript" src="/jquery-3.6.0.min.js"></script>
	<script type="text/javascript" src="insert_form.js"></script>
</head>
<body>
	<h3>作家プロフィール編集</h3>
	<ul id="error_summary">
	<?php
	if (isset($_SESSION['author_update_errors'])) {
       foreach ($_SESSION['author_update_errors'] as $error) {
           print("<li>{$error}</li>");
	   }
	   unset($_SESSION['author_update_errors']);
	}
	?>
	</ul>
	<form id="fm" method="POST" action="update_process.php"
		enctype="multipart/form-data">
		<div class="container">
    		<label for="penname">ペンネーム:</label><br>
    		<input type="text" id="penname" name="penname"
    			value="<?=isset($_SESSION['update_penname']) ? e($_SESSION['update_penname']) : '' ?>" />
		</div>
		<div class="container">
			<label for="profile_image">プロフィール写真:</label><br>
			<img src="/images/<?=isset($_SESSION['update_profile_filepath']) ? e($_SESSION['update_profile_filepath']) : '' ?>" width="300" />
			<input type="file" id="profile_image" name="profile_image" />
		</div>
		<div class="container">
			<input type="hidden" name="max_file_size" value="300000" />
			<input type="hidden" name="user_id" value="<?=$_SESSION['update_user_id'] ?>" />
			<input type="submit" value="更新" />
		</div>
	</form>
	<hr>
	<div>
		<h3>友達申請</h3>
<?php
try {
	$db = getDb();
	$sql = "SELECT U.username, F.user_id AS friend_user_id, 
				   F.author_id AS friend_author_id
			FROM friends AS F
				INNER JOIN users AS U ON F.user_id = U.id
			WHERE F.author_id = 
				(
					SELECT id 
					FROM authors AS A
					where A.user_id = :user_id
				)
			ORDER BY F.is_friend";
	$stt = $db->prepare($sql);
	$stt->bindValue(':user_id', $_GET['id']);
	$stt->execute();
} catch (PDOException $e) {
	die("エラーメッセージ: {$e->getMessage()}");
}
?>
	<table class="table">
		<thead>
			<tr>
				<th>申請者</th><th></th>
			</tr>	
		</thead>
		<tbody>


		</tbody>
	</table>
	</div>
</body>
</html>