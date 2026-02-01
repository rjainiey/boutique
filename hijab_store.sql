-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 19, 2026 at 02:39 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hijab_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`) VALUES
(1, 'kekaboo', 'kekaboo123');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int NOT NULL,
  `cust_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `num_phone` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `cust_name`, `num_phone`, `email`, `address`, `password`, `created_at`) VALUES
(1, 'Sarah Khan', '0123456789', 'sarah@example.com', 'Kuala Lumpur', '$2y$10$pGvUS5e4icjJrMDq7aynM.Y98pcTcF9YWVDSzfLrIfs3z.xIjt4Ka', '2026-01-17 16:05:54'),
(2, 'Muhd Ali', '01254865982', 'ali@gmail.com', 'Ipoh, Perak', '$2y$10$nCn19so4kuwaTI5.8FQZGOzIrLf/Lax95xHAzFwn5oq6N5LsunRzq', '2026-01-18 14:31:02'),
(3, 'Shah Hakimi', '01754821259', 'shah@gmail.com', 'Alor Setar, Kedah', '$2y$10$HvhXdB15kYWWleDOgy/ple4x8H7Xbh4qdNlyXN23iEvH2OHOLowwS', '2026-01-18 14:38:09'),
(4, 'Alyaa Natasha', '01525489512', 'yasha@gmail.com', 'Segamat, Johor', '$2y$10$e93pl3gWN6IKV5ZFce5wGuA7vzFKjpvMe8lbOSISjNZmQ828FB.6i', '2026-01-18 15:19:37'),
(5, 'aini', '0129865428', 'aini11@gmail.com', 'sintok, kedah', '$2y$10$BoQL2/AYqb/m4DkQ.1/dqui0XlrL5J1kudAwCpLCDticrbl/fqUcq', '2026-01-19 09:11:09'),
(6, 'Fahmi Izwan', '01694578521', 'fahmi@gmail.com', 'Seremban, Negeri Sembilan', '$2y$10$yOCtiX3QhSGkCOKrYNsY7e81D4TZ1cpv7bjSclMwlTteNR/QqgO1O', '2026-01-19 21:31:05');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_id` int NOT NULL,
  `item_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `stock_quantity` int NOT NULL,
  `is_on_sale` tinyint(1) DEFAULT '0',
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_id`, `item_name`, `stock_quantity`, `is_on_sale`, `price`, `image_url`) VALUES
(1, 'Vanilla Qyra', 50, 1, 19.00, 'image/hijab1.jpg'),
(2, 'Vanilla Yani', 30, 0, 19.00, 'image/hijab2.jpg'),
(3, 'Vanilla Laza', 25, 0, 19.00, 'image/hijab3.jpg'),
(4, 'Afrah Hejra', 25, 0, 18.90, 'image/afrah_hejra.jpg'),
(5, 'Apple Geo', 30, 1, 21.50, 'image/apple_geo.jpg'),
(6, 'Bahagia Bella', 20, 0, 22.90, 'image/bahagia_bella.jpg'),
(7, 'Bayu Songket', 22, 0, 20.90, 'image/bayu_songket.jpg'),
(8, 'Bloom Tropical', 22, 0, 22.90, 'image/bloom_tropical.jpg'),
(9, 'Blossom Tropical', 22, 0, 22.90, 'image/blossom_tropical.jpg'),
(10, 'Chantek Jelita', 20, 1, 19.90, 'image/chantek_jelita.jpg'),
(11, 'Cindy Black', 30, 0, 19.90, 'image/cindy_black.jpg'),
(12, 'Cinta Jelita', 22, 1, 19.90, 'image/cinta_jelita.jpg'),
(13, 'Coral Tropical', 15, 0, 22.90, 'image/coral_tropical.jpg'),
(14, 'Hazel Geo', 15, 0, 19.00, 'image/hazel_geo.jpg'),
(15, 'Hue Nami', 28, 1, 20.90, 'image/hue_nami.jpg'),
(16, 'Ice Geo', 10, 0, 19.00, 'image/ice_geo.jpg'),
(17, 'Jiwa Bella', 5, 0, 23.00, 'image/jiwa_bella.jpg'),
(18, 'Kalila Hejra', 30, 1, 26.90, 'image/kalila_hejra.jpg'),
(19, 'Kenangan Bella', 25, 0, 24.90, 'image/kenangan_bella.jpg'),
(20, 'Laut Songket', 4, 0, 22.90, 'image/laut_songket.jpg'),
(21, 'Lucy Black', 9, 1, 19.90, 'image/lucy_black.jpg'),
(22, 'Mauve Fancy', 2, 1, 26.90, 'image/mauve_fancy.jpg'),
(23, 'Mustard Fancy', 32, 0, 25.00, 'image/mustard_fancy.jpg'),
(24, 'Rabia Hejra', 28, 0, 28.00, 'image/rabia_hejra.jpg'),
(25, 'Pucuk Tabur', 31, 1, 30.00, 'image/pucuk_tabur.jpg'),
(26, 'Puteri Tabur', 10, 1, 30.00, 'image/puteri_tabur.jpg'),
(27, 'Peony Nami', 14, 0, 25.00, 'image/peony_nami.jpg'),
(28, 'Quinn Black', 30, 0, 35.00, 'image/quinn_black.jpg'),
(29, 'Purple Fancy', 17, 0, 25.00, 'image/purple_fancy.jpg'),
(30, 'Nila Jelita', 24, 0, 28.00, 'image/nila_jelita.jpg'),
(31, 'Mist Nami', 50, 0, 25.00, 'image/mist_nami.jpg'),
(32, 'Raja Tabur', 29, 0, 30.00, 'image/raja_tabur.jpg'),
(33, 'Sutera Tabur', 12, 1, 30.00, 'image/sutera_tabur.jpg'),
(34, 'Teal Fancy', 19, 0, 25.00, 'image/teal_fancy.jpg'),
(35, 'Sky Black', 36, 0, 35.00, 'image/sky_black.jpg'),
(36, 'Sunset Tropical', 50, 0, 28.00, 'image/sunset_tropical.jpg'),
(37, 'Velvet Geo', 35, 0, 25.00, 'image/velvet_geo.jpg'),
(38, 'Rentak Bella', 20, 0, 28.00, 'image/rentak_bella.jpg'),
(39, 'Terra Nami', 28, 0, 25.00, 'image/terra_nami.jpg'),
(40, 'Tamara Hejra', 26, 0, 28.00, 'image/tamara_hejra.jpg'),
(41, 'Rimba Songket', 23, 0, 30.00, 'image/rimba_songket.jpg'),
(42, 'Wangi Jelita', 33, 0, 30.00, 'image/wangi_jelita.jpg'),
(43, 'Teduh Songket', 24, 7, 30.00, 'image/teduh_songket.jpg'),
(44, 'Dusty Pink Tabur', 15, 1, 24.90, 'image/hijab4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `points`
--

CREATE TABLE `points` (
  `points_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `points`
