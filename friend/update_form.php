<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

authenticate();

try {
    $db = getDb();
    $sql = "SELECT id, profile_filepath
            FROM users
            WHERE id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_GET['user_id']);
    $stt->execute();
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        ;
    } else {
        die("エラーメッセージ: {$e->getMessage()}");
    }
} catch(PDOException $e) {
    die("エラーメッセージ: {$e->getMessage()}");
}
