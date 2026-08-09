<?php
/**
 * GetGameUrl.php - Game Launcher (Seamless Wallet)
 * 
 * Ye file aapke website par rahegi.
 * Player jab game khelna chahega to ye file call hogi.
 * JWT se user authenticate hoke, database se balance leke,
 * API call karke game URL return karegi.
 * 
 * SETUP:
 * 1. conn.php aur functions2.php ka path sahi karein
 * 2. API_KEY neeche daalein (already pre-filled hai)
 * 3. EXIT_URL apni website ka URL rakhein
 * 4. Ye file apne server par upload karein
 */

// ============================================
// CONFIGURATION
// ============================================
define('GAME_LAUNCHER_ACCESS', true);

// Apne conn.php aur functions2.php ka path sahi karein
// Agar ye file /application/api/webapi/ me hai to:
include_once "../../conn.php";
include_once "../../functions2.php";

define('API_URL', 'https://qmikiecjseufxefwwoab.supabase.co/functions/v1/game-api');
define('API_KEY', '6b2b20914529ca638a0587b2197356fb2c1a1a2b84677e11fa030355a47bf2e8');
define('LANDING_URL', 'https://achagaming.shop/launch-error');  // Player error page (low GGR balance)
define('EXIT_URL', 'https://yourwebsite.com/');  // Apni website ka URL
// ============================================

header('Content-Type: application/json; charset=utf-8');
header('Strict-Transport-Security: max-age=31536000');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, X-API-Key');
header('Access-Control-Allow-Credentials: true');
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
header('Access-Control-Allow-Origin: ' . $origin);
header('vary: Origin');

date_default_timezone_set("Asia/Kolkata");
$shnunc = date("Y-m-d H:i:s");

// Default Response
$res = [
    'code' => 11,
    'msg' => 'Method not allowed',
    'msgCode' => 12,
    'serviceNowTime' => $shnunc,
];

$shonubody = file_get_contents("php://input");
$shonupost = json_decode($shonubody, true);

// 1. CHECK PARAMETERS
if (isset($shonupost['gameCode'])) {
    
    $gameCode = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['gameCode']));

    // 2. JWT AUTHENTICATION (Security)
    $bearer = explode(" ", $_SERVER['HTTP_AUTHORIZATION'] ?? '');
    $author = $bearer[1] ?? '';                
    $is_jwt_valid = is_jwt_valid($author);
    $data_auth = json_decode($is_jwt_valid, 1);

    if ($data_auth['status'] === 'Success') {
        
        $user_id = $data_auth['payload']['id'];
        
        // 3. GET USER & BALANCE
        $sesquery = "SELECT mobile, akshinak FROM shonu_subjects WHERE id = '$user_id'";
        $sesresult = $conn->query($sesquery);
        
        if (mysqli_num_rows($sesresult) == 1) {
            
            // Fetch Balance from 'motta'
            $stmtMain = $conn->prepare("SELECT motta FROM shonu_kaichila WHERE balakedara = ?");
            $stmtMain->bind_param("s", $user_id);
            $stmtMain->execute();
            $resultMain = $stmtMain->get_result();
            
            $walletBalance = 0.00;
            if ($resultMain && $resultMain->num_rows > 0) {
                $rowMain = $resultMain->fetch_assoc();
                $walletBalance = floatval($rowMain['motta']);
            }

            // 4. PREPARE PAYLOAD - Hamare API ke format me
            $api_payload = [
                "action"         => "game_launch",
                "api_key"        => API_KEY,
                "member_id"      => (string)$user_id,
                "game_uid"       => $gameCode,
                "credit_amount"  => (string)$walletBalance,  // Player ka balance HUIDU ko bhejo
                "currency_code"  => "INR", // Optional: agar game dusri currency demand kare to backend auto-resolve kar lega
                "return_url"     => EXIT_URL,
                "platform"       => "desktop",
                "language"       => "en",
            ];

            // 5. CALL API
            $ch = curl_init(API_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $api_response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            // 6. HANDLE RESPONSE
            if ($curl_error) {
                $res['code'] = 8;
                $res['msg'] = 'Provider Connection Error';
                $res['msgCode'] = 8;
            } else {
                $api_result = json_decode($api_response, true);
                
                // Check multiple response paths for game URL
                $game_url = '';
                if (isset($api_result['data']['payload']['game_launch_url'])) {
                    $game_url = $api_result['data']['payload']['game_launch_url'];
                } elseif (isset($api_result['data']['game_launch_url'])) {
                    $game_url = $api_result['data']['game_launch_url'];
                } elseif (isset($api_result['data']['url'])) {
                    $game_url = $api_result['data']['url'];
                } elseif (isset($api_result['payload']['game_launch_url'])) {
                    $game_url = $api_result['payload']['game_launch_url'];
                }

                if (!empty($game_url)) {
                    // SUCCESS - Return game URL in data.url format
                    // Frontend will read data.url and redirect player to game
                    $res['code'] = 0;
                    $res['msg'] = 'Success';
                    $res['msgCode'] = 0;
                    $res['data'] = [
                        'url' => $game_url,
                        'returnType' => '1'
                    ];
                } elseif (isset($api_result['code']) && $api_result['code'] == 0) {
                    $res['code'] = 0;
                    $res['msg'] = 'Success';
                    $res['data'] = $api_result['data'] ?? $api_result;
                } elseif (isset($api_result['code']) && $api_result['code'] == 10025) {
                    // LOW GGR BALANCE - Operator ka GGR balance kam hai.
                    // Game launch success jaisa hi response do: data.url me landing page.
                    // Operator app already game URL ko isi format se open karta hai.
                    $d = $api_result['details'] ?? [];
                    $required  = isset($d['required_ggr']) ? $d['required_ggr'] : 0;
                    $current   = isset($d['current_balance']) ? $d['current_balance'] : 0;
                    $shortfall = isset($d['shortfall']) ? $d['shortfall'] : 0;
                    $pct       = isset($d['min_ggr_percent']) ? $d['min_ggr_percent'] : 0;
                    $cur       = isset($d['currency']) ? $d['currency'] : 'INR';

                    $landing = LANDING_URL . '?' . http_build_query([
                        'required' => round($required, 2),
                        'available'=> round($current, 2),
                        'shortfall'=> round($shortfall, 2),
                        'pct'      => $pct,
                        'currency' => $cur,
                    ]);

                    $res['code'] = 0;
                    $res['msg'] = 'Success';
                    $res['msgCode'] = 0;
                    $res['low_ggr'] = true;
                    $res['data'] = [
                        'url' => $landing,
                        'returnType' => '1'
                    ];
                    $res['details'] = $api_result['details'] ?? [];
                } else {
                    $res['code'] = 8;
                    $res['msg'] = $api_result['msg'] ?? 'Game Launch Failed';
                    $res['msgCode'] = 8;
                }
            }

        } else {
            $res['code'] = 4;
            $res['msg'] = 'User not found';
            $res['msgCode'] = 2;
            http_response_code(401);
        }
    } else {
        $res['code'] = 4;
        $res['msg'] = 'Permission Denied';
        $res['msgCode'] = 2;
        http_response_code(401);
    }
} else {
    $res['code'] = 7;
    $res['msg'] = 'Invalid Parameters';
    $res['msgCode'] = 6;
}

http_response_code(200);
echo json_encode($res);
?>