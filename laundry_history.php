<?php
require_once("./lib/prelude.php");
$login_user_id=login_user_id();
$db = connectDB();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" href="./layout.css">
</head>
<body>
<?php echo_header(); ?>
<h1>洗濯履歴</h1>
<?php if($login_user_id===null){ ?>
    <p>ログインしてください</p>
<?php } else{ ?>
<?php
$user_laundries_db=$db->prepare("SELECT * FROM laundries WHERE user_id=:user_id");
$user_laundries_db->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
$user_laundries_db->execute();
$user_laundries_history=$user_laundries_db->fetchAll();
$user_laundries_history_days=array_unique(array_column($user_laundries_history, 'created_at'));
//userのlaundry_idを全て取得
$user_laundry_ids=array_unique(array_column($user_laundries_history, 'id'));
//その取得したidのグループ分け情報(laundry_clothes)も全て取得
$user_laundry_ids_db=$db->prepare("SELECT * FROM laundry_clothes WHERE laundry_id=:laundry_id");
$user_laundry_ids_db->bindValue(':laundry_id', $user_laundry_ids, PDO::PARAM_INT);
$user_laundry_ids_db->execute();
$user_laundry_groups=$user_laundry_ids_db->fetchAll();


$clothes_ids=array_unique(array_column($user_laundry_groups, 'clothes_id'));
$clothes_db=$db->prepare("SELECT * FROM clohtes WHERE id IN ".array_prepare_query('clothes_id', count($clothes_ids)));
array_prepare_bind($clothes_db, 'clothes_id', $clothes_ids, PDO::PARAM_INT);
$clothes_db->execute();
$clothes=$clothes_db->fetchAll();

$histories = [];

foreach($user_laundry_groups as $user_laundry_group){
    $histories[$user_laundry_group['laundry_id']][$user_laundry_group['group_id']][] = $user_laundry_group['clothes_id'];
}
$clothes_id_to_clothes = [];
foreach($clothes as $clothe){
    $clothes_id_to_clothes[$clothe['id']] = $clothe;
}
$laundry_id_to_laundry_day = [];
foreach($user_laundries_history as $user_laundry_day){
    $laundry_id_to_laundry_day[$user_laundry_day['id']]=$user_laundry_day;
}
echo '<ul>';
foreach($histories as $laundry_id=>$group){
    echo '<li><a href="./laundry.php?laundry_id='. $laundry_id .'">'.h(app_dateformat($laundry_id_to_laundry_day[$laundry_id]['created_at'])).'</a></li>';
}
echo '</ul>';

?>
<?php } ?>
</body>
</html>
