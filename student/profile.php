<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | SESAcademy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Variables */
        :root {
          --primary-color: #5624d0;
          --primary-hover: #401b9c;
          --secondary-color: #1e1e1c;
          --accent-color: #f5f5f5;
          --text-color: #1c1d1f;
          --text-light: #6a6f73;
          --border-color: #d1d7dc;
          --success-color: #1eb2a6;
          --warning-color: #f69c14;
          --danger-color: #e41e3f;
          --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
          --container-width: 1340px;
          --sidebar-width: 280px;
        }

        /* Reset and Base Styles */
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }

        body {
          font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
          line-height: 1.6;
          color: var(--text-color);
          background-color: #f7f9fa;
        }

        a {
          text-decoration: none;
          color: var(--primary-color);
        }

        ul {
          list-style: none;
        }

        .container {
          width: 100%;
          max-width: var(--container-width);
          margin: 0 auto;
          padding: 0 20px;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
          font-weight: 700;
          line-height: 1.2;
          margin-bottom: 0.5rem;
        }

        h1 {
          font-size: 2.5rem;
        }

        h2 {
          font-size: 2rem;
        }

        h3 {
          font-size: 1.5rem;
        }

        p {
          margin-bottom: 1rem;
        }

        /* Buttons */
        .btn {
          display: inline-block;
          padding: 12px 24px;
          font-weight: 700;
          font-size: 0.9rem;
          text-align: center;
          border-radius: 4px;
          cursor: pointer;
          transition: all 0.2s ease;
          border: none;
        }

        .btn-sm {
          padding: 8px 16px;
          font-size: 0.8rem;
        }

        .btn-primary {
          background-color: var(--primary-color);
          color: white;
        }

        .btn-primary:hover {
          background-color: var(--primary-hover);
        }

        .btn-outline {
          background-color: transparent;
          border: 1px solid var(--border-color);
          color: var(--text-color);
        }

        .btn-outline:hover {
          background-color: #f7f9fa;
        }

        .btn-danger {
          background-color: var(--danger-color);
          color: white;
        }

        .btn-danger:hover {
          background-color: #c91b36;
        }

        .btn-block {
          display: block;
          width: 100%;
        }

        .btn:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }

        /* Forms */
        .form-group {
          margin-bottom: 1.5rem;
        }

        .form-control {
          display: block;
          width: 100%;
          padding: 12px 16px;
          font-size: 1rem;
          line-height: 1.5;
          color: var(--text-color);
          background-color: #fff;
          border: 1px solid var(--border-color);
          border-radius: 4px;
          transition: border-color 0.15s ease-in-out;
        }

        .form-control:focus {
          border-color: var(--primary-color);
          outline: 0;
        }

        .form-control[readonly] {
          background-color: #f7f9fa;
          cursor: not-allowed;
        }

        label {
          display: block;
          margin-bottom: 0.5rem;
          font-weight: 600;
        }

        .form-text {
          display: block;
          margin-top: 0.25rem;
          font-size: 0.875rem;
          color: var(--text-light);
        }

        .form-row {
          display: flex;
          flex-wrap: wrap;
          margin-right: -10px;
          margin-left: -10px;
        }

        .form-row > .form-group {
          flex: 1 0 0%;
          padding-right: 10px;
          padding-left: 10px;
        }

        .form-actions {
          display: flex;
          gap: 10px;
          margin-top: 20px;
        }

        /* Header */
        .header {
          background-color: white;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
          position: sticky;
          top: 0;
          z-index: 100;
        }

        .header-container {
          display: flex;
          align-items: center;
          height: 72px;
        }

        .logo {
          font-size: 1.5rem;
          font-weight: 700;
          color: var(--primary-color);
        }

        .logo span {
          font-weight: 400;
          color: var(--text-color);
        }

        .search-bar {
          flex: 1;
          max-width: 400px;
          margin: 0 20px;
          position: relative;
        }

        .search-bar input {
          width: 100%;
          padding: 12px 16px 12px 40px;
          border: 1px solid var(--border-color);
          border-radius: 9999px;
          font-size: 0.9rem;
        }

        .search-bar i {
          position: absolute;
          left: 16px;
          top: 50%;
          transform: translateY(-50%);
          color: var(--text-light);
        }

        .nav-links {
          display: flex;
          margin-left: auto;
        }

        .nav-links a {
          padding: 0 16px;
          color: var(--text-color);
          font-weight: 500;
          height: 72px;
          display: flex;
          align-items: center;
          border-bottom: 2px solid transparent;
        }

        .nav-links a:hover,
        .nav-links a.active {
          color: var(--primary-color);
          border-bottom: 2px solid var(--primary-color);
        }

        .user-menu {
          margin-left: 20px;
        }

        .user-avatar {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          background-color: var(--primary-color);
          color: white;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: 600;
          cursor: pointer;
        }

        /* Main Layout */
        .main-layout {
          display: flex;
          min-height: calc(100vh - 72px);
        }

        /* Sidebar */
        .sidebar {
          width: var(--sidebar-width);
          background-color: white;
          border-right: 1px solid var(--border-color);
          padding: 20px 0;
          overflow-y: auto;
        }

        .sidebar-menu {
          padding: 0 20px;
        }

        .sidebar-menu h3 {
          font-size: 0.9rem;
          text-transform: uppercase;
          color: var(--text-light);
          margin: 20px 0 10px;
        }

        .sidebar-menu ul {
          margin-bottom: 20px;
        }

        .sidebar-menu li {
          margin-bottom: 5px;
        }

        .sidebar-menu a {
          display: flex;
          align-items: center;
          padding: 10px 15px;
          color: var(--text-color);
          border-radius: 4px;
          font-weight: 500;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
          background-color: #f7f9fa;
          color: var(--primary-color);
        }

        .sidebar-menu a i {
          margin-right: 10px;
          width: 20px;
          text-align: center;
        }

        /* Content */
        .content {
          flex: 1;
          padding: 30px;
          overflow-y: auto;
        }

        .dashboard-header {
          margin-bottom: 30px;
        }

        .dashboard-header h1 {
          margin-bottom: 5px;
        }

        .dashboard-header p {
          color: var(--text-light);
          font-size: 1.1rem;
        }

        /* Profile Page */
        .profile-container {
          display: flex;
          gap: 30px;
        }

        .profile-sidebar {
          width: 280px;
          flex-shrink: 0;
        }

        .profile-avatar-container {
          background-color: white;
          border-radius: 8px;
          box-shadow: var(--card-shadow);
          padding: 20px;
          text-align: center;
          margin-bottom: 20px;
        }

        .profile-avatar {
          width: 150px;
          height: 150px;
          border-radius: 50%;
          background-color: var(--primary-color);
          color: white;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 3rem;
          font-weight: 600;
          margin: 0 auto 15px;
          overflow: hidden;
        }

        .profile-avatar img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          display: none;
        }

        .avatar-upload {
          margin-top: 15px;
        }

        .profile-stats {
          background-color: white;
          border-radius: 8px;
          box-shadow: var(--card-shadow);
          padding: 20px;
        }

        .stat-item {
          display: flex;
          justify-content: space-between;
          margin-bottom: 15px;
        }

        .stat-item:last-child {
          margin-bottom: 0;
        }

        .stat-label {
          color: var(--text-light);
        }

        .stat-value {
          font-weight: 700;
        }

        .profile-content {
          flex: 1;
          background-color: white;
          border-radius: 8px;
          box-shadow: var(--card-shadow);
          overflow: hidden;
        }

        .profile-tabs {
          display: flex;
          border-bottom: 1px solid var(--border-color);
        }

        .tab-btn {
          padding: 15px 20px;
          font-weight: 600;
          background: none;
          border: none;
          cursor: pointer;
          border-bottom: 2px solid transparent;
          transition: all 0.2s ease;
        }

        .tab-btn:hover {
          color: var(--primary-color);
        }

        .tab-btn.active {
          color: var(--primary-color);
          border-bottom-color: var(--primary-color);
        }

        .tab-content {
          display: block;
          padding: 20px;
        }

        .tab-content.hidden {
          display: none;
        }

        .profile-form {
          max-width: 800px;
        }

        /* Modal */
        .modal {
          display: none;
          position: fixed;
          z-index: 1000;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.5);
          overflow: auto;
        }

        .modal-content {
          background-color: white;
          margin: 10% auto;
          padding: 0;
          border-radius: 8px;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
          width: 90%;
          max-width: 500px;
          animation: modalopen 0.3s;
        }

        @keyframes modalopen {
          from {
            opacity: 0;
            transform: translateY(-60px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .modal-header {
          padding: 15px 20px;
          border-bottom: 1px solid var(--border-color);
          display: flex;
          justify-content: space-between;
          align-items: center;
        }

        .modal-header h3 {
          margin: 0;
        }

        .close-modal {
          color: var(--text-light);
          font-size: 28px;
          font-weight: bold;
          cursor: pointer;
        }

        .modal-body {
          padding: 20px;
        }

        .modal-footer {
          padding: 15px 20px;
          border-top: 1px solid var(--border-color);
          display: flex;
          justify-content: flex-end;
        }

        /* Spinner for loading state */
        .spinner {
          border: 4px solid rgba(0, 0, 0, 0.1);
          width: 36px;
          height: 36px;
          border-radius: 50%;
          border-left-color: #7e3af2;
          animation: spin 1s linear infinite;
          margin: 0 auto 15px;
        }

        @keyframes spin {
          0% {
            transform: rotate(0deg);
          }
          100% {
            transform: rotate(360deg);
          }
        }

        /* Text center helper */
        .text-center {
          text-align: center;
        }

        /* Error text color */
        .text-error {
          color: #e02424;
        }

        /* Error button */
        .btn-error {
          background-color: #e02424;
          color: white;
        }

        .btn-error:hover {
          background-color: #c81e1e;
        }

        /* Password strength indicator */
        .password-strength {
          margin-top: 5px;
          height: 5px;
          border-radius: 3px;
          transition: all 0.3s ease;
        }

        .strength-weak {
          background-color: #e02424;
          width: 30%;
        }

        .strength-medium {
          background-color: #f59e0b;
          width: 60%;
        }

        .strength-strong {
          background-color: #10b981;
          width: 100%;
        }

        .password-feedback {
          font-size: 12px;
          margin-top: 5px;
        }

        /* Responsive */
        @media (max-width: 992px) {
          .nav-links {
            display: none;
          }

          .sidebar {
            width: 240px;
          }

          .profile-container {
            flex-direction: column;
          }

          .profile-sidebar {
            width: 100%;
            margin-bottom: 20px;
          }
        }

        @media (max-width: 768px) {
          .main-layout {
            flex-direction: column;
          }

          .sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid var(--border-color);
            padding: 10px 0;
          }

          .sidebar-menu {
            display: flex;
            overflow-x: auto;
            padding: 0 10px;
          }

          .sidebar-menu h3 {
            display: none;
          }

          .sidebar-menu ul {
            display: flex;
            margin-right: 20px;
            margin-bottom: 0;
          }

          .sidebar-menu li {
            margin-bottom: 0;
            margin-right: 5px;
          }

          .form-row {
            flex-direction: column;
          }

          .form-row > .form-group {
            padding: 0;
          }
        }

        @media (max-width: 576px) {
          .header-container {
            height: auto;
            flex-wrap: wrap;
            padding: 10px 20px;
          }

          .logo {
            margin-bottom: 10px;
          }

          .search-bar {
            order: 3;
            max-width: 100%;
            margin: 10px 0 0;
          }

          .user-menu {
            margin-left: auto;
          }

          .profile-tabs {
            flex-direction: column;
          }

          .tab-btn {
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            border-left: 2px solid transparent;
          }

          .tab-btn.active {
            border-bottom-color: var(--border-color);
            border-left-color: var(--primary-color);
          }

          .form-actions {
            flex-direction: column;
          }

          .form-actions .btn {
            width: 100%;
          }
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
                <input type="text" placeholder="Search for courses">
            </div>
            
            <div class="nav-links">
                <a href="dashboard.html">My Learning</a>
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
        <div class="sidebar">
            <div class="sidebar-menu">
                <h3>Menu</h3>
                <ul>
                    <li><a href="dashboard.html"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                    <li><a href="catalog.html"><i class="fas fa-book"></i> <span>Course Catalog</span></a></li>
                    <li><a href="grades.html"><i class="fas fa-chart-bar"></i> <span>My Grades</span></a></li>
                </ul>
                
                <h3>Account</h3>
                <ul>
                    <li><a href="profile.html" class="active"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                    <li><a href="#" id="logout-btn"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="dashboard-header">
                <h1>My Profile</h1>
                <p>View and edit your personal information</p>
            </div>
            
            <div class="profile-container">
                <div class="profile-sidebar">
                    <div class="profile-avatar-container">
                        <div class="profile-avatar" id="profile-avatar">
                            <img id="avatar-preview" src="../images/default-avatar.png" alt="Profile Picture">
                        </div>
                        <div class="avatar-upload">
                            <label for="avatar-upload" class="btn btn-sm btn-outline">
                                <i class="fas fa-camera"></i> Change Photo
                            </label>
                            <input type="file" id="avatar-upload" accept="image/*" style="display: none;">
                        </div>
                    </div>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-label">Enrolled Courses</span>
                            <span class="stat-value" id="enrolled-count">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Completed Courses</span>
                            <span class="stat-value" id="completed-count">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Current GPA</span>
                            <span class="stat-value" id="current-gpa">0.0</span>
                        </div>
                    </div>
                </div>
                
                <div class="profile-content">
                    <div class="profile-tabs">
                        <button class="tab-btn active" data-tab="personal">Personal Information</button>
                        <button class="tab-btn" data-tab="password">Change Password</button>
                    </div>
                    
                    <div class="tab-content" id="personal-tab">
                        <form id="personal-form" class="profile-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first-name">First Name</label>
                                    <input type="text" id="first-name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="last-name">Last Name</label>
                                    <input type="text" id="last-name" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" class="form-control" required>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <button type="reset" class="btn btn-outline">Reset</button>
                            </div>
                        </form>
                    </div>
                                                          
                    <div class="tab-content hidden" id="password-tab">
                        <form id="password-form" class="profile-form">
                            <div class="form-group">
                                <label for="current-password">Current Password</label>
                                <input type="password" id="current-password" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="new-password">New Password</label>
                                <input type="password" id="new-password" class="form-control" required>
                                <small class="form-text">Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm-password">Confirm New Password</label>
                                <input type="password" id="confirm-password" class="form-control" required>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Change Password</button>
                                <button type="reset" class="btn btn-outline">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div class="modal" id="success-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Success</h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <p id="success-message">Your changes have been saved successfully.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary close-modal">OK</button>
            </div>
        </div>
    </div>
    
    <script src="../js/auth.js"></script>
    <script src="../js/data.js"></script>
    <script src="../Student/profile.js"></script>
</body>
</html>
