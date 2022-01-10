<?php
require_once("./lib/prelude.php");
require_once('./lib/image.php');
$login_user_id=login_user_id();
$db = connectDB();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>服-変更完了</title>
</head>
<body>
<?php echo_header(); ?>
<h1>変更完了しました</h1>
<?php if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php } else{ ?>
<?php
if(isset($_POST["clothes_id"])&&isset($_POST["name"]) && verify_csrf_token()){
    $clothes_id=$_POST["clothes_id"];
    if(isset($_FILES["image"])){
        $image=$_FILES['image'] ?? null;
        if($image !==null){
            $image_filename=image\save($image);
        } else {
            $image_filename=null;
        }
    } else {
        if (image\exist($_POST["image_ori"])){
            $image_filename=$_POST["image_ori"];
        } else {
            $image_filename=null;
        }
    }
    $name=$_POST["name"];
    $tags=$_POST["tags"] ?? [];

    $change_clothes=$db->prepare("update clohtes set name=:name,image_filename=:image_filename where id=:clothes_id AND user_id=:user_id");
    $change_clothes->bindValue(":name",$name,PDO::PARAM_STR);//PARAM_INT…int型データ、PARAM_STR…string型データ
    $change_clothes->bindValue(":image_filename",$image_filename,PDO::PARAM_STR);
    $change_clothes->bindValue(":clothes_id",$clothes_id,PDO::PARAM_INT);
    $change_clothes->bindValue(":user_id",$login_user_id,PDO::PARAM_STR);
    $change_clothes->execute();

    if ($change_clothes->rowCount()>0) {
        //増やしたタグと減らしたタグを反映させたい
        $delete_clothes_tags=$db->prepare("delete from clothes_tags where clothes_id=:clothes_id");
        $delete_clothes_tags->bindValue(":clothes_id",$clothes_id,PDO::PARAM_STR);
        $delete_clothes_tags->execute();

        $stmt = $db->prepare('SELECT * FROM tags WHERE user_id=:user_id AND deleted_at IS NULL AND id IN ' . array_prepare_query('id', count($tags)));
        array_prepare_bind($stmt, 'id', $tags, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
        $stmt->execute();
        $verifyTags = $stmt->fetchAll();
        $verifyTagIds = array_column($verifyTags, 'id');

        foreach($verifyTagIds as $tag){
            $add_clothes_tags=$db->prepare("insert into clothes_tags (tag_id,clothes_id) values (:tag_id,:clothes_id)");
            $add_clothes_tags->bindValue(":tag_id",$tag,PDO::PARAM_INT);
            $add_clothes_tags->bindValue(":clothes_id",$clothes_id,PDO::PARAM_INT);
            $add_clothes_tags->execute();
        }
    }
}
?>
<a href="clothes.php">一覧に戻る</a>
<?php } ?>
</body>
</html>
