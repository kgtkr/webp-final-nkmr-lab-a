<?php
require_once('./lib/prelude.php');
$login_user_id = login_user_id();
$msg = null;
if ($login_user_id !== null && verify_csrf_token()) {
    session_destroy();
    $msg = "ログアウトしました";
} else {
    $msg = "ログアウトできませんでした";
}
?>

<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>ログアウト</title>
<link rel="stylesheet" href="./layout.css">
</head>
<body>
<?php echo_header(); ?>
<h1>ログアウト</h1>
<?php
if ($msg !== null) {
    echo "<div>$msg</div>";
}
?>
<a href=".">トップページ</a>
</body>
</html>
