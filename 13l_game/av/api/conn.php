<?php
// api/conn.php
$servername = "localhost";
$username = "rflrovxl_luckywebddjaiclubbbb"; // अपना DB यूजरनेम डालें
$password = "rflrovxl_luckywebddjaiclubbbb"; // अपना DB पासवर्ड डालें
$dbname = "rflrovxl_luckywebddjaiclubbbb";   // अपना DB नाम डालें

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die(json_encode(["status" => "error", "message" => "DB Fail"])); }
?>