<?php
// ===== PHP File Manager with Folder Compression, Download & Rename =====
$user = 'admin';
$pass = '8825';

if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != $user || $_SERVER['PHP_AUTH_PW'] != $pass) {
    header('WWW-Authenticate: Basic realm="Protected"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication required!';
    exit;
}

$base_dir = realpath($_SERVER['DOCUMENT_ROOT']);
$current_dir = isset($_GET['path']) ? realpath($_GET['path']) : $base_dir;

if (strpos($current_dir, $base_dir) !== 0) {
    die('Access denied!');
}

// ===== File Upload =====
if (isset($_FILES['file'])) {
    move_uploaded_file($_FILES['file']['tmp_name'], $current_dir . '/' . $_FILES['file']['name']);
    echo "<p>File uploaded!</p>";
}

// ===== File Delete =====
if (isset($_GET['delete'])) {
    $file_to_delete = realpath($current_dir . '/' . $_GET['delete']);
    if (strpos($file_to_delete, $base_dir) === 0 && is_file($file_to_delete)) {
        unlink($file_to_delete);
        echo "<p>File deleted!</p>";
    } else {
        echo "<p>Delete failed!</p>";
    }
}

// ===== Save Edited File =====
if (isset($_POST['save']) && isset($_POST['filename'])) {
    $file_to_save = realpath($current_dir . '/' . $_POST['filename']);
    if (strpos($file_to_save, $base_dir) === 0 && is_file($file_to_save)) {
        file_put_contents($file_to_save, $_POST['content']);
        echo "<p>File saved!</p>";
    } else {
        echo "<p>Save failed!</p>";
    }
}

// ===== Rename File/Folder =====
if (isset($_POST['rename']) && isset($_POST['old_name']) && isset($_POST['new_name'])) {
    $old_path = realpath($current_dir . '/' . $_POST['old_name']);
    $new_path = $current_dir . '/' . basename($_POST['new_name']);
    if (strpos($old_path, $base_dir) === 0 && file_exists($old_path)) {
        if (rename($old_path, $new_path)) {
            echo "<p>Renamed successfully!</p>";
        } else {
            echo "<p>Rename failed!</p>";
        }
    }
}

// ===== Compress Files/Folders and Download =====
if (isset($_POST['compress']) && !empty($_POST['selected_items'])) {
    $zip_name = 'download_' . time() . '.zip';
    $zip_path = $current_dir . '/' . $zip_name;

    $zip = new ZipArchive();
    if ($zip->open($zip_path, ZipArchive::CREATE) === TRUE) {
        foreach ($_POST['selected_items'] as $item) {
            $real_path = realpath($current_dir . '/' . $item);
            if (strpos($real_path, $base_dir) === 0) {
                if (is_file($real_path)) {
                    $zip->addFile($real_path, $item);
                } elseif (is_dir($real_path)) {
                    $folder_files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($real_path, RecursiveDirectoryIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::SELF_FIRST
                    );
                    foreach ($folder_files as $file) {
                        $file_real = $file->getRealPath();
                        $relative_path = $item . '/' . substr($file_real, strlen($real_path) + 1);
                        if (is_file($file_real)) {
                            $zip->addFile($file_real, $relative_path);
                        } elseif (is_dir($file_real)) {
                            $zip->addEmptyDir($relative_path);
                        }
                    }
                }
            }
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($zip_path) . '"');
        header('Content-Length: ' . filesize($zip_path));
        readfile($zip_path);
        unlink($zip_path);
        exit;
    } else {
        echo "<p>ZIP creation failed!</p>";
    }
}

$items = scandir($current_dir);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Advanced File Manager</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        a { text-decoration: none; }
        ul { list-style: none; padding: 0; }
        li { margin: 5px 0; }
        input[type="checkbox"] { margin-right: 10px; }
        .rename-form { display:inline; }
    </style>
</head>
<body>

<h2>Current Directory: <?php echo htmlspecialchars($current_dir); ?></h2>

<h3>Upload File</h3>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>

<h3>Files and Folders</h3>
<form method="post">
    <ul>
        <?php if ($current_dir != $base_dir): ?>
            <li><a href="?path=<?php echo urlencode(dirname($current_dir)); ?>">[..] Go Up</a></li>
        <?php endif; ?>
        <?php foreach ($items as $item): ?>
            <?php if ($item == '.' || $item == '..') continue; ?>
            <?php $item_path = $current_dir . '/' . $item; ?>
            <li>
                <input type="checkbox" name="selected_items[]" value="<?php echo htmlspecialchars($item); ?>">
                <?php if (is_dir($item_path)): ?>
                    <strong><a href="?path=<?php echo urlencode($item_path); ?>">[Folder] <?php echo htmlspecialchars($item); ?></a></strong>
                <?php else: ?>
                    <?php echo htmlspecialchars($item); ?>
                <?php endif; ?>

                <!-- Delete Option -->
                <?php if (is_file($item_path)): ?>
                    [<a href="?path=<?php echo urlencode($current_dir); ?>&delete=<?php echo urlencode($item); ?>" onclick="return confirm('Delete this file?')">Delete</a>]
                <?php endif; ?>

                <!-- Edit Option -->
                <?php if (is_file($item_path)): ?>
                    [<a href="?path=<?php echo urlencode($current_dir); ?>&edit=<?php echo urlencode($item); ?>">Edit</a>]
                <?php endif; ?>

                <!-- Rename Option -->
                <form class="rename-form" method="post" style="display:inline;">
                    <input type="hidden" name="old_name" value="<?php echo htmlspecialchars($item); ?>">
                    <input type="text" name="new_name" placeholder="New name" required>
                    <button type="submit" name="rename">Rename</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <button type="submit" name="compress">Compress & Download ZIP</button>
</form>

<?php
// ===== Edit Form =====
if (isset($_GET['edit'])):
    $edit_file = realpath($current_dir . '/' . $_GET['edit']);
    if (strpos($edit_file, $base_dir) === 0 && is_file($edit_file)):
        $content = htmlspecialchars(file_get_contents($edit_file));
?>
<h3>Editing File: <?php echo htmlspecialchars($_GET['edit']); ?></h3>
<form method="post">
    <input type="hidden" name="filename" value="<?php echo htmlspecialchars($_GET['edit']); ?>">
    <textarea name="content" rows="20" style="width:100%;"><?php echo $content; ?></textarea><br>
    <button type="submit" name="save">Save File</button>
</form>
<?php
    endif;
endif;
?>

</body>
</html>
