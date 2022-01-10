<?php
require_once("./lib/prelude.php");
require_once("./lib/clothes_group.php");
$login_user_id=login_user_id();
$db = connectDB();

if (verify_csrf_token()) {
    $clothes_ids = array_map('intval',  $_POST['clothes'] ?? []);
    $stat = $db->prepare('SELECT * FROM clohtes WHERE user_id = :user_id AND deleted_at IS NULL AND id IN '. array_prepare_query('id', count($clothes_ids)));
    array_prepare_bind($stat, 'id', $clothes_ids, PDO::PARAM_INT);
    $stat->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
    $stat->execute();
    $clothes = $stat->fetchAll();
    $clothes_group = clothes_group\group($db, $clothes);

    $stat = $db->prepare('INSERT INTO laundries (user_id) VALUES (:user_id)');
    $stat->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
    $stat->execute();
    $laundry_id = $db->lastInsertId();
    foreach ($clothes_group as $clothes_id=>$group_id) {
        $stat = $db->prepare('INSERT INTO laundry_clothes (laundry_id, clothes_id, group_id) VALUES (:laundry_id, :clothes_id, :group_id)');
        $stat->bindValue(':laundry_id', $laundry_id, PDO::PARAM_INT);
        $stat->bindValue(':clothes_id', $clothes_id, PDO::PARAM_INT);
        $stat->bindValue(':group_id', $group_id, PDO::PARAM_INT);
        $stat->execute();
    }
    header('Location: laundry.php?laundry_id=' . $laundry_id);

    exit;
}
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>洗濯グループ分け</title>
<link rel="stylesheet" href="./layout.css">
</head>
<body>
<?php echo_header(); ?>
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
$stat=$db->prepare("
    SELECT
        tags.id as id,
        tags.name as name,
        tags.image_filename as image_filename,
        clothes_tags.clothes_id as clothes_id
    FROM
        clothes_tags JOIN tags ON clothes_tags.tag_id=tags.id
    WHERE
        tags.user_id=:user_id AND tags.deleted_at IS NULL AND clothes_tags.clothes_id IN ".array_prepare_query('clothes_id', count($clothes_ids))."");
array_prepare_bind($stat, 'clothes_id', $clothes_ids, PDO::PARAM_INT);
$stat->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
$stat->execute();
$tags=$stat->fetchAll();
$tagHash=[];
foreach($tags as $tag){
    $tagHash[$tag['clothes_id']][]=$tag;
}
$groups = [];
$group_tags = [];
foreach($laundries as $laundry){
    $groups[$laundry['group_id']][] = $laundry['clothes_id'];
    foreach($tagHash[$laundry['clothes_id']] ?? [] as $tag){
        $group_tags[$laundry['group_id']][$tag['id']] = $tag;
    }
}
$clothes_id_to_clothes = [];
foreach($clothes as $clothe){
    $clothes_id_to_clothes[$clothe['id']] = $clothe;
}
foreach($groups as $group_id=>$clothe_ids){
    echo '<div class="group">';
    echo '<h2>タグ</h2>';
    echo '<ul>';
    foreach($group_tags[$group_id] ?? [] as $tag){
        echo '<li>';
        if ($tag['image_filename']) {
            echo '<img src="images/'.$tag['image_filename'].'">';
        }
        echo h($tag['name']);
        echo '</li>';
    }
    echo '</ul>';
    echo '<h2>服</h2>';
    echo '<ul>';
    foreach($clothe_ids as $clothe_id){
        echo '<li>';
        if ($clothes_id_to_clothes[$clothe_id]['image_filename']) {
            echo '<img src="images/'.$clothes_id_to_clothes[$clothe_id]['image_filename'].'">';
        }
        echo h($clothes_id_to_clothes[$clothe_id]['name']);
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
}

?>
<?php } ?>
</body>
</html>
