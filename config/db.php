<?php
// config/db.php
$host = '127.0.0.1';
$dbname = 'ses_db_test';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass,[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ]);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>

