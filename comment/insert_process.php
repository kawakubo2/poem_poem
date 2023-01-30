<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

authenticate();

session_start();

$_SESSION['insert_poem_id'] = $_POST['poem_id'];
$_SESSION['insert_comment'] = $_POST['comment'];

$errors = [];

if ($_SESSION['insert_comment'] === '') {
    $errors[] = 'コメントは必須入力です。';
} else if (mb_strlen($_SESSION['insert_comment']) > 64) {
    $errors[] = 'コメントは64文字以内で入力してください。';
}

if (count($errors) > 0) {
    $_SESSION['comment_insert_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . '/poem/detail.php');
    exit();
}

try {
    $db = getDb();
    $sql = "INSERT INTO comments(poem_id, comment, user_id)
            VALUES(:poem_id, :comment, :user_id)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':poem_id', $_SESSION['insert_poem_id']);
    $stt->bindValue(':comment', $_SESSION['insert_comment']);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
    $stt->execute();

    unset($_SESSION['insert_comment']);

    header('Location: http://' . $_SERVER['HTTP_HOST']
        . '/poem/detail.php');
    exit();


} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}