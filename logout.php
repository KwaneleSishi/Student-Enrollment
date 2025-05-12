<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
        // Destroy session and redirect to index.php
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    } else {
        // Show confirmation dialog
        $role = $_SESSION['role'] ?? 'Unknown'; // Fallback if role is not set
        $message = "Are you sure you want to log out ($role)?";
        echo "<script>
            if (confirm('$message')) {
                window.location.href = 'logout.php?confirm=yes';
            } else {
                window.history.back();
            }
        </script>";
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>