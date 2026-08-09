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
		if (isset($shonupost['language']) && isset($shonupost['pageNo']) && isset($shonupost['pageSize']) && isset($shonupost['random']) && isset($shonupost['signature']) && isset($shonupost['timestamp'])) {
			$language = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['language']));
			$pageNo = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['pageNo']));
			$pageSize = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['pageSize']));
			$random = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['random']));
			$signature = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['signature']));
			$shonustr = '{"language":'.$language.',"pageNo":'.$pageNo.',"pageSize":'.$pageSize.',"random":"'.$random.'"}';
			$shonusign = strtoupper(md5($shonustr));
			if($shonusign == $signature){
				$bearer = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
				$author = $bearer[1];				
				$is_jwt_valid = is_jwt_valid($author);
				$data_auth = json_decode($is_jwt_valid, 1);
				if($data_auth['status'] === 'Success') {
					$sesquery = "SELECT akshinak
					  FROM shonu_subjects
					  WHERE akshinak = '$author'";
					$sesresult=$conn->query($sesquery);
					$sesnum = mysqli_num_rows($sesresult);
					if($sesnum == 1){
$data["list"][0]["bannerTitle"] = "First Deposit Bonus";
$data["list"][0]["bannerID"] = 71;
$data["list"][0]["bannerUrl"] = "https://i.postimg.cc/nLn8WfmF/6089092143789492301-121.jpg";
$data["list"][0]["jumpType"] = 2;
$data["list"][0]["contents"] = "/activity/FirstRecharge";

$data["list"][1]["bannerTitle"] = "Invitation Bonus";
$data["list"][1]["bannerID"] = 62;
$data["list"][1]["bannerUrl"] = "https://i.postimg.cc/d10bqRng/6089092143789492302-121.jpg";
$data["list"][1]["jumpType"] = 2;
$data["list"][1]["contents"] = "/main/InvitationBonus";



$data["list"][2]["bannerTitle"] = "Lucky Spin To Win Iphone 16 Pro Max";
$data["list"][2]["bannerID"] = 59;
$data["list"][2]["bannerUrl"] = "https://i.postimg.cc/VkGTK0zR/6089092143789492303-121.jpg";
$data["list"][2]["jumpType"] = 2;
$data["list"][2]["contents"] = "/activity/Turntable";

$data["list"][3]["bannerTitle"] = "Daily Bonus Until 1 CRORE";
$data["list"][3]["bannerID"] = 69;
$data["list"][3]["bannerUrl"] = "https://i.postimg.cc/Gp4fV321/6089153888239342059-121.jpg";
$data["list"][3]["jumpType"] = 1;
$data["list"][3]["contents"] = "";

$data["list"][4]["bannerTitle"] = "Monthly VIP Bonus";
$data["list"][4]["bannerID"] = 46;
$data["list"][4]["bannerUrl"] = "https://i.postimg.cc/tT0Bchwh/6089153888239342060-121.jpg";
$data["list"][4]["jumpType"] = 2;
$data["list"][4]["contents"] = "/vip";


						
						$data['pageNo'] = $pageNo;
						$data['totalPage'] = 1;
						$data['totalCount'] = 20;
						
						$res['data'] = $data;
						$res['code'] = 0;
						$res['msg'] = 'Succeed';
						$res['msgCode'] = 0;
						http_response_code(200);
						echo json_encode($res);			
					}
				}
			}
			else{
				$res['code'] = 5;
				$res['msg'] = 'Wrong signature';
				$res['msgCode'] = 3;
				http_response_code(200);
				echo json_encode($res);
			}
		}
		else{
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
?>