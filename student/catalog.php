<?php
// student/catalog.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch departments
$dept_stmt = $conn->query("SELECT * FROM departments");
$departments = $dept_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get selected department from URL
$dept_filter = $_GET['dept'] ?? 'all';

// Fetch all courses
$sql = "
    SELECT c.*, d.name AS department_name, 
           CONCAT(u.first_name, ' ', u.last_name) AS instructor_name,
           (SELECT COUNT(*) FROM enrollments e WHERE e.CourseID = c.id) AS current_enrollment
    FROM courses c
    JOIN departments d ON c.department_id = d.id
    JOIN users u ON c.instructor_id = u.id";
if ($dept_filter !== 'all' && is_numeric($dept_filter)) {
    $sql .= " WHERE c.department_id = :dept_id";
}
$stmt = $conn->prepare($sql);
if ($dept_filter !== 'all' && is_numeric($dept_filter)) {
    $stmt->bindParam(':dept_id', $dept_filter, PDO::PARAM_INT);
}
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch enrolled course IDs for the student
$stmt = $conn->prepare("SELECT CourseID FROM enrollments WHERE StudentID = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$enrolled_course_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_course_id'])) {
    $course_id = $_POST['enroll_course_id'];
    $conn->beginTransaction(); // Start transaction

    try {
        // Lock the course row to prevent concurrent modifications
        $stmt = $conn->prepare("SELECT capacity, (SELECT COUNT(*) FROM enrollments e WHERE e.CourseID = c.id) AS current_enrollment FROM courses c WHERE id = :course_id FOR UPDATE");
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($course && $course['current_enrollment'] < $course['capacity']) {
            $stmt = $conn->prepare("INSERT INTO enrollments (StudentID, CourseID, EnrollmentDate) VALUES (:user_id, :course_id, NOW())");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
            $stmt->execute();

            // Optionally update current_enrollment (though this could be handled by a trigger)
            $stmt = $conn->prepare("UPDATE courses SET current_enrollment = current_enrollment + 1 WHERE id = :course_id");
            $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
            $stmt->execute();

            $conn->commit(); // Commit transaction
            header("Location: catalog.php?enrolled=success");
            exit();
        } else {
            $conn->rollBack(); // Roll back if capacity is exceeded
            header("Location: catalog.php?enrolled=failed");
            exit();
        }
    } catch (PDOException $e) {
        $conn->rollBack(); // Roll back on error (e.g., deadlock)
        error_log("Enrollment failed: " . $e->getMessage());
        header("Location: catalog.php?enrolled=failed");
        exit();
    }
}

