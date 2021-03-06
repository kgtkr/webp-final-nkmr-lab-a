<?php
require_once("./lib/prelude.php");
require_once('./lib/image.php');

$login_user_id=login_user_id();
$db = connectDB();

if(isset($_POST["name"]) && verify_csrf_token()){
    $name=$_POST["name"];
    $image = $_FILES['image'] ?? null;
    $tags=$_POST["tags"] ?? [];
    if ($image !== null) {
        $image_filename = image\save($image);
    } else {
        $image_filename = null;
    }

    $register=$db->prepare("insert into clohtes (name,image_filename,user_id) values (:name,:image_filename,:user_id)");
    $register->bindValue(":name",$name,PDO::PARAM_STR);
    $register->bindValue(":image_filename",$image_filename,PDO::PARAM_STR);
    $register->bindValue(":user_id",$login_user_id,PDO::PARAM_STR);
    $register->execute();

    $clothes_id_new=intval($db->lastInsertId());

    $stmt = $db->prepare('SELECT * FROM tags WHERE user_id=:user_id AND deleted_at IS NULL AND id IN ' . array_prepare_query('id', $tags));
    array_prepare_bind($stmt, 'id', $tags, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
    $stmt->execute();
    $verifyTags = $stmt->fetchAll();
    $verifyTagIds = array_column($verifyTags, 'id');

    foreach($verifyTagIds as $tag){
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
<title>服一覧</title>
<link rel="stylesheet" href="./layout.css">
</head>
<body>
<?php echo_header(); ?>
<h1>服一覧</h1>
<?php if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php } else { ?>
<a href="register_clothes.php">新規登録</a><br>
<form action="laundry.php" method="post">
<table border="1">
<tr>
<th>洗濯</th>
<th>アイコン</th>
<th>名前</th>
<th>編集</th>
</tr>
<?php
$results=$db->prepare("select id,name,image_filename,created_at,deleted_at from clohtes where user_id=:login_user_id and deleted_at is null;");
$results->bindValue(":login_user_id",$login_user_id,PDO::PARAM_STR);
$results->execute();
foreach($results as $clothes){
    ?>
    <tr>
    <td>
        <input type="checkbox" name="clothes[]" value="<?php echo h($clothes['id']); ?>">
    </td>
    <td><?php
        if($clothes['image_filename']!==null){
            print "<img src='images/".h($clothes["image_filename"])."'>";
        }
        ?>
    </td>
    <td><?php echo h($clothes["name"]); ?></td>
    <td>
    <a href="edit_clothes.php?clothes_id=<?php print h($clothes["id"]) ?>">編集</a>
    </td>
    </tr>
    <?php } ?>
</table>
<input type="submit" value="洗濯">
<?php echo_csrf_token(); ?>
</form>
<?php } ?>
</body>
</html>
