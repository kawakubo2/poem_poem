<?php
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();
authenticate();

try {
    $db = getDb();
    // 作家に対する友達申請に対し"承認"または"拒否"をstatusに設定する
    $sql = "UPDATE friends
            SET status = :status
            WHERE user_id = :user_id
              AND author_id = :author_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':status', $_POST['status']);
    $stt->bindValue(':user_id', $_POST['user_id']);
    $stt->bindValue(':author_id', $_POST['author_id']);
    $stt->execute();

    $sql = "SELECT count(*) AS 拒否回数
            FROM friends
            WHERE user_id = :user_id
              AND status = '拒否'";
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_POST['user_id']);
    $stt->execute();
} catch(PDOException $e) {
    die("エラーメッセージ: {$e->getMessage()}");
}

$row = $stt->fetch(PDO::FETCH_ASSOC);

if ($row['拒否回数'] >= 3) {
    try {
        $db = getDb();
        $db->beginTransaction();
        // favoritesテーブルから削除
        $stt = $db->prepare("DELETE FROM favorites WHERE user_id = :user_id");
        $stt->bindValue(':user_id', $_POST['user_id']);
        $stt->execute();
        // commentsテーブルから削除
        $stt = $db->prepare("DELETE FROM comments WHERE user_id = :user_id");
        $stt->bindValue(':user_id', $_POST['user_id']);
        $stt->execute();
        // friendsテーブルから削除
        $stt = $db->prepare("DELETE FROM friends WHERE user_id = :user_id");
        $stt->bindValue(':user_id', $_POST['user_id']);
        $stt->execute();
        // 指定したuserを一定期間無効化する(一定期間をどれくらにするかはTODO) 
        $sql_deactivate = "UPDATE users
                           SET active = 0
                           WHERE id = :user_id";
        $stt = $db->prepare($sql_deactivate);
        $stt->bindValue(':user_id', $_POST['user_id']);
        $stt->execute();

        $db->commit();
    } catch(PDOException $e) {
        $db->rollBack();
        die("エラーメッセージ: {$e->getMessage()}");
    }
}
    
header("Location: http://" . $_SERVER['HTTP_HOST'] . "/author/update_form.php?id={$_SESSION['user']['id']}");