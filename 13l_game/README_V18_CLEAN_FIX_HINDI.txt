V18 Clean Fix

- img folder include nahi hai. Existing cPanel img folder same rakho.
- Recharge payment methods blank fix: DB empty hone par original static UPI/QR categories fallback.
- Native original recharge history JS/CSS preserve. Submit UTR original remark-btn popup se chalega.
- Extra old UTR injected JS disable.
- Back button fallback fix: direct URL se open page me bhi back/home route chalega.

Upload:
1. Zip root me extract/replace karo.
2. img folder delete/replace mat karna.
3. /install.php run karo.
4. DevTools > Application > Service Workers > Unregister.
5. Application > Storage > Clear site data.
6. Ctrl+Shift+R.
