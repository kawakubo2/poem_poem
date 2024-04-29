<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

if (!is_login()) {
    die('権限がありません');
}

$_SESSION['reason'] = $_POST['reason'];
$_SESSION['poem_id'] = $_POST['poem_id'];
$_SESSION['user_id'] = $_POST['user_id'];

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
        . '/terms_of_use_violation/insert_form.php');
    exit();
}

try {
    $db = getDb();
    $sql = "INSERT INTO terms_of_use_violations(poem_id, user_id, reason)
            VALUES(:poem_id, :user_id, :reason)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':poem_id', $_SESSION['poem_id']);
    $stt->bindValue(':user_id', $_SESSION['user_id']);
    $stt->bindValue('reason', $_SESSION['reason']);
    $stt->execute();
    
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . '/poem/detail.php');

    unset($_SESSION['poem_id']);
    unset($_SESSION['user_id']);
    unset($_SESSION['reason']);

    exit();

} catch (PDOException $e) {
    die("エラーメッセージ: {$e->getMessage()}");
}