<?php
// admin/dashboard.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get counts for dashboard
$counts = [
    'students' => $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn(),
    'courses' => $conn->query("SELECT COUNT(*) FROM courses")->fetchColumn(),
    'instructors' => $conn->query("SELECT COUNT(*) FROM users WHERE role = 'instructor'")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            <div class="user-menu">
                <div class="user-avatar">A</div>
            </div>
        </div>
    </header>

    <div class="main-layout">
        <!-- Admin Sidebar -->
        <div class="sidebar">
            <div class="sidebar-menu">
                <h3>Admin Panel</h3>
                <ul>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="courses.php"><i class="fas fa-book"></i> Manage Courses</a></li>
                    <li><a href="students.php"><i class="fas fa-users"></i> Manage Students</a></li>
                    <li><a href="register.php"><i class="fas fa-users"></i> Add Instructors</a></li>
                    <li><a href="tables.php"><i class="fas fa-database"></i> View Tables</a></li>
                    <li><a href="backup.php"><i class="fas fa-database"></i> Backup Database</a></li>
                </ul>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="content">
            <div class="dashboard-header">
                <h1>Welcome, <?= $_SESSION['first_name'] ?></h1>
                <p>Administrator Dashboard</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Students</h3>
                    <div class="value"><?= $counts['students'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Courses</h3>
                    <div class="value"><?= $counts['courses'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Instructors</h3>
                    <div class="value"><?= $counts['instructors'] ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>