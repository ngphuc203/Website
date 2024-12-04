-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th12 02, 2024 lúc 04:20 PM
-- Phiên bản máy phục vụ: 8.3.0
-- Phiên bản PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `doanv2`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `products_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  UNIQUE KEY `unique_cart` (`user_id`,`products_id`),
  KEY `product_id` (`products_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`order_id`, `username`, `fullname`, `address`, `phone`, `payment_method`, `total_amount`, `order_date`, `status`) VALUES
(19, 'ngphuc175', 'Nguyễn Phúc', '79 Lâm Văn Bền, Q7, TPHCM', '0389101040', 'cash_on_delivery', 99999999.99, '2024-12-02 16:03:37', 'Pending'),
(20, 'ngphuc175', 'Nguyễn Phúc', '10/41 âu dương lân', '0389101040', 'online_payment', 99999999.99, '2024-12-02 16:06:56', 'Pending'),
(21, 'xphu1', 'Phú Ăn Cức', '10/41 âu dương lân', '1234512346', 'cash_on_delivery', 99999999.99, '2024-12-02 16:07:56', 'Pending');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`, `total`) VALUES
(30, 16, 18, 'POP MART', 10, 23000000.00, 99999999.99),
(29, 16, 17, 'POP MART', 8, 22000000.00, 99999999.99),
(28, 16, 16, 'POP MART', 4, 21000000.00, 84000000.00),
(27, 16, 19, 'POP MART', 1, 24000000.00, 24000000.00),
(26, 15, 18, 'POP MART', 14, 23000000.00, 99999999.99),
(25, 15, 17, 'POP MART', 8, 22000000.00, 99999999.99),
(24, 15, 16, 'POP MART', 6, 21000000.00, 99999999.99),
(23, 15, 19, 'POP MART', 5, 24000000.00, 99999999.99),
(22, 14, 18, 'POP MART', 10, 23000000.00, 99999999.99),
(21, 14, 17, 'POP MART', 7, 22000000.00, 99999999.99),
(20, 14, 16, 'POP MART', 4, 21000000.00, 84000000.00),
(19, 14, 19, 'POP MART', 1, 24000000.00, 24000000.00),
(31, 17, 16, 'POP MART', 1, 21000000.00, 21000000.00),
(32, 17, 17, 'POP MART', 1, 22000000.00, 22000000.00),
(33, 17, 18, 'POP MART', 5, 23000000.00, 99999999.99),
(34, 18, 20, 'POP MART', 2, 25000000.00, 50000000.00),
(35, 19, 23, 'POP MART', 3, 30000000.00, 90000000.00),
(36, 19, 22, 'POP MART', 6, 23000000.00, 99999999.99),
(37, 19, 20, 'POP MART', 2, 25000000.00, 50000000.00),
(38, 19, 21, 'POP MART', 1, 25000000.00, 25000000.00),
(39, 19, 28, 'POP MART', 10, 12345678.00, 99999999.99),
(40, 20, 23, 'POP MART', 1, 30000000.00, 30000000.00),
(41, 20, 22, 'POP MART', 1, 23000000.00, 23000000.00),
(42, 20, 21, 'POP MART', 1, 25000000.00, 25000000.00),
(43, 20, 28, 'POP MART', 1, 12345678.00, 12345678.00),
(44, 20, 26, 'POP MART', 1, 12000000.00, 12000000.00),
(45, 21, 23, 'POP MART', 1, 30000000.00, 30000000.00),
(46, 21, 23, 'POP MART', 1, 30000000.00, 30000000.00),
(47, 21, 23, 'POP MART', 1, 30000000.00, 30000000.00),
(48, 21, 22, 'POP MART', 1, 23000000.00, 23000000.00),
(49, 21, 21, 'POP MART', 1, 25000000.00, 25000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `quantity`, `image`) VALUES
(20, 'POP MART', 'MEGA SPACE MOLLY 1000% Series 2- B', 25000000.00, 10, 'uploads/674dcfc7798d3_pd9.jpg'),
(23, 'POP MART', 'MEGA SPACE MOLLY 1000% Rick and Morty', 30000000.00, 20, 'uploads/674dd041081be_pd1.jpg'),
(22, 'POP MART', 'MEGA SPACE MOLLY 1000% Minions\r\n', 23000000.00, 12, 'uploads/674dd024693bf_pd2.jpg'),
(21, 'POP MART', 'MEGA SPACE MOLLY 1000% PANDA', 25000000.00, 10, 'uploads/674dcfe109ee8_pd3.jpg'),
(25, 'POP MART', 'SKULLPANDA The Stocking Surprise Figure', 2100000.00, 10, 'uploads/674dd0c276177_sk1.jpg'),
(26, 'POP MART', 'SKULLPANDA The Stocking Surprise Figure', 12000000.00, 20, 'uploads/674dd0d23b461_sk2.jpg'),
(27, 'POP MART', 'SKULLPANDA The Stocking Surprise Figure', 2300000.00, 10, 'uploads/674dd0e7d6a95_sk5.jpg'),
(28, 'POP MART', 'SKULLPANDA The Stocking Surprise Figure', 12345678.00, 12, 'uploads/674dd0f292730_sk8.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fullname` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('Nam','Nữ') COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `gender`, `dob`) VALUES
('674ddae69b760', 'Nhân Đầu Hói', 'hoiv1', '1', 'Nam', '3333-11-22'),
('674ddaad4ba42', 'Phú Ăn Cức', 'xphu1', '1', 'Nam', '2003-03-09'),
('674ddacc2a540', 'Nguyễn Phúc', 'ngphuc175', '1', 'Nam', '2003-12-05'),
('674ddaf1a90fd', 'Nguyễn Phúc', 'admin', '1', 'Nam', '1111-11-11');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
