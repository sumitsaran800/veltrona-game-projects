<?php
require_once __DIR__ . '/api/_core/bootstrap.php';
$conn = db();
header('Content-Type: text/html; charset=utf-8');
if (!$conn) {
    echo '<h2>Database connection failed</h2><p>api/_core/config.php me DB_HOST/DB_NAME/DB_USER/DB_PASS check karo.</p>';
    exit;
}
ensure_database($conn);
// V21 owner license repair
require_once __DIR__ . '/license/_core.php';
ol_ensure();
echo '<h2>13L owner/backend install OK - V21</h2>';
echo '<p>Tables auto-created/updated.</p>';
echo '<p><a href="/api/Home/CheckCanBet">Test CheckCanBet</a></p>';
echo '<p><a href="/webapi/kv/issue/WinGo_30S">Test WinGo issue</a></p>';
echo '<p><a href="/WinGo/WinGo_30S">Open WinGo</a></p>';
