<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

$_SESSION['role_update_role'] = $_POST['role'];

if (!is_admin()) {
    die('権限がありません');
}

try {
    $db = getDb();
    $sql = "UPDATE users
            SET role = :role
            WHERE id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':role', $_SESSION['role_update_role']);
    $stt->bindValue(':id', $_SESSION['role_update_id']);
    $stt->execute();

    unset($_SESSION['role_update_id']);
    unset($_SESSION['role_update_username']);
    unset($_SESSION['role_update_name']);
    unset($_SESSION['role_update_email']);
    unset($_SESSION['role_update_role']);

    header('Location: http://' . $_SERVER['HTTP_HOST']
        . '/user/list.php');
    exit();
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}