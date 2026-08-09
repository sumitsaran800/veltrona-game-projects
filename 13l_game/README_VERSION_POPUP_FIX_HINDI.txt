Problem: popup "A new version is available. Please refresh the page." version.json mismatch ki wajah se aa raha tha.

Fix files:
1) /version.json ka version window.__VERSION__ ke same rakha gaya hai.
2) /js/index-xnhGKCfe.js me force alert/reload disabled kiya gaya hai.

Upload steps:
- public_html/version.json replace karo.
- public_html/js/index-xnhGKCfe.js replace karo.
- Browser me Application > Service Workers > Unregister karo.
- Application > Storage > Clear site data karo.
- Ctrl + Shift + R se hard refresh karo.
