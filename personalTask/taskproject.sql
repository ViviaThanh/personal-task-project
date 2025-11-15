-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2025 at 06:43 AM
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
-- Database: `taskproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(20) DEFAULT '#7b5dff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `name`, `description`, `color`, `created_at`) VALUES
(1, 1, 'Personal Task', 'Các công việc cá nhân', '#7b5dff', '2025-11-14 14:41:12'),
(2, 1, 'Office Task', 'Công việc tại văn phòng', '#7b5dff', '2025-11-14 14:41:12'),
(3, 1, 'Daily Study', 'Học tập hằng ngày', '#7b5dff', '2025-11-14 14:41:12'),
(4, 1, 'Quan Thanh', '', '#7b5dff', '2025-11-14 22:52:24'),
(5, 3, 'Office Task', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-14 23:43:46'),
(6, 3, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-14 23:43:46'),
(7, 3, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-14 23:43:46'),
(8, 4, 'Office Task', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-14 23:43:46'),
(9, 4, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-14 23:43:46'),
(10, 4, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-14 23:43:46'),
(11, 1, 'Office Task', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-14 23:43:46'),
(12, 1, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-14 23:43:46'),
(13, 1, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-14 23:43:46'),
(14, 2, 'Office Task', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-14 23:43:46'),
(15, 2, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-14 23:43:46'),
(16, 2, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-14 23:43:46'),
(17, 5, 'Office Task', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-14 23:43:46'),
(18, 5, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-14 23:43:46'),
(19, 5, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-14 23:43:46'),
(21, 7, 'Office Task', 'Các dự án công việc tại văn phòng.', '#7b5dff', '2025-11-15 05:20:40'),
(22, 7, 'Personal Task', 'Các dự án và kế hoạch cá nhân.', '#7b5dff', '2025-11-15 05:20:40'),
(23, 7, 'Daily Study', 'Ghi chú học tập hàng ngày.', '#7b5dff', '2025-11-15 05:20:40'),
(24, 8, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-15 05:26:19'),
(25, 8, 'Office', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-15 05:26:19'),
(26, 8, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-15 05:26:19');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `project_id`, `title`, `description`, `due_date`, `status`, `created_at`) VALUES
(2, 1, NULL, 'ối dồi ôi', 'làm cực vip', '2025-11-16 00:00:00', 'completed', '2025-11-13 13:57:39'),
(3, 1, NULL, 'góa đã', 'hêhe', '2025-11-14 00:00:00', 'pending', '2025-11-13 14:01:10'),
(4, 4, NULL, 'web asp', 'khó', '2025-11-17 00:00:00', 'in_progress', '2025-11-14 14:30:48'),
(5, 1, 1, 'đồ án web', 'vip', '2025-11-17 00:00:00', 'pending', '2025-11-14 22:30:37'),
(6, 1, 1, 'đồ án web', 'vip', '2025-11-17 00:00:00', 'pending', '2025-11-14 22:31:13'),
(7, 1, 2, 'office 1', 'hêhe', '2025-11-14 00:00:00', 'pending', '2025-11-14 22:33:09'),
(8, 1, 3, 'Speaking', 'important', '2025-11-29 00:00:00', 'pending', '2025-11-14 22:33:58'),
(9, 1, 3, 'writing', 'hihi', '2025-11-21 00:00:00', 'pending', '2025-11-14 22:34:14'),
(10, 1, 4, 'góa đã', 'vip', '2025-11-16 00:00:00', 'pending', '2025-11-14 23:06:28'),
(11, 5, 19, 'web exercise', 'khó', '2025-11-17 00:00:00', 'completed', '2025-11-15 00:36:17'),
(13, 5, 17, 'làm đồ án', 'cũng vip', '2025-12-01 00:00:00', 'in_progress', '2025-11-15 02:28:32'),
(14, 5, 17, 'thực tập', 'hmmmmmmm', '2025-11-24 00:00:00', 'pending', '2025-11-15 02:28:50'),
(15, 5, 18, 'ngủ', 'khò khò', '2025-11-18 00:00:00', 'completed', '2025-11-15 02:29:07'),
(16, 5, 19, 'học tiếng trung', 'cũng cũng', '2025-11-20 00:00:00', 'pending', '2025-11-15 02:29:23'),
(18, 5, 18, 'Rửa mặt', 'hehe', '2025-11-15 00:00:00', 'completed', '2025-11-15 03:15:02'),
(19, 5, 17, 'PHP exercise', 'cũng cũng', '2025-11-15 00:00:00', 'completed', '2025-11-15 03:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `avatar`, `created_at`) VALUES
(1, 'thanhtp', '$2y$10$Jh76tyMroibf7ms/0K7oEOYbhbZHZ4hzomZTPfSU46oT1zLC9Y2.2', 'phamthanhtuanvp@gmail.com', NULL, '2025-11-13 10:08:05'),
(2, 'tuan1329', '$2y$10$WVAqLRxkfSeVjDVT6QLrVe2W/jV0z7hrJFoN/rQqRWBMJOtp9eMlq', 'phamthanhtuanvp123@gmail.com', NULL, '2025-11-13 16:28:22'),
(3, 'hanh', '$2y$10$aFf01v2zO.WmoEYhwV/23e/dDI6TwmCpy/gpZueeX2VUgsLDCIm.K', 'hanh@gmail.com', NULL, '2025-11-14 01:08:51'),
(4, 'thanh', '$2y$10$AdABWlSnQQyjEVeucn0Q0u./XOvurlulAHN7Sgaqu1LQvmN8n9cPO', 'phamthanhtuanvp4@gmail.com', NULL, '2025-11-14 02:52:20'),
(5, 'tuanthanh', '$2y$10$0PirBblDkJLtB63n8C185u/LFGuTf/SU4.6qfqFBk4hrAkffsL0Ey', 'thanhtp1329@gmail.com', 'uploads/avatars/user_5_6918092196171.jpg', '2025-11-14 23:37:06'),
(6, 'duc', '$2y$10$UX6enYYC/DHR4IhZxxZqM.e17eDi6hWzC6llByiGsupxOesabPlra', 'duucit@gmail.com', NULL, '2025-11-15 05:08:56'),
(7, 'aduc', '$2y$10$GKtwg/mYavl0oGGevQUeC.QfZCAgKPNRdiYxSBSOMODI68ozSaRuS', 'aduc@gmail.com', NULL, '2025-11-15 05:20:40'),
(8, 'ktrung', '$2y$10$dfpvN5QwOkGH91Nw7bWz6ugJFSxQFGZlZWnMO2jpM48qdyDfo6Fke', 'ktrung@gmail.com', 'uploads/avatars/user_8_69180f5fd34d9.jpg', '2025-11-15 05:26:19');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    INSERT INTO projects (user_id, name, description)
    VALUES 
        (NEW.id, 'Personal Task', 'Các dự án và kế hoạch cá nhân'),
        (NEW.id, 'Office', 'Các dự án công việc tại văn phòng'),
        (NEW.id, 'Daily Study', 'Ghi chú học tập hàng ngày');
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_projects_user` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_task_project` (`project_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_task_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
