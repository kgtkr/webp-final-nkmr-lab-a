<?php
function connectDB() {
    $db = new PDO("sqlite:" . dirname(__FILE__) . "/../db/db.sqlite");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}
?>
