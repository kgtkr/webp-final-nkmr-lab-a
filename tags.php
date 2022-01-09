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

        if ($action === 'add_relation') {
            $tag_id1 = intval($_POST['tag_id1'] ?? null);
            $tag_id2 = intval($_POST['tag_id2'] ?? null);
            if ($tag_id1 > $tag_id2) {
                $tmp = $tag_id1;
                $tag_id1 = $tag_id2;
                $tag_id2 = $tmp;
            }
            $stat = $db->prepare('SELECT * FROM tags WHERE id = :tag_id1 AND user_id = :user_id AND deleted_at IS NULL');
            $stat->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
            $stat->bindValue(':tag_id1', $tag_id1, PDO::PARAM_INT);
            $stat->execute();
            $tag1 = $stat->fetch(PDO::FETCH_ASSOC);
            if ($tag1 === false) {
                $tag1 = null;
            }
            $stat = $db->prepare('SELECT * FROM tags WHERE id = :tag_id2 AND user_id = :user_id AND deleted_at IS NULL');
            $stat->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
            $stat->bindValue(':tag_id2', $tag_id2, PDO::PARAM_INT);
            $stat->execute();
            $tag2 = $stat->fetch(PDO::FETCH_ASSOC);
            if ($tag2 === false) {
                $tag2 = null;
            }
            if ($tag1 !== null && $tag2 !== null) {
                $stat = $db->prepare('INSERT INTO tag_incompatible_ralations (tag_id1, tag_id2) VALUES (:tag_id1, :tag_id2)');
                $stat->bindValue(':tag_id1', $tag_id1, PDO::PARAM_INT);
                $stat->bindValue(':tag_id2', $tag_id2, PDO::PARAM_INT);
                try {
                    $stat->execute();
                } catch (PDOException $e) {
                    if ($e->getCode() !== '23000') {
                        throw $e;
                    }
                }
            }
        }

        if ($action === 'delete_relation') {
            $tag_id1 = intval($_POST['tag_id1'] ?? null);
            $tag_id2 = intval($_POST['tag_id2'] ?? null);
            if ($tag_id1 > $tag_id2) {
                $tmp = $tag_id1;
                $tag_id1 = $tag_id2;
                $tag_id2 = $tmp;
            }
            $stat = $db->prepare('SELECT * FROM tags WHERE id = :tag_id1 AND user_id = :user_id AND deleted_at IS NULL');
            $stat->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
            $stat->bindValue(':tag_id1', $tag_id1, PDO::PARAM_INT);
            $stat->execute();
            $tag1 = $stat->fetch(PDO::FETCH_ASSOC);
            if ($tag1 === false) {
                $tag1 = null;
            }
            $stat = $db->prepare('SELECT * FROM tags WHERE id = :tag_id2 AND user_id = :user_id AND deleted_at IS NULL');
            $stat->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
            $stat->bindValue(':tag_id2', $tag_id2, PDO::PARAM_INT);
            $stat->execute();
            $tag2 = $stat->fetch(PDO::FETCH_ASSOC);
            if ($tag2 === false) {
                $tag2 = null;
            }
            if ($tag1 !== null && $tag2 !== null) {
                $stat = $db->prepare('DELETE FROM tag_incompatible_ralations WHERE tag_id1 = :tag_id1 AND tag_id2 = :tag_id2');
                $stat->bindValue(':tag_id1', $tag_id1, PDO::PARAM_INT);
                $stat->bindValue(':tag_id2', $tag_id2, PDO::PARAM_INT);
                $stat->execute();
            }
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
        <div>ログインしてください</div>
    <?php
        } else { 
        $stat = $db->prepare('SELECT * FROM tags WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY id');
        $stat->bindValue(':user_id', $login_user_id, PDO::PARAM_STR);
        $stat->execute();
        $tags = $stat->fetchAll();

        $tagHash = [];
        foreach ($tags as $tag) {
            $tagHash[$tag['id']] = $tag;
        }

        $stmt = $db->prepare('SELECT * FROM tag_incompatible_ralations WHERE tag_id1 IN ' . array_prepare_query('tag_id', count($tags)));
        array_prepare_bind($stmt, 'tag_id', array_column($tags, 'id'), PDO::PARAM_INT);
        $stmt->execute();
        $relations = $stmt->fetchAll();
    ?>
        <div><a href="tag_form.php">新規作成</a></div>
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
        <h2>組み合わせてはいけないタグの設定</h2>
        <form action="tags.php" method="post">
            <input type="hidden" name="action" value="add_relation">
            <?php echo_csrf_token(); ?>
            <select name="tag_id1">
                <?php foreach ($tags as $tag) { ?>
                    <option value="<?php echo h($tag['id']); ?>"><?php echo h($tag['name']); ?></option>
                <?php } ?>
            </select>
            <select name="tag_id2">
                <?php foreach ($tags as $tag) { ?>
                    <option value="<?php echo h($tag['id']); ?>"><?php echo h($tag['name']); ?></option>
                <?php } ?>
            </select>
            <input type="submit" value="追加">
        </form>
        <table border='1'>
            <tr>
                <th>タグ1</th>
                <th>タグ2</th>
                <th>削除</th>
            </tr>
            <?php
                foreach ($relations as $relation) {
                ?>
                <tr>
                    <td><?php echo h($tagHash[$relation['tag_id1']]['name']); ?></td>
                    <td><?php echo h($tagHash[$relation['tag_id2']]['name']); ?></td>
                    <td>
                        <form action="tags.php" method="post">
                            <input type="hidden" name="tag_id1" value="<?php echo h($relation['tag_id1']); ?>">
                            <input type="hidden" name="tag_id2" value="<?php echo h($relation['tag_id2']); ?>">
                            <input type="hidden" name="action" value="delete_relation">
                            <?php echo_csrf_token(); ?>
                            <input type="submit" value="削除">
                        </form>
                    </td>
                </tr>
                <?php
                }
            ?>
        </table>
    <?php } ?>
</body>
</html>
