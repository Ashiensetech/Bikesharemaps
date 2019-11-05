-- phpMyAdmin SQL Dump
-- version 4.0.10deb1ubuntu0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 11, 2019 at 05:01 AM
-- Server version: 5.6.33-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bikeshare_dev_fresh`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_history`
--

CREATE TABLE IF NOT EXISTS `activity_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `bike_id` int(11) NOT NULL,
  `stand_id` int(11) NOT NULL,
  `action` enum('rent','return','force_rent','force_return') NOT NULL DEFAULT 'rent',
  `rental_time` varchar(255) DEFAULT NULL,
  `activity_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `bike_id` (`bike_id`),
  KEY `stand_id` (`stand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bikes`
--

CREATE TABLE IF NOT EXISTS `bikes` (
  `bikeNum` int(11) NOT NULL AUTO_INCREMENT,
  `currentUser` int(11) DEFAULT NULL,
  `currentStand` int(11) DEFAULT NULL,
  `currentCode` int(11) NOT NULL,
  `note` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_path` varchar(250) DEFAULT NULL,
  `active` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`bikeNum`),
  UNIQUE KEY `bikeNum` (`bikeNum`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `bikes`
--

INSERT INTO `bikes` (`bikeNum`, `currentUser`, `currentStand`, `currentCode`, `note`, `image_path`, `active`) VALUES
(1, NULL, 1, 5215, NULL, 'img/uploads/downloadfile.jpeg5b119f263525c8.69883595.jpeg', 'Y'),
(2, NULL, 1, 184, '', 'img/uploads/downloadfile.jpeg5b11620344a772.20090579.jpeg', 'Y'),
(3, NULL, 1, 1700, '', 'img/uploads/downloadfile.jpeg5b116254b60320.96929389.jpeg', 'Y'),
(4, NULL, 5, 537, '', 'img/uploads/downloadfile.jpeg5b11626b73ad99.73100445.jpeg', 'Y'),
(5, NULL, 5, 3167, '', 'img/uploads/downloadfile.jpeg5b11627d8d1578.27162250.jpeg', 'Y'),
(6, 2, NULL, 5070, '', 'img/uploads/downloadfile.jpeg5b1162e46cf2e2.90162635.jpeg', 'Y'),
(7, NULL, 5, 6661, '', 'img/uploads/downloadfile.jpeg5b1162fbe9cac2.05322539.jpeg', 'Y'),
(8, NULL, 14, 6985, '', 'img/uploads/downloadfile.jpeg5b1163233cae73.82062924.jpeg', 'Y'),
(9, NULL, 1, 7048, '', 'img/uploads/downloadfile.jpeg5b116343accf52.38217046.jpeg', 'Y'),
(10, NULL, 5, 1865, '', 'img/uploads/downloadfile.jpeg5b11629593e4a1.50458181.jpeg', 'Y'),
(11, NULL, 1, 6451, '', 'img/uploads/downloadfile.jpeg5b1162ab3b4086.38491682.jpeg', 'Y'),
(12, NULL, 1, 4356, NULL, 'img/uploads/downloadfile.jpeg5b392478a24262.01968854.jpeg', 'Y'),
(13, NULL, 1, 3146, NULL, 'img/uploads/downloadfile.jpeg5b392fdf89d201.24652290.jpeg', 'Y'),
(14, 174, NULL, 9723, NULL, 'img/uploads/downloadfile.jpeg5b393006f343e2.91112310.jpeg', 'Y'),
(15, 174, NULL, 655, NULL, 'img/uploads/downloadfile.jpeg5b3930295492a0.00410809.jpeg', 'Y'),
(16, NULL, 1, 7203, 'T', 'img/uploads/downloadfile.jpeg5b393063e9a647.44656747.jpeg', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `bikes_old`
--

CREATE TABLE IF NOT EXISTS `bikes_old` (
  `bikeNum` int(11) NOT NULL,
  `currentUser` int(11) DEFAULT NULL,
  `currentStand` int(11) DEFAULT NULL,
  `currentCode` int(11) NOT NULL,
  `note` varchar(100) DEFAULT NULL,
  `image_path` varchar(250) DEFAULT NULL,
  `active` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`bikeNum`),
  UNIQUE KEY `bikeNum` (`bikeNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bikes_old`
--

INSERT INTO `bikes_old` (`bikeNum`, `currentUser`, `currentStand`, `currentCode`, `note`, `image_path`, `active`) VALUES
(0, NULL, 6, 3401, NULL, 'img/uploads/downloadfile.jpeg5b119f263525c8.69883595.jpeg', 'N'),
(1, NULL, 2, 6359, '', 'img/uploads/downloadfile.jpeg5b11620344a772.20090579.jpeg', 'Y'),
(2, NULL, 5, 761, '', 'img/uploads/downloadfile.jpeg5b116254b60320.96929389.jpeg', 'Y'),
(3, NULL, 5, 2476, '', 'img/uploads/downloadfile.jpeg5b11626b73ad99.73100445.jpeg', 'Y'),
(4, NULL, 2, 7905, '', 'img/uploads/downloadfile.jpeg5b11627d8d1578.27162250.jpeg', 'Y'),
(5, NULL, 5, 2238, '', 'img/uploads/downloadfile.jpeg5b1162e46cf2e2.90162635.jpeg', 'Y'),
(6, NULL, 1, 5687, '', 'img/uploads/downloadfile.jpeg5b1162fbe9cac2.05322539.jpeg', 'Y'),
(7, NULL, 3, 2299, '', 'img/uploads/downloadfile.jpeg5b1163233cae73.82062924.jpeg', 'Y'),
(8, NULL, 3, 5759, '', 'img/uploads/downloadfile.jpeg5b116343accf52.38217046.jpeg', 'Y'),
(9, NULL, 4, 7915, '', 'img/uploads/downloadfile.jpeg5b11629593e4a1.50458181.jpeg', 'Y'),
(10, NULL, 1, 4738, '', 'img/uploads/downloadfile.jpeg5b1162ab3b4086.38491682.jpeg', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon` varchar(6) NOT NULL,
  `value` float(5,2) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupon` (`coupon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `credit`
--

CREATE TABLE IF NOT EXISTS `credit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `credit` float(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=210 ;

--
-- Dumping data for table `credit`
--

INSERT INTO `credit` (`id`, `userId`, `credit`) VALUES
(3, 2, 300.00);

-- --------------------------------------------------------

--
-- Table structure for table `geolocation`
--

CREATE TABLE IF NOT EXISTS `geolocation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `longitude` double(20,17) NOT NULL,
  `latitude` double(20,17) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `bikeNum` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action` varchar(20) NOT NULL,
  `parameter` text NOT NULL,
  `standId` int(11) DEFAULT NULL,
  `pairAction` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE IF NOT EXISTS `inquiries` (
  `inquiryid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `inquiry` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `notified` enum('Y','N') NOT NULL DEFAULT 'N',
  `solved` enum('Y','N') NOT NULL DEFAULT 'N',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`inquiryid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `limits`
--

CREATE TABLE IF NOT EXISTS `limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `userLimit` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=210 ;

--
-- Dumping data for table `limits`
--

INSERT INTO `limits` (`id`, `userId`, `userLimit`) VALUES
(2, 2, 100);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE IF NOT EXISTS `maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bike_id` int(11) NOT NULL,
  `status` enum('Green','Yellow','Orange','Red') NOT NULL,
  `total_rental` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id`, `bike_id`, `status`, `total_rental`, `created_at`, `updated_by`, `updated_at`) VALUES
(1, 1, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(2, 2, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(3, 3, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(4, 4, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(5, 5, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(6, 6, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(7, 7, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(8, 8, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(9, 9, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(10, 10, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(11, 11, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(12, 12, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(13, 13, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(14, 14, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(15, 15, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19'),
(16, 16, 'Green', 0, '2018-12-21 14:22:19', 2, '2018-12-21 14:22:19');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bikeNum` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `note` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notified` enum('Y','N') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `solved` enum('Y','N') CHARACTER SET utf8 NOT NULL DEFAULT 'N',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pairing`
--

CREATE TABLE IF NOT EXISTS `pairing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `standid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_request`
--

CREATE TABLE IF NOT EXISTS `password_reset_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash_code` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `expired_at` datetime NOT NULL,
  `already_used` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payment_subscription`
--

CREATE TABLE IF NOT EXISTS `payment_subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subscription_type` varchar(500) NOT NULL,
  `payment_info` varchar(500) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expiration_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `received`
--

CREATE TABLE IF NOT EXISTS `received` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sms_uuid` varchar(60) NOT NULL,
  `sender` varchar(20) NOT NULL,
  `receive_time` varchar(20) NOT NULL,
  `sms_text` varchar(200) NOT NULL,
  `IP` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE IF NOT EXISTS `registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `userKey` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=208 ;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`id`, `userId`, `userKey`) VALUES
(1, 2, '30ff5af50d538e48c718a6811a2bb332f5228bd85a354f5f9fb8c08fc194d48a');

-- --------------------------------------------------------

--
-- Table structure for table `sent`
--

CREATE TABLE IF NOT EXISTS `sent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `number` varchar(20) NOT NULL,
  `text` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `sessionId` varchar(256) CHARACTER SET latin1 NOT NULL,
  `timeStamp` varchar(256) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`),
  KEY `sessionId` (`sessionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_by`, `updated_at`) VALUES
