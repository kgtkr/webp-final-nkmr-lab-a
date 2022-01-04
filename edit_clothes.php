<html>
<head>
<meta charset="utf-8">
<title>服-一覧</title>
</head>
<body>
<h2>アイテム編集</h2>
<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
require_once("lib/db.php");
$clothes_id=$_GET["clothes_id"];
$db = connectDB();
$result=$db->query("select name,image_sha256 from clothes where id=".$clothes_id);//created_atはいるのかな
$result2=$db->query("select tag_id from clothes_tags where clothes_id=".$clothes_id);//一回clothes_tagsテーブルを挟むでいいのかな
foreach($result as $detail){
    print h($detail["name"]);
    print h($detail["image_sha256"]);//画像にしたい
    foreach($result2 as $tags_id){
        $result3=$db->query("select name,image_sha256 from tags where id=".$tags_id["tags_id"]);
    }
}
?>
</body>
</html>