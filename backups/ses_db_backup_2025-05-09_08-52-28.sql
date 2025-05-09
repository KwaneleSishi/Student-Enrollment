-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: ses_db
-- ------------------------------------------------------
-- Server version	8.0.34

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `course_content`
--

DROP TABLE IF EXISTS `course_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_content` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `lesson_number` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `notes` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_lesson_unique` (`course_id`,`lesson_number`),
  CONSTRAINT `course_content_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_content_chk_1` CHECK ((`lesson_number` between 1 and 4))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_content`
--

LOCK TABLES `course_content` WRITE;
/*!40000 ALTER TABLE `course_content` DISABLE KEYS */;
INSERT INTO `course_content` VALUES (1,9,1,'Basics','https://youtu.be/WWbP246ZWck','IT Fundamentals provide a foundational understanding of computer hardware, software, networking, and security. It\'s an entry-level course for those interested in a career in IT, covering essential concepts and skills needed to perform common IT tasks. \r\n\r\nKey areas covered in IT Fundamentals:\r\nComputer Hardware:\r\nBasic components like the CPU, RAM, storage devices (HDDs, SSDs), and peripherals.\r\n \r\nComputer Software:\r\nOperating systems (Windows, macOS, Linux), software applications, and their functions. \r\n\r\nNetworking:\r\nBasic networking concepts, including IP addresses, protocols, and network devices (routers, switches). \r\n\r\nSecurity:\r\nIntroduction to cybersecurity threats, vulnerabilities, and basic security measures. \r\n\r\nTroubleshooting:\r\nBasic troubleshooting skills for common hardware and software issues. \r\n\r\nData Security and Database Management:\r\nUnderstanding the importance of data security and basic concepts of database management systems (DBMS).','2025-05-09 08:39:53'),(2,9,2,'Hardware and Software','https://youtu.be/WWbP246ZWck','N/A','2025-05-09 08:39:53'),(3,9,3,'Software Development','https://youtu.be/DKvnjeFIjfU','In this livestream we continue our review of the CompTIA ITF+ FC0-U61 exam.  The focus will be on software development.\r\n\r\nObjectives covered in this live stream:\r\nDescribe the purpose of algorithms, flowcharts, and decision tables​\r\nIdentify and give the functions of common flowchart symbols​\r\nExplain the purpose of programming and programming languages​\r\nClassify popular programming languages into different categories​\r\nList and define data types​\r\nList and define common programming decision structures​\r\nCompare and contrast variables and constant identifiers​\r\nDescribe arrays and vectors​\r\nDescribe functions​\r\nDescribe different programming objects​\r\nExplain the purpose of pseudocode and interpret basic examples​\r\nDescribe the general steps and deliverables at each step of the Application Lifecycle Management (ALM) process​\r\nDescribe and explain the testing procedures used in the ALM​','2025-05-09 08:39:53'),(4,9,4,'Ending','https://youtu.be/WWbP246ZWck','N/A','2025-05-09 08:39:53');
/*!40000 ALTER TABLE `course_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (7,'comptia security+','CompTIA Security+ is a global certification that validates the baseline skills necessary to perform core security functions and pursue an IT security career.                                            ',12,'assets/uploads/CompTIA-SecPlus.png',17,1,12,3,'2025-05-05 15:33:08'),(8,'CompTIA Network+','CompTIA Network+ validates the core skills necessary to establish, maintain, troubleshoot and secure networks in any environment, preparing you for a rewarding career in networking and cybersecurity.                                            ',12,'assets/uploads/CompTIA-NetPlus.png',17,1,12,1,'2025-05-05 15:35:54'),(9,'CompTIA IT Fundamentals','CompTIA IT Fundamentals (ITF+) is an introduction to basic IT knowledge and skills that helps you determine whether you have what it takes to work in IT.                                            ',12,'assets/uploads/CompTIA-ITF.png',18,1,12,1,'2025-05-05 15:38:59'),(10,'Internet programming','Internet programming, also known as web programming, involves writing code to create websites and web applications accessible over the internet. It uses programming languages and technologies to build web-based systems, services, and applications that can be accessed globally.                                             ',4,'assets/uploads/Internet Programming.jpg',15,6,4,0,'2025-05-06 01:26:48'),(11,'Computer networking','Computer networking involves connecting devices to share data, resources, and information. These connections, whether wired or wireless, allow devices to communicate and exchange data using protocols like TCP/IP. Networks can range from simple home setups to vast global systems like the internet.                                             ',4,'assets/uploads/ComNet.jpg',13,10,4,1,'2025-05-06 01:28:52'),(12,'Software Development Life Cycle','SDLC stands for Software Development Life Cycle. It\'s a structured process that guides software development from initial planning to deployment and maintenance, ensuring a systematic and efficient approach to creating high-quality software. The SDLC framework helps organizations manage risks, optimize resources, and deliver software that meets user needs and business goals                                            ',3,'assets/uploads/SDLC.jpg',14,7,3,1,'2025-05-06 01:30:43'),(13,'Python for data science','Python is a popular choice for data science due to its versatility, readability, and extensive libraries for data analysis and machine learning. It\'s a free and open-source language, making it accessible for learning and use. Python\'s simplicity and ease of use also make it a good starting point for beginners in the field                                            ',3,'assets/uploads/PY4DataSci.jpg',12,9,4,0,'2025-05-06 01:33:57'),(14,'Cisco Packet Tracer','Cisco Packet Tracer is a network simulation software developed by Cisco Systems. It allows users to visualize and experiment with network topologies, configurations, and protocols in a virtual environment. It\'s used for education, training, and hands-on practice in networking, IoT, and cybersecurity, providing a safe space to learn and troubleshoot network issues                                           ',3,'assets/uploads/CicsoPT.jpg',15,10,12,1,'2025-05-06 01:38:08'),(15,'Calculus','Calculus is a foundational branch of mathematics for university students, particularly those in STEM fields. It deals with the study of continuous change, using concepts like limits, derivatives, and integrals. Calculus is crucial for understanding various scientific and engineering principles, and it\'s often a prerequisite for advanced courses in related disciplines.                                             ',4,'assets/uploads/Calculus.png',12,2,12,0,'2025-05-06 01:41:43'),(16,'Business analysis','Business analysis is the process of examining and evaluating business needs to identify solutions that enable organizations to achieve their goals and manage change effectively. It involves understanding the current state, identifying gaps and problems, and recommending solutions that optimize business processes, systems, and policies                                            ',3,'assets/uploads/BA.jpg',14,4,12,1,'2025-05-06 01:43:58');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Computer Science','Science Building, Room 301'),(2,'Mathematics','Math Building, Room 201'),(3,'Biology','Science Building, Room 101'),(4,'Business','Business Building, Room 401'),(5,'Psychology','Social Sciences Building, Room 201'),(6,'Information Technology','Technology Building, Room 501'),(7,'Software Engineering','Innovation Center, Room 300'),(8,'Cybersecurity','Security Complex, Lab 4'),(9,'Data Science','Analytics Hub, Room 210'),(10,'Network Engineering','Infrastructure Wing, Lab 3');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enrollments` (
  `EnrollmentID` int NOT NULL AUTO_INCREMENT,
  `StudentID` int NOT NULL,
  `CourseID` int NOT NULL,
  `EnrollmentDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_lessons` json DEFAULT NULL COMMENT 'JSON array of completed lesson numbers (1-4)',
  PRIMARY KEY (`EnrollmentID`),
  KEY `StudentID` (`StudentID`),
  KEY `CourseID` (`CourseID`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` VALUES (5,10,13,'2025-05-07 08:02:32',NULL),(6,10,16,'2025-05-07 08:29:56',NULL),(7,10,11,'2025-05-07 08:33:25',NULL),(8,19,12,'2025-05-07 08:41:08',NULL),(9,19,8,'2025-05-07 08:41:13',NULL),(10,1,14,'2025-05-07 08:42:22',NULL),(11,1,7,'2025-05-07 08:42:32',NULL),(12,10,9,'2025-05-09 01:54:07','[1]'),(13,10,7,'2025-05-09 08:44:06',NULL);
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Anele','Sishi','kwanelesishi050509@gmail.com','$2y$10$Sls0Oi5k6AF7jtlPi33a3ebTqcI7PBzSsX33a1pnEEI1Scig8VouG','student',NULL,'2025-05-04 11:00:28'),(7,'John','Doe','john.doe@student.ses.ac','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','student',1,'2025-05-04 11:21:22'),(8,'Jane','Smith','jane.smith@student.ses.ac','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','student',2,'2025-05-04 11:21:22'),(9,'Bob','Wilson','bob.wilson@student.ses.ac','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','student',3,'2025-05-04 11:21:22'),(10,'Azanda','Nyide','azanda@gmail.com','$2y$10$.fpcgCOMQBJpx4iTYc9zH.yDJUWcUPF2rfV0bE9l.WIqtpnq6bRVS','student',5,'2025-05-04 14:06:21'),(11,'Admin','User','admin@ses.ac','$2y$10$92KUNpkjQnOo5byMl.Yed.4Ea3Rd9llC/og4a2mT7d1E9oYzWbqO','admin',NULL,'2025-05-04 11:21:22'),(12,'Sarah','Johnson','s.johnson@ses.ac','$2y$10$T4GUfGwrpqH.QQl8aNObzuCwl2zqRHsCq6QyUeb5xU7mW7fK4hF6W','instructor',1,'2025-05-04 11:21:22'),(13,'Michael','Chen','m.chen@ses.ac','$2y$10$VpVvKXW.6r9d9tTJ4mZrE.8zq1DkOy7S7sS7s4w4jJz1wK4hF6W','instructor',2,'2025-05-04 11:21:22'),(14,'Emily','Wilson','e.wilson@ses.ac','$2y$10$HjZ4V2X.9r9d9tTJ4mZrE.8zq1DkOy7S7sS7s4w4jJz1wK4hF6W','instructor',3,'2025-05-04 11:21:22'),(15,'David','Miller','d.miller@ses.ac','$2y$10$LkM5N2X.6r9d9tTJ4mZrE.8zq1DkOy7S7sS7s4w4jJz1wK4hF6W','instructor',4,'2025-05-04 11:21:22'),(16,'admin','admin','admin12345@gmail.com','$2y$10$CLrB5HvFkLXiiFUYKett2u1/opMZpEPpOqn8OWWrgqxFW.miAdpCi','admin',1,'2025-05-05 09:21:39'),(17,'Menzi','Dlamini','menzi@gmail.com','$2y$10$J4F5WHEYbiK1w1LSF.zFFOzVzQqgZGcL/m4tYsixaWthyH1FA1sVW','instructor',2,'2025-05-05 09:37:42'),(18,'Thokozani','Muthwa','muthwa@gmail.com','$2y$10$dAB4TlF6TqclTmRmlJnsNuyhJYJjUzW3x9S0Vcup02IoeLqzvR3Uu','instructor',2,'2025-05-05 09:39:44'),(19,'Sibonelo','Faye','faye@gmail.com','$2y$10$XS7Pv485FUYBJ0ppmAuc9OLOtj1n1xrw2rhC6/5kl2KAyjwskXa5K','student',1,'2025-05-07 10:40:12');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-09 10:52:29
