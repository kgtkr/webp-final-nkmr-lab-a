<html>
<head><title>洗濯ページ</title></head>　
<meta charset="UTF-8">
<?php
function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
require_once("lib/db.php");
$db = connectDB();
$result_tag_id=$db->query("SELECT tag_id FROM clothes_tags WHERE clothes_id=1");
$i = 0;
for($i = 0; $row=$result_tag_id->fetch(); ++$i){
    echo $row['tag_id'];
    $result_=$db->query("SELECT tag_id FROM clothes_tags WHERE clothes_id=1");
}
?>
</html>