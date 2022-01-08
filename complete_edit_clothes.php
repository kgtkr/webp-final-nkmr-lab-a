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
<title>服-変更完了</title>
</head>
<body>
<h1>変更完了しました</h1>
<?php $login_user_id=user;//消す！！！！！ ?>
<?php if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php } else{ ?>
<?php
if(isset($_GET["clothes_id"])&&isset($_GET["image_ori"])&&isset($_GET["name"])&&isset($_GET["tags"])&&is_array($_GET["tags"])){
    $clothes_id=$_GET["clothes_id"];
    if(isset($_GET["image"])){
        $image=$_FILES['image'] ?? null;
        if($image !==null){
            $image_filename=image/save($image);
        } else {
            $image_filename=null;
        }
    } else {
        $image_filename=$_GET["image_ori"];
    }
    $name=$_GET["name"];
    $tags=$_GET["tags"];

    $change_clothes=$db->prepare("update clohtes set name=:name,image_filename=:image_filename where id=:clothes_id");
    $change_clothes->bindValue(":name",$name,PDO::PARAM_STR);//PARAM_INT…int型データ、PARAM_STR…string型データ
    $change_clothes->bindValue(":image_filename",$image_filename,PDO::PARAM_STR);
    $change_clothes->bindValue(":clothes_id",$clothes_id,PDO::PARAM_INT);
    $change_clothes->execute();

    //増やしたタグと減らしたタグを反映させたい
    // $present_clothes_tags=$db->query("create table #present_clothes_tags(tag_id integer not null,clothes_id integer not null,PRIMARY KEY (tag_id, clothes_id))");
    
    // $BinA=$db->prepare("select * from clothes_tags where clothes_id=:clothes_id intersect select *");
    // $change_clothes_tags=$db->prepare();
}
?>
<a href="clothes.php">一覧に戻る</a>
<?php } ?>
</body>
</html>