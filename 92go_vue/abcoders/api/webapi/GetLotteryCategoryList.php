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

$data = [
    [
        'id' => 1,
        'categoryCode' => 'Win Go',
        'categoryName' => 'WinGo彩票',
        'state' => 1,
        'sort' => 0,
        'categoryImg' => '/apiimages/BDGWin/lotterycategory/lotterycategory_20240321194458iceq.png',
        'wingoAmount' => null,
        'k3Amount' => null,
        'fiveDAmount' => null,
        'trxWingoAmount' => null
    ],
    [
        'id' => 2,
        'categoryCode' => 'K3',
        'categoryName' => 'K3彩票',
        'state' => 1,
        'sort' => 0,
        'categoryImg' => '/apiimages/BDGWin/lotterycategory/lotterycategory_20240321194451en5o.png',
        'wingoAmount' => null,
        'k3Amount' => null,
        'fiveDAmount' => null,
        'trxWingoAmount' => null
    ],
    [
        'id' => 3,
        'categoryCode' => '5D',
        'categoryName' => '5D彩票',
        'state' => 1,
        'sort' => 0,
        'categoryImg' => '/apiimages/BDGWin/lotterycategory/lotterycategory_20240321194510h9i1.png',
        'wingoAmount' => null,
        'k3Amount' => null,
        'fiveDAmount' => null,
        'trxWingoAmount' => null
    ],
    [
        'id' => 4,
        'categoryCode' => 'Trx Win Go',
        'categoryName' => 'TrxWinGo彩票',
        'state' => 1,
        'sort' => 0,
        'categoryImg' => '/apiimages/BDGWin/lotterycategory/lotterycategory_20250210101104jtse.png',
        'wingoAmount' => null,
        'k3Amount' => null,
        'fiveDAmount' => null,
        'trxWingoAmount' => null
    ]
];

$res = [
    'data' => $data,
    'code' => 0,
    'msg' => 'Succeed',
    'msgCode' => 0,
    'serviceNowTime' => $shnunc,
];

http_response_code(200);
echo json_encode($res);
exit;
?>