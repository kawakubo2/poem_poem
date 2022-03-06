<?php
require_once '../DbManager.php';

session_start();

$_SESSION['user_name'] = $_POST['name'];
$_SESSION['user_email'] = $_POST['email'];
$password = $_POST['password'];

$errors = [];

if (trim(str_replace('　', '', $_SESSION['user_name'])) === '') {
    $errors[] = '名前は必須入力です。';
} else if (mb_strlen($_SESSION['user_name']) > 50){
    $errors[] = '名前は50文字以内で入力してください。';
}

if (trim(str_replace('　', '', $_SESSION['user_email'])) === '') {
    $errors[] = 'メールアドレスは必須入力です。';
} else if (!preg_match('/^\w+([-+.\']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/iD', $_SESSION['user_email'])) {
    $errors[] = '不正なメールアドレスです。';
} else {
    $db = getDb();
    $sql = "SELECT email
            FROM users
            WHERE email = :email";
    $stt = $db->prepare($sql);
    $stt->bindValue(':email', $_SESSION['user_email']);
    $stt->execute();

    if ($stt->fetch(PDO::FETCH_ASSOC)) {
        $errors[] = $_SESSION['user_email'] . 'は既に登録されています。';
    }
}

if (mb_strlen($password) < 8 || mb_strlen($password) > 20) {
    $errors[] = 'パスワードは8文字から20文字で入力してください。';
}

if (count($errors) > 0) {
    $_SESSION['user_insert_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/insert_form.php');
    exit();
}

try {
    $db = getDb();
    $sql = "INSERT INTO users(name, email, password)
            VALUES (:name, :email, :password)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':name', $_SESSION['user_name']);
    $stt->bindValue(':email', $_SESSION['user_email']);
    $stt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
    $stt->execute();

    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);

} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}

header('Location: http://' . $_SERVER['HTTP_HOST'] . '/poem_poem/index.php');