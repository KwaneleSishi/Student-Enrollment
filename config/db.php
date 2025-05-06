<?php
// config/db.php
$host = 'localhost';
$dbname = 'ses_db';
$user = 'root';
$pass = 'Kwanele@050509';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
