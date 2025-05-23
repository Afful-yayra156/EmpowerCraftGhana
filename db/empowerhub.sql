-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2025 at 08:11 PM
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
-- Database: `empowerhub`
--

-- --------------------------------------------------------


--contact messages
CREATE TABLE contact_messages (
  id int(11) NOT NULL,
  first_name varchar(255) NOT NULL,
  last_name varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  phone_number varchar(20) NOT NULL,
  category varchar(255) NOT NULL,
  artisan varchar(255) NOT NULL,
  message text NOT NULL,
  submitted_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--orders
CREATE TABLE orders (
  order_id int(11) NOT NULL,
  buyer_id int(11) NOT NULL,
  order_date timestamp NOT NULL DEFAULT current_timestamp(),
  total_amount decimal(10,2) NOT NULL,
  status enum('cart','pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  shipping_address text DEFAULT NULL,
  shipping_method varchar(100) DEFAULT NULL,
  tracking_number varchar(100) DEFAULT NULL,
  notes text DEFAULT NULL,
  service_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--reviews
CREATE TABLE reviews (
  review_id int(11) NOT NULL,
  reviewer_id int(11) NOT NULL,
  reviewed_id int(11) NOT NULL,
  reference_type enum('service','listing','user') NOT NULL,
  reference_id int(11) NOT NULL,
  rating decimal(3,2) NOT NULL CHECK (rating >= 1 and rating <= 5),
  comment text DEFAULT NULL,
  creation_date timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--services
CREATE TABLE services (
  service_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  category_id int(11) NOT NULL,
  title varchar(100) NOT NULL,
  description text NOT NULL,
  price decimal(10,2) NOT NULL,
  category varchar(255) DEFAULT NULL,
  duration varchar(50) DEFAULT NULL,
  availability text DEFAULT NULL,
  is_featured tinyint(1) DEFAULT 0,
  creation_date timestamp NOT NULL DEFAULT current_timestamp(),
  last_updated timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  status enum('active','inactive','pending_approval') DEFAULT 'pending_approval',
  image_path varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--users
CREATE TABLE users (
  user_id int(11) NOT NULL,
  email varchar(100) NOT NULL,
  phone_number varchar(15) DEFAULT NULL,
  password_hash varchar(255) NOT NULL,
  user_type enum('artisan','client','admin','account_manager') NOT NULL,
  first_name varchar(50) NOT NULL,
  last_name varchar(50) NOT NULL,
  profile_picture varchar(255) DEFAULT NULL,
  bio text DEFAULT NULL,
  location varchar(100) DEFAULT NULL,
  registration_date timestamp NOT NULL DEFAULT current_timestamp(),
  last_login timestamp NULL DEFAULT NULL,
  is_verified tinyint(1) DEFAULT 0,
  account_status enum('active','suspended','inactive') DEFAULT 'active',
  average_rating decimal(3,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `artisan_id` int(11) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `last_message_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

CREATE TABLE `checkout` (
  `checkout_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive','sold','pending_approval') DEFAULT 'pending_approval'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checkout_images`
--

CREATE TABLE `checkout_images` (
  `image_id` int(11) NOT NULL,
  `checkout_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `sent_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `checkout_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `payment_type` enum('order','booking','payout') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `payer_id` int(11) NOT NULL,
  `payee_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('mobile_money','card','bank_transfer') NOT NULL,
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `availability` text DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive','pending_approval') DEFAULT 'pending_approval'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_images`
--

CREATE TABLE `service_images` (
  `image_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_type` enum('artisan','client','admin','account_manager') NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `account_status` enum('active','suspended','inactive') DEFAULT 'active',
  `average_rating` decimal(3,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `phone_number`, `password_hash`, `user_type`, `first_name`, `last_name`, `profile_picture`, `bio`, `location`, `registration_date`, `last_login`, `is_verified`, `account_status`, `average_rating`) VALUES
(1, 'nuhefa@mailinator.com', '', '$2y$10$aFLgkcBu.KWNRnEpLgM/.uqPp43GrwhkR8MMQbw0FlhD245VkQ1Ue', 'client', 'Dahlia', 'Ramirez', NULL, NULL, NULL, '2025-03-29 02:17:41', NULL, 0, 'active', 0.00),
(7, 'hacasybe@mailinator.com', NULL, '$2y$10$YRLhQni6EA9IyTt6Ro8.NeW67adS1gdrp.fe9qcXbqudNo.ypyi3u', 'artisan', 'Robert', 'Leach', NULL, NULL, NULL, '2025-04-03 14:47:21', NULL, 0, 'active', 0.00),
(8, 'wimyhivab@mailinator.com', NULL, '$2y$10$WaerlV9Z5KNb6lwMb0Wpbees7a0..Ypv2hqNtd2X6DYtsOBt6Xwaa', 'client', 'Neve', 'Slater', NULL, NULL, NULL, '2025-04-03 22:33:06', NULL, 0, 'active', 0.00),
(9, 'zicyzizoto@mailinator.com', NULL, '$2y$10$E3YvOpMzHzeWyiMpj.ljReZ0P80oXllxt7tdPpqHFkXZiWwTPBQv2', 'client', 'Sylvia', 'Lane', NULL, NULL, NULL, '2025-04-03 23:05:33', NULL, 0, 'active', 0.00),
(10, 'haku@mailinator.com', NULL, '$2y$10$ySXkq8xrrWKoAAtP5gVnUObVnrmsLoVTPGBp36i4/ZCgg0rG2rrxG', 'client', 'Tana', 'Buck', NULL, NULL, NULL, '2025-04-03 23:21:24', NULL, 0, 'active', 0.00),
(12, 'vapuwyheh@mailinator.com', NULL, '$2y$10$SKvp6HOzF4BYtnNIaDdC6ex27GZGB6SYMcb5/F/SRmw2D2Fr8yd3W', 'artisan', 'Murphy', 'Giles', NULL, NULL, NULL, '2025-04-03 23:35:47', NULL, 0, 'active', 0.00),
(13, 'lise@mailinator.com', NULL, '$2y$10$2NfcnuJi9YaPOaXJA1Jov.Nhh0CTcdsJQBwy/D0mf2v7FxO1XK4oK', 'artisan', 'Mia', 'Snyder', NULL, NULL, NULL, '2025-04-03 23:38:42', NULL, 0, 'active', 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `artisan_id` (`artisan_id`),
  ADD KEY `idx_bookings_status` (`status`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `parent_category_id` (`parent_category_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `user1_id` (`user1_id`),
  ADD KEY `user2_id` (`user2_id`),
  ADD KEY `last_message_id` (`last_message_id`);

--
-- Indexes for table `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`checkout_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_checkout_category` (`category_id`);

--
-- Indexes for table `checkout_images`
--
ALTER TABLE `checkout_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `checkout_id` (`checkout_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `checkout_id` (`checkout_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `payer_id` (`payer_id`),
  ADD KEY `payee_id` (`payee_id`),
  ADD KEY `idx_payments_status` (`status`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `reviewed_id` (`reviewed_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_services_category` (`category_id`);

--
-- Indexes for table `service_images`
--
ALTER TABLE `service_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD KEY `idx_users_user_type` (`user_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checkout`
--
ALTER TABLE `checkout`
  MODIFY `checkout_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checkout_images`
--
ALTER TABLE `checkout_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_images`
--
ALTER TABLE `service_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`artisan_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_ibfk_3` FOREIGN KEY (`last_message_id`) REFERENCES `messages` (`message_id`) ON DELETE SET NULL;

--
-- Constraints for table `checkout`
--
ALTER TABLE `checkout`
  ADD CONSTRAINT `checkout_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `checkout_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `checkout_images`
--
ALTER TABLE `checkout_images`
  ADD CONSTRAINT `checkout_images_ibfk_1` FOREIGN KEY (`checkout_id`) REFERENCES `checkout` (`checkout_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
