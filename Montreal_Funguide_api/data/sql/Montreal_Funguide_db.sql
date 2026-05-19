-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 16, 2026 at 01:21 AM
-- Server version: 11.8.5-MariaDB-log
-- PHP Version: 8.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `montreal_funguide_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `calories`
--

CREATE TABLE `calories` (
  `calorie_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `protein` decimal(10,2) DEFAULT NULL,
  `fat` decimal(10,2) DEFAULT NULL,
  `carbohydrates` decimal(10,2) DEFAULT NULL,
  `nutrition_source` varchar(100) DEFAULT 'API-Ninjas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `device_id` int(11) NOT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `device_type` varchar(100) DEFAULT NULL,
  `device_os` varchar(100) DEFAULT NULL,
  `device_ip` varchar(45) DEFAULT NULL,
  `device_last_seen` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fungi`
--

CREATE TABLE `fungi` (
  `fungi_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  `fungi_observation_source` varchar(100) DEFAULT 'GBIF',
  `fungi_observation_date` timestamp NULL DEFAULT NULL,
  `fungi_notes` text DEFAULT NULL,
  `fungi_created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fungi_location`
--

CREATE TABLE `fungi_location` (
  `fungi_location_id` int(11) NOT NULL,
  `fungi_id` int(11) NOT NULL,
  `fungi_location_lat` decimal(10,8) DEFAULT NULL,
  `fungi_location_long` decimal(11,8) DEFAULT NULL,
  `fungi_location_borough` varchar(100) DEFAULT NULL,
  `fungi_location_discovery` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fungi_recipe`
--

CREATE TABLE `fungi_recipe` (
  `fungi_recipe_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `is_main_ingredient` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `habitat`
--

CREATE TABLE `habitat` (
  `habitat_id` int(11) NOT NULL,
  `habitat_type` varchar(100) DEFAULT NULL,
  `habitat_climate` varchar(100) DEFAULT NULL,
  `habitat_soil` varchar(100) DEFAULT NULL,
  `habitat_humindex` varchar(50) DEFAULT NULL,
  `habitat_desc` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredient`
--

CREATE TABLE `ingredient` (
  `ingredient_id` int(11) NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  `ingredient_unit` varchar(50) DEFAULT NULL,
  `ingredient_source` varchar(100) DEFAULT 'API'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(11) NOT NULL,
  `location_name` varchar(255) DEFAULT NULL,
  `location_lat` decimal(10,8) NOT NULL,
  `location_long` decimal(11,8) NOT NULL,
  `location_borough` varchar(100) DEFAULT NULL,
  `location_addr` text DEFAULT NULL,
  `location_type` enum('fungi_spotting','park','forest','urban','trail') DEFAULT NULL,
  `location_image_url` varchar(255) DEFAULT NULL,
  `location_accessibility` enum('easy','moderate','difficult') DEFAULT 'moderate',
  `location_created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `location`
--
DELIMITER $$
CREATE TRIGGER `trg_location_montreal_only` BEFORE INSERT ON `location` FOR EACH ROW BEGIN
    IF NEW.location_borough IS NOT NULL AND NEW.location_borough NOT IN (
        SELECT borough_name FROM montreal_borough
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only Montreal boroughs are allowed';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `map`
--

CREATE TABLE `map` (
  `map_id` int(11) NOT NULL,
  `map_name` varchar(100) DEFAULT 'Montreal Funguide Map',
  `map_region` varchar(100) DEFAULT 'Montreal',
  `map_created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `montreal_borough`
--

CREATE TABLE `montreal_borough` (
  `borough_id` int(11) NOT NULL,
  `borough_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE `recipe` (
  `recipe_id` int(11) NOT NULL,
  `recipe_external_id` varchar(100) DEFAULT NULL,
  `recipe_name` varchar(255) NOT NULL,
  `recipe_source` varchar(100) DEFAULT 'MealDB',
  `recipe_category` varchar(100) DEFAULT NULL,
  `recipe_area` varchar(100) DEFAULT NULL,
  `recipe_instructions` text DEFAULT NULL,
  `recipe_thumbnail` varchar(255) DEFAULT NULL,
  `recipe_tags` varchar(255) DEFAULT NULL,
  `recipe_difficulty` enum('easy','medium','hard') DEFAULT NULL,
  `recipe_image_url` varchar(255) DEFAULT NULL,
  `recipe_created_at` timestamp NULL DEFAULT current_timestamp(),
  `recipe_last_synced` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ingredient`
--

CREATE TABLE `recipe_ingredient` (
  `recipe_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `quantity` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `species`
--

CREATE TABLE `species` (
  `species_id` int(11) NOT NULL,
  `species_name` varchar(255) NOT NULL,
  `species_family` varchar(255) DEFAULT NULL,
  `species_genus` varchar(255) DEFAULT NULL,
  `species_edibility` tinyint(1) DEFAULT 0,
  `species_toxicity` enum('safe','mild','moderate','dangerous') DEFAULT 'safe',
  `species_discovery` int(11) DEFAULT NULL,
  `species_gbif_id` varchar(100) DEFAULT NULL,
  `species_image_url` varchar(255) DEFAULT NULL,
  `species_created_at` timestamp NULL DEFAULT current_timestamp(),
  `species_last_synced` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `species_habitat`
--

CREATE TABLE `species_habitat` (
  `species_id` int(11) NOT NULL,
  `habitat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip`
--

CREATE TABLE `trip` (
  `trip_id` int(11) NOT NULL,
  `trip_desc` varchar(255) DEFAULT NULL,
  `trip_distance` decimal(10,2) DEFAULT NULL,
  `trip_duration` int(11) DEFAULT NULL,
  `trip_travel_mode` enum('walking','cycling','driving') DEFAULT 'walking',
  `trip_doc` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `trip`
--
DELIMITER $$
CREATE TRIGGER `trg_trip_estimate_duration` BEFORE INSERT ON `trip` FOR EACH ROW BEGIN
    DECLARE speed_kmh DECIMAL(5,2);

    IF NEW.trip_travel_mode = 'walking' THEN
        SET speed_kmh = 5;
    ELSEIF NEW.trip_travel_mode = 'cycling' THEN
        SET speed_kmh = 15;
    ELSE
        SET speed_kmh = 40;
    END IF;

    IF NEW.trip_distance IS NOT NULL THEN
        SET NEW.trip_duration = (NEW.trip_distance / speed_kmh) * 60;
    ELSE
        SET NEW.trip_duration = NULL;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `trip_location`
--

CREATE TABLE `trip_location` (
  `trip_location_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `trip_location_visit_order` int(11) NOT NULL,
  `trip_location_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_handle` varchar(48) DEFAULT NULL,
  `user_verified` tinyint(1) DEFAULT 0,
  `user_fname` varchar(32) DEFAULT NULL,
  `user_lname` varchar(32) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `user_role` enum('admin','user','guest') DEFAULT 'guest',
  `user_doc` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `user`
--
DELIMITER $$
CREATE TRIGGER `trg_users_generate_handle` BEFORE INSERT ON `user` FOR EACH ROW BEGIN
    IF NEW.user_handle IS NULL OR NEW.user_handle = '' THEN
        SET NEW.user_handle = CONCAT('anon', UUID_SHORT());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_device`
--

CREATE TABLE `user_device` (
  `user_device_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `first_seen` timestamp NULL DEFAULT current_timestamp(),
  `last_seen` timestamp NULL DEFAULT current_timestamp(),
  `last_ip` varchar(45) DEFAULT NULL,
  `last_user_agent` text DEFAULT NULL,
  `login_count` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `user_device`
--
DELIMITER $$
CREATE TRIGGER `trg_user_device_update` BEFORE UPDATE ON `user_device` FOR EACH ROW BEGIN
    SET NEW.last_seen = CURRENT_TIMESTAMP;
    SET NEW.login_count = OLD.login_count + 1;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calories`
--
ALTER TABLE `calories`
  ADD PRIMARY KEY (`calorie_id`),
  ADD KEY `idx_calorie_ingredient` (`ingredient_id`);

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`device_id`);

--
-- Indexes for table `fungi`
--
ALTER TABLE `fungi`
  ADD PRIMARY KEY (`fungi_id`),
  ADD KEY `idx_fungi_species` (`species_id`);

--
-- Indexes for table `fungi_location`
--
ALTER TABLE `fungi_location`
  ADD PRIMARY KEY (`fungi_location_id`),
  ADD KEY `idx_fungi_location_fungi` (`fungi_id`);

--
-- Indexes for table `fungi_recipe`
--
ALTER TABLE `fungi_recipe`
  ADD PRIMARY KEY (`fungi_recipe_id`),
  ADD KEY `idx_fungi_recipe_species` (`species_id`),
  ADD KEY `idx_fungi_recipe_recipe` (`recipe_id`);

--
-- Indexes for table `habitat`
--
ALTER TABLE `habitat`
  ADD PRIMARY KEY (`habitat_id`);

--
-- Indexes for table `ingredient`
--
ALTER TABLE `ingredient`
  ADD PRIMARY KEY (`ingredient_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `idx_location_borough` (`location_borough`);

--
-- Indexes for table `map`
--
ALTER TABLE `map`
  ADD PRIMARY KEY (`map_id`);

--
-- Indexes for table `montreal_borough`
--
ALTER TABLE `montreal_borough`
  ADD PRIMARY KEY (`borough_id`),
  ADD UNIQUE KEY `uq_borough_name` (`borough_name`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`recipe_id`);

--
-- Indexes for table `recipe_ingredient`
--
ALTER TABLE `recipe_ingredient`
  ADD PRIMARY KEY (`recipe_id`,`ingredient_id`),
  ADD KEY `idx_recipe_ingredient_recipe` (`recipe_id`),
  ADD KEY `idx_recipe_ingredient_ingredient` (`ingredient_id`);

--
-- Indexes for table `species`
--
ALTER TABLE `species`
  ADD PRIMARY KEY (`species_id`);

--
-- Indexes for table `species_habitat`
--
ALTER TABLE `species_habitat`
  ADD PRIMARY KEY (`species_id`,`habitat_id`),
  ADD KEY `fk_species_habitat_habitat` (`habitat_id`);

--
-- Indexes for table `trip`
--
ALTER TABLE `trip`
  ADD PRIMARY KEY (`trip_id`);

--
-- Indexes for table `trip_location`
--
ALTER TABLE `trip_location`
  ADD PRIMARY KEY (`trip_location_id`),
  ADD KEY `idx_trip_location_trip` (`trip_id`),
  ADD KEY `idx_trip_location_location` (`location_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uq_user_email` (`user_email`),
  ADD UNIQUE KEY `uq_user_handle` (`user_handle`);

--
-- Indexes for table `user_device`
--
ALTER TABLE `user_device`
  ADD PRIMARY KEY (`user_device_id`),
  ADD KEY `idx_user_device_user` (`user_id`),
  ADD KEY `idx_user_device_device` (`device_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calories`
--
ALTER TABLE `calories`
  MODIFY `calorie_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device`
--
ALTER TABLE `device`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fungi`
--
ALTER TABLE `fungi`
  MODIFY `fungi_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fungi_location`
--
ALTER TABLE `fungi_location`
  MODIFY `fungi_location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fungi_recipe`
--
ALTER TABLE `fungi_recipe`
  MODIFY `fungi_recipe_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `habitat`
--
ALTER TABLE `habitat`
  MODIFY `habitat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingredient`
--
ALTER TABLE `ingredient`
  MODIFY `ingredient_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `map`
--
ALTER TABLE `map`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `montreal_borough`
--
ALTER TABLE `montreal_borough`
  MODIFY `borough_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `species`
--
ALTER TABLE `species`
  MODIFY `species_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip`
--
ALTER TABLE `trip`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_location`
--
ALTER TABLE `trip_location`
  MODIFY `trip_location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_device`
--
ALTER TABLE `user_device`
  MODIFY `user_device_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `calories`
--
ALTER TABLE `calories`
  ADD CONSTRAINT `fk_calories_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredient` (`ingredient_id`) ON DELETE CASCADE;

--
-- Constraints for table `fungi`
--
ALTER TABLE `fungi`
  ADD CONSTRAINT `fk_fungi_species` FOREIGN KEY (`species_id`) REFERENCES `species` (`species_id`) ON DELETE CASCADE;

--
-- Constraints for table `fungi_location`
--
ALTER TABLE `fungi_location`
  ADD CONSTRAINT `fk_fungi_location_fungi` FOREIGN KEY (`fungi_id`) REFERENCES `fungi` (`fungi_id`) ON DELETE CASCADE;

--
-- Constraints for table `fungi_recipe`
--
ALTER TABLE `fungi_recipe`
  ADD CONSTRAINT `fk_fungi_recipe_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fungi_recipe_species` FOREIGN KEY (`species_id`) REFERENCES `species` (`species_id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe_ingredient`
--
ALTER TABLE `recipe_ingredient`
  ADD CONSTRAINT `fk_recipe_ingredient_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredient` (`ingredient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recipe_ingredient_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints for table `species_habitat`
--
ALTER TABLE `species_habitat`
  ADD CONSTRAINT `fk_species_habitat_habitat` FOREIGN KEY (`habitat_id`) REFERENCES `habitat` (`habitat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_species_habitat_species` FOREIGN KEY (`species_id`) REFERENCES `species` (`species_id`) ON DELETE CASCADE;

--
-- Constraints for table `trip_location`
--
ALTER TABLE `trip_location`
  ADD CONSTRAINT `fk_trip_location_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_trip_location_trip` FOREIGN KEY (`trip_id`) REFERENCES `trip` (`trip_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_device`
--
ALTER TABLE `user_device`
  ADD CONSTRAINT `fk_user_device_device` FOREIGN KEY (`device_id`) REFERENCES `device` (`device_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_device_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
