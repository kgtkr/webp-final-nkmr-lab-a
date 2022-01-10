<?php
require_once('./lib/prelude.php');
require_once('./lib/image.php');

$login_user_id = login_user_id();
$db = connectDB();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>服-登録</title>
<link rel="stylesheet" href="./layout.css">
</head>
<body>
<?php echo_header(); ?>
<h1>新規登録</h1>
<?php if($login_user_id === null) { ?>
    <p>ログインしてください</p>
<?php } else { ?>
<form action="clothes.php" method="post" enctype="multipart/form-data">
<?php echo_csrf_token(); ?>
名称<br>
<input type="text" name="name"><br>
画像<br>
<input type="file" name="image" accept="image/*"><br>
タグ<br>
<?php
$stat=$db->prepare("select id,name,image_filename from tags WHERE user_id=:user_id AND deleted_at IS NULL");
$stat->bindValue(":user_id",$login_user_id,PDO::PARAM_STR);
$stat->execute();
$tags = $stat->fetchAll(PDO::FETCH_ASSOC);
foreach($tags as $tag){
    ?> <input type='checkbox' name='tags[]' value=<?php print h($tag["id"]) ?>> <?php
?>
<?php
print $tag["name"]."<br>";
}
?>
<input type="submit" name="submit" value="登録">
</form>
<?php } ?>
</body>
</html>
