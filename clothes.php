<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
require_once("lib/db.php");
require_once('./lib/image.php');

$login_user_id=$_SESSION['user_id']??null;
$db = connectDB();
$login_user_id="user";

if(isset($_POST["name"])&&isset($_POST["image"])&&isset($_POST["tags"])&&is_array($_POST["tags"])){
    $name=$_POST["name"];
    $image = $_FILES['image'] ?? null;
    $tags=$_POST["tags"];
    if ($image !== null) {
        $image_filename = save($image);
    } else {
        $image_filename = null;
    }

    $register=$db->prepare("insert into clohtes (name,image_filename,user_id) values (:name,:image_filename,:user_id)");
    $register->bindValue(":name",$name,PDO::PARAM_STR);
    $register->bindValue(":image_filename",$image_filename,PDO::PARAM_STR);
    $register->bindValue(":user_id",$login_user_id,PDO::PARAM_STR);//PARAM_INTに変える！！！！！
    $register->execute();

    $clothes_desc=$db->query("select * from clohtes order by id desc");
    $clothes_id_desc=$clothes_desc->fetchAll(PDO::FETCH_ASSOC);
    $clothes_id_new=$clothes_id_desc[0]["id"];

    foreach($tags as $tag){
        $connect_clothes_tags=$db->prepare("insert into clothes_tags (tag_id,clothes_id) values (:tag_id,:clothes_id)");
        $connect_clothes_tags->bindValue(":tag_id",$tag,PDO::PARAM_INT);
        $connect_clothes_tags->bindValue(":clothes_id",$clothes_id_new,PDO::PARAM_INT);
        $connect_clothes_tags->execute();
    }
    
}
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>服-一覧</title>
</head>
<body>
<h1>アイテム一覧</h1>
<?php $login_user_id=user;//消す！！！！！ ?>
<?php if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php } else { ?>
<a href="register_clothes.php">新規登録</a><br>
<?php
$results=$db->prepare("select id,name,image_filename,created_at,deleted_at from clohtes where user_id=:login_user_id and deleted_at is null;");
$results->bindValue(":login_user_id",$login_user_id,PDO::PARAM_STR);///PARAM_INTに変える！！！！！
$results->execute();
foreach($results as $clothes){
    print h($clothes["name"])."<br>";
    if($clothes['image_filename']!==null){
        print "<img src='images/".h($clothes["image_filename"])."><br>";
    }
    print h($clothes["created_at"])."<br>";
?>
    <form action='edit_clothes.php' method='post'>
    <input type='hidden' name='clothes_id' value=<?php print h($clothes['id']) ?>>
    <input type='submit' value='編集'>
    </form>
<?php
}
?>
<?php } ?>
</body>
</html>