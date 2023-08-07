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
            WHERE author_id = :author_id
              AND user_id = :user_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':author_id', $_POST['author_id']);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
    $stt->execute();
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['message'] = '既に友達申請しています。';
        header('Location: http://localhost:3000/poem/list.php');
        exit();
    }
    $sql = "INSERT INTO friends(user_id, author_id, memo, is_friend)
            VALUES(:user_id, :author_id, :memo, 0)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
    $stt->bindValue(':author_id', $_POST['author_id']);
    $stt->bindValue(':memo', $_POST['memo']);
    $stt->execute();
    header('Location: http://localhost:3000/poem/list.php');
    exit();
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
