<?php
// instructor/dashboard.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../login.php");
    exit();
}

// Fetch instructor details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$instructor = $stmt->fetch(PDO::FETCH_ASSOC);
$instructor_name = $instructor['first_name'] . ' ' . $instructor['last_name'];

// Fetch courses taught by the instructor
$stmt = $conn->prepare("
    SELECT c.*, d.name AS department_name
    FROM courses c
    JOIN departments d ON c.department_id = d.id
    WHERE c.instructor_id = :user_id
");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .welcome-message h1 { font-size: 2.2rem; margin-bottom: 0.5rem; }
        .welcome-message p { font-size: 1.1rem; color: var(--text-light); }
        .course-card .course-actions .btn { padding: 10px 20px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search for courses">
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="active">My Courses</a>
                <a href="profile.php">Profile</a>
            </div>
            <div class="user-menu">
                <div class="user-avatar"><?php echo htmlspecialchars($instructor['first_name'][0] . $instructor['last_name'][0]); ?></div>
            </div>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-menu">
                <h3>Navigation</h3>
                <ul>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                    <li><a href="manage_content.php"><i class="fas fa-book"></i> <span>Manage Courses</span></a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="dashboard-header">
                <div class="welcome-message">
                    <h1>Welcome back, <?php echo htmlspecialchars($instructor_name); ?>! 👋</h1>
                    <p>Manage your courses and content</p>
                </div>
            </div>

            <div class="section-header">
                <h2>My Courses</h2>
            </div>

            <div class="courses-grid">
                <?php if (empty($courses)): ?>
                    <p>You are not teaching any courses.</p>
                <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                        <?php
                        $image_url = $course['image_url'];
                        if (strpos($image_url, 'http') !== 0 && strpos($image_url, '/SES1/') !== 0) {
                            $image_url = '/SES1/assets/uploads/' . basename($image_url);
                        }
                        ?>
                        <div class="course-card">
                            <div class="course-image">
                                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" onerror="this.onerror=null; this.src='/SES1/assets/uploads/fallback.jpg';">
                                <div class="course-badge"><?php echo htmlspecialchars($course['department_name']); ?></div>
                            </div>
                            <div class="course-content">
                                <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                                <p class="course-instructor"><?php echo $course['credits']; ?> Credits • <?php echo $course['current_enrollment']; ?> Students</p>
                                <div class="course-actions">
                                    <a href="manage_content.php?course_id=<?php echo $course['id']; ?>" class="btn btn-primary btn-block">Manage Course</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>