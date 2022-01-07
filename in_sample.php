<?php
require_once("lib/prelude.php");

$login_user_id = login_user_id();
$db = connectDB();

$stmt = $db->prepare('SELECT * FROM clohtes WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY id');
$stmt->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
$stmt->execute();
$clohtes = $stmt->fetchAll();

// 効率の悪い例

foreach ($clohtes as $clohte) {
    echo h($clohte['name']) . ':<br>';
    // get tags
    $stmt = $db->prepare('SELECT * FROM clothes_tags WHERE clothes_id = :id');
    $stmt->bindValue(':id', $clohte['id'], PDO::PARAM_INT);
    $stmt->execute();
    $tags = $stmt->fetchAll();
    foreach ($tags as $tag) {
        echo h($tag['tag_id']) . '<br>';
    }
}

echo "<hr>";

// 効率の良い例
$stmt = $db->prepare('SELECT * FROM clothes_tags WHERE clothes_id IN ' . array_prepare_query('clothes_id', count($clohtes)));
array_prepare_bind($stmt, 'clothes_id', array_column($clohtes, 'id'), PDO::PARAM_INT);
$stmt->execute();
$tags = $stmt->fetchAll();
$clothes_id_to_tags = [];
foreach ($tags as $tag) {
    $clothes_id_to_tags[$tag['clothes_id']][] = $tag['tag_id'];
}
foreach ($clohtes as $clohte) {
    echo h($clohte['name']) . ':<br>';
    foreach ($clothes_id_to_tags[$clohte['id']] ?? [] as $tag_id) {
        echo h($tag_id) . '<br>';
    }
}
?>
