-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2025 at 09:07 PM
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
  `booking_type` enum('in-office','virtual') NOT NULL DEFAULT 'in-office',
  `message` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `program`, `appointment_date`, `appointment_time`, `booking_type`, `message`, `status`, `created_at`, `updated_at`) VALUES
(31, 38, NULL, '2025-09-18', '16:00', 'in-office', NULL, 'confirmed', '2025-09-16 14:23:34', '2025-09-16 14:23:34'),
(32, 15, NULL, '2025-09-30', '14:30', 'in-office', 'testing', 'confirmed', '2025-09-26 17:33:35', '2025-09-26 17:33:35');

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
  `booking_type` enum('in-office','virtual') NOT NULL DEFAULT 'in-office',
  `message` text DEFAULT NULL,
  `admin_suggestion` text DEFAULT NULL,
  `client_response` text DEFAULT NULL,
  `response_date` timestamp NULL DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `signed_agreement_path` varchar(255) DEFAULT NULL,
  `signed_agreement_name` varchar(255) DEFAULT NULL,
  `agreement_uploaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `client_notes`
--

CREATE TABLE `client_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
(11, '2025_08_07_215234_make_program_nullable_in_appointments_table', 4),
(12, '2025_08_12_191548_add_phone_to_users_table', 5),
(13, '2025_08_13_175337_add_admin_suggestion_to_bookings_table', 6),
(14, '2025_08_13_180954_add_client_response_fields_to_bookings_table', 7),
(15, '2025_08_13_181631_fix_booking_status_column_length', 8),
(16, '2025_08_13_181657_fix_appointment_status_column_length', 9),
(17, '2025_08_13_200000_add_agreement_fields_to_bookings_table', 10),
(18, '2025_08_23_002943_add_agreement_fields_to_users_table', 11),
(19, '2025_08_26_042306_create_client_notes_table', 12),
(20, '2025_01_27_120000_add_professional_fields_to_users_table', 13),
(21, '2025_09_15_231507_create_programs_table', 14),
(22, '2025_09_15_231511_create_user_programs_table', 14),
(23, '2025_09_16_182730_create_subscriptions_table', 15),
(24, '2025_09_16_182808_add_subscription_fields_to_programs_table', 15),
(25, '2025_09_18_233020_add_education_institution_to_users_table', 16),
(26, '2025_09_25_235546_add_booking_type_to_bookings_table', 17),
(27, '2025_09_25_235711_add_booking_type_to_appointments_table', 18),
(28, '2025_09_26_185416_create_slot_schedules_table', 19),
(29, '2025_09_26_185420_create_slot_exceptions_table', 19),
(30, '2025_10_09_184454_rename_duration_weeks_to_duration_months_in_programs_table', 20);

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
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_months` int(11) DEFAULT NULL,
  `sessions_included` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `features` text DEFAULT NULL,
  `subscription_type` varchar(255) DEFAULT NULL,
  `monthly_price` decimal(10,2) DEFAULT NULL,
  `monthly_sessions` int(11) DEFAULT NULL,
  `booking_limit_per_month` int(11) NOT NULL DEFAULT 0,
  `is_subscription_based` tinyint(1) NOT NULL DEFAULT 0,
  `subscription_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`subscription_features`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `name`, `description`, `price`, `duration_months`, `sessions_included`, `is_active`, `features`, `subscription_type`, `monthly_price`, `monthly_sessions`, `booking_limit_per_month`, `is_subscription_based`, `subscription_features`, `created_at`, `updated_at`) VALUES
