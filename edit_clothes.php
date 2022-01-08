<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
require_once("lib/db.php");
$login_user_id=$_SESSION["user_id"]??null;
$db = connectDB();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>服-一覧</title>
</head>
<body>
<h1>アイテム編集</h1>
<?php //if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php //} else{ ?>
<?php
$clothes_id=$_GET["clothes_id"];
$results=$db->prepare("select name,image_filename from clohtes where id=:id");//created_atはいるのかな
$results->bindValue(":id",$clothes_id,PDO::PARAM_INT);
$results->execute();
$results2=$db->prepare("select tag_id from clothes_tags where clothes_id=:clothes_id");//一回clothes_tagsテーブルを挟むでいいのかな
$results2->bindValue(":clothes_id",$clothes_id,PDO::PARAM_INT);
$results2->execute();

foreach($results as $detail){
    print h($detail["name"])."<br>";
    if($detail['image_filename']!==null){
        print "<img src='images/".h($detail["image_filename"]).">";
    }
    foreach($results2 as $tags_id){
        $results3=$db->prepare("select name,image_filename from tags where id=:tag_id");
        $results3->bindValue(":tag_id",$tags_id['tag_id'],PDO::PARAM_INT);
        $results3->execute();
        foreach($results3 as $tags){
            print "タグ<br>";
            print h($tags["name"])."<br>";
            if($tags['image_filename']!==null){
                print "<img src='images/".h($tags["image_filename"]).">";
            }
        }
    }
}
?>
<a href="clothes.php">戻る</a>
<?php //} ?>
</body>
</html>