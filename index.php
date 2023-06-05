<?php
require_once 'DbManager.php';
require_once 'common/auth.php';

session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>ホームページ | Poem World</title>
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="/css/main.css" />
</head>
<body id="poem-poem-top">
	<header>
    	<h1>Poem World トップページ</h1>

		<?php if(is_login()) { ?>

		<?php
			try {
				$db = getDb();
				$sql = "SELECT id
						FROM authors
						WHERE user_id = :user_id";
				$stt = $db->prepare($sql);
				$stt->bindValue(':user_id', $_SESSION['user']['id']);
				$stt->execute();
				if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
					$author_id = $row['id'];
				} else {
					$author_id = '';
				}
			} catch (PDOException $e) {
				die('エラーメッセージ: ' . $e->getMessage());
			}
		?>
		<nav>
    		<ul>
    		<?php if (is_admin()) { ?>
    			<li><a href="/user/list.php">ユーザ一覧</a></li>
    			<li><a href="/user/image_form.php?id=<?=$_SESSION['user']['id'] ?>">ユーザ画像</a></li>
    			<li><a href="/author/list.php">作家一覧</a></li>
    			<li><a href="/poem/list.php">ポエム一覧</a></li>
				<li><a href="/favorite/list.php">お気に入り一覧</a></li>
    		<?php } else { ?>
    			<li><a href="/user/update_form.php?id=<?=$_SESSION['user']['id'] ?>&page=index.php">ユーザ編集</a></li>
    			<li><a href="/user/image_form.php?id=<?=$_SESSION['user']['id'] ?>">ユーザ画像</a></li>
    			<li><a href="/author/list.php">作家一覧</a></li>
				<?php if ($author_id) { ?>
					<li><a href="/author/update_form.php?id=<?=$_SESSION['user']['id'] ?>">作家編集</a></li>
				<?php } else { ?>
					<li><a href="/author/insert_form.php?id=<?=$_SESSION['user']['id'] ?>">作家登録</a></li>
				<?php } ?>
    			<li><a href="/poem/list.php">ポエム一覧</a></li>
				<li><a href="/favorite/list.php">お気に入り一覧</a></li>
    		</ul>
    		<?php } ?>
		</nav>
		<?php } ?>
		<section id="account">
			<ul>
			<?php if (is_login()) { ?>
				<li><a class="btn btn-primary" href="/login/logout.php">ログアウト</a></li>
			<?php } else { ?>
				<li><a class="btn btn-secondary" href="/user/insert_form.php">アカウント作成</a></li>
				<li><a class="btn btn-primary" href="/login/login.php">ログイン</a></li>
			<?php } ?>
			</ul>
			<span class="sep"></span>
		</section>
	</header>
	<main></main>
</body>
</html>
