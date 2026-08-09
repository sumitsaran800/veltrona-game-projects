<?php
// 1. Error Reporting On (ताकि एरर पता चले)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conn.php';

// 2. CORS Headers (सबसे ज़रूरी - इसके बिना अपडेट नहीं होगा)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 3. Handle Preflight Request (Browser Check)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 4. User ID (Login System के हिसाब से बदलें, अभी 1 फिक्स है)
$user_id = 1;

// 5. Data Read
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Debug Check
if (empty($data)) {
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit();
}

$type = isset($data['type']) ? $data['type'] : '';
$amount = isset($data['amount']) ? floatval($data['amount']) : 0;

// Amount Validation
if ($amount <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid Amount"]);
    exit();
}

// 6. Logic
if ($type == 'bet') {
    // बैलेंस चेक करें
    $check = $conn->query("SELECT motta FROM shonu_kaichila WHERE balakedara = $user_id");
    
    if ($check && $check->num_rows > 0) {
        $row = $check->fetch_assoc();
        $current_bal = floatval($row['motta']);

        if ($current_bal >= $amount) {
            // पैसे काटें (Deduct)
            $update = $conn->query("UPDATE shonu_kaichila SET motta = motta - $amount WHERE balakedara = $user_id");
            
            if ($update) {
                echo json_encode(["status" => "success", "message" => "Bet Placed", "new_balance" => $current_bal - $amount]);
            } else {
                echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Low Balance"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User Not Found"]);
    }
} 
elseif ($type == 'win') {
    // पैसे जोड़ें (Add Win)
    $update = $conn->query("UPDATE shonu_kaichila SET motta = motta + $amount WHERE balakedara = $user_id");
    
    if ($update) {
        echo json_encode(["status" => "success", "message" => "Win Added"]);
    } else {
        echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
    }
} 
else {
    echo json_encode(["status" => "error", "message" => "Invalid Type"]);
}
?>