-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2026 at 05:00 AM
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
-- Database: `odoo_pos_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('restopos-cache-varunbaradia.24.cse@iite.indusuni.ac.in|127.0.0.1', 'i:1;', 1775289602),
('restopos-cache-varunbaradia.24.cse@iite.indusuni.ac.in|127.0.0.1:timer', 'i:1775289602;', 1775289602);

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
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#6366f1',
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `color`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Drinks', '#3b82f6', 3, '2026-04-04 01:33:15', '2026-04-05 00:17:00'),
(2, 'Quickies', '#3b82f6', 3, '2026-04-04 01:33:42', '2026-04-04 06:53:55'),
(3, 'Munchies', '#ec4899', 1, '2026-04-04 02:05:15', '2026-04-04 06:53:55'),
(5, 'Chinese', '#22c55e', 2, '2026-04-04 04:12:51', '2026-04-04 06:53:55'),
(6, 'Quick Bites', '#f97316', 1, '2026-04-05 00:17:00', '2026-04-05 00:17:00'),
(7, 'Main Course', '#ef4444', 2, '2026-04-05 00:17:00', '2026-04-05 00:17:00'),
(8, 'Desserts', '#a855f7', 4, '2026-04-05 00:17:00', '2026-04-05 00:17:00'),
(9, 'Pasta', '#f59e0b', 5, '2026-04-05 00:17:00', '2026-04-05 00:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `category_product`
--

CREATE TABLE `category_product` (
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_product`
--

