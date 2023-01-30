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
    $sql = "SELECT id, username, name, email, role
            FROM users
            WHERE active = 1";
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
  <p id="success_message">
  <?php
  if (isset($_SESSION['update_success_message'])) {
      print($_SESSION['update_success_message']);
      unset($_SESSION['update_success_message']);
  }
  if (isset($_SESSION['delete_success_message'])) {
      print($_SESSION['delete_success_message']);
      unset($_SESSION['delete_success_message']);
  }
  ?>
  </p>
  <a class="btn btn-primary" href="insert_form.php">新規登録</a>
  <table class="table">
  <tr>
       <th>ID</th><th>ユーザ名</th><th>名前<th>Email</th><th>ロール</th><th colspan="2"></th>
       </tr>
       <?php
       while ($row =$stt->fetch(PDO::FETCH_ASSOC)){
       ?>
           <tr>
               <td><?=e($row['id']) ?></td>
               <td><?=e($row['username']) ?></td>
               <td><?=e($row['name']) ?></td>
               <td><?=e($row['email']) ?></td>
               <td><?=e($row['role']) ?></td>
               <td>
               <?php if ($row['id'] === $_SESSION['user']['id']) { ?>
               		<a href="update_form.php?id=<?=$row['id'] ?>&page=list.php">編集</a>
               <?php }?>
               </td>
               <td><a href="/poem_poem/role/update_form.php?id=<?=$row['id'] ?>&page=list.php">ロール変更</a></td>
               <td><a href="delete_form.php?id=<?=$row['id'] ?>&page=list.php">削除</a></td>
           </tr>
       <?php
       }
       ?>
	</table>
</body>
</html>
