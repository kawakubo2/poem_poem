<?php
require_once '../DbManager.php';

session_start();

if (isset($_POST['cancel'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/list.php');
    exit();
} else if (isset($_POST['delete'])) {
    try {
        $db = getDb();
        $sql = "UPDATE authors
                SET delete_flag = 1
                WHERE id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_POST['id']);
        $stt->execute();

        header('Location: http://' . $_SERVER['HTTP_HOST']
            . dirname($_SERVER['PHP_SELF']) . '/list.php');

    } catch (PDOException $e) {
        die('エラーメッセージ: '. $e->getMessage());
    }
}