INSERT INTO `category_product` (`category_id`, `product_id`) VALUES
(1, 1),
(1, 10),
(1, 11),
(1, 12),
(2, 2),
(3, 1),
(5, 3),
(5, 4),
(6, 5),
(6, 6),
(6, 15),
(7, 7),
(7, 8),
(8, 13),
(8, 14),
(9, 9);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `street1` varchar(255) DEFAULT NULL,
  `street2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'India',
  `total_sales` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `street1`, `street2`, `city`, `state`, `country`, `total_sales`, `created_at`, `updated_at`) VALUES
(1, 'Varun Baradia', 'varun.baradia@gmail.com', '7405612501', 'Shiv Park 3', NULL, 'Rajkot', 'Gujarat', 'India', 0.00, '2026-04-04 16:52:21', '2026-04-04 16:52:21'),
(2, 'Manoj sharma', 'manoj.sharma@gg.com', '5421542154', NULL, NULL, 'Jamnagar', 'Gujarat', 'India', 2530.00, '2026-04-04 16:59:04', '2026-04-04 16:59:13');

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
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Ground Floor', '2026-04-04 02:59:07', '2026-04-04 02:59:07'),
(2, 'First Floor', '2026-04-04 02:59:19', '2026-04-04 02:59:19'),
(3, 'Second Floor', '2026-04-04 03:06:33', '2026-04-04 03:06:33'),
(4, 'Third Floor', '2026-04-04 16:12:37', '2026-04-04 16:12:37');

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
(4, '2026_04_04_064352_create_categories_table', 1),
(5, '2026_04_04_064415_create_products_table', 1),
(6, '2026_04_04_064434_create_product_variants_table', 1),
(7, '2026_04_04_072908_add_sort_order_to_categories_table', 2),
(8, '2026_04_04_073018_add_sort_order_to_categories_table', 2),
(9, '2026_04_04_074520_create_category_product_table', 3),
(10, '2026_04_04_081743_create_floors_table', 4),
(11, '2026_04_04_081816_create_tables_table', 4),
(12, '2026_04_04_095217_create_pos_configs_table', 5),
(13, '2026_04_10_000001_create_pos_sessions_table', 6),
(14, '2026_04_10_000002_create_orders_table', 6),
(15, '2026_04_10_000003_create_order_items_table', 6),
(16, '2026_04_04_104948_add_tax_rate_to_order_items_table', 7),
(17, '2026_04_10_000004_add_customer_name_to_orders_table', 8),
(18, '2026_04_10_000005_create_customers_table', 9),
(19, '2026_04_10_000006_add_customer_id_to_orders_table', 9),
(20, '2026_04_10_000007_add_done_to_order_items_table', 10),
(21, '2026_04_05_022927_add_mobile_order_to_pos_configs_table', 11),
(22, '2026_04_05_025217_add_qr_token_to_tables_table', 12),
(23, '2026_04_05_041101_add_role_to_users_table', 13);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pos_session_id` bigint(20) UNSIGNED NOT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `status` enum('pending','preparing','ready','paid','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `pos_session_id`, `table_id`, `customer_id`, `customer_name`, `status`, `subtotal`, `tax`, `total`, `payment_method`, `notes`, `created_at`, `updated_at`) VALUES
(5, 1, 5, NULL, NULL, 'paid', 2050.00, 189.00, 2239.00, 'UPI', NULL, '2026-04-04 06:13:59', '2026-04-04 13:34:02'),
(6, 1, 3, NULL, NULL, 'paid', 750.00, 37.50, 787.50, 'UPI', NULL, '2026-04-04 06:14:24', '2026-04-04 13:34:02'),
(12, 1, 9, NULL, NULL, 'paid', 2250.00, 112.50, 2362.50, 'cash', NULL, '2026-04-04 07:11:22', '2026-04-04 13:34:02'),
(13, 3, 4, NULL, NULL, 'paid', 3300.00, 330.00, 3630.00, 'UPI', NULL, '2026-04-04 13:35:32', '2026-04-04 13:35:50'),
(14, 4, 11, NULL, NULL, 'paid', 4720.00, 448.50, 5168.50, 'cash', 'Do not make it Spicy', '2026-04-04 13:37:02', '2026-04-04 13:42:21'),
(15, 5, 4, NULL, NULL, 'paid', 300.00, 30.00, 330.00, 'cash', NULL, '2026-04-04 13:47:44', '2026-04-04 13:55:33'),
(16, 6, 1, NULL, NULL, 'paid', 480.00, 48.00, 528.00, 'cash', NULL, '2026-04-04 13:56:21', '2026-04-04 13:56:30'),
(17, 7, 3, NULL, NULL, 'paid', 2365.00, 236.50, 2601.50, 'cash', NULL, '2026-04-04 14:13:41', '2026-04-04 14:18:47'),
(18, 7, 3, NULL, 'Manoj', 'paid', 2365.00, 236.50, 2601.50, 'cash', NULL, '2026-04-04 14:17:13', '2026-04-04 14:18:47'),
(19, 8, 11, NULL, NULL, 'paid', 40.00, 2.00, 42.00, 'cash', NULL, '2026-04-04 14:22:03', '2026-04-04 23:05:10'),
(20, 8, 3, NULL, 'Krishh', 'paid', 140.00, 7.00, 147.00, 'cash', NULL, '2026-04-04 14:22:31', '2026-04-04 23:05:10'),
(21, 8, 5, NULL, NULL, 'paid', 1630.00, 152.50, 1782.50, 'cash', NULL, '2026-04-04 14:25:57', '2026-04-04 23:05:10'),
(22, 8, 11, NULL, NULL, 'paid', 550.00, 42.50, 592.50, 'cash', NULL, '2026-04-04 16:13:12', '2026-04-04 23:05:10'),
(23, 8, 4, NULL, NULL, 'paid', 465.00, 34.00, 499.00, 'cash', NULL, '2026-04-04 16:56:11', '2026-04-04 23:05:10'),
(24, 8, 5, 2, 'Manoj sharma', 'paid', 2300.00, 230.00, 2530.00, 'cash', NULL, '2026-04-04 16:59:12', '2026-04-04 23:05:10'),
(25, 8, 11, NULL, NULL, 'paid', 2260.00, 184.00, 2444.00, 'cash', NULL, '2026-04-04 17:57:51', '2026-04-04 23:05:10'),
(26, 8, 5, NULL, NULL, 'paid', 1060.00, 53.00, 1113.00, 'cash', NULL, '2026-04-04 18:07:17', '2026-04-04 23:05:10'),
(27, 8, 1, NULL, NULL, 'paid', 960.00, 96.00, 1056.00, 'cash', NULL, '2026-04-04 18:07:43', '2026-04-04 23:05:10'),
(28, 8, 4, NULL, NULL, 'paid', 1010.00, 101.00, 1111.00, 'cash', NULL, '2026-04-04 20:50:54', '2026-04-04 23:05:10'),
(29, 8, 11, NULL, NULL, 'paid', 2850.00, 237.00, 3087.00, 'cash', NULL, '2026-04-04 22:05:12', '2026-04-04 23:05:10'),
(30, 8, 11, NULL, NULL, 'paid', 2400.00, 202.50, 2602.50, 'cash', NULL, '2026-04-04 22:14:15', '2026-04-04 23:05:10'),
(31, 8, 11, NULL, NULL, 'paid', 1440.00, 144.00, 1584.00, 'cash', NULL, '2026-04-04 22:24:34', '2026-04-04 23:05:10'),
(32, 9, 11, NULL, NULL, 'preparing', 900.00, 90.00, 990.00, NULL, NULL, '2026-04-04 23:28:38', '2026-04-04 23:28:38'),
(33, 9, 1, NULL, NULL, 'paid', 750.00, 25.50, 775.50, 'cash', NULL, '2026-04-04 23:41:00', '2026-04-05 00:17:00'),
(34, 9, 2, NULL, NULL, 'paid', 500.00, 15.00, 515.00, 'upi', NULL, '2026-04-04 21:36:00', '2026-04-05 00:17:01'),
(35, 9, 3, NULL, NULL, 'paid', 1230.00, 109.40, 1339.40, 'card', NULL, '2026-04-04 21:53:01', '2026-04-05 00:17:01'),
(36, 9, 4, NULL, NULL, 'preparing', 680.00, 28.00, 708.00, NULL, NULL, '2026-04-04 22:17:01', '2026-04-05 00:17:01'),
(37, 9, 5, NULL, NULL, 'pending', 460.00, 0.00, 460.00, NULL, NULL, '2026-04-04 21:17:01', '2026-04-05 00:17:01');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `variant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `done` tinyint(1) NOT NULL DEFAULT 0,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `variant_id`, `name`, `price`, `quantity`, `done`, `subtotal`, `tax_rate`, `tax_amount`, `created_at`, `updated_at`) VALUES
(11, 5, 3, 21, 'Chinese Bhel (Half Plate)', 250.00, 1, 0, 250.00, 5.00, 12.50, '2026-04-04 06:13:59', '2026-04-04 06:13:59'),
(12, 5, 1, NULL, 'Coca Cola (500 ML)', 70.00, 1, 0, 70.00, 5.00, 3.50, '2026-04-04 06:13:59', '2026-04-04 06:13:59'),
(13, 5, 2, 16, 'Pani Puri (18 Pcs)', 80.00, 1, 0, 80.00, 10.00, 8.00, '2026-04-04 06:13:59', '2026-04-04 06:13:59'),
(14, 5, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 3, 0, 1650.00, 10.00, 165.00, '2026-04-04 06:13:59', '2026-04-04 06:13:59'),
(15, 6, 3, NULL, 'Chinese Bhel', 250.00, 3, 0, 750.00, 5.00, 37.50, '2026-04-04 06:14:24', '2026-04-04 06:14:24'),
(21, 12, 3, 21, 'Chinese Bhel (Half Plate)', 250.00, 9, 0, 2250.00, 5.00, 112.50, '2026-04-04 07:11:22', '2026-04-04 07:11:22'),
(22, 13, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 6, 0, 3300.00, 10.00, 330.00, '2026-04-04 13:35:33', '2026-04-04 13:35:33'),
(23, 14, 4, NULL, 'Chilli Potato', 300.00, 5, 0, 1500.00, 10.00, 150.00, '2026-04-04 13:37:02', '2026-04-04 13:37:02'),
(24, 14, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 5, 0, 2750.00, 10.00, 275.00, '2026-04-04 13:37:02', '2026-04-04 13:37:02'),
(25, 14, 1, NULL, 'Coca Cola', 20.00, 2, 0, 40.00, 5.00, 2.00, '2026-04-04 13:37:02', '2026-04-04 13:37:02'),
(26, 14, 1, NULL, 'Coca Cola (200 ML)', 40.00, 2, 0, 80.00, 5.00, 4.00, '2026-04-04 13:37:02', '2026-04-04 13:37:02'),
(27, 14, 1, NULL, 'Coca Cola (500 ML)', 70.00, 5, 0, 350.00, 5.00, 17.50, '2026-04-04 13:37:02', '2026-04-04 13:37:02'),
(28, 15, 4, NULL, 'Chilli Potato', 300.00, 1, 0, 300.00, 10.00, 30.00, '2026-04-04 13:47:44', '2026-04-04 13:47:44'),
(29, 16, 2, 16, 'Pani Puri (18 Pcs)', 80.00, 6, 0, 480.00, 10.00, 48.00, '2026-04-04 13:56:21', '2026-04-04 13:56:21'),
(30, 17, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 4, 0, 2200.00, 10.00, 220.00, '2026-04-04 14:13:41', '2026-04-04 14:13:41'),
(31, 17, 2, 15, 'Pani Puri (12 Pcs)', 55.00, 3, 0, 165.00, 10.00, 16.50, '2026-04-04 14:13:41', '2026-04-04 14:13:41'),
(32, 18, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 4, 0, 2200.00, 10.00, 220.00, '2026-04-04 14:17:13', '2026-04-04 14:17:13'),
(33, 18, 2, 15, 'Pani Puri (12 Pcs)', 55.00, 3, 0, 165.00, 10.00, 16.50, '2026-04-04 14:17:13', '2026-04-04 14:17:13'),
(35, 19, 1, NULL, 'Coca Cola (200 ML)', 40.00, 1, 0, 40.00, 5.00, 2.00, '2026-04-04 14:22:14', '2026-04-04 14:22:14'),
(36, 20, 1, NULL, 'Coca Cola (500 ML)', 70.00, 2, 0, 140.00, 5.00, 7.00, '2026-04-04 14:22:32', '2026-04-04 14:22:32'),
(41, 21, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 2, 0, 1100.00, 10.00, 110.00, '2026-04-04 14:26:51', '2026-04-04 14:26:51'),
(42, 21, 1, NULL, 'Coca Cola (500 ML)', 70.00, 3, 0, 210.00, 5.00, 10.50, '2026-04-04 14:26:51', '2026-04-04 14:26:51'),
(43, 21, 2, 16, 'Pani Puri (18 Pcs)', 80.00, 4, 0, 320.00, 10.00, 32.00, '2026-04-04 14:26:51', '2026-04-04 14:26:51'),
(46, 22, 4, NULL, 'Chilli Potato', 300.00, 1, 0, 300.00, 10.00, 30.00, '2026-04-04 16:13:34', '2026-04-04 16:13:34'),
(47, 22, 3, NULL, 'Chinese Bhel', 250.00, 1, 0, 250.00, 5.00, 12.50, '2026-04-04 16:13:34', '2026-04-04 16:13:34'),
(51, 24, 4, NULL, 'Chilli Potato', 300.00, 4, 0, 1200.00, 10.00, 120.00, '2026-04-04 16:59:12', '2026-04-04 16:59:12'),
(52, 24, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 2, 0, 1100.00, 10.00, 110.00, '2026-04-04 16:59:12', '2026-04-04 16:59:12'),
(53, 23, 2, 15, 'Pani Puri (12 Pcs)', 55.00, 1, 0, 55.00, 10.00, 5.50, '2026-04-04 17:57:03', '2026-04-04 17:57:03'),
(54, 23, 2, 16, 'Pani Puri (18 Pcs)', 80.00, 2, 0, 160.00, 10.00, 16.00, '2026-04-04 17:57:03', '2026-04-04 17:57:03'),
(55, 23, 3, NULL, 'Chinese Bhel', 250.00, 1, 0, 250.00, 5.00, 12.50, '2026-04-04 17:57:03', '2026-04-04 17:57:03'),
(61, 25, 1, NULL, 'Coca Cola', 20.00, 3, 0, 60.00, 5.00, 3.00, '2026-04-04 18:06:46', '2026-04-04 18:06:46'),
(62, 25, 1, 24, 'Coca Cola (500 ML)', 70.00, 4, 0, 280.00, 5.00, 14.00, '2026-04-04 18:06:46', '2026-04-04 18:06:46'),
(63, 25, 2, 16, 'Pani Puri (18 Pcs)', 80.00, 4, 0, 320.00, 10.00, 32.00, '2026-04-04 18:06:46', '2026-04-04 18:06:46'),
(64, 25, 3, 21, 'Chinese Bhel (Half Plate)', 250.00, 2, 0, 500.00, 5.00, 25.00, '2026-04-04 18:06:46', '2026-04-04 18:06:46'),
(65, 25, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 2, 0, 1100.00, 10.00, 110.00, '2026-04-04 18:06:46', '2026-04-04 18:06:46'),
(66, 26, 3, 21, 'Chinese Bhel (Half Plate)', 250.00, 2, 0, 500.00, 5.00, 25.00, '2026-04-04 18:07:17', '2026-04-04 18:07:17'),
(67, 26, 1, 24, 'Coca Cola (500 ML)', 70.00, 8, 0, 560.00, 5.00, 28.00, '2026-04-04 18:07:17', '2026-04-04 18:07:17'),
(68, 27, 2, 16, 'Pani Puri (18 Pcs)', 80.00, 12, 0, 960.00, 10.00, 96.00, '2026-04-04 18:07:43', '2026-04-04 18:07:43'),
(75, 28, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 1, 0, 550.00, 10.00, 55.00, '2026-04-04 20:51:48', '2026-04-04 20:51:48'),
(76, 28, 4, NULL, 'Chilli Potato', 300.00, 1, 0, 300.00, 10.00, 30.00, '2026-04-04 20:51:48', '2026-04-04 20:51:48'),
(77, 28, 2, 16, 'Pani Puri (18 Pcs)', 80.00, 2, 0, 160.00, 10.00, 16.00, '2026-04-04 20:51:48', '2026-04-04 20:51:48'),
(78, 29, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 3, 1, 1650.00, 10.00, 165.00, '2026-04-04 22:05:12', '2026-04-04 22:09:14'),
(79, 29, 1, 24, 'Coca Cola (500 ML)', 70.00, 3, 1, 210.00, 5.00, 10.50, '2026-04-04 22:05:12', '2026-04-04 22:09:14'),
(80, 29, 3, 21, 'Chinese Bhel (Half Plate)', 250.00, 3, 1, 750.00, 5.00, 37.50, '2026-04-04 22:05:12', '2026-04-04 22:09:14'),
(81, 29, 2, 16, 'Pani Puri (18 Pcs)', 80.00, 3, 1, 240.00, 10.00, 24.00, '2026-04-04 22:05:12', '2026-04-04 22:09:14'),
(82, 30, 4, 22, 'Chilli Potato (Double Dishes)', 550.00, 3, 0, 1650.00, 10.00, 165.00, '2026-04-04 22:14:15', '2026-04-04 22:14:15'),
(83, 30, 3, 21, 'Chinese Bhel (Half Plate)', 250.00, 3, 0, 750.00, 5.00, 37.50, '2026-04-04 22:14:15', '2026-04-04 22:14:15'),
(84, 31, 2, 16, 'Pani Puri (18 Pcs)', 80.00, 3, 0, 240.00, 10.00, 24.00, '2026-04-04 22:24:34', '2026-04-04 22:24:34'),
(85, 31, 4, NULL, 'Chilli Potato', 300.00, 4, 0, 1200.00, 10.00, 120.00, '2026-04-04 22:24:34', '2026-04-04 22:24:34'),
(86, 32, 4, NULL, 'Chilli Potato', 300.00, 3, 0, 900.00, 10.00, 90.00, '2026-04-04 23:28:38', '2026-04-04 23:28:38'),
(87, 33, 6, NULL, 'Cheese Burger', 180.00, 2, 0, 360.00, 5.00, 18.00, '2026-04-05 00:17:00', '2026-04-05 00:17:00'),
(88, 33, 10, NULL, 'Cold Coffee (250ml)', 120.00, 2, 0, 240.00, 0.00, 0.00, '2026-04-05 00:17:00', '2026-04-05 00:17:00'),
(89, 33, 15, NULL, 'French Fries (Medium)', 150.00, 1, 0, 150.00, 5.00, 7.50, '2026-04-05 00:17:00', '2026-04-05 00:17:00'),
(90, 34, 5, NULL, 'Margherita Pizza (8 inch)', 300.00, 1, 0, 300.00, 5.00, 15.00, '2026-04-05 00:17:00', '2026-04-05 00:17:00'),
(91, 34, 12, NULL, 'Mango Lassi', 100.00, 2, 0, 200.00, 0.00, 0.00, '2026-04-05 00:17:01', '2026-04-05 00:17:01'),
(92, 35, 7, NULL, 'Grilled Chicken', 320.00, 1, 0, 320.00, 5.00, 16.00, '2026-04-05 00:17:01', '2026-04-05 00:17:01'),
(93, 35, 9, NULL, 'Truffle Pasta (Full)', 430.00, 1, 0, 430.00, 18.00, 77.40, '2026-04-05 00:17:01', '2026-04-05 00:17:01'),
(94, 35, 13, NULL, 'Chocolate Lava Cake', 160.00, 2, 0, 320.00, 5.00, 16.00, '2026-04-05 00:17:01', '2026-04-05 00:17:01'),
(95, 35, 11, NULL, 'Fresh Lime Soda', 80.00, 2, 0, 160.00, 0.00, 0.00, '2026-04-05 00:17:01', '2026-04-05 00:17:01'),
(96, 36, 8, NULL, 'Paneer Butter Masala', 280.00, 2, 0, 560.00, 5.00, 28.00, '2026-04-05 00:17:01', '2026-04-05 00:17:01'),
(98, 37, 14, NULL, 'Gulab Jamun (4 pcs)', 140.00, 2, 0, 280.00, 0.00, 0.00, '2026-04-05 00:17:01', '2026-04-05 00:17:01'),
(99, 37, 10, NULL, 'Cold Coffee (500ml)', 180.00, 1, 0, 180.00, 0.00, 0.00, '2026-04-05 00:17:01', '2026-04-05 00:17:01');

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
-- Table structure for table `pos_configs`
--

CREATE TABLE `pos_configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `payment_cash` tinyint(1) NOT NULL DEFAULT 1,
  `payment_card` tinyint(1) NOT NULL DEFAULT 0,
  `payment_upi` tinyint(1) NOT NULL DEFAULT 0,
  `upi_id` varchar(255) DEFAULT NULL,
  `self_ordering` tinyint(1) NOT NULL DEFAULT 0,
  `self_ordering_type` enum('online_ordering','qr_menu') DEFAULT NULL,
  `self_ordering_token` varchar(32) DEFAULT NULL,
  `bg_color` varchar(7) NOT NULL DEFAULT '#111827',
  `bg_image_1` varchar(255) DEFAULT NULL,
  `bg_image_2` varchar(255) DEFAULT NULL,
  `bg_image_3` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pos_configs`
--

INSERT INTO `pos_configs` (`id`, `name`, `is_active`, `payment_cash`, `payment_card`, `payment_upi`, `upi_id`, `self_ordering`, `self_ordering_type`, `self_ordering_token`, `bg_color`, `bg_image_1`, `bg_image_2`, `bg_image_3`, `created_at`, `updated_at`) VALUES
(4, 'Zomato Earning', 0, 1, 1, 1, NULL, 0, NULL, NULL, '#111827', NULL, NULL, NULL, '2026-04-04 04:40:14', '2026-04-04 06:58:04'),
(5, 'Victorious Baboon', 1, 1, 1, 1, '123@oksbi.com', 1, 'online_ordering', 'y5H1gpXm1EIh', '#0b58fe', 'pos-bg/DQpLmTbQlzSzwtYmbYPeCvr1byocVFFVNeV839Pz.jpg', NULL, NULL, '2026-04-04 06:57:40', '2026-04-04 21:19:18'),
(6, 'Main Counter', 1, 1, 1, 1, 'restopos@ybl', 0, NULL, NULL, '#111827', NULL, NULL, NULL, '2026-04-05 00:17:00', '2026-04-05 00:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `pos_sessions`
--

CREATE TABLE `pos_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pos_config_id` bigint(20) UNSIGNED NOT NULL,
  `opened_by` bigint(20) UNSIGNED NOT NULL,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `total_sales` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pos_sessions`
--

INSERT INTO `pos_sessions` (`id`, `pos_config_id`, `opened_by`, `opened_at`, `closed_at`, `status`, `total_sales`, `created_at`, `updated_at`) VALUES
(1, 4, 1, '2026-04-04 13:34:02', '2026-04-04 13:34:02', 'closed', 5389.00, '2026-04-04 05:17:02', '2026-04-04 13:34:02'),
(2, 5, 1, '2026-04-04 12:51:28', '2026-04-04 07:21:28', 'closed', 0.00, '2026-04-04 07:17:58', '2026-04-04 07:21:28'),
(3, 5, 1, '2026-04-04 13:35:50', '2026-04-04 13:35:50', 'closed', 3630.00, '2026-04-04 07:21:54', '2026-04-04 13:35:50'),
(4, 5, 1, '2026-04-04 13:42:21', '2026-04-04 13:42:21', 'closed', 5168.50, '2026-04-04 13:36:38', '2026-04-04 13:42:21'),
(5, 5, 1, '2026-04-04 13:55:33', '2026-04-04 13:55:33', 'closed', 330.00, '2026-04-04 13:47:28', '2026-04-04 13:55:33'),
(6, 5, 1, '2026-04-04 13:56:30', '2026-04-04 13:56:30', 'closed', 528.00, '2026-04-04 13:56:03', '2026-04-04 13:56:30'),
(7, 5, 1, '2026-04-04 14:18:47', '2026-04-04 14:18:47', 'closed', 5203.00, '2026-04-04 14:13:17', '2026-04-04 14:18:47'),
(8, 5, 1, '2026-04-04 23:05:10', '2026-04-04 23:05:10', 'closed', 18590.50, '2026-04-04 14:21:54', '2026-04-04 23:05:10'),
(9, 5, 1, '2026-04-05 00:17:01', NULL, 'open', 2629.90, '2026-04-04 23:10:04', '2026-04-05 00:17:01');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(255) DEFAULT NULL,
  `tax` decimal(5,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `unit`, `tax`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'Coca Cola', 'Chilled Coca Cola With Ice', 20.00, 'ML', 5.00, 'products/RjCzLlpABMyoZqcOaluSN367FCTODjGKCzSkr2Ff.webp', '2026-04-04 01:35:07', '2026-04-04 16:57:53'),
(2, 2, 'Pani Puri', 'Wheat balls filled with potato masala and spicy water', 30.00, 'Pack', 10.00, 'products/g6HmO5X2qM89KFHkTv1c0IX85iQV6OkLLUmpeXgO.webp', '2026-04-04 01:36:01', '2026-04-04 04:11:28'),
(3, NULL, 'Chinese Bhel', 'Exotic Chinese Bhel With Desi Indian Chatka', 250.00, 'Gram', 5.00, 'products/3DJiPF0imv0Mgqva9T2IDbpHqLfpSqqKH1VRWnAJ.webp', '2026-04-04 04:14:29', '2026-04-04 04:15:28'),
(4, NULL, 'Chilli Potato', 'Chillies and potatoes', 300.00, 'Gram', 10.00, 'products/5uCPLxlqdTq5z9ftdAYi8RIPHXR5JqREYb0fZqit.webp', '2026-04-04 04:16:45', '2026-04-04 04:16:45'),
(5, NULL, 'Margherita Pizza', 'Classic tomato and mozzarella pizza', 250.00, 'Piece', 5.00, 'products/mQsiNQ9OjDxF7MQjw6Iw1CznQktN3ybSAyTHm291.webp', '2026-04-05 00:17:00', '2026-04-05 00:21:17'),
(6, NULL, 'Cheese Burger', 'Juicy aloo patty with cheddar cheese', 180.00, 'Piece', 5.00, 'products/FOZ9EI54GYe2h06hUS31qX0y6f9SBa0ZVMVGkkJD.webp', '2026-04-05 00:17:00', '2026-04-05 00:22:12'),
(7, NULL, 'Grilled Sandwhich', 'Herb-marinated grilled sandwhich', 320.00, 'Piece', 5.00, 'products/3QovvFBCUE9OzGAHEMpyPLiNx3PrhP2jpUQCZ4RD.webp', '2026-04-05 00:17:00', '2026-04-05 00:23:10'),
(8, NULL, 'Paneer Butter Masala', 'Creamy tomato-based paneer curry', 280.00, 'Piece', 5.00, 'products/FngsSDUKWduzGQlVRUysg2jfZljCIDy7uZoUnL6g.webp', '2026-04-05 00:17:00', '2026-04-05 00:23:59'),
(9, NULL, 'Truffle Pasta', 'Creamy pasta with truffle oil and parmesan', 350.00, 'Piece', 18.00, 'products/L7kGGRnUg5IQtBnsxwa7ejD5SYfHFUWiL2PmOBXn.webp', '2026-04-05 00:17:00', '2026-04-05 00:24:50'),
(10, NULL, 'Cold Coffee', 'Chilled coffee with milk and ice', 120.00, 'ML', 0.00, 'products/8Ib9LErMaKZ6kgl8MVIyNMaXhpEojX5Qi4vGGNcz.webp', '2026-04-05 00:17:00', '2026-04-05 00:25:57'),
(11, NULL, 'Fresh Lime Soda', 'Refreshing lime with soda water', 80.00, 'ML', 0.00, 'products/s4Jp1SuSiCupYB8LINKRC1pwNce20uSAqogp2F36.webp', '2026-04-05 00:17:00', '2026-04-05 00:26:47'),
(12, NULL, 'Mango Lassi', 'Thick mango yogurt drink', 100.00, 'ML', 0.00, 'products/RHmcblBtLWRPYkSMwnzy95xBSVBBl8BJPZcWVsyw.webp', '2026-04-05 00:17:00', '2026-04-05 00:27:33'),
(13, NULL, 'Chocolate Lava Cake', 'Warm chocolate cake with molten center', 160.00, 'Piece', 5.00, 'products/0EJ0hJvppjG44MZIjOx4g2s1MB5PwYjmehcGymbl.webp', '2026-04-05 00:17:00', '2026-04-05 00:28:18'),
(14, NULL, 'Gulab Jamun', 'Soft milk-solid dumplings in sugar syrup', 80.00, 'Piece', 0.00, 'products/U0sAAsJQO2UNTO2pScKt8AkT3AzzH3kJe5Xp3cd4.webp', '2026-04-05 00:17:00', '2026-04-05 00:29:01'),
(15, NULL, 'French Fries', 'Crispy golden fries with seasoning', 120.00, 'Piece', 5.00, 'products/2aSJXNxbtmAxlE1eBHoRN489KB0rKAumkbKGlgK9.webp', '2026-04-05 00:17:00', '2026-04-05 00:29:48');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `attribute` varchar(255) NOT NULL DEFAULT 'Size',
  `name` varchar(255) NOT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `attribute`, `name`, `unit`, `price`, `created_at`, `updated_at`) VALUES
