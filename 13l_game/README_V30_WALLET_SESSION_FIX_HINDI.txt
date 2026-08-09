V30 WALLET + SESSION SECURITY FIX

Kya fix hua:
1. BankCard par ab sirf BankCard wallets dikhengi. UPI wallet BankCard list me nahi aayega.
2. UPI par sirf UPI wallet dikhengi.
3. GetWalletCodeList ab frontend ke format me name/code list bhejta hai, isliye bank dropdown blank/red nahi rahega.
4. Add wallet me type auto-detect hai: bankCode/IFSC => BankCard, @ wala account => UPI, TRC20 address => USDT.
5. Same account/UPI duplicate bind block kiya gaya hai.
6. Withdraw ke liye 6 digit withdraw password required hai. Agar set nahi hai to frontend pehle set karayega.
7. One ID one device strict session: new login karte hi old token inactive ho jayega.
8. Refresh token rotation add hai. Refresh par new session token + refresh token generate hoga.
9. cPanel Authorization header pass fix .htaccess me add hai.
10. Admin audit logs table add hai; withdraw approve/reject aur wallet actions log honge.

User ke kehne par ye add nahi kiya:
- Bank/UPI bind ke baad 24 hour withdraw lock
- Same IP spam block / rate-limit

Upload steps:
1. Zip extract karke files public_html me upload/replace karo.
2. Agar AUTO_INSTALL_TABLES true hai to DB columns auto add ho jayenge.
3. Agar manual DB use kar rahe ho to database.sql import/update kar do.
4. Browser me Ctrl+Shift+R karo, ya PWA reinstall/cache clear karo.
