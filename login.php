<?php
require_once('./lib/prelude.php');
$_SESSION['user_id'] = 'user';
$_SESSION['csrf_token'] = bin2hex(random_bytes(64));
?>