--

INSERT INTO `points` (`points_id`, `customer_id`, `total_price`, `created_at`) VALUES
(1, 2, 100.00, '2026-01-18 14:31:02'),
(2, 1, 100.00, '2026-01-17 16:05:54'),
(3, 3, 100.00, '2026-01-18 14:38:09'),
(4, 1, -100.00, '2026-01-18 15:12:19'),
(5, 1, 82.00, '2026-01-18 15:12:19'),
(6, 2, 98.00, '2026-01-18 15:17:19'),
(7, 3, -100.00, '2026-01-18 15:18:17'),
(8, 3, 87.00, '2026-01-18 15:18:17'),
(9, 4, 100.00, '2026-01-18 15:19:37'),
(10, 4, -100.00, '2026-01-18 15:20:21'),
(11, 4, 118.00, '2026-01-18 15:20:21'),
(12, 5, 100.00, '2026-01-19 09:11:09'),
(13, 5, -100.00, '2026-01-19 09:11:53'),
(14, 5, 43.00, '2026-01-19 09:11:53'),
(15, 6, 100.00, '2026-01-19 21:31:05'),
(16, 6, 40.00, '2026-01-19 21:42:55'),
(17, 6, -100.00, '2026-01-19 21:44:01'),
(18, 6, 36.00, '2026-01-19 21:44:01');

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `purchase_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `item_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `item_price` decimal(10,0) NOT NULL,
  `order_date` date NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase`
--

INSERT INTO `purchase` (`purchase_id`, `customer_id`, `item_name`, `item_price`, `order_date`, `status`, `created_at`) VALUES
(1, 1, 'Teal Fancy (x1), Mauve Fancy (x1), Purple Fancy (x1)', 83, '2026-01-18', 'processing', '2026-01-18 15:12:19'),
(2, 2, 'Sky Black (x1), Pucuk Tabur (x1), Sutera Tabur (x1)', 98, '2026-01-18', 'shipped', '2026-01-18 15:17:19'),
(3, 3, 'Kalila Hejra (x1), Pucuk Tabur (x1), Vanilla Qyra (x2)', 87, '2026-01-18', 'shipped', '2026-01-18 15:18:17'),
(4, 4, 'Purple Fancy (x1), Dusty Pink Tabur (x1), Nila Jelita (x2), Pucuk Tabur (x1)', 119, '2026-01-18', 'processing', '2026-01-18 15:20:21'),
(5, 5, 'Wangi Jelita (x1)', 44, '2026-01-19', 'delivered', '2026-01-19 09:11:53'),
(6, 6, 'Teal Fancy (x1)', 40, '2026-01-19', 'pending', '2026-01-19 21:42:55'),
(7, 6, 'Laut Songket (x1)', 37, '2026-01-19', 'pending', '2026-01-19 21:44:01');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int NOT NULL,
  `purchase_id` int NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `payment_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `purchase_id`, `payment_method`, `payment_status`, `created_at`) VALUES
