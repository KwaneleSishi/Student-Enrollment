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
$stmt = $conn->prepare("SELECT completed_lessons, total_grade FROM enrollments WHERE StudentID = :user_id AND CourseID = :course_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
$completed_lessons = json_decode($enrollment['completed_lessons'] ?? '[]', true) ?: [];
$total_grade = $enrollment['total_grade'] ?? 0;

// Calculate lesson grade (3 points per lesson)
$lesson_grade = count($completed_lessons) * 3;

// Handle marking as complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete'])) {
    $lesson_number = (int)$_POST['lesson_number'];
    if (!in_array($lesson_number, $completed_lessons)) {
        $completed_lessons[] = $lesson_number;
        $completed_lessons = array_unique($completed_lessons);
        $lesson_grade = count($completed_lessons) * 3; // Recalculate lesson grade

        $stmt = $conn->prepare("UPDATE enrollments SET completed_lessons = :completed, total_grade = :grade WHERE StudentID = :user_id AND CourseID = :course_id");
        $stmt->bindParam(':completed', json_encode($completed_lessons), PDO::PARAM_STR);
        $stmt->bindParam(':grade', $lesson_grade, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    header("Location: course_content.php?id=$course_id&lesson=$selected_lesson");
    exit();
}

// Get selected lesson content
$selected_content = array_filter($lessons, fn($lesson) => $lesson['lesson_number'] == $selected_lesson);
$selected_content = !empty($selected_content) ? reset($selected_content) : ['title' => 'Lesson Not Available', 'youtube_url' => '', 'notes' => ''];

// Check quiz eligibility and fetch questions
$show_quiz = false;
$quiz_questions = [];
$quiz_score = null;
$retry_quiz = isset($_GET['retry']) && $_GET['retry'] === 'true';

if (count($completed_lessons) === 4 && in_array(1, $completed_lessons) && in_array(2, $completed_lessons) && in_array(3, $completed_lessons) && in_array(4, $completed_lessons)) {
    $stmt = $conn->prepare("SELECT score FROM quiz_attempts WHERE enrollment_id = (SELECT EnrollmentID FROM enrollments WHERE StudentID = :user_id AND CourseID = :course_id) ORDER BY completed_at DESC LIMIT 1");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $last_score = $stmt->fetchColumn();

    if ($retry_quiz || $last_score === false || empty($quiz_questions)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/SES1/student/take_quiz.php?course_id=$course_id&user_id=$user_id&role=student");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $quiz_data = json_decode($response, true);
        if ($quiz_data['success']) {
            $show_quiz = true;
            $quiz_questions = $quiz_data['questions'];
        }
    } else {
        $quiz_score = $last_score;
    }
}

// Handle quiz submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $answers = [];
    for ($i = 1; $i <= 8; $i++) {
        $answers[$i] = isset($_POST['q' . $i]) ? (int)$_POST['q' . $i] : 0;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/SES1/student/submit_quiz.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'course_id' => $course_id,
        'answers' => json_encode($answers),
        'user_id' => $user_id,
        'role' => 'student'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    $quiz_result = json_decode($response, true);

    if ($quiz_result['success']) {
        $quiz_score = $quiz_result['score'];
        $total_grade = $quiz_result['total_grade'];
        $show_quiz = false; // Hide quiz form after submission
        header("Location: course_content.php?id=$course_id&lesson=4");
        exit();
    }
}
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

        #quiz-section {
            margin-top: 20px;
        }

        #quiz-form div {
            margin-bottom: 15px;
        }

        #quiz-form label {
            display: block;
            margin-left: 20px;
        }

        #results {
            margin-top: 20px;
            display: <?php echo $quiz_score !== null ? 'block' : 'none'; ?>;
        }

        #retry-quiz {
            background: #6b48ff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
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

                    <!-- Quiz Section -->
                    <div id="quiz-section" style="display: <?php echo $show_quiz ? 'block !important' : 'none'; ?>;">
                        <h3>Course Quiz</h3>
                        <form id="quiz-form" method="POST">
                            <?php foreach ($quiz_questions as $q): ?>
                                <div>
                                    <p><?php echo htmlspecialchars($q['question_number']) . '. ' . htmlspecialchars($q['question_text']); ?></p>
                                    <label><input type="radio" name="q<?php echo $q['question_number']; ?>" value="1" required> <?php echo htmlspecialchars($q['choice_1']); ?></label><br>
                                    <label><input type="radio" name="q<?php echo $q['question_number']; ?>" value="2"> <?php echo htmlspecialchars($q['choice_2']); ?></label><br>
                                    <label><input type="radio" name="q<?php echo $q['question_number']; ?>" value="3"> <?php echo htmlspecialchars($q['choice_3']); ?></label><br>
                                    <label><input type="radio" name="q<?php echo $q['question_number']; ?>" value="4"> <?php echo htmlspecialchars($q['choice_4']); ?></label>
                                </div>
                            <?php endforeach; ?>
                            <button type="submit" name="submit_quiz">Submit Quiz</button>
                        </form>
                    </div>

                    <!-- Results Section -->
                    <div id="results">
                        <h3>Quiz Results</h3>
                        <p>Your Score: <?php echo $quiz_score ?? 0; ?>/16</p>
                        <p>Total Grade: <?php echo $total_grade; ?>/28</p>
                        <a href="course_content.php?id=<?php echo $course_id; ?>&lesson=4&retry=true"><button id="retry-quiz">Retry Quiz</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>