(15, 2, 'Size', '12 Pcs', NULL, 25.00, '2026-04-04 04:11:28', '2026-04-04 04:11:28'),
(16, 2, 'Size', '18 Pcs', NULL, 50.00, '2026-04-04 04:11:28', '2026-04-04 04:11:28'),
(21, 3, 'Half Plate', 'Half Plate', 'Gram', 0.00, '2026-04-04 04:15:28', '2026-04-04 04:15:28'),
(22, 4, 'Double Plate', 'Double Dishes', 'Gram', 250.00, '2026-04-04 04:16:45', '2026-04-04 04:16:45'),
(23, 1, 'Size', '200 ML', NULL, 20.00, '2026-04-04 16:57:53', '2026-04-04 16:57:53'),
(24, 1, 'Size', '500 ML', NULL, 50.00, '2026-04-04 16:57:53', '2026-04-04 16:57:53'),
(39, 5, 'Size', '6 inch', NULL, 0.00, '2026-04-05 00:21:18', '2026-04-05 00:21:18'),
(40, 5, 'Size', '8 inch', NULL, 50.00, '2026-04-05 00:21:18', '2026-04-05 00:21:18'),
(41, 5, 'Size', '12 inch', NULL, 120.00, '2026-04-05 00:21:18', '2026-04-05 00:21:18'),
(44, 6, 'Size', 'Regular', NULL, 0.00, '2026-04-05 00:22:12', '2026-04-05 00:22:12'),
(45, 6, 'Size', 'Large', NULL, 40.00, '2026-04-05 00:22:13', '2026-04-05 00:22:13'),
(46, 9, 'Size', 'Half', NULL, 0.00, '2026-04-05 00:24:51', '2026-04-05 00:24:51'),
(47, 9, 'Size', 'Full', NULL, 80.00, '2026-04-05 00:24:51', '2026-04-05 00:24:51'),
(48, 10, 'Size', '250ml', NULL, 0.00, '2026-04-05 00:25:57', '2026-04-05 00:25:57'),
(49, 10, 'Size', '500ml', NULL, 60.00, '2026-04-05 00:25:57', '2026-04-05 00:25:57'),
(50, 14, 'Size', '2 pcs', NULL, 0.00, '2026-04-05 00:29:01', '2026-04-05 00:29:01'),
(51, 14, 'Size', '4 pcs', NULL, 60.00, '2026-04-05 00:29:01', '2026-04-05 00:29:01'),
(52, 15, 'Size', 'Small', NULL, 0.00, '2026-04-05 00:29:49', '2026-04-05 00:29:49'),
(53, 15, 'Size', 'Medium', NULL, 30.00, '2026-04-05 00:29:49', '2026-04-05 00:29:49'),
(54, 15, 'Size', 'Large', NULL, 60.00, '2026-04-05 00:29:49', '2026-04-05 00:29:49');

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
('3bWLnT9OKKSd9KonPbqFN2jxzkC5nHMuCJdfa9D9', 2, '10.46.255.215', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidUVESmh1a1RRdzFUMmRuNHF4cTZvWGE3ejBPRndia0RyYllnZ2Z2NiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMC40Ni4yNTUuMjE1OjgwMDAvcG9zL3Byb2R1Y3RzIjtzOjU6InJvdXRlIjtzOjEyOiJwb3MucHJvZHVjdHMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1775344591),
('DMWNYbDkGk5SjPBXBROBi06hKKSWYiA15Umg0NzL', NULL, '10.46.255.184', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidEE3YmE3UTZ2c25PRU5wcnJLdlZqNXNmSHdLZDY4RzZRbDh4RjBRRCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovLzEwLjQ2LjI1NS4yMTU6ODAwMC9zL3FwWEhCNE02Ijt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly8xMC40Ni4yNTUuMjE1OjgwMDAvcy9xcFhIQjRNNi9vcmRlci8zMi9zdGF0dXMiO3M6NToicm91dGUiO3M6MTk6Im1vYmlsZS5vcmRlci5zdGF0dXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjIyOiJtb2JpbGVfb3JkZXJzX3FwWEhCNE02IjthOjQ6e2k6MDtpOjI5O2k6MTtpOjMwO2k6MjtpOjMxO2k6MztpOjMyO319', 1775345397),
('eDmhrPdNhuk3WHhI1Beq9sou85Z3FjrIl5GdjESk', 1, '10.46.255.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWXVPeHZ5dWtQRHoyak9qa0ZzUnZmOVQ1RzVPbkR4UXRTdFdoYUd3VSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQxOiJodHRwOi8vMTAuNDYuMjU1LjIxNTo4MDAwL3MvcXBYSEI0TTYvbWVudSI7czo1OiJyb3V0ZSI7czoxMToibW9iaWxlLm1lbnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1775354214);

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `floor_id` bigint(20) UNSIGNED NOT NULL,
  `number` varchar(255) NOT NULL,
  `seats` tinyint(3) UNSIGNED NOT NULL DEFAULT 4,
  `status` enum('vacant','occupied','reserved','inactive') NOT NULL DEFAULT 'vacant',
  `qr_token` varchar(12) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `floor_id`, `number`, `seats`, `status`, `qr_token`, `created_at`, `updated_at`) VALUES
(1, 1, '101', 3, 'vacant', 'VWcRIgTG', '2026-04-04 02:59:07', '2026-04-04 23:10:04'),
(2, 1, '102', 4, 'vacant', 'yr6EgKEy', '2026-04-04 02:59:07', '2026-04-04 21:23:01'),
(3, 1, '103', 4, 'vacant', '6Mb4agyv', '2026-04-04 02:59:07', '2026-04-04 21:23:01'),
(4, 1, '104', 4, 'occupied', 'Mulr64df', '2026-04-04 02:59:07', '2026-04-05 00:17:01'),
(5, 1, '105', 4, 'occupied', 'AtYoCobL', '2026-04-04 02:59:07', '2026-04-05 00:17:01'),
(6, 2, '101', 4, 'vacant', '5QWO7Dox', '2026-04-04 02:59:19', '2026-04-04 21:23:01'),
(7, 2, '102', 4, 'vacant', '5R4xfwfc', '2026-04-04 02:59:19', '2026-04-04 21:23:01'),
(8, 2, '103', 4, 'vacant', 'hZisP9Du', '2026-04-04 02:59:19', '2026-04-04 21:23:01'),
(9, 2, '104', 4, 'vacant', 'aCZhHiw1', '2026-04-04 02:59:19', '2026-04-04 21:23:01'),
(10, 2, '105', 4, 'vacant', 'JmiIg5bG', '2026-04-04 02:59:19', '2026-04-04 21:23:01'),
(11, 1, '106', 10, 'occupied', 'qpXHB4M6', '2026-04-04 03:00:14', '2026-04-04 23:28:38'),
(12, 3, '101', 4, 'vacant', 'seA7amHF', '2026-04-04 03:06:33', '2026-04-04 21:23:01'),
(13, 3, '102', 4, 'vacant', 'BKn27vV8', '2026-04-04 03:06:33', '2026-04-04 21:23:01'),
(14, 3, '103', 4, 'vacant', 'T53j9l2I', '2026-04-04 03:06:33', '2026-04-04 21:23:01'),
(15, 3, '104', 4, 'vacant', 'ljTz0hSq', '2026-04-04 03:06:33', '2026-04-04 21:23:01'),
(16, 3, '105', 4, 'vacant', 'G3SW6LHW', '2026-04-04 03:06:33', '2026-04-04 21:23:01'),
(17, 4, '101', 4, 'vacant', 'PBdFROTC', '2026-04-04 16:12:37', '2026-04-04 21:23:01'),
(18, 4, '102', 4, 'vacant', '4AzkUJUF', '2026-04-04 16:12:37', '2026-04-04 21:23:01'),
(19, 4, '103', 4, 'vacant', 'ld8Aym4z', '2026-04-04 16:12:37', '2026-04-04 21:23:01'),
(20, 4, '104', 4, 'vacant', 'izFC1HMX', '2026-04-04 16:12:37', '2026-04-04 21:23:01'),
(21, 4, '105', 4, 'vacant', '6epnnmgw', '2026-04-04 16:12:37', '2026-04-04 21:23:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','cashier','chef') NOT NULL DEFAULT 'cashier',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@restopos.com', 'admin', '2026-04-04 01:31:01', '$2y$12$D2UkJIQAuQFBoG5sEnx8OuENMqdoQKYoFQOuuk/sy3o9st8M//due', 'ClnGNKGiqMPCfTI28bYTWGplWvb8HtQlt2GnyMiH13B1PKFyb6vUAH8357II', '2026-04-04 01:31:01', '2026-04-05 00:16:58'),
(2, 'Akash ambani', 'cashier@restro.com', 'cashier', NULL, '$2y$12$QlBKQVKe7oAv63sDlj.yke8otMhBXJ16WJGLQjMn4cDH/N1l6P1mu', NULL, '2026-04-04 22:51:48', '2026-04-04 22:59:56'),
(3, 'John Cashier', 'cashier@restopos.com', 'cashier', NULL, '$2y$12$BQ.fQj9VG7zYqR81A/hJn.hx7/0bcK2MCL275lcHURPdZ.hTDQeM.', NULL, '2026-04-05 00:16:59', '2026-04-05 00:16:59'),
(4, 'Chef Marco', 'chef@restopos.com', 'chef', NULL, '$2y$12$z/14wtxgx41m53mDCyT0muZqgXZoPF81wsd1RKizgtY6oBYdZ0IKy', NULL, '2026-04-05 00:17:00', '2026-04-05 00:17:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_product`
--
ALTER TABLE `category_product`
  ADD PRIMARY KEY (`category_id`,`product_id`),
  ADD KEY `category_product_product_id_foreign` (`product_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_pos_session_id_foreign` (`pos_session_id`),
  ADD KEY `orders_table_id_foreign` (`table_id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_variant_id_foreign` (`variant_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pos_configs`
--
ALTER TABLE `pos_configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pos_configs_self_ordering_token_unique` (`self_ordering_token`);

--
-- Indexes for table `pos_sessions`
--
ALTER TABLE `pos_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pos_sessions_pos_config_id_foreign` (`pos_config_id`),
  ADD KEY `pos_sessions_opened_by_foreign` (`opened_by`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tables_qr_token_unique` (`qr_token`),
  ADD KEY `tables_floor_id_foreign` (`floor_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `pos_configs`
--
ALTER TABLE `pos_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pos_sessions`
--
ALTER TABLE `pos_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category_product`
--
ALTER TABLE `category_product`
  ADD CONSTRAINT `category_product_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_pos_session_id_foreign` FOREIGN KEY (`pos_session_id`) REFERENCES `pos_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pos_sessions`
--
ALTER TABLE `pos_sessions`
  ADD CONSTRAINT `pos_sessions_opened_by_foreign` FOREIGN KEY (`opened_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pos_sessions_pos_config_id_foreign` FOREIGN KEY (`pos_config_id`) REFERENCES `pos_configs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tables`
--
ALTER TABLE `tables`
  ADD CONSTRAINT `tables_floor_id_foreign` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
