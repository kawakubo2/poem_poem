<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

authenticate();
authorize($_SESSION['user_id']);

if (isset($_POST['cancel'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/list.php');
    exit();
} else if (isset($_POST['delete'])) {
    try {
        $db = getDb();
        $sql = "DELETE FROM poems
                WHERE id= :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_POST['id']);
        $stt->execute();
        header('Location: http://' . $_SERVER['HTTP_HOST']
            . dirname($_SERVER['PHP_SELF']) . '/list.php');
        unset($_SESSION['user_id']);
        exit();
    } catch (PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
}