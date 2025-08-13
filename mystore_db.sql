-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 07, 2025 at 04:11 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mystore_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `stock_quantity`, `category`, `created_at`) VALUES
(1, 'ปากกาเจลลบได้', 'ปากกาเจลสีด า เขียนลื่น ลบได้', 35.00, 120, 'เครื่องเขียน', '2025-07-31 02:44:17'),
(2, 'สมุดโน้ต A5', 'สมุดขนาด A5 80 แผ่น ปกแข็ง', 45.00, 200, 'เครื่องเขียน', '2025-07-31 02:44:17'),
(3, 'กระบอกน้ าเก็บความเย็น', 'กระบอกน้ าสแตนเลส 500ml', 150.00, 50, 'ของใช้ส่วนตัว', '2025-07-31 02:44:17'),
(4, 'หูฟังบลูทูธ', 'หูฟังไร้สาย ระบบสัมผัส เชื่อมต่อ Bluetooth 5.0', 490.00, 35, 'อุปกรณ์อิเล็กทรอนิกส์', '2025-07-31 02:44:17'),
(5, 'หมวกกันแดด', 'หมวกปีกกว้าง ส าหรับกันแดดกลางแจ้ง', 120.00, 75, 'แฟชั่น', '2025-07-31 02:44:17'),
(6, 'กระเป๋าเป้สะพายหลัง', 'กระเป๋าใส่โน้ตบุ๊ก กันน้ า ขนาด 15 นิ้ว', 890.00, 20, 'แฟชั่น', '2025-07-31 02:44:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
