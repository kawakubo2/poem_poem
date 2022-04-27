<?php
require_once '../DbManager.php';
require_once '../common/auth.php';
session_start();

$_SESSION['insert_user_id'] = $_POST['user_id'];
$_SESSION['insert_penname'] = $_POST['penname'];
$_SESSION['insert_profile_filepath'] = $_POST['profile_filepath'];

authenticate();

$errors = [];

if (trim($_SESSION['insert_penname']) === '') {
    $errors[] = 'ペンネームは必須入力です。';
} else {
    try {
        $db = getDb();
        $sql = "SELECT count(*) as 件数
                    FROM authors
                    WHERE penname = :penname";
        $stt = $db->prepare($sql);
        $stt->bindValue(':penname', $_SESSION['insert_penname']);
        $stt->execute();

        $row = $stt->fetch(PDO::FETCH_ASSOC);
        if ($row['件数'] > 0) {
            $errors[] = $_SESSION['insert_penname'] . 'は既に存在します。';
        }
    } catch (PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
}


$ext = pathinfo($_FILES['profile_image']['name']);

$perm = ['gif', 'jpg', 'jpeg', 'png'];

if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
    $msg = [
        UPLOAD_ERR_INI_SIZE => 'php.iniのupload_max_filesize制限を超えています。',
        UPLOAD_ERR_FORM_SIZE => 'HTMLのMAX_FILE_SIZE制限を超えています。',
        UPLOAD_ERR_PARTIAL => 'ファイルの一部しかアップロードされていません。',
        UPLOAD_ERR_NO_FILE => 'ファイルはアップロードされませんでした。',
        UPLOAD_ERR_NO_TMP_DIR => '一時保存フォルダが存在しません。',
        UPLOAD_ERR_CANT_WRITE => 'ディスクへの書き込みに失敗しました。',
        UPLOAD_ERR_EXTENSION => '拡張モジュールによってアップロードが中断されました。'
    ];
    $errors[] = $msg[$_FILES['profile_image']['error']];
} else if (!in_array(strtolower($ext['extension']), $perm)) {
    $errors[] = '画像以外のファイルはアップロードできません。';
} else if (!@getimagesize($_FILES['profile_image']['tmp_name'])) {
    $errors[] = 'ファイルの内容が画像ではありません。';
} else {
    $src = $_FILES['profile_image']['tmp_name'];
    $dest = mb_convert_encoding($_FILES['profile_image']['name'], 'SJIS-WIN', 'UTF-8');
    if (!move_uploaded_file($src, '../images/' . $dest)) {
        $errors[] = 'アップロード処理に失敗しました。';
    }
}

if (count($errors) > 0) {
    $_SESSION['author_insert_errors'] = $errors;
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . dirname($_SERVER['PHP_SELF']) . '/insert_form.php');
    exit();
}

try {
    $db = getDb();
    $sql = "INSERT INTO authors(user_id, penname, profile_filepath)
            VALUES(:user_id, :penname, :profile_filepath)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
    $stt->bindValue(':penname', $_POST['penname']);
    $stt->bindValue(':profile_filepath', $_FILES['profile_image']['name']);
    $stt->execute();
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}

header('Location: http://' . $_SERVER['HTTP_HOST'] . '/poem_poem/index.php');
