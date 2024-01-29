<?php
require_once '../DbManager.php';
require_once '../Encode.php';

session_start();

try {
    $db = getDb();

    // 既に登録しているものは登録しない
    $sql_exists = "SELECT poem_id
                   FROM favorites
                   WHERE 
                    user_id = :user_id
                    and
                    poem_id = :poem_id";
    $stt_exists = $db->prepare($sql_exists);
    $stt_exists->bindValue(':user_id', $_SESSION['user']['id']);
    $stt_exists->bindValue(':poem_id', $_GET['id']);
    $stt_exists->execute();
    if ($row = $stt_exists->fetch(PDO::FETCH_ASSOC)) {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/favorite/list.php');
        exit();
    }

    $sql = "INSERT INTO favorites(user_id, poem_id)
            VALUES(:user_id, :poem_id)";
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
    $stt->bindValue(':poem_id', e($_GET['id']));
    $stt->execute();

    $page = '';
    if ($_GET['page'] === 'poem_list') {
        $page = '/poem/list.php';
    } else if ($_GET['page'] === 'top30') {
        $page = '/poem/top30.php';
    }
    header('Location: http://' . $_SERVER['HTTP_HOST']
        . $page);
} catch(PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}