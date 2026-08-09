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
			$orderid = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['orderId']));
			$shonustr = '{"language":"' . $language . '","random":"' . $random . '"}';
			$shonusign = strtoupper(md5($shonustr));
			
			
			$query = "SELECT remarks, text_content, created_at FROM your_table WHERE deposit_order_no = '$orderid'";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
			
			$remarks = $row['remarks'];
            $text_content = $row['text_content'];
            $created_at = $row['created_at'];
			
			if ($shonusign) {
                 $res = [
              "data" => [
        [
            "commentContent" => $remarks,
            "attachmentName" => "",
            "attachmentPath" => "https://worktracking-imgs.oss-ap-southeast-1.aliyuncs.com/",
            "commentAt" => $created_at,
            "type" => 0
        ],
        [
            "commentContent" => $text_content,
            "attachmentName" => null,
            "attachmentPath" => null,
            "commentAt" => $created_at,
            "type" => 1
        ]
    ],
    "msgParameters" => null,
    "code" => 0,
    "msg" => "Succeed",
    "msgCode" => 0
];

                 
				$res['code'] = 0;
				$res['msg'] = 'Succeed';
				$res['msgCode'] = 0;
				http_response_code(200);
				echo json_encode($res);
			} else {
				$res['code'] = 13;
				$res['msg'] = 'Invalid signature';
				$res['msgCode'] = 13;
				http_response_code(403);
				echo json_encode($res);
			}
		} else {
			$res['code'] = 14;
			$res['msg'] = 'Missing parameters';
			$res['msgCode'] = 14;
			http_response_code(400);
			echo json_encode($res);
		}
	} else {
		http_response_code(405);
		echo json_encode($res);
	}
?>