(1, 1, 'credit_card', 'pending', '2026-01-18 15:12:19'),
(2, 2, 'credit_card', 'pending', '2026-01-18 15:17:19'),
(3, 3, 'credit_card', 'pending', '2026-01-18 15:18:17'),
(4, 4, 'credit_card', 'pending', '2026-01-18 15:20:21'),
(5, 5, 'credit_card', 'pending', '2026-01-19 09:11:53'),
(6, 6, 'credit_card', 'pending', '2026-01-19 21:42:55'),
(7, 7, 'credit_card', 'pending', '2026-01-19 21:44:01');

-- --------------------------------------------------------

--
-- Table structure for table `upcoming`
--

CREATE TABLE `upcoming` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `upcoming`
--

INSERT INTO `upcoming` (`id`, `name`, `price`, `image_filename`, `created_at`) VALUES
(1, 'Bandung Ombre', 39.00, 'image/bandung_ombre.jpg', '2026-01-19 03:46:25'),
(2, 'Mango Ombre', 39.00, 'image/mango_ombre.jpg', '2026-01-19 03:46:25'),
(3, 'Matcha Ombre', 39.00, 'image/matcha_ombre.jpg', '2026-01-19 03:46:25'),
(4, 'Potato Ombre', 39.00, 'image/potato_ombre.jpg', '2026-01-19 03:46:25'),
(5, 'Melon Keffiyeh', 36.00, 'image/melon_keff.jpg', '2026-01-19 03:51:32'),
(6, 'Tembikai Keffiyeh', 36.00, 'image/tembikai_keff.jpg', '2026-01-19 03:51:32'),
(7, 'Belacan Keffiyeh', 36.00, 'image/belacan_keff.jpg', '2026-01-19 03:52:13'),
(8, 'Burgundy Keffiyeh', 36.00, 'image/burgundy_keff.jpg', '2026-01-19 03:52:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `points`
--
ALTER TABLE `points`
  ADD PRIMARY KEY (`points_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indexes for table `upcoming`
--
ALTER TABLE `upcoming`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `points`
--
ALTER TABLE `points`
  MODIFY `points_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `purchase_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transaction_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `upcoming`
--
ALTER TABLE `upcoming`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `points`
--
ALTER TABLE `points`
  ADD CONSTRAINT `points_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `purchase`
--
ALTER TABLE `purchase`
  ADD CONSTRAINT `purchase_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
