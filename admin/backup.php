<?php
session_start();
header('Content-Type: application/json');

// Authentication and authorization check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Configure backup settings
$backup_dir = realpath(__DIR__ . '/../backups/'); // Ensure absolute path outside web root
if (!file_exists($backup_dir) || !is_writable($backup_dir)) {
    if (!mkdir($backup_dir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Cannot create backup directory']);
        exit;
    }
}

$filename = 'ses_backup_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.sql';
$file_path = $backup_dir . '/' . $filename;

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'Kwanele@050509';
$db_name = 'ses_db';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start transaction to ensure consistent state
    $pdo->beginTransaction();

    // Lock tables for reading to prevent changes during backup (optional, depending on needs)
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
                return $pdo->quote($value);
            }, $row));
            fwrite($handle, "INSERT INTO `$table` VALUES ($values);\n");
        }
        fwrite($handle, "\n");
    }

    // Unlock tables and commit transaction
    $pdo->query("UNLOCK TABLES");
    $pdo->commit();
    fclose($handle);
 
    // Serve file via a secure download script
    $download_url = '/download.php?file=' . urlencode($filename);
    echo json_encode(['success' => true, 'message' => 'Backup created', 'download_url' => $download_url]);
} catch (Exception $e) {
    $pdo->rollBack(); // Roll back transaction on failure
    if (isset($handle) && is_resource($handle)) {
        fclose($handle); // Ensure file handle is closed on error
    }
    echo json_encode(['success' => false, 'message' => 'Backup failed: ' . $e->getMessage()]);
}
?>