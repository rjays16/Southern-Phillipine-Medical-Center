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
/*Table structure for table `seg_eclaims_voucher_charge` */

CREATE TABLE `seg_eclaims_voucher_charge` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `claim_id` int(12) DEFAULT NULL,
  `voucher_id` int(12) DEFAULT NULL,
  `payee_name` varchar(100) DEFAULT NULL,
  `payee_type` char(1) DEFAULT NULL,
  `payee_code` varchar(14) DEFAULT NULL,
  `rmbd` varchar(10) DEFAULT NULL,
  `drugs` varchar(10) DEFAULT NULL,
  `xray` varchar(10) DEFAULT NULL,
  `oprm` varchar(10) DEFAULT NULL,
  `spfee` varchar(10) DEFAULT NULL,
  `gpfee` varchar(10) DEFAULT NULL,
  `surfee` varchar(10) DEFAULT NULL,
  `anesfee` varchar(10) DEFAULT NULL,
  `gross_amount` varchar(12) DEFAULT NULL,
  `tax_amount` varchar(12) DEFAULT NULL,
  `net_amount` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_charge_claim` (`claim_id`),
  KEY `FK_charge_voucher` (`voucher_id`),
  CONSTRAINT `FK_charge_claim` FOREIGN KEY (`claim_id`) REFERENCES `seg_eclaims_claim` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_charge_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `seg_eclaims_voucher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
