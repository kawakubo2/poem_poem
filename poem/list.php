<?php
require_once '../Encode.php';
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<title>詩の一覧 | Poem World</title>
</head>
<body>
	<h3>詩の一覧</h3>
	<p><a href="insert_form.php">詩の登録</a>
	<table class="table">
		<thead>
			<tr><th>タイトル</th><th>ペンネーム</th><th>詩</th><th></th></tr>
		</thead>
		<tbody>
		<?php
		$db = getDb();
		$sql = "SELECT P.id, P.title, A.penname, P.body, A.user_id
                FROM poems AS P
                    INNER JOIN authors AS A ON P.author_id = A.id";
		$stt = $db->prepare($sql);
		$stt->execute();
		while ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
		?>
			<tr>
				<td><?=e($row['title']) ?></td>
				<td><?=e($row['penname']) ?></td>
				<td><?=e($row['body']) ?></td>
				<td>
				<?php if (is_login() && $row['user_id'] === $_SESSION['user']['id']) { ?>
					<a href="update_form.php?id=<?=e($row['id']) ?>">編集</a>
					<a href="delete_form.php?id=<?=e($row['id']) ?>">削除</a>
				<?php } ?>
				</td>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>
</body>
</html>