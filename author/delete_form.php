<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

if (!is_admin()) {
    die('権限がありません。');
}

if (isset($_GET['page']) && $_GET['page'] === 'list.php') {
    try {
        $db = getDb();
        $sql = "SELECT id, penname, profile_filepath
                FROM authors
                WHERE id = :id and delete_flag = 0";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_GET['id']);
        $stt->execute();

        $row = $stt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>作家削除 | Poem World</title>
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="css/main.css" />
</head>
<body>
	<a class="btn btn-info" href="list.php">一覧へ戻る</a>
	<h2>作家削除</h2>
	<table class="table">
		<tr><th>id</th><td><?=e($row['id']) ?></td></tr>
		<tr><th>ペンネーム</th><td><?=e($row['penname']) ?></td></tr>
		<tr><th>写真</th><td><img src="../images/<?=e($row['profile_filepath']) ?>" width="200" /></td></tr>
	</table>
	<form method="POST" action="delete_process.php">
		<input type="hidden" name="id" value="<?=e($row['id']) ?>" />
		<input type="submit" name="delete" value="削除" />
		<input type="submit" name="cancel" value="中止" />
	</form>
</body>
</html>


