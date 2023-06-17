/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`hisdb` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `hisdb`;

/*Table structure for table `seg_affidavit_father_surename` */

DROP TABLE IF EXISTS `seg_affidavit_father_surename`;

CREATE TABLE `seg_affidavit_father_surename` (
  `pid` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `modify_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `modify_dt` datetime DEFAULT NULL,
  `create_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `history` text CHARACTER SET latin1 COLLATE latin1_bin,
  `create_dt` datetime DEFAULT NULL,
  `affiant_mname` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `affiant_lname` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `affiant_fname` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `affiant_address` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `father_surename` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `child_relationship` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `child_fullname` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `affiant_age` smallint(6) DEFAULT NULL,
  `affiant_status` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `affiant_citizenship` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `child_birth_date` date DEFAULT NULL,
  `child_birth_pro` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `child_birth_mun_cty` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `child_birth_country` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `child_birth_reg_num` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `child_birth_reg_date` date DEFAULT NULL,
  `paternity_reg_num` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `paternity_reg_date` date DEFAULT NULL,
  `paternity_reg_place` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `city_mun_lcro_cert` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `province_lcro_cert` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `country_lcro_cert` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `date_ausf_cert` date DEFAULT NULL,
  `place_ausf_cert` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `administer_personell` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `administer_date` date DEFAULT NULL,
  `administer_place` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `is_self` tinyint(1) DEFAULT '0',
  `is_other` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `seg_affidavit_father_surename` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;