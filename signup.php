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

            // 初期データ登録
            $stat = $db->prepare("INSERT INTO tags (name, image_filename, user_id) VALUES (:name, :image_filename, :user_id)");
            $stat->bindValue(":name", "手洗い", PDO::PARAM_STR);
            $stat->bindValue(":image_filename", "tag1.gif", PDO::PARAM_STR);
            $stat->bindValue(":user_id", $user_id, PDO::PARAM_STR);
            $stat->execute();
            $tag1_id = $db->lastInsertId();

            $stat = $db->prepare("INSERT INTO tags (name, image_filename, user_id) VALUES (:name, :image_filename, :user_id)");
            $stat->bindValue(":name", "洗濯機可", PDO::PARAM_STR);
            $stat->bindValue(":image_filename", "tag2.gif", PDO::PARAM_STR);
            $stat->bindValue(":user_id", $user_id, PDO::PARAM_STR);
            $stat->execute();
            $tag2_id = $db->lastInsertId();

            $stat = $db->prepare("INSERT INTO tags (name, image_filename, user_id) VALUES (:name, NULL, :user_id)");
            $stat->bindValue(":name", "色落ちしやすい", PDO::PARAM_STR);
            $stat->bindValue(":user_id", $user_id, PDO::PARAM_STR);
            $stat->execute();
            $tag3_id = $db->lastInsertId();

            $stat = $db->prepare("INSERT INTO tags (name, image_filename, user_id) VALUES (:name, NULL, :user_id)");
            $stat->bindValue(":name", "色が薄い", PDO::PARAM_STR);
            $stat->bindValue(":user_id", $user_id, PDO::PARAM_STR);
            $stat->execute();
            $tag4_id = $db->lastInsertId();

            $stat = $db->prepare("INSERT INTO tag_incompatible_ralations (tag_id1, tag_id2) VALUES (:tag_id1, :tag_id2)");
            $stat->bindValue(":tag_id1", $tag1_id, PDO::PARAM_INT);
            $stat->bindValue(":tag_id2", $tag2_id, PDO::PARAM_INT);
            $stat->execute();

            $stat = $db->prepare("INSERT INTO tag_incompatible_ralations (tag_id1, tag_id2) VALUES (:tag_id1, :tag_id2)");
            $stat->bindValue(":tag_id1", $tag3_id, PDO::PARAM_INT);
            $stat->bindValue(":tag_id2", $tag4_id, PDO::PARAM_INT);
            $stat->execute();

            $stat = $db->prepare("INSERT INTO clohtes (name, image_filename, user_id) VALUES (:name, :image_filename, :user_id)");
            $stat->bindValue(":name", "青い服", PDO::PARAM_STR);
            $stat->bindValue(":image_filename", "clohtes1.png", PDO::PARAM_STR);
            $stat->bindValue(":user_id", $user_id, PDO::PARAM_STR);
            $stat->execute();
            $clohte1_id = $db->lastInsertId();

            $stat = $db->prepare("INSERT INTO clohtes (name, image_filename, user_id) VALUES (:name, :image_filename, :user_id)");
            $stat->bindValue(":name", "白い服", PDO::PARAM_STR);
            $stat->bindValue(":image_filename", "clohtes2.png", PDO::PARAM_STR);
            $stat->bindValue(":user_id", $user_id, PDO::PARAM_STR);
            $stat->execute();
            $clohte2_id = $db->lastInsertId();

            $stat = $db->prepare("INSERT INTO clothes_tags (tag_id, clothes_id) VALUES (:tag_id, :clothes_id)");
            $stat->bindValue(":tag_id", $tag1_id, PDO::PARAM_INT);
            $stat->bindValue(":clothes_id", $clohte1_id, PDO::PARAM_INT);
            $stat->execute();

            $stat = $db->prepare("INSERT INTO clothes_tags (tag_id, clothes_id) VALUES (:tag_id, :clothes_id)");
            $stat->bindValue(":tag_id", $tag2_id, PDO::PARAM_INT);
            $stat->bindValue(":clothes_id", $clohte1_id, PDO::PARAM_INT);
            $stat->execute();
        } catch (PDOException $e) {
            $msg = "ユーザーIDが既に使用されています";
            throw $e;
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
