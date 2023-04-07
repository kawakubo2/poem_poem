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
	<header>
    	<h3>詩の一覧</h3>
    	<p><a href="insert_form.php">詩の登録</a>
    	<?php
    	try {
    	   $db = getDb();
    	   $sql = "SELECT id, penname
                   FROM authors";
    	   $stt = $db->prepare($sql);
    	   $stt->execute();
    	} catch (PDOException $e) {
    	    die('エラーメッセージ: ' . $e->getMessage());
    	}
    	?>
	</header>
	<main>
    	<h4>検索</h4>
    	<form method="GET" action="">
    		<label for="title">タイトル</label>
    		<input type="text" id="title" name="title" />
    		<label for="penname">ペンネーム: </label>
    		<select id="penname" name="penname">
    			<option value=""></option>
    		<?php while ($row = $stt->fetch(PDO::FETCH_ASSOC)) { ?>
    			<option value="<?=e($row['id']) ?>"><?=e($row['penname']) ?></option>
    		<?php } ?>
    		</select>
    		<input type="submit" value="検索" />
    	</form>
    	<table class="table">
    		<thead>
    			<tr><th>タイトル</th><th>ペンネーム</th><th>詩</th><th></th></tr>
    		</thead>
    		<tbody>
    		<?php
    		$db = getDb();
    		$sql = "SELECT P.id, P.title, A.penname, P.body, A.user_id
                    FROM poems AS P
                        INNER JOIN authors AS A ON P.author_id = A.id
                    WHERE 1 = 1";
    		if (isset($_GET['penname']) && $_GET['penname'] !== '') {
    		    $sql .= " AND P.author_id = :author_id";
    		}
    		if (isset($_GET['title']) && $_GET['title'] !== '') {
    		    $sql .= " AND P.title LIKE :title";
    		}
    		$stt = $db->prepare($sql);
    		if (isset($_GET['penname']) && $_GET['penname'] !== '') {
    		    $stt->bindValue(':author_id', $_GET['penname']);
    		}
    		if (isset($_GET['title']) && $_GET['title'] !== '') {
    		    $stt->bindValue(':title', "%{$_GET['title']}%");
    		}
    		$stt->execute();
    		while ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
    		?>
    			<tr>
    				<td><?=e($row['title']) ?></td>
    				<td><?=e($row['penname']) ?></td>
    				<td><?=e($row['body']) ?></td>
    				<td>
    				<?php if (is_login()) { ?>
    					<a href="favorite.php?id=<?=e($row['id']) ?>&page=poem_list">お気に入り登録</a>
    				<?php } ?>
    				<?php if (is_login()) { ?>
    					<a href="detail.php?id=<?=e($row['id']) ?>&page=poem_list">コメント</a>
    				<?php } ?>
    				<?php if (is_login() && $row['user_id'] === $_SESSION['user']['id']) { ?>
    					<a href="update_form.php?id=<?=e($row['id']) ?>&page=poem_list">編集</a>
    				<?php } ?>
    				<?php if (is_login() && $row['user_id'] === $_SESSION['user']['id'] || is_admin()) { ?>
    					<a href="delete_form.php?id=<?=e($row['id']) ?>&page=poem_list">削除</a>
    				<?php } ?>
    				</td>
    			</tr>
    		<?php
    		}
    		?>
    		</tbody>
    	</table>
	</main>
	<footer>
		<small>© 2021-2022, Poem Poem</small>
	</footer>
</body>
</html>