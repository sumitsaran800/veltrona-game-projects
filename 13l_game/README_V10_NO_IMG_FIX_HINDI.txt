V10 fix by AI

Is zip me sab folder/files hain, lekin img/ folder intentionally include nahi hai.
Aapka existing img folder cPanel me rehne do.

Fixes:
- Missing NavBar JS/CSS chunk added.
- Deposit/Pay order CSS added from provided code.
- Recharge APIs + order detail flow fixed.
- Admin me Payment Methods / Multiple UPI manage tab added.
- Banner API/static responses preserved. Banner images /img folder me manual upload karne hain.

Upload:
1) public_html/domain root me extract/overwrite karo
2) img folder replace mat karo
3) /install.php run karo
4) admin: /admin username admin password admin123
5) cache clear + service worker unregister karo
