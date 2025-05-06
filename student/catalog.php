<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Catalog | SESAcademy</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <div class="sidebar">
            <div class="sidebar-menu">
                <h3>Menu</h3>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                    <li><a href="catalog.php" class="active"><i class="fas fa-book"></i> <span>Course Catalog</span></a></li>
                    <li><a href="grades.html"><i class="fas fa-chart-bar"></i> <span>My Grades</span></a></li>
                </ul>
                
                <h3>Departments</h3>
                <ul id="department-menu">
                    <li><a href="#" data-department="all" class="active"><i class="fas fa-th-large"></i> <span>All Departments</span></a></li>
                    <!-- Departments will be loaded here -->
                </ul>
                
                <h3>Account</h3>
                <ul>
                    <li><a href="profile.html"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                    <li><a href="#" id="logout-btn"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="dashboard-header">
                <h1>Course Catalog</h1>
                <p>Browse and enroll in courses</p>
            </div>
            
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
                <!-- Courses will be loaded here -->
                <div class="loading">Loading courses...</div>
            </div>
        </div>
    </div>
    
    <!-- Course Details Modal -->
    <div class="modal-backdrop" id="course-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-course-title">Course Details</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body" id="modal-course-details">
                <!-- Course details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline modal-close">Close</button>
                <button class="btn btn-primary" id="enroll-btn">Enroll Now</button>
            </div>
        </div>
    </div>
    
    <!-- Enrollment Confirmation Modal -->
    <div class="modal-backdrop" id="confirm-modal">
        <div class="modal">
            <div class="modal-header">
                <h3>Confirm Enrollment</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p id="confirm-message">Are you sure you want to enroll in this course?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline modal-close">Cancel</button>
                <button class="btn btn-primary" id="confirm-enroll-btn">Yes, Enroll</button>
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
            document.getElementById('user-avatar').textContent = user.firstName.charAt(0) + user.lastName.charAt(0);
            
            // Setup logout button
            document.getElementById('logout-btn').addEventListener('click', logout);
            
            // Load departments
            loadDepartments();
            
            // Load courses
            loadCourses();
            
            // Setup search
            document.getElementById('search-input').addEventListener('input', function() {
                filterCourses();
            });
            
            // Setup credits filter
            document.getElementById('credits-filter').addEventListener('change', function() {
                filterCourses();
            });
            
            // Setup modal close buttons
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', function() {
                    closeModals();
                });
            });
        });
        
        // Global variables
        let currentCourses = [];
        let selectedCourse = null;
        let studentEnrollments = [];
        
        // Load departments
        function loadDepartments() {
            const departmentMenu = document.getElementById('department-menu');
            const departmentLinks = departments.map(dept => {
                return `<li><a href="#" data-department="${dept.id}"><i class="fas fa-building"></i> <span>${dept.name}</span></a></li>`;
            }).join('');
            
            departmentMenu.innerHTML += departmentLinks;
            
            // Add event listeners to department links
            departmentMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all links
                    departmentMenu.querySelectorAll('a').forEach(l => l.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    // Filter courses by department
                    const departmentId = this.getAttribute('data-department');
                    filterCourses(departmentId);
                });
            });
        }
        
        // Load courses
        function loadCourses() {
            // Get student enrollments
            const user = getCurrentUser();
            studentEnrollments = getEnrollmentsForStudent(parseInt(user.id));
            
            // Get all courses
            currentCourses = [...courses];
            
            displayCourses(currentCourses);
        }
        
        // Display courses
        function displayCourses(coursesToDisplay) {
            const coursesGrid = document.getElementById('courses-grid');
            
            if (coursesToDisplay.length === 0) {
                coursesGrid.innerHTML = '<p>No courses found matching your criteria.</p>';
                return;
            }
            
            let html = '';
            
            coursesToDisplay.forEach(course => {
                // Check if student is enrolled in this course
                const isEnrolled = studentEnrollments.some(enrollment => enrollment.courseId === course.id);
                
                // Get department and instructor
                const department = getDepartmentById(course.departmentId);
                const instructor = getInstructorById(course.instructorId);
                
                // Check availability
                const availability = course.capacity - course.currentEnrollment;
                
                html += `
                    <div class="course-card">
                        <div class="course-image">
                            <img src="${course.image}" alt="${course.title}">
                            <div class="course-badge">${department.name}</div>
                        </div>
                        <div class="course-content">
                            <h3 class="course-title">${course.title}</h3>
                            <p class="course-instructor">${instructor.firstName} ${instructor.lastName} • ${course.credits} Credits</p>
                            <div class="course-meta">
                                <div class="course-rating">
                                    <span class="stars">★★★★★</span>
                                    <span class="value">4.8</span>
                                    <span class="count">(2,345)</span>
                                </div>
                            </div>
                            <div class="course-actions">
                                ${isEnrolled 
                                    ? '<button class="btn btn-outline btn-block" disabled>Already Enrolled</button>' 
                                    : availability <= 0
                                    ? '<button class="btn btn-outline btn-block" disabled>Course Full</button>'
                                    : `<button class="btn btn-primary btn-block view-course" data-course-id="${course.id}">View Course</button>`}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            coursesGrid.innerHTML = html;
            
            // Add event listeners to view course buttons
            coursesGrid.querySelectorAll('.view-course').forEach(btn => {
                btn.addEventListener('click', function() {
                    const courseId = parseInt(this.getAttribute('data-course-id'));
                    showCourseDetails(courseId);
                });
            });
        }
        
        // Filter courses
        function filterCourses(departmentId = 'all') {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const creditsFilter = document.getElementById('credits-filter').value;
            
            let filtered = [...currentCourses];
            
            // Filter by department
            if (departmentId !== 'all') {
                filtered = filtered.filter(course => course.departmentId === parseInt(departmentId));
            }
            
            // Filter by search term
            if (searchTerm) {
                filtered = filtered.filter(course => 
                    course.title.toLowerCase().includes(searchTerm) || 
                    course.description.toLowerCase().includes(searchTerm)
                );
            }
            
            // Filter by credits
            if (creditsFilter !== 'all') {
                filtered = filtered.filter(course => course.credits === parseInt(creditsFilter));
            }
            
            displayCourses(filtered);
        }
        
        // Show course details
        function showCourseDetails(courseId) {
            // Get course details
            const course = getCourseDetails(courseId);
            
            // Store selected course
            selectedCourse = course;
            
            // Update modal title
            document.getElementById('modal-course-title').textContent = course.title;
            
            // Calculate availability
            const availability = course.capacity - course.currentEnrollment;
            
            // Update modal content
            document.getElementById('modal-course-details').innerHTML = `
                <div class="course-detail-info">
                    <p><strong>Department:</strong> ${course.department.name}</p>
                    <p><strong>Instructor:</strong> ${course.instructor.firstName} ${course.instructor.lastName}</p>
                    <p><strong>Credits:</strong> ${course.credits}</p>
                    <p><strong>Availability:</strong> <span class="${availability <= 0 ? 'text-danger' : 'text-success'}">
                        ${availability} / ${course.capacity} seats available
                    </span></p>
                    <p><strong>Description:</strong> ${course.description}</p>
                </div>
                <h4 class="mb-2 mt-3">Course Ratings</h4>
                <div class="course-ratings">
                    <p><strong>Average Rating:</strong> ${course.averageRating} / 5 (${course.ratingCount} ratings)</p>
                </div>
            `;
            
            // Enable/disable enroll button based on availability and enrollment status
            const enrollBtn = document.getElementById('enroll-btn');
            const isEnrolled = studentEnrollments.some(enrollment => enrollment.courseId === course.id);
            
            if (isEnrolled) {
                enrollBtn.disabled = true;
                enrollBtn.textContent = 'Already Enrolled';
            } else if (availability <= 0) {
                enrollBtn.disabled = true;
                enrollBtn.textContent = 'Course Full';
            } else {
                enrollBtn.disabled = false;
                enrollBtn.textContent = 'Enroll Now';
            }
            
            // Add event listener to enroll button
            enrollBtn.onclick = showEnrollConfirmation;
            
            // Show modal
            document.getElementById('course-modal').classList.add('show');
        }
        
        // Show enrollment confirmation
        function showEnrollConfirmation() {
            if (!selectedCourse) {
                alert('Please select a course first');
                return;
            }
            
            // Update confirmation message
            document.getElementById('confirm-message').innerHTML = `
                Are you sure you want to enroll in <strong>${selectedCourse.title}</strong>?
            `;
            
            // Add event listener to confirm button
            document.getElementById('confirm-enroll-btn').onclick = enrollInCourse;
            
            // Hide course modal
            document.getElementById('course-modal').classList.remove('show');
            
            // Show confirmation modal
            document.getElementById('confirm-modal').classList.add('show');
        }
        
        // Enroll in course
        function enrollInCourse() {
            const user = getCurrentUser();
            
            // Create new enrollment
            const newEnrollment = {
                id: enrollments.length + 1,
                studentId: parseInt(user.id),
                courseId: selectedCourse.id,
                enrollmentDate: new Date().toISOString().split('T')[0],
                grade: null
            };
            
            // Add to enrollments
            enrollments.push(newEnrollment);
            studentEnrollments.push(newEnrollment);
            
            // Update course enrollment count
            const courseIndex = courses.findIndex(c => c.id === selectedCourse.id);
            courses[courseIndex].currentEnrollment++;
            
            // Update localStorage
            localStorage.setItem('enrollments', JSON.stringify(enrollments));
            localStorage.setItem('courses', JSON.stringify(courses));
            
            // Show success message
            alert(`Successfully enrolled in ${selectedCourse.title}`);
            
            // Close modal
            closeModals();
            
            // Reload courses
            loadCourses();
        }
        
        // Close all modals
        function closeModals() {
            document.querySelectorAll('.modal-backdrop').forEach(modal => {
                modal.classList.remove('show');
            });
            
            // Reset selected course
            selectedCourse = null;
        }
    </script>
</body>
</html>