// Check for enrollment feedback
$enrollment_message = '';
if (isset($_GET['enrolled'])) {
    if ($_GET['enrolled'] === 'success') {
        $enrollment_message = '<div class="alert alert-success">Successfully enrolled in the course!</div>';
    } elseif ($_GET['enrolled'] === 'failed') {
        $enrollment_message = '<div class="alert alert-danger">Enrollment failed. The course may be full or an error occurred.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Catalog | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .alert-success {
            background-color: rgba(30, 178, 166, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }

        .alert-danger {
            background-color: rgba(228, 30, 63, 0.1);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search for courses" id="search-input">
            </div>
            <div class="nav-links">
                <a href="dashboard.php">My Learning</a>
                <a href="catalog.php" class="active">Course Catalog</a>
                <a href="grades.php">My Grades</a>
            </div>
            <div class="user-menu">
                <div class="user-avatar"><?php echo htmlspecialchars($user['first_name'][0] . $user['last_name'][0]); ?></div>
            </div>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-menu">
                <h3>Menu</h3>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                    <li><a href="catalog.php" class="active"><i class="fas fa-book"></i> <span>Course Catalog</span></a></li>
                    <li><a href="grades.php"><i class="fas fa-chart-bar"></i> <span>My Grades</span></a></li>
                </ul>
                <h3>Departments</h3>
                <ul id="department-menu">
                    <li><a href="?dept=all" class="<?php echo $dept_filter === 'all' ? 'active' : ''; ?>"><i class="fas fa-th-large"></i> <span>All Departments</span></a></li>
                    <?php foreach ($departments as $dept): ?>
                        <li><a href="?dept=<?php echo $dept['id']; ?>" class="<?php echo $dept_filter == $dept['id'] ? 'active' : ''; ?>"><i class="fas fa-building"></i> <span><?php echo htmlspecialchars($dept['name']); ?></span></a></li>
                    <?php endforeach; ?>
                </ul>
                <h3>Account</h3>
                <ul>
                    <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                    <li><a href="/SES1/index.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="dashboard-header">
                <h1>Course Catalog</h1>
                <p>Browse and enroll in courses</p>
            </div>
            <?php echo $enrollment_message; ?>
            <div class="section-header">
                <h2>Available Courses</h2>
                <div class="filters">
                    <select id="credits-filter" class="form-control">
                        <option value="all">All Credits</option>
                        <option value="3">3 Credits</option>
                        <option value="4">4 Credits</option>
                    </select>
                </div>
            </div>
            <div class="courses-grid" id="courses-grid">
                <?php foreach ($courses as $course): ?>
                    <?php
                    $is_enrolled = in_array($course['id'], $enrolled_course_ids);
                    $availability = $course['capacity'] - $course['current_enrollment'];
                    // Adjust image URL for local files
                    $image_url = $course['image_url'];
                    if (strpos($image_url, 'http') !== 0 && strpos($image_url, '/SES1/') !== 0) {
                        $image_url = '/SES1/assets/uploads/' . basename($image_url);
                    }
                    ?>
                    <div class="course-card">
                        <div class="course-image">
                            <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" onerror="this.onerror=null; this.src='/SES1/assets/uploads/fallback.jpg';">
                            <div class="course-badge"><?php echo htmlspecialchars($course['department_name']); ?></div>
                        </div>
                        <div class="course-content">
                            <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                            <p class="course-instructor"><?php echo htmlspecialchars($course['instructor_name']); ?> • <?php echo $course['credits']; ?> Credits</p>
                            <div class="course-meta">
                                <div class="course-rating">
                                    <span class="stars">★★★★★</span>
                                    <span class="value">4.8</span>
                                    <span class="count">(2,345)</span>
                                </div>
                            </div>
                            <div class="course-actions">
                                <?php if ($is_enrolled): ?>
                                    <button class="btn btn-outline btn-block" disabled>Already Enrolled</button>
                                <?php elseif ($availability <= 0): ?>
                                    <button class="btn btn-outline btn-block" disabled>Course Full</button>
                                <?php else: ?>
                                    <button class="btn btn-primary btn-block view-course" data-course-id="<?php echo $course['id']; ?>">Enroll</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Course Details Modal -->
    <div class="modal-backdrop" id="course-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-course-title">Course Details</h3>
                <button class="modal-close">×</button>
            </div>
            <div class="modal-body" id="modal-course-details">
                <!-- Course details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline modal-close">Close</button>
                <button class="btn btn-primary" id="enroll-btn">Enroll Now</button>
                <input type="hidden" id="enroll-course-id">
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const coursesGrid = document.getElementById('courses-grid');
            const courseModal = document.getElementById('course-modal');
            const modalCourseTitle = document.getElementById('modal-course-title');
            const modalCourseDetails = document.getElementById('modal-course-details');
            const enrollBtn = document.getElementById('enroll-btn');
            const enrollCourseId = document.getElementById('enroll-course-id');

            // Setup modal close buttons
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => courseModal.classList.remove('show'));
            });

            // Setup view course buttons
            coursesGrid.querySelectorAll('.view-course').forEach(btn => {
                btn.addEventListener('click', () => {
                    const courseId = btn.getAttribute('data-course-id');
                    fetchCourseDetails(courseId);
                });
            });

            // Fetch course details
            function fetchCourseDetails(courseId) {
                fetch(`get_course_details.php?id=${courseId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok: ' + response.statusText);
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) throw new Error(data.error);
                        modalCourseTitle.textContent = data.title;
                        modalCourseDetails.innerHTML = `
                            <div class="course-detail-info">
                                <p><strong>Department:</strong> ${data.department_name}</p>
                                <p><strong>Instructor:</strong> ${data.instructor_name}</p>
                                <p><strong>Credits:</strong> ${data.credits}</p>
                                <p><strong>Availability:</strong> <span class="${data.availability <= 0 ? 'text-danger' : 'text-success'}">
                                    ${data.availability} / ${data.capacity} seats available
                                </span></p>
                                <p><strong>Description:</strong> ${data.description}</p>
                            </div>
                            <h4 class="mb-2 mt-3">Course Ratings</h4>
                            <div class="course-ratings">
                                <p><strong>Average Rating:</strong> 4.8 / 5 (2,345 ratings)</p>
                            </div>
                        `;
                        enrollCourseId.value = courseId;
                        enrollBtn.disabled = data.availability <= 0 || data.is_enrolled;
                        enrollBtn.textContent = data.is_enrolled ? 'Already Enrolled' : data.availability <= 0 ? 'Course Full' : 'Enroll Now';
                        courseModal.classList.add('show');
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        modalCourseDetails.innerHTML = '<p class="text-danger">Failed to load course details. Please try again.</p>';
                        courseModal.classList.add('show');
                    });
            }

            // Handle enrollment via AJAX
            enrollBtn.addEventListener('click', () => {
                if (enrollBtn.disabled) return;
                const courseId = enrollCourseId.value;

                fetch('catalog.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `enroll_course_id=${courseId}`
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Enrollment failed: ' + response.statusText);
                        return response.text();
                    })
                    .then(() => window.location.href = 'catalog.php?enrolled=success')
                    .catch(error => {
                        console.error('Enrollment error:', error);
                        window.location.href = 'catalog.php?enrolled=failed';
                    });
            });

            // Setup search
            document.getElementById('search-input').addEventListener('input', filterCourses);
            document.getElementById('credits-filter').addEventListener('change', filterCourses);

            function filterCourses() {
                const searchTerm = document.getElementById('search-input').value.toLowerCase();
                const creditsFilter = document.getElementById('credits-filter').value;
                const courseCards = coursesGrid.querySelectorAll('.course-card');

                courseCards.forEach(card => {
                    const title = card.querySelector('.course-title').textContent.toLowerCase();
                    const credits = card.querySelector('.course-instructor').textContent.match(/(\d+) Credits/)[1] || '0';
                    const matchesSearch = title.includes(searchTerm);
                    const matchesCredits = creditsFilter === 'all' || credits === creditsFilter;

                    card.style.display = matchesSearch && matchesCredits ? '' : 'none';
                });
            }
        });
    </script>
</body>

</html>