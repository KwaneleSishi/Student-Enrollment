<?php
// admin/view_table.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$table = isset($_GET['table']) ? $_GET['table'] : '';
if (!$table || !preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
    header("Location: tables.php");
    exit();
}

$data = $conn->query("SELECT * FROM `$table`")->fetchAll();
$columns = $data ? array_keys($data[0]) : [];

// Define key columns to display by default (customize based on table)
$key_columns = [];
if ($table === 'users') {
    $key_columns = ['id', 'first_name', 'last_name', 'email', 'role'];
} elseif ($table === 'courses') {
    $key_columns = ['id', 'title', 'instructor_id', 'department_id', 'credits', 'capacity'];
} elseif ($table === 'enrollments') {
    $key_columns = ['id', 'student_id', 'course_id', 'enrollment_date', 'progress'];
} else {
    // Default to first 5 columns if not specified
    $key_columns = array_slice($columns, 0, 5);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Table: <?php echo htmlspecialchars($table); ?> | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-container {
            max-width: 100%;
            overflow-x: auto;
            margin-bottom: 2rem;
        }

        .table {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .table th, .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table th {
            background-color: #f4f4f4;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table tbody tr:hover {
            background-color: #e6f3ff;
        }

        .table td.json-data, .table td.long-text {
            white-space: normal;
            word-break: break-word;
            max-width: 200px;
        }

        .table td.date {
            white-space: nowrap;
        }

        .hidden-column {
            display: none;
        }

        .toggle-columns {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .toggle-columns label {
            font-size: 0.9rem;
            cursor: pointer;
        }

        .toggle-columns input[type="checkbox"] {
            margin-right: 0.5rem;
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
                    <li><a href="courses.php"><i class="fas fa-book"></i> Manage Courses</a></li>
                    <li><a href="students.php"><i class="fas fa-users"></i> Manage Students</a></li>
                    <li><a href="register.php"><i class="fas fa-users"></i> Add Instructors</a></li>
                    <li><a href="tables.php" class="active"><i class="fas fa-database"></i> View Tables</a></li>
                    <li><a href="backup.php"><i class="fas fa-database"></i> Backup Database</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="dashboard-header">
                <h1>Viewing Table: <?php echo htmlspecialchars($table); ?></h1>
            </div>

            <!-- Column Toggle -->
            <div class="toggle-columns">
                <span>Show/Hide Columns:</span>
                <?php foreach ($columns as $col): ?>
                    <label>
                        <input type="checkbox" class="column-toggle" data-column="<?php echo htmlspecialchars($col); ?>"
                            <?php echo in_array($col, $key_columns) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($col); ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <?php foreach ($columns as $col): ?>
                                <th class="<?php echo !in_array($col, $key_columns) ? 'hidden-column' : ''; ?>"
                                    data-column="<?php echo htmlspecialchars($col); ?>">
                                    <?php echo htmlspecialchars($col); ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr><td colspan="<?php echo count($columns); ?>">No data available.</td></tr>
                        <?php else: ?>
                            <?php foreach ($data as $row): ?>
                                <tr>
                                    <?php foreach ($row as $col => $value): ?>
                                        <?php
                                        // Determine cell class based on data type
                                        $cell_class = '';
                                        if (in_array($col, ['completed_lessons', 'course_content'])) {
                                            $cell_class = 'json-data';
                                            $value = json_encode(json_decode($value, true), JSON_PRETTY_PRINT);
                                        } elseif (in_array($col, ['description'])) {
                                            $cell_class = 'long-text';
                                        } elseif (stripos($col, 'date') !== false) {
                                            $cell_class = 'date';
                                            $value = $value ? date('Y-m-d H:i', strtotime($value)) : $value;
                                        }
                                        ?>
                                        <td class="<?php echo $cell_class; ?> <?php echo !in_array($col, $key_columns) ? 'hidden-column' : ''; ?>"
                                            data-column="<?php echo htmlspecialchars($col); ?>">
                                            <?php echo htmlspecialchars($value ?? 'NULL'); ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Column toggle functionality
        document.querySelectorAll('.column-toggle').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const column = this.dataset.column;
                const elements = document.querySelectorAll(`[data-column="${column}"]`);
                elements.forEach(el => {
                    if (this.checked) {
                        el.classList.remove('hidden-column');
                    } else {
                        el.classList.add('hidden-column');
                    }
                });
            });
        });
    </script>
</body>
</html>