V19 Recharge Turntable Fix

Is patch me img/ folder include nahi hai.

Fix:
- /rechargeTurntable page ke missing recharge wheel assets add kiye (assets/images folder me).
- GetUserRechargeWheelInfo API original JS ke expected keys ke sath response deta hai.
- rewardUpAmount 20000 show hoga, task list + wheel reward list real format me aayegi.
- SpinRechargeWheel / history endpoints working.

Upload:
1. Zip root me extract/replace karo.
2. img folder ko delete/replace mat karna.
3. /install.php run karo.
4. Service Worker unregister + Clear site data + Ctrl Shift R.
