<?php
/**
 * Spribe (Aviator) Game Callback Handler
 * URL: https://yoursite.com/spribecallback/index.php
 * Register this URL in your Spribe operator panel as the callback endpoint.
 *
 * NOTE: Spribe sends amounts in CENTS (smallest currency unit).
 * e.g., amount=1000 means ₹10.00
 *
 * Actions handled:
 *   balance   → Return balance in cents
 *   bet       → Deduct bet amount
 *   win       → Credit win amount
 *   rollback  → Refund cancelled bet
 */

// Load 13L config + functions
define('NO_AUTH_REQUIRED', true);
require_once __DIR__ . '/../api/_core/config.php';
require_once __DIR__ . '/../api/_core/bootstrap.php';

// Security: Only allow if Spribe is enabled
if (!defined('SPRIBE_ENABLED') || !SPRIBE_ENABLED) {
    http_response_code(403);
    echo json_encode(['code' => 403, 'message' => 'Spribe not enabled']);
    exit;
}

spribe_handle_callback();
