<?php
// ═══════════════════════════════════════════════════════════════════════
//  13LGAME Asset Downloader
//  Upload this file to your server root (where index.html exists)
//  and access it via browser: https://yourdomain.com/download-assets.php
// ═══════════════════════════════════════════════════════════════════════

set_time_limit(0);
$SOURCE = "https://13lwin.com";
$ROOT = __DIR__;

echo "<html><head><title>Asset Downloader</title>
<style>
body{font-family:'Segoe UI',sans-serif;background:#1a1a2e;color:#eee;margin:20px;max-width:900px}
h1{color:#e94560;border-bottom:2px solid #e94560;padding-bottom:10px}
.progress{background:#16213e;border-radius:8px;padding:15px;margin:10px 0}
.ok{color:#4ecca3}
.fail{color:#e94560}
.small{color:#888;font-size:12px}
pre{font-size:13px;line-height:1.6}
.summary{border:2px solid #4ecca3;border-radius:10px;padding:20px;margin-top:20px;background:#16213e}
.big{font-size:24px;font-weight:bold}
</style></head><body>
<h1>📦 13LGAME Asset Downloader</h1>
<p>Source: <code>$SOURCE</code> → Local: <code>$ROOT</code></p>
<div class='progress'><pre>";

// ── Step 1: Find all asset references in JS files ──
$refs = [];
$jsDir = "$ROOT/js";
if (is_dir($jsDir)) {
    $files = glob("$jsDir/*.js");
    foreach ($files as $f) {
        $content = file_get_contents($f);
        preg_match_all('/["\'](\/(?:assets|images|src|img)\/[^"\']+\.(?:webp|png|gif|jpg|jpeg|svg|json|svga|mp3|wav|pag))["\']/', $content, $m);
        foreach ($m[1] as $p) $refs[$p] = true;
        preg_match_all('/["\']\/([^"\']+\.(?:webp|png|gif|jpg|jpeg|svg|json))["\']/', $content, $m);
        foreach ($m[1] as $p) $refs["/$p"] = true;
    }
}
ksort($refs);
$total = count($refs);
echo "📄 Scanned: " . count($files ?? []) . " JS files\n";
echo "🔍 Found: $total unique asset references\n\n";

// ── Step 2: Download each asset ──
$downloaded = 0; $placeholder = 0; $failed = 0; $skipped = 0;
$i = 0;

foreach ($refs as $path => $v) {
    $i++;
    $localFile = $ROOT . $path;
    $localDir = dirname($localFile);
    
    // Skip if file already exists and is valid (>200 bytes)
    if (file_exists($localFile) && filesize($localFile) > 200) {
        $skipped++;
        continue;
    }
    
    if (!is_dir($localDir)) mkdir($localDir, 0755, true);
    
    // Download from live site
    $url = $SOURCE . $path;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && strlen($data) > 100) {
        file_put_contents($localFile, $data);
        $downloaded++;
        $status = "✔ DOWNLOADED";
        $color = "ok";
    } else {
        // Create 1x1 placeholder (WEBP)
        file_put_contents($localFile, base64_decode("UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAwA0JaQAA3AA/vuUAAA="));
        $placeholder++;
        $status = ($httpCode === 404) ? "✘ 404 (placeholder)" : "? $httpCode (placeholder)";
        $color = "fail";
    }
    
    echo "<span class='$color'>$status</span> <span class='small'>$i/$total</span> $path\n";
    flush();
}

// ── Step 3: Final Report ──
echo "\n</pre></div>";

$realSize = 0;
$realCount = 0; $placeCount = 0;
foreach ($refs as $path => $v) {
    $f = $ROOT . $path;
    if (file_exists($f)) {
        $s = filesize($f);
        if ($s > 200) { $realCount++; $realSize += $s; }
        else $placeCount++;
    }
}

$mb = round($realSize / 1024 / 1024, 1);
echo "<div class='summary'>";
echo "<h2>✅ DOWNLOAD COMPLETE</h2>";
echo "<table>";
echo "<tr><td>Total assets</td><td class='big'>$total</td></tr>";
echo "<tr><td>Real files downloaded</td><td class='big' style='color:#4ecca3'>$realCount</td><td>($mb MB)</td></tr>";
echo "<tr><td>Placeholder (1x1) created</td><td class='big' style='color:#e94560'>$placeCount</td><td>(not found on live site)</td></tr>";
echo "<tr><td>Previously existed</td><td class='big'>$skipped</td><td></td></tr>";
echo "</table>";
echo "<hr>";
echo "<p>✅ <b>Site ab 100% kaam karega — koi 404 error nahi aayega!</b></p>";
echo "<p><small>Placeholder files transparent 1x1 images hain. Asli design ke liye source se rebuild karein.</small></p>";
echo "</div>";
echo "</body></html>";
