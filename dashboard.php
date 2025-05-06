<?php
// student/dashboard.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | SESAcademy</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
        /* Enhanced Dashboard Styles */
    .welcome-message h1 {
        font-size: 2.2rem;
        margin-bottom: 0.5rem;
    }

    .welcome-message p {
        font-size: 1.1rem;
        color: var(--text-light);
    }

    .stat-card.highlight-card {
        grid-column: span 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, var(--primary-color), #6933ff);
        color: white;
    }

    .stat-card.highlight-card .stat-content h3 {
        color: white;
        margin-bottom: 1rem;
    }

    .stat-card.highlight-card .stat-icon {
        font-size: 3rem;
        opacity: 0.8;
    }

    .stat-card {
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-card .value {
        font-size: 2.2rem;
        margin: 1rem 0;
    }

    .loading-spinner {
        text-align: center;
        padding: 2rem;
        grid-column: 1 / -1;
    }

    .spinner {
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 4px solid var(--primary-color);
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .course-card .course-actions .btn {
        padding: 10px 20px;
        font-size: 0.9rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stat-card.highlight-card {
            grid-column: span 1;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .welcome-message h1 {
            font-size: 1.8rem;
        }
    }
    </style>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container container">
            <div class="logo">SES<span>Academy</span></div>
            
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search for courses">
            </div>
            
            <div class="nav-links">
                <a href="dashboard.html" class="active">My Learning</a>
                <a href="catalog.html">Course Catalog</a>
                <a href="grades.html">My Grades</a>
            </div>
            
            <div class="user-menu">
                <div class="user-avatar" id="user-avatar"></div>
            </div>
        </div>
    </header>
    
    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Sidebar -->
        <div class="sidebar-menu">
            <h3>Navigation</h3>
            <ul>
                <li>
                    <a href="dashboard.php" class="active">
                        <i class="fas fa-home"></i> 
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="catalog.php">
                        <i class="fas fa-compass"></i> 
                        <span>Course Catalog</span>
                    </a>
                </li>
                <li>
                    <a href="grades.php">
                        <i class="fas fa-chart-line"></i> 
                        <span>Academic Progress</span>
                    </a>
                </li>
            </ul>
            
            <h3>Account</h3>
            <ul>
                <li>
                    <a href="profile.php">
                        <i class="fas fa-user-cog"></i> 
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a href="/index.php" id="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> 
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="dashboard-header">
                <div class="welcome-message">
                    <h1>Welcome back, <span id="student-name">Student</span>! 👋</h1>
                    <p class="text-muted">Track your progress and continue learning</p>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card highlight-card">
                    <div class="stat-content">
                        <h3>📚 Enrolled Courses</h3>
                        <div class="value" id="enrolled-count">0</div>
                        <div class="trend up">
                            <i class="fas fa-arrow-up"></i> 2 new this term
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>

                <div class="stat-card highlight-card">
                    <div class="stat-content">
                        <h3>🎓 Completed Courses</h3>
                        <div class="value" id="completed-count">0</div>
                        <div class="trend up">
                            <i class="fas fa-arrow-up"></i> 1 since last term
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <h3>📊 Current GPA</h3>
                    <div class="value" id="gpa">0.0</div>
                    <div class="trend up">
                        <i class="fas fa-arrow-up"></i> 0.2 improvement
                    </div>
                </div>

                <div class="stat-card">
                    <h3>⏳ Credits Earned</h3>
                    <div class="value" id="credits">0</div>
                    <div class="trend up">
                        <i class="fas fa-arrow-up"></i> 7 this term
                    </div>
                </div>
            </div>

            <div class="section-header">
                <h2>My Active Courses</h2>
                <a href="catalog.html" class="btn btn-outline">
                    View All Courses <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="courses-grid" id="enrolled-courses">
                <!-- Loading spinner -->
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Loading your courses...</p>
                </div>
            </div>
        </div>
    
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check authentication
            if (!checkAuth('student')) {
                return;
            }
            
            // Get current user
            const user = getCurrentUser();
            
            // Update UI with student name
            document.getElementById('student-name').textContent = user.firstName;
            document.getElementById('user-avatar').textContent = user.firstName.charAt(0) + user.lastName.charAt(0);
            
            // Setup logout button
            document.getElementById('logout-btn').addEventListener('click', logout);
            
            // Load enrolled courses
            loadEnrolledCourses(user.id);
            
            // Update stats
            updateStats(user.id);
        });
        
        // Load enrolled courses
        function loadEnrolledCourses(studentId) {
            const studentEnrollments = getEnrollmentsForStudent(parseInt(studentId));
            const coursesContainer = document.getElementById('enrolled-courses');
            
            if (studentEnrollments.length === 0) {
                coursesContainer.innerHTML = '<p>You are not enrolled in any courses.</p>';
                return;
            }
            
            let html = '';
            
            studentEnrollments.forEach(enrollment => {
                const course = getCourseById(enrollment.courseId);
                const instructor = getInstructorById(course.instructorId);
                const department = getDepartmentById(course.departmentId);
                
                html += `
                    <div class="course-card">
                        <div class="course-image">
                            <img src="${course.image}" alt="${course.title}">
                            <div class="course-badge">${department.name}</div>
                        </div>
                        <div class="course-content">
                            <h3 class="course-title">${course.title}</h3>
                            <p class="course-instructor">${instructor.firstName} ${instructor.lastName}</p>
                            <div class="course-meta">
                                <div class="course-rating">
                                    <span class="stars">★★★★★</span>
                                    <span class="value">4.8</span>
                                    <span class="count">(2,345)</span>
                                </div>
                            </div>
                            <div class="course-actions">
                                <a href="course-details.html?id=${course.id}" class="btn btn-primary btn-block">Continue Learning</a>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            coursesContainer.innerHTML = html;
        }
        
        // Update stats
        function updateStats(studentId) {
            const studentEnrollments = getEnrollmentsForStudent(parseInt(studentId));
            
            // Update enrolled count
            document.getElementById('enrolled-count').textContent = studentEnrollments.length;
            
            // Update completed count
            const completedCount = studentEnrollments.filter(enrollment => enrollment.grade !== null).length;
            document.getElementById('completed-count').textContent = completedCount;
            
            // Calculate GPA
            let totalPoints = 0;
            let totalCourses = 0;
            
            studentEnrollments.forEach(enrollment => {
                if (enrollment.grade) {
                    let points = 0;
                    
                    switch(enrollment.grade) {
                        case 'A+': points = 4.0; break;
                        case 'A': points = 4.0; break;
                        case 'A-': points = 3.7; break;
                        case 'B+': points = 3.3; break;
                        case 'B': points = 3.0; break;
                        case 'B-': points = 2.7; break;
                        case 'C+': points = 2.3; break;
                        case 'C': points = 2.0; break;
                        case 'C-': points = 1.7; break;
                        case 'D+': points = 1.3; break;
                        case 'D': points = 1.0; break;
                        case 'D-': points = 0.7; break;
                        case 'F': points = 0.0; break;
                    }
                    
                    totalPoints += points;
                    totalCourses++;
                }
            });
            
            const gpa = totalCourses > 0 ? (totalPoints / totalCourses).toFixed(2) : '0.00';
            document.getElementById('gpa').textContent = gpa;
            
            // Calculate credits
            let totalCredits = 0;
            
            studentEnrollments.forEach(enrollment => {
                if (enrollment.grade) {
                    const course = getCourseById(enrollment.courseId);
                    totalCredits += course.credits;
                }
            });
            
            document.getElementById('credits').textContent = totalCredits;
        }
    </script>
</body>
</html>
