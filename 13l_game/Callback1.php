<?php
/**
 * Unified Seamless Wallet Callback Endpoint (Supabase Gaming API)
 * URL: https://yourwebsite.com/Callback.php
 * Handles: bet, settle, rollback
 */

define('NO_AUTH_REQUIRED', true);
require_once __DIR__ . '/api/_core/config.php';
require_once __DIR__ . '/api/_core/bootstrap.php';

// Log incoming raw request for troubleshooting
$raw = file_get_contents('php://input');
$req = json_decode($raw ?: '', true) ?? [];
error_log('[Unified Callback] Received raw request: ' . $raw);

header('Content-Type: application/json');

// Security Check: Verify API Secret from headers, query string, or POST request payload
$secret = defined('UNIFIED_API_SECRET') ? UNIFIED_API_SECRET : '';
$headers = function_exists('getallheaders') ? getallheaders() : [];
$passedSecret = $headers['x-api-secret'] 
    ?? $headers['X-API-Secret'] 
    ?? $headers['Authorization'] 
    ?? $_GET['secret'] 
    ?? $_GET['api_secret'] 
    ?? $req['api_secret'] 
    ?? $req['secret'] 
    ?? '';

if (stripos($passedSecret, 'Bearer ') === 0) {
    $passedSecret = trim(substr($passedSecret, 7));
}

if (!empty($secret) && !empty($passedSecret) && $passedSecret !== $secret) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid API Secret verification failed']);
    exit;
}

$conn = db();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Auto-initialize DB schema if game_bet_logs is missing (0-manual work required!)
@$conn->query("CREATE TABLE IF NOT EXISTS game_bet_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(20) NOT NULL,
    member_account VARCHAR(100) NOT NULL,
    amount DECIMAL(18, 4) NOT NULL,
    game_uid VARCHAR(100) NOT NULL,
    round_id VARCHAR(100) NOT NULL,
    serial_number VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$action       = (string)($req['action'] ?? '');
$memberAccount = (string)($req['member_account'] ?? '');
$amount       = (float)($req['amount'] ?? 0);
$gameUid      = (string)($req['game_uid'] ?? '');
$roundId      = (string)($req['round_id'] ?? '');
$serialNumber = (string)($req['serial_number'] ?? '');

if (empty($action) || empty($memberAccount) || empty($serialNumber)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields (action, member_account, serial_number)']);
    exit;
}

$userId = (int)ltrim($memberAccount, 'P');
$user = current_user_by_id($userId);
if (!$user) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Player not found']);
    exit;
}

// Idempotency: Verify if this transaction has already been logged/processed
$stmt = $conn->prepare("SELECT id FROM game_bet_logs WHERE serial_number = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param('s', $serialNumber);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    if ($existing) {
        // Already processed successfully, return success with current balance
        echo json_encode([
            'status'  => 'success',
            'balance' => round((float)$user['balance'], 4)
        ]);
        exit;
    }
}

// Perform balance update using a secure SQL transaction block
$newBalance = (float)$user['balance'];
$conn->begin_transaction();

try {
    if ($action === 'bet') {
        if ($newBalance < $amount) {
            $conn->rollback();
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Insufficient balance']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->bind_param('di', $amount, $userId);
        $stmt->execute();
        $newBalance -= $amount;

    } elseif ($action === 'settle' || $action === 'win') {
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param('di', $amount, $userId);
        $stmt->execute();
        $newBalance += $amount;

    } elseif ($action === 'rollback') {
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param('di', $amount, $userId);
        $stmt->execute();
        $newBalance += $amount;

    } else {
        throw new Exception("Unsupported action received: " . $action);
    }

    // Write to game_bet_logs
    $stmt = $conn->prepare("INSERT INTO game_bet_logs (action, member_account, amount, game_uid, round_id, serial_number) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssdsss', $action, $memberAccount, $amount, $gameUid, $roundId, $serialNumber);
    $stmt->execute();

    // Write to third_party_transactions log so it is fully integrated with administrative dashboard
    tp_log_transaction($userId, 'UNIFIED', $action, $gameUid, ($action === 'bet' ? -$amount : $amount), $serialNumber, $raw);

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    error_log('[Unified Callback Error] Exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Internal transactional exception occurred']);
    exit;
}

// Respond with the newly updated wallet balance
echo json_encode([
    'status'  => 'success',
    'balance' => round($newBalance, 4)
]);
exit;
