<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
require_once('./lib/db.php');
require_once('./lib/image.php');

$login_user_id = $_SESSION['user_id'] ?? null;
$db = connectDB();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>服-登録</title>
</head>
<body>
<h1>新規登録</h1>
<?php $login_user_id=user;//消す！！！！！ ?>
<?php if($login_user_id === null) { ?>
    <p>ログインしてください</p>
<?php } else { ?>
<form action="clothes.php" method="post" enctype="multipart/form-data">
名称<br>
<input type="text" name="name"><br>
画像<br>
<input type="file" name="image" accept="image/*"><br>
タグ<br>
<?php
$tags=$db->query("select id,name,image_filename from tags");
foreach($tags as $tag){
    if($tag['image_filename']!==null){
        print "<img src='images/".h($tag["image_filename"]).">";
    }
?>
    <input type='checkbox' name='tags[]' value=<?php print h($tag["id"]) ?>>
<?php
print $tag["name"]."<br>";
}
?>
<input type="submit" name="submit" value="登録">
</form>
<?php } ?>
</body>
</html>