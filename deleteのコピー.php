<!DOCTYPE html>
<html lang = “ja”>
	<head>
	<meta charset="utf-8">
	</head>
	<body bgcolor="#B2D4DC">
<div class='logo-box'><h1><a href = "/board/mission_6-1.php"><img src = "/board/logo.jpg" width ="200px"></a></h1></div>
	<div align="right"><a href = "/board/mypage.php">マイページ</a></div>
<hr>	

<?php
//投稿を削除する

//ログイン確認
	session_start();
	if(!isset($_SESSION["USERID"])){
		header("Location:/board/login.php");
		exit;
	}
//$dsnの式の中にスペースを入れないこと！
// 4-1データベースへの接続
	$dsn = 'データベース名';
	$user = 'ユーザー';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//削除の実行
	if(!empty($_POST["delete"])){//削除番号の送信
	$delete = $_POST["delete"];
	$user = $_SESSION["USERID"];
	//投稿番号のチェック。存在していれば$countは1になる。
		$sql = "SELECT * FROM retweet WHERE id = '$delete' and name='$user' ";
		$stmt = $pdo->query($sql);
		$count=$stmt->fetchColumn();
			if($count > 0){
     //メディアテーブル内の削除
     $sql = "DELETE FROM mediatest WHERE 
     fname IN (
     SELECT photofilename FROM retweet WHERE id='$delete') "; 
     	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	
	//投稿テーブル内の削除
	$sql = "delete from retweet where id='$delete' and name='$user' ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$msg= "削除しました。";
	}else{//$count=0の時
	$msg ="投稿がありません。";
	}
		}else{
			$msg= "下部コメント一覧から番号を選択してください。" . "<br>" .
			"他のユーザーの投稿は削除できません。";
		}
		
if(isset($msg)){echo $msg;
}
	?>
	
<form action = "/board/delete.php" method = "post">
<br>
【　削除フォーム　】<br />
	削除番号入力:<br />
	<input type="number" name="delete" size="30" value="" /><br />
	<input type="submit" value="削除"><br />
	<br />
		</form>
<a href ="/board/toukou.php">新規投稿</a><br>
<a href ="/board/delete.php">削除</a><br>
<a href ="/board/edit.php">編集</a><br>
<br><br>
<hr>
	【　コメント一覧　】<br />
<?php
//$dsnの式の中にスペースを入れないこと！
// 4-1データベースへの接続
	$dsn = 'mysql:dbname=tb210320db;host=localhost';
	$user = 'tb-210320';
	$password = 'epFb9nhTnk';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
//4-6入力したデータを表示
$user = $_SESSION["USERID"];//ユーザーの投稿のみ表示
$sql = "SELECT * FROM retweet WHERE name='$user' ";
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
//$rowの中にはテーブルのカラム名が入る
 $target = $row['photofilename'];
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
?>

	</body>
</html>	