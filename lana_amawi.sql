-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2025 at 01:11 AM
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
-- Database: `lana_amawi`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `program` varchar(255) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `program`, `appointment_date`, `appointment_time`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 15, 'Executive Coaching', '2025-08-30', '16:00', 'Career development discussion', 'completed', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(2, 15, 'Leadership Training', '2025-08-24', '11:00', 'Leadership skills development', 'pending', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(3, 16, 'Communication Skills', '2025-09-04', '15:00', 'Career development discussion', 'completed', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(4, 16, 'Communication Skills', '2025-08-11', '09:00', 'Communication skills training', 'completed', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(5, 15, 'Life Coaching', '2025-09-05', '16:00', 'Life coaching session', 'completed', '2025-08-07 15:03:41', '2025-08-07 16:41:46'),
(6, 16, 'Life Coaching', '2025-08-19', '15:00', 'Leadership skills development', 'confirmed', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(7, 16, 'Communication Skills', '2025-08-31', '11:00', 'Progress review meeting', 'pending', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(8, 16, 'Stress Management', '2025-08-19', '09:00', 'Life coaching session', 'completed', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(9, 16, 'Communication Skills', '2025-08-10', '11:00', 'Progress review meeting', 'completed', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(10, 16, 'Communication Skills', '2025-09-05', '09:00', 'Communication skills training', 'pending', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(11, 16, 'Stress Management', '2025-08-29', '16:00', 'Leadership skills development', 'completed', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(12, 15, 'Communication Skills', '2025-08-10', '16:00', 'Goal setting and planning', 'pending', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(13, 16, 'Communication Skills', '2025-08-25', '11:00', 'Communication skills training', 'pending', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(14, 16, 'Career Development', '2025-08-10', '10:00', 'Career development discussion', 'confirmed', '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(15, 15, 'Career Development', '2025-08-18', '09:00', 'Communication skills training', 'pending', '2025-08-07 15:03:41', '2025-08-07 15:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `preferred_date` date NOT NULL,
  `preferred_time` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','suggested_alternative') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `full_name`, `email`, `phone`, `program`, `preferred_date`, `preferred_time`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Thor Glass', 'papaj@mailinator.com', '+1 (227) 965-1079', NULL, '2025-08-09', '12:00', NULL, 'suggested_alternative', '2025-08-07 17:11:18', '2025-08-07 17:13:14'),
(2, 'Griffin Webster', 'xepebekupe@mailinator.com', '+1 (814) 942-8158', NULL, '2025-08-07', '14:00', 'Magnam maiores qui i', 'pending', '2025-08-07 17:22:13', '2025-08-07 17:22:13');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `attachment_name` varchar(255) DEFAULT NULL,
  `attachment_type` varchar(255) DEFAULT NULL,
  `attachment_size` int(11) DEFAULT NULL,
  `sender_type` enum('client','admin') NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `message`, `attachment_path`, `attachment_name`, `attachment_type`, `attachment_size`, `sender_type`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 15, 'ho', NULL, NULL, NULL, NULL, 'admin', 1, '2025-08-07 15:16:19', '2025-08-07 16:41:27'),
(2, 15, 'hi', NULL, NULL, NULL, NULL, 'client', 1, '2025-08-07 15:19:56', '2025-08-07 15:24:41'),
(3, 15, 'hi', 'message-attachments/1754598163_logo-2.png', 'logo-2.png', 'image/png', 5090, 'admin', 1, '2025-08-07 15:22:43', '2025-08-07 16:41:27'),
(4, 15, 'hi', NULL, NULL, NULL, NULL, 'admin', 1, '2025-08-07 15:29:20', '2025-08-07 16:41:27'),
(5, 15, '', 'message-attachments/1754598568_Lana Amawmi Service agreement.docx', 'Lana Amawmi Service agreement.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 136002, 'admin', 1, '2025-08-07 15:29:28', '2025-08-07 16:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_06_224207_create_bookings_table', 1),
(5, '2025_08_06_224219_create_appointments_table', 1),
(6, '2025_08_06_224232_create_messages_table', 1),
(7, '2025_08_06_224245_add_is_admin_to_users_table', 1),
(8, '2025_08_07_172645_add_suggested_alternative_to_bookings_status_enum', 1),
(9, '2025_08_07_201515_add_attachments_to_messages_table', 2),
(10, '2025_08_07_215137_make_program_nullable_in_bookings_table', 3),
(11, '2025_08_07_215234_make_program_nullable_in_appointments_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('D4uXbdltSM5s5rZILtlbzhzAymcAerXjKUDpT1t6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVFg5SnA5ODZQWlVaamlyeTdtS1FLU29vMXRMYkw3aEJMSXBRZ1huSSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=', 1754605879),
('ui8Mb3EEyy8OQjFHJftErKTka1E0IYS8K7Q6nPnN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY0NkSmdUT2x2aGQxRUgxb0xnVXY5UXpIRUtOQzZYWW44aElTSXZvayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fX0=', 1754604114),
('xE9W32J37Uelp9L1soFBFa0VNKEsFGmaLcwv7dU2', 15, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWTZoRHlIU0VIUlVMN1lDeHJRcFBPRWhXSHJncWlnZzZoR3VFUlBMdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jbGllbnQvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTU7fQ==', 1754604150),
('XnPCfgzi3mxtAQdkpcxPfJIxoQijNUv41DZ4CxnZ', 18, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoia3hmNkVSbnFPNkt1ZTJteVhBcHh6eEJaMkM3YXJEOHo5aDFFemtFNiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM4OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvY2xpZW50L2Rhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE4O30=', 1754604838),
('ztmvaMhR2tK3rw7cJoWN4IkpO3qorbRImkR1E0yo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidkpmaUx6YkdrbnB0MUtzS3ZRUWdVTjhIdUllN0JzcmxoR29iZ2VCbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1754604658);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `is_admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(14, 'Admin User', 'admin@example.com', '2025-08-07 15:03:40', '$2y$12$nV9CxLciXFYiNSIl9ehUGO5l8JcZ9Y/Ni.9bn9Ea3GLj0krv6qmn6', 1, NULL, '2025-08-07 15:03:40', '2025-08-07 15:03:40'),
(15, 'Demo User', 'demo@example.com', '2025-08-07 15:03:40', '$2y$12$2xeDWAA2JBkMqBpbb47iF./aG2NhKqii/2LtskDP/fE1APX1511qa', 0, NULL, '2025-08-07 15:03:40', '2025-08-07 15:03:40'),
(16, 'Test User', 'test@example.com', '2025-08-07 15:03:41', '$2y$12$YCLznGXTvzWku2zqROdwFexXTqztB83EioioIm9DqxVzekFRePLly', 0, NULL, '2025-08-07 15:03:41', '2025-08-07 15:03:41'),
(18, 'Thor Glass', 'papaj@mailinator.com', NULL, '$2y$12$qPNA1FSkZdOSLFAvD1/3H.334fTif6iTR5kgI6jAfuO7zDPV50L0W', 0, NULL, '2025-08-07 17:11:14', '2025-08-07 17:11:14'),
(19, 'Griffin Webster', 'xepebekupe@mailinator.com', NULL, '$2y$12$rmhMvO5DjMpTEtLmhJsxWe.JYhBquusdiUq4XCHEulcr0xGzS7M8O', 0, NULL, '2025-08-07 17:22:09', '2025-08-07 17:22:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_user_id_foreign` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
