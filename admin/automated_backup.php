<?php
// Configure backup settings
$backup_dir = realpath(__DIR__ . '/../backups/');
if (!file_exists($backup_dir) || !is_writable($backup_dir)) {
    if (!mkdir($backup_dir, 0755, true)) {
        die('Cannot create backup directory');
    }
}

$filename = 'ses_backup_auto_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.sql';
$file_path = $backup_dir . '/' . $filename;

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'Kwanele@050509';
$db_name = 'ses_db';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();

    $handle = fopen($file_path, 'w');
    if ($handle === false) {
        throw new Exception('Cannot create backup file');
    }

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
        $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        fwrite($handle, $create['Create Table'] . ";\n\n");
    }

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

    $pdo->commit();
    fclose($handle);
    echo "Automated backup created: $filename\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Automated backup failed: " . $e->getMessage() . "\n";
}
?>