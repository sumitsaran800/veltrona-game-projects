<?php
/**
 * Callback.php - Seamless Wallet Handler
 *
 * QUICK SETUP:
 * 1. API_SECRET me callback secret dalo.
 * 2. Agar conn.php use karna hai to USE_CONN_FILE = true rakho aur CONN_FILE path sahi karo.
 * 3. Agar direct DB use karna hai to USE_CONN_FILE = false karke niche DB values bharo.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

// ============================================
// CONFIGURATION
// ============================================
define('API_SECRET', 'ab2bc66c517eb003c0107f7e6d77bfee25dae974c897736b8b48394be1f2ba1d'); // Yahan apna callback secret dalo

define('USE_CONN_FILE', false); // true = conn.php use karo, false = niche DB credentials use karo
define('CONN_FILE', __DIR__ . '/../../conn.php'); // Agar conn.php kahin aur hai to path badlo

define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'rflrovxl_133lgameluckywebd');
define('DB_USER', 'rflrovxl_133lgameluckywebd');
define('DB_PASS', 'rflrovxl_133lgameluckywebd');
// ============================================

$conn = null;
if (USE_CONN_FILE && file_exists(CONN_FILE)) {
    include_once CONN_FILE;
}

if (!isset($conn) || !($conn instanceof mysqli) || $conn->connect_error) {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
}

$log_dir = __DIR__ . '/apilogs';
if (!file_exists($log_dir)) { mkdir($log_dir, 0777, true); }

function writeLog($msg) {
    global $log_dir;
    $date = date('Y-m-d');
    $time = date('H:i:s');
    file_put_contents($log_dir . "/log_$date.txt", "[$time] $msg" . PHP_EOL, FILE_APPEND);
}

function jsonExit($statusCode, $payload) {
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function getSignatureHeader() {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'x-signature') {
            return trim($value);
        }
    }
    return trim($_SERVER['HTTP_X_SIGNATURE'] ?? '');
}

writeLog("------------------------------------------------");
writeLog("Incoming Request: " . ($_SERVER['REQUEST_URI'] ?? 'CLI'));

if (!($conn instanceof mysqli) || $conn->connect_error) {
    writeLog("CRITICAL: DB Connection Failed - " . (($conn instanceof mysqli) ? $conn->connect_error : 'No connection object'));
    jsonExit(500, ['code' => 1, 'msg' => 'DB Connection Error']);
}

$input_json = file_get_contents('php://input');
$data = json_decode($input_json, true);
$signature = getSignatureHeader();
$calculated_sig = hash_hmac('sha256', $input_json, API_SECRET);

writeLog("Payload: " . $input_json);

if ($signature === '' || !hash_equals($calculated_sig, $signature)) {
    writeLog("ERROR: Signature Mismatch. Sent: $signature | Calc: $calculated_sig");
    jsonExit(401, ['code' => 401, 'msg' => 'Invalid Signature']);
}

if (!is_array($data)) {
    writeLog("ERROR: Invalid JSON");
    jsonExit(400, ['code' => 1, 'msg' => 'Invalid JSON']);
}

$platform_uid = trim((string)($data['platform_member_account'] ?? ''));
$uid = trim((string)($data['operator_member_id'] ?? ($data['member_account'] ?? '')));
$game_code = trim((string)($data['game_uid'] ?? ''));
$bet_amount = round((float)($data['bet_amount'] ?? 0), 2);
$win_amount = round((float)($data['win_amount'] ?? 0), 2);
$txn_id = trim((string)($data['serial_number'] ?? ''));
$round_id = trim((string)($data['game_round'] ?? ''));
$currency = trim((string)($data['currency_code'] ?? 'INR'));

if ($uid === '' && $platform_uid !== '') {
    $parts = explode('_', $platform_uid, 3);
    $uid = count($parts) === 3 ? $parts[2] : $platform_uid;
}

if ($uid === '' || $txn_id === '') {
    writeLog("ERROR: Missing member_account or serial_number");
    jsonExit(400, ['code' => 1, 'msg' => 'Missing required fields']);
}

writeLog("Player: $uid | PlatformMember: $platform_uid | Game: $game_code | Bet: $bet_amount | Win: $win_amount | TxnID: $txn_id");

$existing_balance = null;
$dup_stmt = $conn->prepare("SELECT balance_after FROM game_bet_logs WHERE serial_number = ? LIMIT 1");
if ($dup_stmt) {
    $dup_stmt->bind_param("s", $txn_id);
    $dup_stmt->execute();
    $dup_res = $dup_stmt->get_result();
    if ($dup_res && $dup_res->num_rows > 0) {
        $existing = $dup_res->fetch_assoc();
        $existing_balance = (float)($existing['balance_after'] ?? 0);
        writeLog("INFO: Duplicate transaction $txn_id already processed.");
        jsonExit(200, ['code' => 0, 'msg' => 'Success', 'balance' => $existing_balance]);
    }
    $dup_stmt->close();
}

$bal_stmt = $conn->prepare("SELECT motta FROM shonu_kaichila WHERE balakedara = ? LIMIT 1");
if (!$bal_stmt) {
    writeLog("CRITICAL: Balance prepare failed - " . $conn->error);
    jsonExit(500, ['code' => 1, 'msg' => 'Database Prepare Failed']);
}

$bal_stmt->bind_param("s", $uid);
$bal_stmt->execute();
$bal_res = $bal_stmt->get_result();
if (!$bal_res || $bal_res->num_rows === 0) {
    writeLog("ERROR: User $uid not found in shonu_kaichila");
    jsonExit(404, ['code' => 1, 'msg' => 'User not found']);
}

$user_row = $bal_res->fetch_assoc();
$current_bal = round((float)$user_row['motta'], 2);
$bal_stmt->close();

if ($bet_amount > 0 && $current_bal < $bet_amount) {
    writeLog("ERROR: Insufficient balance. Bal: $current_bal | Bet: $bet_amount");
    jsonExit(200, ['code' => 1, 'msg' => 'Insufficient balance', 'balance' => $current_bal]);
}

$conn->begin_transaction();

try {
    if ($bet_amount > 0) {
        $update_stmt = $conn->prepare("UPDATE shonu_kaichila SET motta = motta - ? + ? WHERE balakedara = ? AND motta >= ?");
        if (!$update_stmt) {
            throw new Exception('Balance update prepare failed: ' . $conn->error);
        }
        $update_stmt->bind_param("ddsd", $bet_amount, $win_amount, $uid, $bet_amount);
    } else {
        $update_stmt = $conn->prepare("UPDATE shonu_kaichila SET motta = motta + ? WHERE balakedara = ?");
        if (!$update_stmt) {
            throw new Exception('Win update prepare failed: ' . $conn->error);
        }
        $update_stmt->bind_param("ds", $win_amount, $uid);
    }

    $update_stmt->execute();
    if ($update_stmt->affected_rows <= 0) {
        throw new Exception('Balance update rejected');
    }
    $update_stmt->close();

    $new_bal_stmt = $conn->prepare("SELECT motta FROM shonu_kaichila WHERE balakedara = ? LIMIT 1");
    $new_bal_stmt->bind_param("s", $uid);
    $new_bal_stmt->execute();
    $new_bal_res = $new_bal_stmt->get_result();
    $new_bal_row = $new_bal_res->fetch_assoc();
    $new_balance = round((float)($new_bal_row['motta'] ?? 0), 2);
    $new_bal_stmt->close();

    $profit = round($win_amount - $bet_amount, 2);
    $log_stmt = $conn->prepare("INSERT INTO game_bet_logs (user_id, game_code, bet_amount, win_amount, profit_loss, balance_before, balance_after, serial_number, game_round, currency_code, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    if ($log_stmt) {
        $log_stmt->bind_param("ssdddddsss", $uid, $game_code, $bet_amount, $win_amount, $profit, $current_bal, $new_balance, $txn_id, $round_id, $currency);
        $log_stmt->execute();
        $log_stmt->close();
    } else {
        writeLog("WARN: game_bet_logs insert skipped - " . $conn->error);
    }

    $conn->commit();
    writeLog("SUCCESS: Updated User $uid | Old: $current_bal | New: $new_balance");
    jsonExit(200, ['code' => 0, 'msg' => 'Success', 'balance' => $new_balance]);
} catch (Throwable $e) {
    $conn->rollback();
    writeLog("CRITICAL: " . $e->getMessage());

    $retry_stmt = $conn->prepare("SELECT motta FROM shonu_kaichila WHERE balakedara = ? LIMIT 1");
    if ($retry_stmt) {
        $retry_stmt->bind_param("s", $uid);
        $retry_stmt->execute();
        $retry_res = $retry_stmt->get_result();
        $retry_row = $retry_res ? $retry_res->fetch_assoc() : null;
        $latest_balance = round((float)($retry_row['motta'] ?? 0), 2);
        $retry_stmt->close();

        if ($bet_amount > 0 && $latest_balance < $bet_amount) {
            jsonExit(200, ['code' => 1, 'msg' => 'Insufficient balance', 'balance' => $latest_balance]);
        }
    }

    jsonExit(500, ['code' => 1, 'msg' => 'Database Update Failed']);
}

/*
 * CREATE TABLE IF NOT EXISTS game_bet_logs (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   user_id VARCHAR(100) NOT NULL,
 *   game_code VARCHAR(100),
 *   bet_amount DECIMAL(15,2) DEFAULT 0.00,
 *   win_amount DECIMAL(15,2) DEFAULT 0.00,
 *   profit_loss DECIMAL(15,2) DEFAULT 0.00,
 *   balance_before DECIMAL(15,2) DEFAULT 0.00,
 *   balance_after DECIMAL(15,2) DEFAULT 0.00,
 *   serial_number VARCHAR(200) UNIQUE,
 *   game_round VARCHAR(200),
 *   currency_code VARCHAR(10) DEFAULT 'INR',
 *   created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 *   INDEX idx_user_id (user_id),
 *   INDEX idx_serial_number (serial_number),
 *   INDEX idx_game_round (game_round)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 */
?>