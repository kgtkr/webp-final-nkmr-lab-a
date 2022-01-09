<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
require_once("lib/db.php");
require_once('./lib/image.php');
$login_user_id=$_SESSION["user_id"]??null;
$db = connectDB();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<body>
<h1>削除されました</h1>
<?php $login_user_id=user;//消す！！！！！ ?>
<?php if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php } else{ ?>
<?php
if(isset($_POST["clothes_id"])){
    $clothes_id=$_POST["clothes_id"];
    $delete_clothes=$db->prepare("update clohtes set deleted_at=CURRENT_TIMESTAMP where id=:clothes_id");
    $delete_clothes->bindValue(":clothes_id",$clothes_id,PDO::PARAM_INT);
    $delete_clothes->execute();
}
?>
<a href="clothes.php">戻る</a>
<?php } ?>
</body>
</html>