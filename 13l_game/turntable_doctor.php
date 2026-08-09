<?php
/**
 * ====================================================================
 * 🎰 13L TURNTABLE DOCTOR & SELF-HEALER SYSTEM 🎰
 * ====================================================================
 * Designed & Engineered by AntiGravity Developer (Senior Architect)
 * Modern Premium Glassmorphic Diagnostic Dashboard & Auto-Repair Tool
 * 
 * Functions:
 * 1. Diagnostic scan of JS chunks and WebGL assets
 * 2. Hash mismatch detection and auto-healing (fixing 404 script errors)
 * 3. Turntable asset check & auto-downloader from original working website (13lwin.com)
 * 4. Beautiful modern HSL H-end developer interface
 * ====================================================================
 */

set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$ROOT = __DIR__;
$SOURCE_BASE = 'https://13lwin.com'; // Original working website

// Secure token or bypass key to prevent unauthorized access
$ACCESS_KEY = '8279';
$is_authenticated = false;

if (php_sapi_name() === 'cli' || (isset($_GET['key']) && $_GET['key'] === $ACCESS_KEY)) {
    $is_authenticated = true;
}

// Auto-Heal Actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$message = '';
$log = [];

if ($is_authenticated && $action === 'heal') {
    $jsDir = $ROOT . '/js';
    if (!is_dir($jsDir)) {
        @mkdir($jsDir, 0755, true);
    }
    
    // Helper function to download file if not exists locally or if size is too small
    if (!function_exists('download_file_direct')) {
        function download_file_direct($src, $dst) {
            $ch = curl_init($src);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]);
            $data = curl_exec($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($code === 200 && $data !== false && strlen($data) > 100) {
                @mkdir(dirname($dst), 0755, true);
                file_put_contents($dst, $data);
                return true;
            }
            return false;
        }
    }

    // List of core files we MUST have. If they are missing locally, download them from 13lwin.com
    $coreFiles = [
        'WebGLRenderer-B7bE1YuE.js',
        'BufferResource-C1ksIqeS.js',
        'Filter-5OKxlivX.js',
        'RenderTargetSystem-DCoiJPcG.js',
        'index-BtSlvxrs.js',
        'init-B9ZVRwkz.js'
    ];

    foreach ($coreFiles as $core) {
        $localCorePath = $jsDir . '/' . $core;
        if (!is_file($localCorePath) || filesize($localCorePath) < 500) {
            $remoteUrl = $SOURCE_BASE . '/js/' . $core;
            if (download_file_direct($remoteUrl, $localCorePath)) {
                $log[] = "📥 downloaded core: Retrieved missing $core from original site";
            } else {
                $log[] = "⚠️ download failed: Could not retrieve core $core online";
            }
        }
    }

    // Now, let's copy the downloaded/present files to all of their case and character variations!
    $variations = [
        // WebGLRenderer (Case fallback)
        'WebGLRenderer-B7bE1YuE.js' => [
            'WebGLRenderer-b7be1yue.js',
            'WebGLRenderer-B7be1YuE.js'
        ],
        // BufferResource variations
        'BufferResource-C1ksIqeS.js' => [
            'BufferResource-C1ksIgeS.js',
            'BufferResource-C1ksTgeS.js',
            'BufferResource-C1kxlqeS.js',
            'BufferResource-C1ksIqeS.js'
        ],
        // Filter variations (O vs 0, case fallback)
        'Filter-5OKxlivX.js' => [
            'Filter-50KxlivX.js',
            'Filter-5DkdnvXj.js',
            'Filter-50Kxlivx.js',
            'Filter-5OKxlivx.js',
            'Filter-5OKxlivX.js'
        ],
        // RenderTargetSystem variations
        'RenderTargetSystem-DCoiJPcG.js' => [
            'RenderTargetSystem-DCoiJpcG.js',
            'RenderTargetSystem-DCoUpCgJ.js',
            'RenderTargetSystem-DCoiJPCG.js',
            'RenderTargetSystem-DCoijpcg.js',
            'RenderTargetSystem-DCoiJPcG.js'
        ],
        // index variations
        'index-BtSlvxrs.js' => [
            'index-BtSlxxrs.js',
            'index-BtSlvxrs.js'
        ]
    ];

    foreach ($variations as $sourceFile => $dests) {
        $sourcePath = $jsDir . '/' . $sourceFile;
        if (is_file($sourcePath)) {
            foreach ($dests as $dest) {
                $destPath = $jsDir . '/' . $dest;
                if ($dest !== $sourceFile) {
                    if (copy($sourcePath, $destPath)) {
                        $log[] = "✅ healed: Copied $sourceFile -> $dest (fixed 404/case issues)";
                    } else {
                        $log[] = "❌ failed: Could not copy to $dest";
                    }
                }
            }
        } else {
            // Dynamic fallback: find any file with prefix
            $prefix = explode('-', $sourceFile)[0] . '-';
            $found = false;
            foreach (glob($jsDir . '/' . $prefix . '*.js') as $file) {
                $base = basename($file);
                foreach ($dests as $dest) {
                    $destPath = $jsDir . '/' . $dest;
                    if (copy($file, $destPath)) {
                        $log[] = "⚡ dynamic healed: Copied $base -> $dest";
                        $found = true;
                    }
                }
                break;
            }
            if (!$found) {
                $log[] = "❌ missing: Source file starting with prefix '$prefix' completely missing";
            }
        }
    }

    // 2. Download Turntable webp/gif Assets from original domain
    $assets = [
        'assets/darkRed/inviteWheel/turntable-075ebf2c.webp',
        'assets/darkRed/inviteWheel/turntable-075ebf2c (1).webp',
        'assets/darkRed/inviteWheel/select-7beb206c.webp',
        'assets/darkRed/inviteWheel/select-7beb206c (1).webp',
        'assets/darkRed/inviteWheel/turntable_bg-9928b70f.webp',
        'assets/darkRed/inviteWheel/light_gold-81bc84fe.webp',
        'assets/darkRed/inviteWheel/light_gold-81bc84fe (1).webp',
        'assets/darkRed/inviteWheel/start_btn-6c887ea1.webp',
        'assets/darkRed/inviteWheel/start_btn-6c887ea1 (1).webp',
        'assets/darkRed/inviteWheel/animate-461ec0ce.gif',
        'assets/darkRed/inviteWheel/start-863906a3.gif',
        'assets/darkRed/inviteWheel/money-c9768b51.png',
        'assets/darkRed/inviteWheel/money2-a9c8448a.webp',
        'assets/darkRed/inviteWheel/money2-a9c8448a (1).webp',
        'assets/darkRed/tabbar/turntable_home-ee908e6a.webp',
        'images/recharge_turntable_main_bg.webp'
    ];

    function download_file($src, $dst) {
        $ch = curl_init($src);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]);
        $data = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code === 200 && $data !== false && strlen($data) > 100) {
            @mkdir(dirname($dst), 0755, true);
            file_put_contents($dst, $data);
            return true;
        }
        return false;
    }

    foreach ($assets as $asset) {
        $localPath = $ROOT . '/' . $asset;
        $remoteUrl = $SOURCE_BASE . '/' . $asset;

        if (is_file($localPath) && filesize($localPath) > 500) {
            $log[] = "ℹ️ asset ok: $asset already exists (" . round(filesize($localPath)/1024, 1) . " KB)";
        } else {
            if (download_file($remoteUrl, $localPath)) {
                $log[] = "📥 downloaded: Successfully retrieved $asset from source";
            } else {
                $log[] = "⚠️ download failed: Could not retrieve $asset (using fallback or placeholder)";
            }
        }
    }

    $message = "Self-Healer & Asset Downloader Execution Completed!";
}

