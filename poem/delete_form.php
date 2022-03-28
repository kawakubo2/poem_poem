<?php
require_once '../Encode.php';
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

authenticate();

try {
    $db = getDb();
    $sql = "SELECT P.id, P.title, P.body, A.user_id
            FROM poems AS P
                INNER JOIN authors AS A
                    ON P.author_id = A.id
            WHERE P.id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_GET['id']);
    $stt->execute();

    $row = $stt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['user_id'] = $row['user_id'];

    authorize($_SESSION['user_id']);
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>詩の削除 | Poem World</title>
</head>
<body>
	<a href="list.php">詩の一覧へ戻る</a>
	<table class="table">
		<tr>
			<th>名前</th><td><?=e($row['title']) ?></td>
		</tr>
		<tr>
			<th>詩</th><td><?=e($row['body']) ?></td>
		</tr>
	</table>
	<form method="POST" action="delete_process.php">
		<input type="hidden" name="id" value="<?=e($row['id']) ?>" />
		<input type="submit" name="delete" value="削除" />
		<input type="submit" name="cancel" value="キャンセル" />
	</form>
</body>
</html>