<?php
require_once("lib/prelude.php");
require_once("lib/clothes_group.php");

$login_user_id = login_user_id();
$db = connectDB();

$stmt = $db->prepare('SELECT * FROM clohtes WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY id');
$stmt->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
$stmt->execute();
$clohtes = $stmt->fetchAll();

$result = clothes_group\group($db, $clohtes);
print_r($result);
?>
