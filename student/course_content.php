<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$selected_lesson = isset($_GET['lesson']) ? (int)$_GET['lesson'] : 1;
$selected_lesson = max(1, min(4, $selected_lesson)); // Ensure lesson is between 1 and 4

$stmt = $conn->prepare("SELECT c.title, d.name AS department_name FROM courses c JOIN departments d ON c.department_id = d.id WHERE c.id = :course_id");
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    header("Location: dashboard.php");
    exit();
}

// Fetch lessons for the course
$stmt = $conn->prepare("SELECT * FROM course_content WHERE course_id = :course_id ORDER BY lesson_number");
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check completed lessons
$stmt = $conn->prepare("SELECT completed_lessons FROM enrollments WHERE StudentID = :user_id AND CourseID = :course_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
$completed_lessons = json_decode($enrollment['completed_lessons'] ?? '[]', true) ?: [];

// Handle marking as complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete'])) {
    $lesson_number = (int)$_POST['lesson_number'];
    $completed_lessons[] = $lesson_number;
    $completed_lessons = array_unique($completed_lessons);

    $stmt = $conn->prepare("UPDATE enrollments SET completed_lessons = :completed WHERE StudentID = :user_id AND CourseID = :course_id");
    $stmt->bindParam(':completed', json_encode($completed_lessons), PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: course_content.php?id=$course_id&lesson=$selected_lesson");
    exit();
}

// Get selected lesson content
$selected_content = array_filter($lessons, fn($lesson) => $lesson['lesson_number'] == $selected_lesson);
$selected_content = !empty($selected_content) ? reset($selected_content) : ['title' => 'Lesson Not Available', 'youtube_url' => '', 'notes' => ''];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($course['title']); ?> | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .course-content-container {
            display: flex;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .lesson-sidebar {
            flex: 1;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .lesson-list {
            list-style: none;
            padding: 0;
        }

        .lesson-item {
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .lesson-item.active {
            background: #6b48ff;
            color: #fff;
        }

        .lesson-item.completed::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: #28a745;
            margin-left: 10px;
        }

        .lesson-item.active.completed::after {
            color: #fff;
        }

        .main-content {
            flex: 3;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            /* 16:9 aspect ratio */
            height: 0;
            margin-bottom: 20px;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .fallback-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .notes-section {
            margin-top: 20px;
        }

        .notes-section h3 {
            margin-bottom: 10px;
        }

        .btn-complete {
            background: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-complete:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .course-content-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            <div class="nav-links">
                <a href="dashboard.php">My Learning</a>
            </div>
            <div class="user-menu">
                <div class="user-avatar"><?php echo htmlspecialchars(substr($_SESSION['first_name'], 0, 1) . substr($_SESSION['last_name'], 0, 1)); ?></div>
            </div>
        </div>
    </header>
    <div class="main-layout">
        <div class="content">
            <h1><?php echo htmlspecialchars($course['title']); ?> (<?php echo htmlspecialchars($course['department_name']); ?>)</h1>
            <div class="course-content-container">
                <div class="lesson-sidebar">
                    <ul class="lesson-list">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <?php
                            $lesson = array_filter($lessons, fn($l) => $l['lesson_number'] == $i);
                            $lesson = !empty($lesson) ? reset($lesson) : ['title' => "Lesson $i"];
                            $is_active = $selected_lesson == $i;
                            $is_completed = in_array($i, $completed_lessons);
                            ?>
                            <li class="lesson-item <?php echo $is_active ? 'active' : ''; ?> <?php echo $is_completed ? 'completed' : ''; ?>">
                                <a href="course_content.php?id=<?php echo $course_id; ?>&lesson=<?php echo $i; ?>">
                                    <?php echo htmlspecialchars($lesson['title']); ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
                <div class="main-content">
                    <h2><?php echo htmlspecialchars($selected_content['title']); ?></h2>
                    <?php if (!empty($selected_content['youtube_url'])): ?>
                        <?php
                        // Extract YouTube video ID
                        $video_id = '';
                        if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?([a-zA-Z0-9_-]+)/', $selected_content['youtube_url'], $match)) {
                            $video_id = $match[1];
                        }
                        ?>
                        <?php if ($video_id): ?>
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/<?php echo $video_id; ?>" frameborder="0" allowfullscreen></iframe>
                            </div>
                        <?php else: ?>
                            <img src="../assets/uploads/fallback.jpg" alt="Lesson Image" class="fallback-image">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="../assets/uploads/fallback.jpg" alt="Lesson Image" class="fallback-image">
                    <?php endif; ?>
                    <div class="notes-section">
                        <h3>Notes</h3>
                        <p><?php echo nl2br(htmlspecialchars($selected_content['notes'])); ?></p>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="lesson_number" value="<?php echo $selected_lesson; ?>">
                        <button type="submit" name="mark_complete" class="btn-complete" <?php echo in_array($selected_lesson, $completed_lessons) ? 'disabled' : ''; ?>>
                            <?php echo in_array($selected_lesson, $completed_lessons) ? 'Completed' : 'Mark as Complete'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>