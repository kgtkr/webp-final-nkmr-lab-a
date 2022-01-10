<?php
    require_once('./lib/prelude.php');
    require_once('./lib/image.php');

    $login_user_id = login_user_id();
    $db = connectDB();
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    $action = $_POST['action'] ?? null;

    if ($action !== null && verify_csrf_token()) {
        if ($action === 'submit') {
            $name = $_POST['name'] ?? '';
            $image = $_FILES['image'] ?? null;
            $prev_image_filename = $_POST['image_filename'] ?? null;
            if ($image !== null) {
                $image_filename = image\save($image);
            } else {
                $image_filename = null;
            }
            if ($image_filename === null && $prev_image_filename !== null && image\exist($prev_image_filename)) {
                $image_filename = $prev_image_filename;
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
            <?php echo_csrf_token(); ?>
            <input type="hidden" name="action" value="submit">
            <div>
                <label for="name">タグ名</label>
                <input type="text" name="name" value="<?php if ($tag !== null) { echo h($tag['name']); } ?>">
            </div>
            <div>
                <label for="image">画像</label>
                <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
                <input type="file" name="image">
            </div>
            <?php if ($tag !== null && $tag['image_filename'] !== null) { ?>
                <div>
                    <div>
                        <img src="images/<?php echo h($tag['image_filename']); ?>">
                    </div>
                    <div>
                        <label for="image_filename">画像を保持</label>
                        <input type="checkbox" name="image_filename" value="<?php echo h($tag['image_filename']); ?>" checked>
                    </div>
                </div>
            <?php } ?>
            <input type="submit" value="送信">
    <?php } ?>
</body>
</html>
