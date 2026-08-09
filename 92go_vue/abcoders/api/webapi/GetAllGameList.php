<?php
header("Access-Control-Allow-Origin: http://diuvin.shop");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('Asia/Kolkata');
$serviceNowTimeFormatted = date('Y-m-d H:i:s');

$jsonData = '{
    "data": {
        "popular": {
            "platformList":  [
                {
                    "vendorId": "23",
                    "vendorCode": "TB_Chess",
                    "gameCode": "800",
                    "gameNameEn": "Aviator",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/800.png",
                    "imgUrl2": "",
                    "winOdds": 97.52
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "51",
                    "gameNameEn": "Money Coming",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/51.png",
                    "imgUrl2": "",
                    "winOdds": 97.62
                },
                {
                    "vendorId": "23",
                    "vendorCode": "TB_Chess",
                    "gameCode": "110",
                    "gameNameEn": "Limbo",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/110.png",
                    "imgUrl2": "",
                    "winOdds": 96.29
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "224",
                    "gameNameEn": "Go Rush",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/224.png",
                    "imgUrl2": "",
                    "winOdds": 96.21
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "82",
                    "gameNameEn": "Happy Fishing",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/82.png",
                    "imgUrl2": "",
                    "winOdds": 97.53
                },
                {
                    "vendorId": "17",
                    "vendorCode": "EVO_Electronic",
                    "gameCode": "cupcakesf1000000",
                    "gameNameEn": "Cupcakes",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/EVO_Electronic/cupcakesf1000000.png",
                    "imgUrl2": "",
                    "winOdds": 97.34
                }
            ],
            "clicksTopList": [
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "51",
                    "gameNameEn": "Money Coming",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/51.png",
                    "imgUrl2": "",
                    "winOdds": 97.62
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "109",
                    "gameNameEn": "Fortune Gems",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/109.png",
                    "imgUrl2": "",
                    "winOdds": 96.48
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "223",
                    "gameNameEn": "Fortune Gems 2",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/223.png",
                    "imgUrl2": "",
                    "winOdds": 96.64
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "200",
                    "gameNameEn": "Pappu",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/200.png",
                    "imgUrl2": "",
                    "winOdds": 96.23
                },
                {
                    "vendorId": "2",
                    "vendorCode": "CQ9",
                    "gameCode": "19",
                    "gameNameEn": "Hot Spin",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/CQ9/19.png",
                    "imgUrl2": "",
                    "winOdds": 97.30
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "32",
                    "gameNameEn": "Jack Pot Fishing",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/32.png",
                    "imgUrl2": "",
                    "winOdds": 96.33
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "82",
                    "gameNameEn": "Happy Fishing",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/82.png",
                    "imgUrl2": "",
                    "winOdds": 97.53
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "197",
                    "gameNameEn": "Color Game",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/197.png",
                    "imgUrl2": "",
                    "winOdds": 97.11
                },
                {
                    "vendorId": "4",
                    "vendorCode": "MG",
                    "gameCode": "SMG_wildfireWins",
                    "gameNameEn": "Wildfire Wins",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/MG/SMG_wildfireWins.png",
                    "imgUrl2": "",
                    "winOdds": 97.30
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "49",
                    "gameNameEn": "Super Ace",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/49.png",
                    "imgUrl2": "",
                    "winOdds": 96.03
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "35",
                    "gameNameEn": "Crazy777",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/35.png",
                    "imgUrl2": "",
                    "winOdds": 97.84
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "74",
                    "gameNameEn": "Mega Fishing",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/74.png",
                    "imgUrl2": "",
                    "winOdds": 97.36
                },
                {
                    "vendorId": "2",
                    "vendorCode": "CQ9",
                    "gameCode": "AT01",
                    "gameNameEn": "OneShotFishing",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/CQ9/AT01.png",
                    "imgUrl2": "",
                    "winOdds": 96.38
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "47",
                    "gameNameEn": "Charge Buffalo",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/47.png",
                    "imgUrl2": "",
                    "winOdds": 97.37
                },
                {
                    "vendorId": "6",
                    "vendorCode": "JDB",
                    "gameCode": "9013",
                    "gameNameEn": "Galaxy Burst",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JDB/9013.png",
                    "imgUrl2": "",
                    "winOdds": 97.72
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "103",
                    "gameNameEn": "Golden Empire",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/103.png",
                    "imgUrl2": "",
                    "winOdds": 96.91
                },
                {
                    "vendorId": "2",
                    "vendorCode": "CQ9",
                    "gameCode": "AT05",
                    "gameNameEn": "LuckyFishing",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/CQ9/AT05.png",
                    "imgUrl2": "",
                    "winOdds": 96.50
                },
                {
                    "vendorId": "18",
                    "vendorCode": "JILI",
                    "gameCode": "23",
                    "gameNameEn": "Candy Baby",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/23.png",
                    "imgUrl2": "",
                    "winOdds": 97.22
                }
            ],
            "clicksVideoTopList": [
                {
                    "vendorId": "16",
                    "vendorCode": "EVO_Video",
                    "gameCode": "o735cjzyaeasv4o6",
                    "gameNameEn": "Blackjack VIP 3",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/EVO_Video/o735cjzyaeasv4o6.png",
                    "imgUrl2": "",
                    "winOdds": 0.0
                },
                {
                    "vendorId": "16",
                    "vendorCode": "EVO_Video",
                    "gameCode": "olbibp3fylzaxvhb",
                    "gameNameEn": "Salon Privé Blackjack B",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/EVO_Video/olbibp3fylzaxvhb.png",
                    "imgUrl2": "",
                    "winOdds": 0.0
                },
                {
                    "vendorId": "16",
                    "vendorCode": "EVO_Video",
                    "gameCode": "o735fhvsaeaswamh",
                    "gameNameEn": "Blackjack VIP 6",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/EVO_Video/o735fhvsaeaswamh.png",
                    "imgUrl2": "",
                    "winOdds": 0.0
                },
                {
                    "vendorId": "27",
                    "vendorCode": "SEXY_Video",
                    "gameCode": "MX-LIVE-006",
                    "gameNameEn": "DragonTiger",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SEXY_Video/MX-LIVE-006.png",
                    "imgUrl2": "",
                    "winOdds": 0.0
                },
                {
                    "vendorId": "16",
                    "vendorCode": "EVO_Video",
                    "gameCode": "o735ggd5iwsswcz7",
                    "gameNameEn": "Blackjack VIP 7",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/EVO_Video/o735ggd5iwsswcz7.png",
                    "imgUrl2": "",
                    "winOdds": 0.0
                },
                {
                    "vendorId": "16",
                    "vendorCode": "EVO_Video",
                    "gameCode": "o735hfcqauecwjxp",
                    "gameNameEn": "Blackjack VIP 8",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/EVO_Video/o735hfcqauecwjxp.png",
                    "imgUrl2": "",
                    "winOdds": 0.0
                },
                {
                    "vendorId": "7",
                    "vendorCode": "DG",
                    "gameCode": "1_3",
                    "gameNameEn": "DragonTiger",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/DG/1_3.png",
                    "imgUrl2": "",
                    "winOdds": 0.0
                },
                {
                    "vendorId": "27",
                    "vendorCode": "SEXY_Video",
                    "gameCode": "MX-LIVE-003",
                    "gameNameEn": "Baccarat Insurance",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SEXY_Video/MX-LIVE-003.png",
                    "imgUrl2": "",
                    "winOdds": 0.0
                },
                {
                    "vendorId": "27",
                    "vendorCode": "SEXY_Video",
                    "gameCode": "MX-LIVE-001",
                    "gameNameEn": "Baccarat Classic",
                    "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SEXY_Video/MX-LIVE-001.png",
                    "imgUrl2": "",
                    "winOdds": 0.0
                }
            ]
        },
        "sport": [
            {
                "slotsTypeID": 25,
                "slotsName": "Wickets9",
                "vendorId": 25,
                "vendorCode": "Wickets9",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_202406201501149ms9.png"
            },
            {
                "slotsTypeID": 8,
                "slotsName": "CMD",
                "vendorId": 8,
                "vendorCode": "CMD",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_202406201521086f51.png"
            },
            {
                "slotsTypeID": 14,
                "slotsName": "SaBa",
                "vendorId": 14,
                "vendorCode": "SaBa",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_202406201500542k79.png"
            }
        ],
        "video": [
            {
                "slotsTypeID": 16,
                "slotsName": "EVO_Video",
                "vendorId": 16,
                "vendorCode": "EVO_Video",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_202406261655538a91.png"
            },
            {
                "slotsTypeID": 7,
                "slotsName": "DG",
                "vendorId": 7,
                "vendorCode": "DG",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_2024062616560099q7.png"
            },
            {
                "slotsTypeID": 26,
                "slotsName": "WM_Video",
                "vendorId": 26,
                "vendorCode": "WM_Video",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_202406261655355xtc.png"
            },
            {
                "slotsTypeID": 27,
                "slotsName": "SEXY_Video",
                "vendorId": 27,
                "vendorCode": "SEXY_Video",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240626165544vcaf.png"
            },
            {
                "slotsTypeID": 38,
                "slotsName": "MG_Video",
                "vendorId": 38,
                "vendorCode": "MG_Video",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240626165503tejh.png"
            }
        ],
        "slot": [
            {
                "slotsTypeID": 18,
                "slotsName": "JILI",
                "vendorId": 18,
                "vendorCode": "JILI",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240620091320492c.png"
            },
            {
                "slotsTypeID": 2,
                "slotsName": "CQ9",
                "vendorId": 2,
                "vendorCode": "CQ9",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240620091417crd8.png"
            },
            {
                "slotsTypeID": 4,
                "slotsName": "MG",
                "vendorId": 4,
                "vendorCode": "MG",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240620091425c216.png"
            },
            {
                "slotsTypeID": 6,
                "slotsName": "JDB",
                "vendorId": 6,
                "vendorCode": "JDB",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240620091343y15o.png"
            },
            {
                "slotsTypeID": 5,
                "slotsName": "PG",
                "vendorId": 5,
                "vendorCode": "PG",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240620091256x3kg.png"
            },
            {
                "slotsTypeID": 17,
                "slotsName": "EVO_Electronic",
                "vendorId": 17,
                "vendorCode": "EVO_Electronic",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240620091313tcom.png"
            },
            {
                "slotsTypeID": 41,
                "slotsName": "G9",
                "vendorId": 41,
                "vendorCode": "G9",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20241014015240o1ui.png"
            },
            {
                "slotsTypeID": 37,
                "slotsName": "MG_Fish",
                "vendorId": 37,
                "vendorCode": "MG_Fish",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240620091219gmaa.png"
            }
        ],
        "chess": [
            {
                "slotsTypeID": 21,
                "slotsName": "V8Card",
                "vendorId": 21,
                "vendorCode": "V8Card",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240620145952prhc.png"
            },
            {
                "slotsTypeID": 19,
                "slotsName": "Card365",
                "vendorId": 19,
                "vendorCode": "Card365",
                "state": 1,
                "vendorImg": "https://ossimg.yuk87k786d.com/sikkim/vendorlogo/vendorlogo_20240620145936heyd.png"
            }
        ],
        "fish": [
            {
                "gameID": "32",
                "gameNameEn": "Jack Pot Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/32.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "82",
                "gameNameEn": "Happy Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/82.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "74",
                "gameNameEn": "Mega Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/74.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "AT01",
                "gameNameEn": "OneShotFishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/CQ9/AT01.png",
                "vendorId": 2,
                "vendorCode": "CQ9",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "AT05",
                "gameNameEn": "LuckyFishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/CQ9/AT05.png",
                "vendorId": 2,
                "vendorCode": "CQ9",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "GO02",
                "gameNameEn": "Hero Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/CQ9/GO02.png",
                "vendorId": 2,
                "vendorCode": "CQ9",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "AB3",
                "gameNameEn": "Paradise",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/CQ9/AB3.png",
                "vendorId": 2,
                "vendorCode": "CQ9",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "42",
                "gameNameEn": "Dinosaur Tycoon",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/42.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "60",
                "gameNameEn": "Dragon Fortune",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/60.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "71",
                "gameNameEn": "Boom Legend",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/71.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "SFG_WDFuWaFishing",
                "gameNameEn": "WD FuWa Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/MG_Fish/SFG_WDFuWaFishing.png",
                "vendorId": 37,
                "vendorCode": "MG_Fish",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "SFG_WDGoldBlastFishing",
                "gameNameEn": "WD Gold Blast Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/MG_Fish/SFG_WDGoldBlastFishing.png",
                "vendorId": 37,
                "vendorCode": "MG_Fish",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "SFG_WDGoldenFortuneFishing",
                "gameNameEn": "WD Golden Fortune Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/MG_Fish/SFG_WDGoldenFortuneFishing.png",
                "vendorId": 37,
                "vendorCode": "MG_Fish",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "SFG_WDGoldenFuwaFishing",
                "gameNameEn": "WD Golden Fuwa Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/MG_Fish/SFG_WDGoldenFuwaFishing.png",
                "vendorId": 37,
                "vendorCode": "MG_Fish",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "SFG_WDGoldenTyrantFishing",
                "gameNameEn": "WD Golden Tyrant Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/MG_Fish/SFG_WDGoldenTyrantFishing.png",
                "vendorId": 37,
                "vendorCode": "MG_Fish",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "SFG_WDMerryIslandFishing",
                "gameNameEn": "WD Merry Island Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/MG_Fish/SFG_WDMerryIslandFishing.png",
                "vendorId": 37,
                "vendorCode": "MG_Fish",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "510",
                "gameNameEn": "Fishing Wars",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/V8Card/510.png",
                "vendorId": 21,
                "vendorCode": "V8Card",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "1",
                "gameNameEn": "Royal Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/1.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "119",
                "gameNameEn": "All-star Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/119.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "20",
                "gameNameEn": "Bombing Fishing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/20.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "212",
                "gameNameEn": "Dinosaur Tycoon II",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/212.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            }
        ],
        "flash": [
            {
                "gameID": "800",
                "gameNameEn": "Aviator",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/800.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "801",
                "gameNameEn": "Aviator-1Min",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/801.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "810",
                "gameNameEn": "Cricket",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/810.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "900",
                "gameNameEn": "Keno80",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/900.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "100",
                "gameNameEn": "Mines",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/100.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "110",
                "gameNameEn": "Limbo",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/110.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "224",
                "gameNameEn": "Go Rush",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/224.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "229",
                "gameNameEn": "Mines",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/229.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "236",
                "gameNameEn": "Wheel",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/236.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "232",
                "gameNameEn": "Tower",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/232.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "803",
                "gameNameEn": "Aviator-5Min",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/803.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "802",
                "gameNameEn": "Aviator-3Min",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/802.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "902",
                "gameNameEn": "WinGo Pro",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/902.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "105",
                "gameNameEn": "Goal",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/105.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "811",
                "gameNameEn": "Mines Pro",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/811.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "500",
                "gameNameEn": "Bomb Wave",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/500.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "mines",
                "gameNameEn": "Mines",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SPRIBE/22005.png",
                "vendorId": 20,
                "vendorCode": "SPRIBE",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "113",
                "gameNameEn": "Pharaoh",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/113.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "115",
                "gameNameEn": "TeenPatti",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/115.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "114",
                "gameNameEn": "HorseRacing",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/114.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "112",
                "gameNameEn": "Triple",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/112.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "111",
                "gameNameEn": "Cryptos",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/111.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "109",
                "gameNameEn": "Coinflip",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/109.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "107",
                "gameNameEn": "Hotline",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/107.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "106",
                "gameNameEn": "Keno",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/106.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "104",
                "gameNameEn": "Roulette",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/104.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "103",
                "gameNameEn": "Plinko",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/103.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "102",
                "gameNameEn": "Dice",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/102.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "101",
                "gameNameEn": "Hilo",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/101.png",
                "vendorId": 23,
                "vendorCode": "TB_Chess",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "hi-lo",
                "gameNameEn": "Hi Lo",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SPRIBE/22006.png",
                "vendorId": 20,
                "vendorCode": "SPRIBE",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "keno",
                "gameNameEn": "Keno",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SPRIBE/22007.png",
                "vendorId": 20,
                "vendorCode": "SPRIBE",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "mini-roulette",
                "gameNameEn": "Mini Roulette",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SPRIBE/22008.png",
                "vendorId": 20,
                "vendorCode": "SPRIBE",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "9009",
                "gameNameEn": "King Of Football",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JDB/9009.png",
                "vendorId": 6,
                "vendorCode": "JDB",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "9014",
                "gameNameEn": "Mines",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JDB/9014.png",
                "vendorId": 6,
                "vendorCode": "JDB",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "9016",
                "gameNameEn": "Goal",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JDB/9016.png",
                "vendorId": 6,
                "vendorCode": "JDB",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "233",
                "gameNameEn": "HILO",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/233.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "235",
                "gameNameEn": "Limbo",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/235.png",
                "vendorId": 18,
                "vendorCode": "JILI",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "aviator",
                "gameNameEn": "Aviator",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SPRIBE/22001.png",
                "vendorId": 20,
                "vendorCode": "SPRIBE",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "dice",
                "gameNameEn": "Dice",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SPRIBE/22002.png",
                "vendorId": 20,
                "vendorCode": "SPRIBE",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "goal",
                "gameNameEn": "Goal",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SPRIBE/22003.png",
                "vendorId": 20,
                "vendorCode": "SPRIBE",
                "imgUrl2": "",
                "customGameType": 0
            },
            {
                "gameID": "plinko",
                "gameNameEn": "Plinko",
                "img": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SPRIBE/22004.png",
                "vendorId": 20,
                "vendorCode": "SPRIBE",
                "imgUrl2": "",
                "customGameType": 0
            }
        ],
        "lottery": [
            {
                "id": 1,
                "categoryCode": "Win Go",
                "categoryName": "WinGo彩票",
                "state": 1,
                "sort": 55,
                "categoryImg": "https://ossimg.92gopay.com/92GO/lotterycategory/lotterycategory_20250306171906f5xx.png",
                "wingoAmount": null,
                "k3Amount": null,
                "fiveDAmount": null,
                "trxWingoAmount": null
            },
            {
                "id": 2,
                "categoryCode": "K3",
                "categoryName": "K3彩票",
                "state": 1,
                "sort": 44,
                "categoryImg": "https://ossimg.92gopay.com/92GO/lotterycategory/lotterycategory_20250306171913mhu1.png",
                "wingoAmount": null,
                "k3Amount": null,
                "fiveDAmount": null,
                "trxWingoAmount": null
            },
            {
                "id": 3,
                "categoryCode": "5D",
                "categoryName": "5D彩票",
                "state": 1,
                "sort": 33,
                "categoryImg": "https://ossimg.92gopay.com/92GO/lotterycategory/lotterycategory_20250306171919ojeb.png",
                "wingoAmount": null,
                "k3Amount": null,
                "fiveDAmount": null,
                "trxWingoAmount": null
            },
            {
                "id": 41,
                "categoryCode": "Trx Win Go",
                "categoryName": "TrxWinGo彩票",
                "state": 1,
                "sort": 22,
                "categoryImg": "https://ossimg.92gopay.com/92GO/lotterycategory/lotterycategory_20250430153800oqwb.png",
                "wingoAmount": null,
                "k3Amount": null,
                "fiveDAmount": null,
                "trxWingoAmount": null
            }
        ],
        "awardRecordList": [
            {
                "orderId": 17267,
                "userId": 3001223,
                "userPhoto": "1",
                "userName": "917501215569",
                "gameName": "Money Coming",
                "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/51.png",
                "imgUrl2": "",
                "multiple": 20.00,
                "bonusAmount": 100.00,
                "multipleName": "20-50",
                "createTime": "2025-01-15 12:09:00"
            },
            {
                "orderId": 17266,
                "userId": 3001223,
                "userPhoto": "1",
                "userName": "917501215569",
                "gameName": "Money Coming",
                "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/51.png",
                "imgUrl2": "",
                "multiple": 20.00,
                "bonusAmount": 100.00,
                "multipleName": "20-50",
                "createTime": "2025-01-15 12:06:00"
            },
            {
                "orderId": 17265,
                "userId": 3001223,
                "userPhoto": "1",
                "userName": "917501215569",
                "gameName": "Money Coming",
                "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/51.png",
                "imgUrl2": "",
                "multiple": 20.00,
                "bonusAmount": 100.00,
                "multipleName": "20-50",
                "createTime": "2025-01-15 12:06:00"
            },
            {
                "orderId": 17264,
                "userId": 2344973,
                "userPhoto": "9",
                "userName": "918603687299",
                "gameName": "Mines",
                "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JILI/229.png",
                "imgUrl2": "",
                "multiple": 23.25,
                "bonusAmount": 100.00,
                "multipleName": "20-50",
                "createTime": "2025-01-15 12:00:00"
            },
            {
                "orderId": 17263,
                "userId": 2874668,
                "userPhoto": "1",
                "userName": "919576090157",
                "gameName": "Cricket",
                "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/810.png",
                "imgUrl2": "",
                "multiple": 36.46,
                "bonusAmount": 100.00,
                "multipleName": "20-50",
                "createTime": "2025-01-15 11:12:00"
            },
            {
                "orderId": 17262,
                "userId": 2449458,
                "userPhoto": "1",
                "userName": "918813857002",
                "gameName": "Aviator",
                "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/800.png",
                "imgUrl2": "",
                "multiple": 22.77,
                "bonusAmount": 100.00,
                "multipleName": "20-50",
                "createTime": "2025-01-15 10:57:00"
            },
            {
                "orderId": 17261,
                "userId": 2449458,
                "userPhoto": "1",
                "userName": "918813857002",
                "gameName": "Aviator",
                "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/800.png",
                "imgUrl2": "",
                "multiple": 31.08,
                "bonusAmount": 100.00,
                "multipleName": "20-50",
                "createTime": "2025-01-15 10:45:00"
            },
            {
                "orderId": 17260,
                "userId": 3151496,
                "userPhoto": "1",
                "userName": "919797869157",
                "gameName": "Aviator",
                "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/TB_Chess/800.png",
                "imgUrl2": "",
                "multiple": 21.29,
                "bonusAmount": 100.00,
                "multipleName": "20-50",
                "createTime": "2025-01-15 10:45:00"
            },
            {
                "orderId": 17259,
                "userId": 1597295,
                "userPhoto": "1",
                "userName": "918135854497",
                "gameName": "King Of Football",
                "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/JDB/9009.png",
                "imgUrl2": "",
                "multiple": 38.00,
                "bonusAmount": 100.00,
                "multipleName": "20-50",
                "createTime": "2025-01-15 09:27:00"
            },
            {
                "orderId": 17258,
                "userId": 1736750,
                "userPhoto": "1",
                "userName": "918433089021",
                "gameName": "Aviator",
                "imgUrl": "https://ossimg.yuk87k786d.com/sikkim/gamelogo/SPRIBE/22001.png",
                "imgUrl2": "",
                "multiple": 30.55,
                "bonusAmount": 100.00,
                "multipleName": "20-50",
                "createTime": "2025-01-15 09:24:00"
            }
        ]
    },
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