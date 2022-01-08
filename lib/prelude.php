<?php
include_once(dirname(__FILE__) .  "/db.php");
session_start();

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function login_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function verify_csrf_token() {
    return isset($_POST['csrf_token']) && isset($_SESSION['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token'];
}

function echo_csrf_token() {
    echo '<input type="hidden" name="csrf_token" value="' . h($_SESSION['csrf_token']) . '">';
}
?>
