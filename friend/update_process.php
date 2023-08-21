<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();
authenticate();

try {
    $db = getDb();
    $sql = "UPDATE friends
            SET status = :status
            WHERE user_id = :user_id
              AND author_id = :author_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':status', $_POST['status']);
    $stt->bindValue(':user_id', $_POST['user_id']);
    $stt->bindValue(':author_id', $_POST['author_id']);
    $stt->execute();

    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/author/update_form.php?id={$_SESSION['user']['id']}");

} catch(PDOException $e) {
    die("エラーメッセージ: {$e->getMessage()}");
}