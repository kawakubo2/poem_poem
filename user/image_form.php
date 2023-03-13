<?php
require_once '../DbManager.php' ;
require_once '../Encode.php';
session_start();

if ($_GET['id']  != $_SESSION['user']['id'] && $_SESSION['upload_user_id']  !=$_SESSION['user']['id'])  {
    die('権限がありません。');
}
if (isset($_GET['id']))  {
$_SESSION['upload_user_id'] =$_GET['id'];
}
if(isset($_GET['page'])){
    $_SESSION['page'] = $_GET['page'];
 }
 
try {
    $db = getDb();
    $sql = "SELECT profile_filepath
               FROM users
               WHERE id = :id";
    $stt = $db->prepare($sql) ;
    $stt->bindValue(':id' , $_SESSION['upload_user_id']) ;
    $stt->execute() ;
    if ($row = $stt->fetch(PDO::FETCH_ASSOC)) {
        $filepath = $row['profile_filepath'] ;
    }  else {
      $filepath = NULL;
	  //ファイルパスの取得失敗時
	  die('ファイルパスの取得に失敗しました');
	}
    

} catch(PDOException $e) {
            die('エラーメッセージ：' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>ユーザー画像	| poem_world</title>
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"/>
	<link type="text/css"rel="stylesheet"href="/css/main.css" />

</head>
<body>

	<a href="../index.php">トップページへ戻る</a>
	<h3>ユーザー画像</h3>
	<p id="message">
	<?php
	if(isset($_SESSION['upload_message'])){
	    print($_SESSION['upload_message']);
	    unset($_SESSION['update_message']);
	}
	?>
	</p>
	<ul id="error_summary">
	<?php
	if (isset($_SESSION['upload_errors'])){
	    foreach($_SESSION['upload_errors'] as $error) { ?>
	    <li><?=$error ?></li>
	    <?php
	    }
	    unset($_SESSION['upload_errors'] ) ;
	}?>
	</ul>
</header>
<main>
	<form method="POST" action="image_process.php" enctype="multipart/form-data">
	<input type="hidden" name="id" value="</?=e($_SESSION['upload_user_id'])?>"/>
	<input type="hidden" name="max_file_size" value="1000000"/>
	<input type="hidden" name="update_flag" value="<?=$filepath ? 1: 0 ?>"/>
	<input type="hidden" name="process_name" value='<?=$filepath ?"更新":"登録"?>'/>
	<input type="file" name="upfile" size="50"/>
	<input type="submit"  value="<?=$filepath ? "更新":"登録"  ?>" />
	</form>

<?php  if ($filepath) {?>
	<img src="/images/<?=(mb_convert_encoding ($filepath,'SJIS-WIN','UTF-8')) ?>" />
	<?php } ?>
	</main>
</body>
</html>