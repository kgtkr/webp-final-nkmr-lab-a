<html>
<head>
<meta charset="utf-8">
<title>服-一覧</title>
</head>
<body>
<h2>アイテム一覧</h2>
<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
require_once("lib/db.php");
$db = connectDB();
$result=$db->query("select name,image_sha256,created_at from clothes");//+where user_id=
foreach($result as $clothes){
    print h($clothes["name"]);
    print h($clothes["image_sha256"]);//画像表示に変えたい
    print h($clothes["created_at"]);
    print "
    <form action='edit_clothes.php' method='get'>
    <input type='hidden' name='clothes_id' value=".$clothes["id"].">
    <input type='submit' value='編集'>
    </form>
    ";
}
?>
</body>
</html>