<?php
// login.php
session_start();
require_once 'config/db.php';

// Get selected role from POST or default to student
$selectedRole = strtolower($_POST['role'] ?? 'student');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = strtolower($_POST['role']); // Ensure lowercase

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];

            // Verify redirect path
            $redirect = "{$user['role']}/dashboard.php";
            if (!file_exists($redirect)) {
                die("Dashboard file missing: " . $redirect);
            }

            header("Location: $redirect");
            exit();
        } else {
            header("Location: login.php?error=invalid_credentials");
            exit();
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Student Enrollment System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-header">
                <div class="logo">SES<span>Academy</span></div>
                <p>Sign in to your account</p>
            </div>

            <div class="role-selector">
                <div class="role-btn <?= $selectedRole === 'student' ? 'active' : '' ?>" data-role="student">Student</div>
                <div class="role-btn <?= $selectedRole === 'instructor' ? 'active' : '' ?>" data-role="instructor">Instructor</div>
                <div class="role-btn <?= $selectedRole === 'admin' ? 'active' : '' ?>" data-role="admin">Admin</div>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    Invalid email or password
                </div>
            <?php endif; ?>

            <form class="login-form" id="login-form" method="POST">
                <input type="hidden" name="role" id="role-input" value="<?= $selectedRole ?>">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>

            <div class="login-footer">
                <p>Don't have an account? <a href="register.php">Register now</a></p>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>