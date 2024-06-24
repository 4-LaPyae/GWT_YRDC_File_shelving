-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 21, 2017 at 02:45 PM
-- Server version: 5.5.8
-- PHP Version: 5.6.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yrdc_file_shelving`
--

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_application_type`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_application_type` (
  `application_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `application_type_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`application_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_customer`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_customer` (
  `customer_id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `nrc_no` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `father_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `street` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `house_no` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `division_id` int(11) unsigned NOT NULL,
  `township_id` int(11) unsigned NOT NULL,
  `ward_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`customer_id`),
  KEY `division_id` (`division_id`),
  KEY `township_id` (`township_id`),
  KEY `ward_id` (`ward_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_department`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_department` (
  `department_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_division`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_division` (
  `division_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `division_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`division_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_eventlog`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_eventlog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(100) unsigned NOT NULL,
  `action_date` datetime NOT NULL,
  `action_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `table_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `filter` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `encrypt_value` blob,
  `ip_address` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_file`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_file` (
  `file_id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` bigint(11) unsigned NOT NULL,
  `letter_no` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `letter_date` datetime NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `to_do` text COLLATE utf8_unicode_ci,
  `remark` text COLLATE utf8_unicode_ci,
  `from_department_id` int(11) unsigned DEFAULT NULL,
  `security_type_id` int(11) unsigned DEFAULT NULL,
  `application_type_id` int(11) unsigned DEFAULT NULL COMMENT 'လုပ်ငန်းအမျိုးအစား',
  `application_description` text COLLATE utf8_unicode_ci COMMENT 'အကြောင်းအရာ',
  `application_references` text COLLATE utf8_unicode_ci COMMENT 'ဖော်ပြချက်',
  `receiver_customer_id` bigint(11) unsigned DEFAULT NULL,
  `sender_customer_id` bigint(11) unsigned DEFAULT NULL,
  `destroy_order_employeeid` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_order_employee_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_order_designation` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_order_department` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_duty_employeeid` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_duty_employee_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_duty_designation` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_duty_department` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) unsigned NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(11) unsigned DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`file_id`),
  KEY `folder_id` (`folder_id`),
  KEY `from_department_id` (`from_department_id`),
  KEY `security_type_id` (`security_type_id`),
  KEY `application_type_id` (`application_type_id`),
  KEY `receiver_customer_id` (`receiver_customer_id`),
  KEY `sender_customer_id` (`sender_customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_file_to_dept`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_file_to_dept` (
  `file_id` bigint(11) unsigned NOT NULL,
  `to_department_id` int(11) unsigned NOT NULL,
  KEY `to_department_id` (`to_department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_file_type`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_file_type` (
  `file_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_type_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`file_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_folder`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_folder` (
  `folder_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file_type_id` int(11) unsigned NOT NULL,
  `rfid_no` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'RFID No.',
  `name` text COLLATE utf8_unicode_ci,
  `application_no` varchar(250) COLLATE utf8_unicode_ci NOT NULL COMMENT 'စာဖိုင်တွဲအမှတ်',
  `security_type_id` int(11) unsigned NOT NULL,
  `shelf_id` int(10) unsigned DEFAULT NULL,
  `row` int(10) unsigned NOT NULL DEFAULT '0',
  `column` int(10) unsigned NOT NULL DEFAULT '0',
  `destroy_order_employeeid` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_order_employee_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_order_designation` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_order_department` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_duty_employeeid` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_duty_employee_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_duty_designation` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destroy_duty_department` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_date` date NOT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `modified_date` date DEFAULT NULL,
  `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-unlock, 1-lock',
  `encrypt_value` longblob,
  PRIMARY KEY (`folder_id`),
  KEY `shelf_id` (`shelf_id`),
  KEY `file_type_id` (`file_type_id`),
  KEY `security_type_id` (`security_type_id`),
  KEY `shelf_id_2` (`shelf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_gate`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_gate` (
  `gate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gate_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `location_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`gate_id`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_gate_log`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_gate_log` (
  `id` int(11) NOT NULL,
  `rfid_no` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `log_time` datetime NOT NULL,
  `gate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_invalid_log`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_invalid_log` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT ' 1= admin , 2 = user',
  `action_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `page_name` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'page url ',
  `table_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0',
  `field_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `org_value` longtext COLLATE utf8_unicode_ci NOT NULL,
  `change_value` longtext COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT ' 1 = new , 2 = solved org , 3 = solved change, 4 = missing',
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_location`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_location` (
  `location_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `location_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_menu`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_menu` (
  `menu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `serial_no` int(10) NOT NULL,
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_menu_url`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_menu_url` (
  `menu_id` int(10) unsigned NOT NULL,
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  KEY `menu_id` (`menu_id`),
  KEY `menu_id_2` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_position`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_position` (
  `position_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `position_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_security_type`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_security_type` (
  `security_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `security_type_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`security_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_shelf`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_shelf` (
  `shelf_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(11) unsigned NOT NULL,
  `department_id` int(11) unsigned NOT NULL,
  `shelf_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `row` int(10) unsigned NOT NULL DEFAULT '0',
  `column` int(10) unsigned NOT NULL DEFAULT '0',
  `shelf_short_code` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`shelf_id`),
  KEY `location_id` (`location_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_shelf_type`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_shelf_type` (
  `shelf_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shelf_type_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`shelf_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_township`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_township` (
  `township_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `division_id` int(10) unsigned NOT NULL,
  `township_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`township_id`),
  KEY `district_id` (`division_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_transaction`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_transaction` (
  `transaction_id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` bigint(11) unsigned NOT NULL,
  `taken_date` datetime NOT NULL,
  `given_date` datetime DEFAULT NULL,
  `taken_employeeid` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `taken_employee_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `taken_designation` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `taken_department` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `given_employeeid` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `given_employee_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `given_designation` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `given_department` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remark` text COLLATE utf8_unicode_ci,
  `created_by` int(11) unsigned NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(11) unsigned DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `application_id` (`file_id`),
  KEY `file_id` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_user`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `user_type_id` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL COMMENT '0 - inactive, 1 - active',
  `encrypt_value` blob,
  PRIMARY KEY (`user_id`),
  KEY `user_type_id` (`user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_user_type`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_user_type` (
  `user_type_id` int(100) unsigned NOT NULL AUTO_INCREMENT,
  `user_type_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_user_type_department`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_user_type_department` (
  `user_type_id` int(100) unsigned NOT NULL,
  `department_id` int(11) unsigned NOT NULL,
  KEY `user_type_id` (`user_type_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_user_type_menu`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_user_type_menu` (
  `user_type_id` int(100) unsigned NOT NULL,
  `menu_id` int(100) unsigned NOT NULL,
  KEY `user_type_id` (`user_type_id`),
  KEY `menu_id` (`menu_id`),
  KEY `user_type_id_2` (`user_type_id`),
  KEY `menu_id_2` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fss_tbl_ward`
--

CREATE TABLE IF NOT EXISTS `fss_tbl_ward` (
  `ward_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `township_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ward_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ward_id`),
  KEY `township_id` (`township_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fss_tbl_customer`
--
ALTER TABLE `fss_tbl_customer`
  ADD CONSTRAINT `fss_tbl_customer_ibfk_3` FOREIGN KEY (`ward_id`) REFERENCES `fss_tbl_ward` (`ward_id`),
  ADD CONSTRAINT `fss_tbl_customer_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `fss_tbl_division` (`division_id`),
  ADD CONSTRAINT `fss_tbl_customer_ibfk_2` FOREIGN KEY (`township_id`) REFERENCES `fss_tbl_township` (`township_id`);

--
-- Constraints for table `fss_tbl_file`
--
ALTER TABLE `fss_tbl_file`
  ADD CONSTRAINT `fss_tbl_file_ibfk_6` FOREIGN KEY (`sender_customer_id`) REFERENCES `fss_tbl_customer` (`customer_id`),
  ADD CONSTRAINT `fss_tbl_file_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `fss_tbl_folder` (`folder_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fss_tbl_file_ibfk_2` FOREIGN KEY (`from_department_id`) REFERENCES `fss_tbl_department` (`department_id`),
  ADD CONSTRAINT `fss_tbl_file_ibfk_3` FOREIGN KEY (`security_type_id`) REFERENCES `fss_tbl_security_type` (`security_type_id`),
  ADD CONSTRAINT `fss_tbl_file_ibfk_4` FOREIGN KEY (`application_type_id`) REFERENCES `fss_tbl_application_type` (`application_type_id`),
  ADD CONSTRAINT `fss_tbl_file_ibfk_5` FOREIGN KEY (`receiver_customer_id`) REFERENCES `fss_tbl_customer` (`customer_id`);

--
-- Constraints for table `fss_tbl_folder`
--
ALTER TABLE `fss_tbl_folder`
  ADD CONSTRAINT `fss_tbl_folder_ibfk_3` FOREIGN KEY (`file_type_id`) REFERENCES `fss_tbl_file_type` (`file_type_id`),
  ADD CONSTRAINT `fss_tbl_folder_ibfk_1` FOREIGN KEY (`security_type_id`) REFERENCES `fss_tbl_security_type` (`security_type_id`),
  ADD CONSTRAINT `fss_tbl_folder_ibfk_2` FOREIGN KEY (`shelf_id`) REFERENCES `fss_tbl_shelf` (`shelf_id`);

--
-- Constraints for table `fss_tbl_gate`
--
ALTER TABLE `fss_tbl_gate`
  ADD CONSTRAINT `fss_tbl_gate_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `fss_tbl_location` (`location_id`);

--
-- Constraints for table `fss_tbl_menu_url`
--
ALTER TABLE `fss_tbl_menu_url`
  ADD CONSTRAINT `fss_tbl_menu_url_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `fss_tbl_menu` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fss_tbl_shelf`
--
ALTER TABLE `fss_tbl_shelf`
  ADD CONSTRAINT `fss_tbl_shelf_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `fss_tbl_department` (`department_id`),
  ADD CONSTRAINT `fss_tbl_shelf_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `fss_tbl_location` (`location_id`);

--
-- Constraints for table `fss_tbl_township`
--
ALTER TABLE `fss_tbl_township`
  ADD CONSTRAINT `fss_tbl_township_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `fss_tbl_division` (`division_id`);

--
-- Constraints for table `fss_tbl_transaction`
--
ALTER TABLE `fss_tbl_transaction`
  ADD CONSTRAINT `fss_tbl_transaction_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `fss_tbl_file` (`file_id`);

--
-- Constraints for table `fss_tbl_user_type_department`
--
ALTER TABLE `fss_tbl_user_type_department`
  ADD CONSTRAINT `fss_tbl_user_type_department_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `fss_tbl_department` (`department_id`),
  ADD CONSTRAINT `fss_tbl_user_type_department_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `fss_tbl_user_type` (`user_type_id`);

--
-- Constraints for table `fss_tbl_user_type_menu`
--
ALTER TABLE `fss_tbl_user_type_menu`
  ADD CONSTRAINT `fss_tbl_user_type_menu_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `fss_tbl_menu` (`menu_id`),
  ADD CONSTRAINT `fss_tbl_user_type_menu_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `fss_tbl_user_type` (`user_type_id`);

--
-- Constraints for table `fss_tbl_ward`
--
ALTER TABLE `fss_tbl_ward`
  ADD CONSTRAINT `fss_tbl_ward_ibfk_1` FOREIGN KEY (`township_id`) REFERENCES `fss_tbl_township` (`township_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
