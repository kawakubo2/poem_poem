<?php
require_once 'common/auth.php';

session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>ホームページ | Poem World</title>
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="css/main.css" />
</head>
<body>
	<!-- <img src="images/logo.gif" alt="ロゴ画像" /> -->
	<h1>Poem World トップページ</h1>
	<header>
		<?php if(is_login()) { ?>
		<nav>
    		<ul>
    		<?php if (is_admin()) { ?>
    			<li><a href="user/list.php">ユーザ一覧</a></li>
    		<?php } ?>
    			<li><a href="author/update_form.php?id=<?=$_SESSION['user']['id'] ?>">作家</a></li>
    			<li><a href="poem/list.php">ポエム</a></li>
    		</ul>
		</nav>
		<?php } ?>
		<section id="account">
			<ul>
			<?php if (is_login()) { ?>
				<li><a class="btn btn-primary" href="login/logout.php">ログアウト</a></li>
			<?php } else { ?>
				<li><a class="btn btn-secondary" href="user/insert_form.php">アカウント作成</a></li>
				<li><a class="btn btn-primary" href="login/login.php">ログイン</a></li>
			<?php } ?>
			</ul>
			<span class="sep"></span>
		</section>
	</header>

</body>
</html>
