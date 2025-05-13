<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    // Check if this is an internal cURL call with user_id and role
    if (isset($_GET['user_id']) && isset($_GET['role']) && $_GET['role'] === 'student') {
        $student_id = (int)$_GET['user_id'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
} else {
    $student_id = $_SESSION['user_id'];
}

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'Kwanele@050509';
$db_name = 'ses_db';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

    // Check enrollment and lesson completion
    $stmt = $pdo->prepare("SELECT completed_lessons, total_grade FROM enrollments WHERE StudentID = ? AND CourseID = ?");
    $stmt->execute([$student_id, $course_id]);
    $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$enrollment) {
        echo json_encode(['success' => false, 'message' => 'Not enrolled in this course']);
        exit;
    }

    $completed_lessons = json_decode($enrollment['completed_lessons'] ?? '[]', true);
    if (count($completed_lessons) !== 4 || !in_array(1, $completed_lessons) || !in_array(2, $completed_lessons) || !in_array(3, $completed_lessons) || !in_array(4, $completed_lessons)) {
        echo json_encode(['success' => false, 'message' => 'Complete all lessons to access the quiz']);
        exit;
    }

    // Update grade for lesson completion (if not already done)
    if ($enrollment['total_grade'] < 12) {
        $pdo->prepare("UPDATE enrollments SET total_grade = 12 WHERE StudentID = ? AND CourseID = ?")->execute([$student_id, $course_id]);
    }

    // Fetch quiz questions
    $stmt = $pdo->prepare("SELECT question_number, question_text, choice_1, choice_2, choice_3, choice_4 FROM quiz_questions WHERE course_id = ? ORDER BY question_number");
    $stmt->execute([$course_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($questions) !== 8) {
        echo json_encode(['success' => false, 'message' => 'Quiz not fully set up by instructor']);
        exit;
    }

    echo json_encode(['success' => true, 'questions' => $questions]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>