<!DOCTYPE html>
<html lang = “ja”>
	<head>
	<meta charset="utf-8">
	</head>
	<body bgcolor="#B2D4DC">
	<div class='logo-box'><h1><a href = "/board/mission_6-1.php"><img src = "/board/logo.jpg" width ="200px"></a></h1></div>
	<div align="right"><a href ="/board/logout.php">ログアウト</a></div>

<hr>	

<?php
//マイページ

//ログイン状態を確認。ログインされていなかったらログイン画面へ
	session_start();
	if(!isset($_SESSION["USERID"])){
		header("Location:/board/login.php");
		exit;
}
echo $_SESSION["USERID"] . "さん、こんにちは。";
?>

<br>
<a href ="/board/toukou.php">新規投稿</a><br>
<a href ="/board/delete.php">削除</a><br>
<a href ="/board/edit.php">編集</a><br>
<!それぞれの作業へのリンク!>
<br><br>
<hr>
<font size="4">過去の投稿</font><br>
<?php
//$dsnの式の中にスペースを入れないこと！
// 4-1データベースへの接続
	$dsn = 'データベース名';
	$user = 'ユーザー';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
//4-6入力したデータを表示
$user = $_SESSION["USERID"];
$sql = "SELECT * FROM retweet WHERE name='$user' ";
//whereでログインユーザー限定指定
$stmt = $pdo->query($sql);
	$count=$stmt->fetchColumn();
			if($count > 0){
	$results = $stmt->fetchAll();
	foreach ($results as $row){
//$rowの中にはテーブルのカラム名が入る
 $target = $row['photofilename'];
 //拡張子によって表示わけ
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