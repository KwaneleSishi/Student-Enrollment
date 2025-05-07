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

// Fetch enrolled courses
$stmt = $conn->prepare("
    SELECT c.*, d.name AS department_name, 
           CONCAT(u.first_name, ' ', u.last_name) AS instructor_name
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
$total_points = 0;
$total_courses = 0;
$total_credits = 0;

// Note: The schema does not include a 'grade' column in enrollments, so we skip grade-based calculations
foreach ($enrolled_courses as $course) {
    $total_credits += $course['credits'];
}
$gpa = 'N/A'; // GPA cannot be calculated without grades
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced Dashboard Styles */
        .welcome-message h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .welcome-message p {
            font-size: 1.1rem;
            color: var(--text-light);
        }

        .stat-card.highlight-card {
            grid-column: span 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary-color), #6933ff);
            color: white;
        }

        .stat-card.highlight-card .stat-content h3 {
            color: white;
            margin-bottom: 1rem;
        }

        .stat-card.highlight-card .stat-icon {
            font-size: 3rem;
            opacity: 0.8;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-card .value {
            font-size: 2.2rem;
            margin: 1rem 0;
        }

        .loading-spinner {
            text-align: center;
            padding: 2rem;
            grid-column: 1 / -1;
        }

        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 4px solid var(--primary-color);
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .course-card .course-actions .btn {
            padding: 10px 20px;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .stat-card.highlight-card {
                grid-column: span 1;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .welcome-message h1 {
                font-size: 1.8rem;
            }
        }
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
                <a href="dashboard.php" class="active">My Learning</a>
                <a href="catalog.php">Course Catalog</a>
                <a href="grades.php">My Grades</a>
            </div>
            <div class="user-menu">
                <div class="user-avatar"><?php echo htmlspecialchars($student['first_name'][0] . $student['last_name'][0]); ?></div>
            </div>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
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
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="dashboard-header">
                <div class="welcome-message">
                    <h1>Welcome back, <span id="student-name"><?php echo htmlspecialchars($student_name); ?>!</span> 👋</h1>
                    <p class="text-muted">Track your progress and continue learning</p>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card highlight-card">
                    <div class="stat-content">
                        <h3>📚 Enrolled Courses</h3>
                        <div class="value"><?php echo $enrolled_count; ?></div>
                        <div class="trend up">
                            <i class="fas fa-arrow-up"></i> <?php echo $enrolled_count; ?> this term
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
                <div class="stat-card highlight-card">
                    <div class="stat-content">
                        <h3>🎓 Completed Courses</h3>
                        <div class="value"><?php echo $completed_count; ?></div>
                        <div class="trend up">
                            <i class="fas fa-arrow-up"></i> <?php echo $completed_count; ?> since last term
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>📊 Current GPA</h3>
                    <div class="value"><?php echo $gpa; ?></div>
                    <div class="trend up">
                        <i class="fas fa-arrow-up"></i> Not Available
                    </div>
                </div>
                <div class="stat-card">
                    <h3>⏳ Credits Earned</h3>
                    <div class="value"><?php echo $total_credits; ?></div>
                    <div class="trend up">
                        <i class="fas fa-arrow-up"></i> <?php echo $total_credits; ?> this term
                    </div>
                </div>
            </div>

            <div class="section-header">
                <h2>My Active Courses</h2>
                <a href="catalog.php" class="btn btn-outline">
                    View All Courses <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="courses-grid" id="enrolled-courses">
                <?php if (empty($enrolled_courses)): ?>
                    <p>You are not enrolled in any courses.</p>
                <?php else: ?>
                    <?php foreach ($enrolled_courses as $course): ?>
                        <?php
                        // Adjust image URL for local files
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
                                <p class="course-instructor"><?php echo htmlspecialchars($course['instructor_name']); ?> • <?php echo $course['credits']; ?> Credits</p>
                                <div class="course-meta">
                                    <div class="course-rating">
                                        <span class="stars">★★★★★</span>
                                        <span class="value">4.8</span>
                                        <span class="count">(2,345)</span>
                                    </div>
                                </div>
                                <div class="course-actions">
                                    <a href="course-details.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-block">Continue Learning</a>
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