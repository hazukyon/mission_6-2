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
//ログイン

//$dsnの式の中にスペースを入れないこと！
// 4-1データベースへの接続
	$dsn = 'データベース名';
	$user = 'ユーザー';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
//4-2テーブル作成
	$sql = "CREATE TABLE IF NOT EXISTS usercontrol
	 (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name char(32),
	pass char(32) 
	)";
	//カラム設定、ユーザー管理用のテーブル
		$stmt = $pdo->query($sql);

//セッション開始、すでにログイン済みならマイページに飛んでくれる
session_start() ;
if(isset($_SESSION["USERID"])){
header('Location: /board/mypage.php');
exit;
} 

if(!isset($_POST["login"])){
$msg ="ログインしましょう。新規登録は画面右上へ。" . "<br>";
}

//ログイン操作
if(isset($_POST["login"])){
	if(empty($_POST["name"])){
		$msg= "IDが未入力です。";
	}elseif(empty($_POST["pass"])){
		$msg = "パスワードが未入力です。";
	}
if(!empty($_POST["name"]) && !empty($_POST["pass"])){
	$name = $_POST["name"];
	$logpass = $_POST["pass"];
	$sql = "SELECT pass FROM usercontrol WHERE name = '$name' ";
	//ユーザーIDのパスワードを検索
	$stmt = $pdo->query($sql);
	$count=$stmt->fetchColumn();
		if($count > 0){//ユーザーの登録が確認できるとき
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		$pass = $row['pass'];//ユーザーIDのパスワード取得
	}
	
		if($logpass == $pass){//送信されたパスワードとユーザーIDのパスワードが一致した時
		$_SESSION['USERID'] = $_POST["name"];//ログインの保持
		header('Location: /board/mypage.php');//マイページにジャンプ
		exit();
		}else{//IDはあっているがパスワードが違うとき
		$msg= "ユーザーIDあるいはパスワードに誤りがあります。";
		}
	
}else{//IDが存在しないとき＝ユーザーの登録がそもそもないとき
	$msg= "ユーザーIDあるいはパスワードに誤りがあります。";
}
}
}
//メッセージ表示
if(isset($msg)){
echo $msg;
}
?>

	<form action = "login.php" method = "post">
		ID:<br />
	<input type="text" name="name" size="30" value="" ><br />
	パスワード:<br />
	<input type="password" name="pass" size="30" value=""><br />
	<input type="submit" name="login" value="送信"><br />
	</form>
	</body>
</html>