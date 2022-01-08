<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
require_once("lib/db.php");

$login_user_id=$_SESSION['user_id']??null;
$db = connectDB();

if(isset($_GET["name"])&&isset($_GET["image"])&&isset($_GET["tag"])&&is_array($_GET["tag"])){
    $name=$_GET["name"];
    $image = $_FILES['image'] ?? null;
    $tag=$_GET["tag"];
    if ($image !== null) {
        $image_filename = image\save($image);
    } else {
        $image_filename = null;
    }

    $register=$db->prepare("insert into clohtes(name,image_filename,user_id) values(:name,:image_filename,:user_id)");
    $register->bindValue(":name",$name,PDO::PARAM_INT);
    $register->bindValue(":image_filename",$image_filename,PDO::PARAM_INT);
    $register->bindValue(":user_id",$login_user_id,PDO::PARAM_INT);
    $register->execute();
    //服とタグの関係をsqlで保存する
    $clothes_desc=$db->query("select * from clohtes order by id desc");
    $clothes_id_desc=$clothes_desc->fetchAll(PDO::FETCH_ASSOC);
    $clothes_id_new=$id_desc[0]["id"];
    for($i=0;$i<count($tag);$i++){
        $connect_clothes_tags=$db->prepare("insert into clothes_tags(tag_id,clothes_id) values(:tag_id,:clothes_id)");
        $connect_clothes_tags->bindValue(":tag_id",$tag[$i],PDO::PARAM_INT);
        $connect_clothes_tags->bindValue(":clothes_id",$clothes_id_new,PDO::PARAM_INT);
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
<?php //if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php //} else { ?>
<a href="register_clothes.php">新規登録</a><br>
<?php
$results=$db->query("select id,name,image_filename,created_at from clohtes;");//+where user_id=
foreach($results as $clothes){
    print h($clothes["name"])."<br>";
    if($clothes['image_filename']!==null){
        print "<img src='images/".h($clothes["image_filename"]).">";
    }
    print h($clothes["created_at"])."<br>";
    print "
    <form action='edit_clothes.php' method='get'>
    <input type='hidden' name='clothes_id' value=".$clothes['id'].">
    <input type='submit' value='編集'>
    </form>
    ";
}
?>
<?php //} ?>
</body>
</html>