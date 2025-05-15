<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$course_id = (int)$_GET['id'];
$student_id = $_SESSION['user_id'];

// Fetch course details
$stmt = $conn->prepare("
    SELECT c.*, d.name AS department_name, 
           CONCAT(u.first_name, ' ', u.last_name) AS instructor_name,
           (SELECT COUNT(*) FROM enrollments e WHERE e.CourseID = c.id) AS current_enrollment
    FROM courses c
    JOIN departments d ON c.department_id = d.id
    JOIN users u ON c.instructor_id = u.id
    WHERE c.id = :course_id
");
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    echo json_encode(['error' => 'Course not found']);
    exit;
}

// Check if the student is enrolled
$stmt = $conn->prepare("SELECT EnrollmentID FROM enrollments WHERE StudentID = :student_id AND CourseID = :course_id");
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$is_enrolled = $stmt->fetch() ? true : false;

// Prepare response
$response = [
    'title' => $course['title'],
    'department_name' => $course['department_name'],
    'instructor_name' => $course['instructor_name'],
    'credits' => $course['credits'],
    'description' => $course['description'],
    'availability' => $course['capacity'] - $course['current_enrollment'],
    'capacity' => $course['capacity'],
    'is_enrolled' => $is_enrolled
];

echo json_encode($response);
?>