<?php
// admin/view_table.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$table = $_GET['table'];
$data = $conn->query("SELECT * FROM $table")->fetchAll();
$columns = $data ? array_keys($data[0]) : [];
?>

<!DOCTYPE html>
<!-- Similar header -->
<div class="content">
    <div class="dashboard-header">
        <h1>Viewing Table: <?= $table ?></h1>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <?php foreach ($columns as $col): ?>
                    <th><?= $col ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($row as $value): ?>
                    <td><?= htmlspecialchars($value) ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>