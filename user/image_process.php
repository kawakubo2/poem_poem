<?php
require_once '../DbManager.php';
require_once '../Encode.php';
session_start();
if ($_SESSION['upload_user_id'] != $_SESSION['user']['id']) {
    die('権限がありません。');
}
if (isset($_FILES['upfile'])) {

    $ext = pathinfo($_FILES['upfile']['name']);

    $perm = [
        'gif',
        'jpg',
        'jpeg',
        'png'
    ];

    $errors = [];
    if ($_FILES['upfile']['error'] !== UPLOAD_ERR_OK) {
        $msg = [
            UPLOAD_ERR_INI_SIZE => 'php.iniのupload_max_filesize制限を超えています。',
            UPLOAD_ERR_FORM_SIZE => 'HTMLのMAX_FILE_SIZE制限を超えています。',
            UPLOAD_ERR_PARTIAL => 'ファイルの一部しかアップロードされていません。',
            UPLOAD_ERR_NO_FILE => 'ファイルはアップロードされませんでした。',
            UPLOAD_ERR_NO_TMP_DIR => '一時保存フォルダが存在しません。',
            UPLOAD_ERR_CANT_WRITE => 'ディスクへの書き込みに失敗しました。',
            UPLOAD_ERR_EXTENSION => '拡張モジュールによってアップロードが中断されました。'
        ];
        $errors[] = $msg[$_FILES['upfile']['error']];
    } else if (! in_array(strtolower($ext['extension']), $perm)) {
        $errors[] = '画像以外のファイルはアップロードできません。';
    } else if (! @getimagesize($_FILES['upfile']['tmp_name'])) {
        $errors[] = 'ファイルの内容が画像ではありません。';
    } else {
        $src = $_FILES['upfile']['tmp_name'];
        $dest = mb_convert_encoding($_FILES['upfile']['name'] ,'SJIS-WIN', 'UTF-8');
        if (! move_uploaded_file($src ,"/images/" . $dest)) {
            $errors[] = 'アップロード処理に失敗しました。';
        }
    }
    if (count($errors) > 0) {
        $_SESSION['update_errors'] = $errors;
        header('Location: http://' . $_SERVER['HTTP_HOST'] .'/user/image_form.php'); 
        exit();
}
    try {
        $db = getDb();
        $sql = "UPDATE users
                   SET profile_filepath = :path
                   WHERE id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':path' ,$_FILES['upfile']['name']);
        $stt->bindValue(':id', $_SESSION['upload_user_id']) ;
        $stt->execute() ;
    } catch (PDOException $e) {
        unlink('/..images/'.$dest);
        die("エラーメッセージ：". $e->getMessage()) ;
    }
    $_SESSION['upload_message'] = '画像' . $_POST['process_name'].'アップロードに成功しました。';
    header('Location: http://' . $_SERVER['HTTP_HOST'] .'/user/image_form.php');
}