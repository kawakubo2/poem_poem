<?php
require_once '../DbManager.php';
session_start();

/*
print('---< $_POST >---');
print('<pre>');
print_r($_POST);
print('</pre>');

print('---< $_FILES >---');
print('<pre>');
print_r($_FILES);
print('</pre>');

print('---< $_SESSION >---');
print('<pre>');
print_r($_SESSION);
print('</pre>');
*/

if (!isset($_FILES['profile_image'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/poem_poem/index.php');
    exit();
}


$ext = pathinfo($_FILES['profile_image']['name']);

$perm = ['gif', 'jpg', 'jpeg', 'png'];

if (isset($_FILES['profile_image']['error'])) {
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
        $err_msg = $msg[$_FILES['profile_image']['error']];
    } else if (!in_array(strtolower($ext['extension']), $perm)) {
        $err_msg = '画像以外のファイルはアップロードできません。';
    } else if (!@getimagesize($_FILES['profile_image']['tmp_name'])) {
        $err_msg = 'ファイルの内容が画像ではありません。';
    } else {
        $src = $_FILES['profile_image']['tmp_name'];
        $dest = mb_convert_encoding($_FILES['profile_image']['name'], 'SJIS-WIN', 'UTF-8');
        if (!move_uploaded_file($src, '../images/' . $dest)) {
            $err_msg = 'アップロード処理に失敗しました。';
        }
    }

    if (isset($err_msg)) {
        die('<div>' . $err_msg . '</div>');
    }
}

try {
    $db = getDb();
    $sql = "UPDATE authors
            SET profile_filepath = :profile_filepath
            WHERE user_id = :user_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':profile_filepath', $_FILES['profile_image']['name']);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
    $stt->execute();
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}

header('Location: http://' . $_SERVER['HTTP_HOST'] . '/poem_poem/index.php');
