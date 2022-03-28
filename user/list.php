<?php
require_once'../DbManager.php';
require_once'../Encode.php';
require_once'../common/auth.php';

session_start();

if (!is_admin()){
    die('権限がありません。');
}

try {
    $db = getDb();
    $sql ="SELECT id, name,email,role
       FROM users";
    $stt = $db->prepare($sql);
    $stt->execute();

} catch(PDOException $e){
    die('エラーメッセージ:'.$e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>ユーザー一覧｜Poem World</title>
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="css/main.css" />
</head>
<body>
  <h3>ユーザー一覧</h3>
  <table class="table">
  <tr>
       <th>ID</th><th>名前<th>Email</th><th>ロール</th><th colspan="2"></th>
       </tr>
       <?php
       while ($row =$stt->fetch(PDO::FETCH_ASSOC)){
       ?>
           <tr>
               <td><?=e($row['id'])?></td>
               <td><?=e($row['name'])?></td>
               <td><?=e($row['email'])?></td>
               <td><?=e($row['role'])?></td>
               <td><a href="update_form.php?id=<?=$row['id'] ?>">編集</a></td>
               <td><a href="delete_form.php?id=<?=$row['id'] ?>">削除</a></td>
           </tr>
       <?php
       }
       ?>
	</table>
</body>
</html>
