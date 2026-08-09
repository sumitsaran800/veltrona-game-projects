<?php
require_once __DIR__ . '/_core.php';
$conn = ol_ensure();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') { echo json_encode(['ok'=>true]); exit; }
$d = ol_json();
$domain = ol_domain($d['domain'] ?? $d['d'] ?? ($_SERVER['HTTP_ORIGIN'] ?? ''));
$key = trim((string)($d['key'] ?? $d['licenseKey'] ?? ''));
$msg = ol_global_message($conn);
$lic = null;
if ($key !== '') { $st=$conn->prepare('SELECT * FROM owner_licenses WHERE license_key=? LIMIT 1'); if($st){$st->bind_param('s',$key);$st->execute();$lic=$st->get_result()->fetch_assoc();} }
if (!$lic && $domain !== '') { $st=$conn->prepare('SELECT * FROM owner_licenses WHERE domain=? LIMIT 1'); if($st){$st->bind_param('s',$domain);$st->execute();$lic=$st->get_result()->fetch_assoc();} }
if (!$lic && $domain !== '') { $st=$conn->prepare('SELECT * FROM owner_domain_whitelist WHERE domain=? AND status=1 LIMIT 1'); if($st){$st->bind_param('s',$domain);$st->execute();$wl=$st->get_result()->fetch_assoc(); if($wl){ echo json_encode(['ok'=>true,'status'=>'active','domain'=>$domain,'message'=>$wl['message'] ?: ($msg['popup_message']??''),'telegramUrl'=>$msg['telegram_url']??'']); exit; }} }
if (!$lic) { echo json_encode(['ok'=>false,'status'=>'inactive','domain'=>$domain,'message'=>'Domain is not whitelisted','telegramUrl'=>$msg['telegram_url']??'']); exit; }
$bound = ol_domain($lic['domain'] ?? '');
if ($bound && $bound !== $domain) { echo json_encode(['ok'=>false,'status'=>'inactive','domain'=>$domain,'message'=>'Domain mismatch']); exit; }
if (($lic['status'] ?? '') === 'blocked') { echo json_encode(['ok'=>false,'status'=>'blocked','domain'=>$domain,'message'=>'License blocked by owner','telegramUrl'=>($lic['owner_telegram']??'') ?: ($msg['telegram_url']??'')]); exit; }
if (!empty($lic['valid_until']) && strtotime($lic['valid_until']) < time()) { echo json_encode(['ok'=>false,'status'=>'expired','domain'=>$domain,'message'=>'License expired','telegramUrl'=>($lic['owner_telegram']??'') ?: ($msg['telegram_url']??'')]); exit; }
$showPopup=false; $until=$lic['popup_until']??''; if(!empty($lic['popup_message']) && (!$until || strtotime($until)>time())) $showPopup=true;
echo json_encode(['ok'=>($lic['status']==='active'),'status'=>$lic['status'],'domain'=>$domain,'customerName'=>$lic['customer_name']??'','validUntil'=>$lic['valid_until']??'','message'=>$msg['popup_message']??'','telegramUrl'=>($lic['owner_telegram']??'') ?: ($msg['telegram_url']??''),'popup'=>['enabled'=>$showPopup,'message'=>(string)($lic['popup_message']??''),'duration'=>(int)($lic['popup_duration']??60),'until'=>$until]], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>