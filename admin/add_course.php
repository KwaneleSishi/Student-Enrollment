<?php
// admin/add_course.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

// Get instructors and departments for dropdowns
$instructors = $conn->query("SELECT * FROM users WHERE role = 'instructor'")->fetchAll();
$departments = $conn->query("SELECT * FROM departments")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $credits = $_POST['credits'];
        $instructor_id = $_POST['instructor'];
        $department_id = $_POST['department'];
        $capacity = $_POST['capacity'];
        
        // Handle image upload
        $image_url = '';
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "../assets/uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = str_replace('../', '', $target_file);
            }
        } elseif (!empty($_POST['image_url'])) {
            $image_url = $_POST['image_url'];
        }

        $stmt = $conn->prepare("INSERT INTO courses 
            (title, description, credits, image_url, instructor_id, department_id, capacity)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $credits, $image_url, $instructor_id, $department_id, $capacity]);
        
        $success = "Course created successfully!";
        // Clear form fields
        $_POST = array();
    } catch (PDOException $e) {
        $error = "Error creating course: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<style>
    /* Add Course Page Styles */
.course-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: var(--card-shadow);
    margin-top: 2rem;
}

.alert-success {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid var(--success-color);
    border-radius: 4px;
    color: var(--success-color);
    background-color: rgba(30, 178, 166, 0.1);
}

.btn-lg {
    padding: 15px 30px;
    font-size: 1.1rem;
}

.course-form .form-grid {
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.course-form .form-group {
    margin-bottom: 0;
}
</style>
<body>
    <div class="content">
        <div class="dashboard-header">
            <h1>Add New Course</h1>
            <a href="courses.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Courses
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="course-form">
            <div class="form-grid">
                <!-- Keep all the form fields from the modal -->
                <div class="form-group">
                    <label>Course Title *</label>
                    <input type="text" name="title" class="form-control" 
                           value="<?= $_POST['title'] ?? '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" class="form-control" rows="3" required>
                        <?= $_POST['description'] ?? '' ?>
                    </textarea>
                </div>

                    <div class="form-group">
                        <label>Credits *</label>
                        <input type="number" name="credits" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Capacity *</label>
                        <input type="number" name="capacity" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Department *</label>
                        <select name="department" class="form-control" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Instructor *</label>
                        <select name="instructor" class="form-control" required>
                            <option value="">Select Instructor</option>
                            <?php foreach ($instructors as $inst): ?>
                            <option value="<?= $inst['id'] ?>">
                                <?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Course Image</label>
                        <div class="file-upload">
                            <input type="file" name="image" class="form-control" 
                                accept="image/png, image/jpeg">
                            <div class="form-text">Or provide image URL:</div>
                            <input type="url" name="image_url" class="form-control mt-1" 
                                placeholder="https://example.com/image.jpg">
                        </div>
                    </div>
                <!-- Add any additional fields you need here -->
                <!-- ... rest of the form fields ... -->
                
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Create Course
                </button>
            </div>
        </form>
    </div>
</body>
</html>