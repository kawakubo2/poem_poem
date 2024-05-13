<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

if (!is_admin()) {
    die('権限がありません');
}

try {
    $db = getDb();
    $sql = "SELECT T.id, T.reason, U.name AS user_name, 
                   A.penname, P.title, T.posted_date
            FROM terms_of_use_violations AS T
                INNER JOIN users AS U ON T.user_id = U.id
                INNER JOIN poems AS P ON T.poem_id = P.id
                INNER JOIN authors AS A ON P.author_id = A.id
            ORDER BY posted_date DESC";
    $stt = $db->prepare($sql);
    $stt->execute();
} catch (PDOException $e) {
    die("エラーメッセージ: {$e->getMessage()}");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <title>利用規約違反報告一覧 | Poem World</title>
</head>
<body>
    <header>
        <a href="../index.php"><img src="/images/poem_world.png" /></a>
        <h3>利用規約違反報告一覧</h3>
    </header>
    <main>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>id</th>
                    <th>利用規約違反と考えた理由</th>
                    <th>報告ユーザ名</th>
                    <th>詩のタイトル</th>
                    <th>作家名</th>
                    <th>報告日</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $stt->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td><?=e($row['id']) ?></td>
                    <td><?=e($row['reason']) ?></td>
                    <td><?=e($row['user_name']) ?></td>
                    <td><?=e($row['title']) ?></td>
                    <td><?=e($row['penname']) ?></td>
                    <td><?=e($row['posted_date']) ?></td>
                </tr>    
            <?php } ?>
            </tbody>
        </table>
    </main>
</body>
</html>
