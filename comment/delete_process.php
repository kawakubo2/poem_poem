<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

authenticate();

try {
    $db = getDb();
    $sql = "DELETE FROM comments
            WHERE id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_POST['id']);
    $stt->execute();

    header('Location: http://' . $_SERVER['HTTP_HOST']
        . '/poem_poem/poem/detail.php');
    exit();
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}