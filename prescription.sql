/*
SQLyog Professional v12.09 (64 bit)
MySQL - 8.0.31 : Database - prescription
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`prescription` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `prescription`;

/*Table structure for table `diagnostic` */

DROP TABLE IF EXISTS `diagnostic`;

CREATE TABLE `diagnostic` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL COMMENT 'Doctor ID',
  `patient_id` int DEFAULT NULL,
  `diagnostic_template_id` int DEFAULT NULL,
  `diagnostic` text COLLATE utf8mb4_unicode_ci,
  `note` text COLLATE utf8mb4_unicode_ci,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `diagnostic` */

/*Table structure for table `diagnostic_template` */

DROP TABLE IF EXISTS `diagnostic_template`;

CREATE TABLE `diagnostic_template` (
  `id` int NOT NULL AUTO_INCREMENT,
  `diagnostic` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `diagnostic_template` */

/*Table structure for table `diagnostic_template_prescription` */

DROP TABLE IF EXISTS `diagnostic_template_prescription`;

CREATE TABLE `diagnostic_template_prescription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `diagnostic_template_id` int DEFAULT NULL,
  `drug_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `most_used` tinyint(1) DEFAULT '0',
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `diagnostic_template_prescription` */

/*Table structure for table `drug` */

DROP TABLE IF EXISTS `drug`;

CREATE TABLE `drug` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'viên, gói, ống, lọ, ....',
  `price` int DEFAULT NULL,
  `in_price` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `drug` */

insert  into `drug`(`id`,`user_id`,`name`,`unit`,`price`,`in_price`,`note`,`date_created`,`date_updated`,`removed`) values (1,1,'Augmentin 250mg','Gói',0,0,'',1720690892,NULL,0),(2,1,'Unicon 4mg','Viên',0,0,'',1720690892,NULL,0),(3,1,'VitaminD3BON','Ống',0,0,'Uống 6 tháng 01 ống',1720690892,NULL,0),(4,1,'Vitamin C 500mg','Viên',0,0,'',1720690892,NULL,0);

/*Table structure for table `migration` */

DROP TABLE IF EXISTS `migration`;

CREATE TABLE `migration` (
  `version` text COLLATE utf8mb4_unicode_ci,
  `apply_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migration` */

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `diagnostic_id` int DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `price` int DEFAULT NULL,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `orders` */

/*Table structure for table `package` */

DROP TABLE IF EXISTS `package`;

CREATE TABLE `package` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `package_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `package` */

/*Table structure for table `package_orders` */

DROP TABLE IF EXISTS `package_orders`;

CREATE TABLE `package_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `package_id` int DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `package_orders` */

/*Table structure for table `package_prescription` */

DROP TABLE IF EXISTS `package_prescription`;

CREATE TABLE `package_prescription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `package_id` int DEFAULT NULL,
  `drug_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `time_in_day` int DEFAULT NULL,
  `unit_in_time` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `package_prescription` */

/*Table structure for table `patient` */

DROP TABLE IF EXISTS `patient`;

CREATE TABLE `patient` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `note` text COLLATE utf8mb4_unicode_ci,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `patient` */

/*Table structure for table `prescription` */

DROP TABLE IF EXISTS `prescription`;

CREATE TABLE `prescription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `diagnostic_id` int DEFAULT NULL,
  `drug_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `time_in_day` int DEFAULT NULL,
  `unit_in_time` int DEFAULT NULL,
  `unit_price` int DEFAULT NULL,
  `drug_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `in_unit_price` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `prescription` */

/*Table structure for table `remember_users` */

DROP TABLE IF EXISTS `remember_users`;

CREATE TABLE `remember_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `browser` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `remember_hash` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `remember_users` */

insert  into `remember_users`(`id`,`user_id`,`browser`,`remember_hash`,`created_at`) values (1,1,'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:127.0) Gecko/20100101 Firefox/127.0','d232a89b72cc4f4edd9a976b716e921b',1720687143),(4,1,'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:127.0) Gecko/20100101 Firefox/127.0','be060b8a3b6b7d3329ffcc024ce7b789',1720688389);

/*Table structure for table `services` */

DROP TABLE IF EXISTS `services`;

CREATE TABLE `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `service_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `price` int DEFAULT NULL,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `services` */

insert  into `services`(`id`,`user_id`,`service_name`,`notes`,`price`,`date_created`,`date_updated`,`removed`) values (1,1,'Siêu âm màu','',0,1720691284,NULL,0),(2,1,'Siêu âm trắng đen','',0,1720691474,NULL,0),(3,1,'Xét nghiệm máu','',0,1720691479,NULL,0),(4,1,'Xét nghiệm nước tiểu','',0,1720691483,NULL,0),(5,1,'Khám','',100000,1720691493,NULL,0);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_enable` tinyint(1) DEFAULT '1',
  `expired_at` int DEFAULT NULL,
  `date_created` int DEFAULT NULL,
  `date_updated` int DEFAULT NULL,
  `removed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`email`,`username`,`password`,`phone`,`address`,`name`,`fullname`,`tax_id`,`admin_password`,`report_enable`,`expired_at`,`date_created`,`date_updated`,`removed`) values (1,'thaoth.it@gmail.com','admin','f199e21d400b0a3a26d2195cbf14a608','0828868779','119 Đường D2, Phường Tăng Nhơn Phú A, Tp Thủ Đức, Tp Hồ Chí Minh','Trần Hoàng Thao','Trần Hoàng Thao',NULL,'e10adc3949ba59abbe56e057f20f883e',1,1912006800,NULL,NULL,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
