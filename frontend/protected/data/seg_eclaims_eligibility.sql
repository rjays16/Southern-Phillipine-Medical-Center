/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.6.12-log : Database - hisdbeclaims
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `seg_eclaims_eligibility` */

CREATE TABLE `seg_eclaims_eligibility` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `encounter_nr` varchar(12) NOT NULL,
  `tracking_number` varchar(12) DEFAULT NULL,
  `as_of` date DEFAULT NULL,
  `remaining_days` int(11) DEFAULT NULL,
  `is_nhts` tinyint(1) DEFAULT NULL,
  `with_3over6` tinyint(1) DEFAULT NULL,
  `with_9over12` tinyint(1) DEFAULT NULL,
  `is_eligible` tinyint(1) DEFAULT NULL,
  `is_final` tinyint(1) DEFAULT NULL,
  `patient_lname` varchar(60) DEFAULT NULL,
  `patient_fname` varchar(60) DEFAULT NULL,
  `patient_mname` varchar(60) DEFAULT NULL,
  `patient_suffix` varchar(10) DEFAULT NULL,
  `patient_birth_date` date DEFAULT NULL,
  `patient_admission_date` date DEFAULT NULL,
  `patient_discharged_date` date DEFAULT NULL,
  `member_pin` varchar(25) DEFAULT NULL,
  `member_type` varchar(5) DEFAULT NULL,
  `member_lname` varchar(60) DEFAULT NULL,
  `member_fname` varchar(60) DEFAULT NULL,
  `member_mname` varchar(60) DEFAULT NULL,
  `member_suffix` varchar(10) DEFAULT NULL,
  `member_birth_date` date DEFAULT NULL,
  `member_relation` char(1) DEFAULT NULL,
  `member_employer_no` varchar(25) DEFAULT NULL,
  `member_employer_name` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `encounter_nr` (`encounter_nr`),
  KEY `tracking_number` (`tracking_number`),
  CONSTRAINT `FK_encounter_nr` FOREIGN KEY (`encounter_nr`) REFERENCES `care_encounter` (`encounter_nr`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
