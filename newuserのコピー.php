<!DOCTYPE html>
<html lang = “ja”>
	<head>
	<meta charset="utf-8">
	</head>
	<body bgcolor="#B2D4DC">
<div class='logo-box'><h1><a href = "/board/mission_6-1.php"><img src = "/board/logo.jpg" width ="200px"></a></h1></div>
<hr>	

<?php
//新規ユーザー登録をする

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
	name char(32) UNIQUE KEY NOT NULL,
	pass char(32) NOT NULL
	)";
	//カラム設定、ユーザー管理用のテーブル
		$stmt = $pdo->query($sql);
		
//セッション開始
session_start();
//既にログインしている場合はマイページへ
if(isset($_SESSION['USERID'])){
header('Location: /board/mypage/php');
exit;
}

//ユーザー登録
if(isset($_POST["put"])){
	if(empty($_POST["name"])){
		echo "IDが未入力です。";
		$repass = $_POST["pass"];
		}elseif(empty($_POST["pass"])){
		$msg= "パスワードが未入力です。";
		$rename = $_POST["name"];
	}
	if(!empty($_POST["name"]) && !empty($_POST["pass"])){
		$name = $_POST["name"];
		$pass = $_POST["pass"];
//重複IDのチェック。同じIDで登録されているものがあれば$countは1になる。
		$sql = "SELECT * FROM usercontrol WHERE name = '$name' ";
		$stmt = $pdo->query($sql);
		$count=$stmt->fetchColumn();
			if($count > 0){
				$msg ="そのIDは既に使われています。" . "<br/>";   
			}else{//$count=0の時
	//4-5insertを用いてデータを入力
	$sql = $pdo -> prepare("INSERT INTO usercontrol (name, pass) VALUES ('$name' , '$pass')");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	$sql -> execute();
	$_SESSION['USERID'] = $_POST["name"];//ログイン保持のために必要な作業
	echo '<script>
	alert("登録が完了しました。");
	location.href = "/board/mypage.php"
	</script>';
	exit();
	}
}
}

if(!isset($_POST["put"])){
	$msg ="ユーザー登録しましょう" ;
}
if(isset($msg)){echo $msg;
}
	?>

	
<form action = "newuser.php" method = "post">
	ID:<br />
	<input type="text" name="name" size="30" value=<?php if(isset($rename)){echo $rename;}?> ><br />
	パスワード:<br />
	<input type="password" name="pass" size="30" value=<?php if(isset($repass)){echo $repass;}?> ><br />
	<input type="submit" name="put" value="登録"><br />
		
	</form>
	</body>
</html>