<?php
include_once(dirname(__FILE__) .  "/db.php");
ini_set('display_errors', "On");
ini_set('error_reporting', E_ALL);
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

function echo_header() {
    $login_user_id = login_user_id();
    if ($login_user_id === null) {
        echo '<a href="login.php">ログイン</a>';
    } else {
        ?>
        <table>
        <tr>
        <td>
            <a href="clothes.php">服一覧</a>
        </td>
        <td>
            <a href="tags.php">タグ一覧</a>
        </td>
        <td>
            <a href="laundry_history.php">洗濯履歴</a>
        </td>
        <td>
            <form action="logout.php" method="post">
            <?php echo_csrf_token(); ?>
            <input type="submit" value="ログアウト">
            </form>
        </td>
        </tr>
        </table>
        <?php
    }
}

function app_dateformat($date) {
    return date_create($date)->setTimeZone(new DateTimeZone('Asia/Tokyo'))->format('Y/m/d H:i');
}
?>
