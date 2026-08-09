<?php
include "../../conn.php";
include "../../functions2.php";
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
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
    if (isset($shonupost['language'], $shonupost['random'], $shonupost['signature'], $shonupost['timestamp'])) {
        $language = mysqli_real_escape_string($conn, $shonupost['language']);
        $random = mysqli_real_escape_string($conn, $shonupost['random']);
        $signature = mysqli_real_escape_string($conn, $shonupost['signature']);
        $shonustr = '{"language":"' . $language . '","random":"' . $random . '"}';
        $shonusign = strtoupper(md5($shonustr));
        $dataBlock = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['dataBlock']));

        $query = "SELECT id FROM shonu_subjects WHERE akshinak = '$dataBlock' LIMIT 1";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];
        $workOrderNo = time() . rand(1000, 9999);
        if (isset($shonupost['formId']) && $shonupost['formId'] == 14) {
            $formId = $shonupost['formId'];
            $depositOrderNo = '';
            $orderAmount = '';
            $textContent = '';
            $utr = '';
            $fileUpload = '';
            $imageUpload = '';

            foreach ($shonupost['formFields'] as $item) {
                if ($item['typeCode'] == 'DepositOrderNo') {
                    $depositOrderNo = mysqli_real_escape_string($conn, $item['fieldValue']);
                } elseif ($item['typeCode'] == 'OrderAmount') {
                    $orderAmount = mysqli_real_escape_string($conn, $item['fieldValue']);
                } elseif ($item['typeCode'] == 'TextContent') {
                    $textContent = mysqli_real_escape_string($conn, $item['fieldValue']);
                } elseif ($item['typeCode'] == 'UTR') {
                    $utr = mysqli_real_escape_string($conn, $item['fieldValue']);
                } elseif ($item['typeCode'] == 'FileUpload') {
                    $fileUpload = mysqli_real_escape_string($conn, $item['fieldValue']);
                } elseif ($item['typeCode'] == 'ImageUpload') {
                    $imageUpload = mysqli_real_escape_string($conn, $item['fieldValue']);
                }
            }

            $fileName = $fileUpload ? basename(parse_url($fileUpload, PHP_URL_PATH)) : '';

            $uploadDir = '../../uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if ($fileUpload) {
                $filePath = $uploadDir . $fileName;

                if (@file_put_contents($filePath, @file_get_contents($fileUpload)) === false) {
                    $response = [
                        'code' => 17,
                        'msg' => 'Failed to download file',
                        'msgCode' => 17,
                    ];
                    http_response_code(500);
                    echo json_encode($response);
                    exit;
                }
            }

                if ($imageUpload) {
                $imageName = basename(parse_url($imageUpload, PHP_URL_PATH));
                $imagePath = $uploadDir . $imageName;

                if (@file_put_contents($imagePath, @file_get_contents($imageUpload)) === false) {
                    $response = [
                        'code' => 17,
                        'msg' => 'Failed to download image',
                        'msgCode' => 17,
                    ];
                    http_response_code(500);
                    echo json_encode($response);
                    exit;
                }
            }
            $prob = 'Deposit Not Receive';
            $workOrderNo = time() . rand(1000, 9999);
            $query = "INSERT INTO your_table (prob,userid,form_id, deposit_order_no, order_amount, text_content, utr, file_upload, image_upload, oid) 
              VALUES ('$prob','$id','16', '$depositOrderNo', '$orderAmount', '$textContent', '$utr', '$fileName', '$imageName', '$workOrderNo')";

            if (mysqli_query($conn, $query)) {
                $res = [
                    "data" => true,
                    "msgParameters" => null,
                    "code" => 0,
                    "msg" => "Succeed",
                    "msgCode" => 0
                ];
                http_response_code(200);
            } else {
                $error = mysqli_error($conn);
                $res['code'] = 15;
                $res['msg'] = 'Database error: ' . $error;
                $res['msgCode'] = 15;
                http_response_code(500);
            }
            echo json_encode($res);
        } else if (isset($shonupost['formId']) && $shonupost['formId'] == 16) {
            $ifsc = '';
            $bankAccountNumber = '';

            foreach ($shonupost['formFields'] as $item) {
                if ($item['typeCode'] == 'IFSC') {
                    $ifsc = mysqli_real_escape_string($conn, $item['fieldValue']);
                } elseif ($item['typeCode'] == 'BankAccountNumber') {
                    $bankAccountNumber = mysqli_real_escape_string($conn, $item['fieldValue']);
                }
            }
            $prob = 'IFSC Modification';
            $workOrderNo = time() . rand(1000, 9999);
            $query = "INSERT INTO your_table (prob,deposit_order_no,userid, form_id, ifsc, bank_account_number) VALUES ('$prob','$workOrderNo','$id', '16', '$ifsc', '$bankAccountNumber')";

            if (mysqli_query($conn, $query)) {
                $res = [
                    "data" => true,
                    "msgParameters" => null,
                    "code" => 0,
                    "msg" => "Succeed",
                    "msgCode" => 0
                ];
                http_response_code(200);
            } else {
                $error = mysqli_error($conn);
                $res['code'] = 15;
                $res['msg'] = 'Database error: ' . $error;
                $res['msgCode'] = 15;
                http_response_code(500);
            }
            echo json_encode($res);
        } elseif (isset($shonupost['formId']) && $shonupost['formId'] == 75) {
            $longText = '';
            $fileUpload = '';

            foreach ($shonupost['formFields'] as $item) {
                if ($item['typeCode'] == 'LongText') {
                    $longText = mysqli_real_escape_string($conn, $item['fieldValue']);
                } elseif ($item['typeCode'] == 'FileUpload') {
                    $fileUpload = mysqli_real_escape_string($conn, $item['fieldValue']);
                }
            }

            $fileName = $fileUpload ? basename(parse_url($fileUpload, PHP_URL_PATH)) : '';

            $uploadDir = '../../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if ($fileUpload) {
                $filePath = $uploadDir . $fileName;

                if (@file_put_contents($filePath, @file_get_contents($fileUpload)) === false) {
                    $response = [
                        'code' => 17,
                        'msg' => 'Failed to download file',
                        'msgCode' => 17,
                    ];
                    http_response_code(500);
                    echo json_encode($response);
                    exit;
                }
            }

            $query = "INSERT INTO your_table (deposit_order_no,form_id,prob,userid, text_content, file_upload) VALUES ('$workOrderNo','16','Game Problems','$id', '$longText', '$fileName')";

            if (mysqli_query($conn, $query)) {
                $res = [
                    "data" => true,
                    "msgParameters" => null,
                    "code" => 0,
                    "msg" => "Succeed",
                    "msgCode" => 0
                ];
                http_response_code(200);    
            
        } else {
                $error = mysqli_error($conn);
                $res['code'] = 15;
                $res['msg'] = 'Database error: ' . $error;
                $res['msgCode'] = 15;
                http_response_code(500);
            }
            echo json_encode($res);
        } else if (isset($shonupost['formId']) && $shonupost['formId'] == 15) {
            $ifsc = '';
            $bankAccountNumber = '';

            foreach ($shonupost['formFields'] as $item) {
                if ($item['typeCode'] == 'CardName') {
                    $CardName = preg_replace('/=\d+/', '', $item['fieldValue']);

                } elseif ($item['typeCode'] == 'BankAccountNumber') {
                    $bankAccountNumber = mysqli_real_escape_string($conn, $item['fieldValue']);
                }
            }
            $prob = 'Change bank name';
            $workOrderNo = time() . rand(1000, 9999);
            $query = "INSERT INTO your_table (prob,deposit_order_no,userid, form_id, CardName, bank_account_number) VALUES ('$prob','$workOrderNo','$id', '16', '$CardName', '$bankAccountNumber')";

            if (mysqli_query($conn, $query)) {
                $res = [
                    "data" => true,
                    "msgParameters" => null,
                    "code" => 0,
                    "msg" => "Succeed",
                    "msgCode" => 0
                ];
                http_response_code(200);
            } else {
                $error = mysqli_error($conn);
                $res['code'] = 15;
                $res['msg'] = 'Database error: ' . $error;
                $res['msgCode'] = 15;
                http_response_code(500);
            }
            echo json_encode($res);
} elseif (isset($shonupost['formId']) && $shonupost['formId'] == 25) {
            $longText = '';
            $fileUpload = '';

            foreach ($shonupost['formFields'] as $item) {
                if ($item['typeCode'] == 'TextContent') {
                    $longText = mysqli_real_escape_string($conn, $item['fieldValue']);
                } elseif ($item['typeCode'] == 'FileUpload') {
                    $fileUpload = mysqli_real_escape_string($conn, $item['fieldValue']);
                }elseif ($item['typeCode'] == 'WithdrawAmount') {
                    $WithdrawAmount= mysqli_real_escape_string($conn, $item['fieldValue']);
                }elseif ($item['typeCode'] == 'WithdrawOrderNo') {
                    $WithdrawOrderNo = mysqli_real_escape_string($conn, $item['fieldValue']);
                }
            }

            $fileName = $fileUpload ? basename(parse_url($fileUpload, PHP_URL_PATH)) : '';

            $uploadDir = '../../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if ($fileUpload) {
                $filePath = $uploadDir . $fileName;

                if (@file_put_contents($filePath, @file_get_contents($fileUpload)) === false) {
                    $response = [
                        'code' => 17,
                        'msg' => 'Failed to download file',
                        'msgCode' => 17,
                    ];
                    http_response_code(500);
                    echo json_encode($response);
                    exit;
                }
            }

            $query = "INSERT INTO your_table (order_amount,deposit_order_no,form_id,prob,userid, text_content, file_upload) VALUES ('$WithdrawAmount','$WithdrawOrderNo','16','Withdrawal problem','$id', '$longText', '$fileName')";

            if (mysqli_query($conn, $query)) {
                $res = [
                    "data" => true,
                    "msgParameters" => null,
                    "code" => 0,
                    "msg" => "Succeed",
                    "msgCode" => 0
                ];
                http_response_code(200);    
            
        } else {
                $error = mysqli_error($conn);
                $res['code'] = 15;
                $res['msg'] = 'Database error: ' . $error;
                $res['msgCode'] = 15;
                http_response_code(500);
            }
            echo json_encode($res);
        } else {
            $res['code'] = 16;
            $res['msg'] = 'Invalid formId';
            $res['msgCode'] = 16;
            http_response_code(400);
            echo json_encode($res);
        }
    } else {
        $res['code'] = 13;
        $res['msg'] = 'Invalid signature';
        $res['msgCode'] = 13;
        http_response_code(403);
        echo json_encode($res);
    }
} else {
    http_response_code(405);
    echo json_encode($res);
}
?>
