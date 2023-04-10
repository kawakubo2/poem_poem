<?php
require_once '../Encode.php';
require_once '../DbManager.php';
require_once '../common/auth.php';

session_start();

try {
    $db = getDb();
    $sql = "SELECT F.id AS favorite_id, P.id, A.penname, P.title, P.body
            FROM poems AS P
                INNER JOIN authors AS A
                    ON P.author_id = A.id
                INNER JOIN favorites AS F
                    ON P.id = F.poem_id
            WHERE F.user_id = :user_id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':user_id', $_SESSION['user']['id']);
    $stt->execute();
} catch (PDOException $e) {
    die('エラーメッセージ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<title>お気に入り一覧 | Poem World</title>
</head>
<body>
	<header>
        <a href="../index.php"><img src="/images/poem_world.png" /></a>
    	<h3>お気に入り一覧</h3>
    </header>
    <main>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>詩のID</th><th>ペンネーム</th><th>タイトル</th><th>内容</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $stt->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td><?=e($row['id']) ?></td>
                    <td><?=e($row['penname']) ?></td>
                    <td><?=e($row['title']) ?></td>
                    <td><?=e($row['body']) ?></td>
                    <td>
                        <form method="post" action="delete_process.php">
                            <input type="hidden" name="id" value="<?=e($row['favorite_id']) ?>" >
                            <input type="submit" value="お気に入りから外す" /> 
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </main>
</body>
</html>