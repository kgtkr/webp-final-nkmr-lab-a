<?php
require_once("./lib/prelude.php");
require_once('./lib/image.php');
$login_user_id=login_user_id();
$db = connectDB();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<body>
<h1>削除されました</h1>
<?php if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php } else{ ?>
<?php
if(isset($_POST["clothes_id"]) && verify_csrf_token()){
    $clothes_id=$_POST["clothes_id"];
    $delete_clothes=$db->prepare("update clohtes set deleted_at=CURRENT_TIMESTAMP where id=:clothes_id AND user_id=:user_id");
    $delete_clothes->bindValue(":clothes_id",$clothes_id,PDO::PARAM_INT);
    $delete_clothes->bindValue(":user_id",$login_user_id,PDO::PARAM_STR);
    $delete_clothes->execute();
}
?>
<a href="clothes.php">戻る</a>
<?php } ?>
</body>
</html>
