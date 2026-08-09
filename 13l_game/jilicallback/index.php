<?php
/**
 * JILI Game Callback Handler
 * URL: https://yoursite.com/jilicallback/index.php
 * Register this URL in your JILI agent panel as the callback endpoint.
 *
 * Types handled:
 *   balance   → Return user balance
 *   bet       → Deduct bet amount
 *   win       → Credit win amount
 *   rollback  → Refund cancelled bet
 */

// Load 13L config + functions
define('NO_AUTH_REQUIRED', true);
require_once __DIR__ . '/../api/_core/config.php';
require_once __DIR__ . '/../api/_core/bootstrap.php';

// Security: Only allow if JILI is enabled
if (!defined('JILI_ENABLED') || !JILI_ENABLED) {
    http_response_code(403);
    echo json_encode(['code' => 403, 'message' => 'JILI not enabled']);
    exit;
}

jili_handle_callback();
