<?php
if (!defined("SECURITY_PASS")) {
    define("SECURITY_PASS", true);
}
error_reporting(0);

$conn = @mysqli_connect('localhost', 'onorc_89club', 'onorc_89club', 'onorc_89club');
if (!$conn) {
    $conn = @mysqli_connect('localhost', 'root', '', 'onorc_89club');
}
if (!$conn) {
    $conn = @mysqli_connect('localhost', 'root', '', 'clubgo_bot');
}
if (!$conn) {
    echo "Error: " . mysqli_connect_error();
    exit();
}

date_default_timezone_set("Asia/Kolkata"); 
?>