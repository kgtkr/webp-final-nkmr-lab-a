<?php
require_once("./lib/prelude.php");
$login_user_id=login_user_id();
$db = connectDB();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>洗濯グループ分け</title>
</head>
<body>
<h1>洗濯グループ分け</h1>
<?php if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php } else{ ?>
<?php
$laundry_id=intval($_GET['laundry_id']);
$stat = $db->prepare("SELECT * FROM laundries WHERE id=:laundry_id AND user_id=:user_id");
$stat->bindValue(':laundry_id', $laundry_id, PDO::PARAM_INT);
$stat->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
$stat->execute();
$laundry = $stat->fetch();

$laundries_db=$db->prepare("SELECT * FROM laundry_clothes WHERE laundry_id=:laundry_id");
$laundries_db->bindValue(':laundry_id', $laundry['id'], PDO::PARAM_INT);
$laundries_db->execute();
$laundries=$laundries_db->fetchAll();
$clothes_ids=array_unique(array_column($laundries, 'clothes_id'));
$clothes_db=$db->prepare("SELECT * FROM clohtes WHERE id IN ".array_prepare_query('clothes_id', count($clothes_ids)));
array_prepare_bind($clothes_db, 'clothes_id', $clothes_ids, PDO::PARAM_INT);
$clothes_db->execute();
$clothes=$clothes_db->fetchAll();
$groups = [];
foreach($laundries as $laundry){
    $groups[$laundry['group_id']][] = $laundry['clothes_id'];
}
$clothes_id_to_clothes = [];
foreach($clothes as $clothe){
    $clothes_id_to_clothes[$clothe['id']] = $clothe;
}
foreach($groups as $group_id=>$clothe_ids){
    echo $group_id . "<br>";
    foreach($clothe_ids as $clothe_id){
        echo h($clothes_id_to_clothes[$clothe_id]['name']) . "<br>";
    }
}

?>
<?php } ?>
</body>
</html>
