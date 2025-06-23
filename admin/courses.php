<?php
// admin/courses.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle course creation
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $credits = (int)$_POST['credits'];
    $instructor_id = (int)$_POST['instructor'];
    $department_id = (int)$_POST['department'];
    $capacity = (int)$_POST['capacity'];

    // Handle image upload
    $image_url = '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = realpath(__DIR__ . '/../assets/uploads/') . '/';
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = 'assets/uploads/' . basename($_FILES["image"]["name"]);
        } else {
            $message = '<div class="alert-danger">Failed to upload image!</div>';
        }
    } elseif (!empty($_POST['image_url'])) {
        $image_url = trim($_POST['image_url']);
    }

    if (empty($message) && !empty($title) && !empty($description) && $credits > 0 && $instructor_id > 0 && $department_id > 0 && $capacity > 0) {
        $stmt = $conn->prepare("INSERT INTO courses 
            (title, description, credits, image_url, instructor_id, department_id, capacity)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $credits, $image_url, $instructor_id, $department_id, $capacity]);
        $message = '<div class="alert-success">Course added successfully!</div>';
    } else {
        $message = '<div class="alert-danger">Please fill in all required fields!</div>';
    }
}

// Handle course update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_course'])) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $credits = (int)$_POST['credits'];
    $instructor_id = (int)$_POST['instructor'];
    $department_id = (int)$_POST['department'];
    $capacity = (int)$_POST['capacity'];

    if (!empty($title) && !empty($description) && $credits > 0 && $instructor_id > 0 && $department_id > 0 && $capacity > 0) {
        $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ?, credits = ?, instructor_id = ?, department_id = ?, capacity = ? WHERE id = ?");
        $stmt->execute([$title, $description, $credits, $instructor_id, $department_id, $capacity, $id]);
        $message = '<div class="alert-success">Course updated successfully!</div>';
    } else {
        $message = '<div class="alert-danger">Please fill in all required fields!</div>';
    }
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .course-thumbnail {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .modal-backdrop {
            display: none;
        }

        .modal-backdrop.show {
            display: flex;
        }

        .disabled-field {
            background-color: #f7f9fa;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            <div class="user-menu">
                <div class="user-avatar"><?php echo htmlspecialchars(substr($_SESSION['first_name'], 0, 1) . substr($_SESSION['last_name'], 0, 1)); ?></div>
            </div>
        </div>
    </header>

    <div class="main-layout">
        <!-- Admin Sidebar -->
        <div class="sidebar">
            <div class="sidebar-menu">
                <h3>Admin Panel</h3>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="courses.php" class="active"><i class="fas fa-book"></i> Manage Courses</a></li>
                    <li><a href="students.php"><i class="fas fa-users"></i> Manage Students</a></li>
                    <li><a href="register.php"><i class="fas fa-users"></i> Add Instructors</a></li>
                    <li><a href="tables.php"><i class="fas fa-database"></i> View Tables</a></li>
                    <li><a href="backup.php"><i class="fas fa-database"></i> Backup Database</a></li>
                    <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="dashboard-header">
                <h1>Manage Courses</h1>
                <button class="btn btn-primary" onclick="document.getElementById('add-course-modal').classList.add('show')">
                    <i class="fas fa-plus"></i> Add New Course
                </button>
            </div>

            <?php echo $message; ?>

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
                                        <img src="<?php echo htmlspecialchars('/Student-Enrollment/' . $course['image_url']); ?>"
                                            alt="Course image" class="course-thumbnail" onerror="this.onerror=null; this.src='/Student-Enrollment/assets/uploads/fallback.jpg';">
                                    <?php else: ?>
                                        <img src="/Student-Enrollment/assets/uploads/fallback.jpg" alt="Course image" class="course-thumbnail">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="course-title"><?php echo htmlspecialchars($course['title']); ?></div>
                                    <div class="text-light"><?php echo substr(htmlspecialchars($course['description']), 0, 50); ?>...</div>
                                </td>
                                <td><?php echo htmlspecialchars($course['department']); ?></td>
                                <td><?php echo htmlspecialchars($course['instructor']); ?></td>
                                <td><?php echo $course['credits']; ?></td>
                                <td><?php echo $course['capacity']; ?></td>
                                <td><?php echo $course['current_enrollment']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline" onclick="document.getElementById('edit-course-modal-<?php echo $course['id']; ?>').classList.add('show')">Edit</button>
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Add Course Modal -->
            <div id="add-course-modal" class="modal-backdrop">
                <div class="modal">
                    <div class="modal-header">
                        <h3>Add New Course</h3>
                        <button class="modal-close" onclick="document.getElementById('add-course-modal').classList.remove('show')">×</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Credits</label>
                                    <input type="number" name="credits" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Instructor</label>
                                    <select name="instructor" class="form-control" required>
                                        <option value="">Select Instructor</option>
                                        <?php foreach ($instructors as $instructor): ?>
                                            <option value="<?php echo $instructor['id']; ?>">
                                                <?php echo htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Department</label>
                                    <select name="department" class="form-control" required>
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo $dept['id']; ?>">
                                                <?php echo htmlspecialchars($dept['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Capacity</label>
                                    <input type="number" name="capacity" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Course Image</label>
                                <div class="form-control" style="padding: 1rem; border: 1px dashed var(--border-color);">
                                    <input type="file" name="image" accept="image/*">
                                    <p class="form-text">Or enter image URL:</p>
                                    <input type="url" name="image_url" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Course Modal -->
            <?php foreach ($courses as $course): ?>
                <div id="edit-course-modal-<?php echo $course['id']; ?>" class="modal-backdrop">
                    <div class="modal">
                        <div class="modal-header">
                            <h3>Edit Course</h3>
                            <button class="modal-close" onclick="document.getElementById('edit-course-modal-<?php echo $course['id']; ?>').classList.remove('show')">×</button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Credits</label>
                                        <input type="number" name="credits" class="form-control" value="<?php echo $course['credits']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Instructor</label>
                                        <select name="instructor" class="form-control" required>
                                            <option value="">Select Instructor</option>
                                            <?php foreach ($instructors as $instructor): ?>
                                                <option value="<?php echo $instructor['id']; ?>" <?php echo $instructor['id'] == $course['instructor_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select name="department" class="form-control" required>
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?php echo $dept['id']; ?>" <?php echo $dept['id'] == $course['department_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($dept['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Capacity</label>
                                        <input type="number" name="capacity" class="form-control" value="<?php echo $course['capacity']; ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Course Image</label>
                                    <div class="form-control disabled-field" style="padding: 1rem; border: 1px dashed var(--border-color);">
                                        <p class="form-text">Image remains unchanged: <?php echo htmlspecialchars($course['image_url'] ?: 'No image'); ?></p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="edit_course" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        window.onclick = function(event) {
            if (event.target.className === 'modal-backdrop') {
                event.target.classList.remove('show');
            }
        }
    </script>
</body>

</html>