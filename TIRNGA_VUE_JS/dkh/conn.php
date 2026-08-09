<?php
date_default_timezone_set("Asia/Kolkata");

define("DB_SERVER", "localhost");
define("DB_USERNAME", "onorc_tirngatirnga");
define("DB_PASSWORD", "onorc_tirngatirnga");
define("DB_NAME", "onorc_tirngatirnga");

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn == false){
    die("Error: Cannot connect to database");
}
?>