<?php
session_start();
header('Content-Type: application/json');

// Authentication and authorization check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'Kwanele@050509';
$db_name = 'ses_db';

// Action handling (backup or recover)
$action = isset($_GET['action']) ? $_GET['action'] : 'backup';

if ($action === 'backup') {
    // Configure backup settings
    $backup_dir = realpath(__DIR__ . '/../backups/');
    if (!file_exists($backup_dir) || !is_writable($backup_dir)) {
        if (!mkdir($backup_dir, 0755, true)) {
            echo json_encode(['success' => false, 'message' => 'Cannot create backup directory']);
            exit;
        }
    }

    $filename = 'ses_backup_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.sql';
    $file_path = $backup_dir . '/' . $filename;

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Lock tables for reading to prevent changes during backup
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $lock_stmt = $pdo->prepare("LOCK TABLES " . implode(" READ, ", $tables) . " READ");
        $lock_stmt->execute();

        // Open file for writing
        $handle = fopen($file_path, 'w');
        if ($handle === false) {
            throw new Exception('Cannot create backup file');
        }

        // Export database structure
        foreach ($tables as $table) {
            fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
            $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
            fwrite($handle, $create['Create Table'] . ";\n\n");
        }

        // Export data with streaming for large tables
        foreach ($tables as $table) {
            $stmt = $pdo->query("SELECT * FROM `$table`");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $values = implode(', ', array_map(function($value) use ($pdo) {
                    if (is_null($value)) {
                        return 'NULL';
                    }
                    return $pdo->quote($value);
                }, $row));
                fwrite($handle, "INSERT INTO `$table` VALUES ($values);\n");
            }
            fwrite($handle, "\n");
        }

        // Unlock tables
        $pdo->query("UNLOCK TABLES");
        fclose($handle);

        // Serve file via a secure download script
        $download_url = '/download.php?file=' . urlencode($filename);
        echo json_encode(['success' => true, 'message' => 'Backup created', 'download_url' => $download_url]);
    } catch (Exception $e) {
        try {
            $pdo->query("UNLOCK TABLES");
        } catch (Exception $unlockException) {
            // Ignore unlock errors
        }
        if (isset($handle) && is_resource($handle)) {
            fclose($handle);
        }
        echo json_encode(['success' => false, 'message' => 'Backup failed: ' . $e->getMessage()]);
    }
} elseif ($action === 'recover') {
    // Handle database recovery
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['backup_file'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    $file = $_FILES['backup_file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
        exit;
    }

    // Validate file type and size (max 50MB)
    $max_size = 50 * 1024 * 1024; // 50MB in bytes
    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'File too large (max 50MB)']);
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if ($mime_type !== 'text/plain' || pathinfo($file['name'], PATHINFO_EXTENSION) !== 'sql') {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only .sql files allowed']);
        exit;
    }

    // Move uploaded file to a temporary location
    $temp_path = sys_get_temp_dir() . '/' . uniqid('ses_recovery_') . '.sql';
    if (!move_uploaded_file($file['tmp_name'], $temp_path)) {
        echo json_encode(['success' => false, 'message' => 'Failed to process file']);
        exit;
    }

    try {
        // Use mysql command to restore the database
        $mysql_path = 'C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe'; // Adjust path as needed
        $command = "\"$mysql_path\" -h $db_host -u $db_user -p\"$db_pass\" $db_name < \"$temp_path\" 2>&1";
        exec($command, $output, $exit_code);

        if ($exit_code !== 0) {
            throw new Exception('Recovery failed: ' . implode("\n", $output));
        }

        // Clean up temporary file
        unlink($temp_path);
        echo json_encode(['success' => true, 'message' => 'Database recovery successful']);
    } catch (Exception $e) {
        if (file_exists($temp_path)) {
            unlink($temp_path);
        }
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>