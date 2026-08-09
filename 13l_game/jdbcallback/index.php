<?php
/**
 * JDB Game Callback Handler
 * URL: https://yoursite.com/jdbcallback/index.php
 * Register this URL in your JDB agent panel as the callback endpoint.
 *
 * Actions handled:
 *   action=6  → GetBalance
 *   action=8  → Bet / Win / Draw
 *   action=9  → Rollback
 */

// Load 13L config + functions
define('NO_AUTH_REQUIRED', true);
require_once __DIR__ . '/../api/_core/config.php';
require_once __DIR__ . '/../api/_core/bootstrap.php';

// Security: Only allow if JDB is enabled
if (!defined('JDB_ENABLED') || !JDB_ENABLED) {
    http_response_code(403);
    echo json_encode(['status' => '0002', 'err_text' => 'JDB not enabled']);
    exit;
}

jdb_handle_callback();
