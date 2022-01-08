<?php
namespace image;

function save($file) {
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

    $filename = uniqid() . $ext;
    $path = 'images/' . $filename;
    move_uploaded_file($file['tmp_name'], $path);
    return $filename;
}
?>
