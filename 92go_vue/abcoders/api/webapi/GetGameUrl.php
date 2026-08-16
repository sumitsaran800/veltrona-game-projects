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

                            // Generate required variables for the API
                            $timestamp = round(microtime(true) * 1000); // Milliseconds timestamp
                            $agencyUid = "62bab62c3e4233e190ade02b7a41112e";
                            $aesKey = "2be93fdc095b37a65c37ac0e98e84619";
                            $serverUrl = "https://jsgame.live/game/v2";
                            $playerPrefix = "h82c22";

                            $memberAccount = "{$playerPrefix}{$userName}new24gamebdg";
                            $transferId = $memberAccount . '_' . bin2hex(random_bytes(3)); // Unique transfer ID

                            if (empty($serverUrl)) {
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

                            $initPayload = [
                                'agency_uid' => $agencyUid,
                                'member_account' => $memberAccount,
                                'timestamp' => $timestamp,
                                'credit_amount' => "0", // Set balance to 0
                                'currency_code' => "INR",
                                'language' => "en",
                                'platform' => "2",
                                'home_url' => $origin, // Using the origin header as referer
                                'transfer_id' => $transferId,
                            ];

                            // Encrypt the payload using AES256 (ECB mode)
                            $initEncryptedPayload = encryptAES256ECB(json_encode($initPayload), $aesKey);

                            // Prepare the request payload for the initial API request
                            $initRequestPayload = [
                                'agency_uid' => $agencyUid,
                                'timestamp' => $timestamp,
                                'payload' => $initEncryptedPayload,
                            ];

                            // Step 3: Send the initial request to the server
                            $ch = curl_init($serverUrl);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($initRequestPayload));
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Content-Type: application/json',
                            ]);

                            $initResponse = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            $curlError = curl_error($ch);
                            curl_close($ch);

                            // Handle the initial response
                            if ($httpCode === 200 && $initResponse !== false) {
                                $initResponseData = json_decode($initResponse, true);

                                // Extract after_amount to determine how much to deduct
                                if ($initResponseData['code'] === 0) {
                                    $initAdd = $initResponseData['payload']['after_amount']; // Amount to deduct

                                    // Step 4: Prepare the deduction payload
                                    $deductPayload = [
                                        'agency_uid' => $agencyUid,
                                        'member_account' => $memberAccount,
                                        'credit_amount' => "-" . $initAdd, // Deduct the current balance
                                        'currency_code' => "BRL",
                                        'language' => "en",
                                        'platform' => "2",
                                        'home_url' => $origin, // Using the origin header as referer
                                        'transfer_id' => $memberAccount . '_' . bin2hex(random_bytes(3)), // New transfer ID
                                        'timestamp' => round(microtime(true) * 1000), // New timestamp
                                    ];

                                    // Encrypt the deduction payload using AES-256-ECB
                                    $deductEncryptedPayload = encryptAES256ECB(json_encode($deductPayload), $aesKey);

                                    // Prepare the request payload for the deduction API request
                                    $deductRequestPayload = [
                                        'agency_uid' => $agencyUid,
                                        'timestamp' => $deductPayload['timestamp'],
                                        'payload' => $deductEncryptedPayload,
                                    ];

                                    // Step 5: Send the deduction request to the server
                                    $ch = curl_init($serverUrl);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($deductRequestPayload));
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                        'Content-Type: application/json',
                                    ]);

                                    $deductResponse = curl_exec($ch);
                                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                    $curlError = curl_error($ch);
                                    curl_close($ch);

                                    // Handle the deduction response
                                    if ($httpCode === 200 && $deductResponse !== false) {
                                        $deductResponseData = json_decode($deductResponse, true);
                                        if ($deductResponseData['code'] === 0) {
                                            // Deduction successful, launch the game

                                            // Step 6: Prepare the game launch payload
                                            $creditAmt = $userBalance + $initAdd; // Calculate new balance
                                            $gamePayload = [
                                                'agency_uid' => $agencyUid,
                                                'member_account' => $memberAccount,
                                                'game_uid' => $gameCode, // Game UID should be passed from the request
                                                'timestamp' => round(microtime(true) * 1000),
                                                'credit_amount' => (string) $creditAmt, // Convert to string for consistency
                                                'currency_code' => "BRL",
                                                'language' => "en",
                                                'platform' => "2",
                                                'home_url' => $origin,
                                                'transfer_id' => $memberAccount . '_' . bin2hex(random_bytes(3)),
                                            ];

                                            // Encrypt the game payload using AES-256-ECB
                                            $gameEncryptedPayload = encryptAES256ECB(json_encode($gamePayload), $aesKey);

                                            // Prepare the game request payload
                                            $gameRequestPayload = [
                                                'agency_uid' => $agencyUid,
                                                'timestamp' => round(microtime(true) * 1000),
                                                'payload' => $gameEncryptedPayload,
                                            ];

                                            // Step 7: Send the game launch request
                                            $ch = curl_init($serverUrl);
                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                            curl_setopt($ch, CURLOPT_POST, true);
                                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($gameRequestPayload));
                                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                                'Content-Type: application/json',
                                            ]);

                                            $gameResponse = curl_exec($ch);
                                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                            $curlError = curl_error($ch);
                                            curl_close($ch);

                                            // Handle the game launch response
                                            if ($httpCode === 200 && $gameResponse !== false) {
                                                $gameResponseData = json_decode($gameResponse, true);

                                                if ($gameResponseData['code'] === 0) {
                                                    // Game launched successfully, return the game URL
                                                    $gameLaunchUrl = $gameResponseData['payload']['game_launch_url'];

                                                    // Return the game launch URL to the user
                                                    $res['code'] = 0;
                                                    $res['msg'] = 'Game launched successfully';
                                                    $res['data'] = [
                                                        'url' => $gameLaunchUrl,
                                                    ];
                                                    
// Check if the user's balance is already 0
$checkBalanceQuery = "SELECT motta FROM shonu_kaichila WHERE balakedara = ?";
$stmt = $conn->prepare($checkBalanceQuery);
$stmt->bind_param("s", $userName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['motta'] != 0) {
        // Update the user's balance to 0 only if it's not already 0
        $updateBalanceQuery = "UPDATE shonu_kaichila SET motta = 0 WHERE balakedara = ?";
        $stmt = $conn->prepare($updateBalanceQuery);
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        echo "";
    } else {
        echo "";
    }
} else {
    echo "";
}
                                    http_response_code(200);
                                    echo json_encode($res);
                                                } else {
                                                    $res['code'] = $gameResponseData['code'];
                                                    $res['msg'] = 'Game launch error: ' . $gameResponseData['msg'];
                                                    http_response_code(400);
                                                    echo json_encode($res);
                                                }
                                            } else {
                                                $res['code'] = 9;
                                                $res['msg'] = 'Failed to connect to the game launch API';
                                                $res['error'] = $curlError ?: 'Unknown error';
                                                http_response_code(500);
                                                echo json_encode($res);
                                            }
                                        } else {
                                            $res['code'] = $deductResponseData['code'];
                                            $res['msg'] = 'Deduction API error: ' . $deductResponseData['msg'];
                                            http_response_code(400);
                                            echo json_encode($res);
                                        }
                                    } else {
                                        $res['code'] = 9;
                                        $res['msg'] = 'Failed to deduct money from the server';
                                        $res['error'] = $curlError ?: 'Unknown error';
                                        http_response_code(500);
                                        echo json_encode($res);
                                    }
                                } else {
                                    $res['code'] = 9;
                                    $res['msg'] = 'Failed to process the deduction';
                                    http_response_code(400);
                                    echo json_encode($res);
                                }
                            } else {
                                $res['code'] = 9;
                                $res['msg'] = 'Failed to process the initial request';
                                http_response_code(400);
                                echo json_encode($res);
                            }
                        } else {
                            $res['code'] = 8;
                            $res['msg'] = 'Balance not found for user';
                            http_response_code(400);
                            echo json_encode($res);
                        }
                    } else {
                        $res['code'] = 8;
                        $res['msg'] = 'User not found';
                        http_response_code(400);
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
