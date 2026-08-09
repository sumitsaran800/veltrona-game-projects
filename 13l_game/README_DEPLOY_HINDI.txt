13L GAME Backend FIXED v4 AUTO INSTALL

Bhai is zip me ye fix kiya gaya hai:
1) api/ThirdGame/GetGameUrl ab WinGo ke liye direct /WinGo/WinGo_30S route return karega.
2) api/ThirdGame/Transfer ka response original jaisa: {code,msg,msgCode,serverTime} without data.
3) api/Home/CheckCanBet response original jaisa: {data:true,code,msg,msgCode,serverTime}.
4) webapi/kv/issue/WinGo_30S aur webapi/v/issue/WinGo_30S dynamic issue return karenge.
5) /WinGo/WinGo_30S.json aur /WinGo/WinGo_30S/GetHistoryIssuePage.json local backend se chalenge.
6) api/_core/config.php me DB name/user/pass set hai: rflrovxl_133lgameluckywebd
7) AUTO_INSTALL_TABLES true hai, pehli API request par tables automatic create ho jayengi.
8) Jo local image missing references the unke liye placeholder files/fallback add kiya hai, taki 404 se UI na tute.

UPLOAD STEPS:
1. Is zip ko public_html me extract/replace karo.
2. Browser me open karo: https://YOURDOMAIN/install.php
3. Agar install OK aaye to ye test karo:
   https://YOURDOMAIN/api/Home/CheckCanBet
   https://YOURDOMAIN/api/ThirdGame/Transfer
   https://YOURDOMAIN/webapi/kv/issue/WinGo_30S
   https://YOURDOMAIN/WinGo/WinGo_30S

CACHE CLEAR:
Chrome DevTools -> Application -> Service Workers -> Unregister
Application -> Storage -> Clear site data
Phir Ctrl + Shift + R.

LOGIN:
User mobile: 919119098026
Password: 123456
Admin: /admin
Admin username: admin
Admin password: admin123

NOTE:
Ye project/demo virtual wallet backend hai. Real-money payment gateway/live gambling settlement connect nahi hai.
Font files include nahi kiye gaye hain; browser fallback font use karega.


V7 FIX NOTES:
- WinGo page me ab sirf WinGo 30sec/1Min/3Min/5Min tabs aayenge. K3 page me K3, 5D page me 5D, Moto page me Moto group aayega.
- Recharge API added: /api/Recharge/GetRechargeCategoryList, /api/Recharge/GetRechargeBasicInfo, /api/Recharge/DepositRecharge, /api/Recharge/GetRechargeRecord.
- Recharge orders admin panel se approve karne par wallet me amount + gift credit hota hai.
- Missing images ab fake placeholder se replace nahi hongi. Real image file missing hai to 404 dikh sakta hai; aap original file same path par upload kar sakte ho.
