<?php
require_once '../DbManager.php';

session_start();

$errors = [];

if (trim(str_replace('　', '', $_POST['email'])) === '') {
    $errors[] = 'メールアドレスは必須入力です。';
}
if (trim(str_replace('　', '', $_POST['password'])) === '') {
    $errors[] = 'パスワードは必須入力です。';
}

if (count($errors) === 0) {
    try {
        $db = getDb();
        $sql = "SELECT id, username, name, email, role, active, password FROM users
                WHERE email = :email";
        $stt = $db->prepare($sql);
        $stt->bindValue(':email', $_POST['email']);
        $stt->execute();

        if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['active'] != 1) {
                die('退会中のためログインできません。');
            }
            $hash_password = $row['password'];
            if (password_verify($_POST['password'], $hash_password)) {
                $_SESSION['user'] = [
                    'id'    => $row['id'],
                    'username' => $row['username'],
                    'name'  => $row['name'],
                    'email' => $row['email'],
                    'role'  => $row['role'],
                    'active'=> $row['active'],
                ];
            } else {
                $errors[] = 'メールアドレスまたはパスワードに誤りがあります。';
            }
        } else {
            $errors[] = 'メールアドレスまたはパスワードに誤りがあります。';
        }
    } catch (PDOException $e) {
        die('エラーメッセージ:' . $e->getMessage());
    }
}

if (count($errors) > 0) {
    $_SESSION['login_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/login.php');
    exit();
} else {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/poem_poem/index.php');
}