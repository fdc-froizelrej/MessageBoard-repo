-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Sep 30, 2024 at 08:17 AM
-- Server version: 8.0.39
-- PHP Version: 8.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `messageboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `conversation`
--

CREATE TABLE `conversation` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `conversation`
--

INSERT INTO `conversation` (`id`, `sender_id`, `receiver_id`) VALUES
(76, 5, 13),
(85, 12, 21),
(87, 5, 12),
(88, 5, 18);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int NOT NULL,
  `conversation_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created_ip` varchar(20) DEFAULT NULL,
  `modified_ip` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `conversation_id`, `user_id`, `content`, `created`, `modified`, `created_ip`, `modified_ip`) VALUES
(191, 69, 5, 'Hello, Killua!', '2024-09-26 16:46:28', '2024-09-26 16:46:28', '192.168.65.1', '192.168.65.1'),
(192, 70, 5, 'test', '2024-09-26 16:46:44', '2024-09-26 16:46:44', '192.168.65.1', '192.168.65.1'),
(284, 85, 12, 'hellooo', '2024-09-27 11:14:13', '2024-09-27 11:14:13', '192.168.65.1', '192.168.65.1'),
(327, 76, 5, 'sdfsdfsdfdsfsfdsqqwweqwe', '2024-09-30 09:25:15', '2024-09-30 09:25:15', '192.168.65.1', '192.168.65.1'),
(330, 76, 5, 'hello', '2024-09-30 09:39:41', '2024-09-30 09:39:41', '192.168.65.1', '192.168.65.1'),
(331, 76, 5, 'dfsdfdsfds', '2024-09-30 09:40:01', '2024-09-30 09:40:01', '192.168.65.1', '192.168.65.1'),
(332, 76, 5, 'hello he hello', '2024-09-30 09:40:05', '2024-09-30 09:40:05', '192.168.65.1', '192.168.65.1'),
(333, 76, 13, 'hello gon', '2024-09-30 09:54:49', '2024-09-30 09:54:49', '192.168.65.1', '192.168.65.1'),
(334, 76, 13, 'test', '2024-09-30 09:55:49', '2024-09-30 09:55:49', '192.168.65.1', '192.168.65.1'),
(337, 76, 5, 'fsdfds', '2024-09-30 10:04:49', '2024-09-30 10:04:49', '192.168.65.1', '192.168.65.1'),
(338, 87, 5, 'Hello, Kurapika! How are you?', '2024-09-30 11:49:08', '2024-09-30 11:49:08', '192.168.65.1', '192.168.65.1'),
(339, 87, 12, 'Hello, Gon. I\'m doing good. How about you?', '2024-09-30 11:49:35', '2024-09-30 11:49:35', '192.168.65.1', '192.168.65.1'),
(340, 87, 5, 'I\'m doing good too. Me and Killua are in Yorknew City now. Where are you?', '2024-09-30 11:50:29', '2024-09-30 11:50:29', '192.168.65.1', '192.168.65.1'),
(341, 87, 12, 'I\'m here in the city too. Currently working as a bodyguard. What are you two doing at the moment?', '2024-09-30 11:50:58', '2024-09-30 11:50:58', '192.168.65.1', '192.168.65.1'),
(342, 87, 5, 'Looking for ways to earn money hahaha we\'re letting people bet for arm wrestling.', '2024-09-30 11:51:19', '2024-09-30 11:51:19', '192.168.65.1', '192.168.65.1'),
(352, 88, 5, 'Hello, Hisoka', '2024-09-30 13:10:09', '2024-09-30 13:10:09', '192.168.65.1', '192.168.65.1');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `profile_picture` text,
  `birthday` date DEFAULT NULL,
  `gender` text,
  `hobby` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `joined_date` datetime DEFAULT NULL,
  `last_logged_in` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `profile_picture`, `birthday`, `gender`, `hobby`, `joined_date`, `last_logged_in`) VALUES
(5, 'Gon Freecss', 'gon@email.com', '7a725ed6f96e6d3bf641ad000203156a36a60fd6', 'uploads/profile_pictures/gonnnnnn.gif', '1997-05-12', 'Male', 'As I venture through the lush forests and towering mountains, my heart races with excitement at the thought of discovering new wonders. There\'s something truly exhilarating about exploring nature—the vibrant colors of the flowers, the sound of leaves rustling in the wind, and the thrill of spotting a rare creature. I often set out on long hikes, feeling the earth beneath my feet and the fresh air filling my lungs. Every new path I take reveals something unexpected, whether it\'s a hidden waterfall or a breathtaking view from a cliff\'s edge. I love the feeling of freedom that comes with being outside, away from the constraints of civilization, where I can immerse myself in the beauty of the world. Each adventure fuels my curiosity and reminds me of the countless mysteries waiting to be uncovered. Whether I\'m climbing a tree to get a better look at the scenery or sitting quietly to observe the animals around me, these moments in nature bring me pure joy and a deeper understanding of what it means to be alive.', '2024-09-20 09:00:00', '2024-09-30 11:45:09'),
(7, 'Killua Zoldyck', 'killua@email.com', '7a725ed6f96e6d3bf641ad000203156a36a60fd6', 'uploads/profile_pictures/killua.gif', '1997-07-07', 'Male', 'I love the thrill of learning new skills, especially when it comes to honing my lightning-fast speed and agility. Whether it’s mastering new techniques or testing myself against formidable opponents, every experience helps me grow stronger. I also enjoy spending time with my friends, especially when we’re on missions together, because their camaraderie makes every challenge worth facing. Ultimately, my goal is to find out what it truly means to be free and to live life on my own terms, far from the shadows of my family.', '2024-09-20 09:00:00', '2024-09-30 09:01:15'),
(12, 'Kurapika Kurta', 'kurapika@email.com', '7a725ed6f96e6d3bf641ad000203156a36a60fd6', 'uploads/profile_pictures/kurapikaaa.gif', '1987-04-04', 'Male', 'I find solace in quiet moments of study, particularly in learning and mastering Nen techniques, which I see not just as a form of power, but as a key to achieving justice. Reading and researching history, especially anything related to my clan or ancient knowledge, keeps my mind sharp and focused. I also practice meditation to maintain control over my emotions, especially my anger. While my life is consumed by my duty, I do appreciate moments of solitude in nature, where I can reflect and regain my inner strength.', '2024-09-20 09:00:00', '2024-09-30 11:49:21'),
(13, 'Leorio Paradinight', 'leorio@email.com', '7a725ed6f96e6d3bf641ad000203156a36a60fd6', 'uploads/profile_pictures/leorio.gif', '1990-03-03', 'Male', 'First and foremost, I’m passionate about medicine and the desire to become a doctor, which drives me to constantly learn and improve my skills. I also enjoy playing video games, often using them as a way to unwind and connect with my friends. Additionally, I have a keen interest in shopping, particularly when it comes to fashion, as I love to keep up with the latest trends. Hanging out with my friends, especially Gon and Killua, is another favorite pastime; I value our camaraderie and the adventures we share. Lastly, I have a competitive spirit, so I enjoy participating in various competitions, whether it\'s in sports or other challenges. These hobbies keep me motivated and grounded as I pursue my dreams!', '2024-09-20 09:00:00', '2024-09-30 11:22:10'),
(18, 'Hisoka Morow', 'hisoka@email.com', '7a725ed6f96e6d3bf641ad000203156a36a60fd6', 'uploads/profile_pictures/hisoka.gif', '1977-06-06', 'Male', 'I have a fascination with playing card games, particularly those that involve strategy and deception, allowing me to showcase my skills and entertain myself. Engaging in battles with strong opponents is another passion of mine; the thrill of combat and the challenge it presents excite me immensely. I also enjoy collecting unique and interesting items, especially those that relate to my encounters with others, as each piece tells a story of a challenge or thrill. Additionally, I have a penchant for performing magic tricks, delighting in the reactions they evoke. Ultimately, I’m always on the lookout for formidable adversaries, as the anticipation of a challenging fight keeps me engaged and ensures that life remains exciting!', '2024-09-23 10:45:56', '2024-09-25 16:27:42'),
(20, 'Illumi Zoldyck', 'illumi@email.com', '7a725ed6f96e6d3bf641ad000203156a36a60fd6', NULL, NULL, NULL, NULL, '2024-09-26 09:10:14', '2024-09-27 11:09:34'),
(21, 'Chrollo Lucilfer', 'chrollo@email.com', '7a725ed6f96e6d3bf641ad000203156a36a60fd6', 'uploads/profile_pictures/chrollo.gif', '1987-11-14', 'Male', 'I find solace in reading, particularly immersing myself in diverse texts that explore the depths of human nature and the intricacies of power dynamics. Collecting rare and valuable artifacts is another passion of mine, as each piece holds a story that adds to my understanding of the world. Additionally, I enjoy engaging in strategic games that challenge my intellect, allowing me to hone my skills in foresight and manipulation. In quieter moments, I appreciate the beauty of the night sky, finding inspiration in its vastness and the mysteries it holds. Ultimately, my hobbies serve as a means to sharpen my mind, understand the complexities of others, and enhance my ever-evolving plans.', '2024-09-26 09:15:15', '2024-09-27 11:07:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_conversation`
--

CREATE TABLE `user_conversation` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `conversation_id` int NOT NULL,
  `is_deleted` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_conversation`
--

INSERT INTO `user_conversation` (`id`, `user_id`, `conversation_id`, `is_deleted`) VALUES
(71, 5, 76, 0),
(72, 13, 76, 0),
(89, 12, 85, 0),
(90, 21, 85, 0),
(93, 5, 87, 0),
(94, 12, 87, 0),
(95, 5, 88, 0),
(96, 18, 88, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conversation`
--
ALTER TABLE `conversation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_conversation`
--
ALTER TABLE `user_conversation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conversation`
--
ALTER TABLE `conversation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=364;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_conversation`
--
ALTER TABLE `user_conversation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
