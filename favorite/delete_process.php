<?php
require_once '../DbManager.php';

try {
    $db = getDb();
    $sql = "DELETE FROM favorites
            WHERE id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_POST['id']);
    $stt->execute();

    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/favorite/list.php');
    exit();
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}