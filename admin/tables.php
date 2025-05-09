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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Tables | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            <div class="user-menu">
                <div class="user-avatar"><?php echo htmlspecialchars(substr($_SESSION['first_name'], 0, 1) . substr($_SESSION['last_name'], 0, 1)); ?></div>
            </div>
        </div>
    </header>

    <div class="main-layout">
        <!-- Admin Sidebar -->
        <div class="sidebar">
            <div class="sidebar-menu">
                <h3>Admin Panel</h3>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="courses.php"><i class="fas fa-book"></i> Manage Courses</a></li>
                    <li><a href="students.php"><i class="fas fa-users"></i> Manage Students</a></li>
                    <li><a href="register.php"><i class="fas fa-users"></i> Add Instructors</a></li>
                    <li><a href="tables.php" class="active"><i class="fas fa-database"></i> View Tables</a></li>
                    <li><a href="backup.php"><i class="fas fa-database"></i> Backup Database</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="dashboard-header">
                <h1>Database Tables</h1>
            </div>
            <div class="courses-grid">
                <?php foreach ($tables as $table): ?>
                    <div class="course-card">
                        <div class="course-content">
                            <div class="course-title"><?php echo htmlspecialchars($table); ?></div>
                            <div class="course-actions">
                                <a href="view_table.php?table=<?php echo urlencode($table); ?>" class="btn btn-outline">View Data</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>