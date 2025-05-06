<?php
// index.php
session_start();
require_once 'config/db.php';

// Get selected department from URL
$dept_filter = $_GET['dept'] ?? 'all';

// Base SQL query
$sql = "SELECT c.*, d.name AS department_name, 
        CONCAT(u.first_name, ' ', u.last_name) AS instructor_name
        FROM courses c
        JOIN departments d ON c.department_id = d.id
        JOIN users u ON c.instructor_id = u.id";

// Add department filter if needed
if ($dept_filter !== 'all' && is_numeric($dept_filter)) {
    $sql .= " WHERE c.department_id = :dept_id";
}

// Get departments for filter menu
$dept_stmt = $conn->query("SELECT * FROM departments");
$departments = $dept_stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare and execute course query
$stmt = $conn->prepare($sql);
if ($dept_filter !== 'all' && is_numeric($dept_filter)) {
    $stmt->bindParam(':dept_id', $dept_filter, PDO::PARAM_INT);
}
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Catalog | SESAcademy</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<style>/* Auth Navigation */
.auth-nav {
  display: flex;
  gap: 15px;
  align-items: center;
}

.btn {
  padding: 10px 20px;
  border-radius: 4px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-outline {
  color: #5624d0;
  border: 1px solid #5624d0;
}

.btn-outline:hover {
  background: #f0f2f5;
}

.btn-primary {
  background: #5624d0;
  color: white;
}

.btn-primary:hover {
  background: #401b9c;
}
</style>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search for courses" id="search-input">
            </div>

            <div class="auth-nav">
                <a href="login.php" class="btn btn-outline">Log in</a>
                <a href="register.php" class="btn btn-primary">Sign up</a>
            </div>


        </div>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
            <!-- Sidebar -->
            <!-- Department Menu -->
            <div class="sidebar">
                <div class="sidebar-menu">
                    <h3>Departments</h3>
                    <ul id="department-menu">
                        <li><a href="?dept=all" class="<?= $dept_filter === 'all' ? 'active' : '' ?>">
                            <i class="fas fa-th-large"></i> <span>All Departments</span>
                        </a></li>
                        <?php foreach ($departments as $dept): ?>
                        <li><a href="?dept=<?= $dept['id'] ?>" class="<?= $dept_filter == $dept['id'] ? 'active' : '' ?>">
                            <i class="fas fa-building"></i> <span><?= htmlspecialchars($dept['name']) ?></span>
                        </a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

         <!-- Content -->
        <div class="content">
            <div class="dashboard-header">
                <h1>Course Catalog</h1>
                <p>Browse and enroll in courses</p>
            </div>
            
            <div class="section-header">
                <h2>Available Courses</h2>
                <div class="filters">
                    <select id="credits-filter" class="form-control">
                        <option value="all">All Credits</option>
                        <option value="3">3 Credits</option>
                        <option value="4">4 Credits</option>
                    </select>
                </div>
            </div>

            <!-- courses-grid -->
            <div class="courses-grid">
            <?php foreach ($courses as $course): ?>
            <div class="course-card">
                <div class="course-image">
                    <img src="<?= htmlspecialchars($course['image_url']) ?>" alt="<?= $course['title'] ?>">
                    <div class="course-badge"><?= $course['department_name'] ?></div>
                </div>
                <div class="course-content">
                    <h3><?= htmlspecialchars($course['title']) ?></h3>
                    <p><?= htmlspecialchars($course['instructor_name']) ?> • <?= $course['credits'] ?> Credits</p>
                    <!-- Add other course details -->
                </div>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2023 SES Academy. All rights reserved.</p>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>