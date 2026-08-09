<?php
// ===============================
// 13L GAME Backend Config
// cPanel MySQL details yaha set hain.
// AUTO_INSTALL_TABLES=true hone par pehli API request par tables automatic create/seed ho jayengi.
// ===============================

define('DB_HOST', 'localhost');
define('DB_NAME', 'onorc_13l');
define('DB_USER', 'onorc_13l');
define('DB_PASS', 'onorc_13l');

define('AUTO_INSTALL_TABLES', true);
define('AUTO_INSTALL_VERSION', '2026_05_16_v21_owner_license_server');

define('APP_TENANT_ID', 6007);
define('APP_CURRENCY', 'INR');
define('APP_CURRENCY_SIGN', '₹');
define('APP_SECRET', 'change_this_secret_key_13l_game');
define('APP_TIMEZONE', 'Asia/Kolkata');

// true = project/demo virtual wallet only. Real-money payment gateway/live settlement connected nahi hai.
define('DEMO_MODE', true);

@date_default_timezone_set(APP_TIMEZONE);

// V20 License system
define('LICENSE_SERVER_URL', 'https://133lgameluckywebd.abhaydeveloper.us.cc/license/api.php');
define('LICENSE_PRODUCT_ID', '13lgame');
define('LICENSE_SHARED_SECRET', '13l_owner_license_shared_secret_8279_change_later');
define('LICENSE_CACHE_SECONDS', 900);

// Owner package: do not require client license on owner domain
define('OWNER_SITE_MODE', true);

// ===============================
// JDB GAME API CREDENTIALS
// Provider: JDB (jdb711.com)
// Apne JDB reseller se ye values lo
// ===============================
define('JDB_ENABLED', false); // true karo jab real credentials lagao
define('JDB_DC',         'your_jdb_dc_here');
define('JDB_PARENT',     'your_jdb_parent_here');
define('JDB_AGENT',      'your_jdb_agent_here');
define('JDB_KEY',        'your_jdb_aes_key_here');   // 16/24/32 char AES key
define('JDB_IV',         'your_jdb_aes_iv_here');    // 16 char IV
define('JDB_API_URL',    'https://api.jdb711.com');  // JDB production endpoint
define('JDB_CURRENCY',   'INR');
// Callback URL: https://133lgameluckywebd.abhaydeveloper.us.cc/jdbcallback/index.php
// Ye URL apne JDB panel mein register karo

// ===============================
// JILI GAME API CREDENTIALS
// Provider: JILI Sports
// Apne JILI reseller se ye values lo
// ===============================
define('JILI_ENABLED', false); // true karo jab real credentials lagao
define('JILI_AGENT_CODE', 'your_jili_agent_code');
define('JILI_KEY',        'your_jili_secret_key');
define('JILI_API_URL',    'https://api.jilisports.com'); // JILI production endpoint
define('JILI_CURRENCY',   'INR');
// Callback URL: https://133lgameluckywebd.abhaydeveloper.us.cc/jilicallback/index.php
// Ye URL apne JILI panel mein register karo

// ===============================
// SPRIBE GAME API (Aviator)
// Provider: Spribe (Aviator ka original provider)
// ===============================
define('SPRIBE_ENABLED', false); // true karo jab real credentials lagao
define('SPRIBE_CLIENT_ID',     'your_spribe_client_id');
define('SPRIBE_CLIENT_SECRET', 'your_spribe_client_secret');
define('SPRIBE_API_URL',       'https://api.spr.be'); // Spribe production endpoint
define('SPRIBE_CURRENCY',      'INR');
// Callback URL: https://133lgameluckywebd.abhaydeveloper.us.cc/spribecallback/index.php

// =============================================================================
// UNIFIED GAMING API CREDENTIALS (Supabase)
// =============================================================================
define('UNIFIED_API_ENABLED', false); // Games served locally, disable Supabase API
define('UNIFIED_API_KEY', '6b2b20914529ca638a0587b2197356fb2c1a1a2b84677e11fa030355a47bf2e8');
define('UNIFIED_API_SECRET', 'ab2bc66c517eb003c0107f7e6d77bfee25dae974c897736b8b48394be1f2ba1d');
define('UNIFIED_API_URL', 'https://qmikiecjseufxefwwoab.supabase.co/functions/v1/game-api');