(4, 'Life Coaching Program', 'This coaching program is designed to support high school, college, and graduate school students in achieving their academic, personal, and professional goals. The program provides personalized coaching to enhance study skills, time management, motivation, stress management and test taking. Students receive guidance on navigating educational challenges, setting clear goals, and building confidence to succeed in their academic journeys. With a focus on holistic development, the program empowers students to thrive academically while maintaining balance and well-being throughout their education.', 249.00, 1, 2, 1, '[\"360-degree feedback analysis\"]', 'student', 249.00, 2, 2, 1, '[\"2 Sessions Per Month\"]', '2025-09-15 18:16:26', '2025-10-09 14:05:35');

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
('8P91xxblMzM3L6vBbcTtd7NOnLRMKjsBKJ67UzIL', 15, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYTBpNUlRZ3RrR2tvMG1PdEFwb3EzYXVjQXFacTc5SHJHc3o2c2N1ZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jbGllbnQvYXBwb2ludG1lbnRzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTU7fQ==', 1759347966),
('bJ8XUss8T5QHoAFjKRGxOY95RUDycpLYmnqKvijs', 43, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoienpzNDJjUXI2QzNTZWtHU0hLTnptWWQ3SHV3VUhCYVVZZzNsQzFETSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jbGllbnQvcHJvZ3JhbXMiO31zOjE4OiJyZWdpc3RyYXRpb25fZW1haWwiO3M6MTk6InNlc3lAbWFpbGluYXRvci5jb20iO3M6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQzO30=', 1760036804),
('bLk6d2JznhQeLvv5rzKJbjqMhIxLk5YxZu7ROnRD', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV1padDlrWEFUZGFCdU9penBnQ2xHT01WazBVc0hNaGlwemd1OGVCRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9ncmFtcy9hcHBsaWNhdGlvbnM/cHJvZ3JhbT00Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTQ7fQ==', 1760036743),
('e7Cp2IjS3O7B4tirghfqJUQQ2k5ORhZo4GS0P9o5', 15, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ2FSTEl6aVJwdGlTSGRheXpZakJMY01XYkVQeGh0Zk4zQ25ZQ1JudCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jbGllbnQvZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTU7fQ==', 1759182697),
('QHsNPbFY4pJW0zRfT78KmdV0DhnbOiNlKWlsvppR', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUzE5T2NkWDB4TXNjYUNjTmM2RmJyeXc0dE9OblMwZmp1UFpRazRiaCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTQ7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9ncmFtcy9hcHBsaWNhdGlvbnMiO319', 1759178932),
('UnsZ1wbKyzBYml3bChasZLkiMS3Y6VQcifuH8hm9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNW1ZSlF1aUFSZTl1TXdEOE51YXBqUFBQMkNmcjJFYXBXTFltN1JZdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9ib29raW5ncyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759347947);

-- --------------------------------------------------------

--
-- Table structure for table `slot_exceptions`
--

CREATE TABLE `slot_exceptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exception_date` date NOT NULL,
  `booking_type` enum('in-office','virtual','both') NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `exception_type` enum('blocked','modified','closed') NOT NULL,
  `reason` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `slot_schedules`
--

CREATE TABLE `slot_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
  `booking_type` enum('in-office','virtual') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slot_duration` int(11) NOT NULL DEFAULT 60,
  `break_duration` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `max_bookings_per_slot` int(11) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slot_schedules`
--

INSERT INTO `slot_schedules` (`id`, `name`, `day_of_week`, `booking_type`, `start_time`, `end_time`, `slot_duration`, `break_duration`, `is_active`, `max_bookings_per_slot`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Default Schedule', 'monday', 'virtual', '10:00:00', '18:00:00', 60, 0, 1, 1, 'Default availability schedule', '2025-09-26 13:56:05', '2025-09-26 13:56:05'),
(2, 'Default Schedule', 'monday', 'in-office', '18:00:00', '21:00:00', 60, 0, 1, 1, 'Default availability schedule', '2025-09-26 13:56:05', '2025-09-26 13:56:05'),
(3, 'Default Schedule', 'tuesday', 'in-office', '08:30:00', '17:00:00', 60, 0, 1, 1, 'Default availability schedule', '2025-09-26 13:56:05', '2025-09-26 13:56:05'),
(4, 'Default Schedule', 'wednesday', 'in-office', '09:00:00', '12:00:00', 60, 0, 1, 1, 'Default availability schedule', '2025-09-26 13:56:05', '2025-09-26 13:56:05'),
(5, 'Default Schedule', 'wednesday', 'virtual', '12:00:00', '17:00:00', 60, 0, 1, 1, 'Default availability schedule', '2025-09-26 13:56:05', '2025-09-26 13:56:05'),
(6, 'Default Schedule', 'thursday', 'in-office', '09:00:00', '12:00:00', 60, 0, 1, 1, 'Default availability schedule', '2025-09-26 13:56:05', '2025-09-26 13:56:05'),
(7, 'Default Schedule', 'thursday', 'virtual', '12:00:00', '17:00:00', 60, 0, 1, 1, 'Default availability schedule', '2025-09-26 13:56:05', '2025-09-26 13:56:05'),
(8, 'Default Schedule', 'friday', 'virtual', '10:00:00', '16:00:00', 60, 0, 1, 1, 'Default availability schedule', '2025-09-26 13:56:05', '2025-09-26 13:56:05');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_type` varchar(255) NOT NULL,
  `monthly_price` decimal(10,2) NOT NULL,
  `monthly_sessions` int(11) NOT NULL,
  `booking_limit_per_month` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `next_billing_date` datetime DEFAULT NULL,
  `last_billing_date` datetime DEFAULT NULL,
  `total_bookings_this_month` int(11) NOT NULL DEFAULT 0,
  `subscription_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`subscription_features`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other','prefer_not_to_say') DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `languages_spoken` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`languages_spoken`)),
  `institution_hospital` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `position_as_of_date` date DEFAULT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  `education_institution` varchar(255) DEFAULT NULL,
  `graduation_date` date DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `signed_agreement_path` varchar(255) DEFAULT NULL,
  `signed_agreement_name` varchar(255) DEFAULT NULL,
  `agreement_uploaded_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `date_of_birth`, `gender`, `age`, `languages_spoken`, `institution_hospital`, `position`, `position_as_of_date`, `specialty`, `education_institution`, `graduation_date`, `email_verified_at`, `password`, `is_admin`, `signed_agreement_path`, `signed_agreement_name`, `agreement_uploaded_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(14, 'Admin User', 'admin@example.com', '+1 (555) 000-0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 15:03:40', '$2y$12$nV9CxLciXFYiNSIl9ehUGO5l8JcZ9Y/Ni.9bn9Ea3GLj0krv6qmn6', 1, NULL, NULL, NULL, 'Q8GdYBUIRhqJMq94UpSpLfu7XJUNqmQjEGsSNOv8ZTejcavF9VYniK0eFkVR', '2025-08-07 15:03:40', '2025-09-13 21:31:06'),
(15, 'Demo User', 'demo@example.com', '+1 (555) 000-0000', '123 Medical Center Dr, Suite 200, New York, NY 10001', '1985-03-15', 'female', 39, '[\"English\",\"Spanish\"]', 'New York General Hospital', 'Cardiologist', '2020-01-15', 'Interventional Cardiology', NULL, '2010-06-15', '2025-08-07 15:03:40', '$2y$12$2xeDWAA2JBkMqBpbb47iF./aG2NhKqii/2LtskDP/fE1APX1511qa', 0, NULL, NULL, NULL, NULL, '2025-08-07 15:03:40', '2025-09-13 21:38:56'),
(16, 'Test User', 'test@example.com', '+1 (555) 000-0000', '456 Healthcare Ave, Floor 5, Los Angeles, CA 90210', '1982-07-22', 'male', 42, '[\"English\",\"Chinese\",\"French\"]', 'Cedars-Sinai Medical Center', 'Emergency Medicine Physician', '2018-09-01', 'Emergency Medicine', NULL, '2008-05-20', '2025-08-07 15:03:41', '$2y$12$YCLznGXTvzWku2zqROdwFexXTqztB83EioioIm9DqxVzekFRePLly', 0, NULL, NULL, NULL, NULL, '2025-08-07 15:03:41', '2025-09-13 21:38:56'),
(38, 'Lareina Love', 'hiqos@mailinator.com', '+1 (414) 201-6348', 'Nemo qui commodi pos', '2001-12-30', 'female', 42, '[\"English\",\"French\",\"Other\"]', 'Qui sed et sunt occa', 'Fugit distinctio F', '1991-07-18', 'Fugiat eligendi qui', NULL, '1970-01-17', NULL, '$2y$12$Hs//s07LotAOyZqr88rZNelVr3hNw1mxI0k8rZXuCNMDiu1s1JrQu', 0, NULL, NULL, NULL, NULL, '2025-09-16 13:22:07', '2025-09-16 13:22:07'),
(39, 'Brittany Sparks', 'wytebypora@mailinator.com', '+1 (868) 576-5411', 'Sit vel at voluptate', '2016-02-05', 'other', 73, '[\"English\",\"French\",\"Mandarin Chinese\",\"Japanese\",\"Other\"]', 'Eveniet ad molestia', 'In doloremque commod', '1982-03-12', 'Natus suscipit nulla', NULL, '1993-12-12', NULL, '$2y$12$20uHo5NVPytw0Mgkzfs5DOriGlIdjZlYLkuDBjpWGJf/oXzqysaUe', 0, NULL, NULL, NULL, NULL, '2025-09-18 18:24:41', '2025-09-18 18:24:41'),
(40, 'Megan Weeks', 'decute@mailinator.com', '+1 (161) 675-3869', 'Dolor ex voluptate m', '1978-12-14', 'male', 60, '[\"English\",\"French\",\"Spanish\",\"Mandarin Chinese\",\"Japanese\",\"Vietnamese\",\"Other\"]', 'Velit ea commodo su', 'Molestiae accusantiu', '1973-08-10', 'Exercitation alias e', 'Accusantium ut nostr', '2012-07-17', NULL, '$2y$12$e25lvl9wrkFoHszfdyauOexwrGV40aUqwXOHifMLS9CNtV9TTQ5/W', 0, NULL, NULL, NULL, NULL, '2025-09-18 18:36:02', '2025-09-18 18:36:02'),
(41, 'Cadman Beard', 'pykyfapu@mailinator.com', '+1 (861) 822-7781', 'Eos pariatur Ut la', '2005-05-05', 'male', 42, '[\"English\",\"Arabic\",\"French\",\"Spanish\",\"Mandarin Chinese\",\"German\",\"Japanese\",\"Vietnamese\",\"Other\"]', 'Eius ea reiciendis t', 'Sint necessitatibus', '1978-05-02', 'Do quo impedit sunt', 'Eaque consequat Inv', '2020-09-28', NULL, '$2y$12$.7MiNwidN9XcQtihdVzjj.a6wX.RVf8OPkeqwpRurTzxJfqM4A2qy', 0, NULL, NULL, NULL, NULL, '2025-09-29 15:26:35', '2025-09-29 15:26:35'),
(42, 'Oleg Wyatt', 'gowowady@mailinator.com', '+1 (997) 522-3529', 'Tempore beatae cill', '2013-03-12', 'prefer_not_to_say', 30, '[\"German\",\"Vietnamese\"]', 'Tempora quia volupta', 'Sit quia beatae rep', '1973-05-05', 'Atque ex rerum et cu', 'Do est commodi asper', '2004-10-01', NULL, '$2y$12$aSFXXeffv82YlNwSdlBTfOgbi7i8gyQhY.CAUzkP0oWclAFyG6uQW', 0, NULL, NULL, NULL, NULL, '2025-10-09 13:06:57', '2025-10-09 13:06:57'),
(43, 'Mercedes Wilcox', 'sesy@mailinator.com', '+1 (243) 953-8292', 'Sunt modi veniam in', '1991-12-21', 'female', 91, '[\"English\",\"German\"]', 'Aute autem delectus', 'Dolor id ea ipsum p', '1980-12-28', 'Nihil maxime vel atq', 'Quis et et proident', '2018-03-20', NULL, '$2y$12$pPHrNCXsts0Ql3hDz6Pef.i1EeiMbGQeNEnQM6CgS4F36oeEsU0Dq', 0, NULL, NULL, NULL, NULL, '2025-10-09 13:29:00', '2025-10-09 13:29:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_programs`
--

CREATE TABLE `user_programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','agreement_sent','agreement_uploaded','approved','payment_requested','payment_completed','active','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `agreement_path` varchar(255) DEFAULT NULL,
  `signed_agreement_path` varchar(255) DEFAULT NULL,
  `signed_agreement_name` varchar(255) DEFAULT NULL,
  `agreement_sent_at` timestamp NULL DEFAULT NULL,
  `agreement_uploaded_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `payment_requested_at` timestamp NULL DEFAULT NULL,
  `payment_completed_at` timestamp NULL DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_programs`
--

INSERT INTO `user_programs` (`id`, `user_id`, `program_id`, `status`, `admin_notes`, `agreement_path`, `signed_agreement_path`, `signed_agreement_name`, `agreement_sent_at`, `agreement_uploaded_at`, `approved_at`, `payment_requested_at`, `payment_completed_at`, `amount_paid`, `payment_reference`, `created_at`, `updated_at`) VALUES
(5, 38, 4, 'active', NULL, 'agreements/agreement_5_1758049437.pdf', 'signed-agreements/signed_agreement_5_1758049471.pdf', 'program_agreement_leadership-excellence-program_lareina-love.pdf', '2025-09-16 14:03:58', '2025-09-16 14:04:31', '2025-09-16 14:19:43', '2025-09-16 14:19:52', '2025-09-16 14:20:08', 499.00, 'jhgjhg', '2025-09-16 14:03:46', '2025-09-16 14:20:12'),
(6, 39, 4, 'active', NULL, 'agreements/agreement_6_1758238643.pdf', 'signed-agreements/signed_agreement_6_1758238691.pdf', 'program_agreement_leadership-excellence-program_brittany-sparks.pdf', '2025-09-18 18:37:25', '2025-09-18 18:38:11', '2025-09-18 18:38:32', '2025-09-18 18:38:58', '2025-09-18 18:39:34', 499.00, '432432', '2025-09-18 18:36:13', '2025-09-18 18:39:41'),
(7, 15, 4, 'active', '\n\n[CANCELLED BY CLIENT] Reason: Not wanted - 2025-09-29 20:46:39\n\n[RE-SELECTED BY CLIENT] Program re-selected after cancellation - 2025-09-29 20:46:42', 'agreements/agreement_7_1759178868.pdf', 'signed-agreements/signed_agreement_7_1759178896.pdf', 'program_agreement_career-development-program_kennan-frederick (1).pdf', '2025-09-29 15:47:50', '2025-09-29 15:48:16', '2025-09-29 15:48:36', '2025-09-29 15:48:39', '2025-09-29 15:48:48', 499.00, '123', '2025-09-26 13:22:05', '2025-09-29 15:48:52'),
(8, 41, 4, 'cancelled', '\n\n[CANCELLED BY CLIENT] Reason: I want a different program. - 2025-09-29 20:32:20\n\n[RE-SELECTED BY CLIENT] Program re-selected after cancellation - 2025-09-29 20:41:02\n\n[CANCELLED BY CLIENT] Reason: No Reason - 2025-09-29 20:41:36\n\n[RE-SELECTED BY CLIENT] Program re-selected after cancellation - 2025-09-29 20:42:38\n\n[CANCELLED BY CLIENT] Reason: I want a different program - 2025-09-29 20:44:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-29 15:27:31', '2025-09-29 15:44:11'),
(9, 42, 4, 'agreement_sent', '\n\n[CANCELLED BY CLIENT] Reason: I dont need it - 2025-10-09 18:20:33\n\n[RE-SELECTED BY CLIENT] Program re-selected after cancellation - 2025-10-09 18:20:38', 'agreements/agreement_9_1760034049.pdf', NULL, NULL, '2025-10-09 13:20:49', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-09 13:12:00', '2025-10-09 13:20:49'),
(10, 43, 4, 'agreement_uploaded', NULL, 'agreements/agreement_10_1760034641.pdf', 'signed-agreements/signed_agreement_10_1760036791.pdf', 'life_coaching_contract (1).pdf', '2025-10-09 13:30:41', '2025-10-09 14:06:31', NULL, NULL, NULL, NULL, NULL, '2025-10-09 13:30:27', '2025-10-09 14:06:31');

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
-- Indexes for table `client_notes`
--
ALTER TABLE `client_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_notes_admin_id_foreign` (`admin_id`),
  ADD KEY `client_notes_user_id_created_at_index` (`user_id`,`created_at`);

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
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `slot_exceptions`
--
ALTER TABLE `slot_exceptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slot_exceptions_exception_date_booking_type_is_active_index` (`exception_date`,`booking_type`,`is_active`);

--
-- Indexes for table `slot_schedules`
--
ALTER TABLE `slot_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_day_type_time` (`day_of_week`,`booking_type`,`start_time`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriptions_user_id_is_active_index` (`user_id`,`is_active`),
  ADD KEY `subscriptions_program_id_is_active_index` (`program_id`,`is_active`),
  ADD KEY `subscriptions_next_billing_date_index` (`next_billing_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_programs`
--
ALTER TABLE `user_programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_programs_user_id_foreign` (`user_id`),
  ADD KEY `user_programs_program_id_foreign` (`program_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `client_notes`
--
ALTER TABLE `client_notes`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `slot_exceptions`
--
ALTER TABLE `slot_exceptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `slot_schedules`
--
ALTER TABLE `slot_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `user_programs`
--
ALTER TABLE `user_programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_notes`
--
ALTER TABLE `client_notes`
  ADD CONSTRAINT `client_notes_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_programs`
--
ALTER TABLE `user_programs`
  ADD CONSTRAINT `user_programs_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_programs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
