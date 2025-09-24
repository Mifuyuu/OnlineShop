-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2025 at 11:58 AM
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
-- Database: `online_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'อิเล็กทรอนิกส์'),
(2, 'เครื่องเขียน'),
(3, 'เสื้อผ้า'),
(4, 'ไก่อิ้ง'),
(5, 'เครื่องนุ่งห่ม');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','processing','shipped','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `order_date`, `status`) VALUES
(1, NULL, 834.00, '2025-08-07 03:38:16', 'processing');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 599.00),
(2, 1, 2, 2, 35.00),
(3, 1, 3, 1, 199.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `stock`, `image`, `category_id`, `created_at`) VALUES
(1, 'หูฟังไร้สาย', 'หูฟัง Bluetooth คุณภาพเสียงดี', 599.00, 50, 'product_1758704349.jpg', 1, '2025-08-07 03:38:16'),
(2, 'สมุดโน้ต', 'สมุดโน้ตขนาด A5', 35.00, 100, 'product_1758704359.jpg', 2, '2025-08-07 03:38:16'),
(3, 'เสื้อยืดคอกลม', 'เสื้อยืดสีขาวคอกลม', 199.00, 80, 'product_1758704373.jpg', 3, '2025-08-07 03:38:16'),
(4, 'การ์ดจอ RTX 9000', 'การ์ดจอสุดแรง', 129900.00, 5, 'product_1758704341.jpg', 1, '2025-08-14 04:43:08'),
(12, 'Castorice', 'ทดสอบ', 555.00, 1, 'product_1758704332.jpg', 1, '2025-09-18 04:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `shipping_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `shipping_status` enum('not_shipped','shipped','delivered') DEFAULT 'not_shipped'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`shipping_id`, `order_id`, `address`, `city`, `postal_code`, `phone`, `shipping_status`) VALUES
(1, 1, '123 ถนนหลัก เขตเมือง', 'กรุงเทพมหานคร', '10100', '0812345678', 'shipped');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','member') DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `full_name`, `role`, `created_at`) VALUES
(13, 'admin', '$2y$10$aCFgNJf9FZjoQQjvF6uNN.Z1p5AHNNX4fNb0AkmeiojSpAACqOiWa', 'admin@mail.org', 'admin', 'admin', '2025-08-14 04:43:08'),
(26, 'siriaek็ngaerng', '$2y$10$SjeudGIMgPDaRmFtjCuzPukptd/qJgVc57PGFniN5iHCdelm5K.q2', 'siriaek็ngaerng@outlook.com', 'สิริ แข็งแรง', 'member', '2025-09-24 09:40:01'),
(27, 'wัnep็ywirkul', '$2y$10$VakDNm.3ZCClGZUK/59QGeyVcO59Ro/RYsui7Xbhgtbd1QwoXaNcu', 'wัnep็ywirkul@outlook.com', 'วันเพ็ญ วีรกุล', 'member', '2025-09-24 09:40:01'),
(28, 'maliwirkul', '$2y$10$fw0h/hzUZrpJjl0yUuf/HebgKYK/PyYrID1Qx5Kfd8xSHVy9pjpai', 'maliwirkul@gmail.com', 'มาลี วีรกุล', 'member', '2025-09-24 09:40:01'),
(29, 'manabuyln', '$2y$10$7mGS7ShF019YFFiVoyTlcuX9LHZ3Bmh9hJSDr58DQ03w6l4fX8BJC', 'manabuyln@live.com', 'มานะ บุญล้น', 'member', '2025-09-24 09:40:01'),
(30, 'wichัypัฒna', '$2y$10$nro4KDBHx5ihevNzpgMVH.kwu5AqvNHtKpUUh8q/yLEErcAMj.NbO', 'wichัypัฒna@hotmail.com', 'วิชัย พัฒนา', 'member', '2025-09-24 09:40:01'),
(31, 'praesriฐoajhay', '$2y$10$1xLPL3DmfnmPb4wqqLDjvOllZRBUglahH6OlvvnUFyMTUGfsXyG9y', 'praesriฐoajhay@outlook.com', 'ประเสริฐ อาจหาญ', 'member', '2025-09-24 09:40:01'),
(32, 'smchaysuksm', '$2y$10$lZe5QKLbnLvCdwbgg6BkQOBPR0Lza9IhSvkQdRhK.bbo5EykHvPjq', 'smchaysuksm@yahoo.com', 'สมชาย สุขสม', 'member', '2025-09-24 09:40:01'),
(33, 'sukัyyaaisaij', '$2y$10$FYKBFVklntv9srgsJ/Jvgu7GQMfsI9pXm9x7mhroXWP.OKYwVD8UG', 'sukัyyaaisaij@outlook.com', 'สุกัญญา ใสใจ', 'member', '2025-09-24 09:40:01'),
(34, 'wัnep็ysnga', '$2y$10$2DfXYQLc4.B/5q61S5peV.V7bj/UKhNTulht3UzaiSIsEVujp6vAS', 'wัnep็ysnga@outlook.com', 'วันเพ็ญ สง่า', 'member', '2025-09-24 09:40:01'),
(35, 'srismrsngangam', '$2y$10$oH1bK03rgGq1Ldgk0.843eqPFdqi0KcQNW7W/o3xObN9KpjDN0tyW', 'srismrsngangam@yahoo.com', 'ศรีสมร สง่างาม', 'member', '2025-09-24 09:40:01'),
(36, 'rัtnsukais', '$2y$10$AcMMCag6m9XQYiyL539A4e1qv3RSxFlHlpqQEFvReMkVoIz5r1/HC', 'rัtnsukais@hotmail.com', 'รัตน์ สุกใส', 'member', '2025-09-24 09:40:01'),
(37, 'buymimัngmi', '$2y$10$KxcGFBu0HMoyPONy1MMd6OBFYZx8XQefGJqXnx24E4IGzS7mYqC9a', 'buymimัngmi@outlook.com', 'บุญมี มั่งมี', 'member', '2025-09-24 09:40:01'),
(38, 'sirisukais', '$2y$10$9Z8ZBZhSZGhYXSIDiR4LHeiPLMiWcg5QFL7/XNwJ4FnbDGG2wZMl6', 'sirisukais@live.com', 'สิริ สุกใส', 'member', '2025-09-24 09:40:01'),
(39, 'maliklahay', '$2y$10$bPFIcHi2ATskvhC97Sga5ugruLB1paTtk5DLp5R5COz5vUAWhehC2', 'maliklahay@yahoo.com', 'มาลี กล้าหาญ', 'member', '2025-09-24 09:40:01'),
(40, 'nirัndrsngangam', '$2y$10$BXu.MLTDy1LHe70La7kTKeVz/o/dpjJF6i/TjZHPKwx6uvqLbk2Dq', 'nirัndrsngangam@gmail.com', 'นิรันดร์ สง่างาม', 'member', '2025-09-24 09:40:01'),
(41, 'buymijัntrep็y', '$2y$10$mTv.z2mSoXP9Kz8Md9qCgO5pjk5B1Av83UrPfEU/MjAlDM3djAXOG', 'buymijัntrep็y@hotmail.com', 'บุญมี จันทร์เพ็ญ', 'member', '2025-09-24 09:40:01'),
(42, 'prapaaisaij', '$2y$10$wNR1gSVIwzBqqCwopaT0UOZnTFNH/uIdexueCLqaJa4QH8g6EfS/m', 'prapaaisaij@gmail.com', 'ประภา ใสใจ', 'member', '2025-09-24 09:40:02'),
(43, 'sukัyyamisuk', '$2y$10$B7zhAdpj3i5lOD.kEd/zV.UttIadOTo11vy/5tzmRlZPvzTbGNa5y', 'sukัyyamisuk@live.com', 'สุกัญญา มีสุข', 'member', '2025-09-24 09:40:02'),
(44, 'supjnramrwy', '$2y$10$nikM2vyU2IwTMRy43uqJVuCLmy1//tYk.1n3hZVNktvZ4jLCbbx2u', 'supjnramrwy@live.com', 'สุพจน์ ร่ำรวย', 'member', '2025-09-24 09:40:02'),
(45, 'rัtnadiaij', '$2y$10$HODBaodoq2ctl2BeD5AvWeWXYmrxECpUGROHLPuUd8rgEl1fwDW9a', 'rัtnadiaij@live.com', 'รัตนา ดีใจ', 'member', '2025-09-24 09:40:02'),
(46, 'knisฐayัngyuen', '$2y$10$5v4ZMX/61BxGur0YiIVzn.AtRCN2HlnHmu2EaenhIRXVn3cdPccZC', 'knisฐayัngyuen@yahoo.com', 'กนิษฐา ยั่งยืน', 'member', '2025-09-24 09:40:02'),
(47, 'jirayuchayฤtti', '$2y$10$yr2aje7Zb4VE0G4S4fFHYeeO7hSFUWxSrAbyn9ogy8SDhQk7tDr/2', 'jirayuchayฤtti@gmail.com', 'จิรายุ ชาญฤทธิ์', 'member', '2025-09-24 09:40:02'),
(48, 'nirัndrsngangam1', '$2y$10$wD8/m5TLuR2ewqesfnr8yeRwjW7UPoLaWnXMiH34vtfW.RmTvwDUi', 'nirัndrsngangam1@yahoo.com', 'นิรันดร์ สง่างาม', 'member', '2025-09-24 09:40:02'),
(49, 'prapaoajhay', '$2y$10$fy63piQxGFVeRjpzcHr7F..vhf5bpobgWcLUStrZSYwOoLIdqaife', 'prapaoajhay@outlook.com', 'ประภา อาจหาญ', 'member', '2025-09-24 09:40:02'),
(50, 'prayuttaek็ngaerng', '$2y$10$DbzgBO5c2NADd3K1Oot.2e5lwe0PWAVCXf8vdo/ZvTR/RocVcQ2te', 'prayuttaek็ngaerng@live.com', 'ประยุทธ แข็งแรง', 'member', '2025-09-24 09:40:02'),
(51, 'krณwirkul', '$2y$10$tcFi6uzdNogSJGsSfu/QZeX2EjzVe5DUx7cuulmXxv9TPzVZ0SaCi', 'krณwirkul@yahoo.com', 'กรณ์ วีรกุล', 'member', '2025-09-24 09:40:02'),
(52, 'rัtnjัntrep็y', '$2y$10$zYng/UjNM4kjLSuEJ/fYGeuR/qJXVmalFZkl7r.pIIhwwvdvueo5G', 'rัtnjัntrep็y@gmail.com', 'รัตน์ จันทร์เพ็ญ', 'member', '2025-09-24 09:40:02'),
(53, 'tnplejriyrung', '$2y$10$0E1WtuNQ/cT.iOP7Ts00I.pz81SxLIWh4uNNP71xCUkdneAnpqsem', 'tnplejriyrung@live.com', 'ธนพล เจริญรุ่ง', 'member', '2025-09-24 09:40:02'),
(54, 'onuchatntan', '$2y$10$e2UDxvkeedVYb5ifL3Zi4ehCUMmOeGxbGfQHj2Q2xHgDj.1DWbU9K', 'onuchatntan@gmail.com', 'อนุชา ทนทาน', 'member', '2025-09-24 09:40:02'),
(55, 'smhyingmisuk', '$2y$10$YqSdweydFpfhdUu9tpLvV.lsyZXqsXBs4QBQdggoT.x/YZUKmvCVq', 'smhyingmisuk@hotmail.com', 'สมหญิง มีสุข', 'member', '2025-09-24 09:40:02'),
(56, 'piyatongdi', '$2y$10$dAIqhZAJjbsS/kFfeZyeQue7b5DTPuuqDqg3FXWQzVlf2jVVWVZiG', 'piyatongdi@live.com', 'ปิยะ ทองดี', 'member', '2025-09-24 09:40:02'),
(57, 'srismryัngyuen', '$2y$10$yC/u8ZLh/CX3x3H9KczdX.3h9YMNHYJEBjmFH.iU9Y62nqsTYeAiS', 'srismryัngyuen@yahoo.com', 'ศรีสมร ยั่งยืน', 'member', '2025-09-24 09:40:02'),
(58, 'ejriyraering', '$2y$10$cEShAE7XRmJFsiS3Ttcx2eiHbj4njos2rGo9nnQ3hvdRGTaqXbAyG', 'ejriyraering@gmail.com', 'เจริญ ร่าเริง', 'member', '2025-09-24 09:40:02'),
(59, 'prayuttsirimngkl', '$2y$10$tmOXrDdT/Ssa.xVgiXOzseKGo6BLRo7X6F0wGIqqPM5wXJ9ouc5iG', 'prayuttsirimngkl@outlook.com', 'ประยุทธ ศิริมงคล', 'member', '2025-09-24 09:40:02'),
(60, 'jitraklahay', '$2y$10$PJo18bBZXTeL1CvR8Ii7WeLliM8LLC6tKbFXgjXGuBphsfJAMHYDq', 'jitraklahay@live.com', 'จิตรา กล้าหาญ', 'member', '2025-09-24 09:40:03'),
(61, 'wichัysukais', '$2y$10$gP6wpYT4JA7evSoBK.2apep//9yCVQ3kmSNg2mmt5dBJFd0dgTd5y', 'wichัysukais@live.com', 'วิชัย สุกใส', 'member', '2025-09-24 09:40:03'),
(62, 'buymiyัngyuen', '$2y$10$Bx/.VACv1DNM8VsH0Ku1HuuvZ7.Dw5Xqh2jn6y853HcmVSTtFHEYe', 'buymiyัngyuen@hotmail.com', 'บุญมี ยั่งยืน', 'member', '2025-09-24 09:40:03'),
(63, 'sirisirimngkl', '$2y$10$rDkpfuY4xALLCO7Sp0YUt.kjRLaaPPrXM4mIyGHrOS2G47ClG0Vxy', 'sirisirimngkl@hotmail.com', 'สิริ ศิริมงคล', 'member', '2025-09-24 09:40:03'),
(64, 'siriprmisuk', '$2y$10$vRPv.QaO9EUEaYEN8XMZKOlf3ls2w0CrJTy/cJMfPg6UZmrh0A15K', 'siriprmisuk@live.com', 'สิริพร มีสุข', 'member', '2025-09-24 09:40:03'),
(65, 'nirัndrkawhna', '$2y$10$pkHM7CStRpa4ZJYANE1o5utJeXdUCnJ4PLRA1fNkBMY8ycEToPHzi', 'nirัndrkawhna@gmail.com', 'นิรันดร์ ก้าวหน้า', 'member', '2025-09-24 09:40:03'),
(66, 'wichัyaesngaekw', '$2y$10$IHzybhKaSPvlg4C94hWcbuRKlsK4k1O5mUZsVMeDdBcBFlJ8Qp.mK', 'wichัyaesngaekw@outlook.com', 'วิชัย แสงแก้ว', 'member', '2025-09-24 09:40:03'),
(67, 'smchaybuyln', '$2y$10$5tZ3MkrTYmHMwgMwUrfR4ed6kc4BrmROefvVJ5oJ7zStLZqaaz2z.', 'smchaybuyln@gmail.com', 'สมชาย บุญล้น', 'member', '2025-09-24 09:40:03'),
(68, 'jirayuejriyrung', '$2y$10$K7wcpYsEj5D4ky3nZf5tLuixSB7gXvR8LBuUDVGiRk0/3OiA1Ycau', 'jirayuejriyrung@yahoo.com', 'จิรายุ เจริญรุ่ง', 'member', '2025-09-24 09:40:03'),
(69, 'krณmisuk', '$2y$10$A2boSPRE8/BBkw.vS4/uMeo120qQtdPfCsjnZtfFENeosio.2HInm', 'krณmisuk@gmail.com', 'กรณ์ มีสุข', 'member', '2025-09-24 09:40:03'),
(70, 'knisฐamัnkng', '$2y$10$gvgPuovrzka6DYl9ggZqvOBlvTK21Kh7ml.Jw6ZxW.6jgI9g8ZdRq', 'knisฐamัnkng@outlook.com', 'กนิษฐา มั่นคง', 'member', '2025-09-24 09:40:03'),
(71, 'jitraaesngaekw', '$2y$10$bjoF/uTtszJe6dwx9PfvMubQgTcD2f/2Y1tV4MSr3dpGyJng5uhwu', 'jitraaesngaekw@hotmail.com', 'จิตรา แสงแก้ว', 'member', '2025-09-24 09:40:03'),
(72, 'wichitapraณit', '$2y$10$vYRyycHwuINSW4/XB.biYeqrP61SqIMHIjQfkQX8YQ/vU5x5RrGmu', 'wichitapraณit@live.com', 'วิชิตา ประณีต', 'member', '2025-09-24 09:40:03'),
(73, 'piyakawhna', '$2y$10$i9ifHy3ls4wEfkmqXEm2w.vQZSg1R9EaBuytLwvllbmL3IPEm18sa', 'piyakawhna@yahoo.com', 'ปิยะ ก้าวหน้า', 'member', '2025-09-24 09:40:03'),
(74, 'siripraek็ngaerng', '$2y$10$5aDCJsVab2l6XMfY2ZHAJOa6swNzbApbiIdeEhwycqGthOj93gnBi', 'siripraek็ngaerng@yahoo.com', 'สิริพร แข็งแรง', 'member', '2025-09-24 09:40:03'),
(75, 'tnplaijdi', '$2y$10$Tie16KrTR0X.8X7j3wg1Nusw9YNYtu7BHme/55jEbfnp64ZeHLE0G', 'tnplaijdi@hotmail.com', 'ธนพล ใจดี', 'member', '2025-09-24 09:40:03'),
(76, 'siriaisaij', '$2y$10$L9VzEZk5N4fWued11PGbHOH3njfn5WWIcE.xBZulKPGyqueeEwesG', 'siriaisaij@hotmail.com', 'สิริ ใสใจ', 'member', '2025-09-24 09:40:03'),
(77, 'sumaliaijdi', '$2y$10$xGSRd1l8Em./xE4ssI3Couka5JdrQ6UYUoupwmot0No87oatGZA9i', 'sumaliaijdi@hotmail.com', 'สุมาลี ใจดี', 'member', '2025-09-24 09:40:03'),
(78, 'sirirwyengin', '$2y$10$SR62XsUMoniDXiFGJWUPKeEiSF8SJMIU6zJWkthl5XVP5whW9/ZxG', 'sirirwyengin@hotmail.com', 'สิริ รวยเงิน', 'member', '2025-09-24 09:40:03'),
(79, 'rัtnaraering', '$2y$10$9EN9q.ZkyLu1xiRs.wg1/eUP7iZkjhPkwuRFgEwFm7XErD2FtPJbK', 'rัtnaraering@live.com', 'รัตนา ร่าเริง', 'member', '2025-09-24 09:40:04'),
(80, 'srismrsngangam1', '$2y$10$ofgkJfVc7s84grswNtkmzOGPKuOdaAiiOOsYDlGR4bFf3ihF34h8u', 'srismrsngangam1@outlook.com', 'ศรีสมร สง่างาม', 'member', '2025-09-24 09:40:04'),
(81, 'prayuttsirimngkl1', '$2y$10$1gDH0z2He04qe1bqKC/Pae1d.k8BQGC/5eWwZCMQ/yS01MSx52jDe', 'prayuttsirimngkl1@yahoo.com', 'ประยุทธ ศิริมงคล', 'member', '2025-09-24 09:40:04'),
(82, 'supjnramrwy1', '$2y$10$a0G.d6u0uAe9rH53WfZ/5u3AIJllyhzNGuzJMhI5LImTD.ApZRCUK', 'supjnramrwy1@yahoo.com', 'สุพจน์ ร่ำรวย', 'member', '2025-09-24 09:40:04'),
(83, 'prayuttmัnkng', '$2y$10$Yp47/8.r.yfiFq1baIaK.Od23EU5ueEpupmWb/3BvJ0N7UqZIHlmC', 'prayuttmัnkng@yahoo.com', 'ประยุทธ มั่นคง', 'member', '2025-09-24 09:40:04'),
(84, 'sukijmัngmi', '$2y$10$QLh4EnXF1uLXn1VVHB02GO20VmVUYbcOBnz3zS8wRUxTKCUVn6e3W', 'sukijmัngmi@hotmail.com', 'สุกิจ มั่งมี', 'member', '2025-09-24 09:40:04'),
(85, 'tnplpraณit', '$2y$10$jGF61WX1n5DB9DBNckzc9O6KwkauAVb4HtBhpPFiZ2C3mrK5gXIHa', 'tnplpraณit@gmail.com', 'ธนพล ประณีต', 'member', '2025-09-24 09:40:04'),
(86, 'sirisuksm', '$2y$10$yklS..B501dO03q7FZpQle3i7EoduAWtI4sFLpg.5MEdl0wHgNzzu', 'sirisuksm@live.com', 'สิริ สุขสม', 'member', '2025-09-24 09:40:04'),
(87, 'jirayusuksm', '$2y$10$U7bbsVCcPTKuGe.STJ/CGOhSJJe0qr.POd6q8itZDrrL6NVtgXUQ.', 'jirayusuksm@gmail.com', 'จิรายุ สุขสม', 'member', '2025-09-24 09:40:04'),
(88, 'wัnep็yrungerueong', '$2y$10$OsEhCU9oT5i1UdYE3Zz73uNOXVeZF92uWLSwjpEeLw1Qz0hLsKdhG', 'wัnep็yrungerueong@outlook.com', 'วันเพ็ญ รุ่งเรือง', 'member', '2025-09-24 09:40:04'),
(89, 'nirmljัntrep็y', '$2y$10$2bMW9IGuh79opp6pyA7.A.GIxrsr.SJILQ2WIOv4ezkrgadRhfahK', 'nirmljัntrep็y@live.com', 'นิรมล จันทร์เพ็ญ', 'member', '2025-09-24 09:40:04'),
(90, 'sripimppraณit', '$2y$10$kyCe0SlYDRx7Vfip8YvY.OQu3YhcwDR9U9QVTJa5w3pdaDTKZv3xO', 'sripimppraณit@outlook.com', 'ศรีพิมพ์ ปราณีต', 'member', '2025-09-24 09:40:04'),
(91, 'sukัyyaejriyrung', '$2y$10$f5AtXxSR93XaJQMXETXfRu6ZW.0WdBWNtguFXUgfPrJXDX5tu491K', 'sukัyyaejriyrung@yahoo.com', 'สุกัญญา เจริญรุ่ง', 'member', '2025-09-24 09:40:04'),
(92, 'sumaliaisaij', '$2y$10$QTSOga0tVUwBasckSAz9V.HqEiEHaIv8iV7VpE9uS3wdzWHhvEp.G', 'sumaliaisaij@live.com', 'สุมาลี ใสใจ', 'member', '2025-09-24 09:40:04'),
(93, 'ejriylัksณaesngaekw', '$2y$10$R0mqXXX.Zsd0/fiRzBGEqe6ygRH9aX5kQdpwjzy7lr4XKUnarBq2q', 'ejriylัksณaesngaekw@gmail.com', 'เจริญลักษณ์ แสงแก้ว', 'member', '2025-09-24 09:40:04'),
(94, 'praesriฐpraณit', '$2y$10$6TDMldUDyaTjYNJS7tM8secWUts0eeo38b6fIvL/J32n5wNJYzVq2', 'praesriฐpraณit@hotmail.com', 'ประเสริฐ ปราณีต', 'member', '2025-09-24 09:40:04'),
(95, 'sumalioajhay', '$2y$10$ix.Td.CwHIoiL.Jg5Aco1uaFTIlW0eb/Px52KWEm/CnlWATMQuBGG', 'sumalioajhay@hotmail.com', 'สุมาลี อาจหาญ', 'member', '2025-09-24 09:40:04'),
(96, 'buymisirimngkl', '$2y$10$/P4Z5g6WIOsR4UZjCGae4O4vKUqTuUrWjMBWd/FjFJw/cqVx5DwZ6', 'buymisirimngkl@outlook.com', 'บุญมี ศิริมงคล', 'member', '2025-09-24 09:40:05'),
(97, 'wัnchัytongdi', '$2y$10$AvUj68byEzR.p8kmPmb1xe9FGjeULq1w.7vFvbN.W1Hf0O.950S.K', 'wัnchัytongdi@live.com', 'วันชัย ทองดี', 'member', '2025-09-24 09:40:05'),
(98, 'knisฐarwyengin', '$2y$10$eZV7yEX6kPS54EtfuiviHuRxeFTlddghFD.bOXan8p5oFH7n17HIq', 'knisฐarwyengin@live.com', 'กนิษฐา รวยเงิน', 'member', '2025-09-24 09:40:05'),
(99, 'onusrapraณit', '$2y$10$8xMyIoSTf/kYkvJxf76QrOT.TWAYOWAaFb3P9BQAMSXJTxl7.olB.', 'onusrapraณit@yahoo.com', 'อนุสรา ประณีต', 'member', '2025-09-24 09:40:05'),
(100, 'nirัndrrwyengin', '$2y$10$TvpQR2idlebLW4S/Mw/rEePZx2XGchUXoRnpG/qBYZ9JIMpelkz4G', 'nirัndrrwyengin@gmail.com', 'นิรันดร์ รวยเงิน', 'member', '2025-09-24 09:40:05'),
(101, 'nirัndrsukais', '$2y$10$PIulSbSWck5T9qj5XaB.zuqj.jv9E16mUtiiVpFOUyucpFkB/oP36', 'nirัndrsukais@hotmail.com', 'นิรันดร์ สุกใส', 'member', '2025-09-24 09:40:05'),
(102, 'wัnep็ywirkul1', '$2y$10$pV1fgkowH5bGreJBfASChu6ufSWpxaqkTK5/nx6JYpgHWLlnTOKYm', 'wัnep็ywirkul1@gmail.com', 'วันเพ็ญ วีรกุล', 'member', '2025-09-24 09:40:05'),
(103, 'prayutttntan', '$2y$10$aHEvNH/jt98Qf9CjtG9e9OInJjPb1l1y24IFUVzddQm2oVJn7p8cW', 'prayutttntan@gmail.com', 'ประยุทธ ทนทาน', 'member', '2025-09-24 09:40:05'),
(104, 'onusraejriyrung', '$2y$10$Kj4D87ZAOzI4.9PBSJWCEuAnp8LDDeHOsQZqjcO15jSw3mpepTYKC', 'onusraejriyrung@gmail.com', 'อนุสรา เจริญรุ่ง', 'member', '2025-09-24 09:40:05'),
(105, 'wichัypัฒna1', '$2y$10$RgS8irlyYR2r5Tmm2uvHWOrJBrNPSm22lwG6mxQrk2nr7FyfUIhPq', 'wichัypัฒna1@yahoo.com', 'วิชัย พัฒนา', 'member', '2025-09-24 09:40:05'),
(106, 'praparaering', '$2y$10$a6JHPsqTyUywZtUjXy5MG.dabZlPLjL90RjApsSYGu20d2TfzHrVu', 'praparaering@live.com', 'ประภา ร่าเริง', 'member', '2025-09-24 09:40:05'),
(107, 'onusrawirkul', '$2y$10$ISjYJKCaXZcOka.EbjECt.Y3v5Sr2REU./8YJwE8f0CQsbUPKanMG', 'onusrawirkul@hotmail.com', 'อนุสรา วีรกุล', 'member', '2025-09-24 09:40:05'),
(108, 'rัtnadiaij1', '$2y$10$W8JgFjXmcyq/M8ehqJpmg.HJDiK3KNG4wvP3d5dvFFbXVcBXCAE..', 'rัtnadiaij1@yahoo.com', 'รัตนา ดีใจ', 'member', '2025-09-24 09:40:05'),
(109, 'rัtnpัฒna', '$2y$10$X.L05MYUjcyUScH44opuTOw4rXG2ev2aK5oNxNLKWbMhkniwyialK', 'rัtnpัฒna@outlook.com', 'รัตน์ พัฒนา', 'member', '2025-09-24 09:40:05'),
(110, 'sumaliramrwy', '$2y$10$VXa5/cb3rpKRYM067lzqS.bMb0Me/k2TopkLwH3GNK6/QGqs5txqu', 'sumaliramrwy@live.com', 'สุมาลี ร่ำรวย', 'member', '2025-09-24 09:40:05'),
(111, 'jirayubuymak', '$2y$10$lukefkTNt/tLFCRRFFmVHezcrfmEdMuB040lUfY/h97.6Jg.nnhaq', 'jirayubuymak@hotmail.com', 'จิรายุ บุญมาก', 'member', '2025-09-24 09:40:05'),
(112, 'tirdamัnkng', '$2y$10$75pyfcbRYxlti8OmgWIvlODRR8vjTKGRyKV486UFQK7ycX/giI.4q', 'tirdamัnkng@live.com', 'ธีรดา มั่นคง', 'member', '2025-09-24 09:40:05'),
(113, 'piyatongdi1', '$2y$10$C459cf8aVzYjxUtNDWbXyuCmC4o9HVGxdr.y/Jw/830T5p0S7AsSG', 'piyatongdi1@hotmail.com', 'ปิยะ ทองดี', 'member', '2025-09-24 09:40:05'),
(114, 'pranommัngmi', '$2y$10$.mavRbxIQiMjcO029Fgvq.tjZ2Co/uq9SI7IpLt7hamtFBQmiXHN.', 'pranommัngmi@live.com', 'ประนอม มั่งมี', 'member', '2025-09-24 09:40:06'),
(115, 'sumaliraering', '$2y$10$0yUE6zvMAeRbBcv9WiqYFeDyOySa7GtvA9opa1G2fDx30uoJsgJIu', 'sumaliraering@live.com', 'สุมาลี ร่าเริง', 'member', '2025-09-24 09:40:06'),
(116, 'nirัndrrungerueong', '$2y$10$N.ea3pWfRKNvYNbfv0iiReNlwfQ1k1nuRgq/DKGXpdBwtl/8mse9y', 'nirัndrrungerueong@gmail.com', 'นิรันดร์ รุ่งเรือง', 'member', '2025-09-24 09:40:06'),
(117, 'siripraณit', '$2y$10$9Lb3kx2fTEtfoo9iWW6tk.lmzlxwVVnp2WHDVfGPFudav8rxfiu1u', 'siripraณit@hotmail.com', 'สิริ ปราณีต', 'member', '2025-09-24 09:40:06'),
(118, 'onuchatongdi', '$2y$10$089.u7M2j1gOedT77NwUE.4viJMCTzPTDTOHpyE60P/pe0Xr83RU6', 'onuchatongdi@hotmail.com', 'อนุชา ทองดี', 'member', '2025-09-24 09:40:06'),
(119, 'onuchapraณit', '$2y$10$LZ.q37tP3e4mi4u8MQu3vuYBzSO/r8Gm8Lv8GpssMzO7G/yEVJtpu', 'onuchapraณit@live.com', 'อนุชา ปราณีต', 'member', '2025-09-24 09:40:06'),
(120, 'wichัydiaij', '$2y$10$yCjmATEE85aZ4UVpXgne1OF9rjPk6IDkF1VD5gjW1OTe3Svmzk18y', 'wichัydiaij@live.com', 'วิชัย ดีใจ', 'member', '2025-09-24 09:40:06'),
(121, 'tirdayัngyuen', '$2y$10$Fec9jFGMnI10yp8TEnlv.OMVeDRhg.8O6tM6cWIZfUj3DFGB/WYMK', 'tirdayัngyuen@outlook.com', 'ธีรดา ยั่งยืน', 'member', '2025-09-24 09:40:06'),
(122, 'nirmloajhay', '$2y$10$SvtDzCd9t9jE/kdHqeVufejv.sVgKz7NHsU5tkIH6njcn04M7F.ri', 'nirmloajhay@outlook.com', 'นิรมล อาจหาญ', 'member', '2025-09-24 09:40:06'),
(123, 'prapangampring', '$2y$10$W/lmCL4/6ZEDms8XmqucieYl0H6.LkzXUqlupEy9ftbDoYWnJD7Yy', 'prapangampring@yahoo.com', 'ประภา งามพริ้ง', 'member', '2025-09-24 09:40:06'),
(124, 'onuchabrisutti', '$2y$10$MbR9wVVs3y659.zBSUpf/u4YSYwRpxmctiq3Jb5izZUqkjH1P.GQ6', 'onuchabrisutti@gmail.com', 'อนุชา บริสุทธิ์', 'member', '2025-09-24 09:40:06'),
(125, 'rัtnngam', '$2y$10$120eRl666HLTqyDCTBoJy.J2/w4mx/topEP6tDq9dc/0QAHZhe5Ta', 'rัtnngam@gmail.com', 'รัตน์ งาม', 'member', '2025-09-24 09:40:06'),
(126, 'tirdaklahay', '$2y$10$y/9gVoVoAczNXa/cMJWreOASyQkNJrEB0gvLVYuUpCpdroUYydK7W', 'tirdaklahay@gmail.com', 'ธีรดา กล้าหาญ', 'member', '2025-09-24 09:41:49'),
(127, 'maliwirkul1', '$2y$10$plL/dswxVDxkJhUPtizcpurnm7g7oiKaNYuvNJLIwsdHqgx//0c6m', 'maliwirkul1@gmail.com', 'มาลี วีรกุล', 'member', '2025-09-24 09:41:49'),
(128, 'ejriybuyln', '$2y$10$Y3Cm2U0A/zC0PDqksyPtsO0VBValsT4TEBlZfzGRkx2HxClIB0QQ.', 'ejriybuyln@outlook.com', 'เจริญ บุญล้น', 'member', '2025-09-24 09:41:50'),
(129, 'tnplsngangam', '$2y$10$B1wpfHj2doMack7QWlaPP.WwI/M2tblsWyJ.PVCn6byf5ZuAF7kbW', 'tnplsngangam@live.com', 'ธนพล สง่างาม', 'member', '2025-09-24 09:41:50'),
(130, 'tnplepchrklm', '$2y$10$XDkb.kbHNaFtbFHxLDPuUeS4kL/CfhsBrim/n2t4BoCcymnfuCqOm', 'tnplepchrklm@gmail.com', 'ธนพล เพชรกลม', 'member', '2025-09-24 09:41:50'),
(131, 'prayuttsnga', '$2y$10$ipPLYlZxaAPdZI51NpuA2OVYnhgNeuTcUqlzDRZYzxkyHuCXqv.Ra', 'prayuttsnga@gmail.com', 'ประยุทธ สง่า', 'member', '2025-09-24 09:41:50'),
(132, 'onuchamัnkng', '$2y$10$Mzjcnrdcn4QkOTHjrRmk8eoa8XuBAPUvxzL1jSbNT26EMfWnbnf6y', 'onuchamัnkng@live.com', 'อนุชา มั่นคง', 'member', '2025-09-24 09:41:50'),
(133, 'piyadaejriyrung', '$2y$10$/MI0GoUbU0sXSsq9TnT1gep0E0egWQc1mCsBr2OWVRcZLm.L4gJL2', 'piyadaejriyrung@hotmail.com', 'ปิยาดา เจริญรุ่ง', 'member', '2025-09-24 09:41:50'),
(134, 'smchayngam', '$2y$10$Ct3bNlRytqq6NZYsGsekwuGVOxM4LeDkRAWYrTeQmM2RByN7YaRBi', 'smchayngam@yahoo.com', 'สมชาย งาม', 'member', '2025-09-24 09:41:50'),
(135, 'praesriฐchayฤtti', '$2y$10$dMsaYqJyM9.lnso/OfFk6.m1HUYtOb7bdP3pqHvJygTEecJb9IySm', 'praesriฐchayฤtti@outlook.com', 'ประเสริฐ ชาญฤทธิ์', 'member', '2025-09-24 09:41:50'),
(136, 'praesriฐepchrklm', '$2y$10$SgWHBXAVf.ZNRXQuWltEFeSZsbSTsLIFcYAugNqyOt6mKOPqog3KG', 'praesriฐepchrklm@hotmail.com', 'ประเสริฐ เพชรกลม', 'member', '2025-09-24 09:41:50'),
(137, 'tirdabuymak', '$2y$10$3xWswWRDpWuiV7wYMpkI5eJ3kH3uZ8rv8KHuKX5ZO1pPs8DYh9ItK', 'tirdabuymak@yahoo.com', 'ธีรดา บุญมาก', 'member', '2025-09-24 09:41:50'),
(138, 'rัtnarungerueong', '$2y$10$NMI3BpVB8yjfV6yB8WkPnOBhwibb0dZ9W6fURixiag/kaIenR1FnW', 'rัtnarungerueong@live.com', 'รัตนา รุ่งเรือง', 'member', '2025-09-24 09:41:50'),
(139, 'buyerueonrwyengin', '$2y$10$TcN28Yw3YJoitLo5s9skn.Uf8ReaSpGSOvjj1NkCmTdJGNjiIdSkm', 'buyerueonrwyengin@yahoo.com', 'บุญเรือน รวยเงิน', 'member', '2025-09-24 09:41:50'),
(140, 'smchaypraณit', '$2y$10$sD753ubwysXQYAPaV9/Na.s9BfUdEZFNGZ2s6omWbJk0th8mS7EX2', 'smchaypraณit@live.com', 'สมชาย ปราณีต', 'member', '2025-09-24 09:41:50'),
(141, 'sukijmัngmi1', '$2y$10$zMXxGDFE/tQSNIlqVgNWqO9N53eVI4aHMp72G6EBZ5.xzOUv/4Yp2', 'sukijmัngmi1@yahoo.com', 'สุกิจ มั่งมี', 'member', '2025-09-24 09:41:50'),
(142, 'prapaejriyrung', '$2y$10$5xC8KzBl8YegmBn8cLH6h.4Cbxd5CRsHx0M7psMaOHFkIu8ra.3/O', 'prapaejriyrung@hotmail.com', 'ประภา เจริญรุ่ง', 'member', '2025-09-24 09:41:50'),
(143, 'buymioajhay', '$2y$10$gJZdGtOSR5w6VzzQIhIn0eDg1uC3PLdVr4dAzzooVJKtgwvwrxAMq', 'buymioajhay@gmail.com', 'บุญมี อาจหาญ', 'member', '2025-09-24 09:41:50'),
(144, 'rัtnsirimngkl', '$2y$10$nwe7fupeyRSqFp.8ktRAseYJHYQJLlDE0DPoWTsMUx7pfBGflAl8S', 'rัtnsirimngkl@yahoo.com', 'รัตน์ ศิริมงคล', 'member', '2025-09-24 09:41:50'),
(145, 'buyerueonbuymak', '$2y$10$KPH20uUFMiyye3RzJ/F3Yuz.eSWMxegG3J15mZbe.RECHx/kzVZce', 'buyerueonbuymak@outlook.com', 'บุญเรือน บุญมาก', 'member', '2025-09-24 09:41:50'),
(146, 'tnplepchrklm1', '$2y$10$KHQEuVijYwNTlzjPIvFxKemEXuJ23sHUDgbC2oEIXi8IZ5YGWCQ8e', 'tnplepchrklm1@outlook.com', 'ธนพล เพชรกลม', 'member', '2025-09-24 09:41:51'),
(147, 'sumalisukais', '$2y$10$tg5pyTminOKIeAxB/q4hN.r25vX3OeSLwHeodH6tWYVK4WMlwwWOG', 'sumalisukais@yahoo.com', 'สุมาลี สุกใส', 'member', '2025-09-24 09:41:51'),
(148, 'prapaerueongaesng', '$2y$10$cC9hkdcs9Ct/YKtLo5rTlOggouJVRwOujWEDmV3U.uHjz6r.mAIVK', 'prapaerueongaesng@yahoo.com', 'ประภา เรืองแสง', 'member', '2025-09-24 09:41:51'),
(149, 'praesriฐchayฤtti1', '$2y$10$IbTz4MrIGq630vHX9ybDqOZUaBXDwT/ftQQDcotOYr.szdcDmX9Uy', 'praesriฐchayฤtti1@hotmail.com', 'ประเสริฐ ชาญฤทธิ์', 'member', '2025-09-24 09:41:51'),
(150, 'ejriylัksณmisuk', '$2y$10$uq5txxpihV9AkSyk92fsKuYv9orPRWj1BaHmGAG4ohWScBYg1GAPO', 'ejriylัksณmisuk@live.com', 'เจริญลักษณ์ มีสุข', 'member', '2025-09-24 09:41:51'),
(151, 'tirdabrisutti', '$2y$10$VaK2hIpThSLkTdSVyvkdue5OtFuRm.Y//5GRh1/wyc4xInOzyDg.e', 'tirdabrisutti@yahoo.com', 'ธีรดา บริสุทธิ์', 'member', '2025-09-24 09:41:51'),
(152, 'prayuttwirkul', '$2y$10$wrjSVIDlbMVW.Vc6.XnZzu4BVnHDgRVgWGchLDSafK7pB4t.tBM96', 'prayuttwirkul@outlook.com', 'ประยุทธ วีรกุล', 'member', '2025-09-24 09:41:51'),
(153, 'piyadakawhna', '$2y$10$Mr7v4Sy9SFMlm6FceMdT.OSV8crfZV24DHZiCDGt6iXAKJdSikrYu', 'piyadakawhna@gmail.com', 'ปิยาดา ก้าวหน้า', 'member', '2025-09-24 09:41:51'),
(154, 'sumalitongdi', '$2y$10$8nIz5k/4uCVTYWkcM9GYzuz0taVAAWGHw/fVEMy8izGP4UT5.PUZy', 'sumalitongdi@hotmail.com', 'สุมาลี ทองดี', 'member', '2025-09-24 09:41:51'),
(155, 'malimัnkng', '$2y$10$bFntkq4FUsRPRAmmlwHlq.wa9mnGL/ra2ECwFsttYefAfYRwsBa7C', 'malimัnkng@gmail.com', 'มาลี มั่นคง', 'member', '2025-09-24 09:41:51'),
(156, 'sukัyyakawhna', '$2y$10$XzI0BNBp2xQXGA5df7/Sj..2h6hgKkdVL74GNyUvJ9Ktw/Q7U2Yli', 'sukัyyakawhna@yahoo.com', 'สุกัญญา ก้าวหน้า', 'member', '2025-09-24 09:41:51'),
(157, 'siriprsnga', '$2y$10$C7CsOyZxreNWT3QRRSiylO7QDetkMDRjh56cp4MWyKePvs5lQiiCu', 'siriprsnga@live.com', 'สิริพร สง่า', 'member', '2025-09-24 09:41:51'),
(158, 'wichัyrwyengin', '$2y$10$SCgAmb1ckl1C/72NLuo9VepMbKSAwdYSjGSzjawjBANY0wz2K4sXq', 'wichัyrwyengin@yahoo.com', 'วิชัย รวยเงิน', 'member', '2025-09-24 09:41:51'),
(159, 'siriproajhay', '$2y$10$nenGan29FSpsKHgfMo8RpOQQC4GygpV3YK609FGLFbsYo4sxybvfO', 'siriproajhay@hotmail.com', 'สิริพร อาจหาญ', 'member', '2025-09-24 09:41:51'),
(160, 'prayutttntan1', '$2y$10$FQrb2ziAJMgpGH4U9MeJh..akKWd1bcziWc6Tc9TXCQN0CfM6rv6u', 'prayutttntan1@outlook.com', 'ประยุทธ ทนทาน', 'member', '2025-09-24 09:41:51'),
(161, 'tnplpraณit1', '$2y$10$B9rbln6.QfiXw4xyp2ZAMOdSX4uRyhCk.eghTE..3GZHdomxc8SWW', 'tnplpraณit1@yahoo.com', 'ธนพล ประณีต', 'member', '2025-09-24 09:41:51'),
(162, 'tiramisuk', '$2y$10$5MkKWtIOTI3MjCfvQYI3Uuw8f5mbRzgF802mbZYIldu0Qg2rG1i3K', 'tiramisuk@yahoo.com', 'ธีระ มีสุข', 'member', '2025-09-24 09:41:51'),
(163, 'srismrkawhna', '$2y$10$s1G5m05Sm5SuqTqhnT2Ir.7oNZIorf2hm/ZUERenaIJoomkzIHLhe', 'srismrkawhna@outlook.com', 'ศรีสมร ก้าวหน้า', 'member', '2025-09-24 09:41:51'),
(164, 'tiratongdi', '$2y$10$9oTCphOzw.KcHMtCealmWu1.4I6KZp9qTkkzF7E/r1CDubHUo5hhO', 'tiratongdi@gmail.com', 'ธีระ ทองดี', 'member', '2025-09-24 09:41:52'),
(165, 'wัnchัyramrwy', '$2y$10$GMAvvjuWNX5VX6GDInu8kOu7faLRl1Xl9YMLTkqMvbwJI./ZnDZka', 'wัnchัyramrwy@hotmail.com', 'วันชัย ร่ำรวย', 'member', '2025-09-24 09:41:52'),
(166, 'wichitaaisaij', '$2y$10$l/BwjUegdoAQ8C6YSrPNsu4hWlj64xfkAYs8NpmZ.JJe2nOf4.d/q', 'wichitaaisaij@gmail.com', 'วิชิตา ใสใจ', 'member', '2025-09-24 09:41:52'),
(167, 'tnplngam', '$2y$10$h.Nh1CPaV3tWiGKLbMiUa.603N2Lg.2FgJ6DF2Xa.dm8viemUvv2S', 'tnplngam@live.com', 'ธนพล งาม', 'member', '2025-09-24 09:41:52'),
(168, 'tirdabrisutti1', '$2y$10$hRvZS4K/pclwIVwtxd0vU.n6BFC1bv2gSU6xXExMlxF4QgnYvpTNy', 'tirdabrisutti1@hotmail.com', 'ธีรดา บริสุทธิ์', 'member', '2025-09-24 09:41:52'),
(169, 'onuchamัngmi', '$2y$10$VYUJJHL19HPmKZv/bf/d0.HlVfYeN0mOgw2Cm.p/6baQIgquEXiYC', 'onuchamัngmi@yahoo.com', 'อนุชา มั่งมี', 'member', '2025-09-24 09:41:52'),
(170, 'ejriyjัntrep็y', '$2y$10$77qceZ4sku97Lig46YpXC.Dt4FfM2tapJbsgzhnWtcF7HGNI.cZZ6', 'ejriyjัntrep็y@live.com', 'เจริญ จันทร์เพ็ญ', 'member', '2025-09-24 09:41:52'),
(171, 'sumalirwyengin', '$2y$10$ATLh8UxhUJqZ83JT/IccLuOCiroFZj1caRWZaf/97xgWQ5kzmfeeu', 'sumalirwyengin@yahoo.com', 'สุมาลี รวยเงิน', 'member', '2025-09-24 09:41:52'),
(172, 'tirdabuymak1', '$2y$10$bkAWmlFM33sG6BxQyX.PJewaGuF/Sq26eKKzLsE7F/Uxnf0ameEEO', 'tirdabuymak1@outlook.com', 'ธีรดา บุญมาก', 'member', '2025-09-24 09:41:52'),
(173, 'sumaliswangais', '$2y$10$dYbs37E6Kkv9fwk2DVQ4V.HaadGc7SH8DNCGkY97RKqgz9.6xHJ0W', 'sumaliswangais@yahoo.com', 'สุมาลี สว่างใส', 'member', '2025-09-24 09:41:52'),
(174, 'nirmlsnga', '$2y$10$bNtmyF03U5sg5GThaz0sV.o0D2WgMpjJcmqnStecS.u9SR2H6JtCC', 'nirmlsnga@live.com', 'นิรมล สง่า', 'member', '2025-09-24 09:41:52'),
(175, 'smhyingngampring', '$2y$10$oshBX0t4w/0NQkXCTOXqW.jnj6l.myFHncmgbngpCj9xt5AX.SmgO', 'smhyingngampring@yahoo.com', 'สมหญิง งามพริ้ง', 'member', '2025-09-24 09:41:52'),
(176, 'wichitatntan', '$2y$10$u4zcNibF3SGcWFBTUlk.nexL26qFyIVJRkfcqRcEPW3Er1D73oFpe', 'wichitatntan@live.com', 'วิชิตา ทนทาน', 'member', '2025-09-24 09:41:52'),
(177, 'onuchaaek็ngaerng', '$2y$10$sEep3lPQ6AbxViuEQWfsx.qNAqTVVUT2J9/2sZC2au5mcudKZNKvG', 'onuchaaek็ngaerng@outlook.com', 'อนุชา แข็งแรง', 'member', '2025-09-24 09:41:52'),
(178, 'siriprngam', '$2y$10$ACiRszJof.sj69ZOlbHSWuXi/cLnOWSjGVLAk1Jz1wxBnoEx3EE4y', 'siriprngam@hotmail.com', 'สิริพร งาม', 'member', '2025-09-24 09:41:52'),
(179, 'jirayuklahay', '$2y$10$sDJtfHwBUyKR/M7hYhP1SODDUtmY27ydS603ZiOXYy/Gwy.s1g1Ke', 'jirayuklahay@hotmail.com', 'จิรายุ กล้าหาญ', 'member', '2025-09-24 09:41:52'),
(180, 'buymiejriyrung', '$2y$10$kUfpfivYy5j6FQP0aR/lLebeD.qCzkQ6UzpP8IT0tHgIusDPYpKq6', 'buymiejriyrung@gmail.com', 'บุญมี เจริญรุ่ง', 'member', '2025-09-24 09:41:52'),
(181, 'buymiklahay', '$2y$10$Kku1oNOqksfHqQFS8utDY.AmOpVeoNqKnMq042U5Ytdj5q2HvpPgG', 'buymiklahay@hotmail.com', 'บุญมี กล้าหาญ', 'member', '2025-09-24 09:41:52'),
(182, 'wichัymัnkng', '$2y$10$RZWwJE6Ni6wY9INQWRFQ8.8GGvv1ZrcBvLKvDEicpt/SC8c55scjG', 'wichัymัnkng@outlook.com', 'วิชัย มั่นคง', 'member', '2025-09-24 09:41:53'),
(183, 'tirasukais', '$2y$10$5oo.dzJBzUs5jRZLnSQAmumNJYAj8QZB2TINWKmumphVWYgo3TAVq', 'tirasukais@live.com', 'ธีระ สุกใส', 'member', '2025-09-24 09:41:53'),
(184, 'ejriyaisaij', '$2y$10$LZVfo/9m5DUnNm6b0M53DOB0xLaAcmcZ/Y/pSr1wip5H1s.i.deoS', 'ejriyaisaij@gmail.com', 'เจริญ ใสใจ', 'member', '2025-09-24 09:41:53'),
(185, 'prayutterueongaesng', '$2y$10$2l7CkFHWMx24wGS7V8vWEuk9kQ2oJvSwliE7lY5dXXvsKvBpq5i2a', 'prayutterueongaesng@gmail.com', 'ประยุทธ เรืองแสง', 'member', '2025-09-24 09:41:53'),
(186, 'sukijchayฤtti', '$2y$10$iQhB3qGVpZ5p.ef7qv.27OF4WURAjTKPq07mnhXZ2tYWJxuF3LCR.', 'sukijchayฤtti@live.com', 'สุกิจ ชาญฤทธิ์', 'member', '2025-09-24 09:41:53'),
(187, 'sukijmัnkng', '$2y$10$.hL6TmrRZekREEIzrAqUSOukKNBBvQXzZYTZFLXcUVAVxtxjF.0Ci', 'sukijmัnkng@hotmail.com', 'สุกิจ มั่นคง', 'member', '2025-09-24 09:41:53'),
(188, 'jitrasnga', '$2y$10$N4EM5e9yjDUC4wXISUDtX./RmGxHFI8.vclA0EKEq.TqTzQNw1gC2', 'jitrasnga@yahoo.com', 'จิตรา สง่า', 'member', '2025-09-24 09:41:53'),
(189, 'jitrasirimngkl', '$2y$10$j9cNauzcJ26e4N2pFrVWWOfoDXNnl0oTglhVmfWxKnG.G1gFzJPMm', 'jitrasirimngkl@gmail.com', 'จิตรา ศิริมงคล', 'member', '2025-09-24 09:41:53'),
(190, 'piyawirkul', '$2y$10$Gf6.RFiJIED3WgKfRhXRnuP/e8Gun9BXhtj6kAU3h1yZFhoy31D7i', 'piyawirkul@gmail.com', 'ปิยะ วีรกุล', 'member', '2025-09-24 09:41:53'),
(191, 'knisฐaoajhay', '$2y$10$4iFdkOkVbd.s.G25pDIMOuqBGgJNUMDBhhSSP/YYn3F8y3goaUxI.', 'knisฐaoajhay@gmail.com', 'กนิษฐา อาจหาญ', 'member', '2025-09-24 09:41:53'),
(192, 'prapasnga', '$2y$10$irReMRyBQarfHmnLBfjWBecqN7gKFGlJ4Dsoc9697iKHlhzAeWInm', 'prapasnga@outlook.com', 'ประภา สง่า', 'member', '2025-09-24 09:41:53'),
(193, 'ejriyrungerueong', '$2y$10$2lHkfFWkEAwBYNAx3oLKkOZEGXrY36tMEOe6Nr1P7ZNN9/7BqAb3m', 'ejriyrungerueong@live.com', 'เจริญ รุ่งเรือง', 'member', '2025-09-24 09:41:53'),
(194, 'tirayัngyuen', '$2y$10$zsObAqOekrolRZhom.PSI.YLOcm70ZHTSb8zsRSyVxdaenDLifhni', 'tirayัngyuen@yahoo.com', 'ธีระ ยั่งยืน', 'member', '2025-09-24 09:41:53'),
(195, 'smhyingbrisutti', '$2y$10$p8n32Oa/bvHfx2c3DYQjA.pFI/EfLuGcQickI991jfY6nLA.gvkHm', 'smhyingbrisutti@gmail.com', 'สมหญิง บริสุทธิ์', 'member', '2025-09-24 09:41:53'),
(196, 'buymiyัngyuen1', '$2y$10$/2oqQ5ie6A5O1jFU1.dqnO01HeXN5zP/SWa0pj2sKhUCCk2SicXtS', 'buymiyัngyuen1@gmail.com', 'บุญมี ยั่งยืน', 'member', '2025-09-24 09:41:53'),
(197, 'smhyingsirimngkl', '$2y$10$2DJ37radqubQqsv08BfD/u1GnMIj7wcQshooXEe3u4l789wpi9nOW', 'smhyingsirimngkl@hotmail.com', 'สมหญิง ศิริมงคล', 'member', '2025-09-24 09:41:53'),
(198, 'sukัyyasuksm', '$2y$10$FMwB338SpOQZovESvM0jvOfhzHdbl/D/LjcRSpMScW7BoWdoSFrU2', 'sukัyyasuksm@gmail.com', 'สุกัญญา สุขสม', 'member', '2025-09-24 09:41:53'),
(199, 'buymibuyln', '$2y$10$kFKazqYC.8v9uKJWibFui.76tFNM2H3MZ4McuomHl9/KShTEKdIEq', 'buymibuyln@hotmail.com', 'บุญมี บุญล้น', 'member', '2025-09-24 09:41:53'),
(216, 'buyerueonmัngmi', '$2y$10$Vd1tTyasJhOIVvmOHtHm7uS3FmEBvfZL/EZjQ0BCJG7CZWPAAk8wC', 'buyerueonmัngmi@gmail.com', 'บุญเรือน มั่งมี', 'member', '2025-09-24 09:41:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipping_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