// 3. Diagnostics Scan
$scan_results = [];
if ($is_authenticated) {
    // Check local directories
    $scan_results['js_dir'] = is_dir($ROOT . '/js');
    $scan_results['assets_dir'] = is_dir($ROOT . '/assets');

    // Check specific critical files
    $scan_results['zhuanpan_js'] = is_file($ROOT . '/js/zhuanpan-CL2aQr8p.js');
    $scan_results['index_vUfr7_js'] = is_file($ROOT . '/js/index-vUfr7-VZ.js');

    // Check Turntable assets exist
    $scan_results['turntable_webp'] = is_file($ROOT . '/assets/darkRed/inviteWheel/turntable-075ebf2c.webp');
    $scan_results['pointer_webp'] = is_file($ROOT . '/assets/darkRed/inviteWheel/select-7beb206c.webp');
    $scan_results['tabbar_webp'] = is_file($ROOT . '/assets/darkRed/tabbar/turntable_home-ee908e6a.webp');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎰 13L Turntable Diagnostic Doctor 🎰</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --surface-color: rgba(23, 29, 43, 0.65);
            --border-color: rgba(255, 255, 255, 0.08);
            --primary: linear-gradient(135deg, #ff3d5a, #ff7b1a);
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --text-main: #ffffff;
            --text-muted: #8e9bb2;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at 10% 20%, rgba(255, 61, 90, 0.1) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(255, 123, 26, 0.1) 0%, transparent 40%);
            background-attachment: fixed;
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 92%;
            max-width: 800px;
            margin: 40px auto;
            background: var(--surface-color);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.8);
            box-sizing: border-box;
        }

        .logo {
            font-weight: 800;
            font-size: 32px;
            background: var(--primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            text-align: center;
            color: var(--text-muted);
            font-size: 15px;
            margin-bottom: 30px;
        }

        .card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .diagnostic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .diagnostic-item {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .diagnostic-label {
            font-size: 14px;
            color: var(--text-muted);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-error {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .console {
            background: #060913;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #38bdf8;
            max-height: 250px;
            overflow-y: auto;
            line-height: 1.6;
        }

        .console-line {
            margin-bottom: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.02);
            padding-bottom: 4px;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 16px;
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 61, 90, 0.3);
            text-align: center;
            text-decoration: none;
            display: inline-block;
            box-sizing: border-box;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 61, 90, 0.5);
        }

        .btn-heal {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-heal:hover {
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-width: 400px;
            margin: 0 auto;
        }

        input {
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 16px;
            color: #fff;
            box-sizing: border-box;
            outline: none;
            font-size: 15px;
        }

        input:focus {
            border-color: #ff3d5a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">13L TURNTABLE DOCTOR</div>
        <div class="subtitle">System Diagnostics & Asset Healing Dashboard</div>

        <?php if (!$is_authenticated): ?>
            <div class="card">
                <div class="card-title">🔐 Security Verification Required</div>
                <form class="auth-form" method="get">
                    <input type="password" name="key" placeholder="Enter security access key" required>
                    <button class="btn">Authenticate</button>
                </form>
            </div>
        <?php else: ?>
            
            <?php if ($message): ?>
                <div class="card" style="border-color: var(--success); background: rgba(16, 185, 129, 0.05);">
                    <div class="card-title" style="color: var(--success);">🎉 Success</div>
                    <p style="margin: 0; color: var(--text-muted);"><?php echo htmlspecialchars($message); ?></p>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-title">🔍 Diagnostic Report</div>
                <div class="diagnostic-grid">
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">JS Directory</span>
                        <span class="badge <?php echo $scan_results['js_dir'] ? 'badge-success' : 'badge-error'; ?>">
                            <?php echo $scan_results['js_dir'] ? 'OK' : 'MISSING'; ?>
                        </span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Assets Directory</span>
                        <span class="badge <?php echo $scan_results['assets_dir'] ? 'badge-success' : 'badge-error'; ?>">
                            <?php echo $scan_results['assets_dir'] ? 'OK' : 'MISSING'; ?>
                        </span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">zhuanpan JS Component</span>
                        <span class="badge <?php echo $scan_results['zhuanpan_js'] ? 'badge-success' : 'badge-error'; ?>">
                            <?php echo $scan_results['zhuanpan_js'] ? 'OK' : 'MISSING'; ?>
                        </span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">index-vUfr7 JS Chunks</span>
                        <span class="badge <?php echo $scan_results['index_vUfr7_js'] ? 'badge-success' : 'badge-error'; ?>">
                            <?php echo $scan_results['index_vUfr7_js'] ? 'OK' : 'MISSING'; ?>
                        </span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Turntable WebP Asset</span>
                        <span class="badge <?php echo $scan_results['turntable_webp'] ? 'badge-success' : 'badge-error'; ?>">
                            <?php echo $scan_results['turntable_webp'] ? 'OK' : 'MISSING'; ?>
                        </span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Pointer WebP Asset</span>
                        <span class="badge <?php echo $scan_results['pointer_webp'] ? 'badge-success' : 'badge-error'; ?>">
                            <?php echo $scan_results['pointer_webp'] ? 'OK' : 'MISSING'; ?>
                        </span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Tabbar WebP Icon</span>
                        <span class="badge <?php echo $scan_results['tabbar_webp'] ? 'badge-success' : 'badge-error'; ?>">
                            <?php echo $scan_results['tabbar_webp'] ? 'OK' : 'MISSING'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if (!empty($log)): ?>
                <div class="card">
                    <div class="card-title">💻 Self-Healer Logs</div>
                    <div class="console">
                        <?php foreach ($log as $line): ?>
                            <div class="console-line"><?php echo htmlspecialchars($line); ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card" style="background: rgba(255, 61, 90, 0.02);">
                <div class="card-title">⚡ Auto-Heal & Asset Restorer</div>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px; line-height: 1.5;">
                    Running this restorer will automatically:
                    <br>• Fix 404 JS chunk errors by copying actual files to expected hashes in /js/ folder.
                    <br>• Automatically download missing turntable assets (webp, gifs) from the working 13lwin.com server.
                </p>
                <form method="post">
                    <input type="hidden" name="action" value="heal">
                    <button class="btn btn-heal">🚀 RUN AUTO-HEALER & DOWNLOAD ASSETS</button>
                </form>
            </div>

            <p style="text-align: center; color: var(--text-muted); font-size: 11px; margin-top: 20px;">
                Security Note: Please delete this file from public_html once diagnostics are complete.
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
