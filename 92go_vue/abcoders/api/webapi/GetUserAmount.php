<?php 
	if(file_exists(__DIR__ . "/../../../conn.php")){ include_once __DIR__ . "/../../../conn.php"; } elseif(file_exists(__DIR__ . "/../../conn.php")) { include_once __DIR__ . "/../../conn.php"; }
	if(file_exists(__DIR__ . "/../../../functions2.php")){ include_once __DIR__ . "/../../../functions2.php"; } elseif(file_exists(__DIR__ . "/../../functions2.php")) { include_once __DIR__ . "/../../functions2.php"; }
	
	header('Content-Type: application/json; charset=utf-8');
	header('Strict-Transport-Security: max-age=31536000');
	header('Access-Control-Allow-Headers: ar-origin, Origin, X-Requested-With, Content-Type, Accept, Authorization');
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
			$shonustr = '{"language":' . $language . ',"random":"' . $random . '"}';
			$shonusign = strtoupper(md5($shonustr));
			if ($shonusign == $signature) {
				$bearer = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
				$author = $bearer[1];
				$is_jwt_valid = is_jwt_valid($author);
				$data_auth = json_decode($is_jwt_valid, 1);
				if ($data_auth['status'] === 'Success') {
					$sesquery = "SELECT akshinak FROM shonu_subjects WHERE akshinak = '$author'";
					$sesresult = $conn->query($sesquery);
					$sesnum = mysqli_num_rows($sesresult);
					if ($sesnum == 1) {
						$userQuery = "SELECT id FROM shonu_subjects WHERE akshinak = '$author'";
						$userResult = $conn->query($userQuery);
						if ($userResult && $userResult->num_rows > 0) {
							$userRow = $userResult->fetch_assoc();
							$userName = $userRow['id'];
	
							$balanceQuery = "SELECT motta, wll_jili FROM shonu_kaichila WHERE balakedara = '$userName'";
							$balanceResult = $conn->query($balanceQuery);
	
							if ($balanceResult && $balanceResult->num_rows > 0) {
								$balanceRow = $balanceResult->fetch_assoc();
								$userBalance = (float) $balanceRow['motta'];
								$wllJili = (float) $balanceRow['wll_jili'];
	
								$totalThirdParty = $wllJili;
								$amToShow = $totalThirdParty;
								$updatedAmount = $userBalance + $totalThirdParty;
								$updateBalanceQuery = "
									UPDATE shonu_kaichila 
									SET motta = motta + $totalThirdParty,
										wll_jili = 0
									WHERE balakedara = '$userName'
								";
								$updateResult = $conn->query($updateBalanceQuery);
	
								if ($amToShow > 0) {
									$order_num = date('Yz') . str_pad(mt_rand(0, 9999999999999999), 16, '0', STR_PAD_LEFT);
									$res_insertQuery = "
										INSERT INTO xd_wallet_transfer_records (user_id, amount, order_num, type) 
										VALUES ('$userName', $amToShow, '$order_num', 'out')
									";
									$conn->query($res_insertQuery);
								}
	
								if ($updateResult) {
									$res['data'] = [
										'amount' => $updatedAmount,
										'uRate' => 94.0,
										'uGold' => 0.00,
									];
									$res['code'] = 0;
									$res['msg'] = 'Recovery of the balance is begin';
									http_response_code(200);
									echo json_encode($res);
								} else {
									$res['code'] = 8;
									$res['msg'] = 'Failed to update balance';
									http_response_code(500);
									echo json_encode($res);
								}
							} else {
								$res['code'] = 8;
								$res['msg'] = 'Balance not found for user';
								http_response_code(400);
								echo json_encode($res);
							}
						} else {
							$res['code'] = 4;
							$res['msg'] = 'No operation permission';
							http_response_code(401);
							echo json_encode($res);
						}
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
	