-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2026 at 08:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `studentbase`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(500) NOT NULL,
  `files_url` varchar(500) NOT NULL,
  `due_date` date NOT NULL,
  `unit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `title`, `description`, `files_url`, `due_date`, `unit_id`) VALUES
(4, 'TCA - CT2 FALLACIES', '', '', '2026-01-30', 20),
(8, 'School Website', 'Create a school website', '', '2026-01-23', 16),
(9, 'Technical Document', 'Describe the process', '', '2026-01-30', 16),
(13, 'Finalise Website', 'Time to finalise the website', '', '2026-01-25', 16),
(15, 'Final Submission', 'Present your final assignment here', '', '2026-02-05', 16);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(250) NOT NULL,
  `course_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `group_name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `course_id`, `year`, `group_name`) VALUES
(6, '', 1, 1, 'A'),
(7, '', 5, 1, 'A'),
(8, '', 5, 1, 'B'),
(9, '', 3, 1, 'A'),
(10, '', 2, 2, 'A'),
(11, '', 1, 2, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `class_unit`
--

CREATE TABLE `class_unit` (
  `class_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(500) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `description` varchar(5000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `course_code`, `description`) VALUES
(1, 'Bachelor of Arts (Honours) in Interactive Digital Media', 'BsC-IDM', 'This degree programme prepares learners to work in various sectors within an everevolving digital industry by giving them exposure to creative design and software\r\ndevelopment techniques for interactive media content. Learners will receive a strong\r\ngrounding in graphic design principles and programming techniques for games,\r\nwebsite technologies and interactive installations. They will concurrently acquire applied\r\nknowledge in the fundamental practices of the industry by exploring user experience\r\ndesign, game design, and generative digital imaging techniques. In their final year of\r\nstudy, learners will be able to team up with students from other disciplines to create rich\r\ninteractive experiences'),
(2, 'Bachelor of Arts (Honours) in Journalism', 'BsC-JN', 'The journalism sphere offers exciting new career opportunities for individuals who are\r\nkeen to learn and to offer a much needed service to society. Journalism is a vocation\r\nthat can take learners to unimaginable places and lead them to meet extraordinary and\r\ndiverse people. Learners will be expected to effectively analyse and report the events\r\nthat shape our lives and the world around us. They will be guided to design, develop and\r\nproduce news content using the latest audio-visual technologies and recording devices.\r\nMoreover, learners will explore how to present their journalistic works to the public in a\r\nvariety of formats such as print, radio, television and online. Throughout this programme,\r\nlearners will work on real-life case scenarios, applying theoretical knowledge to practical\r\njournalism and related projects, working both in teams as well as autonomously.'),
(3, 'Bachelor of Arts (Honours) in Creative Media Production', 'BsC-CMP', 'This degree programme is intended to give learners the opportunity to work hands-on in creative\r\nenvironments and produce audio-visual content for both artistic and commercial practices. The\r\ncourse emphasizes content generation for the creative media and cultural industries. Learners will\r\nbe initially exposed to key technical, conceptual, and critical aspects of media production. They will\r\nlearn about the overarching principles and practices that underpin a professional productions. This\r\ndegree programme gives young professionals all the necessary tools and techniques to develop ideas\r\nfor screen-based content. Learners are then introduced to four main disciplines: Film; Documentary;\r\nTV and Digital Video, and then choose one discipline to specialise in it. This programme will give\r\nlearners the opportunity to produce a fully-fledged creative media production in one of the chosen\r\ndisciplines as a final major production'),
(5, 'Bachelor of Arts (Honours) in Animation', 'BsC-ANI', 'Animation is a powerful storytelling medium that is becoming more accessible to the\r\naverage person. It is a form of expression through which artists can create communicative\r\nand imaginative art for others to enjoy and appreciate. This degree introduces learners to\r\nthe tools and techniques required to produce animated work using traditional and digital\r\ntools. Learners will start by exploring basic animation theory and mechanics, develop\r\nobservational and drawing skills and study the fundamental principles of visual design,\r\nlayout, and storyboarding. They will gain experience in producing traditional artwork for\r\nstop-motion and frame-by-frame animation and gain a working knowledge of 2D and 3D\r\nanimation practices including cinematography, motion graphics, rigging, visual effects\r\nand sound design.');

-- --------------------------------------------------------

--
-- Table structure for table `course_unit`
--

