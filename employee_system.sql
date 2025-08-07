-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 07, 2025 at 01:16 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `employee_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `status` enum('Present','Absent','Late','Half Day') DEFAULT 'Present',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `prefix` varchar(255) DEFAULT NULL,
  `ssm_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `prefix`, `ssm_number`, `address`, `contact_person`, `contact_email`, `contact_phone`, `created_at`, `updated_at`) VALUES
(1, 'Julang Network', 'JN', 'AS0459331-X', '10-11(1),Stellar Suites, Jln Puteri 4/7, Bandar Puteri, 47100 Puchong, Selangor', 'Danny', 'julangnetwork@gmail.com', '0128559967', '2025-05-15 12:36:39', '2025-05-15 12:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `company_acknowledgments`
--

CREATE TABLE `company_acknowledgments` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `granted_by` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compensation`
--

CREATE TABLE `compensation` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL,
  `effective_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `currency_id` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `allowance` decimal(10,2) DEFAULT 0.00,
  `overtime` decimal(10,2) DEFAULT 0.00,
  `epf_employee` decimal(10,2) DEFAULT 0.00,
  `socso_employee` decimal(10,2) DEFAULT 0.00,
  `eis_employee` decimal(10,2) DEFAULT 0.00,
  `pcb` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compensation`
--

INSERT INTO `compensation` (`id`, `employee_id`, `hourly_rate`, `monthly_salary`, `effective_date`, `created_by`, `currency_id`, `created_at`, `updated_at`, `allowance`, `overtime`, `epf_employee`, `socso_employee`, `eis_employee`, `pcb`) VALUES
(2, 2, 0.00, 2800.00, '2025-08-07', 1, 2, '2025-08-07 00:35:40', '2025-08-07 00:35:40', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(5, 1, 0.00, 3500.00, '2025-08-07', 1, 2, '2025-08-07 13:08:28', '2025-08-07 13:08:28', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(6, 1, 0.00, 4000.00, '2025-08-07', 1, 2, '2025-08-07 13:09:01', '2025-08-07 13:09:01', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `currency_symbol` varchar(5) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `country_name`, `currency_code`, `currency_symbol`, `status`, `created_at`, `updated_at`) VALUES
(1, 'United States', 'USD', '$', 'inactive', '2025-05-15 16:21:57', NULL),
(2, 'Malaysia', 'MYR', 'RM', 'active', '2025-05-15 16:21:57', NULL),
(3, 'Singapore', 'SGD', 'S$', 'inactive', '2025-05-15 16:21:57', NULL),
(4, 'China', 'CNY', '¥', 'inactive', '2025-05-15 16:21:57', '2025-08-07 00:48:02'),
(5, 'Hong Kong', 'HKD', 'HK$', 'inactive', '2025-05-15 16:21:57', NULL),
(6, 'United Kingdom', 'GBP', '£', 'inactive', '2025-05-15 16:21:57', NULL),
(7, 'Euro Zone', 'EUR', '€', 'inactive', '2025-05-15 16:21:57', NULL),
(8, 'Japan', 'JPY', '¥', 'inactive', '2025-05-15 16:21:57', NULL),
(9, 'Australia', 'AUD', 'A$', 'inactive', '2025-05-15 16:21:57', '2025-05-15 17:28:33'),
(10, 'India', 'INR', '₹', 'inactive', '2025-05-15 16:21:57', NULL),
(11, 'Thailand', 'THB', '฿', 'inactive', '2025-05-15 16:21:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `hire_date` date NOT NULL,
  `status` enum('Active','On Leave','Terminated') DEFAULT 'Active',
  `id_type` enum('Passport','NRIC') DEFAULT NULL,
  `id_number` varchar(20) DEFAULT NULL,
  `passport_file` varchar(255) DEFAULT NULL,
  `nric_front` varchar(255) DEFAULT NULL,
  `nric_back` varchar(255) DEFAULT NULL,
  `offer_letter` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `company_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `first_name`, `last_name`, `phone`, `address`, `emergency_contact`, `date_of_birth`, `hire_date`, `status`, `id_type`, `id_number`, `passport_file`, `nric_front`, `nric_back`, `offer_letter`, `department`, `position`, `email`, `company_id`, `created_at`, `updated_at`, `bank_name`, `bank_account`) VALUES
