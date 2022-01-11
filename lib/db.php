<?php
function connectDB() {
    $db = new PDO("sqlite:" . dirname(__FILE__) . "/../db/db.sqlite");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function array_prepare_query($key, $array) {
    $query = "(";
    foreach ($array as $i => $value) {
        $query .= ":${key}__${i}";
        $query .= ",";
    }
    if (count($array) > 0) {
        $query = substr($query, 0, -1);
    }
    $query .= ")";
    return $query;
}

function array_prepare_bind($stat, $key, $array, $type) {
    foreach ($array as $i => $value) {
        $stat->bindValue(":${key}__${i}", $value, $type);
    }
}
?>
