<?php
// admin/courses.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle course creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $credits = $_POST['credits'];
    $instructor_id = $_POST['instructor'];
    $department_id = $_POST['department'];
    $capacity = $_POST['capacity'];
    
    // Handle image upload
    $image_url = '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "assets/uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_url = $target_file;
    } elseif (!empty($_POST['image_url'])) {
        $image_url = $_POST['image_url'];
    }

    $stmt = $conn->prepare("INSERT INTO courses 
        (title, description, credits, image_url, instructor_id, department_id, capacity)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $credits, $image_url, $instructor_id, $department_id, $capacity]);
}

// Get existing courses
$courses = $conn->query("SELECT c.*, d.name AS department, 
    CONCAT(u.first_name, ' ', u.last_name) AS instructor 
    FROM courses c
    JOIN departments d ON c.department_id = d.id
    JOIN users u ON c.instructor_id = u.id")->fetchAll();

// Get instructors and departments for dropdowns
$instructors = $conn->query("SELECT * FROM users WHERE role = 'instructor'")->fetchAll();
$departments = $conn->query("SELECT * FROM departments")->fetchAll();
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
    .course-thumbnail {
    width: 60px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
}

.table-container {
    background-color: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
}

.table th {
    background-color: var(--accent-color);
    padding: 1rem;
}

.table td {
    vertical-align: middle;
    padding: 1rem;
}

.text-muted {
    color: var(--text-light);
}

.small {
    font-size: 0.875rem;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.file-upload {
    border: 1px dashed var(--border-color);
    padding: 1rem;
    border-radius: 4px;
}

.file-upload input[type="file"] {
    margin-bottom: 0.5rem;
}
</style>
<div class="content">
    <div class="dashboard-header">
        <h1>Manage Courses</h1>
        <a href="add_course.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Course
        </a>
    </div>

    <!-- Courses Table -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Instructor</th>
                    <th>Credits</th>
                    <th>Capacity</th>
                    <th>Enrolled</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                <tr>
                    <td>
                        <?php if ($course['image_url']): ?>
                        <img src="<?= htmlspecialchars($course['image_url']) ?>" 
                            alt="Course image" class="course-thumbnail">
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="course-title"><?= htmlspecialchars($course['title']) ?></div>
                        <div class="text-muted small"><?= substr(htmlspecialchars($course['description']), 0, 50) ?>...</div>
                    </td>
                    <td><?= htmlspecialchars($course['department']) ?></td>
                    <td><?= htmlspecialchars($course['instructor']) ?></td>
                    <td><?= $course['credits'] ?></td>
                    <td><?= $course['capacity'] ?></td>
                    <td><?= $course['current_enrollment'] ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    
</div>