<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

if (!is_admin() && $_SESSION['update_id'] !== $_SESSION['user']['id']) {
    die('権限がありません');
}

$_SESSION['update_name'] = $_POST['name'];
$_SESSION['update_email'] = $_POST['email'];
$_SESSION['update_role'] = $_POST['role'];

//////////////////////////////////////////////////
// 入力値検証
//////////////////////////////////////////////////

/* エラーメッセージ格納用配列 */
$errors = [];

if (trim(str_replace('　', '', $_SESSION['update_name'])) === '') {
    $errors[] = '名前は必須入力です。';
} else if (mb_strlen($_SESSION['update_name']) > 50){
    $errors[] = '名前は50文字以内で入力してください。';
}

if (trim(str_replace('　', '', $_SESSION['update_email'])) === '') {
    $errors[] = 'メールアドレスは必須入力です。';
} else if (!preg_match('/^\w+([-+.\']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/iD', $_SESSION['update_email'])) {
    $errors[] = '不正なメールアドレスです。';
} else {
    if ($_SESSION['update_old_email'] !== $_SESSION['update_email']) {
        $db = getDb();
        $sql = "SELECT email
            FROM users
            WHERE email = :email";
        $stt = $db->prepare($sql);
        $stt->bindValue(':email', $_SESSION['update_email']);
        $stt->execute();

        if ($stt->fetch(PDO::FETCH_ASSOC)) {
            $errors[] = $_SESSION['update_email'] . 'は既に登録されています。';
        }
    }
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
            SET role = :role, name = :name, email = :email
            WHERE id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':role', $_SESSION['update_role']);
    $stt->bindValue(':name', $_SESSION['update_name']);
    $stt->bindvalue(':email', $_SESSION['update_email']);
    $stt->bindValue(':id', $_SESSION['update_id']);
    $stt->execute();

    unset($_SESSION['update_id']);
    unset($_SESSION['update_username']);
    unset($_SESSION['update_name']);
    unset($_SESSION['update_email']);
    unset($_SESSION['update_role']);

    $_SESSION['update_success_message'] = 'ユーザの更新に成功しました。';
    if (is_admin()) {
        header('Location: http://' . $_SERVER['HTTP_HOST'] .
            dirname($_SERVER['PHP_SELF']) . '/list.php');
    } else if (is_user()) {
        header('Location: http://' . $_SERVER['HTTP_HOST'] .
            '/poem_poem/index.php');
    }

    exit();

} catch (PDOException $e) {
    die('エラーメッセージ:' . $e->getMessage());
}