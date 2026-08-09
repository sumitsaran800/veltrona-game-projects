<?php 
include "../../conn.php";
include "../../functions2.php";

header('Content-Type: application/json; charset=utf-8');
header('Strict-Transport-Security: max-age=31536000');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Allow-Credentials: true');
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
header('Access-Control-Allow-Origin: ' . $origin);
header('vary: Origin');

date_default_timezone_set("Asia/Kolkata");
$shnunc = date("Y-m-d H:i:s");
$res = [
    'code' => 11,
    'msg' => 'Method not allowed',
    'msgCode' => 12,
    'serviceNowTime' => $shnunc,
];

$shonubody = file_get_contents("php://input");
$shonupost = json_decode($shonubody, true);

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    if (isset($shonupost['language']) && isset($shonupost['random']) && isset($shonupost['signature']) && isset($shonupost['timestamp'])) {
        $language = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['language']));
        $random = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['random']));
        $signature = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['signature']));
        $shonustr = '{"language":"' . $language . '","random":"' . $random . '"}';
        $shonusign = strtoupper(md5($shonustr));

        if ($shonusign) { // Compare the generated signature with the received one
            $url = 'https://starapp.xyz/api/kids'; // Replace with your desired URL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch) || $httpCode != 200) {
                $res['code'] = 15;
                $res['msg'] = 'Request failed';
                $res['msgCode'] = 15;
                $res['errorDetails'] = curl_error($ch);
                http_response_code(500);
            } else {
                $apiResponse = json_decode($response, true);

                // Check if the API response contains 'data' and its required fields
                if (isset($apiResponse['data']['accessKeyId'], 
                          $apiResponse['data']['accessKeySecret'], 
                          $apiResponse['data']['securityToken'], 
                          $apiResponse['data']['expiration'])) {

                    $res['code'] = 0;
                    $res['msg'] = 'Succeed';
                    $res['msgCode'] = 0;
                    $res['data'] = [
                        'accessKeyId' => $apiResponse['data']['accessKeyId'],
                        'accessKeySecret' => $apiResponse['data']['accessKeySecret'],
                        'securityToken' => $apiResponse['data']['securityToken'],
                        'expiration' => $apiResponse['data']['expiration'],
                        'bucket' => 'finaloss', // Replace with actual bucket if needed
                        'region' => $apiResponse['data']['region'] ?? 'ap-southeast-1', // Use region from API or fallback
                        'dir' => $apiResponse['data']['dir'] ?? '/Prod/User/' // Use directory from API or fallback
                    ];
                    http_response_code(200);
                } else {
                    $res['code'] = 16;
                    $res['msg'] = 'Invalid API response format';
                    $res['msgCode'] = 16;
                    $res['response'] = $apiResponse;
                    http_response_code(502);
                }
            }
            curl_close($ch);
        } else {
            $res['code'] = 13;
            $res['msg'] = 'Invalid signature';
            $res['msgCode'] = 13;
            http_response_code(403);
        }
    } else {
        $res['code'] = 14;
        $res['msg'] = 'Missing parameters';
        $res['msgCode'] = 14;
        http_response_code(400);
    }
} else {
    http_response_code(405);
}

echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
