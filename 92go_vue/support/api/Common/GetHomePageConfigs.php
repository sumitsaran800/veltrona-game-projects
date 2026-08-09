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
		'code' => 0,
		'msg' => 'Succeed',
		'msgCode' => 0,
		'serviceNowTime' => $shnunc,
		'data' => [
			'bannerUrl' => '',
			'kindTipsText' => "Please select the relevant query and submit it for review. After successful submission, the customer service specialist will handle it for you immediately.\nAfter submitting for review, you can use [Question in progress] to view the review results of the work order you submitted."
		],
		'msgParameters' => null
	];
	
	// Output the response
	http_response_code(200);
	echo json_encode($res);
?>
