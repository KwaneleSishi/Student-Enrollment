<?php
// student/dashboard.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

// Fetch student details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);
$student_name = $student['first_name'] . ' ' . $student['last_name'];

// Fetch enrolled courses with completion stats
$stmt = $conn->prepare("
    SELECT c.*, d.name AS department_name, 
           CONCAT(u.first_name, ' ', u.last_name) AS instructor_name,
           e.completed_lessons
    FROM enrollments e
    JOIN courses c ON e.CourseID = c.id
    JOIN departments d ON c.department_id = d.id
    JOIN users u ON c.instructor_id = u.id
    WHERE e.StudentID = :user_id
");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$enrolled_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$enrolled_count = count($enrolled_courses);
$completed_count = 0;
$total_credits = 0;
foreach ($enrolled_courses as $course) {
    $total_credits += $course['credits'];
    $completed_lessons = json_decode($course['completed_lessons'] ?? '[]', true) ?: []; // Handle NULL with default empty array
    $stmt = $conn->prepare("SELECT COUNT(*) FROM course_content WHERE course_id = :course_id");
    $stmt->bindParam(':course_id', $course['id'], PDO::PARAM_INT);
    $stmt->execute();
    $total_lessons = $stmt->fetchColumn();
    if ($total_lessons > 0 && count($completed_lessons) >= $total_lessons) {
        $completed_count++;
    }
}
$gpa = 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .course-actions .btn { padding: 10px 20px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            <div class="nav-links">
                <a href="dashboard.php" class="active">My Learning</a>
                <a href="catalog.php">Course Catalog</a>
                <a href="grades.php">My Grades</a>
            </div>
            <div class="user-menu">
                <div class="user-avatar"><?php echo htmlspecialchars($student['first_name'][0] . $student['last_name'][0]); ?></div>
            </div>
        </div>
    </header>
    <div class="main-layout">
        <div class="sidebar-menu">
            <h3>Navigation</h3>
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="catalog.php"><i class="fas fa-compass"></i> <span>Course Catalog</span></a></li>
                <li><a href="grades.php"><i class="fas fa-chart-line"></i> <span>Academic Progress</span></a></li>
            </ul>
            <h3>Account</h3>
            <ul>
                <li><a href="profile.php"><i class="fas fa-user-cog"></i> <span>Profile</span></a></li>
                <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        <div class="content">
            <div class="dashboard-header">
                <h1>Welcome back, <?php echo htmlspecialchars($student_name); ?>! 👋</h1>
                <p>Track your progress and continue learning</p>
            </div>
            <div class="stats-grid">
                <div class="stat-card highlight-card">
                    <h3>📚 Enrolled Courses</h3>
                    <div class="value"><?php echo $enrolled_count; ?></div>
                </div>
                <div class="stat-card highlight-card">
                    <h3>🎓 Completed Courses</h3>
                    <div class="value"><?php echo $completed_count; ?></div>
                </div>
                <div class="stat-card">
                    <h3>📊 Current GPA</h3>
                    <div class="value"><?php echo $gpa; ?></div>
                </div>
                <div class="stat-card">
                    <h3>⏳ Credits Earned</h3>
                    <div class="value"><?php echo $total_credits; ?></div>
                </div>
            </div>
            <div class="section-header">
                <h2>My Active Courses</h2>
                <a href="catalog.php" class="btn btn-outline">View All Courses</a>
            </div>
            <div class="courses-grid">
                <?php if (empty($enrolled_courses)): ?>
                    <p>You are not enrolled in any courses.</p>
                <?php else: ?>
                    <?php foreach ($enrolled_courses as $course): ?>
                        <?php
                        $image_url = $course['image_url'];
                        if (strpos($image_url, '/SES1/') !== 0) {
                            $image_url = '/SES1/assets/uploads/' . basename($image_url);
                        }
                        ?>
                        <div class="course-card">
                            <div class="course-image">
                                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" onerror="this.onerror=null; this.src='/SES1/assets/uploads/fallback.jpg';">
                                <div class="course-badge"><?php echo htmlspecialchars($course['department_name']); ?></div>
                            </div>
                            <div class="course-content">
                                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                                <p><?php echo htmlspecialchars($course['instructor_name']); ?> • <?php echo $course['credits']; ?> Credits</p>
                                <div class="course-actions">
                                    <a href="course_content.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-block">Continue Learning</a>
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