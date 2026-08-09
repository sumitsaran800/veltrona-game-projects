<?php
// 13L Owner License Server V20
require_once __DIR__ . '/../api/_core/config.php';
require_once __DIR__ . '/../api/_core/bootstrap.php';
if (!defined('LICENSE_SHARED_SECRET')) define('LICENSE_SHARED_SECRET', '13l_owner_license_shared_secret_8279_change_later');
if (!defined('OWNER_ADMIN_USER')) define('OWNER_ADMIN_USER', 'owner');
if (!defined('OWNER_ADMIN_PASS')) define('OWNER_ADMIN_PASS', 'owner8279');

function ol_b64url(string $s): string { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); }
function ol_sign(string $blob): string { return hash_hmac('sha256', $blob, LICENSE_SHARED_SECRET); }
function ol_reply(array $payload): void { header('Content-Type: application/json; charset=utf-8'); $blob=ol_b64url(json_encode($payload,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); echo json_encode(['ok'=>($payload['status']??'')==='active' || !empty($payload['ok']),'blob'=>$blob,'sig'=>ol_sign($blob),'message'=>$payload['message']??''], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); exit; }
function ol_json(): array { $raw=file_get_contents('php://input'); $d=json_decode($raw?:'',true); return is_array($d)?$d:array_merge($_GET,$_POST); }
function ol_domain($d): string { $h=strtolower(trim((string)$d)); $h=preg_replace('/^https?:\/\//','',$h); $h=preg_replace('/\/.*$/','',$h); $h=preg_replace('/:\\d+$/','',$h); return $h; }
function ol_ensure(): mysqli { $conn=db(); if(!$conn) die('DB failed');
  @ $conn->query("CREATE TABLE IF NOT EXISTS owner_licenses (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, license_key VARCHAR(120) NOT NULL, customer_name VARCHAR(190) DEFAULT '', domain VARCHAR(190) DEFAULT '', status ENUM('active','blocked','expired','inactive') NOT NULL DEFAULT 'active', valid_until DATETIME DEFAULT NULL, max_domains INT NOT NULL DEFAULT 1, note TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id), UNIQUE KEY uq_license_key(license_key), KEY idx_domain(domain), KEY idx_status(status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  @ $conn->query("CREATE TABLE IF NOT EXISTS owner_domain_whitelist (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, domain VARCHAR(190) NOT NULL, license_key VARCHAR(120) DEFAULT '', customer_name VARCHAR(190) DEFAULT '', status TINYINT NOT NULL DEFAULT 1, message TEXT, valid_until DATETIME DEFAULT NULL, auto_days INT NOT NULL DEFAULT 30, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id), UNIQUE KEY uq_domain(domain)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  @ $conn->query("CREATE TABLE IF NOT EXISTS owner_license_messages (id TINYINT NOT NULL PRIMARY KEY DEFAULT 1, popup_message TEXT, telegram_url VARCHAR(255) DEFAULT '', upi_id VARCHAR(190) DEFAULT '', upi_name VARCHAR(190) DEFAULT '', qr_image VARCHAR(255) DEFAULT '', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  @ $conn->query("ALTER TABLE owner_licenses ADD COLUMN popup_message TEXT");
  @ $conn->query("ALTER TABLE owner_licenses ADD COLUMN popup_duration INT NOT NULL DEFAULT 60");
  @ $conn->query("ALTER TABLE owner_licenses ADD COLUMN popup_until DATETIME DEFAULT NULL");
  @ $conn->query("ALTER TABLE owner_licenses ADD COLUMN owner_telegram VARCHAR(255) DEFAULT ''");
  @ $conn->query("ALTER TABLE owner_domain_whitelist ADD COLUMN valid_until DATETIME DEFAULT NULL");
  @ $conn->query("ALTER TABLE owner_domain_whitelist ADD COLUMN auto_days INT NOT NULL DEFAULT 30");
  @ $conn->query("ALTER TABLE owner_domain_whitelist ADD COLUMN updated_at DATETIME DEFAULT NULL");
  @ $conn->query("INSERT IGNORE INTO owner_license_messages(id,popup_message,telegram_url,upi_id,upi_name,qr_image) VALUES(1,'License required. Contact owner for activation.','https://t.me/GAME13L','','','')");
  @ $conn->query("CREATE TABLE IF NOT EXISTS owner_license_chat (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, domain VARCHAR(190) DEFAULT '', license_key VARCHAR(120) DEFAULT '', name VARCHAR(190) DEFAULT '', contact VARCHAR(190) DEFAULT '', message TEXT, reply TEXT, status ENUM('open','closed') NOT NULL DEFAULT 'open', created_at DATETIME DEFAULT CURRENT_TIMESTAMP, replied_at DATETIME DEFAULT NULL, PRIMARY KEY(id), KEY idx_domain(domain), KEY idx_status(status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  return $conn; }
function ol_global_message(mysqli $conn): array { $rs=@$conn->query('SELECT * FROM owner_license_messages WHERE id=1'); return ($rs&&($r=$rs->fetch_assoc()))?$r:['popup_message'=>'License required','telegram_url'=>'']; }
function ol_make_key(): string { return '13L-'.strtoupper(bin2hex(random_bytes(3))).'-'.strtoupper(bin2hex(random_bytes(3))).'-'.strtoupper(bin2hex(random_bytes(3))); }
function ol_license_payload(mysqli $conn, array $lic, string $domain, string $message=''): array { $msg=ol_global_message($conn); $showPopup=false; $until=$lic['popup_until']??''; if(!empty($lic['valid_until']) && strtotime((string)$lic['valid_until']) < time()) { $lic['status']='expired'; } if(!empty($lic['popup_message']) && (!$until || strtotime($until)>time())) $showPopup=true; return ['productId'=>'13lgame','licenseKey'=>$lic['license_key']??'', 'domain'=>$domain, 'domains'=>[$domain], 'status'=>$lic['status']??'inactive', 'customerName'=>$lic['customer_name']??'', 'expiresAt'=>$lic['valid_until']??'', 'validUntil'=>$lic['valid_until']??'', 'message'=>$message ?: ($msg['popup_message']??''), 'telegramUrl'=>($lic['owner_telegram']??'') ?: ($msg['telegram_url']??''), 'upiId'=>$msg['upi_id']??'', 'upiName'=>$msg['upi_name']??'', 'qrImage'=>$msg['qr_image']??'', 'popup'=>['enabled'=>$showPopup,'message'=>(string)($lic['popup_message']??''),'duration'=>(int)($lic['popup_duration']??60),'until'=>$until], 'serverTime'=>time()]; }
?>