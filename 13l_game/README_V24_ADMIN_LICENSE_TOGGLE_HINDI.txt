V24 update
- img folder intentionally not included. Keep existing cPanel img folder.
- Admin Site Settings toggles are wired to API guard, not fake UI.
- Maintenance mode blocks user APIs and shows frontend overlay.
- Lottery/Game toggles added: WinGo, K3, 5D, Moto, TRX.
- User game history stays 10 per page.
- Payment/UPI QR fix from V23 kept.
- Owner zip includes license management inside /admin/?tab=owner_license; separate panel is no longer needed.

After upload run /install.php, then clear service worker and site data.
