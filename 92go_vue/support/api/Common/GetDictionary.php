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
			'language' => [
				['id' => 0, 'nativeName' => 'English', 'name' => 'en', 'code' => 'en'],
				['id' => 1, 'nativeName' => 'Indonesia', 'name' => 'id', 'code' => 'id'],
				['id' => 2, 'nativeName' => 'Tiếng Việt', 'name' => 'vi', 'code' => 'vi'],
				['id' => 3, 'nativeName' => 'Português (Brasil)', 'name' => 'pt', 'code' => 'pt'],
				['id' => 4, 'nativeName' => 'မြန်မာ', 'name' => 'my', 'code' => 'my'],
				['id' => 5, 'nativeName' => 'हिंदी', 'name' => 'hi', 'code' => 'hi'],
				['id' => 6, 'nativeName' => 'Melayu', 'name' => 'ms', 'code' => 'ms'],
				['id' => 7, 'nativeName' => 'العربية', 'name' => 'ar', 'code' => 'ar'],
				['id' => 8, 'nativeName' => 'កម្ពុជា។', 'name' => 'km', 'code' => 'km'],
				['id' => 9, 'nativeName' => 'اردو', 'name' => 'ur', 'code' => 'ur']
			],
			'walletTypeName' => ['CPF', 'Phone', 'Email'],
			'categorySelection' => ['Change new bank', 'Wrong beneficiary', 'Wrong bank number'],
			'tenantList' => null,
			'workOrderTypeList' => null,
			'fieldTypeList' => null,
			'countryList' => null,
			'typeCodeList' => [
				['description' => '用户Id', 'id' => 1, 'name' => 'UserId'],
				['description' => '用户名称', 'id' => 2, 'name' => 'UserName'],
				['description' => '文本', 'id' => 3, 'name' => 'TextContent'],
				['description' => '图片上传', 'id' => 4, 'name' => 'ImageUpload'],
				['description' => '验证码', 'id' => 5, 'name' => 'Captcha'],
				['description' => '银行卡号', 'id' => 6, 'name' => 'BankAccountNumber'],
				['description' => 'ISFC', 'id' => 7, 'name' => 'IFSC'],
				['description' => '银行名称', 'id' => 8, 'name' => 'BankName'],
				['description' => '充值订单号', 'id' => 9, 'name' => 'DepositOrderNo'],
				['description' => 'UTR', 'id' => 10, 'name' => 'UTR'],
				['description' => '订单金额', 'id' => 11, 'name' => 'OrderAmount'],
				['description' => '上传文件', 'id' => 12, 'name' => 'FileUpload'],
				['description' => '手机/邮箱验证码', 'id' => 13, 'name' => 'PhoneEmailCaptcha'],
				['description' => '取款订单号', 'id' => 14, 'name' => 'WithdrawOrderNo'],
				['description' => '新密码', 'id' => 15, 'name' => 'NewPassword'],
				['description' => '银行卡名称', 'id' => 16, 'name' => 'CardName'],
				['description' => '提现金额', 'id' => 17, 'name' => 'WithdrawAmount'],
				['description' => '长文本', 'id' => 18, 'name' => 'LongText'],
				['description' => '链接地址', 'id' => 19, 'name' => 'LinkUrl'],
				['description' => 'PIX类型选择', 'id' => 20, 'name' => 'PixType'],
				['description' => 'PIX账号', 'id' => 21, 'name' => 'PixAccount'],
				['description' => 'CPF', 'id' => 22, 'name' => 'CPF'],
				['description' => 'USDT地址', 'id' => 23, 'name' => 'UsdtAddress'],
				['description' => '类型选择', 'id' => 24, 'name' => 'CategorySelection']
			],
			'cacheInstanceList' => null,
			'cacheDBList' => null
		],
		'msgParameters' => null
	];
	
	// Output the response
	http_response_code(200);
	echo json_encode($res);
?>
