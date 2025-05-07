<?php
// get_course_details.php
require_once '../config/db.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid course ID']);
    exit();
}

$course_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

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
    http_response_code(404);
    echo json_encode(['error' => 'Course not found']);
    exit();
}

// Check if student is enrolled
$stmt = $conn->prepare("SELECT 1 FROM enrollments WHERE StudentID = :user_id AND CourseID = :course_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$is_enrolled = $stmt->fetchColumn();

$availability = $course['capacity'] - $course['current_enrollment'];

header('Content-Type: application/json');
echo json_encode([
    'id' => $course['id'],
    'title' => $course['title'],
    'description' => $course['description'],
    'credits' => $course['credits'],
    'capacity' => $course['capacity'],
    'availability' => $availability,
    'department_name' => $course['department_name'],
    'instructor_name' => $course['instructor_name'],
    'is_enrolled' => !!$is_enrolled
]);
?>