<?php 
	include_once __DIR__ . "/../../conn.php";
	include_once __DIR__ . "/../../functions2.php";
	
	header('Content-Type: application/json; charset=utf-8');
	header('Strict-Transport-Security: max-age=31536000');
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
	header('Access-Control-Allow-Credentials: true');
	$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
	header('Access-Control-Allow-Origin: ' . $origin);
	header('vary: Origin');
	
	date_default_timezone_set("Asia/Kolkata");
	$shnunc = date("Y-m-d H:i:s");

	$data = [];

	$data[0]['id'] = 1;
	$data[0]['categoryCode'] = 'Win Go';
	$data[0]['categoryName'] = 'WinGo彩票';
	$data[0]['state'] = 1;
	$data[0]['sort'] = 0;
	$data[0]['categoryImg'] = 'https://ossimg.91admin123admin.com/91club/lotterycategory/lotterycategory_202502101011154e3a.png';
	$data[0]['wingoAmount'] = null;
	$data[0]['k3Amount'] = null;
	$data[0]['fiveDAmount'] = null;
	$data[0]['trxWingoAmount'] = null;
	
	$data[1]['id'] = 2;
	$data[1]['categoryCode'] = 'K3';
	$data[1]['categoryName'] = 'K3彩票';
	$data[1]['state'] = 1;
	$data[1]['sort'] = 0;
	$data[1]['categoryImg'] = 'https://ossimg.91admin123admin.com/91club/lotterycategory/lotterycategory_20250210101053ntrf.png';
	$data[1]['wingoAmount'] = null;
	$data[1]['k3Amount'] = null;
	$data[1]['fiveDAmount'] = null;
	$data[1]['trxWingoAmount'] = null;
	
	$data[2]['id'] = 3;
	$data[2]['categoryCode'] = '5D';
	$data[2]['categoryName'] = '5D彩票';
	$data[2]['state'] = 1;
	$data[2]['sort'] = 0;
	$data[2]['categoryImg'] = 'https://ossimg.91admin123admin.com/91club/lotterycategory/lotterycategory_20250210101042iwui.png';
	$data[2]['wingoAmount'] = null;
	$data[2]['k3Amount'] = null;
	$data[2]['fiveDAmount'] = null;
	$data[2]['trxWingoAmount'] = null;
	
	$data[3]['id'] = 4;
	$data[3]['categoryCode'] = 'Trx Win Go';
	$data[3]['categoryName'] = 'TrxWinGo彩票';
	$data[3]['state'] = 1;
	$data[3]['sort'] = 0;
	$data[3]['categoryImg'] = 'https://ossimg.91admin123admin.com/91club/lotterycategory/lotterycategory_20250210101104jtse.png';
	$data[3]['wingoAmount'] = null;
	$data[3]['k3Amount'] = null;
	$data[3]['fiveDAmount'] = null;
	$data[3]['trxWingoAmount'] = null;
	
	$res = [
		'code' => 0,
		'msg' => 'Succeed',
		'msgCode' => 0,
		'serviceNowTime' => $shnunc,
		'data' => $data
	];

	http_response_code(200);
	echo json_encode($res);
?>