<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

if (!is_admin()) {
    die('権限がありません');
}

$_SESSION['update_user_name'] = $_POST['name'];
$_SESSION['update_user_email'] = $_POST['email'];
$_SESSION['update_user_role'] = $_POST['role'];

//////////////////////////////////////////////////
// 入力値検証
//////////////////////////////////////////////////

/* エラーメッセージ格納用配列 */
$errors = [];

if (trim(str_replace('　', '', $_SESSION['update_user_name'])) === '') {
    $errors[] = '名前は必須入力です。';
} else if (mb_strlen($_SESSION['update_user_name']) > 50){
    $errors[] = '名前は50文字以内で入力してください。';
}

if (trim(str_replace('　', '', $_SESSION['update_user_email'])) === '') {
    $errors[] = 'メールアドレスは必須入力です。';
} else if (!preg_match('/^\w+([-+.\']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/iD', $_SESSION['update_user_email'])) {
    $errors[] = '不正なメールアドレスです。';
} else {
    /*
    $db = getDb();
    $sql = "SELECT email
            FROM users
            WHERE email = :email";
    $stt = $db->prepare($sql);
    $stt->bindValue(':email', $_SESSION['update_user_email']);
    $stt->execute();

    if ($stt->fetch(PDO::FETCH_ASSOC)) {
        $errors[] = $_SESSION['update_user_email'] . 'は既に登録されています。';
    }
    */
}

if (count($errors) > 0) {
    $_SESSION['update_user_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/update_form.php');
    exit();
}

//////////////////////////////////////////////////
// データベースの更新
//////////////////////////////////////////////////

try {
    $db = getDb();
    $sql = "UPDATE users
            SET role = :role, name = :name
            WHERE id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':role', $_SESSION['update_user_role']);
    $stt->bindValue(':name', $_SESSION['update_user_name']);
    $stt->bindValue(':id', $_SESSION['update_user_id']);
    $stt->execute();

    unset($_SESSION['update_user_id']);
    unset($_SESSION['update_user_name']);
    unset($_SESSION['update_user_email']);
    unset($_SESSION['update_user_role']);

    $_SESSION['update_success_message'] = 'ユーザの更新に成功しました。';
    header('Location: http://' . $_SERVER['HTTP_HOST'] .
        dirname($_SERVER['PHP_SELF']) . '/list.php');
    exit();

} catch (PDOException $e) {
    die('エラーメッセージ:' . $e->getMessage());
}