(1, 6, 'YEW MING', 'LIEW', '+60167515154', 'E67-1A, Lengkongan 5, Kampung Baru 81000 Kulai Jaya, Johor', '', '1989-12-04', '2024-05-03', 'Active', 'NRIC', '891204016031', NULL, 'nric_front_yew_ming_liew_1754497206_2033.jpeg', 'nric_back_yew_ming_liew_1754497206_2542.jpeg', 'offer_letter_yew_ming_liew_1754497206_5088.pdf', 'Account Department', 'Account Manager', 'yuhengming1989@hotmail.com', 1, '2025-08-07 00:20:06', '2025-08-07 00:20:06', 'Public Bank', '3179871403'),
(2, 7, 'SOOK TING', 'TAN', '+60198330523', 'No 78, Jalan Tembaga 1/6, Bukit Desa Country Height 70200 Seremban, Negeri Sembilan', '', '2001-05-23', '2024-05-15', 'Active', 'NRIC', '010523050242', NULL, 'nric_front_sook_ting_tan_1754498140_8539.jpeg', 'nric_back_sook_ting_tan_1754498140_8162.jpeg', 'offer_letter_sook_ting_tan_1754498140_3930.pdf', 'Account Department', 'Account Assistant', 'carentan0523@gmail.com', 1, '2025-08-07 00:35:40', '2025-08-07 00:35:40', 'Maybank', '162433074010');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_date` date NOT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `status` enum('active','cancelled','completed') NOT NULL DEFAULT 'active',
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

CREATE TABLE `payslips` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month` varchar(10) NOT NULL,
  `year` int(4) NOT NULL,
  `basic_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `allowance` decimal(10,2) DEFAULT 0.00,
  `overtime` decimal(10,2) DEFAULT 0.00,
  `epf_employee` decimal(10,2) DEFAULT 0.00,
  `socso_employee` decimal(10,2) DEFAULT 0.00,
  `eis_employee` decimal(10,2) DEFAULT 0.00,
  `pcb` decimal(10,2) DEFAULT 0.00,
  `total_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pay_date` date NOT NULL,
  `working_days` int(3) NOT NULL DEFAULT 0,
  `generated_by` int(11) NOT NULL,
  `currency_id` int(11) DEFAULT 1,
  `status` enum('generated','paid','cancelled') NOT NULL DEFAULT 'generated',
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'Admin', '{\"all\": true}'),
(2, 'Company', '{\"view_dashboard\": true, \"view_employees\": true, \"create_employees\": true, \"edit_employees\": true, \"delete_employees\": true, \"view_attendance\": true, \"create_attendance\": true, \"edit_attendance\": true, \"delete_attendance\": true, \"view_attendance_report\": true, \"view_compensation\": true, \"create_compensation\": true, \"edit_compensation\": true, \"view_users\": true, \"create_users\": true, \"edit_users\": true, \"delete_users\": true}'),
(3, 'Sub-account', '{\"view_dashboard\": true, \"view_employees\": true, \"view_attendance\": true, \"create_attendance\": true, \"edit_attendance\": true, \"view_attendance_report\": true, \"view_compensation\": true}'),
(7, 'Employee', '{\"view_dashboard\": true, \"clock_attendance\": true, \"view_attendance\": true}');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `company_id` int(11) DEFAULT NULL,
  `permission_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role_id`, `created_by`, `created_at`, `updated_at`, `company_id`, `permission_updated_at`) VALUES
(1, 'julangadmin', '$2y$10$P69U7dzs6JYyNQ3m67.3b.lcujW0M2CP5tHjnGoN2K9h2uuBfO87a', 'julangnetwork@gmail.com', 1, NULL, '2025-05-15 11:52:10', '2025-07-09 14:35:21', NULL, NULL),
(6, 'yliew', '$2y$10$zWvRw6v9LICtxKdkreO44OWi8ATr.JUpVMrG6gZwgQJn/bf34R4wC', 'yuhengming1989@hotmail.com', 7, 1, '2025-08-07 00:20:06', '2025-08-07 00:20:06', 1, NULL),
(7, 'stan', '$2y$10$iZ5HAis3P.0Wh/Yy1O0CUOUxtuWfBoUEuU3V0GKggHXGfvt.S1xHq', 'carentan0523@gmail.com', 7, 1, '2025-08-07 00:35:40', '2025-08-07 00:35:40', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_companies`
--

CREATE TABLE `user_companies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_acknowledgments`
--
ALTER TABLE `company_acknowledgments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_user_unique` (`company_id`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `granted_by` (`granted_by`);

--
-- Indexes for table `compensation`
--
ALTER TABLE `compensation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `fk_compensation_currency` (`currency_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currency_code` (`currency_code`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `payslips`
--
ALTER TABLE `payslips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `month_year_employee` (`month`,`year`,`employee_id`),
  ADD KEY `fk_payslips_currency` (`currency_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `user_companies`
--
ALTER TABLE `user_companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_company_unique` (`user_id`,`company_id`),
  ADD KEY `user_companies_ibfk_2` (`company_id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_acknowledgments`
--
ALTER TABLE `company_acknowledgments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compensation`
--
ALTER TABLE `compensation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_companies`
--
ALTER TABLE `user_companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `company_acknowledgments`
--
ALTER TABLE `company_acknowledgments`
  ADD CONSTRAINT `company_acknowledgments_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_acknowledgments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `compensation`
--
ALTER TABLE `compensation`
  ADD CONSTRAINT `compensation_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `compensation_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_compensation_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payslips`
--
ALTER TABLE `payslips`
  ADD CONSTRAINT `fk_payslips_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  ADD CONSTRAINT `payslips_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_companies`
--
ALTER TABLE `user_companies`
  ADD CONSTRAINT `user_companies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_companies_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
