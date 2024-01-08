<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

authenticate();

try {
    $db = getDb();
    $sql = "SELECT 
                P.id, P.title, A.id AS author_id, A.penname, P.body, A.user_id,
                P.posted_date, FAV.fav_count
            FROM favorites AS FAV
                INNER JOIN poems AS P ON FAV.poem_id = P.id
            WHERE P.posted_date >= SUBDATE(current_date(), INTERNAL 30 DAY)
                AND
                P.poem_id IN
                (
                    SELECT poem_id
                    FROM
                    (
                        SELECT poem_id, COUNT(*) AS お気に入り数
                        FROM favorites
                        GROUP BY poem_id
                    ) AS FAV_COUNT
                    ORDER BY FAV_COUNT.お気に入り数 DESC
                    LIMIT 5
                )";
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
    <title>過去1か月のランキング</title>
</head>
<body>
	<a href="../index.php"><img src="/images/poem_world.png" /></a>
    <h3>1か月ランキング</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ランク</th><th>タイトル</th><th>作家</th><th>詩</th><th>お気に入り数</th><th></th>
            </tr>
        </thead>
        <tbody>
<?php
        $rank = 1;
        while ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
?>
            <tr>
                <td><?=$rank ?></td>
                <td><?=e($row['title']) ?></td>
                <td><?=e($row['penname']) ?></td>
                <td><?=e($row['body']) ?></td>
                <td><?=e($row['お気に入り数']) ?></td>
                <td></td>
            </tr>
<?php
            $rank++;
        }
?>
        </tbody>
    </table>
</body>
</html>