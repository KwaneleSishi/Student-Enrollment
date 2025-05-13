<?php
// student/academic_progress.php
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

// Fetch completed courses with grades
$stmt = $conn->prepare("
    SELECT c.*, d.name AS department_name, 
           CONCAT(u.first_name, ' ', u.last_name) AS instructor_name,
           e.completed_lessons, e.total_grade, e.EnrollmentID
    FROM enrollments e
    JOIN courses c ON e.CourseID = c.id
    JOIN departments d ON c.department_id = d.id
    JOIN users u ON c.instructor_id = u.id
    WHERE e.StudentID = :user_id
");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$completed_courses = [];
foreach ($enrollments as $enrollment) {
    $course_id = $enrollment['id'];
    $completed_lessons = json_decode($enrollment['completed_lessons'] ?? '[]', true) ?: [];

    // Check total lessons for the course
    $stmt = $conn->prepare("SELECT COUNT(*) FROM course_content WHERE course_id = :course_id");
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $total_lessons = $stmt->fetchColumn();

    // Check if quiz has been attempted
    $stmt = $conn->prepare("SELECT COUNT(*) FROM quiz_attempts WHERE enrollment_id = :enrollment_id");
    $stmt->bindParam(':enrollment_id', $enrollment['EnrollmentID'], PDO::PARAM_INT);
    $stmt->execute();
    $quiz_attempted = $stmt->fetchColumn() > 0;

    // A course is completed if all lessons are done and the quiz has been attempted
    if ($total_lessons > 0 && count($completed_lessons) >= $total_lessons && $quiz_attempted) {
        // Calculate lesson mark (3 points per lesson)
        $lesson_mark = count($completed_lessons) * 3;

        // Fetch the latest quiz score
        $stmt = $conn->prepare("SELECT score FROM quiz_attempts WHERE enrollment_id = :enrollment_id ORDER BY completed_at DESC LIMIT 1");
        $stmt->bindParam(':enrollment_id', $enrollment['EnrollmentID'], PDO::PARAM_INT);
        $stmt->execute();
        $quiz_mark = $stmt->fetchColumn() ?: 0;

        // Add lesson and quiz marks to the enrollment data
        $enrollment['lesson_mark'] = $lesson_mark;
        $enrollment['quiz_mark'] = $quiz_mark;

        $completed_courses[] = $enrollment;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Academic Progress | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .course-actions .btn {
            padding: 10px 20px;
            font-size: 0.9rem;
        }

        .course-card .grade-details {
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .course-card .grade-details p {
            margin: 2px 0;
        }

        .course-card .lesson-mark {
            color: #007bff;
        }

        .course-card .quiz-mark {
            color: #ff9800;
        }

        .course-card .total-grade {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            <div class="nav-links">
                <a href="dashboard.php">My Learning</a>
                <a href="catalog.php">Course Catalog</a>
                <a href="academic_progress.php" class="active">My Grades</a>
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
                <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="catalog.php"><i class="fas fa-compass"></i> <span>Course Catalog</span></a></li>
                <li><a href="academic_progress.php" class="active"><i class="fas fa-chart-line"></i> <span>Academic Progress</span></a></li>
            </ul>
            <h3>Account</h3>
            <ul>
                <li><a href="profile.php"><i class="fas fa-user-cog"></i> <span>Profile</span></a></li>
                <li><a href="/SES1/index.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        <div class="content">
            <div class="dashboard-header">
                <h1>Academic Progress, <?php echo htmlspecialchars($student_name); ?> 📊</h1>
                <p>View your completed courses and grades</p>
            </div>
            <div class="section-header">
                <h2>Completed Courses</h2>
            </div>
            <div class="courses-grid">
                <?php if (empty($completed_courses)): ?>
                    <p>You have not completed any courses yet.</p>
                <?php else: ?>
                    <?php foreach ($completed_courses as $course): ?>
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
                                <div class="grade-details">
                                    <p class="lesson-mark">Lesson Mark: <?php echo htmlspecialchars($course['lesson_mark']); ?>/12</p>
                                    <p class="quiz-mark">Quiz Mark: <?php echo htmlspecialchars($course['quiz_mark']); ?>/16</p>
                                    <p class="total-grade">Total Grade: <?php echo htmlspecialchars($course['total_grade']); ?>/28</p>
                                </div>
                                <div class="course-actions">
                                    <a href="course_content.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-block">View Course</a>
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