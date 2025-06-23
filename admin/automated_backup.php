<?php
// Configure backup settings
$backup_dir = 'C:/Users/HP/Downloads/SES_Backups/'; // Replace 'YourUser' with your Windows username
if (!file_exists($backup_dir)) {
    if (!mkdir($backup_dir, 0755, true)) {
        die('Cannot create backup directory in Downloads');
    }
} elseif (!is_writable($backup_dir)) {
    die('Downloads backup directory is not writable');
}

$filename = 'ses_backup_auto_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.sql';
$file_path = $backup_dir . $filename;

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'Kwanele@050509';
$db_name = 'ses_db';

// Use mysqldump to export the database
$mysqldump_path = 'C:\wamp64\bin\mysql\mysql8.0.31\bin\mysqldump.exe'; // Adjust path based on your WAMP MySQL version
$command = "\"$mysqldump_path\" -h $db_host -u $db_user -p\"$db_pass\" $db_name > \"$file_path\"";

exec($command, $output, $exit_code);

if ($exit_code !== 0) {
    echo "Automated backup failed: Exit code $exit_code\n";
    print_r($output);
    exit(1);
}

if (!file_exists($file_path) || filesize($file_path) === 0) {
    echo "Automated backup failed: Backup file is empty or not created\n";
    exit(1);
}

echo "Automated backup created: $filename\n";
?>