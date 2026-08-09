<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Headers: ar-origin, Origin, X-Requested-With, Content-Type, Accept, Authorization');
date_default_timezone_set('Asia/Kolkata');
$serviceNowTimeFormatted = date('Y-m-d H:i:s');

$jsonData = '{
    "data": [
        {
            "vendorCode": "JILI",
            "sort": 95,
            "childList": [
                {
                    "gameID": "109",
                    "gameNameEn": "Fortune Gems",
                    "img": "https://ossimg.tashanedc.com/Tashanwin/gamelogo/JILI/109.png",
                    "vendorId": 18,
                    "vendorCode": "JILI",
                    "imgUrl2": null,
                    "customGameType": 0
                },
                {
                    "gameID": "223",
                    "gameNameEn": "Fortune Gems 2",
                    "img": "https://ossimg.tashanedc.com/Tashanwin/gamelogo/JILI/223.png",
                    "vendorId": 18,
                    "vendorCode": "JILI",
                    "imgUrl2": null,
                    "customGameType": 0
                },
                {
                    "gameID": "51",
                    "gameNameEn": "Money Coming",
                    "img": "https://ossimg.tashanedc.com/Tashanwin/gamelogo/JILI/51.png",
                    "vendorId": 18,
                    "vendorCode": "JILI",
                    "imgUrl2": null,
                    "customGameType": 0
                },
                {
                    "gameID": "224",
                    "gameNameEn": "Go Rush",
                    "img": "https://ossimg.tashanedc.com/Tashanwin/gamelogo/JILI/224.png",
                    "vendorId": 18,
                    "vendorCode": "JILI",
                    "imgUrl2": null,
                    "customGameType": 0
                },
                {
                    "gameID": "49",
                    "gameNameEn": "Super Ace",
                    "img": "https://ossimg.tashanedc.com/Tashanwin/gamelogo/JILI/49.png",
                    "vendorId": 18,
                    "vendorCode": "JILI",
                    "imgUrl2": null,
                    "customGameType": 0
                },
                {
                    "gameID": "300",
                    "gameNameEn": "Fortune Gems 3",
                    "img": "https://ossimg.tashanedc.com/Tashanwin/gamelogo/JILI/300.png",
                    "vendorId": 18,
                    "vendorCode": "JILI",
                    "imgUrl2": null,
                    "customGameType": 0
                },
                {
                    "gameID": "229",
                    "gameNameEn": "Mines",
                    "img": "https://ossimg.tashanedc.com/Tashanwin/gamelogo/JILI/229.png",
                    "vendorId": 18,
                    "vendorCode": "JILI",
                    "imgUrl2": null,
                    "customGameType": 0
                },
                {
                    "gameID": "324",
                    "gameNameEn": "Nightfall Hunting",
                    "img": "https://ossimg.tashanedc.com/Tashanwin/gamelogo/JILI/324.png",
                    "vendorId": 18,
                    "vendorCode": "JILI",
                    "imgUrl2": null,
                    "customGameType": 0
                },
                {
                    "gameID": "403",
                    "gameNameEn": "Super Ace Deluxe",
                    "img": "https://ossimg.tashanedc.com/Tashanwin/gamelogo/JILI/403.png",
                    "vendorId": 18,
                    "vendorCode": "JILI",
                    "imgUrl2": null,
                    "customGameType": 0
                }
            ]
        }
    ],
    "code": 0,
    "msg": "Succeed",
    "msgCode": 0,
    "serviceNowTime": "' . $serviceNowTimeFormatted . '"
}';

$data = json_decode($jsonData, true);

$response = json_encode($data, JSON_PRETTY_PRINT);

header('Content-Type: application/json');
echo $response;

?>