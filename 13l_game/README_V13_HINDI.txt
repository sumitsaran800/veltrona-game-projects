13L V13 PATCH - NO IMG FOLDER

Is zip me img/ folder include nahi hai. Aapka cPanel wala existing img/ folder same rehne do.

Fix:
1. arUpiV2 deposit page me UTR submit box add.
2. UTR submit hone par recharge_orders status PendingReview ho jayega.
3. Admin panel Deposit Orders me UTR, submit time aur approve/reject option.
4. Withdraw account user-isolated hai, dusre user ka account show nahi hoga.
5. One account one active session logic v12 se preserve.
6. Invite Wheel first/free spin reward 0.10 se 3.00 ke beech default.
7. Admin se free spin amount/probability edit.
8. Admin se wheel min withdraw, free spins, invite recharge, expiry, turnover x manage.
9. Admin me Site Settings: captcha on/off, popup on/off, maintenance, home on/off, gift on/off, min withdraw.
10. Gift code manage + claim history.
11. Payment / UPI multiple method manage.
12. Admin UI grouped sidebar + svg icons.

Upload:
1. Zip ko domain root me extract/replace karo.
2. img folder ko delete/replace mat karna.
3. Run: https://31.55ak.xyz/install.php
4. Admin: https://31.55ak.xyz/admin
5. Login: admin / admin123

Important PHP files:
- api/_router.php : API route logic
- api/_core/bootstrap.php : DB auto install/migrations/settings
- admin/index.php : Admin panel
- js/utr-submit-v13.js : arUpiV2 UTR submit frontend helper

Note: Payment manual/admin approval mode hai. Real UPI gateway auto-confirm connect nahi kiya.
