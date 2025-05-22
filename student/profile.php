<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name, last_name, email, role, department_id FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../login.php");
    exit();
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM enrollments WHERE StudentID = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$enrolled_count = $stmt->fetchColumn();

$stmt = $conn->prepare("
    SELECT COUNT(*) FROM enrollments e
    JOIN (SELECT course_id, COUNT(*) as total FROM course_content GROUP BY course_id) cc ON e.CourseID = cc.course_id
    WHERE e.StudentID = :user_id AND JSON_LENGTH(e.completed_lessons) >= cc.total
");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$completed_count = $stmt->fetchColumn();

$gpa = 'N/A'; // Placeholder, requires grade data

// Handle form submission for updating profile
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);

    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $message = '<div class="alert alert-danger">All fields are required!</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Invalid email format!</div>';
    } else {
        // Check if email is already in use by another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $message = '<div class="alert alert-danger">Email is already in use!</div>';
        } else {
            // Update the user's information
            $stmt = $conn->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email WHERE id = :user_id");
            $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Update session variables
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;

            $message = '<div class="alert alert-success">Profile updated successfully!</div>';
            // Refresh user data
            $stmt = $conn->prepare("SELECT first_name, last_name, email, role, department_id FROM users WHERE id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-container {
            display: flex;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .profile-sidebar {
            flex: 1;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-avatar-container {
            margin-bottom: 20px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-image: url('../assets/images/default-avatar.png');
            background-size: cover;
            margin: 0 auto;
            border: 4px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-stats {
            margin-top: 20px;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .stat-label {
            font-weight: 500;
            color: #333;
        }

        .stat-value {
            font-weight: 600;
            color: #555;
        }

        .profile-content {
            flex: 2;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-tabs {
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 20px;
            background: #f0f0f0;
            border: none;
            border-radius: 4px 4px 0 0;
            cursor: pointer;
            margin-right: 5px;
        }

        .tab-btn.active {
            background: #6b48ff;
            color: #fff;
        }

        .profile-form {
            display: grid;
            gap: 15px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin: 0;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background: #fff;
        }

        .form-control[readonly] {
            background: #f9f9f9;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #444;
        }

        .btn-update {
            background: #6b48ff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        .btn-update:hover {
            background: #5a3de6;
        }

        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            <div class="nav-links">
                <a href="dashboard.php">My Learning</a>
                <a href="catalog.php">Course Catalog</a>
                <a href="academic_progress.php">My Grades</a>
            </div>
            <div class="user-menu">
                <div class="user-avatar"><?php echo htmlspecialchars($user['first_name'][0] . $user['last_name'][0]); ?></div>
            </div>
        </div>
    </header>
    <div class="main-layout">
        <div class="sidebar">
            <div class="sidebar-menu">
                <h3>Menu</h3>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                    <li><a href="catalog.php"><i class="fas fa-book"></i> <span>Course Catalog</span></a></li>
                    <li><a href="academic_progress.php"><i class="fas fa-chart-bar"></i> <span>My academic progress</span></a></li>
                </ul>
                <h3>Account</h3>
                <ul>
                    <li><a href="profile.php" class="active"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>
        <div class="content">
            <div class="dashboard-header">
                <h1>My Profile</h1>
                <p>View and edit your personal information</p>
            </div>
            <div class="profile-container">
                <div class="profile-sidebar">
                    <div class="profile-avatar-container">
                        <div class="profile-avatar"></div>
                    </div>
                    <div class="profile-stats">
                        <div class="stat-item"><span class="stat-label">Enrolled Courses</span><span class="stat-value"><?php echo $enrolled_count; ?></span></div>
                        <div class="stat-item"><span class="stat-label">Completed Courses</span><span class="stat-value"><?php echo $completed_count; ?></span></div>
                        <div class="stat-item"><span class="stat-label">Current GPA</span><span class="stat-value"><?php echo $gpa; ?></span></div>
                    </div>
                </div>
                <div class="profile-content">
                    <?php echo $message; ?>
                    <div class="profile-tabs">
                        <button class="tab-btn active" data-tab="personal">Personal Information</button>
                    </div>
                    <div class="tab-content" id="personal-tab">
                        <form class="profile-form" method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['role']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Department</label>
                                <input type="text" class="form-control" value="<?php
                                                                                $dept = $user['department_id'] ? $conn->query("SELECT name FROM departments WHERE id = " . $user['department_id'])->fetchColumn() : 'N/A';
                                                                                echo htmlspecialchars($dept);
                                                                                ?>" readonly>
                            </div>
                            <button type="submit" name="update_profile" class="btn-update">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.close-modal').forEach(btn => btn.addEventListener('click', () => document.getElementById('success-modal').style.display = 'none'));
    </script>
</body>

</html>