<?php
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

if (!is_login()) {
    die('権限がありません。');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/main.css" />
    <title>利用規約違反 | Poem World</title>
</head>
<body>
    <header>
        <a href="../index.php"><img src="/images/poem_world.png" alt="poem worldのアイコン" /></a>
        <h2>利用規約違反</h2>
        <ul id="error_summary">
        <?php
        if (isset($_SESSION['terms_of_use_violation_errors'])) {
            foreach ($_SESSION['terms_of_use_violation_errors'] as $error) {
                print("<li>{$error}</li>");
            }
            unset($_SESSION['terms_of_use_violation_errors']);
        }
        ?>
        </ul>
    </header>
    <main>
        <form method="POST" action="insert_process.php">
            <div class="container">
                <label for="comment">利用規約違反と考えた理由</label>
                <textarea name="reason" id="reason" cols="50" 
                    rows="10"><?=isset($_SESSION['reason']) ? e($_SESSION['reason']) : '' ?></textarea>
            </div>
            <div>
                <input type="hidden" name="poem_id" 
                    value="<?=isset($_GET['poem_id']) ? e($_GET['poem_id']) : $_SESSION['poem_id'] ?>" >
                <input type="hidden" name="user_id" 
                    value="<?=isset($_GET['user_id']) ? e($_GET['user_id']) : $_SESSION['user_id'] ?>" >
                <input type="submit" value="報告" />
            </div>
        </form>
    </main>
</body>
</html>