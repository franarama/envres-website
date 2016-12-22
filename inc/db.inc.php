<?php
function fConnectDb() {
    $db = new mysqli('localhost','env-user','PASSWORD',"envres");
    if (! $db->connect_error) {
        return $db;
    }
    else {
        die("Cannot connect to database" . $db->conn);
    }
}
?>
