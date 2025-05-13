<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    // Check if this is an internal cURL call with user_id and role
    if (isset($_POST['user_id']) && isset($_POST['role']) && $_POST['role'] === 'student') {
        $student_id = (int)$_POST['user_id'];
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

    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $answers = json_decode($_POST['answers'] ?? '{}', true);

    if (!$course_id || count($answers) !== 8) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    // Fetch correct answers
    $stmt = $pdo->prepare("SELECT question_number, correct_choice FROM quiz_questions WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $correct_answers = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $correct_answers[$row['question_number']] = (int)$row['correct_choice'];
    }

    // Calculate score (2 points per correct answer, total 16 points)
    $score = 0;
    for ($i = 1; $i <= 8; $i++) {
        if (isset($answers[$i], $correct_answers[$i]) && $answers[$i] === $correct_answers[$i]) {
            $score += 2;
        }
    }

    // Get enrollment
    $stmt = $pdo->prepare("SELECT EnrollmentID, total_grade FROM enrollments WHERE StudentID = ? AND CourseID = ?");
    $stmt->execute([$student_id, $course_id]);
    $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$enrollment) {
        echo json_encode(['success' => false, 'message' => 'Not enrolled']);
        exit;
    }

    $enrollment_id = $enrollment['EnrollmentID'];
    $total_grade = $enrollment['total_grade'] + $score; // Add quiz score to lesson grade

    // Record the attempt
    $stmt = $pdo->prepare("INSERT INTO quiz_attempts (enrollment_id, attempt_number, score, completed_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$enrollment_id, 1, $score]); // Simplified: always attempt 1 for now

    // Update total grade in enrollments
    $stmt = $pdo->prepare("UPDATE enrollments SET total_grade = ? WHERE EnrollmentID = ?");
    $stmt->execute([$total_grade, $enrollment_id]);

    echo json_encode(['success' => true, 'score' => $score, 'total_grade' => $total_grade]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>