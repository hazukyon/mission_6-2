<!DOCTYPE html>
<html lang = “ja”>
	<head>
	<meta charset="utf-8">
	</head>
	<body bgcolor="#B2D4DC">
<div class='logo-box'><h1><a href = "/board/mission_6-1.php"><img src = "/board/logo.jpg" width ="200px"></a></h1></div>
	<div align="right"><a href ="/board/newuser.php">新規登録</a></div>
	<hr>	
	
<?php
//ログアウト

//$dsnの式の中にスペースを入れないこと！
// 4-1データベースへの接続
	$dsn = 'データベース名';
	$user = 'ユーザー';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
//ログアウト処理
session_start();
// セッションクリア＝$_SESSION["USERID"]の設定を解除
session_destroy();
$error = "ログアウトしました。";
?>
<div> <?php echo $error; ?> </div>
<ul>
<a href="/board/login.php">ログインページへ</a>
	</form>
	</body>
</html>