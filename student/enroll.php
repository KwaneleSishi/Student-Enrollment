<?php
session_start();
require_once '../db.php'; // Database connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized access']));
}

$data = json_decode(file_get_contents('php://input'), true);
$courseId = $data['courseId'] ?? null;
$studentId = $_SESSION['user_id'];

if (!$courseId) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid course ID']));
}

try {
    $conn->beginTransaction();

    // Lock course row for update
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ? FOR UPDATE");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course || ($course['current_enrollment'] >= $course['capacity'])) {
        throw new Exception('Course full or not found');
    }

    // Check existing enrollment
    $stmt = $conn->prepare("SELECT * FROM enrollments WHERE StudentID = ? AND CourseID = ?");
    $stmt->execute([$studentId, $courseId]);
    if ($stmt->fetch()) {
        throw new Exception('Already enrolled in this course');
    }

    // Create enrollment
    $stmt = $conn->prepare("INSERT INTO enrollments (StudentID, CourseID) VALUES (?, ?)");
    $stmt->execute([$studentId, $courseId]);

    // Update enrollment count
    $stmt = $conn->prepare("UPDATE courses SET current_enrollment = current_enrollment + 1 WHERE id = ?");
    $stmt->execute([$courseId]);

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}