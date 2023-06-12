<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

try {
    $db = getDb();
    $sql = "SELECT id, penname, profile_filepath
            FROM authors
            WHERE id = :author_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':author_id', $_GET['author_id']);
    $row = $stt->execute(); 
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}