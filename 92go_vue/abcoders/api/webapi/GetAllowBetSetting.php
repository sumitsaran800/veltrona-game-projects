<?php 
if (file_exists(__DIR__ . "/../../../conn.php")) {
    include_once __DIR__ . "/../../../conn.php";
} elseif (file_exists(__DIR__ . "/../../conn.php")) {
    include_once __DIR__ . "/../../conn.php";
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, token');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

date_default_timezone_set("Asia/Kolkata");
$shnunc = date("Y-m-d H:i:s");

$res = [
    'code' => 0,
    'msg' => 'Succeed',
    'msgCode' => 0,
    'serviceNowTime' => $shnunc,
    'data' => [
        'canDirectToGame' => true,
        'userRechargeTimes' => 1,
        'allowNoRechargeGame' => "1",
        'userRechargeAmount' => 1000,
        'lowestRechargeAmountToGame' => "0"
    ]
];

http_response_code(200);
echo json_encode($res);
exit;
?>
