-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 11:42 PM
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
(30, '2025_10_09_184454_rename_duration_weeks_to_duration_months_in_programs_table', 20),
(31, '2025_11_07_194117_merge_booking_limit_into_monthly_sessions', 21),
(32, '2025_11_11_200934_add_contract_and_payment_fields_to_user_programs_table', 22),
(33, '2025_11_11_200937_add_additional_booking_charge_to_programs_table', 22),
(34, '2025_11_11_200938_create_payments_table', 22),
(35, '2025_11_11_202001_add_one_time_payment_amount_to_programs_table', 23),
(36, '2025_11_11_212209_add_agreement_template_path_to_programs_table', 24),
(37, '2025_11_18_212510_add_stripe_fields_to_payments_table', 25),
(38, '2025_11_18_212511_add_stripe_fields_to_user_programs_table', 25),
(39, '2025_11_19_165801_create_settings_table', 26);

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_program_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_type` enum('contract_monthly','contract_one_time','additional_session') NOT NULL DEFAULT 'contract_monthly',
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `stripe_payment_intent_id` varchar(255) DEFAULT NULL,
  `stripe_charge_id` varchar(255) DEFAULT NULL,
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `month_number` int(11) DEFAULT NULL COMMENT 'For monthly payments: 1, 2, or 3',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_program_id`, `appointment_id`, `payment_type`, `status`, `amount`, `payment_reference`, `stripe_payment_intent_id`, `stripe_charge_id`, `stripe_customer_id`, `notes`, `month_number`, `paid_at`, `created_at`, `updated_at`) VALUES
(3, 15, NULL, 'contract_one_time', 'completed', 675.00, 'PAY-SJIVQ4CI-1763509373', 'pi_3SUyXuLXqt7gmBJh0609oDiG', 'ch_3SUyXuLXqt7gmBJh0djl2zNt', 'cus_TRqCsdWtWaGCwc', 'Payment completed via Stripe. Payment Intent: pi_3SUyXuLXqt7gmBJh0609oDiG', NULL, '2025-11-18 18:42:53', '2025-11-18 18:42:53', '2025-11-18 18:42:53'),
(4, 16, NULL, 'contract_monthly', 'completed', 399.00, 'PAY-3HIICHRE-1763510194', 'pi_3SUyl9LXqt7gmBJh0vfi1nfw', 'ch_3SUyl9LXqt7gmBJh0SCaCpbc', 'cus_TRsWSIWur6r7qi', 'Payment completed via Stripe. Payment Intent: pi_3SUyl9LXqt7gmBJh0vfi1nfw', 1, '2025-11-18 18:56:34', '2025-11-18 18:56:34', '2025-11-18 18:56:34');

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
  `additional_booking_charge` decimal(10,2) DEFAULT NULL COMMENT 'Charge for additional 60-minute sessions beyond monthly limit',
  `one_time_payment_amount` decimal(10,2) DEFAULT NULL COMMENT 'Custom one-time payment amount for 3-month contract',
  `agreement_template_path` varchar(255) DEFAULT NULL COMMENT 'Path to program-specific agreement PDF template',
  `is_subscription_based` tinyint(1) NOT NULL DEFAULT 0,
  `subscription_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`subscription_features`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `name`, `description`, `price`, `duration_months`, `sessions_included`, `is_active`, `features`, `subscription_type`, `monthly_price`, `monthly_sessions`, `additional_booking_charge`, `one_time_payment_amount`, `agreement_template_path`, `is_subscription_based`, `subscription_features`, `created_at`, `updated_at`) VALUES
(14, 'Life Coaching', 'Supports development of professional and personal goals in the medical and dental community.', 0.00, NULL, NULL, 1, '[\"2 sessions\\/month\",\"Text-support\"]', 'life_coaching', 399.00, 2, 165.00, 1080.00, 'agreement-templates/agreement_life-coaching_1762896325.pdf', 1, NULL, '2025-11-11 16:01:20', '2025-11-11 16:25:25'),
(15, 'Student Coaching Program', 'This coaching program is designed to support high school, college, and graduate school students in achieving their academic, personal, and professional goals. The program provides personalized coaching to enhance study skills, time management, motivation, stress management, and test taking. Students receive guidance on navigating educational challenges, setting clear goals, and building confidence to succeed in their academic journeys. With a focus on holistic development, the program empowers students to thrive academically while maintaining balance and well-being throughout their education.', 0.00, NULL, NULL, 1, '[\"2 sessions\\/month\",\"Text-support\"]', 'student', 249.00, 2, 125.00, 675.00, 'agreement-templates/agreement_student-coaching-program_1762896305.pdf', 1, NULL, '2025-11-11 16:03:52', '2025-11-11 16:25:05'),
(16, 'Relationship Program', 'This coaching program is designed to support multicultural, multi-ethnic, and cross-cultural couples, marriages, and families globally. It offers a culturally sensitive and inclusive approach to strengthen communication, deepen understanding, and resolve conflicts unique to diverse backgrounds. The program empowers participants to navigate cultural differences with respect and empathy, fostering stronger, more resilient bonds within their relationships and families. Through personalized coaching, practical tools, and insightful guidance, couples and families learn to build connection, harmony, and lasting love across similar or different cultural boundaries.', 0.00, NULL, NULL, 1, '[\"2 sessions\\/month\",\"Text-support\"]', 'relationship', 399.00, 2, 165.00, 1080.00, 'agreement-templates/agreement_relationship-program_1762896296.pdf', 1, NULL, '2025-11-11 16:05:59', '2025-11-11 16:24:56'),
(17, 'Resident Program', 'This coaching program supports residents in medicine and dentistry as they navigate the demanding stages of advanced training. The program provides individualized and confidential coaching to address academic development, clinical performance, work-life balance, wellness, and professional identity formation. Through regular sessions, participants gain actionable feedback, set clear goals, and develop personalized strategies for resilience, communication, and career advancement. Coaches serve as allies and advocates throughout the residency and fellowship journey—helping trainees unlock potential, manage stress, and thrive in both their professional and personal lives.', 0.00, NULL, NULL, 1, '[\"2 sessions\\/month\",\"Text-support\"]', 'resident', 299.00, 2, 150.00, 810.00, 'agreement-templates/agreement_resident-program_1762896281.pdf', 1, NULL, '2025-11-11 16:07:24', '2025-11-11 16:24:41'),
(18, 'Fellow Program', 'This coaching program supports fellows in medicine and dentistry as they navigate the demanding stages of advanced training. The program provides individualized and confidential coaching to address academic development, clinical performance, work-life balance, wellness, and professional identity formation. Through regular sessions, participants gain actionable feedback, set clear goals, and develop personalized strategies for resilience, communication, and career advancement. Coaches serve as allies and advocates throughout the residency and fellowship journey—helping trainees unlock potential, manage stress, and thrive in both their professional and personal lives.', 0.00, NULL, NULL, 1, '[\"2 sessions\\/month\",\"Text-support\"]', 'fellow', 349.00, 2, 165.00, 945.00, 'agreement-templates/agreement_fellow-program_1762896269.pdf', 1, NULL, '2025-11-11 16:08:54', '2025-11-11 16:24:29'),
(19, 'Professional Program', 'This exclusive coaching program serves a select group of healthcare providers, offering personalized support through the unique challenges of clinical practice. Addressing issues such as managing difficult patients and families, coping with long shifts, balancing work-life demands, and strengthening family and partner relationships, this program ensures each provider receives tailored guidance and confidential support. As you remain on call for your patients, let this program be on call for you—providing expert help whenever you need it, restoring balance and resilience so you can thrive in your professional and personal life.', 0.00, NULL, NULL, 1, '[\"2 sessions\\/month\",\"Text-support\"]', 'professional', 379.00, 2, 165.00, 1025.00, 'agreement-templates/agreement_professional-program_1762896260.pdf', 1, NULL, '2025-11-11 16:10:14', '2025-11-11 16:24:20'),
(20, 'Medical Concierge Program', 'This exclusive coaching program serves a select group of healthcare providers, offering personalized support through the unique challenges of clinical practice. Addressing issues such as managing difficult patients and families, coping with long shifts, balancing work-life demands, and strengthening family and partner relationships, this program ensures each provider receives tailored guidance and confidential support. Enrollment is limited to preserve an intimate, high-impact experience. As you remain on call for your patients, let this program be on call for you—providing expert help whenever you need it, restoring balance and resilience so you can thrive in your professional and personal life.', 0.00, NULL, NULL, 1, '[\"3 sessions\\/month\",\"On-call Availability\",\"Text-support\"]', 'concierge', 499.00, 3, 165.00, 1350.00, 'agreement-templates/agreement_medical-concierge-program_1762896239.pdf', 1, NULL, '2025-11-11 16:12:05', '2025-11-11 16:23:59');

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
('CiD2zO82JwITM3x9oQbGJJgcL6HzCqmirBBtRwKA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYVJ4ZDY5dUF0VzZBNUVMamdaZ3FIWEp0RVROcnpCSnpYeXJOekxnNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jbGllbnQvcHJvZ3JhbXMvNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1763579027),
('dzsiNMys32cFLTnUgzDWmngdjqjFvB0frKQ7LWPN', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNGlBNlFFMGh5VHNMQ1lsUjdwTERJVEZwOGEyYURuVUlhdEE1TWQ3WiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9zdWJzY3JpcHRpb25zLWxpc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxNDt9', 1763572487),
('NLy600CBng2HJsuy7OAhvrNoeRO3iadg5khqJVZP', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic0Z5ZU1zc2d3UmZ5cHpOZ2NNMHg5b1htTDFmV2NBd2RERjlFeFZyTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wYXltZW50cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1763571298),
('tUiyN4kl3f6dGkkwgTacpKRGqEpvuFdUWaqt61fe', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib0p4RlpzcU9PejNPYkNrRk5TdVdHUmZWbmtNVk5Ld1RzSUlKbVJPdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wYXltZW50cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE0O30=', 1763510245),
('x7RjD0IUgyw3OsnDjpHWezNKj4NbDyfQ9uzXti0m', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZHlTcHZlOFhpbngzRHR1Ymd5YVUwbkxkeldIM1NTYmtYbVdZUk9qTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wYXltZW50cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1763571299),
('Xv9z02W3HeY8y7K5rAx6OlV4IXHXNPQd5ti5ONEO', 15, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia0dtakU5OW1MQjJLTUM5QW1uZTNzWEpNaWQxdEY3V0dTaFV5a3diSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jbGllbnQvcHJvZ3JhbXMvYWdyZWVtZW50LzE3L2Rvd25sb2FkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTU7fQ==', 1763510313);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES
(1, 'stripe_key', 'YOUR_STRIPE_PUBLISHABLE_KEY_HERE', 'stripe', '2025-11-19 12:03:38', '2025-11-19 12:03:38'),
(2, 'stripe_secret', 'YOUR_STRIPE_SECRET_KEY_HERE', 'stripe', '2025-11-19 12:03:38', '2025-11-19 12:03:38'),
(3, 'stripe_webhook_secret', 'YOUR_STRIPE_WEBHOOK_SECRET_HERE', 'stripe', '2025-11-19 12:03:38', '2025-11-19 12:03:38');

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
(14, 'Admin User', 'admin@example.com', '+1 (555) 000-0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-07 15:03:40', '$2y$12$4NOvhn6kqJm/3frH6D181ud0c1WEDLzLuoLHoFiJkFQFT5upBOKbW', 1, NULL, NULL, NULL, 'Q8GdYBUIRhqJMq94UpSpLfu7XJUNqmQjEGsSNOv8ZTejcavF9VYniK0eFkVR', '2025-08-07 15:03:40', '2025-11-18 16:18:38'),
(15, 'Demo User', 'demo@example.com', '+1 (555) 000-0000', '123 Medical Center Dr, Suite 200, New York, NY 10001', '1985-03-15', 'female', 39, '[\"English\",\"Spanish\"]', 'New York General Hospital', 'Cardiologist', '2020-01-15', 'Interventional Cardiology', NULL, '2010-06-15', '2025-08-07 15:03:40', '$2y$12$yZnjSViyCK3VBS4uC8Ti7..7zm.Shcgr7mxX/vYG6cLQaNMDp/lLW', 0, NULL, NULL, NULL, NULL, '2025-08-07 15:03:40', '2025-11-18 16:15:06'),
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
  `contract_duration_months` int(11) NOT NULL DEFAULT 3,
  `payment_type` enum('monthly','one_time') DEFAULT NULL,
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `next_payment_date` date DEFAULT NULL,
  `total_payments_due` int(11) NOT NULL DEFAULT 3,
  `payments_completed` int(11) NOT NULL DEFAULT 0,
  `one_time_payment_amount` decimal(10,2) DEFAULT NULL,
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `stripe_subscription_id` varchar(255) DEFAULT NULL,
  `stripe_price_id` varchar(255) DEFAULT NULL,
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

INSERT INTO `user_programs` (`id`, `user_id`, `program_id`, `status`, `contract_duration_months`, `payment_type`, `contract_start_date`, `contract_end_date`, `next_payment_date`, `total_payments_due`, `payments_completed`, `one_time_payment_amount`, `stripe_customer_id`, `stripe_subscription_id`, `stripe_price_id`, `admin_notes`, `agreement_path`, `signed_agreement_path`, `signed_agreement_name`, `agreement_sent_at`, `agreement_uploaded_at`, `approved_at`, `payment_requested_at`, `payment_completed_at`, `amount_paid`, `payment_reference`, `created_at`, `updated_at`) VALUES
(15, 15, 15, 'cancelled', 3, 'one_time', '2025-11-18', '2026-02-18', NULL, 3, 1, 675.00, 'cus_TRqCsdWtWaGCwc', 'sub_1SUyUjLXqt7gmBJh78BszdiA', 'price_1SUyUiLXqt7gmBJhVXTMijiG', '\n\n[PAYMENT COMPLETED & PROGRAM ACTIVATED]\nPayment Type: One-Time\nPayment Method: Stripe (Credit Card)\nAmount: $675.00\nPayment Reference: PAY-SJIVQ4CI-1763509373\nStripe Payment Intent: pi_3SUyXuLXqt7gmBJh0609oDiG\nStripe Charge: ch_3SUyXuLXqt7gmBJh0djl2zNt\nBilling Address: 123 Medical Center Dr, Suite 200, New York, NY 10001, San Antonio, Texas 78222, US\nCompleted: 2025-11-18 23:42:53\n\n[CANCELLED BY CLIENT]\nReason: Testing\nCancelled at: 2025-11-18 23:49:49\nStripe Subscription ID: sub_1SUyUjLXqt7gmBJh78BszdiA', 'agreements/agreement_15_1763500590.pdf', 'signed-agreements/signed_agreement_15_1763500619.pdf', 'student-coaching-program_agreement.pdf', '2025-11-18 16:16:31', '2025-11-18 16:16:59', '2025-11-18 16:18:50', NULL, '2025-11-18 18:42:53', 675.00, 'PAY-SJIVQ4CI-1763509373', '2025-11-18 16:16:30', '2025-11-18 18:49:49'),
(16, 15, 16, 'active', 3, 'monthly', '2025-11-18', '2026-02-18', '2025-12-18', 3, 1, NULL, 'cus_TRsWSIWur6r7qi', 'sub_1SUyl7LXqt7gmBJh050FeTRP', 'price_1SUyl6LXqt7gmBJhRtBg101n', '\n\n[PAYMENT COMPLETED & PROGRAM ACTIVATED]\nPayment Type: Monthly\nPayment Method: Stripe (Credit Card)\nAmount: $399.00\nPayment Reference: PAY-3HIICHRE-1763510194\nStripe Payment Intent: pi_3SUyl9LXqt7gmBJh0vfi1nfw\nStripe Charge: ch_3SUyl9LXqt7gmBJh0SCaCpbc\nStripe Subscription: sub_1SUyl7LXqt7gmBJh050FeTRP\nBilling Address: 123 Medical Center Dr, Suite 200, New York, NY 10001, San Antonio, Texas 78222, US\nCompleted: 2025-11-18 23:56:34', 'agreements/agreement_16_1763510126.pdf', 'signed-agreements/signed_agreement_16_1763510145.pdf', 'student-coaching-program_agreement.pdf', '2025-11-18 18:55:26', '2025-11-18 18:55:45', '2025-11-18 18:55:55', NULL, '2025-11-18 18:56:34', 399.00, 'PAY-3HIICHRE-1763510194', '2025-11-18 18:55:26', '2025-11-18 18:56:34'),
(17, 15, 14, 'agreement_sent', 3, NULL, NULL, NULL, NULL, 3, 0, NULL, NULL, NULL, NULL, NULL, 'agreements/agreement_17_1763510301.pdf', NULL, NULL, '2025-11-18 18:58:21', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-18 18:58:21', '2025-11-18 18:58:21');

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_appointment_id_foreign` (`appointment_id`),
  ADD KEY `payments_user_program_id_status_index` (`user_program_id`,`status`),
  ADD KEY `payments_payment_type_status_index` (`payment_type`,`status`),
  ADD KEY `payments_stripe_payment_intent_id_index` (`stripe_payment_intent_id`),
  ADD KEY `payments_stripe_customer_id_index` (`stripe_customer_id`);

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
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

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
  ADD KEY `user_programs_program_id_foreign` (`program_id`),
  ADD KEY `user_programs_stripe_customer_id_index` (`stripe_customer_id`),
  ADD KEY `user_programs_stripe_subscription_id_index` (`stripe_subscription_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_user_program_id_foreign` FOREIGN KEY (`user_program_id`) REFERENCES `user_programs` (`id`) ON DELETE CASCADE;

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
