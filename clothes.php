<html>
<head>
<meta charset="utf-8">
<title>服-一覧</title>
</head>
<body>
<h2>アイテム一覧</h2>
<a href="register_clothes.php">新規登録</a><br>
<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
require_once("lib/db.php");
$db = connectDB();
//$db=PDO("sqlite:db/db.sqlite");
$results=$db->query("select id,name,image_filename,created_at from clohtes;");//+where user_id=
foreach($results as $clothes){
    print h($clothes["name"])."<br>";
    print h($clothes["image_filename"])."<br>";//画像表示に変えたい
    print h($clothes["created_at"])."<br>";
    print "
    <form action='edit_clothes.php' method='get'>
    <input type='hidden' name='clothes_id' value=".$clothes['id'].">
    <input type='submit' value='編集'>
    </form>
    ";
}
?>
</body>
</html>