<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

try {
    $db = getDb();
    $sql = "SELECT id, penname, profile_filepath
            FROM authors
            WHERE id = :author_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':author_id', $_GET['author_id']);
    $stt->execute(); 
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        ;
    } else {
        die('該当する作家は存在しません。');
    }

    $sql_poem = "SELECT title, body
                 FROM poems
                 WHERE author_id = :author_id";
    $stt_poem = $db->prepare($sql_poem);
    $stt_poem->bindValue(':author_id', $_GET['author_id']);
    $stt_poem->execute();
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>作家詳細 | Poem World</title>
    <link type="text/css" rel="stylesheet" href="/css/main.css" />
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
</head>
<body>
    <a href="../index.php"><img src="/images/poem_world.png" /></a>
    <?php if (is_admin()) { ?>
        <a href="../poem/list.php">一覧へ戻る</a>
    <?php } ?>
    <h2>作家詳細</h2>
    <a href="../friend/insert_form.php">友達申請</a>
    <table class="table">
        <tr><th>ペンネーム</th><td><?=e($row['penname']) ?></td></tr>
        <tr><th></th><td><img src="../images/<?=e($row['profile_filepath']) ?>" /></td></tr>
    </table>
    <hr>
    <table class="table">
        <thead>
            <tr>
                <th>タイトル</th>
                <th>詩</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $stt_poem->fetch(PDO::FETCH_ASSOC)) { ?>
            <tr>
                <td><?=e($row['title']) ?></td>
                <td><?=e($row['body']) ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</body>
</html>