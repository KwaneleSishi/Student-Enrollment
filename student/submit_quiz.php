<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    if (isset($_POST['user_id']) && isset($_POST['role']) && $_POST['role'] === 'student') {
        $student_id = (int)$_POST['user_id'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
} else {
    $student_id = $_SESSION['user_id'];
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ses_db", 'root', 'Kwanele@050509');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $answers = json_decode($_POST['answers'] ?? '{}', true);

    if (!$course_id || count($answers) !== 8) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $max_retries = 3;
    $retry_count = 0;

    while ($retry_count < $max_retries) {
        $pdo->beginTransaction();

        // Fetch enrollment with version
        $stmt = $pdo->prepare("SELECT EnrollmentID, total_grade, version FROM enrollments WHERE StudentID = ? AND CourseID = ?");
        $stmt->execute([$student_id, $course_id]);
        $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$enrollment) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Not enrolled']);
            exit;
        }

        $enrollment_id = $enrollment['EnrollmentID'];
        $current_version = $enrollment['version'];

        // Fetch correct answers
        $stmt = $pdo->prepare("SELECT question_number, correct_choice FROM quiz_questions WHERE course_id = ?");
        $stmt->execute([$course_id]);
        $correct_answers = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $correct_answers[$row['question_number']] = (int)$row['correct_choice'];
        }

        // Calculate score
        $score = 0;
        for ($i = 1; $i <= 8; $i++) {
            if (isset($answers[$i], $correct_answers[$i]) && $answers[$i] === $correct_answers[$i]) {
                $score += 2;
            }
        }

        // Record attempt
        $stmt = $pdo->prepare("INSERT INTO quiz_attempts (enrollment_id, attempt_number, score, completed_at) VALUES (?, 1, ?, NOW())");
        $stmt->execute([$enrollment_id, $score]);

        // Update total grade with version check
        $total_grade = $enrollment['total_grade'] + $score;
        $stmt = $pdo->prepare("UPDATE enrollments SET total_grade = ?, version = version + 1 WHERE EnrollmentID = ? AND version = ?");
        $stmt->execute([$total_grade, $enrollment_id, $current_version]);

        if ($stmt->rowCount() === 0) {
            $pdo->rollBack();
            $retry_count++;
            continue;
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'score' => $score, 'total_grade' => $total_grade]);
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Failed due to concurrent updates']);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>