<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();
if (!is_login()) {
    print('ログインが必要です。');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="/css/main.css" />
    <title>不正報告 | Poem World</title>
</head>
<body>
    <header>
        <a href="../index.php"><img src="/images/poem_world.png" /></a>
        <a href="../poem/list.php">一覧へ戻る</a>
        <h2>不正報告</h2>
    </header>
    <main>
        <form method="POST" action="insert_process.php">
            <div class="container">
                <label for="reason">不正と考えた理由や根拠</label><br>
                <textarea name="reason" id="reason" 
                            cols="30" rows="10"></textarea>
            </div>
            <div class="container">
                <label for="source">典拠(書籍名、雑誌名、サイト名等)</label><br>
                <textarea name="source" id="source" 
                            cols="30" rows="10"></textarea>
            </div>
            <div class="container">
                <input type="hidden" name="poem_id" value="<?=e($_GET['poem_id']) ?>" />
                <input type="hidden" name="user_id" value="<?=e($_GET['user_id']) ?>" />
                <input type="submit" value="報告" />
            </div>
        </form>
    </main>
</body>
</html>