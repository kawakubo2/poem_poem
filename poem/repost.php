<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

authenticate();

try {
    $db = getDb();
    $sql = "SELECT user_id
            FROM authors
            WHERE id = 
            ( 
                SELECT author_id
                FROM poems
                WHERE id = :poem_id
            )";
    $stt = $db->prepare($sql);
    $stt->bindValue(':poem_id', $_GET['poem_id']);
    $stt->execute();
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['user_id'] != $_SESSION['user']['id']) {
            die('再投稿の資格がありません。');
        }
    } else {
        die('該当する詩が存在しません。');
    }
    $sql = "UPDATE poems
            SET posted_date = CURRENT_DATE()
            WHERE id = :poem_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':poem_id', $_GET['poem_id']);
    $stt->execute();

    header('Location: http://' . $_SERVER['HTTP_HOST']
        . "/author/update_form.php?id=" . e($_SESSION['user']['id']));
    exit();
} catch (PDOException $e) {
    die("エラーメッセージ: {$e->getMessage()}");
}