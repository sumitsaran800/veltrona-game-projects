<?php 
	if(file_exists(__DIR__ . "/../../../conn.php")){ include_once __DIR__ . "/../../../conn.php"; } elseif(file_exists(__DIR__ . "/../../conn.php")) { include_once __DIR__ . "/../../conn.php"; }
	if(file_exists(__DIR__ . "/../../../functions2.php")){ include_once __DIR__ . "/../../../functions2.php"; } elseif(file_exists(__DIR__ . "/../../functions2.php")) { include_once __DIR__ . "/../../functions2.php"; }
	
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
            $vendorCode = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['vendorCode']));
			$gameCode = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['gameCode']));
			$phonetype = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['phonetype']));
			$shonustr = '{"gameCode":"'.$gameCode.'","language":'.$language.',"phonetype":'.$phonetype.',"random":"'.$random.'","vendorCode":'.$vendorCode.'}';
			$shonusign = strtoupper(md5($shonustr));

			if ($shonusign != $signature) {
				$bearer = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
				$author = $bearer[1];				
				$is_jwt_valid = is_jwt_valid($author);
				$data_auth = json_decode($is_jwt_valid, 1);
				if ($data_auth['status'] === 'Success') {
			
					$sesquery = ($vendorCode == 18) 
						? "SELECT id, akshinak FROM shonu_subjects WHERE akshinak = '$author'"
						: "SELECT mobile, akshinak FROM shonu_subjects WHERE akshinak = '$author'";
					
					$sesresult = $conn->query($sesquery);
					$sesnum = mysqli_num_rows($sesresult);
					$row = $sesresult->fetch_assoc();						
						if ($sesnum == 1) {
                    // Fetch user details from the database using the token
                    $userQuery = "SELECT id FROM shonu_subjects WHERE akshinak = '$author'";
                    $userResult = $conn->query($userQuery);
                    // Encrypt payload using AES256 (ECB mode)
                    function encryptAES256ECB($data, $key)
                    {
                        // Ensure the key length is 32 bytes (AES-256 requires 256-bit keys, i.e., 32 bytes)
                        $key = str_pad($key, 32, "\0"); // Pad key if it is less than 32 bytes

                        $encryptedData = openssl_encrypt($data, 'AES-256-ECB', $key, OPENSSL_RAW_DATA);
                        return base64_encode($encryptedData); // Return base64-encoded encrypted data
                    }
                    if ($userResult && $userResult->num_rows > 0) {
                        $userRow = $userResult->fetch_assoc();
                        $userName = $userRow['id']; // 'id' corresponds to 'userName'

                        // Fetch user balance from shonu_kaichila using the userName
                        $balanceQuery = "SELECT motta FROM shonu_kaichila WHERE balakedara = '$userName'";
                        $balanceResult = $conn->query($balanceQuery);

                        if ($balanceResult && $balanceResult->num_rows > 0) {
                            $balanceRow = $balanceResult->fetch_assoc();
                            $userBalance = $balanceRow['motta']; // 'motta' is the balance column

                            // Generate required variables for HUIDU v1 API
                            $agencyUid = "515d62d4b6910681bd037f3be9f4d356";
                            $aesKey = "ddeb387628093a52503618f257cb071f";
                            $serverUrl = "https://play.huiduapi.org/game/v1";
                            $callbackUrl = "https://huidu.forum/api/huidu/callback";
                            $memberPrefix = "h0b243mm";
                            $forwardedIp = "145.79.12.201";

                            $memberAccount = $memberPrefix . $userName;
                            $timestampStr = (string)(int)(microtime(true) * 1000);

                            // Inner payload for HUIDU v1
                            $innerPayload = [
                                'agency_uid'     => $agencyUid,
                                'callback_url'   => $callbackUrl,
                                'member_account' => $memberAccount,
                                'credit_amount'  => (string) $userBalance,
                                'game_uid'       => (string) $gameCode,
                                'currency_code'  => 'INR',
                                'language'       => 'en',
                                'timestamp'      => $timestampStr,
                            ];

                            // Encrypt using AES-256-ECB
                            $encryptedPayload = base64_encode(openssl_encrypt(
                                json_encode($innerPayload, JSON_UNESCAPED_SLASHES),
                                'AES-256-ECB',
                                $aesKey,
                                OPENSSL_RAW_DATA
                            ));

                            // Request body
                            $requestPayload = [
                                'agency_uid' => $agencyUid,
                                'timestamp'  => $timestampStr,
                                'payload'    => $encryptedPayload,
                            ];

                            // Call HUIDU v1 API
                            $ch = curl_init($serverUrl);
                            curl_setopt_array($ch, [
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POST => true,
                                CURLOPT_POSTFIELDS => json_encode($requestPayload),
                                CURLOPT_HTTPHEADER => [
                                    'Content-Type: application/json',
                                    "X-Forwarded-For: $forwardedIp"
                                ],
                                CURLOPT_TIMEOUT => 15,
                                CURLOPT_SSL_VERIFYPEER => false,
                            ]);

                            $response = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);

                            if ($httpCode === 200 && $response !== false) {
                                $responseData = json_decode($response, true);
                                $gameUrl = '';

                                if (isset($responseData['payload']['game_launch_url']) && !empty($responseData['payload']['game_launch_url'])) {
                                    $gameUrl = $responseData['payload']['game_launch_url'];
                                } elseif (isset($responseData['data']['game_launch_url']) && !empty($responseData['data']['game_launch_url'])) {
                                    $gameUrl = $responseData['data']['game_launch_url'];
                                } elseif (isset($responseData['game_launch_url']) && !empty($responseData['game_launch_url'])) {
                                    $gameUrl = $responseData['game_launch_url'];
                                }

                                if (!empty($gameUrl)) {
                                    $res['code'] = 0;
                                    $res['msg'] = 'Game launched successfully';
                                    $res['data'] = [
                                        'url' => $gameUrl,
                                        'gameUrl' => $gameUrl,
                                        'returnUrl' => $origin . '/',
                                        'isOpenWindow' => true
                                    ];
                                } else {
                                    $res['code'] = 0;
                                    $res['msg'] = 'Game launched successfully';
                                    $res['data'] = [
                                        'url' => $origin . '/#/home',
                                        'gameUrl' => $origin . '/#/home',
                                        'returnUrl' => $origin . '/',
                                        'isOpenWindow' => true
                                    ];
                                }
                            } else {
                                $res['code'] = 0;
                                $res['msg'] = 'Game launched successfully';
                                $res['data'] = [
                                    'url' => $origin . '/#/home',
                                    'gameUrl' => $origin . '/#/home',
                                    'returnUrl' => $origin . '/',
                                    'isOpenWindow' => true
                                ];
                            }
                            http_response_code(200);
                            echo json_encode($res);
                            exit;
                        } else {
                            $res['code'] = 0;
                            $res['msg'] = 'Game launched successfully';
                            $res['data'] = [
                                'url' => $origin . '/#/home',
                                'gameUrl' => $origin . '/#/home',
                                'returnUrl' => $origin . '/',
                                'isOpenWindow' => true
                            ];
                            http_response_code(200);
                            echo json_encode($res);
                            exit;
                        }
                    } else {
                        $res['code'] = 0;
                        $res['msg'] = 'Game launched successfully';
                        $res['data'] = [
                            'url' => $origin . '/#/home',
                            'gameUrl' => $origin . '/#/home',
                            'returnUrl' => $origin . '/',
                            'isOpenWindow' => true
                        ];
                        http_response_code(200);
                        echo json_encode($res);
                        exit;
                    }
                } else {
                    $res['code'] = 4;
                    $res['msg'] = 'No operation permission';
                    $res['msgCode'] = 2;
                    http_response_code(401);
                    echo json_encode($res);
                }
            } else {
                $res['code'] = 4;
                $res['msg'] = 'No operation permission';
                $res['msgCode'] = 2;
                http_response_code(401);
                echo json_encode($res);
            }
        } else {
            $res['code'] = 5;
            $res['msg'] = 'Wrong signature';
            $res['msgCode'] = 3;
            http_response_code(200);
            echo json_encode($res);
        }
    } else {
        $res['code'] = 7;
        $res['msg'] = 'Param is Invalid';
        $res['msgCode'] = 6;
        http_response_code(200);
        echo json_encode($res);
    }
} else {
    http_response_code(405);
    echo json_encode($res);
}
