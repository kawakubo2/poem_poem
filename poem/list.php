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
        <a href="../index.php"><img src="/images/poem_world.png" /></a>
    	<h3>詩の一覧</h3>
    	<p><a href="insert_form.php">詩の登録</a>
		<p id="error_summary">
		<?php
			if ($_SESSION['message']) {
				print($_SESSION['message']);
				unset($_SESSION['message']);
			}
		?>
		</p>
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
    		<label for="penname">作家: </label>
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
    			<tr><th>タイトル</th><th>作家</th><th>詩</th><th>お気に入り数</th><th></th></tr>
    		</thead>
    		<tbody>
    		<?php
    		$db = getDb();
    		$sql = "SELECT 
						P.id, P.title, A.id AS author_id, A.penname, P.body, A.user_id, 
						FRI.status, R.poem_id AS favorite, FAV.fav_count, 
						LOGIN_AUTHOR.id AS login_author_id
                    FROM poems AS P
                        INNER JOIN authors AS A ON P.author_id = A.id
						LEFT OUTER JOIN
						(
							SELECT author_id, status
							FROM friends
							WHERE user_id = :user_id
						) AS FRI
							ON A.id = FRI.author_id
						LEFT OUTER JOIN (
							SELECT id
							FROM authors
							WHERE user_id = :user_id
						) AS LOGIN_AUTHOR
							ON P.author_id = LOGIN_AUTHOR.id
						LEFT OUTER JOIN
						(
							SELECT poem_id, count(*) AS fav_count
							FROM favorites
							GROUP BY poem_id
						) AS FAV
							ON P.id = FAV.poem_id
						LEFT OUTER JOIN
						(
							SELECT poem_id
							FROM favorites
							WHERE user_id = :user_id
						) AS R
							ON P.id = R.poem_id
                    WHERE 1 = 1";
    		if (isset($_GET['penname']) && $_GET['penname'] !== '') {
    		    $sql .= " AND P.author_id = :author_id";
    		}
    		if (isset($_GET['title']) && $_GET['title'] !== '') {
    		    $sql .= " AND P.title LIKE :title";
    		}
    		$stt = $db->prepare($sql);
			$stt->bindValue(':user_id', $_SESSION['user']['id']);
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
					<td><?=($row['fav_count'] === null) ? "": e($row['fav_count']) ?></td>
    				<td>
    				<?php 
						if (is_login()) { 
							if ($row['favorite']) { 	
					?>
								<span>お気に入り登録済</span>
					<?php 	} else { ?>
							<a href="../favorite/favorite.php?id=<?=e($row['id']) ?>&page=poem_list">お気に入り登録</a>
					<?php
							}

							if ($row['author_id'] != $row['login_author_id']) {
					?>
							<a href="../author/detail.php?author_id=<?=$row['author_id'] ?>">作家</a>
					<?php
							}
						}	
					?>
    				<?php if (is_login() && $row['status'] === '承認') { ?>
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