<?php
include "../../conn.php";
include "../../functions2.php";

header('Content-Type: application/json; charset=utf-8');
header('Strict-Transport-Security: max-age=31536000');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Allow-Credentials: true');
$allowedOrigins = ['https://yourdomain.com']; // Restrict allowed origins
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Vary: Origin');

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($shonupost['language'], $shonupost['random'], $shonupost['signature'], $shonupost['timestamp'], $shonupost['dataBlock'])) {
        $language = htmlspecialchars($shonupost['language']);
        $random = htmlspecialchars($shonupost['random']);
        $signature = htmlspecialchars($shonupost['signature']);
        $dataBlock = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['dataBlock']));

        try {
            // Fetch `id` based on `dataBlock`
            $query = "SELECT id FROM shonu_subjects WHERE akshinak = '$dataBlock' LIMIT 1";
            $result = mysqli_query($conn, $query);

            if ($result === false) {
                throw new Exception("Database error in query: '$query' - " . mysqli_error($conn));
            }

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $id = $row['id'];

                // Fetch work order data for the user
                $query = "SELECT oid, prob, deposit_order_no, form_id, order_amount, id, created_at, status, remarks 
                  FROM your_table 
                  WHERE userid = $id 
                  ORDER BY created_at DESC";

                $result = mysqli_query($conn, $query);

                if ($result === false) {
                    throw new Exception("Database error in query: '$query' - " . mysqli_error($conn));
                }

                if (mysqli_num_rows($result) > 0) {
                    $list = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $list[] = [
                            "id" => $row['oid'],
                            "workOrderNo" => $row['deposit_order_no'],
                            "workOrderTypeID" => intval($row['form_id']),
                            "workOrderTypeName" => $row['prob'],
                            "submissionTime" => ($row['created_at']),
                            "status" => intval($row['status']),
                            "remark" => $row['remarks']
                        ];
                    }

                    $res = [
                        "data" => [
                            "list" => $list,
                            "pageNo" => 1, // Assuming 1 for simplicity
                            "totalPage" => 1, // Assuming 1 for simplicity
                            "totalCount" => count($list)
                        ],
                        "msgParameters" => null,
                        "code" => 0,
                        "msg" => "Succeed",
                        "msgCode" => 0
                    ];
                    http_response_code(200);
                } else {
                    $res = [
                        "data" => [
                            "list" => [],
                            "pageNo" => 1,
                            "totalPage" => 0,
                            "totalCount" => 0
                        ],
                        "msgParameters" => null,
                        "code" => 0,
                        "msg" => "No work order data found for the given user.",
                        "msgCode" => 0
                    ];
                    http_response_code(200);
                }
            } else {
                $res = [
                    'code' => 1,
                    'msg' => 'Session expired',
                    'msgCode' => 705
                ];
                http_response_code(200);
            }
        } catch (Exception $e) {
            // Catch and return any database error with the query and detailed message
            $res['code'] = 15;
            $res['msg'] = "Database error: " . $e->getMessage();
            $res['msgCode'] = 15;
            http_response_code(500);
        }
    } else {
        $res = [
            'code' => 13,
            'msg' => 'Invalid request data',
            'msgCode' => 13
        ];
        http_response_code(400);
    }
} else {
    http_response_code(405);
}

echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
