-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 10:55 AM
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
-- Database: `taara_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `activity_id` int(25) NOT NULL,
  `animal_id` int(25) NOT NULL,
  `description` varchar(500) NOT NULL,
  `date_recorded` date NOT NULL,
  `time_recorded` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adoption`
--

CREATE TABLE `adoption` (
  `adoption_id` int(25) NOT NULL,
  `user_id` int(25) NOT NULL,
  `animal_id` int(25) NOT NULL,
  `date_adopted` date NOT NULL,
  `full_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adoption`
--

INSERT INTO `adoption` (`adoption_id`, `user_id`, `animal_id`, `date_adopted`, `full_name`) VALUES
(1, 1, 7, '0000-00-00', ''),
(2, 1, 7, '0000-00-00', ''),
(3, 1, 7, '0000-00-00', ''),
(4, 1, 7, '2025-10-05', ''),
(5, 1, 7, '2025-10-05', ''),
(6, 1, 7, '2025-10-05', '');

-- --------------------------------------------------------

--
-- Table structure for table `adoption_application`
--

CREATE TABLE `adoption_application` (
  `a_application_id` int(25) NOT NULL,
  `user_id` int(25) NOT NULL,
  `animal_id` int(25) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(1000) NOT NULL,
  `classification` varchar(50) NOT NULL,
  `comp_name` varchar(600) NOT NULL,
  `id_img` varchar(900) NOT NULL,
  `date_applied` date NOT NULL,
  `status` varchar(25) NOT NULL,
  `date_responded` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adoption_application`
--

INSERT INTO `adoption_application` (`a_application_id`, `user_id`, `animal_id`, `full_name`, `address`, `classification`, `comp_name`, `id_img`, `date_applied`, `status`, `date_responded`) VALUES
(26, 1, 7, 'Carl Daniel Talento Buenconsejo', 'Sipocot, Camarines Sur', 'employed', 'CBSUA Sipocot', '', '2025-10-03', 'Accepted', '2025-10-05'),
(27, 1, 7, 'Carl Daniel Talento Buenconsejo', 'Sipocot, Camarines Sur', 'employed', 'CBSUA Sipocot', '', '2025-10-03', 'Accepted', '2025-10-05'),
(28, 1, 7, 'Carl Daniel Talento Buenconsejo', 'Sipocot, Camarines Sur', 'employed', 'CBSUA Sipocot', 'Assets/UserGenerated/cat3.jpg', '2025-10-03', 'Accepted', '2025-10-05'),
(29, 1, 7, 'Carl Daniel Talento Buenconsejo', 'Sipocot, Camarines Sur', 'employed', 'CBSUA Sipocot', 'Assets/UserGenerated/cat3.jpg', '2025-10-03', 'Accepted', '2025-10-05'),
(30, 0, 0, '', '', '', '', '', '0000-00-00', '', '2025-10-05'),
(31, 1, 2, 'Carl Daniel Talento Buenconsejo', 'Sipocot, Camarines Sur', 'employed', 'CBSUA Sipocot', 'Assets/UserGenerated/cat1.webp', '2025-10-03', 'Rejected', '2025-10-05'),
(32, 1, 2, 'Carl Daniel Talento Buenconsejo', 'Sipocot, Camarines Sur', 'employed', 'CBSUA Sipocot', 'Assets/UserGenerated/cat1.webp', '2025-10-04', 'pending', '2025-10-05'),
(33, 2, 15, 'Carl Daniel Talento Buenconsejo', 'Sipocot, Camarines Sur', 'employed', 'CBSUA Sipocot', 'Assets/UserGenerated/frontpic-removebg-preview.png', '2025-10-04', 'pending', '2025-10-05'),
(34, 1, 11, 'Carl Daniel Talento Buenconsejo', 'Sipocot, Camarines Sur', 'unemployed', 'CBSUA Sipocot', 'Assets/UserGenerated/1759535929_cat3.jpg', '2025-10-06', 'pending', '0000-00-00'),
(35, 1, 12, 'Juan dela Cruz', 'Tabaco, City', 'student', 'One Hardware', 'Assets/UserGenerated/1759441859_cat3.jpg', '2025-10-06', 'pending', '0000-00-00'),
(36, 1, 12, 'Juan dela Cruz', 'Tabaco, City', 'student', 'One Hardware', 'Assets/UserGenerated/1759441859_cat3.jpg', '2025-10-06', 'pending', '0000-00-00'),
(37, 1, 12, 'Juan dela Cruz', 'Tabaco, City', 'student', 'One Hardware', 'Assets/UserGenerated/1759441859_cat3.jpg', '2025-10-06', 'pending', '0000-00-00'),
(38, 1, 12, 'Juan dela Cruz', 'Tabaco, City', 'student', 'One Hardware', 'Assets/UserGenerated/1759441859_cat3.jpg', '2025-10-06', 'pending', '0000-00-00'),
(39, 7, 11, 'Juan dela Cruz', 'Tabaco, City', 'employed', 'One Hardware', 'Assets/UserGenerated/home_banner.jpg', '2025-10-07', 'pending', '0000-00-00'),
(40, 2, 2, 'Denaun Stain', 'Quezon City', 'employed', 'Jayden Electronics', 'Assets/UserGenerated/frontpic-removebg-preview.png', '2025-11-26', 'pending', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `adoption_screening`
--

CREATE TABLE `adoption_screening` (
  `screening_id` int(25) NOT NULL,
  `a_application_id` int(25) NOT NULL,
  `housing` varchar(255) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `own_pets` varchar(255) NOT NULL,
  `time_dedicated` varchar(255) NOT NULL,
  `children_info` varchar(255) NOT NULL,
  `financial_ready` varchar(255) NOT NULL,
  `breed_interest` varchar(255) NOT NULL,
  `allergy_info` varchar(255) NOT NULL,
  `alone_time_plan` varchar(255) NOT NULL,
  `researched_breed` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adoption_screening`
--

INSERT INTO `adoption_screening` (`screening_id`, `a_application_id`, `housing`, `reason`, `own_pets`, `time_dedicated`, `children_info`, `financial_ready`, `breed_interest`, `allergy_info`, `alone_time_plan`, `researched_breed`) VALUES
(1, 31, 'apartment', 'companion_child', 'yes', '1-2 hours', 'ccxzx', 'yes', 'cczczczx', 'czxczc', 'czxcxzcxcxzcx', 'yes'),
(2, 32, 'apartment', 'companion_child', 'yes', '1-2 hours', 'dsfd', 'yes', 'ds', 'sfdffsd', 'fssff', 'yes'),
(3, 33, 'house', 'companion_child', 'yes', '1-2 hours', 'Yes', 'yes', 'fgdgdgggdg', 'No', 'gdggdg', 'yes'),
(4, 34, 'apartment', 'companion_child', 'yes', '1-2 hours', '35', 'yes', '3544', '353', '33535553', 'yes'),
(5, 39, 'apartment', 'companion_child', 'yes', '1-2 hours', 'yes', 'yes', 'aspin', 'no', 'yes', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `animal`
--

CREATE TABLE `animal` (
  `animal_id` int(25) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `type` varchar(255) NOT NULL,
  `breed` varchar(255) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `age` int(10) NOT NULL,
  `behavior` varchar(225) NOT NULL,
  `date_rescued` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `img` varchar(500) NOT NULL DEFAULT 'dog.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `animal`
--

INSERT INTO `animal` (`animal_id`, `name`, `description`, `type`, `breed`, `gender`, `age`, `behavior`, `date_rescued`, `status`, `img`) VALUES
(2, 'Max', 'sfsfsd', 'Dog', 'Aspin', 'Male', 3, 'Calm', '2025-10-04', 'At a Shelter', 'dog3_1764167881.webp'),
(7, 'Backy', 'This is Backy. Most of his life, he lived outside as a stray. But one day, he chose our volunteer to take care of him. Now, Backy is always at the door, waiting for food, pets, and treats.\r\nOur volunteer immediately fostered Backy and has been taking care of him since then.\r\nBacky was brought to a vet to make sure he\'s healthy, but they found out he has gingivitis, respiratory issues, and a suspected FELV/FIV.', 'Cat', 'Orange cat', 'Male', 2, 'Playful', '2025-07-28', 'Adopted', 'cat1.webp'),
(11, 'PinPin', 'Pinpin was crossing the road near Infinitea, Panganiban when she got hit by a mototcyle. She was rescued by one our volunteer and is now confined at the vet. Recently, she was spayed and is now looking for a furever home!', 'Cat', 'Bengal', 'Female', 3, 'Aggressive', '2025-06-07', 'At a shelter', 'cat3.jpg'),
(12, 'Luna', 'Luna is one of the dogs from the pound and is diagnosed with Parvovirus. Fortunately, she was adopted and currently recovering at her loving home.', 'Dog', 'Labrador', 'Female', 5, 'Timid', '2025-08-24', 'At a Shelter', 'dog3.webp'),
(15, 'Ricky', 's', 'Dog', 'Husky', 'Male', 2, '', '2025-10-29', 'At a shelter', '1759535633_cat3.jpg'),
(16, 'Ricky', 's', 'Dog', 'Husky', 'Male', 2, 'Playful', '2025-10-29', 'At a Shelter', 'dog1_1764167992.webp'),
(17, 'Orpheus', 'ffdfssgsfs', 'Dog', 'Poodle', 'Male', 4, 'Timid', '2025-10-07', 'At a Shelter', 'dog3.webp'),
(18, 'Bobby', 'Cat', 'Cat', 'Persian', 'Female', 3, 'Playful', '2025-11-25', 'At a Shelter', 'dog2_1764167969.webp');

-- --------------------------------------------------------

--
-- Table structure for table `donation_details`
--

CREATE TABLE `donation_details` (
  `d_details_id` int(11) NOT NULL,
  `reference number` int(255) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `date_donated` date NOT NULL,
  `time_donated` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation_inventory`
--

CREATE TABLE `donation_inventory` (
  `item_id` int(25) NOT NULL,
  `item_type` varchar(255) NOT NULL,
  `quantity` int(50) NOT NULL,
  `date_stored` date NOT NULL,
  `item_img` varchar(500) NOT NULL,
  `donater_name` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_inventory`
--

INSERT INTO `donation_inventory` (`item_id`, `item_type`, `quantity`, `date_stored`, `item_img`, `donater_name`) VALUES
(1, 'food', 1, '2025-10-04', '../Assets/UserGenerated/Dog_food.jpg', 'Carl Daniel T. Buenconsejo'),
(2, 'toys', 1, '2025-10-05', '../Assets/UserGenerated/frontpic-removebg-preview.', 'Carl Daniel T. Buenconsejo'),
(3, 'food', 1, '2025-10-07', '../Assets/UserGenerated/Dog_food.jpg', 'James Hansberg'),
(4, 'food', 1, '2025-11-23', '../Assets/UserGenerated/Dog_food.jpg', 'James Hansberg');

-- --------------------------------------------------------

--
-- Table structure for table `donation_post`
--

CREATE TABLE `donation_post` (
  `dpost_id` int(25) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `post_img` varchar(1000) NOT NULL,
  `target` int(25) NOT NULL,
  `goal_amount` decimal(20,2) NOT NULL,
  `current_amount` decimal(20,2) NOT NULL,
  `date_posted` date NOT NULL,
  `deadline` date NOT NULL,
  `post_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_post`
--

INSERT INTO `donation_post` (`dpost_id`, `title`, `description`, `post_img`, `target`, `goal_amount`, `current_amount`, `date_posted`, `deadline`, `post_type`) VALUES
(1, 'Food for dogs & cats', 'Help us buy dog and cat food for our animals.', 'Dog_food.jpg', 0, 5000.00, 2720.00, '2025-10-01', '2025-10-31', 'supplies'),
(2, 'Cleaning Supplies', 'Help us maintain the shelters of animals by donating supply funds to us.', 'Dog_food.jpg', 0, 3000.00, 240.00, '2025-09-01', '2025-10-31', 'supplies'),
(3, 'Vitamins for Pets', 'Help us improve the health of our cats and dogs.', 'Dog_food.jpg', 0, 5000.00, 1368.00, '2025-09-01', '2025-10-31', 'supplies'),
(4, 'Donate to TAARA', 'Help us continue helping strays and give them shelter. Please rest assured that all the donations will be allocated properly.', 'Taara_Logo.webp', 0, 10000.00, 20053.00, '2025-10-01', '2025-10-31', 'general');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `event_id` int(25) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(800) NOT NULL,
  `img` varchar(500) NOT NULL,
  `location` varchar(50) NOT NULL,
  `event_date` date NOT NULL,
  `date_posted` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`event_id`, `title`, `description`, `img`, `location`, `event_date`, `date_posted`) VALUES
(3, 'Taara Animal Vaccination', 'We will be conducting free anti-rabies vaccinations for your cats and dogs.', 'Assets/Images/1759794435_volunteer_banner.jpg', 'Sipocot, Camarines Sur', '2025-10-31', '2025-10-07'),
(4, 'Animal Welfare Seminar', 'We will be conducting a seminar to teach participants about the importance of taking care of animals.', 'Assets/Images/Donation_Banner.jpg', 'Libmanan, Camarines Sur', '2025-10-23', '2025-10-07');

-- --------------------------------------------------------

--
-- Table structure for table `inkind_donation`
--

CREATE TABLE `inkind_donation` (
  `i_donation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(500) NOT NULL,
  `donation_type` varchar(100) NOT NULL,
  `img` varchar(50) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `contact_num` int(20) NOT NULL,
  `location` varchar(1000) NOT NULL,
  `date` date NOT NULL,
  `agreed_to_email` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_history`
--

CREATE TABLE `medical_history` (
  `med_history_id` int(25) NOT NULL,
  `animal_id` int(25) NOT NULL,
  `description` varchar(500) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monetary_donation`
--

CREATE TABLE `monetary_donation` (
  `m_donation_id` int(25) NOT NULL,
  `user_id` int(25) NOT NULL,
  `dpost_id` int(25) NOT NULL,
  `full_name` varchar(500) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `payment_option` varchar(25) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `contact_num` int(20) NOT NULL,
  `agreed_to_email` varchar(20) NOT NULL,
  `date_donated` date NOT NULL,
  `proof` varchar(1000) DEFAULT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monetary_donation`
--

INSERT INTO `monetary_donation` (`m_donation_id`, `user_id`, `dpost_id`, `full_name`, `amount`, `payment_option`, `message`, `contact_num`, `agreed_to_email`, `date_donated`, `proof`, `status`) VALUES
(143, 2, 3, 'Jimmy Allens', 300.00, 'gcash', 'ssfd', 8786564, 'no', '2025-11-26', '1764128009_frontpic-removebg-preview.png', 'Verified'),
(144, 2, 2, 'Carl Daniel Buenconsejo', 500.00, 'gcash', 'Hello there', 2147483647, 'no', '2025-11-27', '1764227872_Screenshot 2025-11-27 151718.png', 'Verified'),
(145, 2, 2, 'Carl Daniel Buenconsejo', 200.00, 'paypal', 'fsfdsf', 2147483647, 'no', '2025-11-27', NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `point_of_interest`
--

CREATE TABLE `point_of_interest` (
  `poi_id` int(25) NOT NULL,
  `type` enum('partner','shelter','hq') NOT NULL,
  `description` varchar(255) NOT NULL,
  `location` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `point_of_interest`
--

INSERT INTO `point_of_interest` (`poi_id`, `type`, `description`, `location`) VALUES
(33, 'hq', 'Main headquarters', '13.3583,123.7332'),
(34, 'shelter', 'Animal shelter 1 in Bicol region', '13.3537,123.7348'),
(35, 'shelter', 'Animal shelter 2 in Bicol region', '13.36235,123.73805'),
(36, 'shelter', 'Animal shelter 3 in Bicol region', '13.35375,123.7295'),
(37, 'shelter', 'Animal shelter 4 in Bicol region', '13.3576,123.73125'),
(38, 'shelter', 'Animal shelter 5 in Bicol region', '13.36165,123.731'),
(39, 'partner', 'Partner organization 1', '13.3547,123.73015'),
(40, 'partner', 'Partner organization 2', '13.36225,123.73685'),
(41, 'partner', 'Partner organization 3', '13.35715,123.7348'),
(42, 'partner', 'Partner organization 4', '13.357,123.7356'),
(43, 'partner', 'Partner organization 5', '13.3594,123.7285'),
(44, 'partner', 'Partner organization 6', '13.3578,123.73725'),
(45, 'partner', 'Partner organization 7', '13.3595,123.7372'),
(46, 'partner', 'Partner organization 8', '13.36135,123.73615'),
(47, 'partner', 'Partner organization 9', '13.3575,123.73805'),
(48, 'partner', 'Partner organization 10', '13.3568,123.73295');

-- --------------------------------------------------------

--
-- Table structure for table `rescue_report`
--

CREATE TABLE `rescue_report` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(1200) NOT NULL,
  `full_name` varchar(500) NOT NULL,
  `contact_num` int(20) NOT NULL,
  `location` varchar(500) NOT NULL,
  `img` varchar(500) NOT NULL,
  `agreed_to_email` int(11) NOT NULL,
  `date_posted` date NOT NULL,
  `status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rescue_report`
--

INSERT INTO `rescue_report` (`report_id`, `user_id`, `type`, `description`, `full_name`, `contact_num`, `location`, `img`, `agreed_to_email`, `date_posted`, `status`) VALUES
(2, 1, 'rescue', 'sadasdas', 'errerwe', 68677886, '13.786207, 122.979446', '../Assets/UserGenerated/Dog_food.jpg', 0, '2025-10-03', 'resolved'),
(3, 1, 'lost_and_found', 'wterrt', 'Carl Daniel Buenceonsejo', 2147483647, '13.337403, 123.724401', '../Assets/UserGenerated/Donation_Banner.jpg', 0, '2025-10-03', 'resolved'),
(4, 1, 'lost_and_found', 'Lost dog', 'Carl Daniel Buenceonsejo', 2147483647, '13.785632, 122.980059', '../Assets/UserGenerated/volunteer_banner.jpg', 0, '2025-10-04', 'cancelled'),
(5, 1, 'rescue', 'injured cat', 'Sam Wilson', 2147483647, '13.785622, 122.980066', '../Assets/UserGenerated/1759535178_1759535048_cat2.jpg', 0, '2025-10-06', 'pending'),
(6, 2, 'lost_and_found', 'Animal found', 'Jane Foster', 2147483647, '13.518302, 123.597745', '../Assets/UserGenerated/Donation_Banner.jpg', 0, '2025-10-07', 'pending'),
(7, 2, 'rescue', 'Dog found in a ditch', 'Peter B. Parker', 2147483647, '13.318096, 123.435416', '../Assets/UserGenerated/dog3.webp', 0, '2025-10-07', 'pending'),
(8, 2, 'rescue', 'Dog found in a ditch', 'Peter B. Parker', 2147483647, '13.341056, 123.663569', '../Assets/UserGenerated/dog3.webp', 0, '2025-10-07', 'Resolved'),
(9, 4, 'rescue', 'wounded stray dogs', 'Peter B. Parker', 2147483647, '13.443797, 123.619196', '../Assets/UserGenerated/dog3.webp', 0, '2025-10-07', 'pending'),
(10, 7, 'lost_and_found', 'lost do', 'John Walker', 2147483647, '13.784789, 122.980270', '../Assets/UserGenerated/volunteer_banner.jpg', 0, '2025-10-07', 'pending'),
(11, 2, 'rescue', 'lost animal', 'John Walker', 2147483647, '13.285690, 123.789513', '../Assets/UserGenerated/Donation_Banner.jpg', 0, '2025-10-09', 'pending'),
(12, 2, 'rescue', 'safsafafas', 'Carl Daniel T. Buenconsejo', 2147483647, '13.760459, 122.983505', '../Assets/UserGenerated/feeding.jpg', 0, '2025-10-09', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_img` varchar(500) DEFAULT 'user.png',
  `user_type` varchar(20) NOT NULL DEFAULT 'client',
  `role` enum('director','adoption','rescue','donation','event','volunteer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `password`, `profile_img`, `user_type`, `role`) VALUES
(1, 'Carl Daniel', 'carldanielbuenconeewesejo@gmail.com', 'Taara_Haru69', 'user.png', 'admin', 'director'),
(2, 'Carl Daniel', 'cbuenconsejobusacc@gmail.com', 'admin', 'profile.jpg', 'admin', 'director'),
(3, 'Director', 'taara.admin@gmail.com', 'admin', 'user.png', 'client', 'director'),
(4, 'Michael Jordan', 'mj@gmail.com', 'mj', 'user.png', 'client', 'director'),
(5, 'James Bond', 'jb@gmail.com', 'iambond', 'user.png', 'client', 'director'),
(6, 'Han Solo', 'hansolo@gmail.com', 'ishotfirst', 'user.png', 'client', 'director'),
(7, 'user', 'user@gmail.com', 'user', 'user.png', 'client', 'director'),
(8, 'Eliad Karson', 'karson@gmail.com', '$2y$10$S6QT/fePrYc1NAQLuvv9yOFMPdiIVE5virwa5Yw10USKV0GyEf2Qu', 'user.png', 'client', 'director'),
(9, 'Admin User', 'qrt@gmail.com', '$2y$10$971KJ355vHMcDxvVLfAW5OZPD9CRY8B1FvcY2oomZcew14ruC9zLu', 'user.png', 'client', 'director'),
(10, 'Carl ', 'carldanielbuenconsejo22@gmail.com', 'admin', 'user.png', 'client', 'director'),
(11, 'Michael Jordan', 'eeee@gmail.com', '1234', 'user.png', 'client', 'director'),
(12, 'Carl Daniel', 'carldanielbuenconsejo@gmail.com', 'Taara_Haru69', 'user.png', 'client', 'director');

-- --------------------------------------------------------

--
-- Table structure for table `vaccinations`
--

CREATE TABLE `vaccinations` (
  `vaccination_id` int(25) NOT NULL,
  `animal_id` int(25) NOT NULL,
  `vaccine_type` varchar(255) NOT NULL,
  `date_vaccinated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccinations`
--

INSERT INTO `vaccinations` (`vaccination_id`, `animal_id`, `vaccine_type`, `date_vaccinated`) VALUES
(1, 2, 'anti-rabies', '2025-09-30');

-- --------------------------------------------------------

--
-- Table structure for table `volunteer`
--

CREATE TABLE `volunteer` (
  `volunteer_id` int(25) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'None',
  `user_id` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer`
--

INSERT INTO `volunteer` (`volunteer_id`, `full_name`, `role`, `user_id`) VALUES
(1, 'Carl Daniel T. Buenconsejo', 'None', 2),
(3, 'Juan dela Cruz', 'None', 2);

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_application`
--

CREATE TABLE `volunteer_application` (
  `v_application_id` int(11) NOT NULL,
  `user_id` int(25) NOT NULL,
  `first_committee` varchar(255) NOT NULL,
  `second_committee` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `classification` varchar(255) NOT NULL,
  `age` int(25) NOT NULL,
  `birth_date` date NOT NULL,
  `contact_num` int(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `id_img` varchar(255) NOT NULL,
  `reason_for_joining` varchar(500) NOT NULL,
  `date_appied` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer_application`
--

INSERT INTO `volunteer_application` (`v_application_id`, `user_id`, `first_committee`, `second_committee`, `full_name`, `classification`, `age`, `birth_date`, `contact_num`, `address`, `id_img`, `reason_for_joining`, `date_appied`, `status`) VALUES
(1, 2, 'Secretariat', 'Logistics', 'Carl Daniel T. Buenconsejo', 'Student', 23, '0000-00-00', 2147483647, 'Sipocot, Camarines Sur', 'uploads/1759812164_home_banner.jpg', 'To help unfortunate animals', '2025-10-07', 'approved'),
(2, 2, 'Rescue Initiatives', 'Documentation', 'Juan dela Cruz', 'Student', 23, '0000-00-00', 2147483647, 'Tabaco, City', 'uploads/1759818923_volunteer_banner.jpg', 'ydhghghgf', '2025-10-07', 'approved'),
(3, 7, 'Secretariat', 'Adoption and Foster', 'Juan dela Cruz', 'Student', 23, '0000-00-00', 2147483647, 'Tabaco, City', 'uploads/1759821165_dog3.webp', 'desire to help animals', '2025-10-07', 'pending'),
(4, 7, 'Secretariat', 'Adoption and Foster', 'Juan dela Cruz', 'Student', 23, '0000-00-00', 2147483647, 'Tabaco, City', 'uploads/1759821259_dog3.webp', 'desire to help animals', '2025-10-07', 'pending'),
(5, 2, 'Logistics', 'Secretariat', 'Juan dela Cruz', 'Student', 23, '0000-00-00', 2147483647, 'Tabaco, City', 'uploads/1759949943_home_banner.jpg', 'lkkkjjljljlkj', '2025-10-08', 'pending'),
(6, 2, 'Secretariat', 'Logistics', 'Carl Daniel Talento Buenconsejo', 'Student', 23, '0000-00-00', 2147483647, 'Sipocot, Camarines Sur', 'uploads/1759966665_home_banner.jpg', 'reg', '2025-10-09', 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `animal_id` (`animal_id`);

--
-- Indexes for table `adoption`
--
ALTER TABLE `adoption`
  ADD PRIMARY KEY (`adoption_id`),
  ADD KEY `animal_id` (`animal_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `adoption_application`
--
ALTER TABLE `adoption_application`
  ADD PRIMARY KEY (`a_application_id`);

--
-- Indexes for table `adoption_screening`
--
ALTER TABLE `adoption_screening`
  ADD PRIMARY KEY (`screening_id`),
  ADD KEY `a_application_id` (`a_application_id`);

--
-- Indexes for table `animal`
--
ALTER TABLE `animal`
  ADD PRIMARY KEY (`animal_id`);

--
-- Indexes for table `donation_details`
--
ALTER TABLE `donation_details`
  ADD PRIMARY KEY (`d_details_id`);

--
-- Indexes for table `donation_inventory`
--
ALTER TABLE `donation_inventory`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `donation_post`
--
ALTER TABLE `donation_post`
  ADD PRIMARY KEY (`dpost_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `inkind_donation`
--
ALTER TABLE `inkind_donation`
  ADD PRIMARY KEY (`i_donation_id`);

--
-- Indexes for table `medical_history`
--
ALTER TABLE `medical_history`
  ADD PRIMARY KEY (`med_history_id`);

--
-- Indexes for table `monetary_donation`
--
ALTER TABLE `monetary_donation`
  ADD PRIMARY KEY (`m_donation_id`),
  ADD KEY `dpost_id` (`dpost_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `point_of_interest`
--
ALTER TABLE `point_of_interest`
  ADD PRIMARY KEY (`poi_id`);

--
-- Indexes for table `rescue_report`
--
ALTER TABLE `rescue_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `vaccinations`
--
ALTER TABLE `vaccinations`
  ADD PRIMARY KEY (`vaccination_id`),
  ADD KEY `animal_id` (`animal_id`);

--
-- Indexes for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD PRIMARY KEY (`volunteer_id`);

--
-- Indexes for table `volunteer_application`
--
ALTER TABLE `volunteer_application`
  ADD PRIMARY KEY (`v_application_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `activity_id` int(25) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adoption`
--
ALTER TABLE `adoption`
  MODIFY `adoption_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `adoption_application`
--
ALTER TABLE `adoption_application`
  MODIFY `a_application_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `adoption_screening`
--
ALTER TABLE `adoption_screening`
  MODIFY `screening_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `animal`
--
ALTER TABLE `animal`
  MODIFY `animal_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `donation_details`
--
ALTER TABLE `donation_details`
  MODIFY `d_details_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation_inventory`
--
ALTER TABLE `donation_inventory`
  MODIFY `item_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `donation_post`
--
ALTER TABLE `donation_post`
  MODIFY `dpost_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inkind_donation`
--
ALTER TABLE `inkind_donation`
  MODIFY `i_donation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `medical_history`
--
ALTER TABLE `medical_history`
  MODIFY `med_history_id` int(25) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monetary_donation`
--
ALTER TABLE `monetary_donation`
  MODIFY `m_donation_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `point_of_interest`
--
ALTER TABLE `point_of_interest`
  MODIFY `poi_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `rescue_report`
--
ALTER TABLE `rescue_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vaccinations`
--
ALTER TABLE `vaccinations`
  MODIFY `vaccination_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `volunteer`
--
ALTER TABLE `volunteer`
  MODIFY `volunteer_id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `volunteer_application`
--
ALTER TABLE `volunteer_application`
  MODIFY `v_application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `adoption`
--
ALTER TABLE `adoption`
  ADD CONSTRAINT `adoption_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adoption_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `adoption_screening`
--
ALTER TABLE `adoption_screening`
  ADD CONSTRAINT `adoption_screening_ibfk_1` FOREIGN KEY (`a_application_id`) REFERENCES `adoption_application` (`a_application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `monetary_donation`
--
ALTER TABLE `monetary_donation`
  ADD CONSTRAINT `monetary_donation_ibfk_1` FOREIGN KEY (`dpost_id`) REFERENCES `donation_post` (`dpost_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `monetary_donation_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `rescue_report`
--
ALTER TABLE `rescue_report`
  ADD CONSTRAINT `rescue_report_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vaccinations`
--
ALTER TABLE `vaccinations`
  ADD CONSTRAINT `vaccinations_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `volunteer_application`
--
ALTER TABLE `volunteer_application`
  ADD CONSTRAINT `volunteer_application_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