CREATE TABLE `course_unit` (
  `course_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_unit`
--

INSERT INTO `course_unit` (`course_id`, `unit_id`) VALUES
(1, 16),
(1, 17),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 27),
(2, 25),
(5, 26);

-- --------------------------------------------------------

--
-- Table structure for table `deadlines`
--

CREATE TABLE `deadlines` (
  `deadline_id` int(11) NOT NULL,
  `due_date` datetime NOT NULL,
  `reminder_sent` tinyint(1) NOT NULL,
  `assignment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `content` varchar(250) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(250) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_at` datetime NOT NULL,
  `submission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`file_id`, `file_name`, `file_path`, `uploaded_at`, `submission_id`) VALUES
(3, 'Assignment 1.pdf', 'uploads/submissions/1769148589_sub0_Assignment_1.pdf', '0000-00-00 00:00:00', 0),
(4, 'Assignment 2.pdf', 'uploads/submissions/1769148589_sub0_Assignment_2.pdf', '0000-00-00 00:00:00', 0),
(5, '537.jpg', 'uploads/submissions/1769153877_sub2_537.jpg', '0000-00-00 00:00:00', 2),
(6, 'Assignment 1.pdf', 'uploads/submissions/1769154014_sub3_Assignment_1.pdf', '0000-00-00 00:00:00', 3),
(7, 'Assignment 2.pdf', 'uploads/submissions/1769154014_sub3_Assignment_2.pdf', '0000-00-00 00:00:00', 3),
(8, 'Assignment 1.pdf', 'uploads/submissions/1769193629_sub4_Assignment_1.pdf', '0000-00-00 00:00:00', 4);

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `grade` decimal(10,0) NOT NULL,
  `feedback` varchar(1000) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `grade`, `feedback`, `submission_id`, `lecturer_id`) VALUES
(1, 100, 'Great Job!', 3, 14),
(2, 96, 'Could have done better', 4, 14);

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `login_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime NOT NULL,
  `ip_address` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_history`
--

INSERT INTO `login_history` (`login_id`, `user_id`, `login_time`, `logout_time`, `ip_address`) VALUES
(1, 2, '2026-01-23 01:35:54', '0000-00-00 00:00:00', '::1'),
(2, 13, '2026-01-23 01:47:25', '0000-00-00 00:00:00', '::1'),
(3, 2, '2026-01-23 02:02:13', '2026-01-23 02:02:31', '::1'),
(4, 19, '2026-01-23 02:38:56', '2026-01-23 03:18:03', '::1'),
(5, 13, '2026-01-23 02:53:17', '0000-00-00 00:00:00', '::1'),
(6, 13, '2026-01-23 02:53:25', '0000-00-00 00:00:00', '::1'),
(7, 2, '2026-01-23 02:53:40', '2026-01-23 02:53:57', '::1'),
(8, 13, '2026-01-23 02:54:01', '0000-00-00 00:00:00', '::1'),
(9, 13, '2026-01-23 02:54:26', '2026-01-23 19:41:23', '::1'),
(10, 2, '2026-01-23 03:18:11', '2026-01-23 03:23:02', '::1'),
(11, 13, '2026-01-23 03:23:06', '2026-01-23 03:28:23', '::1'),
(12, 19, '2026-01-23 03:28:33', '0000-00-00 00:00:00', '::1'),
(13, 2, '2026-01-23 03:29:02', '0000-00-00 00:00:00', '::1'),
(14, 13, '2026-01-23 03:29:40', '0000-00-00 00:00:00', '::1'),
(15, 19, '2026-01-23 03:29:51', '2026-01-23 05:52:20', '::1'),
(16, 2, '2026-01-23 04:23:36', '0000-00-00 00:00:00', '::1'),
(17, 14, '2026-01-23 05:53:34', '2026-01-23 11:31:19', '::1'),
(18, 2, '2026-01-23 08:44:39', '0000-00-00 00:00:00', '::1'),
(19, 13, '2026-01-23 11:31:27', '2026-01-23 11:32:11', '::1'),
(20, 2, '2026-01-23 11:32:32', '2026-01-23 13:14:23', '::1'),
(21, 14, '2026-01-23 13:14:30', '2026-01-23 20:09:04', '::1'),
(22, 2, '2026-01-23 20:09:56', '2026-01-23 20:10:05', '::1'),
(23, 13, '2026-01-23 20:10:16', '2026-01-23 20:10:21', '::1'),
(24, 13, '2026-01-23 20:31:26', '0000-00-00 00:00:00', '::1'),
(25, 2, '2026-01-23 20:31:45', '2026-01-23 20:31:49', '::1'),
(26, 2, '2026-01-23 20:51:32', '0000-00-00 00:00:00', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `is_read` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 13, 'New assignment posted: Final Submission', 1, '2026-01-23 19:39:56'),
(2, 25, 'New assignment posted: Final Submission', 0, '2026-01-23 19:39:56'),
(4, 14, 'New submission received for assignment #15', 0, '2026-01-23 19:40:29'),
(5, 13, 'Your submission has been graded.', 0, '2026-01-23 19:41:02');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `reset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(500) NOT NULL,
  `expiry` datetime NOT NULL,
  `used` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`reset_id`, `user_id`, `token`, `expiry`, `used`) VALUES
