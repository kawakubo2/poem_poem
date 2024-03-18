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
        <h2>オリジナリティ？</h2>
        <p style="color: blue">
        <!-- 2024-03-18追加予定分 -->
        <?php
            if (isset($_SESSION['illegal_post_report_success']) 
                   && $_SESSION['illegal_post_poem_id']
                   && $_SESSION['user_id']) {
                print($_SESSION['illegal_post_report_success']);
                unset($_SESSION['illegal_post_report_success']);
                unset($_SESSION['illegal_post_poem_id']);
                unset($_SESSION['user_id']);
            }
        ?>
        </p>
        <ul id="error_summary">
        <?php
            if (isset($_SESSION['illegal_post_report_errors'])) {
                foreach ($_SESSION['illegal_post_report_errors'] as $error) {
        ?>
                    <li><?=e($error) ?></li>
        <?php
                }
                unset($_SESSION['illegal_post_report_errors']);
            }
        ?>
        </ul>
    </header>
    <main>
        <form method="POST" action="insert_process.php">
            <div class="container">
                <label for="reason">似ていると考えた理由や根拠</label><br>
                <textarea name="reason" id="reason" 
                            cols="30" rows="10"><?=e($_SESSION['reason']) ?></textarea>
            </div>
            <div class="container">
                <label for="source">典拠(書籍名、雑誌名、サイト名等)</label><br>
                <textarea name="source" id="source" 
                            cols="30" rows="10"><?=e($_SESSION['source']) ?></textarea>
            </div>
            <div class="container">
                <input type="hidden" name="poem_id" value="<?=isset($_GET['poem_id']) ? e($_GET['poem_id']) : e($_SESSION['poem_id']) ?>" />
                <input type="hidden" name="user_id" value="<?=isset($_GET['user_id']) ? e($_GET['user_id']) : e($_SESSION['user_id']) ?>" />
                <input type="submit" value="報告" />
            </div>
        </form>
    </main>
</body>
</html>