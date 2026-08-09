13L WinGo Fix v2 - Deploy Steps
================================

Problem kya tha:
1) WinGo click par game page (/WinGo/WinGo_30S) open hona chahiye tha, lekin raw issue API (/webapi/v/issue/WinGo_30S) ya sirf ek JSON file load ho rahi thi.
2) Kuch build /webapi/kv/issue use karta hai aur kuch /webapi/v/issue. Pehle .htaccess me sirf kv route tha.
3) ThirdGame/GetGameUrl lottery ke liye proper app-origin Token URL nahi de raha tha, isliye frontend lottery route me properly enter nahi kar pa raha tha.
4) Browser/PWA cache old JS chala sakta hai.

Is zip me kya fix hai:
- /webapi/kv/issue/WinGo_30S and /webapi/v/issue/WinGo_30S dono supported.
- ThirdGame/GetGameUrl.php lottery ke liye current domain ka Token URL return karega.
- Frontend lottery APIs local domain par chalenge: /api/Lottery/... and /WinGo/...json.
- Missing/legacy lottery aliases add: GetmyEmeralds, GetmyIssusPage, GetMyGameRecordPageList, GameBetting, etc.
- JS/CSS no-cache testing headers added.

Upload:
1) Zip extract karo.
2) public_html ke andar sab files replace/upload karo.
3) phpMyAdmin me database.sql import karo.
4) api/_core/config.php me DB_HOST, DB_USER, DB_PASS, DB_NAME sahi karo.
5) Browser me hard refresh karo: Ctrl + Shift + R.
6) Chrome Application tab > Service Workers > Unregister karo, Storage > Clear site data karo.
7) Test URL:
   https://yourdomain.com/api/Home/GetHomeAllGameList
   https://yourdomain.com/api/ThirdGame/GetGameUrl?vendorCode=ARLottery&gameCode=WinGo_30S
   https://yourdomain.com/webapi/v/issue/WinGo_30S
   https://yourdomain.com/webapi/kv/issue/WinGo_30S
   https://yourdomain.com/WinGo/WinGo_30S

Expected:
- WinGo click par URL /WinGo/WinGo_30S hona chahiye.
- Network me sirf ek issue API nahi, balki /api/Lottery/GetGameInfo, GetBetLimit, GetHistoryIssuePage, GetRecordPage, GetTrendStatistics etc. bhi load hone chahiye.

Note:
Real-money gateway/settlement add nahi hai. Ye project/demo backend hai.