(1, 13, 'd297d415e5c6d974d0a8dfd00b4a128bc3b4744c05e89e75783adec91329e170', '2026-01-23 21:01:59', 0),
(2, 13, '86d9834bf60c43b51a060197bf34e5ab359a37132838066f5993be7880f4b82d', '2026-01-23 21:03:23', 0),
(3, 13, 'c4c985a2272bae0cee0181d583c081341ab55ec064ba06a855401ace768d7094', '2026-01-23 21:12:46', 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Lecturer'),
(3, 'Student\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `assignment_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `file_url` varchar(500) NOT NULL,
  `submission_date` datetime NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`assignment_id`, `submission_id`, `file_url`, `submission_date`, `student_id`) VALUES
(4, 2, '', '0000-00-00 00:00:00', 13),
(8, 3, '', '0000-00-00 00:00:00', 13),
(15, 4, '', '0000-00-00 00:00:00', 13);

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `timetable_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `room` varchar(100) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `class_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`timetable_id`, `unit_id`, `lecturer_id`, `room`, `day_of_week`, `start_time`, `end_time`, `class_id`) VALUES
(3, 22, 23, 'ICA A112', 'Tuesday', '08:00:00', '11:30:00', 6),
(4, 20, 19, 'ICA A030', 'Tuesday', '12:00:00', '14:00:00', 6),
(5, 21, 22, 'ICA B004', 'Tuesday', '14:00:00', '15:30:00', 6),
(6, 23, 21, 'ICA A025', 'Wednesday', '08:00:00', '10:00:00', 6),
(7, 16, 14, 'ICA B010', 'Thursday', '08:00:00', '11:30:00', 6),
(8, 17, 15, 'ICA E001', 'Thursday', '12:00:00', '15:30:00', 6),
(9, 24, 20, 'ICA A113', 'Friday', '08:00:00', '11:30:00', 6);

-- --------------------------------------------------------

--
-- Table structure for table `timetable_students`
--

CREATE TABLE `timetable_students` (
  `student_id` int(11) NOT NULL,
  `ts_id` int(11) NOT NULL,
  `timetable_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `unit_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `unit_name`) VALUES
(16, 'Php and Databases'),
(17, 'Game Programming'),
(20, 'Critical Thinking'),
(21, 'Critical Studies and Research Methods'),
(22, 'Animation for Communication'),
(23, 'Prototyping and Testing Techniques (CORE UNIT)'),
(24, '3D Sculpting and Retopology'),
(25, 'Journalism'),
(26, 'Animation Principles'),
(27, 'Web Development');

-- --------------------------------------------------------

--
-- Table structure for table `unit_briefs`
--

