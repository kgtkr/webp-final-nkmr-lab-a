<?php
require_once('./lib/db.php');
require_once('./lib/image.php');

$login_user_id = $_SESSION['user_id'] ?? null;
$db = connectDB();
$tags=$db->query("select name,image_filename from tags");
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>服-登録</title>
</head>
<body>
<h1>新規登録</h1>
<?php //if($login_user_id === null) { ?>
    <p>ログインしてください</p>
<?php //} else { ?>
<form action="clothes.php" method="get">
名称<br>
<input type="text" name="name"><br>
<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
画像<br>
<input type="file" name="image"><br>
タグ<br>
<?php
foreach($tags as $tag){
    if($tag['image_filename']!==null){
        print "<img src='images/".h($tag["image_filename"]).">";
    }
    print "<input type='checkbox' name='tag' value=".$tag["name"]."><br>";
}
?>
<input type="submit" name="submit" value="登録">
</form>
<?php //} ?>
</body>
</html>