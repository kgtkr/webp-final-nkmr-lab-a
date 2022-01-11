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

echo '<ul>';
foreach($user_laundries_history as $user_laundry_day){
    echo '<li><a href="./laundry.php?laundry_id='. $user_laundry_day['id'] .'">'.h(app_dateformat($user_laundry_day['created_at'])).'</a></li>';
}
echo '</ul>';
?>
<?php } ?>
</body>
</html>
