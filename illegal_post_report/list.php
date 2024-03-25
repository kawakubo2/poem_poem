<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

if (!is_admin()) {
    die('アクセス権がありません。');
}

try {
    $db = getDb();
    $sql = "SELECT P.id AS 詩のid, A.penname AS 作家名, U.name AS ユーザ名, 
                I.reason AS 似ていると考えた理由や根拠,
                I.source AS 典拠
            FROM illegal_post_reports AS I
                INNER JOIN users AS U
                    ON I.user_id = U.id
                INNER JOIN poems AS P
                    ON I.poem_id = P.id
                INNER JOIN authors AS A
                    ON P.author_id = A.id";
    $stt = $db->prepare($sql);
    $stt->execute();
} catch(PDOException $e) {
    die("エラーメッセージ: {$e->getMessage()}");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <title>報告一覧</title>
</head>
<body>
    <header>
        <a href="../index.php"><img src="/images/poem_world.png" /></a>
        <h3>報告一覧</h3>
    </header>
    <main>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>詩のid</th>
                    <th>作家名</th>
                    <th>ユーザ名</th>
                    <th>似ていると考えた理由や根拠</th>
                    <th>典拠</th>
                </tr>
            </thead>
            <tbody>
        <?php while ($row = $stt->fetch(PDO::FETCH_ASSOC)) { ?>
            <tr>
                <td><?=e($row['詩のid']) ?></td>
                <td><?=e($row['作家名']) ?></td>
                <td><?=e($row['ユーザ名']) ?></td>
                <td><?=e($row['似ていると考えた理由や根拠']) ?></td>
                <td><?=e($row['典拠']) ?></td>
            </tr>
        <?php } ?>
            </tbody>
        </table>
    </main>

</body>
</html>