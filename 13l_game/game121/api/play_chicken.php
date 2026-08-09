<?php
include "../conn.php"; // Database Connection

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 1. Input Check
$input = json_decode(file_get_contents("php://input"), true);
$betAmount = isset($input['bet']) ? $input['bet'] : 0;

// 2. Fetch Admin Setting from NEW TABLE (chicken_game_control)
$sql = "SELECT control_value FROM chicken_game_control WHERE control_name = 'win_percentage' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $admin_setting = intval($row['control_value']);
} else {
    // Agar Table nahi mili to Default 50 maan lo
    $admin_setting = 50; 
}

// 3. ADMIN LOGIC (The Curve)
// Formula: (AdminValue * AdminValue) / 125
// Example: 
// 100 input = 80% Win Chance (Easy)
// 50 input  = 20% Win Chance (Hard/High Profit for Admin)
$actual_win_chance = ($admin_setting * $admin_setting) / 125;

// Safety Cap (Max 90% Win Chance)
if($actual_win_chance > 90) $actual_win_chance = 90;

// 4. RNG (Random Number Generator)
$rng = rand(1, 100);

// 5. DECISION
if ($rng <= $actual_win_chance) {
    // SAFE (User Wins this step)
    echo json_encode([
        'status' => 'success',
        'msg' => 'safe',
        'debug_chance' => $actual_win_chance
    ]);
} else {
    // CRASH (User Loses - Car hits)
    echo json_encode([
        'status' => 'crash',
        'msg' => 'dead',
        'debug_chance' => $actual_win_chance
    ]);
}
?>