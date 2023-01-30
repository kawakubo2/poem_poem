<?php
require_once '../DbManager.php';

session_start();

$_SESSION['insert_username'] = $_POST['username'];
$_SESSION['insert_name'] = $_POST['name'];
$_SESSION['insert_email'] = $_POST['email'];
$password = $_POST['password'];

$errors = [];

if (trim(str_replace('　', '', $_SESSION['insert_username'])) === '') {
    $errors[] = 'ユーザ名は必須入力です。';
} else if (mb_strlen($_SESSION['insert_username']) > 50) {
    $errors[] = 'ユーザ名は50文字以内で入力してください。';
} else {
    try {
        $db = getDb();
        $sql = "SELECT username
                FROM users
                WHERE username = :username";
        $stt = $db->prepare($sql);
        $stt->bindValue(':username', $_SESSION['insert_username']);
        if ($stt->fetch(PDO::FETCH_ASSOC)) {
            $errors[] = "既に{$_SESSION['insert_username']}は存在します。";
        }
    } catch (PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
}

if (trim(str_replace('　', '', $_SESSION['insert_name'])) === '') {
    $errors[] = '名前は必須入力です。';
} else if (mb_strlen($_SESSION['insert_name']) > 50){
    $errors[] = '名前は50文字以内で入力してください。';
}

if (trim(str_replace('　', '', $_SESSION['insert_email'])) === '') {
    $errors[] = 'メールアドレスは必須入力です。';
} else if (!preg_match('/^\w+([-+.\']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/iD', $_SESSION['insert_email'])) {
    $errors[] = '不正なメールアドレスです。';
} else {
    $db = getDb();
    $sql = "SELECT email
            FROM users
            WHERE email = :email";
    $stt = $db->prepare($sql);
    $stt->bindValue(':email', $_SESSION['insert_email']);
    $stt->execute();

    if ($stt->fetch(PDO::FETCH_ASSOC)) {
        $errors[] = $_SESSION['insert_email'] . 'は既に登録されています。';
    }
}

if (mb_strlen($password) < 8 || mb_strlen($password) > 20) {
    $errors[] = 'パスワードは8文字から20文字で入力してください。';
}

if (count($errors) > 0) {
    $_SESSION['insert_insert_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/insert_form.php');
    exit();
}

try {
    $db = getDb();
    $sql = "INSERT INTO users(username, name, email, role, password)
            VALUES (:username, :name, :email, 'user', :password)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':username', $_SESSION['insert_username']);
    $stt->bindValue(':name', $_SESSION['insert_name']);
    $stt->bindValue(':email', $_SESSION['insert_email']);
    $stt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
    $stt->execute();

    unset($_SESSION['insert_username']);
    unset($_SESSION['insert_name']);
    unset($_SESSION['insert_email']);

} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}

header('Location: http://' . $_SERVER['HTTP_HOST'] . '/poem_poem/index.php');