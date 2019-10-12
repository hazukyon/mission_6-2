<!DOCTYPE html>
<html lang = “ja”>
	<head>
	<meta charset="utf-8">
	</head>
	<body bgcolor="#B2D4DC">
<div class='logo-box'><h1><a href = "/board/mission_6-1.php"><img src = "/board/logo.jpg" width ="200px"></a></h1></div>
<hr>	

<?php
//新規投稿をする

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
	
//画像保存用テーブル
	$sql = "CREATE TABLE IF NOT EXISTS mediatest
	 (
	id INT AUTO_INCREMENT PRIMARY KEY,
	fname TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	extension TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	raw_data LONGBLOB
	)";
	//カラム設定
		$stmt = $pdo->query($sql);

//ファイルアップロード、コメント投稿があったとき
if (!empty($_POST["comment"]) && isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error']) && $_FILES["upfile"]["name"] !== ""){
        	
            //エラーチェック、RuntimeExceptionについてたまにエラーが出るので改善予定です。
            switch ($_FILES['upfile']['error']) {
                case UPLOAD_ERR_OK: // OK
                    break;
                case UPLOAD_ERR_NO_FILE:   // 未選択
                    throw new RuntimeException('ファイルが選択されていません', 400);
                case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
                    throw new RuntimeException('ファイルサイズが大きすぎます', 400);
                default:
                    throw new RuntimeException('その他のエラーが発生しました', 500);
            }

            //画像・動画をバイナリデータにする．
            $raw_data = file_get_contents($_FILES['upfile']['tmp_name']);

            //拡張子を見る
            $tmp = pathinfo($_FILES["upfile"]["name"]);
            $extension = $tmp["extension"];
            if($extension === "jpg" || $extension === "jpeg" || $extension === "JPG" || $extension === "JPEG"){
                $extension = "jpeg";
            }
            elseif($extension === "png" || $extension === "PNG"){
                $extension = "png";
            }
            elseif($extension === "gif" || $extension === "GIF"){
                $extension = "gif";
            }
            elseif($extension === "mp4" || $extension === "MP4"){
                $extension = "mp4";
            }
            else{
                echo "非対応ファイルです。<br/>";
                echo ("<a href=\"toukou.php\">戻る</a><br/>");
                exit(1);
            }

            //DBに格納するファイルネーム設定
            //サーバー側の一時的なファイルネームと取得時刻を結合した文字列にsha256をかける．
            $date = getdate();
            $fname = $_FILES["upfile"]["tmp_name"].$date["year"].$date["mon"].$date["mday"].$date["hours"].$date["minutes"].$date["seconds"];
            $fname = hash("sha256", $fname);

            //画像・動画を画像保存用テーブルに保存
            $sql = "INSERT INTO mediatest(fname, extension, raw_data) VALUES (:fname, :extension, :raw_data);";
            $stmt = $pdo->prepare($sql);
            $stmt -> bindValue(":fname",$fname, PDO::PARAM_STR);
            $stmt -> bindValue(":extension",$extension, PDO::PARAM_STR);
            $stmt -> bindValue(":raw_data",$raw_data, PDO::PARAM_STR);
            $stmt -> execute();
            
//投稿テーブルに保存        
	$name = $_SESSION["USERID"];
	$comment = $_POST["comment"]; 
	date_default_timezone_set("Asia/Tokyo");
	$nowtime = date("Y/m/d H:i:s");
//4-5insertを用いてデータを入力
	$sql = $pdo -> prepare("INSERT INTO retweet (name, comment,  nowtime,photofilename,extension) VALUES ('$name', '$comment', '$nowtime', '$fname' , '$extension')");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':nowtime', $nowtime, PDO::PARAM_STR);
	$sql -> bindParam(':photofilename', $fname, PDO::PARAM_STR);
	$sql -> bindParam(':extension', $extension, PDO::PARAM_STR);
	$sql -> execute();
	$msg= "投稿しました。";
}elseif(empty($_POST["comment"])){
    $msg ="コメントを入力してください。";
        }
        
	
if(empty($_POST["put"])){
	$msg = "画像・コメントを送信してください。<br>";
}
if(isset($msg)){echo $msg;
}
	?>

<br>
<br>
【　投稿フォーム　】<br />

	<form action = "toukou.php" enctype = "multipart/form-data" method = "post">
		メディア:<br />
	<input type="file" name="upfile">

	<br /><br />
	コメント:<br />
	<input type="text" name="comment" size="32" value=<?php if(isset($newcomment)){ echo "$newcomment" ;} ?> ><br />

	<input type="submit" name="put" value="送信"><br />
	<br />
<a href ="/board/toukou.php">新規投稿</a><br>
<a href ="/board/delete.php">削除</a><br>
<a href ="/board/edit.php">編集</a><br>
<br><br>
<hr>
<font size="4">過去の投稿</font><br>
<?php
// 4-1データベースへの接続
	$dsn = 'データベース名';
	$user = 'ユーザー';
	$password = 'パスワード';
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
	</form>
	</body>
</html>	