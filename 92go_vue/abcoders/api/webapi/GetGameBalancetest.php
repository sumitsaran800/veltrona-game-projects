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


$acho = $_REQUEST['acho'];
$hara = $_REQUEST['hara'];
$laga = $_REQUEST['laga'];



$query = "SELECT * FROM shonu_subjects WHERE mobile = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $acho);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $userDetails = $result->fetch_assoc();
    $balakedara = $userDetails['id'];
    $akshinak = $userDetails['akshinak'];
    
    $mottaQuery = "SELECT motta FROM shonu_kaichila WHERE balakedara = ?";
    $mottaStmt = $conn->prepare($mottaQuery);
    $mottaStmt->bind_param('s', $balakedara);
    $mottaStmt->execute();
    $mottaResult = $mottaStmt->get_result();
    if ($mottaResult->num_rows > 0) {
        $mottaData = $mottaResult->fetch_assoc();
        $usermotta = $mottaData['motta'];
        if ($usermotta >= $laga) {
            $newmotta = $usermotta - $laga + $hara;
            
            $updateQuery = "UPDATE shonu_kaichila SET motta = ? WHERE balakedara = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('is', $newmotta, $balakedara);
            
            if ($updateStmt->execute()) {
                echo json_encode(['newmotta' => $newmotta]);
                http_response_code(200);
            } else {
                echo json_encode(['error' => '501']);
                http_response_code(500);
            }
        } else {
            echo json_encode(['error' => '50']);
            http_response_code(400);
        }
    } else {
        echo json_encode(['error' => '404']);
        http_response_code(404);
    }
} else {
    echo json_encode(['error' => '404']);
    http_response_code(404);
}

$stmt->close();
$conn->close();
?>
