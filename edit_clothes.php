<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
require_once("lib/db.php");
require_once('./lib/image.php');
$login_user_id=$_SESSION["user_id"]??null;
$db = connectDB();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>服-編集</title>
</head>
<body>
<h1>アイテム編集</h1>
<?php $login_user_id=user;//消す！！！！！ ?>
<?php if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php } else{ ?>
<?php
$clothes_id=$_POST["clothes_id"];
$results=$db->prepare("select name,image_filename from clohtes where id=:id");
$results->bindValue(":id",$clothes_id,PDO::PARAM_INT);
$results->execute();
$results2=$db->prepare("select tag_id from clothes_tags where clothes_id=:clothes_id");
$results2->bindValue(":clothes_id",$clothes_id,PDO::PARAM_INT);
$results2->execute();

$results3=$db->prepare("select id from tags where id not in (select tag_id from clothes_tags where clothes_id=:clothes_id)");
$results3->bindValue(":clothes_id",$clothes_id,PDO::PARAM_INT);
$results3->execute();
?>
<form action="complete_edit_clothes.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="clothes_id" value="<?php print h($clothes_id) ?>">
<?php
foreach($results as $detail){
    if($detail['image_filename']!==null){
        print "<img src='images/".h($detail["image_filename"]).">";
    }
?>
<input type="file" name="image" accept="image/*">
<input type="hidden" name="image_ori" value="<?php print h($detail['image_filename']) ?>">
<table>
<tr>
    <td>名称</td>
    <td><input type="text" name="name" value="<?php print h($detail["name"]) ?>"></td>
</tr>
<tr>
    <td>タグ</td>
    <td>
<?php
    foreach($results2 as $tags_id){
        $results4=$db->prepare("select id,name,image_filename from tags where id=:tag_id");
        $results4->bindValue(":tag_id",$tags_id['tag_id'],PDO::PARAM_INT);
        $results4->execute();
        $tag_checked=$results4->fetchAll(PDO::FETCH_ASSOC);
?>
        <input type="checkbox" name="tags[]" value="<?php print h($tag_checked[0]['id']) ?>" checked><?php print h($tag_checked[0]['name']); ?>
<?php
        if($tags['image_filename']!==null){
                print "<img src='images/".h($tag_checked[0]['image_filename']).">";
        }
    }
    foreach ($results3 as $tags_id_2){
        $results5=$db->prepare("select id,name,image_filename from tags where id=:tag_id");
        $results5->bindValue(":tag_id",$tags_id_2['id'],PDO::PARAM_INT);
        $results5->execute();
        $tag_unchecked=$results5->fetchAll(PDO::FETCH_ASSOC);
?> 
        <input type="checkbox" name="tags[]" value="<?php print h($tag_unchecked[0]['id']) ?>"><?php print h($tag_unchecked[0]['name']); ?>
<?php
        if($not_tags['image_filename']!==null){
            print "<img src='images/".h($tag_unchecked[0]['image_filename']).">";
        }
    }
}
?>
    </td>
</table>
<input type="submit" name="submit" value="変更">
</form>
<form action="clothes_deleted.php" method="get">
<input type="hidden" name="clothes_id" value="<?php print h($clothes_id) ?>">
<input type="submit" name="submit" value="削除">
</form>
<a href="clothes.php">戻る</a>
<?php } ?>
</body>
</html>