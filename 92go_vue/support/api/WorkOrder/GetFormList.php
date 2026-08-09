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
		'data' => [],
		'msgParameters' => null
	];


	$shonubody = file_get_contents("php://input");
	$shonupost = json_decode($shonubody, true);
	
	// Check for JSON parsing errors
	if (json_last_error() !== JSON_ERROR_NONE) {
		$res['code'] = 1;
		$res['msg'] = 'Invalid JSON input';
		http_response_code(400);
		echo json_encode($res);
		exit;
	}

	$res['data'] = [
		[
			'id' => 14,
			'workOrderTypeId' => 6,
			'workOrderTypeName' => '存款未到账',
			'displayName' => 'Deposit Not Receive',
			'sort' => 2,
			'icon' => 'https://support.bdgpremium.cloud/im/Prod/System/022631934.png',
			'type' => 1,
			'outLink' => null
		],
		[
			'id' => 16,
			'workOrderTypeId' => 8,
			'workOrderTypeName' => '修改ISFC',
			'displayName' => 'IFSC Modification',
			'sort' => 4,
			'icon' => 'https://support.bdgpremium.cloud/im/Prod/System/022625045.png',
			'type' => 1,
			'outLink' => null
		],
		[
			'id' => 30,
			'workOrderTypeId' => 12,
			'workOrderTypeName' => '删除银行卡自动化',
			'displayName' => 'Self-service Delete Bank Account and Rebind',
			'sort' => 6,
			'icon' => 'https://support.bdgpremium.cloud/im/Prod/System/022611927.png',
			'type' => 1,
			'outLink' => null
		],
		[
			'id' => 18,
			'workOrderTypeId' => 11,
			'workOrderTypeName' => '删除银行卡',
			'displayName' => 'Delete Withdraw Bank Account and Rebind',
			'sort' => 6,
			'icon' => 'https://support.bdgpremium.cloud/im/Prod/System/022618086.png',
			'type' => 1,
			'outLink' => null
		],
		[
			'id' => 25,
			'workOrderTypeId' => 7,
			'workOrderTypeName' => '取款未到账',
			'displayName' => 'Withdrawal problem',
			'sort' => 8,
			'icon' => 'https://support.bdgpremium.cloud/im/Prod/System/022600588.png',
			'type' => 1,
			'outLink' => null
		],
		[
			'id' => 944,
			'workOrderTypeId' => 17,
			'workOrderTypeName' => '外部链接',
			'displayName' => 'Self-service Change Password',
			'sort' => 333,
			'icon' => 'https://support.bdgpremium.cloud/im/Prod/System/022551773.png',
			'type' => 1,
			'outLink' => 'https://support.bdgpremium.cloud/#/rpwd',
		],
		[
			'id' => 15,
			'workOrderTypeId' => 9,
			'workOrderTypeName' => '修改银行名称',
			'displayName' => 'Change bank name',
			'sort' => 14,
			'icon' => 'https://support.bdgpremium.cloud/im/Prod/System/022522020.png',
			'type' => 1,
			'outLink' => null
		],
      
      [
    'id' => 891,
    'workOrderTypeId' => 18,
    'workOrderTypeName' => '删除PIX',
    'displayName' => 'USDT verification (Indian Members)',
    'sort' => 15,
    'icon' => 'https://worktracking-imgs.oss-ap-southeast-1.aliyuncs.com/Prod/System/112626842-4565-035031553-34-金融-05.png',
    'type' => 1,
    'outLink' => null
],
[
    'id' => 890,
    'workOrderTypeId' => 19,
    'workOrderTypeName' => '新增USDT',
    'displayName' => 'USDT verification (Non Indian Members)',
    'sort' => 15,
    'icon' => 'https://worktracking-imgs.oss-ap-southeast-1.aliyuncs.com/Prod/System/112633672-4566-035031553-34-金融-05.png',
    'type' => 1,
    'outLink' => null
],

		[
			'id' => 17,
			'workOrderTypeId' => 10,
			'workOrderTypeName' => '删除USDT',
			'displayName' => 'Delete Old USDT Address and Rebind',
			'sort' => 16,
			'icon' => 'https://support.bdgpremium.cloud/im/Prod/System/022500280.png',
			'type' => 1,
			'outLink' => null
		],
		[
			'id' => 31,
			'workOrderTypeId' => 13,
			'workOrderTypeName' => '删除USDT自动化',
			'displayName' => 'Self-service to Delete Old USDT Address and Rebind',
			'sort' => 16,
			'icon' => 'https://support.bdgpremium.cloud/im/Prod/System/022510828.png',
			'type' => 1,
			'outLink' => null
		],
		[
			'id' => 75,
			'workOrderTypeId' => 16,
			'workOrderTypeName' => '一对一客服',
			'displayName' => 'Game Problems',
			'sort' => 120,
			'icon' => 'https://support.bdgpremium.cloud/im/Prod/System/022453400.png',
			'type' => 1,
			'outLink' => null
		],
      [
			'id' => 906,
			'workOrderTypeId' => 17,
			'workOrderTypeName' => '外部链接',
			'displayName' => '【BDG Premium】Official Channel',
			'sort' => 123,
			'icon' => 'https://worktracking-imgs.oss-ap-southeast-1.aliyuncs.com/Prod/System/112601001-4564-金融-36.png',
			'type' => 1,
			'outLink' => 'https://t.me/BdgPremiumOfficial',
		],
      [
			'id' => 944,
			'workOrderTypeId' => 17,
			'workOrderTypeName' => '外部链接',
			'displayName' => 'Check BDG Premium link',
			'sort' => 333,
			'icon' => 'https://worktracking-imgs.oss-ap-southeast-1.aliyuncs.com/Prod/System/063506701-12685-金融-37.png',
			'type' => 1,
			'outLink' => 'https://bdgpremium.cloud/',
		]
      
	];

	http_response_code($res['code'] == 0 ? 200 : 400);
	echo json_encode($res);
?>
