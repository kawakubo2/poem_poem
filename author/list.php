<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

try {
    $db = getDb();
    $sql = "SELECT id, penname, profile_filepath
            FROM authors
            WHERE delete_flag = 0";
    $stt = $db->prepare($sql);
    $stt->execute();
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>作家一覧 | Poem World</title>
	<link type="text/css" rel="stylesheet" href="/css/main.css" />
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
</head>
<body>
	<a href="../index.php"><img src="/images/poem_world.png" /></a>
	<h2>作家一覧</h2>
	<table class="table table-striped">
		<thead>
			<tr><th>id</th><th>ペンネーム</th><th>写真</th><th></th></tr>
		</thead>
		<tbody>
		<?php
		while ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
		?>
			<tr>
				<td><?=e($row['id']) ?></td>
				<td><a href="detail.php?author_id=<?=e($row['id']) ?>"><?=e($row['penname']) ?></a></td>
				<td><img src="/images/<?=e($row['profile_filepath']) ?>" width="200"
						alt="<?=e($row['penname']) ?>のプロフィール写真" /></td>
				<td>
					<?php if (is_admin()) { ?>
						<a class="btn btn-danger" href="delete_form.php?id=<?=e($row['id']) ?>&page=list.php">削除</a>
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