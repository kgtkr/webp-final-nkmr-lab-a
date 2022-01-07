<?php
function connectDB() {
    $db = new PDO("sqlite:" . dirname(__FILE__) . "/../db/db.sqlite");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function array_prepare_query($key, $n) {
    $query = "(";
    for ($i = 0; $i < $n; $i++) {
        $query .= ":$key$i";
        if ($i < $n - 1) {
            $query .= ",";
        }
    }
    $query .= ")";
    return $query;
}

function array_prepare_bind($stat, $key, $array, $type) {
    foreach ($array as $i => $value) {
        $stat->bindValue(":$key$i", $value, $type);
    }
}
?>
