<?php
// admin/backup.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Configure backup settings
$backup_dir = __DIR__ . '/../backups/'; // Outside web root
$max_backups = 5;
$date = date('Y-m-d_H-i-s');
$backup_file = $backup_dir . "ses_db_backup_$date.sql";

try {
    // Create backup directory if not exists
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }

    // Verify directory permissions
    if (!is_writable($backup_dir)) {
        throw new Exception("Backup directory is not writable");
    }

    // Build mysqldump command
    $command = sprintf(
        'mysqldump --user=%s --password=%s --host=%s %s > %s',
        escapeshellarg($user),
        escapeshellarg($pass),
        escapeshellarg($host),
        escapeshellarg($dbname),
        escapeshellarg($backup_file)
    );

    // Execute command
    exec($command, $output, $return_var);

    if ($return_var !== 0) {
        throw new Exception("Backup failed with error code: $return_var");
    }

    // Cleanup old backups
    $files = glob($backup_dir . "ses_db_backup_*.sql");
    if (count($files) > $max_backups) {
        array_multisort(
            array_map('filemtime', $files),
            SORT_NUMERIC,
            SORT_ASC,
            $files
        );
        unlink($files[0]);
    }

    // Download the backup
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
    readfile($backup_file);
    exit;

} catch (Exception $e) {
    die("Backup error: " . $e->getMessage());
}

// Alternative backup using PHP
$tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

$sql = "";
foreach ($tables as $table) {
    $sql .= "DROP TABLE IF EXISTS `$table`;\n";
    $create = $conn->query("SHOW CREATE TABLE `$table`")->fetch();
    $sql .= $create['Create Table'] . ";\n\n";
    
    $data = $conn->query("SELECT * FROM `$table`");
    while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
        $sql .= "INSERT INTO `$table` VALUES(";
        $sql .= implode(", ", array_map(fn($v) => $conn->quote($v), $row));
        $sql .= ");\n";
    }
    $sql .= "\n";
}

file_put_contents($backup_file, $sql);