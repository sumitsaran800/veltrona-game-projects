13L GAME V6 FIX
================

Is version me ye fixes hain:

1) Placeholder/missing fake images hata diye gaye hain.
   V5 me jo 70/72 byte fake PNG/WEBP placeholders add ho gaye the, unko remove kar diya.
   Jo image file original package me thi wahi rakhi hai. Missing image blank/404 rahegi jab tak real file upload/download na ho.

2) api/Home/GetHomeAllGameList response uploaded original response se replace kiya gaya hai.
   ARLottery paths jaise WinGo_30S, WinGo_1M, K3_1M, D5_1M, MotoRace_1M same response ke hisaab se hain.

3) Bet logic fixed:
   - Bet lagte hi sirf stake + fee wallet se debit hoga.
   - Win amount turant add nahi hoga.
   - Timer khatam hone ke baad jab issue result history/result API me aayega tab pending bet settle hogi.
   - Settlement same issue result se match karega, alag random result se nahi.
   - Win par payout wallet me credit hoga.
   - Loss par extra kuch add nahi hoga, stake pehle hi debit ho chuka hota hai.
   - History/admin me PENDING/WIN/LOSS status dikhega.

4) Admin panel:
   - Payout multiplier, fee %, force result manage kar sakte ho.
   - Force Result empty hoga to system result issue-level par generate karega.
   - Force Result set karoge to wahi result use hoga.

5) Missing assets downloader:
   Root me download_missing_assets.php diya hai.
   Use only if source domain/assets aapke hain ya permission hai.
   Browser me run:
   https://yourdomain.com/download_missing_assets.php?key=8279&limit=300

   Missing URLs manually add karne ke liye root me missing_urls.txt me one path per line paste karo.
   Example:
   /images/ball_8-BPpYUprp.webp

   Downloader font files skip karta hai.
   Download complete hone ke baad security ke liye download_missing_assets.php delete kar dena.

Setup:
1. Zip ko public_html me extract/replace karo.
2. https://yourdomain.com/install.php open karo.
3. Browser cache clear karo:
   DevTools > Application > Service Workers > Unregister
   Application > Storage > Clear site data
   Ctrl + Shift + R

Admin:
https://yourdomain.com/admin
Default: admin / admin123

Note:
Ye demo/virtual wallet backend mode me hai. Real-money payment gateway/live gambling settlement connect nahi hai.
