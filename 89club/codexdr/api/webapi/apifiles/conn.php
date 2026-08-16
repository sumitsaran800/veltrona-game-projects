<?php
/*
This file contains database configuration assuming you are running MySQL
using user "game_91game" and password "game_91game"
*/

date_default_timezone_set('Asia/Kolkata');

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'onorc_89club');
define('DB_PASSWORD', 'onorc_89club');
define('DB_NAME', 'onorc_89club');

$conn = @mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$conn) {
    $conn = @mysqli_connect('localhost', 'root', '', 'clubgo_bot');
}
if (!$conn) {
    $conn = mysqli_connect('localhost', 'root', '', 'onorc_89club');
}
if ($conn == false) {
    die('Database connection error');
}

// Check the connection
if ($conn === false) {
    die("❌ Error: Cannot connect to database");
}

// --- Auto Clean Log File ---
$logFile = "/www/server/nodejs/vhost/logs/donttouch.log";

if (file_exists($logFile) && is_writable($logFile)) {
    // Blank the log file
    file_put_contents($logFile, "");
    //echo "✅ Log file cleared successfully."; // Uncomment if you want message
}
?>
