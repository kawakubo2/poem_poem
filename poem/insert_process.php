<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

authenticate();
//////////////////////////////
// POSTデータを$_SESSIONに退避
/////////////////////////////
$_SESSION['insert_title'] = $_POST['title'];
$_SESSION['insert_body']  = $_POST['body'];

//////////////////////////////
// 入力チェック
/////////////////////////////

$errors = []; // エラーメッセージ格納用配列

if (trim(str_replace('　', '', $_SESSION['insert_title'])) === '') {
    $errors[] = 'タイトルは必須入力です。';
}

if (mb_strlen($_SESSION['insert_title']) > 100) {
    $errors[] = 'タイトルは100文字以内で入力してください。';
}

if (trim(str_replace('　', '', $_SESSION['insert_body'])) === '') {
    $errors[] = '詩は必須入力です。';
}

if (mb_strlen($_SESSION['insert_body']) > 1000) {
    $errors[] = '詩は1000文字以内で入力してください。';
}

if (count($errors) > 0) {
    $_SESSION['insert_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/insert_form.php');
    exit();
}

try {
    $db = getDb();
    $sql = "SELECT id
            FROM authors
            WHERE user_id = :user_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
    $stt->execute();
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        $author_id = $row['id'];
    }

    $sql = "INSERT INTO poems(title, author_id, body)
            VALUES(:title, :author_id, :body)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':title', $_SESSION['insert_title']);
    $stt->bindValue(':author_id', $author_id);
    $stt->bindValue(':body', $_SESSION['insert_body']);
    $stt->execute();

    $_SESSION['insert_success'] = '登録しました。';
    unset($_SESSION['insert_title']);
    unset($_SESSION['insert_body']);

    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/insert_form.php');
    exit();
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}