(18, 'total_rent', 4, '2018-12-22 09:38:20', 2, '2019-01-04 11:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `stands`
--

CREATE TABLE IF NOT EXISTS `stands` (
  `standId` int(11) NOT NULL AUTO_INCREMENT,
  `standName` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `standAddress` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `standPhoto` varchar(255) DEFAULT NULL,
  `serviceTag` int(10) DEFAULT NULL,
  `standDescription` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `longitude` double(20,17) DEFAULT NULL,
  `latitude` double(20,17) DEFAULT NULL,
  `active` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`standId`),
  UNIQUE KEY `standName` (`standName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `stands`
--

INSERT INTO `stands` (`standId`, `standName`, `standAddress`, `standPhoto`, `serviceTag`, `standDescription`, `longitude`, `latitude`, `active`) VALUES
(1, 'LAKIN MAIN STREET', '115 N. MAIN ST', 'img/uploads/downloadfile-6.jpeg5b1160d5575e27.36646267.jpeg', 0, 'MAIN STREET', -101.25531500000000000, 37.94089700000000000, 'Y'),
(2, 'KEARNY CO LIBRARY', '101 PRAIRIE AVE.', 'img/uploads/downloadfile-4.jpeg5b1160b7a24046.57242113.jpeg', 0, 'LIBRARY', -101.25647000000000000, 37.94304300000000000, 'Y'),
(3, 'LAKIN POOL', '111 W RUSSELL RD.', 'img/uploads/downloadfile-5.jpeg5b1160f6bd4525.92493880.jpeg', 0, 'POOL', -101.25982700000000000, 37.94406400000000000, 'Y'),
(4, 'LAKIN MIDDLE SCHOOL', '', 'img/uploads/20180601_102333_HDR-1.jpg5b1193064d3d99.36924261.jpg', 0, 'MIDDLE SCHOOL', -101.26733400000000000, 37.94086700000000000, 'N'),
(5, 'KEARNY CO. HOSPITAL', '500 E. THORPE', 'img/uploads/downloadfile-3.jpeg5b1160a42f6895.34817119.jpeg', 0, 'KEARNY CO. HOSPITAL', -101.25190800000000000, 37.94629300000000000, 'Y'),
(9, 'IOLA DEMO 1', 'IOLA DEMO 1', 'img/uploads/downloadfile-10.jpeg5b3923f256b3f6.07815674.jpeg', 0, 'DUANES FLOWERS', -95.40327400000000000, 37.92192100000000000, 'N'),
(10, 'IOLA DEMO 2', 'IOLA DEMO 2', 'img/uploads/downloadfile-17.jpeg5b392afdc78a75.95615996.jpeg', 0, 'PARK', -95.39912400000000000, 37.93141800000000000, 'N'),
(11, 'DEERFIELD REC', '609 MAIN ST.', 'img/uploads/downloadfile-1.jpeg5b392c5d9e4b99.93086098.jpeg', 0, 'CHACHIS PLACE', -101.13367300000000000, 37.98027900000000000, 'Y'),
(12, 'LEOTI DEMO 2', 'LEOTI DEMO 2', 'img/uploads/downloadfile-18.jpeg5b392d01d3a6c6.51141467.jpeg', 0, 'POOL', -101.35676200000000000, 38.47609400000000000, 'N'),
(13, 'LEOTI DEMO 5', 'LEOTI DEMO 1', 'img/uploads/downloadfile-24.jpeg5b392d4e87c622.30785983.jpeg', 0, 'PARK', -101.35580500000000000, 38.47524600000000000, 'N'),
(14, 'DEERFIELD SCHOOLS', '803 BEECH ST.', 'img/uploads/downloadfile-13.jpeg5b392e4f5e1cb8.26128976.jpeg', 0, 'DEERFIELD SCHOOLS', -101.13794200000000000, 37.98143200000000000, 'Y'),
(15, 'WAKEFIELD DEMO 1', 'WAKEFIELD DEMO 1', 'img/uploads/downloadfile-15.jpeg5b49128f490192.65063776.jpeg', 0, 'POOL', -97.01292100000000000, 39.21179700000000000, 'N'),
(16, 'WAKEFIELD DEMO 2', 'WAKEFIELD DEMO 2', 'img/uploads/downloadfile-19.jpeg5b4912d325a926.68359739.jpeg', 0, 'JUNIPER AND EIGHTH', -97.02038900000000000, 39.21780300000000000, 'N'),
(17, 'TEST1', 'TEST ADDRESS', 'img/uploads/attachment-4506433758462896806Screenshot_0630_115049.png5be562dede0925.54963778.png', 0, 'TEST DESCS', 11.00000000000000000, 22.00000000000000000, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` text NOT NULL,
  `age` enum('18','18-29','30-44','45-64','65+') NOT NULL,
  `gender` enum('M','F','no-answer') NOT NULL,
  `race` enum('white','hispanic-or-latino','black-or-african-american','native-american','asian-or-pacific','other','no-answer') NOT NULL,
  `mail` varchar(30) NOT NULL,
  `number` varchar(30) NOT NULL,
  `mailingAddress` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `physicalAddress` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `status` enum('pending','active','','') NOT NULL,
  `zipcode` varchar(50) DEFAULT NULL,
  `privileges` int(11) NOT NULL DEFAULT '0',
  `note` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `recommendations` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=216 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `userName`, `password`, `age`, `gender`, `race`, `mail`, `number`, `mailingAddress`, `physicalAddress`, `city`, `state`, `status`, `zipcode`, `privileges`, `note`, `recommendations`) VALUES
(2, 'Kyle Hunnicutt', '363d4c2e64aab4fc278b852d715c335789f6387760bdfd272911803a6d757534fa8527d508508bfa770efc3b42138ffe228fade5bcd8f567ef10bb5b9beca1e2', '', '', '', 'k-hunnicutt@mt-hawk.com', '18302794556', '', '', '', '', 'active', '', 7, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE IF NOT EXISTS `videos` (
  `videoId` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `videoPath` varchar(250) NOT NULL,
  `size` double(16,1) NOT NULL,
  `length` time DEFAULT NULL,
  `thumbnailPath` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`videoId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`videoId`, `fileName`, `videoPath`, `size`, `length`, `thumbnailPath`) VALUES
(1, 'Welcome video', 'vids/uploads/kdjddndndnddiddnd.mp4', 0.0, NULL, 'img/uploads/thumbnail.png');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_history`
--
ALTER TABLE `activity_history`
  ADD CONSTRAINT `activity_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activity_history_ibfk_2` FOREIGN KEY (`bike_id`) REFERENCES `bikes` (`bikeNum`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activity_history_ibfk_3` FOREIGN KEY (`stand_id`) REFERENCES `stands` (`standId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_reset_request`
--
ALTER TABLE `password_reset_request`
  ADD CONSTRAINT `password_reset_request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
