<?php
// Local development router for PHP built-in CLI server (php -S localhost:8000 router.php)

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$filePath = __DIR__ . $uri;

// 1. If physical file exists, serve static asset
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    if ($ext === 'js' || $ext === 'mjs') {
        header('Content-Type: application/javascript');
    } elseif ($ext === 'css') {
        header('Content-Type: text/css');
    } elseif ($ext === 'json' || $ext === 'webmanifest') {
        header('Content-Type: application/json');
    }
    return false;
}

// 2. Dynamic local lottery issue/json routes
if (preg_match('#^/webapi/(kv|v)/issue/([^/]+)$#i', $uri, $m)) {
    $_GET['gameCode'] = $m[2];
    require __DIR__ . '/api/_draw_router.php';
    exit;
}

if (preg_match('#^/(WinGo|K3|D5|MotoRace|TrxWinGo)/(.+\.json)$#i', $uri, $m)) {
    $_GET['path'] = $m[1] . '/' . $m[2];
    require __DIR__ . '/api/_draw_router.php';
    exit;
}

// 3. webapi specific fallbacks
if ($uri === '/webapi/v/config.js') {
    header('Content-Type: application/javascript');
    require __DIR__ . '/webapi/kv/config.js';
    exit;
}
if ($uri === '/webapi/v/firebaseConfig.js') {
    header('Content-Type: application/javascript');
    require __DIR__ . '/webapi/kv/firebaseConfig.js';
    exit;
}
if (strpos($uri, '/webapi/') === 0) {
    if (file_exists($filePath . '.js')) {
        header('Content-Type: application/javascript');
        require $filePath . '.js';
        exit;
    }
    if (file_exists($filePath . '.html')) {
        header('Content-Type: text/html');
        require $filePath . '.html';
        exit;
    }
}

// 4. WorkOrder API
if (strpos($uri, '/WorkOrder/') === 0) {
    if (file_exists($filePath . '.php')) {
        require $filePath . '.php';
        exit;
    }
    $path = preg_replace('#^/WorkOrder/#i', 'WorkOrder/', $uri);
    $_GET['path'] = $path;
    require __DIR__ . '/api/_router.php';
    exit;
}

// 5. /api/ extensionless support & router
if (strpos($uri, '/api/') === 0) {
    if (file_exists($filePath . '.php')) {
        require $filePath . '.php';
        exit;
    }
    if (file_exists($filePath . '.html')) {
        header('Content-Type: text/html');
        require $filePath . '.html';
        exit;
    }
    $path = preg_replace('#^/api/#i', '', $uri);
    $_GET['path'] = $path;
    require __DIR__ . '/api/_router.php';
    exit;
}

// 6. Direct folder or PHP script access (e.g. /install.php, /admin/index.php)
if (file_exists($filePath) && is_dir($filePath)) {
    if (file_exists($filePath . '/index.php')) {
        require $filePath . '/index.php';
        exit;
    }
    if (file_exists($filePath . '/index.html')) {
        header('Content-Type: text/html');
        require $filePath . '/index.html';
        exit;
    }
}

// 7. SPA Fallback
require __DIR__ . '/index.php';
