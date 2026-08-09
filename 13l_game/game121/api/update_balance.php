<?php
// File: api/update_balance.php

// 1. Database Connection & Functions Check
// Automatic path detection to find conn.php
if (file_exists("../conn.php")) { 
    include "../conn.php"; 
    include "../functions2.php"; 
} else if (file_exists("../../conn.php")) { 
    include "../../conn.php"; 
    include "../../functions2.php"; 
} else { 
    // Agar file na mile to error do
    header('Content-Type: application/json');
    die(json_encode(['code'=>500, 'msg'=>'Critical Error: DB Connection File Not Found'])); 
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 2. Read Raw Input (Fix for Invalid Amount Error)
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

// Agar JSON sahi nahi hai
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['code'=>400, 'msg'=>'Invalid JSON Format']);
    exit;
}

$action = isset($data['action']) ? $data['action'] : ''; 
$amount = isset($data['amount']) ? floatval($data['amount']) : 0;

// 3. Token Verification
$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');

if(!$authHeader) {
    echo json_encode(['code'=>401, 'msg'=>'Token Missing']);
    exit;
}

$token = str_replace('Bearer ', '', $authHeader);

// functions2.php ka function call karein
if (!function_exists('is_jwt_valid')) {
    echo json_encode(['code'=>500, 'msg'=>'JWT Function Missing']);
    exit;
}

$checkToken = is_jwt_valid($token);
$authData = json_decode($checkToken, true);

if(!isset($authData['status']) || $authData['status'] !== 'Success') {
    echo json_encode(['code'=>401, 'msg'=>'Invalid Token']);
    exit;
}

$userId = $authData['payload']['id'];

// 4. Main Logic Switch
switch ($action) {
    
    // CASE A: Fetch Balance
    case 'fetch':
        $chk = $conn->query("SELECT motta FROM shonu_kaichila WHERE balakedara='$userId'");
        if($chk && $chk->num_rows > 0){
            $row = $chk->fetch_assoc();
            echo json_encode(['code'=>0, 'balance'=>$row['motta']]);
        } else {
            echo json_encode(['code'=>0, 'balance'=>0]); 
        }
        break;

    // CASE B: Deduct Bet (Cut Money)
    case 'deduct':
        if($amount <= 0) { 
            echo json_encode(['code'=>400, 'msg'=>"Invalid Amount: $amount"]); 
            exit; 
        }
        
        // Check Balance
        $chk = $conn->query("SELECT motta FROM shonu_kaichila WHERE balakedara='$userId'");
        $row = $chk->fetch_assoc();
        $currentBal = floatval($row['motta']);
        
        if($currentBal >= $amount) {
            $sql = "UPDATE shonu_kaichila SET motta = motta - $amount WHERE balakedara = '$userId'";
            if($conn->query($sql)) {
                $newBal = $currentBal - $amount;
                echo json_encode(['code'=>0, 'msg'=>'Bet Placed', 'balance'=>$newBal]);
            } else {
                echo json_encode(['code'=>500, 'msg'=>'Database Error']);
            }
        } else {
            echo json_encode(['code'=>400, 'msg'=>'Low Balance']);
        }
        break;

    // CASE C: Add Win (Add Money)
    case 'win':
        if($amount <= 0) { 
            echo json_encode(['code'=>400, 'msg'=>"Invalid Win Amount: $amount"]); 
            exit; 
        }
        
        $sql = "UPDATE shonu_kaichila SET motta = motta + $amount WHERE balakedara = '$userId'";
        if($conn->query($sql)) {
            // Get updated balance
            $chk = $conn->query("SELECT motta FROM shonu_kaichila WHERE balakedara='$userId'");
            $row = $chk->fetch_assoc();
            echo json_encode(['code'=>0, 'msg'=>'Win Added', 'balance'=>$row['motta']]);
        } else {
            echo json_encode(['code'=>500, 'msg'=>'Database Error']);
        }
        break;

    default:
        echo json_encode(['code'=>400, 'msg'=>"Unknown Action: $action"]);
        break;
}
?>