DROP TABLE IF EXISTS `course_content`;
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `EnrollmentID` int NOT NULL AUTO_INCREMENT,
  `StudentID` int NOT NULL,
  `CourseID` int NOT NULL,
  `EnrollmentDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_lessons` json DEFAULT NULL COMMENT 'JSON array of completed lesson numbers (1-4)',
  `total_grade` int DEFAULT '0',
  PRIMARY KEY (`EnrollmentID`),
  KEY `StudentID` (`StudentID`),
  KEY `CourseID` (`CourseID`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `quiz_attempts`;
CREATE TABLE `quiz_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `enrollment_id` int NOT NULL,
  `attempt_number` int NOT NULL DEFAULT '1',
  `score` int NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `quiz_attempts_ibfk_1` (`enrollment_id`),
  CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`EnrollmentID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `quiz_questions`;
CREATE TABLE `quiz_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `question_number` int NOT NULL,
  `question_text` text NOT NULL,
  `choice_1` varchar(255) NOT NULL,
  `choice_2` varchar(255) NOT NULL,
  `choice_3` varchar(255) NOT NULL,
  `choice_4` varchar(255) NOT NULL,
  `correct_choice` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_question_unique` (`course_id`,`question_number`),
  CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `course_content` VALUES ('1', '9', '1', 'Basics', 'https://youtu.be/WWbP246ZWck', 'IT Fundamentals provide a foundational understanding of computer hardware, software, networking, and security. It\'s an entry-level course for those interested in a career in IT, covering essential concepts and skills needed to perform common IT tasks. \r\n\r\nKey areas covered in IT Fundamentals:\r\nComputer Hardware:\r\nBasic components like the CPU, RAM, storage devices (HDDs, SSDs), and peripherals.\r\n \r\nComputer Software:\r\nOperating systems (Windows, macOS, Linux), software applications, and their functions. \r\n\r\nNetworking:\r\nBasic networking concepts, including IP addresses, protocols, and network devices (routers, switches). \r\n\r\nSecurity:\r\nIntroduction to cybersecurity threats, vulnerabilities, and basic security measures. \r\n\r\nTroubleshooting:\r\nBasic troubleshooting skills for common hardware and software issues. \r\n\r\nData Security and Database Management:\r\nUnderstanding the importance of data security and basic concepts of database management systems (DBMS).', '2025-05-09 10:39:53');
INSERT INTO `course_content` VALUES ('2', '9', '2', 'Hardware and Software', 'https://youtu.be/WWbP246ZWck', 'N/A', '2025-05-09 10:39:53');
INSERT INTO `course_content` VALUES ('3', '9', '3', 'Software Development', 'https://youtu.be/DKvnjeFIjfU', 'In this livestream we continue our review of the CompTIA ITF+ FC0-U61 exam.  The focus will be on software development.\r\n\r\nObjectives covered in this live stream:\r\nDescribe the purpose of algorithms, flowcharts, and decision tables​\r\nIdentify and give the functions of common flowchart symbols​\r\nExplain the purpose of programming and programming languages​\r\nClassify popular programming languages into different categories​\r\nList and define data types​\r\nList and define common programming decision structures​\r\nCompare and contrast variables and constant identifiers​\r\nDescribe arrays and vectors​\r\nDescribe functions​\r\nDescribe different programming objects​\r\nExplain the purpose of pseudocode and interpret basic examples​\r\nDescribe the general steps and deliverables at each step of the Application Lifecycle Management (ALM) process​\r\nDescribe and explain the testing procedures used in the ALM​', '2025-05-09 10:39:53');
INSERT INTO `course_content` VALUES ('4', '9', '4', 'Ending', 'https://youtu.be/WWbP246ZWck', 'N/A', '2025-05-09 10:39:53');
INSERT INTO `course_content` VALUES ('5', '7', '1', 'Security Concepts', 'https://youtu.be/6CIN-_cSegQ', '1. CIA Triad\r\n\r\nConfidentiality: Ensures data is accessible only to authorized users (e.g., encryption, access controls).\r\n\r\nIntegrity: Maintains data authenticity and prevents unauthorized modification (e.g., digital signatures, hashing).\r\n\r\nAvailability: Ensures systems/data are accessible when needed (e.g., redundancy, backups).\r\n\r\n2. Cybersecurity Framework (NIST)\r\n\r\nFive Functions:\r\n\r\nIdentify: Develop policies, assess risks, and recommend controls.\r\n\r\nProtect: Implement safeguards (e.g., firewalls, antivirus).\r\n\r\nDetect: Continuously monitor for threats (e.g., IDS, audits).\r\n\r\nRespond: Mitigate and contain incidents (e.g., incident response plans).\r\n\r\nRecover: Restore operations post-incident (e.g., backups, disaster recovery).\r\n\r\n3. Access Control\r\n\r\nIdentification: Assigning unique user accounts (e.g., Active Directory).\r\n\r\nAuthentication: Verifying identity (e.g., passwords, biometrics, MFA).\r\n\r\nAuthorization: Granting permissions based on roles (e.g., least privilege).\r\n\r\nAccounting: Logging and auditing access (e.g., audit trails).', '2025-05-10 15:21:54');
INSERT INTO `course_content` VALUES ('6', '7', '2', 'Security Controls', 'https://youtu.be/6CIN-_cSegQ', '1. Security Control Categories\r\n\r\nManagerial: Policies, risk assessments, governance (e.g., risk management frameworks).\r\n\r\nOperational: Implemented by people (e.g., training, security guards).\r\n\r\nTechnical: Automated systems (e.g., firewalls, encryption).\r\n\r\nPhysical: Tangible safeguards (e.g., locks, CCTV).\r\n\r\n2. Security Control Types\r\n\r\nPreventive: Block attacks (e.g., firewalls, antivirus).\r\n\r\nDetective: Identify breaches (e.g., IDS, log monitoring).\r\n\r\nCorrective: Mitigate damage (e.g., patches, backups).\r\n\r\nDirective: Enforce policies (e.g., SOPs, compliance standards).\r\n\r\nDeterrent: Discourage attacks (e.g., warnings, security signage).\r\n\r\nCompensating: Alternative safeguards (e.g., multi-factor authentication).', '2025-05-10 15:21:54');
INSERT INTO `course_content` VALUES ('7', '7', '3', 'Information Security Roles and Responsibilities', 'https://youtu.be/6CIN-_cSegQ', '1. Organizational Roles\r\n\r\nCISO/CSO: Oversees security strategy and compliance.\r\n\r\nIT Managers: Implement and maintain controls.\r\n\r\nSecurity Administrators: Monitor threats and enforce policies.\r\n\r\nEnd Users: Follow security protocols (e.g., password hygiene).\r\n\r\n2. Security Competencies\r\n\r\nRisk assessment, incident response, and security tool configuration.\r\n\r\nCollaboration across departments (e.g., HR for training, legal for compliance).', '2025-05-10 15:21:54');
INSERT INTO `course_content` VALUES ('8', '7', '4', 'Information Security Business Units', 'https://youtu.be/6CIN-_cSegQ', 'Business Units\r\n\r\nSOC (Security Operations Center): Monitors and responds to threats.\r\n\r\nDevSecOps: Integrates security into software development.\r\n\r\nCIRT (Computer Incident Response Team): Handles security incidents.', '2025-05-10 15:21:54');
INSERT INTO `course_content` VALUES ('9', '8', '1', 'Networking Overview', 'https://youtu.be/Jc0nySo2wjY', 'CompTIA Network+ N10-009 Full Video Course. In this lesson we start things of in the course doing a Networking Overview. This is the first lesson in Module 1 of the course which consists of 14 modules in total\r\nIn this lesson we compare Personal and IT Networks, we discuss Peer-to-Peer and Client Server networks, we discuss different kinds of Local Area Networks and then lastly, we also cover Network Topologies', '2025-05-10 16:10:25');
INSERT INTO `course_content` VALUES ('10', '8', '2', 'OSI Model layers', 'https://youtu.be/uE84knYoJSY', 'CompTIA Network+ N10-009 Full Video Course. In this lesson we discuss the OSI Model layers in depth. This is the second lesson in Module 1 of the course which consists of 14 modules in total\r\n\r\nIn this lesson we do an overview of the OSI Model and compare them to each other with a discussion on what each layer actually does', '2025-05-10 16:10:25');
INSERT INTO `course_content` VALUES ('11', '8', '3', 'Configure SOHO Networks', 'https://youtu.be/9qDD-bwiQKM', 'CompTIA Network+ N10-009 Full Video Course. In this lesson we discuss SOHO Networks and Routers in depth. This is the third lesson in Module 1 of the course which consists of 14 modules in total\r\n\r\nIn this lesson we do an overview of what SOHO networks are and discuss a bit about Routers, how they form part of Local Area Networks', '2025-05-10 16:10:25');
INSERT INTO `course_content` VALUES ('12', '8', '4', 'Troubleshooting Methodology', 'https://youtu.be/Ogx3IE1gbGo', 'CompTIA Network+ N10-009 Full Video Course. In this lesson we discuss the Troubleshooting Methodology in depth. This is the fourth lesson in Module 1 of the course which consists of 14 modules in total\r\n\r\nIn this lesson we do an overview of the Troubleshooting Methodology technicians follow when troubleshooting network related problems as well as other problems in IT in general. Think of them as the order in which troubleshooting steps need to take place.', '2025-05-10 16:10:25');
INSERT INTO `course_content` VALUES ('13', '11', '1', 'Introduction to Networking', 'https://youtu.be/9SIjoeE93lo', 'This Network Fundamentals Series is designed to help anyone wanting to get into the IT field learn more about Networking. We will introduce you to the terminology used in the field and help you understand what it means. Whether you\'re a newbie in the IT field, looking for a career in computer networking or just someone who\'s always been fascinated by the magic of networking, this series is tailor-made for you.\r\n\r\n2. What is a Network?\r\nDefinition:\r\n\r\nA system enabling multiple devices to communicate.\r\n\r\nKey Components:\r\n\r\nDevices: Computers, printers, TVs, phones, etc.\r\n\r\nPurpose: Share information (documents, emails, videos, internet access).\r\n\r\n3. Connecting Devices\r\nWired Connections:\r\n\r\nSwitch: Central device connecting wired devices (e.g., in schools or offices).\r\n\r\nPatch Panel: Organizes cables from wall sockets to switches.\r\n\r\nExample Setup:\r\n\r\nWall socket → Patch panel → Switch → Network.\r\n\r\nWireless Connections:\r\n\r\nWireless Access Point (WAP): Connects devices via Wi-Fi (e.g., phones, laptops).\r\n\r\nHybrid Networks: Combine switches and WAPs for flexibility.\r\n\r\nExample: Laptop uses cable at desk and Wi-Fi in meetings.\r\n\r\n4. Protocols: The \"Language\" of Networks\r\nPurpose: Ensure devices agree on how data is sent, received, and organized.\r\n\r\nCommon Protocols:\r\n\r\nEthernet & TCP: For general data transmission.\r\n\r\nHTTP: Web browsing.\r\n\r\nSMTP: Email communication.\r\n\r\nKey Insight: Multiple protocols often work together for tasks (e.g., loading a webpage uses HTTP over TCP/IP).\r\n\r\n5. Quiz & Learning Reinforcement\r\nPurpose: Test understanding and identify knowledge gaps.\r\n\r\nResources:\r\n\r\nVisit networkdirection.net for answers and explanations.\r\n\r\n6. Key Takeaways\r\nNetworks enable communication between devices via wired/wireless connections.\r\n\r\nSwitches and WAPs are central to connectivity.\r\n\r\nProtocols ensure devices \"speak the same language.\"', '2025-05-10 16:46:52');
INSERT INTO `course_content` VALUES ('14', '11', '2', 'Different Types Of Networks', 'https://youtu.be/h42qLaQM0_s', 'This video is designed for those who are new to networking. You only need to bring your curiosity and a desire to learn. This video explains - how networks connect devices together, and what are SOHO networks, SMB networks, LANs and WANs.\r\n\r\nOur Network Fundamentals Series is designed to help anyone wanting to get into the IT field learn more about Networking. We will introduce you to the terminology used in the field and help you understand what it means. Whether you\'re a newbie in the IT field, looking for a career in computer networking or just someone who\'s always been fascinated by the magic of networking, this series is tailor-made for you.', '2025-05-10 16:46:52');
INSERT INTO `course_content` VALUES ('15', '11', '3', 'How the OSI Model Works', 'https://youtu.be/y9PG-_ZNbWg', 'How the OSI Model Works | Network Fundamentals Part 3\r\nThe OSI Model Explained\r\n\r\nSurely you\'ve heard about the #OSI model. That\'s why you\'re here right?\r\n\r\nWell, this is the right place for you! In this video we discuss models, and why they\'re used. We look into the physical, data link, network, transport, session, presentation, and application layers, and how they relate to the real world.\r\n\r\nAnd finally, we look at an example. This shows how a web request flows through each of the layers.', '2025-05-10 16:46:52');
INSERT INTO `course_content` VALUES ('16', '11', '4', 'How IP Addresses Work', 'https://youtu.be/v8aYhOxZuNg', 'Welcome to the fourth part of the Network Foundation series. This video looks at IP addressing, and how it works. This is critical information for anyone new to networking, or studying for CCNA or CCENT exams.\r\n\r\nWe start at the beginning, with what IP’s look like, and why. Understanding binary is your friend here! (   • Learn Binary and Convert to Decimal |...  ).\r\n\r\nDid you know that IP addresses are two addresses in one? Yes, it’s true! An IP includes the host address, as well as the address of the network it resides in.\r\n\r\nSpeaking of networks, we have changed how we address them over time. One of the early methods was to use classes. Perhaps you’ve heard of class A, B, and C networks?But this has its limitations. So, we also have classless networks, or CIDR (Classless Inter-Domain Routing). This introduces a new concept: The subnet mask. Now we can break up networks as we see fit!', '2025-05-10 16:46:52');
INSERT INTO `course_content` VALUES ('17', '14', '1', 'Basics of Cisco Packet Tracer (Part 1)', 'https://youtu.be/frUQMHXhnvs?list=PLBlnK6fEyqRgMoNJXQxrIIKRpSrVBuTlK', 'Computer Networks: \r\nBasics of Cisco Packet Tracer (Part 1)\r\nTopics discussed:\r\n1) The download procedure of Cisco Packet Tracer.\r\n2) The basics of Cisco Packet Tracer.\r\n3) Example packet tracer peer-to-peer network.', '2025-05-10 16:53:12');
INSERT INTO `course_content` VALUES ('18', '14', '2', 'Basics of Cisco Packet Tracer (Part 2) | Hub', 'https://youtu.be/FZ8hRDakHvI?list=PLBlnK6fEyqRgMoNJXQxrIIKRpSrVBuTlK', 'Computer Networks: \r\nBasics of Cisco Packet Tracer (Part 2)\r\n \r\nTopics discussed:\r\n1) Basics of Cisco Packet Tracer.\r\n2) Hub.\r\n3) Cisco Packet Tracer Simulation of LAN using the hub.\r\n4) Creating a LAN using the hub.\r\n5) Pros and Cons of the hub.', '2025-05-10 16:53:13');
INSERT INTO `course_content` VALUES ('19', '14', '3', 'Basics of Cisco Packet Tracer (Part 3) | Switch', 'https://youtu.be/eFY6mi3lmRQ?list=PLBlnK6fEyqRgMoNJXQxrIIKRpSrVBuTlK', 'Computer Networks: \r\nBasics of Cisco Packet Tracer (Part 3)\r\n \r\nTopics discussed:\r\n1) Basics of Cisco Packet Tracer.\r\n2) Switch.\r\n3) Cisco Packet Tracer Simulation of LAN using the switch.\r\n4) Creating a LAN using the switch.\r\n5) Difference between hub and switch.', '2025-05-10 16:53:13');
INSERT INTO `course_content` VALUES ('20', '14', '4', 'Basics of Cisco Packet Tracer (Part 4) | Router', 'https://youtu.be/FnH1XUQsoD8?list=PLBlnK6fEyqRgMoNJXQxrIIKRpSrVBuTlK', 'Computer Networks: \r\nBasics of Cisco Packet Tracer (Part 4)\r\n \r\nTopics discussed:\r\n1) Basics of router.\r\n2) Working of router.\r\n3) Inter-LAN communication using router.', '2025-05-10 16:53:13');
INSERT INTO `course_content` VALUES ('21', '10', '1', 'Visual Studio Code - Installation and Demo', 'https://youtu.be/mUoRE-PQoIE?list=PLqfPEK2RTgChhPaqS_Zxyvjs5jGJU3dZE', 'In this session we will look at Visual Studio Code Installation and Demo using an HTML file. We will install the live server extension for VS code.', '2025-05-10 17:03:29');
INSERT INTO `course_content` VALUES ('22', '10', '2', 'Introduction to HTML - Session  - Demo on iframe, video, audio, img , div, form and input tags', 'https://youtu.be/UxvrgBT_20o?list=PLqfPEK2RTgChhPaqS_Zxyvjs5jGJU3dZE', 'In this session we will look at the use of the following HTML tags \r\niframe\r\nvideo\r\naudio\r\nimg\r\ndiv\r\nform\r\ninput  (text, password, radio, checkbox, date, email, number , select, range, search, submit , reset)', '2025-05-10 17:03:29');
INSERT INTO `course_content` VALUES ('23', '10', '3', 'Cascading Style Sheets - selectors -font &text styling - padding - border - inherit - background', 'https://youtu.be/g5f5CfCEl48?list=PLqfPEK2RTgChhPaqS_Zxyvjs5jGJU3dZE', 'In this session we will look at the following concepts in Cascading Style Sheets (CSS) with sample code.\r\nselectors\r\nfont\r\ntext\r\npadding\r\nborder\r\ninherit \r\nbackground', '2025-05-10 17:03:29');
INSERT INTO `course_content` VALUES ('24', '10', '4', 'Introduction to JavaScript - I (variables, if else , switch , loops - for, while, do while - arrays)', 'https://youtu.be/RlnvXCKQuso?list=PLqfPEK2RTgChhPaqS_Zxyvjs5jGJU3dZE', 'In this session we will take a look at the following concepts \r\n1. use of var , let and const in JavaScript \r\n2. Data types (number, BigInt, String, undefined, null, object) \r\n3. if else and nested if else statement \r\n4. Switch case ( with Integer and String) \r\n4.  Arrays \r\n5. use of simple for loop \r\n6. for in loop \r\n7. for of loop \r\n8. while loop\r\n9 do while loop', '2025-05-10 17:03:29');
INSERT INTO `course_content` VALUES ('25', '15', '1', 'Calculus Basics - Introduction |', 'https://youtu.be/mRCXh__pexQ?list=PLmdFyQYShrjd4Qn42rcBeFvF6Qs-b6e-L', 'In the following videos, we are going to understand the basic concepts of Calculus - Limits, Differentiation, Integration. Calculus is the branch of mathematics that deals with change. Watch this video to know what does this means and the things that we are going to understand in this course', '2025-05-10 17:14:22');
INSERT INTO `course_content` VALUES ('26', '15', '2', 'What is Calculus - Lesson 2', 'https://youtu.be/RcavZVxE9Kk?list=PLmdFyQYShrjd4Qn42rcBeFvF6Qs-b6e-L', 'n Calculus, it\'s extremely important to understand the concept of Limits. With an interesting example, or a paradox we could say, this video explains how Limits help us understand values that cannot be determined using basic math. Watch this video to get an understanding of the concept of Limits.', '2025-05-10 17:14:22');
INSERT INTO `course_content` VALUES ('27', '15', '3', 'What is Calculus - Lesson 3', 'https://youtu.be/UKrrX47_kmg?list=PLmdFyQYShrjd4Qn42rcBeFvF6Qs-b6e-L', 'In this video, we will understand the idea of limits and differentiation. These concepts were used to find the instantaneous speed of an object. Differentiation could be understood as a process used to find the rate of change of any quantity at an instant. Watch this video to know what  Differentiation is exactly.', '2025-05-10 17:14:22');
INSERT INTO `course_content` VALUES ('28', '15', '4', 'What is Calculus - Lesson 4', 'https://youtu.be/odBiKFAdEXc?list=PLmdFyQYShrjd4Qn42rcBeFvF6Qs-b6e-L', 'In this video, we will understand the idea of integration. The basic idea of integration was known to people since ancient times, and it was called the method of exhaustion. But this method was not a general one that could be used for everything. The technique of integration is a general method used to find the area of curved shapes. Watch this video to know about the method of exhaustion and integratio', '2025-05-10 17:14:22');
INSERT INTO `course_content` VALUES ('29', '12', '1', 'What Is SDLC? | Introduction to Software Development Life Cycle | SDLC Life Cycl', 'https://youtu.be/5b36UTNRmtI', 'In this video on \'What Is SDLC?\', we will look into the multiple phases of software product development. The phases are designed to produce the most optimized result for the software\'s functioning, and each phase acts based on multiple pre-designed protocols and steps.', '2025-05-12 03:17:15');
INSERT INTO `course_content` VALUES ('30', '12', '2', 'Why Apply SDLC', 'https://youtu.be/5b36UTNRmtI', 'In this video on \'What Is SDLC?\', we will look into the multiple phases of software product development. The phases are designed to produce the most optimized result for the software\'s functioning, and each phase acts based on multiple pre-designed protocols and steps.', '2025-05-12 03:17:15');
INSERT INTO `course_content` VALUES ('31', '12', '3', 'Phases of SDLC', 'https://youtu.be/5b36UTNRmtI', 'In this video on \'What Is SDLC?\', we will look into the multiple phases of software product development. The phases are designed to produce the most optimized result for the software\'s functioning, and each phase acts based on multiple pre-designed protocols and steps.', '2025-05-12 03:17:15');
INSERT INTO `course_content` VALUES ('32', '12', '4', 'SDLC Models', 'https://youtu.be/5b36UTNRmtI', 'In this video on \'What Is SDLC?\', we will look into the multiple phases of software product development. The phases are designed to produce the most optimized result for the software\'s functioning, and each phase acts based on multiple pre-designed protocols and steps.', '2025-05-12 03:17:15');
INSERT INTO `course_content` VALUES ('33', '16', '1', 'Basics of business analysis', 'https://youtu.be/LktbfHdEm-U', 'In this course you will learn about basics of business analysis , how to become a business analsyt , skills needed for business analyst ,  business analysis techniques like swot analysis , pestle analysis , tools like JIRA , confluence , How to write user stories , estimation , story points ,  data flow diagrams . process flow diagrams , how to write test plan documents , how to test healthcare products , how to answer agile interview questions .', '2025-05-12 03:25:08');
INSERT INTO `course_content` VALUES ('34', '16', '2', 'Skills needed for business analyst', 'https://youtu.be/LktbfHdEm-U', 'In this course you will learn about basics of business analysis , how to become a business analsyt , skills needed for business analyst ,  business analysis techniques like swot analysis , pestle analysis , tools like JIRA , confluence , How to write user stories , estimation , story points ,  data flow diagrams . process flow diagrams , how to write test plan documents , how to test healthcare products , how to answer agile interview questions .', '2025-05-12 03:25:08');
INSERT INTO `course_content` VALUES ('35', '16', '3', 'Business analysis techniques like swot analysis , pestle analysis , tools like JIRA.', 'https://youtu.be/LktbfHdEm-U', 'In this course you will learn about basics of business analysis , how to become a business analsyt , skills needed for business analyst ,  business analysis techniques like swot analysis , pestle analysis , tools like JIRA , confluence , How to write user stories , estimation , story points ,  data flow diagrams . process flow diagrams , how to write test plan documents , how to test healthcare products , how to answer agile interview questions .', '2025-05-12 03:25:08');
INSERT INTO `course_content` VALUES ('36', '16', '4', 'Process flow diagrams and how to write test plan documents.', 'https://youtu.be/LktbfHdEm-U', 'In this course you will learn about basics of business analysis , how to become a business analsyt , skills needed for business analyst ,  business analysis techniques like swot analysis , pestle analysis , tools like JIRA , confluence , How to write user stories , estimation , story points ,  data flow diagrams . process flow diagrams , how to write test plan documents , how to test healthcare products , how to answer agile interview questions .', '2025-05-12 03:25:08');

INSERT INTO `courses` VALUES ('7', 'CompTIA Security+', 'CompTIA Security+ is a global certification that validates the baseline skills necessary to perform core security functions and pursue an IT security career.', '12', 'assets/uploads/CompTIA-SecPlus.png', '17', '1', '12', '3', '2025-05-05 17:33:08');
INSERT INTO `courses` VALUES ('8', 'CompTIA Network+', 'CompTIA Network+ validates the core skills necessary to establish, maintain, troubleshoot and secure networks in any environment, preparing you for a rewarding career in networking and cybersecurity.                                            ', '12', 'assets/uploads/CompTIA-NetPlus.png', '17', '1', '12', '2', '2025-05-05 17:35:54');
INSERT INTO `courses` VALUES ('9', 'CompTIA IT Fundamentals', 'CompTIA IT Fundamentals (ITF+) is an introduction to basic IT knowledge and skills that helps you determine whether you have what it takes to work in IT.                                            ', '12', 'assets/uploads/CompTIA-ITF.png', '18', '1', '12', '1', '2025-05-05 17:38:59');
INSERT INTO `courses` VALUES ('10', 'Internet programming', 'Internet programming, also known as web programming, involves writing code to create websites and web applications accessible over the internet. It uses programming languages and technologies to build web-based systems, services, and applications that can be accessed globally.', '4', 'assets/uploads/Internet Programming.jpg', '21', '6', '4', '1', '2025-05-06 03:26:48');
INSERT INTO `courses` VALUES ('11', 'Computer networking', 'Computer networking involves connecting devices to share data, resources, and information. These connections, whether wired or wireless, allow devices to communicate and exchange data using protocols like TCP/IP. Networks can range from simple home setups to vast global systems like the internet.', '4', 'assets/uploads/ComNet.jpg', '17', '10', '12', '2', '2025-05-06 03:28:52');
INSERT INTO `courses` VALUES ('12', 'Software Development Life Cycle', 'SDLC stands for Software Development Life Cycle. It\'s a structured process that guides software development from initial planning to deployment and maintenance, ensuring a systematic and efficient approach to creating high-quality software. The SDLC framework helps organizations manage risks, optimize resources, and deliver software that meets user needs and business goals', '3', 'assets/uploads/SDLC.jpg', '22', '7', '6', '1', '2025-05-06 03:30:43');
INSERT INTO `courses` VALUES ('13', 'Python for data science', 'Python is a popular choice for data science due to its versatility, readability, and extensive libraries for data analysis and machine learning. It\'s a free and open-source language, making it accessible for learning and use. Python\'s simplicity and ease of use also make it a good starting point for beginners in the field', '3', 'assets/uploads/PY4DataSci.jpg', '23', '9', '8', '0', '2025-05-06 03:33:57');
INSERT INTO `courses` VALUES ('14', 'Cisco Packet Tracer', 'Cisco Packet Tracer is a network simulation software developed by Cisco Systems. It allows users to visualize and experiment with network topologies, configurations, and protocols in a virtual environment. It\'s used for education, training, and hands-on practice in networking, IoT, and cybersecurity, providing a safe space to learn and troubleshoot network issues', '3', 'assets/uploads/CicsoPT.jpg', '18', '10', '12', '4', '2025-05-06 03:38:08');
INSERT INTO `courses` VALUES ('15', 'Calculus', 'Calculus is a foundational branch of mathematics for university students, particularly those in STEM fields. It deals with the study of continuous change, using concepts like limits, derivatives, and integrals. Calculus is crucial for understanding various scientific and engineering principles, and it\'s often a prerequisite for advanced courses in related disciplines.', '4', 'assets/uploads/Calculus.png', '20', '2', '12', '1', '2025-05-06 03:41:43');
INSERT INTO `courses` VALUES ('16', 'Business analysis', 'Business analysis is the process of examining and evaluating business needs to identify solutions that enable organizations to achieve their goals and manage change effectively. It involves understanding the current state, identifying gaps and problems, and recommending solutions that optimize business processes, systems, and policies', '3', 'assets/uploads/BA.jpg', '24', '4', '12', '0', '2025-05-06 03:43:58');

INSERT INTO `departments` VALUES ('1', 'Computer Science', 'Science Building, Room 301');
INSERT INTO `departments` VALUES ('2', 'Mathematics', 'Math Building, Room 201');
INSERT INTO `departments` VALUES ('3', 'Biology', 'Science Building, Room 101');
INSERT INTO `departments` VALUES ('4', 'Business', 'Business Building, Room 401');
INSERT INTO `departments` VALUES ('5', 'Psychology', 'Social Sciences Building, Room 201');
INSERT INTO `departments` VALUES ('6', 'Information Technology', 'Technology Building, Room 501');
INSERT INTO `departments` VALUES ('7', 'Software Engineering', 'Innovation Center, Room 300');
INSERT INTO `departments` VALUES ('8', 'Cybersecurity', 'Security Complex, Lab 4');
INSERT INTO `departments` VALUES ('9', 'Data Science', 'Analytics Hub, Room 210');
INSERT INTO `departments` VALUES ('10', 'Network Engineering', 'Infrastructure Wing, Lab 3');

INSERT INTO `enrollments` VALUES ('20', '1', '14', '2025-05-13 11:15:29', '[1, 2, 3, 4]', '28');

INSERT INTO `quiz_attempts` VALUES ('1', '20', '1', '16', '2025-05-13 11:25:54');

INSERT INTO `quiz_questions` VALUES ('1', '14', '1', 'What is the primary purpose of Cisco Packet Tracer?', ' Video editing', 'Network simulation and configuration', ' Database management', ' Graphic design', '2');
INSERT INTO `quiz_questions` VALUES ('2', '14', '2', ' In Lesson 1, what type of network is demonstrated as an example?', 'Client-server network', 'Peer-to-peer network', 'Cloud network', 'Mesh network', '2');
INSERT INTO `quiz_questions` VALUES ('3', '14', '3', 'Which device operates at the physical layer (Layer 1) and broadcasts data to all connected devices?', 'Switch', 'Router', 'Hub', 'Firewall', '3');
INSERT INTO `quiz_questions` VALUES ('4', '14', '4', 'What is a major disadvantage of using a hub in a LAN?', 'High cost', 'Creates a single collision domain', 'Requires complex configuration', 'Limited port availability', '2');
INSERT INTO `quiz_questions` VALUES ('5', '14', '5', 'How does a switch differ from a hub?', 'A switch operates at the network layer.', 'A switch forwards data only to the intended recipient.', 'A switch cannot connect multiple devices.', 'A switch uses wireless connections.', '2');
INSERT INTO `quiz_questions` VALUES ('6', '14', '6', 'At which layer of the OSI model does a switch operate?', 'Physical layer (Layer 1)', 'Data link layer (Layer 2)', 'Network layer (Layer 3)', 'Transport layer (Layer 4)', '2');
INSERT INTO `quiz_questions` VALUES ('7', '14', '7', 'What is the primary function of a router in a network?', 'Amplify electrical signals', 'Connect devices within the same LAN', 'Route data between different networks', 'Filter spam emails', '3');
INSERT INTO `quiz_questions` VALUES ('8', '14', '8', 'Which device is required for inter-LAN communication?', 'Hub', 'Switch', 'Router', 'Repeater', '3');

INSERT INTO `users` VALUES ('1', 'Kwanele', 'Sishi', 'kwanelesishi050509@gmail.com', '$2y$10$Sls0Oi5k6AF7jtlPi33a3ebTqcI7PBzSsX33a1pnEEI1Scig8VouG', 'student', '', '2025-05-04 13:00:28');
INSERT INTO `users` VALUES ('7', 'John', 'Doe', 'john.doe@student.ses.ac', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '1', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES ('10', 'Azanda', 'Nyide', 'azanda@gmail.com', '$2y$10$.fpcgCOMQBJpx4iTYc9zH.yDJUWcUPF2rfV0bE9l.WIqtpnq6bRVS', 'student', '5', '2025-05-04 16:06:21');
INSERT INTO `users` VALUES ('11', 'Admin', 'User', 'admin@ses.ac', '$2y$10$92KUNpkjQnOo5byMl.Yed.4Ea3Rd9llC/og4a2mT7d1E9oYzWbqO', 'admin', '', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES ('12', 'Sarah', 'Johnson', 's.johnson@ses.ac', '$2y$10$T4GUfGwrpqH.QQl8aNObzuCwl2zqRHsCq6QyUeb5xU7mW7fK4hF6W', 'instructor', '1', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES ('13', 'Michael', 'Chen', 'm.chen@ses.ac', '$2y$10$VpVvKXW.6r9d9tTJ4mZrE.8zq1DkOy7S7sS7s4w4jJz1wK4hF6W', 'instructor', '2', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES ('14', 'Emily', 'Wilson', 'e.wilson@ses.ac', '$2y$10$HjZ4V2X.9r9d9tTJ4mZrE.8zq1DkOy7S7sS7s4w4jJz1wK4hF6W', 'instructor', '3', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES ('15', 'David', 'Miller', 'd.miller@ses.ac', '$2y$10$LkM5N2X.6r9d9tTJ4mZrE.8zq1DkOy7S7sS7s4w4jJz1wK4hF6W', 'instructor', '4', '2025-05-04 13:21:22');
INSERT INTO `users` VALUES ('16', 'admin', 'admin', 'admin12345@gmail.com', '$2y$10$CLrB5HvFkLXiiFUYKett2u1/opMZpEPpOqn8OWWrgqxFW.miAdpCi', 'admin', '1', '2025-05-05 11:21:39');
INSERT INTO `users` VALUES ('17', 'Menzi', 'Dlamini', 'menzi@gmail.com', '$2y$10$J4F5WHEYbiK1w1LSF.zFFOzVzQqgZGcL/m4tYsixaWthyH1FA1sVW', 'instructor', '2', '2025-05-05 11:37:42');
INSERT INTO `users` VALUES ('18', 'Thokozani', 'Muthwa', 'muthwa@gmail.com', '$2y$10$dAB4TlF6TqclTmRmlJnsNuyhJYJjUzW3x9S0Vcup02IoeLqzvR3Uu', 'instructor', '2', '2025-05-05 11:39:44');
INSERT INTO `users` VALUES ('19', 'Sibonelo', 'Faye', 'faye@gmail.com', '$2y$10$XS7Pv485FUYBJ0ppmAuc9OLOtj1n1xrw2rhC6/5kl2KAyjwskXa5K', 'student', '1', '2025-05-07 12:40:12');
INSERT INTO `users` VALUES ('20', 'Amahle', 'Ngcobo', 'amahle@gmail.com', '$2y$10$D1bTc3DtGfzFMecQ3oy72OyMePERiSWSShrkORFtAuM5ZIkXSdv.y', 'instructor', '2', '2025-05-10 16:57:55');
INSERT INTO `users` VALUES ('21', 'Ramdel', 'Rajesh', 'ramdel@gmail.com', '$2y$10$KMaAuQPB5IDXqL0LuEFe..nS9S0YzjIEQYRgiHEMzsrwESTaZRGCe', 'instructor', '', '2025-05-10 17:00:14');
INSERT INTO `users` VALUES ('22', 'Khumbe', 'Mzobe', 'mzobe@gmail.com', '$2y$10$GIMyRBAMZ.DUGfBPP7T3auK5giYUV1Hh3KF.795ZonEJOppIR0y5G', 'instructor', '', '2025-05-12 02:53:22');
INSERT INTO `users` VALUES ('23', 'Anelisa', 'Jako', 'jako@gmail.com', '$2y$10$N9YSrgTTL6Aoh/sCVnAgIuRc3.gJpshb7MReUHsflERWzvxtmgmCW', 'instructor', '9', '2025-05-12 03:05:23');
INSERT INTO `users` VALUES ('24', 'Akhona', 'Dlamini', 'akhona@gmail.com', '$2y$10$wiqSxFHpOtJkYswS0GDc6eOF1/M/2gDExFU9TStj4HpxN2AzqiyQy', 'instructor', '4', '2025-05-12 03:06:47');

