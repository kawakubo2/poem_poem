<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

if (!is_login()) {
    die('権限がありません');
}

$_SESSION['reason'] = $_POST['reason'];

$errors = [];

if (trim($_SESSION['reason']) === '') {
    $errors[] = '利用規約違反と考えた理由は必須入力です。';
} else {
    if (mb_strlen($_SESSION['reason']) > 255) {
        $errors[] = '利用規約と考えた理由は255文字以内で入力してください。';
    }
}

if (count($errors) > 0) {
    $_SESSION['terms_of_use_violation_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . $_SERVER['PHP_SELF'] . '/insert_form.php');
}