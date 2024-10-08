<?php
require_once '../Encode.php';
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

authenticate();

try {
    $db = getDb();
    $sql = "SELECT id, user_id, penname, profile_filepath, activity
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
        $_SESSION['update_activity'] = $row['activity'];
    }
    // authorize($_SESSION['update_user_id']);
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>作家プロフィール編集 | Poem World</title>
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="/css/main.css" />
	<script type="text/javascript" src="/jquery-3.6.0.min.js"></script>
	<script type="text/javascript" src="insert_form.js"></script>
</head>
<body>
	<a href="../index.php"><img src="/images/poem_world.png" /></a>
	<h3>作家プロフィール編集</h3>
	<p id="message">
		<?php
		if (isset($_SESSION['author_update_success_message'])) {
			print($_SESSION['author_update_success_message']);
			unset($_SESSION['author_update_success_message']);
		}
		?>
	</p>
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
			<label for="activity">作家活動</label><br>
			<textarea id="activity" name="activity"
				rows="8" cols="60"><?=isset($_SESSION['update_activity']) ? e($_SESSION['update_activity']) : '' ?></textarea>
		</div>
		<div class="container">
			<input type="hidden" name="max_file_size" value="300000" />
			<input type="hidden" name="user_id" value="<?=$_SESSION['update_user_id'] ?>" />
			<input type="submit" value="更新" />
		</div>
	</form>
	<hr>
	<div>
		<h3>承認待ち</h3>
