<?php
    // Set response headers for JSON format
    header('Content-Type: application/json; charset=utf-8');
    header('Strict-Transport-Security: max-age=31536000');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Access-Control-Allow-Credentials: true');
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    header('Access-Control-Allow-Origin: ' . $origin);
    header('vary: Origin');
    
    // Set the current time zone
    date_default_timezone_set("Asia/Kolkata");
    $shnunc = date("Y-m-d H:i:s");
    
    // Define the response structure
    $res = [
        'code' => 0,
        'msg' => 'Succeed',
        'msgCode' => 0,
        'serviceNowTime' => $shnunc,
        'data' => [
            'state' => 1,
            'customerServiceLink' => null,
            'isEnableServiceLink' => 0,
            'timeZoneIdentity' => 'Asia/Kolkata',
            'lotteryH5Host' => 'EDC100',
            'isEnableFaq' => false
        ],
        'msgParameters' => null
    ];
    
    // Return the response in JSON format
    http_response_code(200); // OK status
    echo json_encode($res);
?>
