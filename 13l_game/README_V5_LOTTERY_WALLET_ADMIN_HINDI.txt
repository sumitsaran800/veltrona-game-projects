13L GAME - V5 Lottery Wallet/Admin Fix
======================================

Is build me ye fixes add hain:

1) /api/Lottery/WinGoBet 500 error fix
   - Bet request parse hota hai.
   - User balance check hota hai.
   - Stake + fee wallet se debit hota hai.
   - Win hone par payout wallet me credit hota hai.
   - Loss hone par stake cut hota hai.
   - Bet history lottery_bets me save hoti hai.
   - Win/Loss financial_records me save hota hai.

2) Win/Loss result logic
   - WinGo / TrxWinGo: number, color, big/small
   - K3: sum/number/basic match
   - 5D: digit/basic match
   - MotoRace: first number/basic match

3) Admin se manage
   URL: /admin
   Login: admin / admin123

   Admin panel me section milega:
   "Lottery Win/Loss & Payout Manage"

   Fields:
   - Game Code: WinGo_30S, WinGo_1Min, WinGo_3Min, WinGo_5Min, K3_1Min, 5D_1Min, MotoRace_1Min, TrxWinGo_1Min etc.
   - Win %: auto mode me kitna chance win ka.
   - Force Mode: auto / win / lose
   - Force Result:
       WinGo: 0-9
       K3: 1,2,3
       5D: 12345
       MotoRace: 1,2,3,4,5,6,7,8,9,10
   - Fee %
   - Payout Number / Color / Violet / BigSmall / K3 / 5D / Moto

4) Auto DB install/update
   DB config already set in:
   api/_core/config.php

   DB_NAME: rflrovxl_133lgameluckywebd
   DB_USER: rflrovxl_133lgameluckywebd
   DB_PASS: rflrovxl_133lgameluckywebd

   Upload ke baad open karo:
   /install.php

   Ya koi bhi API hit karte hi AUTO_INSTALL_TABLES=true ki wajah se tables auto create/update ho jayenge.

5) Missing assets
   - JS/CSS folder same rakha gaya hai.
   - Existing img/6007 banner, gamelogo, gamecategory paths same rakhe gaye hain.
   - Missing non-font assets ke local fallback files same path par add kiye gaye hain, taki 404 kam ho.
   - Font files include nahi kiye gaye. CSS ke missing font-face refs remove hain, browser fallback font use karega.

6) Important cache clear
   Chrome DevTools -> Application -> Service Workers -> Unregister
   Chrome DevTools -> Application -> Storage -> Clear site data
   Fir Ctrl + Shift + R hard refresh karo.

Test endpoints:
/api/Home/CheckCanBet
/api/ThirdGame/Transfer
/webapi/kv/issue/WinGo_30S
/webapi/v/issue/WinGo_30S
/api/Lottery/WinGoBet
/api/Lottery/K3Bet
/api/Lottery/D5Bet
/api/Lottery/MotoRaceBet
/api/Lottery/TrxWinGoBet

Note:
Ye demo/virtual wallet backend hai. Real-money payment gateway ya live gambling settlement connect nahi hai.
