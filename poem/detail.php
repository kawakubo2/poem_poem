<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

authenticate();

session_start();

if (isset($_GET['id'])) {
    $_SESSION['insert_poem_id'] = $_GET['id'];
}

try {
    $db = getDb();
    $sql = "SELECT P.id, P.title, P.body,
            A.penname, A.profile_filepath
            FROM poems AS P
                INNER JOIN authors as A
                ON P.author_id = A.id
            WHERE P.id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_SESSION['insert_poem_id']);
    $stt->execute();
    $row = $stt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('エラーメッセージ: '. $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>詩の詳細 | Poem World</title>
<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="../css/main.css" />
</head>
<body>
	<a href="list.php">詩の一覧へ戻る</a>
	<ul id="error_summary">
	<?php
	if (isset($_SESSION['comment_insert_errors'])) {
	    foreach ($_SESSION['comment_insert_errors'] as $error) {
	        print("<li>{$error}</li>");
	    }
	    unset($_SESSION['comment_insert_errors']);
	}
	?>
	</ul>
	<table id="author_profile" class="table">
		<tr>
			<th>作家名</th>
			<td><?=e($row['penname']) ?></td>
			<td><img src="/images/<?=e($row['profile_filepath']) ?>" alt="<?=e($row['penname']) ?>" height="100" /></td>
		</tr>
		<tr>
			<td colspan="3">

<basefont size="3">
<pre class="poem-font-size">
<?=e($row['body']) ?>
</pre>
</basefont>
			</td>
		</tr>
	</table>
	<section class="section-center">
		<h3>コメント</h3>
		<?php
		try {
		    $db = getDb();
		    $sql = "SELECT C.id, C.comment, C.user_id, U.username
                    FROM comments AS C
                        INNER JOIN users as U
                        ON C.user_id = U.id
                    WHERE C.poem_id = :poem_id";
		    $stt = $db->prepare($sql);
		    $stt->bindValue(':poem_id', $_SESSION['insert_poem_id']);
		    $stt->execute();
		} catch (PDOException $e) {
		    die('エラーメッセージ: '. $e->getMessage());
		}
		while ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
		?>
			<div class="comment">
				<span><?=e($row['comment']) ?></span>&nbsp;&nbsp;
				<span><?=e($row['username']) ?></span>
				<?php
				if (is_admin() || $row['user_id'] === $_SESSION['user']['id']) {
				?>
    				<form method="POST" action="../comment/delete_process.php">
    					<input type="hidden" name="id" value="<?=e($row['id']) ?>" />
    					<input class="btn btn-danger" type="submit" value="削除" />
    				</form>
				<?php
				}
				?>
			</div>
		<?php
		}
		?>
		<form method="POST" action="/comment/insert_process.php">
			<input type="hidden" name="poem_id" value="<?=e($_SESSION['insert_poem_id']) ?>" />
			<label for="comment">コメント: </label>
			<textarea rows="5" cols="40"
				name="comment" id="comment"></textarea>
			<input type="submit" value="追加" />
		</form>
	</section>
</body>
</html>