CREATE TABLE `unit_briefs` (
  `unit_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unit_briefs`
--

INSERT INTO `unit_briefs` (`unit_id`, `lecturer_id`, `file_name`, `file_path`, `uploaded_at`) VALUES
(16, 14, 'PHP & Databases - INT5.2.pdf', 'uploads/unit-briefs/1769146049_unit16_PHP___Databases_-_INT5.2.pdf', '2026-01-23 06:27:29'),
(27, 14, 'Web Development - 5.1.pdf', 'uploads/unit-briefs/1769163378_unit27_Web_Development_-_5.1.pdf', '2026-01-23 11:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `unit_lecturers`
--

CREATE TABLE `unit_lecturers` (
  `unit_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unit_lecturers`
--

INSERT INTO `unit_lecturers` (`unit_id`, `lecturer_id`) VALUES
(16, 14),
(17, 15),
(20, 19),
(21, 22),
(22, 23),
(23, 15),
(23, 21),
(24, 20),
(25, 27),
(26, 23),
(27, 14);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `class_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(500) NOT NULL,
  `password` varchar(250) NOT NULL,
  `role_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`class_id`, `user_id`, `name`, `email`, `password`, `role_id`, `course_id`, `deleted`) VALUES
(NULL, 2, 'Yan Cesare', 'admin@studentbase.edu.mt', '$2y$10$v466ynfnkjbUqmHBbr5ubOoNGnf9UaLx18Zo9hHF0CetgwjwM5bjS', 1, NULL, 0),
(11, 13, 'Yan Cesare', 'cesarey2004@gmail.com', '$2y$10$LvMe2zHH022AvoYfrkhysuRxzrlrOlLBI3aW08HALdd.qjadQzeay', 3, 1, 0),
(NULL, 14, 'Matthew Cumbo', 'matthewcumbo@studentbase.edu.mt', '$2y$10$ZOcd2NQKVjuW/OrPKnD/yeyyESLLrDVQK7Kt2NPB7C8UHQCUupsEq', 2, NULL, 0),
(NULL, 15, 'Redd Caruana', 'reddcaruana@studentbase.edu.mt', '$2y$10$5FwI8GcXVm6hJHdcBLN1T.P6/y86E/lHZtJNhOe4OTUfcSQde00I6', 2, NULL, 0),
(7, 16, 'John Micallef', 'john@studentbase.edu.mt', '$2y$10$upew4cpK/Wy9Ip53aZPKb.eHg9JYgXoyyCYKhSQgW4hWtg37sQo86', 3, 5, 0),
(NULL, 17, 'Dominic Tringali', 'dominictringali@studentbase.edu.mt', '$2y$10$cofmw45vVWv8yldh4pgmfe7zLKI6jQde1.OAnjrttFN0lAMbWxekW', 2, NULL, 0),
(NULL, 19, 'Reuben Zammit', 'reubenzammit@studentbase.edu.mt', '$2y$10$yToVFGU5vRT2u5SFjfxP2u/mUnI.TwAlyUX8v52DvtdsDmmk.e8lm', 2, NULL, 0),
(NULL, 20, 'Adam Cassar ', 'adamcassar@studentbase.edu.mt', '$2y$10$M5.0v0lCkqmGQqLLb/afqe.sKaGlJ0mtdHm4bVlBNcxARm6lrhRTe', 2, NULL, 0),
(NULL, 21, 'Josef Florian Micallef', 'JFM@studentbase.edu.mt', '$2y$10$evJlla6SoeCtkw472OFWdeAOFQhyKeiBzRLyfTVnaIpaRNHxU4yPy', 2, NULL, 0),
(NULL, 22, 'Adolf Formosa', 'adolfformosa@studentbase.edu.mt', '$2y$10$Nf.Ukyh8wshJO4MQ3/TmvuWX/KHkpL0yOgX0s.GkQD7Jy3t/sNgjG', 2, NULL, 0),
(NULL, 23, 'Francantonio Cuschieri', 'franky@studentbase.edu.mt', '$2y$10$BshpsRUilJSxvfBJsqjTSeE76G6SEOv0N.QEkzgaFoOdJQ..EpLeK', 2, NULL, 0),
(NULL, 24, 'test', 'test@test.test', '$2y$10$PHaadeGm2K8WkM8/owWWj.duGff2begZ7tUjOPPwNimyxoXsJJ47C', 2, NULL, 1),
(11, 25, 'Nirvana Vella', 'nirvanavella@studentbase.edu.mt', '$2y$10$CEiVLAZYGJFyQ2GsRcF8f.NjmOzCLdXBBD1Y/ww29mOCQczsTvrUm', 3, 1, 0),
(10, 26, 'Elena Festari', 'elenafestari@studentbase.edu.mt', '$2y$10$o3oMlemyr7BMmDcipNMMTe4Vj8XKA.TOHW6hc6x6pkHl/yScEE0OG', 3, 2, 0),
(NULL, 27, 'Christopher Francalanza', 'Frankalanza@studentbase.edu.mt', '$2y$10$UgxDUVkmEKi9y6GrOQd5T.9lvvvdqPNDbda67KzD2.8svEVn3NsW2', 2, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_class`
--

CREATE TABLE `user_class` (
  `unit_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `fk_assignments_unit` (`unit_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `fk_classes_course` (`course_id`);

--
-- Indexes for table `class_unit`
--
ALTER TABLE `class_unit`
  ADD PRIMARY KEY (`class_id`,`unit_id`),
  ADD KEY `fk_class_unit_unit` (`unit_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `course_unit`
--
ALTER TABLE `course_unit`
  ADD PRIMARY KEY (`course_id`,`unit_id`),
  ADD UNIQUE KEY `uq_course_unit` (`course_id`,`unit_id`),
  ADD KEY `fk_course_unit_unit` (`unit_id`);

--
-- Indexes for table `deadlines`
--
ALTER TABLE `deadlines`
  ADD PRIMARY KEY (`deadline_id`),
  ADD KEY `fk_deadlines_assignment` (`assignment_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `fk_feedback_submission` (`submission_id`),
  ADD KEY `fk_feedback_lecturer` (`lecturer_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `fk_files_submission` (`submission_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `fk_grades_submission` (`submission_id`),
  ADD KEY `fk_grades_lecturer` (`lecturer_id`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`login_id`),
  ADD KEY `fk_login_user` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notifications_user` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `fk_reset_user` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `fk_submissions_assignment` (`assignment_id`),
  ADD KEY `fk_submissions_student` (`student_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`timetable_id`),
  ADD KEY `fk_timetable_class` (`class_id`),
  ADD KEY `fk_timetable_lecturer` (`lecturer_id`),
  ADD KEY `fk_timetable_unit` (`unit_id`);

--
-- Indexes for table `timetable_students`
--
ALTER TABLE `timetable_students`
  ADD PRIMARY KEY (`ts_id`),
  ADD KEY `fk_ts_timetable` (`timetable_id`),
  ADD KEY `fk_ts_student` (`student_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`),
  ADD UNIQUE KEY `uq_unit_name` (`unit_name`) USING HASH;

--
-- Indexes for table `unit_briefs`
--
ALTER TABLE `unit_briefs`
  ADD PRIMARY KEY (`unit_id`),
  ADD KEY `fk_brief_lecturer` (`lecturer_id`);

--
-- Indexes for table `unit_lecturers`
--
ALTER TABLE `unit_lecturers`
  ADD PRIMARY KEY (`unit_id`,`lecturer_id`),
  ADD UNIQUE KEY `uq_unit_lecturers` (`unit_id`,`lecturer_id`),
  ADD KEY `fk_unit_lecturers_lecturer` (`lecturer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `fk_users_roles` (`role_id`),
  ADD KEY `fk_users_class` (`class_id`),
  ADD KEY `fk_users_course` (`course_id`);

--
-- Indexes for table `user_class`
--
ALTER TABLE `user_class`
  ADD PRIMARY KEY (`class_id`,`unit_id`),
  ADD KEY `fk_user_class_unit` (`unit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `deadlines`
--
ALTER TABLE `deadlines`
  MODIFY `deadline_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `timetable_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `timetable_students`
--
ALTER TABLE `timetable_students`
  MODIFY `ts_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `fk_assignments_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_classes_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON UPDATE CASCADE;

--
-- Constraints for table `class_unit`
--
ALTER TABLE `class_unit`
  ADD CONSTRAINT `fk_class_unit_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  ADD CONSTRAINT `fk_class_unit_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`);

--
-- Constraints for table `course_unit`
--
ALTER TABLE `course_unit`
  ADD CONSTRAINT `fk_course_unit_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_course_unit_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `deadlines`
--
ALTER TABLE `deadlines`
  ADD CONSTRAINT `fk_deadlines_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_feedback_submission` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `fk_files_submission` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `fk_grades_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_grades_submission` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `fk_login_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_submissions_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_submissions_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `fk_timetable_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_timetable_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_timetable_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `timetable_students`
--
ALTER TABLE `timetable_students`
  ADD CONSTRAINT `fk_ts_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_ts_timetable` FOREIGN KEY (`timetable_id`) REFERENCES `timetable` (`timetable_id`) ON DELETE CASCADE;

--
-- Constraints for table `unit_briefs`
--
ALTER TABLE `unit_briefs`
  ADD CONSTRAINT `fk_brief_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_brief_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Constraints for table `unit_lecturers`
--
ALTER TABLE `unit_lecturers`
  ADD CONSTRAINT `fk_unit_lecturers_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_unit_lecturers_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  ADD CONSTRAINT `fk_users_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `user_class`
--
ALTER TABLE `user_class`
  ADD CONSTRAINT `fk_user_class_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_class_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
