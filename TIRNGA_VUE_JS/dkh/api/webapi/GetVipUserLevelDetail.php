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
        if (isset($shonupost['language']) && isset($shonupost['random']) && isset($shonupost['signature']) && isset($shonupost['timestamp'])) {
            $language = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['language']));
            $random = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['random']));
            $signature = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['signature']));
            $shonustr = '{"language":'.$language.',"random":"'.$random.'"}';
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
                    $sesresult = $conn->query($sesquery);
                    $sesnum = mysqli_num_rows($sesresult);
                    if($sesnum == 1){
                        $vipquery = "SELECT expe, lvl
                          FROM vip
                          WHERE userid = ".$data_auth['payload']['id'];
                        $vipresult = $conn->query($vipquery);
                        $viparr = mysqli_fetch_array($vipresult);
                        
                        $vip_levels = [
                            ["id" => 1, "vipName" => "VIP1", "status" => 1, "currentExp" => (int)$viparr['expe'], "upgrade" => 3000, "relegationExp" => 0, "relegation" => 1000, "deductExp" => 1500, "amount" => 1, "upgradeStatus" => $viparr['lvl'] >= 1 ? 1 : 0],
                            ["id" => 2, "vipName" => "VIP2", "status" => 1, "currentExp" => (int)$viparr['expe'], "upgrade" => 30000, "relegationExp" => 0, "relegation" => 10000, "deductExp" => 15000, "amount" => 1, "upgradeStatus" => $viparr['lvl'] >= 2 ? 1 : 0],
                            ["id" => 3, "vipName" => "VIP3", "status" => 1, "currentExp" => (int)$viparr['expe'], "upgrade" => 400000, "relegationExp" => 0, "relegation" => 100000, "deductExp" => 200000, "amount" => 1, "upgradeStatus" => $viparr['lvl'] >= 3 ? 1 : 0],
                            ["id" => 4, "vipName" => "VIP4", "status" => 1, "currentExp" => (int)$viparr['expe'], "upgrade" => 4000000, "relegationExp" => 0, "relegation" => 1000000, "deductExp" => 2000000, "amount" => 1, "upgradeStatus" => $viparr['lvl'] >= 4 ? 1 : 0],
                            ["id" => 5, "vipName" => "VIP5", "status" => 1, "currentExp" => (int)$viparr['expe'], "upgrade" => 20000000, "relegationExp" => 0, "relegation" => 10000000, "deductExp" => 10000000, "amount" => 1, "upgradeStatus" => $viparr['lvl'] >= 5 ? 1 : 0],
                            ["id" => 6, "vipName" => "VIP6", "status" => 1, "currentExp" => (int)$viparr['expe'], "upgrade" => 80000000, "relegationExp" => 0, "relegation" => 20000000, "deductExp" => 40000000, "amount" => 1, "upgradeStatus" => $viparr['lvl'] >= 6 ? 1 : 0],
                            ["id" => 7, "vipName" => "VIP7", "status" => 1, "currentExp" => (int)$viparr['expe'], "upgrade" => 300000000, "relegationExp" => 0, "relegation" => 50000000, "deductExp" => 100000000, "amount" => 1, "upgradeStatus" => $viparr['lvl'] >= 7 ? 1 : 0],
                            ["id" => 8, "vipName" => "VIP8", "status" => 1, "currentExp" => (int)$viparr['expe'], "upgrade" => 1000000000, "relegationExp" => 0, "relegation" => 200000000, "deductExp" => 400000000, "amount" => 1, "upgradeStatus" => $viparr['lvl'] >= 8 ? 1 : 0],
                            ["id" => 9, "vipName" => "VIP9", "status" => 1, "currentExp" => (int)$viparr['expe'], "upgrade" => 5000000000, "relegationExp" => 0, "relegation" => 1000000000, "deductExp" => 2000000000, "amount" => 1, "upgradeStatus" => $viparr['lvl'] >= 9 ? 1 : 0],
                            ["id" => 10, "vipName" => "VIP10", "status" => 1, "currentExp" => (int)$viparr['expe'], "upgrade" => 9999999999, "relegationExp" => 0, "relegation" => 3333333333, "deductExp" => 6666666666, "amount" => 1, "upgradeStatus" => $viparr['lvl'] >= 10 ? 1 : 0]
                        ];
                        
                        $res['data'] = $vip_levels;
                        $res['code'] = 0;
                        $res['msg'] = 'Succeed';
                        $res['msgCode'] = 0;
                        http_response_code(200);
                        echo json_encode($res);    
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
