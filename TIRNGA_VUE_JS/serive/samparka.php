<?php
$conn = mysqli_connect("localhost", "onorc_tirngatirnga", "onorc_tirngatirnga", "onorc_tirngatirnga");
if (!$conn) {
    echo "Error: " . mysqli_connect_error();
    exit();
}
date_default_timezone_set("Asia/Kolkata"); 
?>