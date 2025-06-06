<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch courses taught by the instructor
$stmt = $conn->prepare("SELECT c.* FROM courses c WHERE c.instructor_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for adding/updating lessons
$message = '';
$selected_course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$lessons = [];

if ($selected_course_id) {
    $stmt = $conn->prepare("SELECT * FROM course_content WHERE course_id = :course_id ORDER BY lesson_number");
    $stmt->bindParam(':course_id', $selected_course_id, PDO::PARAM_INT);
    $stmt->execute();
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $lesson_map = [];
    foreach ($lessons as $lesson) {
    $lesson_map[$lesson['lesson_number']] = $lesson;
    }

    for ($i = 1; $i <= 4; $i++) {
         $lessons[$i] = $lesson_map[$i] ?? ['lesson_number' => $i, 'title' => '', 'youtube_url' => '', 'notes' => '', 'version' => 0];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_lessons'])) {
    $course_id = (int)$_POST['course_id'];
    $titles = $_POST['title'];
    $youtube_urls = $_POST['youtube_url'];
    $notes = $_POST['notes'];
     
    $conn->beginTransaction();
    $conn->exec("SET innodb_lock_wait_timeout = 10");
    try {
        for ($i = 1; $i <= 4; $i++) {
            $title = trim($titles[$i]);
            $youtube_url = trim($youtube_urls[$i]);
            $note = trim($notes[$i]);
            $current_lesson = array_filter($lessons, fn($l) => $l['lesson_number'] == $i);
            $current_lesson = reset($current_lesson);
            $current_version = $current_lesson['version'] ?? 0;

            if (empty($title) || empty($note)) {
                $message = '<div class="alert alert-danger">Title and notes are required for all lessons!</div>';
                break;
            }

            $stmt = $conn->prepare("SELECT id, version FROM course_content WHERE course_id = :course_id AND lesson_number = :lesson_number FOR UPDATE");
            $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
            $stmt->bindParam(':lesson_number', $i, PDO::PARAM_INT);
            $stmt->execute();
            $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($lesson) {
                $stmt = $conn->prepare("UPDATE course_content SET title = :title, youtube_url = :youtube_url, notes = :notes, version = version + 1 WHERE id = :id AND version = :current_version");
                $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                $stmt->bindParam(':youtube_url', $youtube_url, PDO::PARAM_STR);
                $stmt->bindParam(':notes', $note, PDO::PARAM_STR);
                $stmt->bindParam(':id', $lesson['id'], PDO::PARAM_INT);
                $stmt->bindParam(':current_version', $current_version, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() === 0) {
                    $conn->rollBack();
                    $message = '<div class="alert alert-danger">Conflict detected, please try again.</div>';
                    break;
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO course_content (course_id, lesson_number, title, youtube_url, notes, version) VALUES (:course_id, :lesson_number, :title, :youtube_url, :notes, 0)");
                $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
                $stmt->bindParam(':lesson_number', $i, PDO::PARAM_INT);
                $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                $stmt->bindParam(':youtube_url', $youtube_url, PDO::PARAM_STR);
                $stmt->bindParam(':notes', $note, PDO::PARAM_STR);
                $stmt->execute();
            }
        }

        $conn->commit();
        $message = '<div class="alert alert-success">Lessons updated successfully!</div>';
        header("Location: manage_content.php?course_id=$course_id");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
       if (str_contains($e->getMessage(), 'Lock wait timeout exceeded')) {
            $message = '<div class="alert alert-danger">Content is currently being viewed by students. Please try again shortly.</div>';
       } else {
        $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Content | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .lesson-form {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 4px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn-update {
            background: #6b48ff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-update:hover {
            background: #5a3de6;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            <div class="nav-links">
                <a href="manage_content.php" class="active">Manage Content</a>
            </div>
            <div class="user-menu">
                <div class="user-avatar"><?php echo htmlspecialchars(substr($_SESSION['first_name'], 0, 1) . substr($_SESSION['last_name'], 0, 1)); ?></div>
            </div>
        </div>
    </header>
    <div class="main-layout">
        <div class="content">
            <h1>Manage Course Content</h1>
            <form method="GET" class="form-group">
                <select name="course_id" onchange="this.form.submit()" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>" <?php echo $selected_course_id == $course['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ($selected_course_id): ?>
                <?php echo $message; ?>
                <form method="POST" class="form-group">
                    <input type="hidden" name="course_id" value="<?php echo $selected_course_id; ?>">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="lesson-form">
                            <h3>Lesson <?php echo $i; ?></h3>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($lessons[$i - 1]['title'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>YouTube URL (Optional)</label>
                                <input type="url" name="youtube_url[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($lessons[$i - 1]['youtube_url'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea name="notes[<?php echo $i; ?>]" required><?php echo htmlspecialchars($lessons[$i - 1]['notes'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    <?php endfor; ?>
                    <button type="submit" name="update_lessons" class="btn-update">Update Lessons</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</body>

</html>