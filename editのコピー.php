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
//投稿を編集する

	session_start();
	if(!isset($_SESSION["USERID"])){
		header("Location:/board/login.php");
		exit;
}
error_reporting(E_ALL);//エラー発見
ini_set('display_errors', '1');

//$dsnの式の中にスペースを入れないこと！
// 4-1データベースへの接続
	$dsn = データベース;
	$user = ユーザー;
	$password = パス;
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
//編集フォーム、投稿フォームに表示するまで
$user = $_SESSION["USERID"];
if(!empty($_POST["edit"])){//編集番号の送信
//4-7updateで編集
$edit = $_POST["edit"];
$sql = "SELECT * FROM retweet WHERE id='$edit' and name='$user' ";//変更する投稿番号を選択
		$stmt = $pdo->query($sql);
		$count=$stmt->fetchColumn();
			if($count > 0){
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		$newcomment =  $row['comment']; //変更したい画像、変更したいコメント
		$file = $row['photofilename'];
			}
			$msg = "コメント、メディアを編集してください";
			}else{//$count=0のとき
				$msg="投稿がありません。";
			}
	}else{//ポスト送信されていないとき
		$msg= "下部履歴から番号を入力してください";
}


//編集作業、画像の編集→投稿の編集の流れ
//ファイルアップロードがあったとき
        if (!empty($_POST["editver"]) && !empty($_POST["put"]) && isset($_FILES['pic']['error']) && is_int($_FILES['pic']['error']) && $_FILES["pic"]["name"] !== ""){
            //エラーチェック
            switch ($_FILES['pic']['error']) {
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
            $raw_data = file_get_contents($_FILES['pic']['tmp_name']);

            //拡張子を見る
            $tmp = pathinfo($_FILES["pic"]["name"]);
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
                echo "非対応ファイルです．<br/>";
                echo ("<a href=\"/board/edit.php\">戻る</a><br/>");
                exit(1);
            }

            //DBに格納するファイルネーム設定
            //サーバー側の一時的なファイルネームと取得時刻を結合した文字列にsha256をかける．
            $date = getdate();
            $fname = $_FILES["pic"]["tmp_name"].$date["year"].$date["mon"].$date["mday"].$date["hours"].$date["minutes"].$date["seconds"];
            $fname = hash("sha256", $fname);

            //画像・動画を画像保存用テーブルの編集。 
  $id = $_POST["editver"];    
     $sql ="UPDATE mediatest SET fname=:fname, extension=:extension, raw_data=:raw_data WHERE 
     fname IN(SELECT photofilename FROM retweet WHERE id=:id) "; 
     $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
	$stmt->bindParam(':extension', $extension, PDO::PARAM_STR);
	$stmt->bindParam(':raw_data', $raw_data, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
        
//投稿テーブルの編集
$id = $_POST["editver"]; //変更する投稿番号
$name = $_SESSION["USERID"];
	$comment = $_POST["comment"]; //変更したい画像、変更したいコメントは自分で決めること
	date_default_timezone_set("Asia/Tokyo");
	$nowtime = date("Y/m/d H:i:s");
	$sql = "UPDATE retweet SET name='$name',comment='$comment',  photofilename='$fname', extension='$extension', nowtime='$nowtime' WHERE id='$id' ";//編集実行
	
	//エラー箇所
$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	$stmt->bindParam(':photofilename', $fname, PDO::PARAM_STR);
	$stmt->bindParam(':extension', $extension, PDO::PARAM_STR);
	$stmt->bindParam(':nowtime', $nowtime, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();

		$msg= "編集しました";
}

if(isset($msg)){
echo $msg;
}
	?>
<br>
<form action = "/board/edit.php" enctype = "multipart/form-data" method = "post">
	【　編集フォーム　】<br />
	編集番号入力:<br />
	<input type="number" name="edit" size="30" value="" /><br />
	<input type="submit" value="番号送信"><br />
【　投稿フォーム　】<br /> 
	コメント:<br /> 
	<input type="text" name="comment" size="32" value=<?php if(isset($newcomment)){ echo "$newcomment" ;} ?> ><br />
	メディア:<br />
	<input type="file" name="pic" size="30" value="" ><br />
	<input type="submit" name="put" value="編集"><br />
	<br />

	<!-編集対象番号・ファイル名表示。のちにhidden->
	<input type="hidden" name="editver" value=<?php if(isset($edit)){echo "$edit";} ?> ><br />
	<br>
<a href ="/board/toukou.php">新規投稿</a><br>
<a href ="/board/delete.php">削除</a><br>
<a href ="/board/edit.php">編集</a><br>
<br><br>
<hr>
	【　コメント一覧　】<br />
<?php
//4-6入力したデータを表示
$user= $_SESSION["USERID"];
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