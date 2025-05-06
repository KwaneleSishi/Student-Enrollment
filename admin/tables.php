<?php
// admin/tables.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get all tables
$tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<!-- Similar header -->
<div class="content">
    <div class="dashboard-header">
        <h1>Database Tables</h1>
    </div>

    <div class="tables-list">
        <?php foreach ($tables as $table): ?>
        <div class="table-card">
            <h3><?= $table ?></h3>
            <a href="view_table.php?table=<?= $table ?>" class="btn btn-outline">
                View Data
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>