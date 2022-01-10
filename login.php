<?php
require_once("./lib/prelude.php");
$db = connectDB();
$msg = null;

if (isset($_POST["user_id"]) && isset($_POST["password"])) {
    $user_id = $_POST["user_id"];
    $password = $_POST["password"];
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch();
    if ($user !== false) {
        if (password_verify($password, $user["hashed_password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["csrf_token"] =  bin2hex(random_bytes(64));
            $msg = "ログインしました";
        } else {
            $msg = "ユーザーIDまたはパスワードが間違っています";
        }
    } else {
        $msg = "ユーザーIDまたはパスワードが間違っています";
    }
}
$login_user_id=login_user_id();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>ログイン</title>
</head>
<body>
<?php echo_header(); ?>
<h1>ログイン</h1>
<?php
if ($msg !== null) {
    echo "<div>$msg</div>";
}
?>
<?php if ($login_user_id === null) { ?>
<form action="login.php" method="post">
    <div>
        <input type="text" name="user_id" placeholder="ユーザーID">
    </div>
    <div>
        <input type="password" name="password" placeholder="パスワード">
    </div>
    <div>
        <input type="submit" value="ログイン">
    </div>
</form>
<a href="signup.php">新規登録</a>
<?php } else if ($msg === null) { ?>
<div>「<?php echo h($login_user_id); ?>」でログインしています</div>
<a href=".">トップページ</a>
<?php } ?>
</body>
</html>
