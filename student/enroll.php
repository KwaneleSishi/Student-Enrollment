<?php
session_start();
require_once '../config/db.php';

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
    $stmt = $conn->prepare("SELECT capacity, current_enrollment, version FROM courses WHERE id = :course_id FOR UPDATE");
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course || $course['current_enrollment'] >= $course['capacity']) {
        $conn->rollBack();
        throw new Exception('Course full or not found');
    }

    // Check existing enrollment
    $stmt = $conn->prepare("SELECT EnrollmentID FROM enrollments WHERE StudentID = :student_id AND CourseID = :course_id");
    $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->fetch()) {
        $conn->rollBack();
        throw new Exception('Already enrolled in this course');
    }

    // Create enrollment
    $stmt = $conn->prepare("INSERT INTO enrollments (StudentID, CourseID, version) VALUES (:student_id, :course_id, 0)");
    $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    $stmt->execute();

    // Update course enrollment with version check
    $stmt = $conn->prepare("UPDATE courses SET current_enrollment = current_enrollment + 1, version = version + 1 WHERE id = :course_id AND version = :current_version");
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    $stmt->bindParam(':current_version', $course['version'], PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        $conn->rollBack();
        throw new Exception('Concurrent update detected, please try again');
    }

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(400);
    error_log("Enrollment failed: " . $e->getMessage()); // Log for debugging
    echo json_encode(['error' => $e->getMessage()]);
}
?>