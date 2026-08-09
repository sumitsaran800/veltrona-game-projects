<?php
// Router for 89 CLUB on PHP built-in CLI server (php -S 127.0.0.1:8001 router.php)

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$filePath = __DIR__ . $uri;

// 1. Existing file/directory
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    if ($ext === 'js' || $ext === 'mjs') {
        header('Content-Type: application/javascript');
    } elseif ($ext === 'css') {
        header('Content-Type: text/css');
    } elseif ($ext === 'json') {
        header('Content-Type: application/json');
    } elseif ($ext === 'svg') {
        header('Content-Type: image/svg+xml');
    }
    return false;
}

// 2. Extensionless .php file
if (file_exists($filePath . '.php')) {
    require $filePath . '.php';
    exit;
}

// 3. Folder with index.php / index.html
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

// 4. Default to index.php
require __DIR__ . '/index.php';
