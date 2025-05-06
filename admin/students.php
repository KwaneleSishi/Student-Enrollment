<?php
// admin/students.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle student actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    switch ($action) {
        case 'delete':
            $conn->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
            break;
    }
}

// Get all students
$students = $conn->query("SELECT * FROM users WHERE role = 'student'")->fetchAll();
?>

<!DOCTYPE html>
<!-- Similar header -->
<div class="content">
    <div class="dashboard-header">
        <h1>Manage Students</h1>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></td>
                <td><?= htmlspecialchars($student['email']) ?></td>
                <td><?= date('M d, Y', strtotime($student['created_at'])) ?></td>
                <td>
                    <a href="edit_student.php?id=<?= $student['id'] ?>" 
                       class="btn btn-sm btn-outline">Edit</a>
                    <a href="?action=delete&id=<?= $student['id'] ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>