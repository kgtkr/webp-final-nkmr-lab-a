<?php
    require_once('./lib/prelude.php');

    $login_user_id = login_user_id();
    $db = connectDB();

    $action = $_POST['action'] ?? null;

    if ($action !== null && verify_csrf_token()) {
        if ($action === 'delete') {
            $id = intval($_POST['id'] ?? null);
            $stmt = $db->prepare('UPDATE tags SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id AND user_id = :user_id');
            $stmt->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タグ一覧</title>
</head>
<body>
    <h1>タグ一覧</h1>
    <?php if ($login_user_id === null) { ?>
        <p>ログインしてください</p>
    <?php } else { ?>
        <table border="1">
            <tr>
                <th>タグ名</th>
                <th>画像</th>
                <th>編集</th>
                <th>削除</th>
            </tr>
            <?php
                $stmt = $db->prepare('SELECT * FROM tags WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY id');
                $stmt->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
                $stmt->execute();
                $tags = $stmt->fetchAll();
                foreach ($tags as $tag) {
            ?>
                <tr>
                    <td><?php echo h($tag['name']); ?></td>
                    <td>
                        <?php if ($tag['image_filename'] !== null) { ?>
                            <img src="images/<?php echo h($tag['image_filename']); ?>">
                        <?php } ?>
                    </td>
                    <td><a href="tag_form.php?id=<?php echo h($tag['id']); ?>">編集</a></td>
                    <td>
                        <form action="tags.php" method="post">
                            <input type="hidden" name="id" value="<?php echo h($tag['id']); ?>">
                            <input type="hidden" name="action" value="delete">
                            <?php echo_csrf_token(); ?>
                            <input type="submit" value="削除">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</body>
</html>