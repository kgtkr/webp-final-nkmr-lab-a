<?php
namespace image;

function save(array $file): ?string {
    $mime = $file['type'];
    if ($mime === 'image/jpeg') {
        $ext = '.jpg';
    } else if ($mime === 'image/png') {
        $ext = '.png';
    } else if ($mime === 'image/gif') {
        $ext = '.gif';
    } else {
        return null;
    }

    $filename = bin2hex(random_bytes(32)) . $ext;
    $path = 'images/' . $filename;
    move_uploaded_file($file['tmp_name'], $path);
    return $filename;
}

function exist(string $filename): bool {
    return preg_match('/^[0-9a-z]+\.[0-9a-z]+$/', $filename) && file_exists('images/' . $filename);
}

?>
