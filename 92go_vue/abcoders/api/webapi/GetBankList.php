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
		if (isset($shonupost['language']) && isset($shonupost['random']) && isset($shonupost['signature']) && isset($shonupost['timestamp']) && isset($shonupost['withdrawid'])) {
			$language = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['language']));
			$random = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['random']));
			$signature = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['signature']));
			$withdrawid = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['withdrawid']));
			$shonustr = '{"language":'.$language.',"random":"'.$random.'","withdrawid":'.$withdrawid.'}';
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
						http_response_code(200);
						if($withdrawid == 1){
							echo '
								{
								  "data": {
									"banklist": [
									  {
										"bankID": 16,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Bank of Baroda",
										"reserved": "1"
									  },
									  {
										"bankID": 15,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Union Bank of India",
										"reserved": "1"
									  },
									  {
										"bankID": 14,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Central Bank of India",
										"reserved": "1"
									  },
									  {
										"bankID": 13,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Yes Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 12,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "HDFC Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 11,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Karnataka Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 10,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Standard Chartered Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 9,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "IDBI Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 8,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Bank of India",
										"reserved": "1"
									  },
									  {
										"bankID": 7,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Punjab National Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 6,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "ICICI Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 5,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Canara Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 4,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Kotak Mahindra Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 3,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "State Bank of India",
										"reserved": "1"
									  },
									  {
										"bankID": 2,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Indian Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 1,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Axis Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 17,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "FEDERAL BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 18,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Syndicate Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 22,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Citibank India",
										"reserved": "1"
									  },
									  {
										"bankID": 23,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Indian Overseas Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 24,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "IDFC Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 25,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Bandhan Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 26,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Indusind Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 29,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Equitas Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 30,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "India Post Payments Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 31,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Corporation Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 27,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Jammu & Kashmir Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 32,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "City Union Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 28,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "PYTM PAYMENTS BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 33,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Karur Vysya Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 34,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Tamilnad Mercantile Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 35,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Allahabad Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 36,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "varachha co-operative bank",
										"reserved": "1"
									  },
									  {
										"bankID": 37,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Meghalaya Rural Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 38,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "AU Small Finance Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 39,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Lakshmi Vilas Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 40,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "South Indian Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 41,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Bassein catholic co-operative Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 42,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Airtel Payment Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 43,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "State Bank of Hyderabad",
										"reserved": "1"
									  },
									  {
										"bankID": 44,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Gp parsik bank",
										"reserved": "1"
									  },
									  {
										"bankID": 45,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Kerala Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 46,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "RBL Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 47,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Dhanlaxmi Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 48,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "TJSB Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 49,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Punjab & Sind Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 50,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Purvanchal bank",
										"reserved": "1"
									  },
									  {
										"bankID": 51,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Sarva Haryana Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 52,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Ahmedabad District Co-Operative Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 53,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Fino Payments Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 54,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Saraswat Cooperative Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 62,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Dhanlaxmi bank",
										"reserved": "1"
									  },
									  {
										"bankID": 63,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Telangana Grameena Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 57,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "andhra pragathi grameena bank",
										"reserved": "1"
									  },
									  {
										"bankID": 58,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "rajasthan marudhara gramin bank",
										"reserved": "1"
									  },
									  {
										"bankID": 59,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Abhyudaya bank",
										"reserved": "1"
									  },
									  {
										"bankID": 60,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "ujjivan small finance bank",
										"reserved": "1"
									  },
									  {
										"bankID": 61,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Pragathi Krishna Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 64,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "capital small finance bank",
										"reserved": "1"
									  },
									  {
										"bankID": 65,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Mizoram Rural Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 66,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Andhra Pradesh Grameena Vikas Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 67,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Karnataka Vikas Grameena Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 68,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "The Ahmedabad merchantile co-op bank Ltd",
										"reserved": "1"
									  },
									  {
										"bankID": 69,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Madhya Bihar Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 70,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "NSDL Payments Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 71,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "ESAF Small Finance Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 72,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Himachal Pradesh state cooperative bank",
										"reserved": "1"
									  },
									  {
										"bankID": 73,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Maharashtra state cooperative bank",
										"reserved": "1"
									  },
									  {
										"bankID": 74,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "ORIENTAL BANK OF COMMERCE",
										"reserved": "1"
									  },
									  {
										"bankID": 75,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "nainital bank",
										"reserved": "1"
									  },
									  {
										"bankID": 76,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Telangana grameena bank",
										"reserved": "1"
									  },
									  {
										"bankID": 77,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Jharkhand Rajya Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 78,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "jio payments bank",
										"reserved": "1"
									  },
									  {
										"bankID": 79,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "MAHARASHTRA GRAMIN BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 80,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "AIRTEL PAYMENTS BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 81,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Uttarakhand Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 82,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "DBS BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 83,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Equitas Small Finance Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 84,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Himachal Pradesh Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 85,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Krishna District Co-Operative Central Bank Ltd.",
										"reserved": "1"
									  },
									  {
										"bankID": 86,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "RAJKOT NAGARIK SAHAKARI BANK LTD",
										"reserved": "1"
									  },
									  {
										"bankID": 87,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "North East small financial bank",
										"reserved": "1"
									  },
									  {
										"bankID": 88,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Catholic syrian bank",
										"reserved": "1"
									  },
									  {
										"bankID": 89,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Fincare small finance bank",
										"reserved": "1"
									  },
									  {
										"bankID": 90,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Baroda Uttar Pradesh Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 91,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Dhanalakshmi bank",
										"reserved": "1"
									  },
									  {
										"bankID": 92,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Cosmos Co-operative Bank Ltd",
										"reserved": "1"
									  },
									  {
										"bankID": 93,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Saurashtra gramin bank",
										"reserved": "1"
									  },
									  {
										"bankID": 94,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Baroda Rajasthan kshetriya gramin bank",
										"reserved": "1"
									  },
									  {
										"bankID": 95,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Suco Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 96,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Jana small finance bank",
										"reserved": "1"
									  },
									  {
										"bankID": 97,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "",
										"reserved": "1"
									  },
									  {
										"bankID": 98,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Dena Gujarat Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 99,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Chaitanya Godavari Grameena Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 100,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "SVC BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 101,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Bharat cooperative bank",
										"reserved": "1"
									  },
									  {
										"bankID": 102,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "The Surat District Co-Op. Bank Ltd.",
										"reserved": "1"
									  },
									  {
										"bankID": 103,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "USDT",
										"reserved": "1"
									  },
									  {
										"bankID": 104,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "The Kalupur Commercial Co-operative Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 105,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "India Post Payments Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 106,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Prime co-operative Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 107,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Tripura Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 108,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Zila Sahakari Bank Ltd Bareilly",
										"reserved": "1"
									  },
									  {
										"bankID": 109,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "ARYAVART Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 110,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Development credit Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 111,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Ujjivan Small Finance Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 112,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Sarva UP Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 113,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "New India Co-Operative Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 114,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "NKGSB Co-operative Bank Ltd.",
										"reserved": "1"
									  },
									  {
										"bankID": 115,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Vijaya Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 116,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "United Bank of India",
										"reserved": "1"
									  },
									  {
										"bankID": 117,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "State Bank of Bikaner And Jaipur",
										"reserved": "1"
									  },
									  {
										"bankID": 118,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Shri Janata Sahakari Bank LTD",
										"reserved": "1"
									  },
									  {
										"bankID": 119,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Rajgurunagar Sahakari Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 120,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "FEDERAL NEO BANK JUPITER",
										"reserved": "1"
									  },
									  {
										"bankID": 121,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "CHHATTISGARH RAJYA GRAMIN BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 122,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Apna Sahakari Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 123,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "GS Mahanagar Co-Op Bank Ltd",
										"reserved": "1"
									  },
									  {
										"bankID": 124,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Bangiya Gramin Vikash Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 125,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Assam Gramin Vikash Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 126,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Saurashtra Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 127,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Kangra Central Co-operative Bank Ltd",
										"reserved": "1"
									  },
									  {
										"bankID": 128,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Punjab Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 129,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Assam gramin bikash bank",
										"reserved": "1"
									  },
									  {
										"bankID": 130,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Karnataka Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 131,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "SURYODAY SMALL FINANCE BANK LIMITED",
										"reserved": "1"
									  },
									  {
										"bankID": 132,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Utkarsh Small Finance Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 133,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "The Meghalaya Co-operative Apex Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 134,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "UTTAR BIHAR GRAMIN BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 135,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "STATE BANK OF TRAVANCORE",
										"reserved": "1"
									  },
									  {
										"bankID": 136,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "SHIVALIK SMALL FIHANCE BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 137,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "DAKSHIN BIHIR GRAMIN BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 138,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "DBS Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 139,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "State Bank of Hyderabad",
										"reserved": "1"
									  },
									  {
										"bankID": 140,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "manipur rural bank",
										"reserved": "1"
									  },
									  {
										"bankID": 141,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "State bank of patiala",
										"reserved": "1"
									  },
									  {
										"bankID": 142,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "BARODA GUJARAT GRAMIN BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 143,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "The Gujarat State Co-operative Bank Limited",
										"reserved": "1"
									  },
									  {
										"bankID": 144,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "vasai vikas sahakari",
										"reserved": "1"
									  },
									  {
										"bankID": 145,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "paschim banga gramin bank",
										"reserved": "1"
									  },
									  {
										"bankID": 146,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "PRAGATHI KRISHNA GRAMIN BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 147,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "VISHAPATNAM co-operative bank",
										"reserved": "1"
									  },
									  {
										"bankID": 148,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Samarth Sahakari Bank Ltd",
										"reserved": "1"
									  },
									  {
										"bankID": 149,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "uttarbanga kshetriya gramin bank",
										"reserved": "1"
									  },
									  {
										"bankID": 150,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "janata sahakari bank ltd",
										"reserved": "1"
									  },
									  {
										"bankID": 152,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "the gayatri co-operative urban bank",
										"reserved": "1"
									  },
									  {
										"bankID": 153,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Jupiter Federal Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 154,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "ABHYUDAYA CO-OP. BANK LTD.",
										"reserved": "1"
									  },
									  {
										"bankID": 155,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "J&K Grameen Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 156,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Post Office Savings Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 157,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "SBM Bank India",
										"reserved": "1"
									  },
									  {
										"bankID": 20,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Bank of maharashtra",
										"reserved": "1"
									  },
									  {
										"bankID": 158,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "DAMAN",
										"reserved": "1"
									  },
									  {
										"bankID": 159,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Jind central Co-OP Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 151,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "PRATHAMA Up Gramin Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 160,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "The Jalgaon Peoples Co-Op Bank",
										"reserved": "1"
									  },
									  {
										"bankID": 161,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Associated Co-operative Bank limited",
										"reserved": "1"
									  },
									  {
										"bankID": 162,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "Mizoram Co-operative Apex Bank Ltd.",
										"reserved": "1"
									  },
									  {
										"bankID": 163,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "The Muslim Co-operative bank",
										"reserved": "1"
									  },
									  {
										"bankID": 164,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "PRATHAMA BANK",
										"reserved": "1"
									  },
									  {
										"bankID": 165,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "KASIKORNBANK",
										"reserved": "1"
									  }
									]
								  },
								  "code": 0,
								  "msg": "Succeed",
								  "msgCode": 0,
								  "serviceNowTime": "$shnunc"
								}
							';				
						}
						else if($withdrawid == 3){
							echo '
								{
								  "data": {
									"banklist": [
									  {
										"bankID": 55,
										"bankLogo": "https://ossimg.bdg123456.com/BDGWin",
										"bankName": "TRC",
										"reserved": "3"
									  }
									]
								  },
								  "code": 0,
								  "msg": "Succeed",
								  "msgCode": 0,
								  "serviceNowTime": "$shnunc"
								}
							';	
						}
					}
					else{
						$res['code'] = 4;
						$res['msg'] = 'No operation permission';
						$res['msgCode'] = 2;
						http_response_code(401);
						echo json_encode($res);
					}					
				}
				else{					
					$res['code'] = 4;
					$res['msg'] = 'No operation permission';
					$res['msgCode'] = 2;
					http_response_code(401);
					echo json_encode($res);					
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