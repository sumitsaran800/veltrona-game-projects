<?php
require_once __DIR__ . '/api/_core/config.php';
require_once __DIR__ . '/api/_core/bootstrap.php';
require_once __DIR__ . '/api/_core/license_client.php';
header('Content-Type: application/json; charset=utf-8');
$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '', true); if (!is_array($data)) $data = array_merge($_GET, $_POST);
$action = $data['action'] ?? $_POST['lc_action'] ?? '';
if ($action === 'activate') { echo json_encode(lc_activate((string)($data['licenseKey'] ?? $data['license_key'] ?? '')), JSON_UNESCAPED_SLASHES); exit; }
if ($action === 'chat') { echo json_encode(lc_send_chat((string)($data['name'] ?? ''),(string)($data['message'] ?? ''),(string)($data['contact'] ?? '')), JSON_UNESCAPED_SLASHES); exit; }
echo json_encode(['ok'=>lc_is_valid(),'state'=>lc_state()], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>