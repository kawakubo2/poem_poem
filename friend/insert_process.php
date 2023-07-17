<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

if (authenticate()) {
    exit();
}

try {
    $db = getDb();
    $sql = "SELECT author_id
            FROM friends
            WHERE author_id = :author_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':author_id', $_POST['author_id']);
    $stt->execute();
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['message'] = '既に友達申請しています。';
        header('Location: http://localhost/peom/list.php');
        exit();
    }
    $sql = "INSERT INTO friends(user_id, author_id, memo, is_friend)
            VALUES(:user_id, :author_id, :memo, 0)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
    $stt->bindValue(':author_id', $_POST['author_id']);
    $stt->bindValue(':memo', $_POST['memo']);
    $stt->execute();
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
