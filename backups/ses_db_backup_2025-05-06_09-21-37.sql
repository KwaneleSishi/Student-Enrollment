DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text,
  `credits` int DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `instructor_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `current_enrollment` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `courses` VALUES('1', 'Introduction to Programming', 'Fundamentals of programming using Python', '3', 'https://via.placeholder.com/400x200?text=Programming+Course', '2', '1', '30', '28', '2025-05-04 13:21:22');
INSERT INTO `courses` VALUES('2', 'Calculus I', 'Differential and integral calculus', '4', 'https://img-c.udemycdn.com/course/240x135/2095166_c9d1_3.jpg', '3', '2', '25', '22', '2025-05-04 13:21:22');
INSERT INTO `courses` VALUES('3', 'Cell Biology', 'Introduction to cellular structures and functions', '4', 'https://via.placeholder.com/400x200?text=Biology+Course', '4', '3', '35', '15', '2025-05-04 13:21:22');
INSERT INTO `courses` VALUES('4', 'Business Management', 'Principles of modern business management', '3', 'https://via.placeholder.com/400x200?text=Business+Course', '5', '4', '40', '32', '2025-05-04 13:21:22');
INSERT INTO `courses` VALUES('5', 'Data Structures', 'Advanced programming concepts and data structures', '3', 'https://via.placeholder.com/400x200?text=Data+Structures', '2', '1', '25', '18', '2025-05-04 13:21:22');
INSERT INTO `courses` VALUES('6', 'Cognitive Psychology', 'Study of mental processes and behavior', '3', 'https://via.placeholder.com/400x200?text=Psychology+Course', '3', '5', '30', '25', '2025-05-04 13:21:22');
INSERT INTO `courses` VALUES('7', 'comptia security+', 'CompTIA Security+ is a global certification that validates the baseline skills necessary to perform core security functions and pursue an IT security career.                                            ', '12', 'assets/uploads/CompTIA-SecPlus.png', '17', '1', '12', '0', '2025-05-05 17:33:08');
INSERT INTO `courses` VALUES('8', 'CompTIA Network+', 'CompTIA Network+ validates the core skills necessary to establish, maintain, troubleshoot and secure networks in any environment, preparing you for a rewarding career in networking and cybersecurity.                                            ', '12', 'assets/uploads/CompTIA-NetPlus.png', '17', '1', '12', '0', '2025-05-05 17:35:54');
INSERT INTO `courses` VALUES('9', 'CompTIA IT Fundamentals', 'CompTIA IT Fundamentals (ITF+) is an introduction to basic IT knowledge and skills that helps you determine whether you have what it takes to work in IT.                                            ', '12', 'assets/uploads/CompTIA-ITF.png', '18', '1', '12', '0', '2025-05-05 17:38:59');
INSERT INTO `courses` VALUES('10', 'Internet programming', 'Internet programming, also known as web programming, involves writing code to create websites and web applications accessible over the internet. It uses programming languages and technologies to build web-based systems, services, and applications that can be accessed globally.                                             ', '4', 'assets/uploads/Internet Programming.jpg', '15', '6', '4', '0', '2025-05-06 03:26:48');
INSERT INTO `courses` VALUES('11', 'Computer networking', 'Computer networking involves connecting devices to share data, resources, and information. These connections, whether wired or wireless, allow devices to communicate and exchange data using protocols like TCP/IP. Networks can range from simple home setups to vast global systems like the internet.                                             ', '4', 'assets/uploads/ComNet.jpg', '13', '10', '4', '0', '2025-05-06 03:28:52');
INSERT INTO `courses` VALUES('12', 'Software Development Life Cycle', 'SDLC stands for Software Development Life Cycle. It\'s a structured process that guides software development from initial planning to deployment and maintenance, ensuring a systematic and efficient approach to creating high-quality software. The SDLC framework helps organizations manage risks, optimize resources, and deliver software that meets user needs and business goals                                            ', '3', 'assets/uploads/SDLC.jpg', '14', '7', '3', '0', '2025-05-06 03:30:43');
INSERT INTO `courses` VALUES('13', 'Python for data science', 'Python is a popular choice for data science due to its versatility, readability, and extensive libraries for data analysis and machine learning. It\'s a free and open-source language, making it accessible for learning and use. Python\'s simplicity and ease of use also make it a good starting point for beginners in the field                                            ', '3', 'assets/uploads/PY4DataSci.jpg', '12', '9', '4', '0', '2025-05-06 03:33:57');
INSERT INTO `courses` VALUES('14', 'Cisco Packet Tracer', 'Cisco Packet Tracer is a network simulation software developed by Cisco Systems. It allows users to visualize and experiment with network topologies, configurations, and protocols in a virtual environment. It\'s used for education, training, and hands-on practice in networking, IoT, and cybersecurity, providing a safe space to learn and troubleshoot network issues                                           ', '3', 'assets/uploads/CicsoPT.jpg', '15', '10', '12', '0', '2025-05-06 03:38:08');
INSERT INTO `courses` VALUES('15', 'Calculus', 'Calculus is a foundational branch of mathematics for university students, particularly those in STEM fields. It deals with the study of continuous change, using concepts like limits, derivatives, and integrals. Calculus is crucial for understanding various scientific and engineering principles, and it\'s often a prerequisite for advanced courses in related disciplines.                                             ', '4', 'assets/uploads/Calculus.png', '12', '2', '12', '0', '2025-05-06 03:41:43');
INSERT INTO `courses` VALUES('16', 'Business analysis', 'Business analysis is the process of examining and evaluating business needs to identify solutions that enable organizations to achieve their goals and manage change effectively. It involves understanding the current state, identifying gaps and problems, and recommending solutions that optimize business processes, systems, and policies                                            ', '3', 'assets/uploads/BA.jpg', '14', '4', '12', '0', '2025-05-06 03:43:58');

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `departments` VALUES('1', 'Computer Science', 'Science Building, Room 301');
INSERT INTO `departments` VALUES('2', 'Mathematics', 'Math Building, Room 201');
INSERT INTO `departments` VALUES('3', 'Biology', 'Science Building, Room 101');
INSERT INTO `departments` VALUES('4', 'Business', 'Business Building, Room 401');
INSERT INTO `departments` VALUES('5', 'Psychology', 'Social Sciences Building, Room 201');
INSERT INTO `departments` VALUES('6', 'Information Technology', 'Technology Building, Room 501');
INSERT INTO `departments` VALUES('7', 'Software Engineering', 'Innovation Center, Room 300');
INSERT INTO `departments` VALUES('8', 'Cybersecurity', 'Security Complex, Lab 4');
INSERT INTO `departments` VALUES('9', 'Data Science', 'Analytics Hub, Room 210');
INSERT INTO `departments` VALUES('10', 'Network Engineering', 'Infrastructure Wing, Lab 3');

DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `EnrollmentID` int NOT NULL AUTO_INCREMENT,
  `StudentID` int NOT NULL,
  `CourseID` int NOT NULL,
  `EnrollmentDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`EnrollmentID`),
  KEY `StudentID` (`StudentID`),
  KEY `CourseID` (`CourseID`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `enrollments` VALUES('1', '1', '1', '2025-05-06 10:37:51');
INSERT INTO `enrollments` VALUES('2', '7', '2', '2025-05-06 10:37:51');
INSERT INTO `enrollments` VALUES('3', '8', '3', '2025-05-06 10:37:51');
INSERT INTO `enrollments` VALUES('4', '9', '4', '2025-05-06 10:37:51');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','instructor','admin') NOT NULL,
  `department_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` VALUES('1', 'Kwanele', 'Sishi', 'kwanelesishi050509@gmail.com', '$2y$10$Sls0Oi5k6AF7jtlPi33a3ebTqcI7PBzSsX33a1pnEEI1Scig8VouG', 'student', '', '2025-05-04 13:00:28');
INSERT INTO `users` VALUES('7', 'John', 'Doe', 'john.doe@student.ses.ac', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '1', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES('8', 'Jane', 'Smith', 'jane.smith@student.ses.ac', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '2', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES('9', 'Bob', 'Wilson', 'bob.wilson@student.ses.ac', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '3', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES('10', 'Azanda', 'Nyide', 'azanda@gmail.com', '$2y$10$.fpcgCOMQBJpx4iTYc9zH.yDJUWcUPF2rfV0bE9l.WIqtpnq6bRVS', 'student', '5', '2025-05-04 16:06:21');
INSERT INTO `users` VALUES('11', 'Admin', 'User', 'admin@ses.ac', '$2y$10$92KUNpkjQnOo5byMl.Yed.4Ea3Rd9llC/og4a2mT7d1E9oYzWbqO', 'admin', '', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES('12', 'Sarah', 'Johnson', 's.johnson@ses.ac', '$2y$10$T4GUfGwrpqH.QQl8aNObzuCwl2zqRHsCq6QyUeb5xU7mW7fK4hF6W', 'instructor', '1', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES('13', 'Michael', 'Chen', 'm.chen@ses.ac', '$2y$10$VpVvKXW.6r9d9tTJ4mZrE.8zq1DkOy7S7sS7s4w4jJz1wK4hF6W', 'instructor', '2', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES('14', 'Emily', 'Wilson', 'e.wilson@ses.ac', '$2y$10$HjZ4V2X.9r9d9tTJ4mZrE.8zq1DkOy7S7sS7s4w4jJz1wK4hF6W', 'instructor', '3', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES('15', 'David', 'Miller', 'd.miller@ses.ac', '$2y$10$LkM5N2X.6r9d9tTJ4mZrE.8zq1DkOy7S7sS7s4w4jJz1wK4hF6W', 'instructor', '4', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES('16', 'admin', 'admin', 'admin12345@gmail.com', '$2y$10$CLrB5HvFkLXiiFUYKett2u1/opMZpEPpOqn8OWWrgqxFW.miAdpCi', 'admin', '1', '2025-05-05 11:21:39');
INSERT INTO `users` VALUES('17', 'Menzi', 'Dlamini', 'menzi@gmail.com', '$2y$10$J4F5WHEYbiK1w1LSF.zFFOzVzQqgZGcL/m4tYsixaWthyH1FA1sVW', 'instructor', '2', '2025-05-05 11:37:42');
INSERT INTO `users` VALUES('18', 'Thokozani', 'Muthwa', 'muthwa@gmail.com', '$2y$10$dAB4TlF6TqclTmRmlJnsNuyhJYJjUzW3x9S0Vcup02IoeLqzvR3Uu', 'instructor', '2', '2025-05-05 11:39:44');

