<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

    print("--- debug1 ---");
    print("<pre>");
    print_r($_FILES);
    print("</pre>");

if (!is_admin() && $_SESSION['update_id'] !== $_SESSION['user']['id']) {
    die('権限がありません');
}

$_SESSION['update_name'] = $_POST['name'];
$_SESSION['update_email'] = $_POST['email'];

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
            SET name = :name, email = :email
            WHERE id = :id";
    $stt = $db->prepare($sql);
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

    // 2024-06-17
    // ここでexit()してはいけない
    // exit();

} catch (PDOException $e) {
    die('エラーメッセージ:' . $e->getMessage());
}

if (isset($_FILES['profile_image'])) {
    $ext = pathinfo($_FILES['profile_image']['name']);

    $perm = ['gif', 'jpg', 'jpeg', 'png'];

    // 2024-06-03 授業後変更
    print("--- debug3 ---");
    print("<pre>");
    print_r($_FILES['profile_image']);
    print("</pre>");
    if ($_FILES['profile_image']['size'] > 0) {
        print("--- debug4 ---");
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
            $_SESSION['update_user_errors'] = $errors;
            header('Location: http://' . $_SERVER['HTTP_HOST']
                . dirname($_SERVER['PHP_SELF']) . "/update_form.php?id={$_SESSION['user']['id']}");
            exit();
        }
        try {
            $db = getDb();
            $sql = "UPDATE users
                SET profile_filepath = :profile_filepath
                WHERE id = :user_id";
            $stt = $db->prepare($sql);
            $stt->bindValue(':profile_filepath', $_FILES['profile_image']['name']);
            $stt->bindValue(':user_id', $_SESSION['user']['id']);
            $stt->execute();
            unset($_SESSION['user_id']);
        } catch(PDOException $e) {
            die('エラーメッセージ: ' . $e->getMessage());
        }
    }

    $_SESSION['user_update_success_message'] = '更新しました。';
    header("Location: http://" . $_SERVER['HTTP_HOST'] 
        . dirname($_SERVER['PHP_SELF']) . "/update_form.php?id={$_SESSION['user']['id']}");
}

if (is_admin()) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] .
        dirname($_SERVER['PHP_SELF']) . '/list.php');
} else if (is_user()) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] .
        dirname($_SERVER['PHP_SELF']) . "/update_form.php?id={$_SESSION['user']['id']}");
}
exit();