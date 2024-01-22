<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

authenticate();

try {
    $db = getDb();
    $sql = "SELECT 
                P.id, P.title, A.id AS author_id, A.penname, P.body, FAV_COUNT.お気に入り数,
                LOGIN_AUTHOR.id AS login_author_id
            FROM poems AS P
                INNER JOIN authors AS A ON P.author_id = A.id
                LEFT OUTER JOIN
                (
                    SELECT poem_id, COUNT(*) AS お気に入り数
                    FROM favorites
                    GROUP BY poem_id
                ) AS FAV_COUNT
                ON P.id = FAV_COUNT.poem_id
                LEFT OUTER JOIN
                (
                    SELECT id
                    FROM authors
                    WHERE user_id = :user_id
                ) AS LOGIN_AUTHOR
                ON P.author_id = LOGIN_AUTHOR.id
            WHERE P.posted_date >= SUBDATE(current_date(), INTERVAL 365 DAY)
            ORDER BY FAV_COUNT.お気に入り数 DESC
            LIMIT 5";
			// TODO 本番用。上記は開発用で365日以内の詩の一覧を取得するようにしている。
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
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
                <td>
                <?php if ($row['author_id'] != $row['login_author_id']) { ?>
                    <a href="../author/detail.php?author_id=<?=e($row['author_id']) ?>"><?=e($row['penname']) ?></a>
                <?php } else { ?>
                    <?=e($row['penname']) ?>
                <?php } ?>
                </td>
                <td><?=e($row['body']) ?></td>
                <td><?=e($row['お気に入り数']) ?></td>
            </tr>
<?php
            $rank++;
        }
?>
        </tbody>
    </table>
</body>
</html>