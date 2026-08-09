<?php
declare(strict_types=1);

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
|--------------------------------------------------------------------------
| Folder File Counter
|--------------------------------------------------------------------------
| Put this file in public_html/file_count.php
| Change password below.
*/

$ADMIN_PASSWORD = '1'; // CHANGE THIS PASSWORD
$ROOT_DIR = realpath(__DIR__); // current folder as root

if ($ROOT_DIR === false) {
    die('Root directory not found.');
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatSize(int $bytes): string
{
    if ($bytes < 1024) {
        return $bytes . ' B';
    }

    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }

    if ($bytes < 1024 * 1024 * 1024) {
        return round($bytes / (1024 * 1024), 2) . ' MB';
    }

    return round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
}

function isSafePath(string $root, string $path): bool
{
    $real = realpath($path);
    if ($real === false) {
        return false;
    }

    return str_starts_with($real, $root);
}

function scanFolder(string $folder): array
{
    $result = [
        'direct_files' => 0,
        'recursive_files' => 0,
        'direct_folders' => 0,
        'recursive_folders' => 0,
        'total_size' => 0,
        'extensions' => [],
        'children' => [],
    ];

    if (!is_dir($folder) || !is_readable($folder)) {
        return $result;
    }

    $items = scandir($folder);
    if ($items === false) {
        return $result;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        // hidden files/folders skip karne ke liye uncomment karo
        // if (str_starts_with($item, '.')) continue;

        $fullPath = $folder . DIRECTORY_SEPARATOR . $item;

        if (is_file($fullPath)) {
            $result['direct_files']++;
            $result['recursive_files']++;

            $size = filesize($fullPath);
            if ($size !== false) {
                $result['total_size'] += $size;
            }

            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if ($ext === '') {
                $ext = 'no_extension';
            }

            if (!isset($result['extensions'][$ext])) {
                $result['extensions'][$ext] = 0;
            }

            $result['extensions'][$ext]++;
        } elseif (is_dir($fullPath)) {
            $result['direct_folders']++;
            $result['recursive_folders']++;

            $child = scanFolder($fullPath);

            $result['recursive_files'] += $child['recursive_files'];
            $result['recursive_folders'] += $child['recursive_folders'];
            $result['total_size'] += $child['total_size'];

            foreach ($child['extensions'] as $ext => $count) {
                if (!isset($result['extensions'][$ext])) {
                    $result['extensions'][$ext] = 0;
                }
                $result['extensions'][$ext] += $count;
            }

            $result['children'][$item] = $child;
        }
    }

    ksort($result['extensions']);
    ksort($result['children']);

    return $result;
}

function renderFolderRows(array $children, string $basePath = '', int $level = 0): string
{
    $html = '';

    foreach ($children as $name => $data) {
        $folderPath = trim($basePath . '/' . $name, '/');
        $padding = 18 + ($level * 22);

        $html .= '<tr>';
        $html .= '<td style="padding-left:' . $padding . 'px;">📁 <a href="?path=' . urlencode($folderPath) . '">' . h($name) . '</a></td>';
        $html .= '<td>' . (int)$data['direct_files'] . '</td>';
        $html .= '<td>' . (int)$data['recursive_files'] . '</td>';
        $html .= '<td>' . (int)$data['direct_folders'] . '</td>';
        $html .= '<td>' . (int)$data['recursive_folders'] . '</td>';
        $html .= '<td>' . h(formatSize((int)$data['total_size'])) . '</td>';
        $html .= '</tr>';

        if (!empty($data['children'])) {
            $html .= renderFolderRows($data['children'], $folderPath, $level + 1);
        }
    }

    return $html;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: file_count.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if (hash_equals($ADMIN_PASSWORD, (string)$_POST['password'])) {
        $_SESSION['file_counter_login'] = true;
        header('Location: file_count.php');
        exit;
    }

    $error = 'Wrong password.';
}

