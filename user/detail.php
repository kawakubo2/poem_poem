<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

authenticate();

session_start();

if (isset($_GET['id'])) {
    $_SESSION['insert_poem_id'] = $_GET['id'];
}

try {
    $db = getDb();
    $sql = "SELECT P.id, P.title, P.body,
            A.penname, A.profile_filepath
            FROM poems AS P
                INNER JOIN authors as A
                ON P.author_id = A.id
            WHERE P.id = :id";
    $stt = $db->prepare($sql);
    $stt->bindValue(':id', $_SESSION['insert_poem_id']);
    $stt->execute();
    $row = $stt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('エラーメッセージ: '. $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>詩の詳細 | Poem World</title>
<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="../css/main.css" />
</head>
<body>
	<a href="../index.php"><img src="/images/poem_world.png" /></a>
	<ul id="error_summary"></ul>
    <div>
        <h2>友達作家一覧</h2>
        <table class="tabel">
            <thead>
                <tr>
                    <th>ペンネーム</th>
                    <th>画像</th>
                </tr>
            </thead>
            <tbody>
        <?php
            try {
                $db = getDb();
                $sql = "SELECT id, penname, profile_filepath
                        FROM authors
                        WHERE id IN
                            (
                                SELECT author_id
                                FROM friends
                                WHERE 
                                    user_id = :user_id
                                    AND
                                    status = '承認'
                            )";
                $stt = $db->prepare($sql);
                $stt->bindValue(':user_id', $_SESSION['user']['id']);
                $stt->execute();
            } catch(PDOException $e) {
                die("エラーメッセージ: {$e->getMessage()}");
            }
            while ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <tr>
                <td><a href="../author/detail.php?author_id=<?=e($row['id']) ?>" ><?=e($row['penname']) ?></a></td>
                <td><img src="../images/<?=e($row['profile_filepath']) ?>" width="50" /></td>
            </tr>
        <?php
            }
        ?>
            </tbody>
        </table>
    </div>
</body>
</html>