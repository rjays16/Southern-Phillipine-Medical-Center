/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.24-log : Database - hisdbeclaims
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `seg_eclaims_claim_payee` */

CREATE TABLE `seg_eclaims_claim_payee` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `payment_claim_id` int(10) DEFAULT NULL,
  `voucher_no` varchar(60) DEFAULT NULL,
  `voucher_date` date DEFAULT NULL,
  `check_no` varchar(10) DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `check_amount` varchar(12) DEFAULT NULL,
  `claim_amount` varchar(12) DEFAULT NULL,
  `claim_payee_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_payment_claim_id` (`payment_claim_id`),
  KEY `FK_payee_voucher` (`voucher_no`),
  CONSTRAINT `FK_payment_claim_id` FOREIGN KEY (`payment_claim_id`) REFERENCES `seg_eclaims_with_payment_claim_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
