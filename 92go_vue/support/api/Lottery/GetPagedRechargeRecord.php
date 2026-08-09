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

    $res = [
        "data" => [
            "list" => [],
            "pageNo" => 0,
            "totalPage" => 0,
            "totalCount" => 0
        ],
        "msgParameters" => null,
        "code" => 11,
        "msg" => "Method not allowed",
        "msgCode" => 12
    ];

    $shonubody = file_get_contents("php://input");
    $shonupost = json_decode($shonubody, true);

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        if (isset($shonupost['language'], $shonupost['random'], $shonupost['signature'], $shonupost['timestamp'])) {
            $language = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['language']));
            $random = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['random']));
            $signature = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['signature']));
            $dataBlock = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['dataBlock']));

            $shonustr = '{"language":"' . $language . '","random":"' . $random . '"}';
            $shonusign = strtoupper(md5($shonustr));

            if ($signature) {
                // Extract id from shonu_subjects where akshinak = $dataBlock
                $query = "SELECT id FROM shonu_subjects WHERE akshinak = '$dataBlock' LIMIT 1";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $id = $row['id'];

                    // Fetch all records from thevani where balakedara = id and sthiti = 0
                    $query2 = "SELECT dharavahi,mula, dinankavannuracisi, motta 
                               FROM thevani 
                               WHERE balakedara = '$id' AND sthiti = 0 
                               ORDER BY dinankavannuracisi DESC"; 
                    $result2 = mysqli_query($conn, $query2);

                    if ($result2 && mysqli_num_rows($result2) > 0) {
                        $list = [];
                        while ($row2 = mysqli_fetch_assoc($result2)) {
                            $list[] = [
                                "addTime" => $row2['dinankavannuracisi'],
                                "rechargeNumber" => $row2['dharavahi'],
                                "type" => 10138,
                                "price" => $row2['motta'],
                                "state" => 0,
                                "uRate" => 93.00,
                                "uGold" => 10.00,
                                "payID" => 11,
                                "payName" => $row2['mula'],
                                "submitState" => 1
                            ];
                        }

                        $res["data"] = [
                            "list" => $list,
                            "pageNo" => 1,
                            "totalPage" => 1,
                            "totalCount" => count($list),
                            "payload" => [
                                "dataBlock" => $dataBlock,
                                "extractedId" => $id
                            ]
                        ];
                        $res['code'] = 0;
                        $res['msg'] = 'Succeed';
                        $res['msgCode'] = 0;
                        http_response_code(200);
                    } else {
                        $res['code'] = 0;
                        $res['msg'] = 'No records found';
                        $res['msgCode'] = 8;
                        http_response_code(200);
                    }
                } else {
                    $res = [
                    'code' => 1,
                    'msg' => 'Session expired',
                    'msgCode' => 705
                ];
                http_response_code(200);
                exit();
                }
            } else {
                $res['code'] = 4;
                $res['msg'] = 'No operation permission';
                $res['msgCode'] = 2;
                http_response_code(401);
            }
        } else {
            $res['code'] = 7;
            $res['msg'] = 'Param is Invalid';
            $res['msgCode'] = 6;
            http_response_code(400);
        }
    } else {
        http_response_code(405);
    }

    echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
