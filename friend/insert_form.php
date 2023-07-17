<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

if (authenticate()) {
    exit();
}

try {
    $db = getDb();
    $sql = "SELECT id, user_id, penname, profile_filepath
            FROM authors
            WHERE id = :author_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':author_id', $_GET['author_id']);
    $stt->execute();
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        ;
    } else {
        die('指定した作家が見つかりません。');
    }
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h3>友達申請</h3>
    <div>
        <p>作家: <?=$row['penname'] ?></p>
    </div>
    <div>
        <form method="POST" action="insert_process.php">
            <div>
                <label for="memo">コメント(任意): </label><br>
                <textarea id="memo" name="memo"
                    rows="4" cols="50" placeholder="100文字まで"></textarea>
            </div>
            <div>
                <input type="hidden" name="author_id" value="<?=$_GET['author_id'] ?>" >
                <input type="submit" value="申請">
            </div>
        </form>
    </div>
</body>
</html>