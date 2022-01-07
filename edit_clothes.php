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
//print $clothes_id;
$db = connectDB();
$results=$db->query("select name,image_filename from clohtes where id=".$clothes_id);//created_atはいるのかな
$results2=$db->query("select tag_id from clothes_tags where clothes_id=".$clothes_id);//一回clothes_tagsテーブルを挟むでいいのかな
foreach($results as $detail){
    print h($detail["name"])."<br>";
    print h($detail["image_filename"]);//画像にしたい
    foreach($results2 as $tags_id){
        //print $tags_id["tag_id"];
        $results3=$db->query("select name,image_filename from tags where id=".$tags_id['tag_id']);
        foreach($results3 as $tags){
            print "タグ<br>";
            print h($tags["name"])."<br>";
            print h($tags["image_filename"])."<br>";
        }
    }
}
?>
</body>
</html>