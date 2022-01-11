<?php
require_once("./lib/prelude.php");
$db = connectDB();
$msg = null;

$login_user_id=login_user_id();
if ($login_user_id!==null && isset($_POST["newPass"]) && isset($_POST["oldPass"])) {
    $newPass = $_POST["newPass"];
    $oldPass = $_POST["oldPass"];
    if(!preg_match("/^[0-9a-zA-Z]{8,64}$/", $newPass)) {
        $msg = "パスワードは半角英数字8文字以上64文字以下で入力してください";
    }

    else{
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
        $stmt->bindValue(":user_id", $login_user_id, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();
        if (password_verify($oldPass, $user["hashed_password"])) {
            $st = $db->prepare("UPDATE users SET hashed_password = :newPass WHERE id = :login_user_id;");
            $st->bindValue(":newPass", password_hash($newPass, PASSWORD_DEFAULT), PDO::PARAM_STR);
            $st->bindValue(":login_user_id", $login_user_id, PDO::PARAM_STR);
            $st->execute();
            $msg = "パスワードを変更しました。";
        } else {
            $msg = "パスワードが間違っています";
        }
    }
}
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>パスワード変更</title>
<link rel="stylesheet" href="./layout.css">
</head>
<body>
<?php echo_header(); ?>
<h1>パスワード変更</h1>
<?php
if ($msg !== null) {
    echo "<div>$msg</div>";
}
?>
<?php if ($login_user_id !== null) { ?>
<form action="change_password.php" method="post">
    <div>
        <input type="password" name="oldPass" placeholder="現在のパスワード">
    </div>
    <div>
        <input type="password" name="newPass" placeholder="新しいパスワード">
    </div>
    <div>
        <input type="submit" value="変更">
    </div>
</form>
<?php } else{ ?>ログインしてください。
<?php } ?>
</body>
</html>
