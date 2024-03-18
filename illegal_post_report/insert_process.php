<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

authenticate();

$_SESSION['illegal_post_poem_id'] = $_POST['poem_id'];
$_SESSION['user_id'] = $_POST['user_id'];
$_SESSION['reason'] = $_POST['reason'];
$_SESSION['source'] = $_POST['source'];

//---------------------------------------------------
// 入力値検証
//---------------------------------------------------
$errors = []; // エラーメッセージ格納用配列
if (str_replace('　', '', trim($_SESSION['reason'])) === '') {
    $errors[] = '理由や根拠は必須入力です。';
} else if (mb_strlen($_SESSION['reason']) > 255) {
    $errors[] = '理由や根拠は255文字以内で入力してください。';
}
if (str_replace('　', '', trim($_SESSION['source'])) === '') {
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

try {
    $db = getDb();
    $sql = "INSERT INTO illegal_post_reports(user_id, poem_id, reason, source, report_datetime)
            VALUES(:user_id, :poem_id, :reason, :source, :report_datetime)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_POST['user_id']);
    $stt->bindValue(':poem_id', $_POST['poem_id']);
    $stt->bindValue(':reason', $_POST['reason']);
    $stt->bindValue(':source', $_POST['source']);
    $stt->bindValue(':report_datetime', date('Y-m-d H:i:s'));
    
    $stt->execute();

    // 2024-03-18に追加する分
    $_SESSION['illegal_post_report_success'] = '報告が完了しました';

    header('Location: http://' . $_SERVER['HTTP_HOST']
        . '/illegal_post_report/insert_form.php');

    // 2024-03-18に追加する分
    unset($_SESSION['reason']);
    unset($_SESSION['source']);
} catch(PDOException $e) {
    die("エラーメッセージ: {$e->getMessage()}");
}