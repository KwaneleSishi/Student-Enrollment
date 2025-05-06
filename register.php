<?php
// register.php
require_once 'config/db.php';

$error = '';
$preservedValues = [
    'firstName' => '',
    'lastName' => '',
    'email' => '',
    'major' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preserve input values
    $preservedValues = [
        'firstName' => htmlspecialchars($_POST['firstName'] ?? ''),
        'lastName' => htmlspecialchars($_POST['lastName'] ?? ''),
        'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
        'major' => $_POST['major'] ?? ''
    ];

    // Validate inputs
    $required = ['firstName', 'lastName', 'email', 'password', 'confirmPassword', 'major'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $error = "All fields are required";
            break;
        }
    }

    if (!$error && $_POST['password'] !== $_POST['confirmPassword']) {
        $error = "Passwords do not match";
    }

    if (!$error && strlen($_POST['password']) < 8) {
        $error = "Password must be at least 8 characters";
    }

    if (!$error) {
        $deptMap = [
            'Computer Science' => 1,
            'Mathematics' => 2,
            'Biology' => 3,
            'Business' => 4,
            'Psychology' => 5
        ];
        $deptId = $deptMap[$_POST['major']] ?? null;

        try {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$_POST['email']]);
            if ($stmt->fetch()) {
                $error = "Email already exists";
            } else {
                $stmt = $conn->prepare("INSERT INTO users 
                    (first_name, last_name, email, password, role, department_id)
                    VALUES (?, ?, ?, ?, 'student', ?)");
                $stmt->execute([
                    $preservedValues['firstName'],
                    $preservedValues['lastName'],
                    $preservedValues['email'],
                    password_hash($_POST['password'], PASSWORD_DEFAULT),
                    $deptId
                ]);
                
                header("Location: login.php?registration=success");
                exit();
            }
        } catch(PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Student Enrollment System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-page">
        <div class="login-container register-container">
            <div class="login-header">
                <div class="logo">SES<span>Academy</span></div>
                <p>Create your student account</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form class="login-form" id="register-form"  method="POST">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" class="form-control" value="<?= $preservedValues['firstName'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" class="form-control" value="<?= $preservedValues['lastName'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= $preservedValues['email'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <small class="form-text">Password must be at least 8 characters long</small>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="major">Field of Study</label>
                    <select id="major" name="major" class="form-control" required>
                        <option value="">Select your field of study</option>
                        <option value="Computer Science" <?= $preservedValues['major'] === 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
                        <option value="Mathematics" <?= $preservedValues['major'] === 'Mathematics' ? 'selected' : '' ?>>Mathematics</option>
                        <option value="Biology" <?= $preservedValues['major'] === 'Biology' ? 'selected' : '' ?>>Biology</option>
                        <option value="Business" <?= $preservedValues['major'] === 'Business' ? 'selected' : '' ?>>Business</option>
                        <option value="Psychology" <?= $preservedValues['major'] === 'Psychology' ? 'selected' : '' ?>>Psychology</option>
                        <option value="Other" <?= $preservedValues['major'] === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-container">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a></label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>
            
            <div class="login-footer">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('register-form');
            
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                if (password.length < 8) {
                    alert('Password must be at least 8 characters');
                    e.preventDefault();
                    return;
                }
                
                if (password !== confirmPassword) {
                    alert('Passwords do not match');
                    e.preventDefault();
                }
            });
        });
    </script>
    
</body>
</html>

