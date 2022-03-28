<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

authenticate();
authorize($_SESSION['user_id']);

//////////////////////////////
// POSTデータを$_SESSIONに退避
/////////////////////////////
$_SESSION['update_title'] = $_POST['title'];
$_SESSION['update_body']  = $_POST['body'];

//////////////////////////////
// 入力チェック
/////////////////////////////

$errors = []; // エラーメッセージ格納用配列

if (trim(str_replace('　', '', $_SESSION['update_title'])) === '') {
    $errors[] = 'タイトルは必須入力です。';
}

if (mb_strlen($_SESSION['update_title']) > 100) {
    $errors[] = 'タイトルは100文字以内で入力してください。';
}

if (trim(str_replace('　', '', $_SESSION['update_body'])) === '') {
    $errors[] = '詩は必須入力です。';
}

if (mb_strlen($_SESSION['update_body']) > 1000) {
    $errors[] = '詩は1000文字以内で入力してください。';
}

if (count($errors) > 0) {
    $_SESSION['update_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/update_form.php');
    exit();
}

try {
    $db = getDb();

    $sql = "UPDATE poems
            SET title = :title, body = :body
            WHERE id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':title', $_SESSION['update_title']);
    $stt->bindValue(':body', $_SESSION['update_body']);
    $stt->bindValue(':id', $_SESSION['update_id']);
    $stt->execute();

    $_SESSION['update_success'] = '更新しました。';
    unset($_SESSION['update_id']);
    unset($_SESSION['update_title']);
    unset($_SESSION['update_body']);
    unset($_SESSION['user_id']);

    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/list.php');
    exit();
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}