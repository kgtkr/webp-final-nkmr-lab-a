<?php
    require_once('./lib/db.php');
    require_once('./lib/image.php');

    session_start();

    $login_user_id = $_SESSION['user_id'] ?? null;
    $csrf_token = $_SESSION['csrf_token'] ?? null;
    $db = connectDB();
    $id = intval($_GET['id']) ?? null;

    $action = $_POST['action'] ?? null;

    $tag = null;
    if ($id !== null) {
        $stmt = $db->prepare('SELECT * FROM tags WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL');
        $stmt->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $tag = $stmt->fetch();
        if ($tag === false) {
            $tag = null;
        }
    }

    if ($action !== null) {
        $verify_csrf_token = isset($_POST['csrf_token']) && $_POST['csrf_token'] === $csrf_token;
        if ($verify_csrf_token) {
            if ($action === 'submit') {
                $name = $_POST['name'] ?? '';
                $image = $_FILES['image'] ?? null;
                if ($image !== null) {
                    $image_filename = image\save($image);
                } else {
                    $image_filename = null;
                }

                if ($id === null) {
                    $stmt = $db->prepare('INSERT INTO tags (name, image_filename, user_id) VALUES (:name, :image_filename, :user_id)');
                } else {
                    $stmt = $db->prepare('UPDATE tags SET name = :name, image_filename = :image_filename WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL');
                    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                }
                $stmt->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
                $stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt->bindValue(':image_filename', $image_filename, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タグ<?php if ($id !== null) { echo '編集'; } else { echo '追加'; }?></title>
</head>
<body>
    <h1>タグ<?php if ($id !== null) { echo '編集'; } else { echo '追加'; }?></h1>
    <?php if ($login_user_id === null) { ?>
        <p>ログインしてください</p>
    <?php } else { ?>
        <form action="tag_form.php<?php if ($id !== null) { echo '?id=' . $id; } ?>" method="post"  enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="action" value="submit">
            <input type="text" name="name" value="<?php if ($tag !== null) { echo htmlspecialchars($tag['name']); } ?>">
            <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
            <input type="file" name="image">
            <input type="submit" value="送信">
    <?php } ?>
</body>
</html>
