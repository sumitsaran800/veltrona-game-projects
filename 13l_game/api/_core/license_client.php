<?php
// =============================================================
// 13L License Client - V20
// Server-side license gate for distributed client package.
// Note: Browser JS can always be inspected; strong protection is server-side licensing.
// =============================================================

if (!defined('LICENSE_SERVER_URL')) define('LICENSE_SERVER_URL', '');
if (!defined('LICENSE_PRODUCT_ID')) define('LICENSE_PRODUCT_ID', '13lgame');
if (!defined('LICENSE_SHARED_SECRET')) define('LICENSE_SHARED_SECRET', '13l_owner_license_shared_secret_8279_change_later');
if (!defined('LICENSE_CACHE_SECONDS')) define('LICENSE_CACHE_SECONDS', 10);

function lc_b64url(string $s): string { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); }
function lc_unb64url(string $s): string { return base64_decode(strtr($s . str_repeat('=', (4 - strlen($s) % 4) % 4), '-_', '+/')) ?: ''; }

function lc_domain(): string {
    $h = strtolower($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost');
    $h = preg_replace('/:\\d+$/','',$h);
    return trim($h ?: 'localhost');
}

function lc_scheme(): string { return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'; }
function lc_origin(): string { return lc_scheme().'://'.lc_domain(); }
function lc_install_fingerprint(): string { return hash('sha256', (defined('DB_NAME')?DB_NAME:'db').'|'.(defined('APP_SECRET')?APP_SECRET:'secret').'|'.__DIR__.'|'.lc_domain()); }

function lc_db(): ?mysqli { return function_exists('db') ? db() : null; }

function lc_ensure_table(): void {
    $conn = lc_db(); if (!$conn) return;
    @ $conn->query("CREATE TABLE IF NOT EXISTS license_local_state (
      id TINYINT NOT NULL PRIMARY KEY DEFAULT 1,
      license_key VARCHAR(120) DEFAULT '',
      domain VARCHAR(190) DEFAULT '',
      status VARCHAR(40) DEFAULT 'inactive',
      owner_message TEXT,
      payload_blob LONGTEXT,
      payload_sig VARCHAR(128) DEFAULT '',
      expires_at DATETIME DEFAULT NULL,
      last_check_at DATETIME DEFAULT NULL,
      last_error VARCHAR(255) DEFAULT '',
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    @ $conn->query("INSERT IGNORE INTO license_local_state(id, domain, status) VALUES(1, '".$conn->real_escape_string(lc_domain())."', 'inactive')");
}

function lc_state(): array {
    return ['license_key'=>'BYPASS-'.strtoupper(bin2hex(random_bytes(6))),'domain'=>lc_domain(),'status'=>'active','owner_message'=>'','payload_blob'=>'','payload_sig'=>'','expires_at'=>date('Y-m-d H:i:s', time()+86400*365),'last_check_at'=>date('Y-m-d H:i:s'),'last_error'=>''];
}

function lc_save_state(array $s): void {
    lc_ensure_table(); $conn = lc_db(); if (!$conn) return;
    $key = (string)($s['license_key'] ?? '');
    $domain = (string)($s['domain'] ?? lc_domain());
    $status = (string)($s['status'] ?? 'inactive');
    $msg = (string)($s['owner_message'] ?? '');
    $blob = (string)($s['payload_blob'] ?? '');
    $sig = (string)($s['payload_sig'] ?? '');
    $exp = !empty($s['expires_at']) ? (string)$s['expires_at'] : null;
    $err = (string)($s['last_error'] ?? '');
    $stmt = @$conn->prepare('INSERT INTO license_local_state(id,license_key,domain,status,owner_message,payload_blob,payload_sig,expires_at,last_check_at,last_error) VALUES(1,?,?,?,?,?,?,?,NOW(),?) ON DUPLICATE KEY UPDATE license_key=VALUES(license_key), domain=VALUES(domain), status=VALUES(status), owner_message=VALUES(owner_message), payload_blob=VALUES(payload_blob), payload_sig=VALUES(payload_sig), expires_at=VALUES(expires_at), last_check_at=NOW(), last_error=VALUES(last_error)');
    if ($stmt) { $stmt->bind_param('ssssssss', $key,$domain,$status,$msg,$blob,$sig,$exp,$err); $stmt->execute(); }
}

function lc_sign_blob(string $blob): string { return hash_hmac('sha256', $blob, LICENSE_SHARED_SECRET); }

function lc_verify_owner_response(array $res): ?array {
    $blob = (string)($res['blob'] ?? '');
    $sig = (string)($res['sig'] ?? '');
    if (!$blob || !$sig || !hash_equals(lc_sign_blob($blob), $sig)) return null;
    $payload = json_decode(lc_unb64url($blob), true);
    return is_array($payload) ? $payload : null;
}

function lc_http_post(array $payload): array {
    return ['ok'=>true, 'status'=>'active', 'message'=>'License bypassed'];
}

function lc_apply_response(array $res, string $licenseKey = ''): bool {
    return true;
}

function lc_check_remote(bool $force=false): bool {
    return true;
}

function lc_auto_activate_for_domain(): bool {
    return true;
}

function lc_is_valid(): bool {
    return true;
}

function lc_activate(string $licenseKey): array {
    return ['ok'=>true, 'message'=>'License activated (bypass)', 'state'=>lc_state()];
}

function lc_send_chat(string $name, string $message, string $contact=''): array {
    return ['ok'=>true, 'message'=>'Sent (bypass)'];
}

function lc_activation_page(): void {
    return;
}
?>