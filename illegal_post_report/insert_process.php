<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

authenticate();

$_SESSION['pome_id'] = $_POST['poem_id'];
$_SESSION['user_id'] = $_POST['user_id'];
$_SESSION['reason'] = $_POST['reason'];
$_SESSION['source'] = $_POST['source'];

//---------------------------------------------------
// 入力値検証
//---------------------------------------------------
$errors = []; // エラーメッセージ格納用配列
// 2024-03-04で間違えていた書き方
// if (mb_strlen(str_replace(trim($_SESSION['reason']), '　', '')) === 0) {
if (mb_strlen(str_replace('　', '', trim($_SESSION['reason']))) === 0) {
    $errors[] = '理由や根拠は必須入力です。';
} else if (mb_strlen($_SESSION['reason']) > 255) {
    $errors[] = '理由や根拠は255文字以内で入力してください。';
}
// 2024-03-04で間違えていた書き方
// if (mb_strlen(str_replace(trim($_SESSION['source']), '　', '')) === 0) {
if (mb_strlen(str_replace('　', '', trim($_SESSION['source']))) === 0) {
    $errors[] = '典拠は必須入力です。';
} else if (mb_strlen($_SESSION['source']) > 255) {
    $errors[] = '典拠は255文字以内入力してください。';
}

if (count($errors) > 0) {
    $_SESSION['illegal_post_report_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . '/illegal_post_report/insert_form.php');
    exit();
}

