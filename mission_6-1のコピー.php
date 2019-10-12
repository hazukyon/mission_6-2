<!DOCTYPE html>
<html lang = “ja”>
	<head>
	<meta charset="utf-8">
	<title>画像送信機能付き掲示板</title>
	</head>
	<body bgcolor="#B2D4DC">
	<header>
	<div class='logo-box'><h1><a href = "/board/mission_6-1.php"><img src = "/board/logo.jpg" width ="200px"></a></h1></div>
	<?php 
	session_start();
	if(isset($_SESSION['USERID'])){
	echo '<div class="mypage" align="right"><a href = "/board/mypage.php">マイページ</a></div>';
	}else{
	echo 	'<div class="login" align="right"><a href = "/board/login.php">新規登録・ログイン</a></div>';
}
?>
  </header>
	<hr>	
	
<?php
//トップページ、登録なしでも閲覧可能

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

$sql = "CREATE TABLE IF NOT EXISTS retweet
	 (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name char(32),
	comment char(32),
	photofilename TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	extension TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
	nowtime char(32) 
	)";
	//カラム設定、投稿用のテーブル
		$stmt = $pdo->query($sql);
	?>

	<?php
//4-6入力したデータを表示
$sql = 'SELECT * FROM retweet';
	$stmt = $pdo->query($sql);
	$count=$stmt->fetchColumn();
			if($count > 0){
	$results = $stmt->fetchAll();
	foreach ($results as $row){
//$rowの中にはテーブルのカラム名が入る
  $target = $row['photofilename'];
  //拡張子で場合分け
         if($row["extension"] == "mp4"){
            echo ("<video src=\"mediatest_import.php?target=$target\" width=\"31%\" height=\"auto\" controls></video>");
        }
        elseif($row["extension"] == "jpeg" || $row["extension"] == "png" || $row["extension"] == "gif"){
            echo ("<img src='mediatest_import.php?target=$target' width=\"31%\" height=\"auto\" controls></img>");
        }
		echo  "<br>" ;
		echo $row['id'] . ' ';
		echo $row['name'] . "<br>";
		echo $row['comment'] . "<br>";
		echo $row['nowtime'] . "<br>" . "<hr>";
	}
	}
	?>

	</body>
</html>	
