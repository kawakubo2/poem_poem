<?php
require_once '../DbManager.php';
require_once '../Encode.php';
require_once '../common/auth.php';

session_start();

if (!is_admin()) {
    die('権限がありません');
}

if (isset($_GET['page']) && ($_GET['page'] === 'list.php' || $_GET['page'] === 'index.php')) {
    try {
        $db = getDb();
        $sql = "SELECT id, username, name, email, role
            FROM users
            where id = :id";
        $stt = $db->prepare($sql);
        $stt->bindValue(':id', $_GET['id']);
        $stt->execute();
        $user = $stt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['role_update_id'] = $user['id'];
        $_SESSION['role_update_username'] = $user['username'];
        $_SESSION['role_update_name'] = $user['name'];
        $_SESSION['role_update_email'] = $user['email'];
        $_SESSION['role_update_role'] = $user['role'];
    } catch (PDOException $e) {
        die('エラーメッセージ: ' . $e->getMessage());
    }
    $roles = ['user', 'admin'];
}
?>

<!DOCTYPE>
<html>
<head>
<meta charset="UTF-8" />
<title>ロール変更 | Poem World</title>
</head>
<body>
	<h3>ロール変更</h3>
	<form method="POST" action="update_process.php">
		<table class="table table-striped">
			<tr><th>ユーザ名</th><td><?=$_SESSION['role_update_username'] ?></td></tr>
			<tr><th>本名</th><td><?=$_SESSION['role_update_name'] ?></td></tr>
			<tr><th>email</th><td><?=$_SESSION['role_update_email'] ?></td></tr>
			<tr>
				<th>ロール</th>
				<td>
					<select name="role">
				<?php
				foreach ($roles as $role) {
				    $prop = $role === $_SESSION['role_update_role'] ? 'selected': '';
				    print("<option value='{$role}' {$prop} >{$role}</option>");
				}
				?>
					</select>
				</td>
			</tr>
		</table>
		<input type="submit" value="更新" />
	</form>
</body>
</html>