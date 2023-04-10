<?php
require_once '../DbManager.php';
require_once '../Encode.php';

session_start();

try {
    $db = getDb();
    $sql = "INSERT INTO favorites(user_id, poem_id)
            VALUES(:user_id, :poem_id)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
    $stt->bindValue(':poem_id', e($_GET['id']));
    $stt->execute();

    header('Location: http://' . $_SERVER['HTTP_HOST']
        . '/poem/list.php');
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}