if (empty($_SESSION['file_counter_login'])) {
    ?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Folder File Counter Login</title>
        <style>
            body {
                background: #0f172a;
                color: #e5e7eb;
                font-family: Arial, sans-serif;
                display: flex;
                min-height: 100vh;
                align-items: center;
                justify-content: center;
            }
            .box {
                background: #111827;
                padding: 25px;
                width: 360px;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0,0,0,.4);
            }
            input, button {
                width: 100%;
                padding: 12px;
                margin-top: 10px;
                border-radius: 10px;
                border: 1px solid #374151;
                box-sizing: border-box;
            }
            input {
                background: #020617;
                color: #fff;
            }
            button {
                background: #22c55e;
                color: #052e16;
                font-weight: bold;
                cursor: pointer;
            }
            .err {
                color: #f87171;
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
    <div class="box">
        <h2>Folder File Counter</h2>
        <form method="post">
            <input type="password" name="password" placeholder="Enter password" required>
            <button type="submit">Login</button>
        </form>
        <?php if ($error): ?>
            <div class="err"><?= h($error) ?></div>
        <?php endif; ?>
    </div>
    </body>
    </html>
    <?php
    exit;
}

$requestedPath = trim((string)($_GET['path'] ?? ''), '/\\');
$currentDir = $ROOT_DIR;

if ($requestedPath !== '') {
    $target = realpath($ROOT_DIR . DIRECTORY_SEPARATOR . $requestedPath);

    if ($target !== false && is_dir($target) && isSafePath($ROOT_DIR, $target)) {
        $currentDir = $target;
    }
}

$data = scanFolder($currentDir);
$relativeCurrent = trim(str_replace($ROOT_DIR, '', $currentDir), DIRECTORY_SEPARATOR);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Folder File Counter</title>
    <style>
        body {
            background: #0f172a;
            color: #e5e7eb;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 25px;
        }
        .wrap {
            max-width: 1200px;
            margin: auto;
        }
        .top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            margin-bottom: 18px;
        }
        a {
            color: #93c5fd;
            text-decoration: none;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .card {
            background: #111827;
            border: 1px solid #1f2937;
            padding: 18px;
            border-radius: 14px;
        }
        .label {
            color: #9ca3af;
            font-size: 12px;
        }
        .value {
            font-size: 26px;
            font-weight: bold;
            margin-top: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #111827;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        th, td {
            border-bottom: 1px solid #1f2937;
            padding: 11px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background: #020617;
            color: #cbd5e1;
        }
        tr:hover {
            background: #1f2937;
        }
        .pathbox {
            background: #020617;
            border: 1px solid #1f2937;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 16px;
            color: #cbd5e1;
            font-family: monospace;
        }
        .btn {
            display: inline-block;
            background: #22c55e;
            color: #052e16;
            padding: 9px 12px;
            border-radius: 10px;
            font-weight: bold;
        }
        .btn.red {
            background: #ef4444;
            color: white;
        }
        .small {
            color: #9ca3af;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <div>
            <h1>Folder File Counter</h1>
            <div class="small">Root: <?= h($ROOT_DIR) ?></div>
        </div>
        <div>
            <a class="btn" href="file_count.php">Root</a>
            <a class="btn red" href="?logout=1">Logout</a>
        </div>
    </div>

    <div class="pathbox">
        Current folder:
        <?= $relativeCurrent === '' ? '/' : h($relativeCurrent) ?>
    </div>

    <div class="cards">
        <div class="card">
            <div class="label">Direct Files</div>
            <div class="value"><?= (int)$data['direct_files'] ?></div>
        </div>
        <div class="card">
            <div class="label">Total Files Recursive</div>
            <div class="value"><?= (int)$data['recursive_files'] ?></div>
        </div>
        <div class="card">
            <div class="label">Direct Folders</div>
            <div class="value"><?= (int)$data['direct_folders'] ?></div>
        </div>
        <div class="card">
            <div class="label">Total Folders Recursive</div>
            <div class="value"><?= (int)$data['recursive_folders'] ?></div>
        </div>
        <div class="card">
            <div class="label">Total Size</div>
            <div class="value"><?= h(formatSize((int)$data['total_size'])) ?></div>
        </div>
    </div>

    <h2>Folder-wise Count</h2>
    <table>
        <thead>
        <tr>
            <th>Folder</th>
            <th>Direct Files</th>
            <th>Total Files</th>
            <th>Direct Folders</th>
            <th>Total Folders</th>
            <th>Size</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($data['children'])): ?>
            <?= renderFolderRows($data['children'], $relativeCurrent) ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No folders found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <h2>Extension-wise Count</h2>
    <table>
        <thead>
        <tr>
            <th>Extension</th>
            <th>Files</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($data['extensions'])): ?>
            <?php foreach ($data['extensions'] as $ext => $count): ?>
                <tr>
                    <td><?= h($ext) ?></td>
                    <td><?= (int)$count ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">No files found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>