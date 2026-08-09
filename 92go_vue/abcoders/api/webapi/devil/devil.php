<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'fmyaeusu_777');
define('DB_PASSWORD', 'fmyaeusu_777');
define('DB_NAME', 'fmyaeusu_777');

function getDBConnection() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