<?php
try {
	$db = getDb();
	$sql = "SELECT U.username, F.user_id AS friend_user_id, 
				   F.author_id AS friend_author_id
			FROM friends AS F
				INNER JOIN users AS U ON F.user_id = U.id
			WHERE
				F.status = '処理待ち'
				AND
				F.author_id = 
				(
					SELECT id 
					FROM authors AS A
					where A.user_id = :user_id
				)
			ORDER BY F.status";
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
				<th>申請者</th><th>&nbsp;&nbsp;&nbsp;</th><th>&nbsp;&nbsp;&nbsp;</th>
			</tr>	
		</thead>
		<tbody>
<?php while($row = $stt->fetch(PDO::FETCH_ASSOC)) { ?>
			<tr>
				<td><?=e($row['username']) ?></td>
				<td>
					<form method="POST" action="../friend/update_process.php">
						<input type="hidden" name="user_id" value="<?=e($row['friend_user_id']) ?>" />
						<input type="hidden" name="author_id" value="<?=e($row['friend_author_id']) ?>" />
						<input type="hidden" name="status" value="承認" />
						<input type="submit" value="承認" />
					</form>
				</td>
				<td>
					<form method="POST" action="../friend/update_process.php">
						<input type="hidden" name="user_id" value="<?=e($row['friend_user_id']) ?>" />
						<input type="hidden" name="author_id" value="<?=e($row['friend_author_id']) ?>" />
						<input type="hidden" name="status" value="拒否" />
						<input type="submit" value="拒否" />
					</form>
				</td>
			</tr>
<?php } ?>
		</tbody>
	</table>
	</div>
	<div>
		<h3>友達リスト</h3>
<?php
try {
	$db = getDb();
	$sql = "SELECT U.username, 
				F.user_id AS friend_user_id, 
				F.author_id AS friend_author_id
			FROM users AS U
				INNER JOIN friends AS F ON U.id = F.user_id
			WHERE F.status = '承認'
			  AND F.author_id =
			  	(
					SELECT id
					FROM authors
					WHERE user_id = :user_id
				)";
	$stt = $db->prepare($sql);
	$stt->bindValue(':user_id', $_SESSION['user']['id']);
	$stt->execute();
?>
		<table class="table">
			<thead>
				<tr>
					<th>ユーザ名</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
		<?php
		while ($friend_row = $stt->fetch(PDO::FETCH_ASSOC)) {
		?>
				<tr>
					<td><?=e($friend_row['username'] ) ?></td> 
					<td>
						<form method="POST" action="../friend/update_process.php">
							<input type="hidden" name="user_id" value="<?=e($friend_row['friend_user_id']) ?>" />
							<input type="hidden" name="author_id" value="<?=e($friend_row['friend_author_id']) ?>" />
							<input type="hidden" name="status" value="拒否" />
							<input type="submit" value="拒否" />
						</form>
					</td>
				</tr>
		<?php
		}
		?>
			</tbody>
		</table>
<?php
} catch (PDOException $e) {
	die("エラーメッセージ: {$e->getMessage()}");
}
?>
	</div>
	<div>
		<h3>詩とコメントの一覧</h3>
<?php
try {
	$db = getDb();
	$sql = "SELECT P.id, P.title, P.body, C.comment, U.username, U.profile_filepath,
			P.posted_date < SUBDATE(CURRENT_DATE(), INTERVAL 30 DAY) AS repost_flag
			FROM poems AS P
				LEFT OUTER JOIN comments AS C ON P.id = C.poem_id
				INNER JOIN users AS U ON C.user_id = U.id
			WHERE P.author_id = 
			(
				SELECT id
				FROM authors
				WHERE user_id = :user_id
			)";
	$stt = $db->prepare($sql);
	$stt->bindValue(':user_id', $_SESSION['user']['id']);
	$stt->execute();
} catch(PDOException $e) {
	die("エラーメッセージ: {$e->getMessage()}");
}
?>
		<table class="table">
			<thead>
				<tr>
					<th>タイトル</th>
					<th>詩</th>
					<th></th>
					<th>コメント</th>
					<th>ユーザ名</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
<?php
			$poem_id = 0;
			while($row = $stt->fetch(PDO::FETCH_ASSOC)) {
?>
				<tr>
			<?php
				if ($row['id'] != $poem_id) {
			?>
					<td><?=e($row['title']) ?></td>
					<td><?=e($row['body']) ?></td>
			<?php if ($row['repost_flag'] == 1) {?>
					<td><a class="btn btn-secondary" href="../poem/repost.php?poem_id=<?=e($row['id']) ?>" >再投稿</a></td>
			<?php } else { ?>
					<td></td>
			<?php
				  }
				} else {
			?>
					<td colspan="3"></td>
			<?php
				}
			?>
					<td><?=e($row['comment']) ?></td>
					<td><?=e($row['username']) ?></td>
					<td><img src="../images/<?=e($row['profile_filepath']) ?>" height="50" alt="ユーザのプロフィール画像" /></td>
			<?php
				$poem_id = $row['id'];
			?>
				</tr>
<?php
			}
?>
			</tbody>
		</table>
	</div>
	<div>
		<h3>利用規約違反</h3>
		<table class="table">
			<thead>
				<tr>
					<th>id</th>
					<th>詩のタイトル</th>
					<th>詩</th>
					<th>違反理由</th>
					<th>報告日</th>
				</tr>
			</thead>
			<tbody>
<?php
	try {
		$sql_terms_of_use = "
			SELECT P.id, P.title, CONCAT(SUBSTR(p.body, 1, 30), '...') AS body, 
			T.reason, T.posted_date
			FROM terms_of_use_violations AS T
				INNER JOIN poems AS P
					ON T.poem_id = P.id
				INNER JOIN authors AS A
					ON P.author_id = A.id
			WHERE A.user_id = :user_id";
		$stt_terms_of_use = $db->prepare($sql_terms_of_use);
		$stt_terms_of_use->bindValue(':user_id', $_SESSION['user']['id']);
		$stt_terms_of_use->execute();
	} catch(PDOException $e) {
		die("エラーメッセージ: {$e->getMessage()}");
	}
	while ($row_terms_of_use = $stt_terms_of_use->fetch(PDO::FETCH_ASSOC)) {
?>
			<tr>
				<td><?=$row_terms_of_use['id'] ?></td>
				<td><?=$row_terms_of_use['title'] ?></td>
				<td><?=$row_terms_of_use['body'] ?></td>
				<td><?=$row_terms_of_use['reason'] ?></td>
				<td><?=$row_terms_of_use['posted_date'] ?></td>
			</tr>
<?php
	}
?>
			</tbody>
		</table>
	</div>
</body>
</html>