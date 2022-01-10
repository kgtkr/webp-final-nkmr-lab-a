<?php
require_once("./lib/prelude.php");
$db = connectDB();
$msg = null;

if (isset($_POST["user_id"]) && isset($_POST["password"])) {
    // resister
    $user_id = $_POST["user_id"];
    $password = $_POST["password"];
    if (!preg_match("/^[0-9a-z]{2,20}$/", $user_id)) {
        $msg = "ユーザーIDは半角英数字2文字以上20文字以下で入力してください";
    } else if (!preg_match("/^[0-9a-zA-Z]{8,64}$/", $password)) {
        $msg = "パスワードは半角英数字8文字以上64文字以下で入力してください";
    } else {
        $stmt = $db->prepare("INSERT INTO users (id, hashed_password) VALUES (:user_id, :hashed_password)");
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->bindValue(":hashed_password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        try {
            $stmt->execute();
            $msg = "ユーザーを登録しました";

            $_SESSION["user_id"] = $user_id;
            $_SESSION["csrf_token"] = bin2hex(random_bytes(64));
        } catch (PDOException $e) {
            $msg = "ユーザーIDが既に使用されています";
        }
    }
}

$login_user_id=login_user_id();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>ユーザー登録</title>
<link rel="stylesheet" href="./layout.css">
</head>
<body>
<? echo_header(); ?>
<h1>ユーザー登録</h1>
<?php
if ($msg !== null) {
    echo "<div>$msg</div>";
}
?>
<?php if ($login_user_id === null) { ?>
<form action="signup.php" method="post">
    <div>
        <input type="text" name="user_id" placeholder="ユーザーID">
    </div>
    <div>
        <input type="password" name="password" placeholder="パスワード">
    </div>
    <div>
        <input type="submit" value="登録">
    </div>
</form>
<?php } else if ($msg === null) { ?>
<div>「<?php echo h($login_user_id); ?>」でログインしています</div>
<a href=".">トップページ</a>
<?php } ?>
</body>
</html>
