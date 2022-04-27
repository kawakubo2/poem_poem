<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

if (!is_admin()) {
    die('権限がありません。');
}

if (isset($_POST['cancel'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/list.php');
    exit();
} else if (isset($_POST['delete'])) {
    try {
        $db = getDb();
        $sql = "UPDATE users
                SET active = 0
                WHERE id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_POST['id']);
        $stt->execute();

        $_SESSION['delete_success_message'] = '削除に成功しました。';

        header('Location: http://' . $_SERVER['HTTP_HOST']
            . dirname($_SERVER['PHP_SELF']) . '/list.php');
        exit();
    } catch (PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
}