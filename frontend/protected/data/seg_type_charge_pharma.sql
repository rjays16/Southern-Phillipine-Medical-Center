create table `seg_type_charge_pharma` (
	`id` varchar (90),
	`charge_name` varchar (225),
	`description` text ,
	`ordering` tinyint (1),
	`is_excludedfrombilling` tinyint (1),
	`in_pharmacy` tinyint (1)
); 
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('','','','0','1','0');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('CHARITY','CHARITY','Approved by Social Service','4','1','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('CMAP','MAP','Covered by MAP','5','1','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('DSWD','DSWD','DSWD','8','1','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('EP','EP','EP','7','1','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('LINGAP','LINGAP','Covered by Lingap','6','1','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('MISSION','MISSION','Sponsored','5','1','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('NSC-M','NSC-M','New Born Screening - Mindanao','11','1','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('PAY','PAY','PAY','3','1','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('PCSO','PCSO','Covered by PCSO','9','1','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('PERSONAL','TPL','TPL','1','0','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('PHIC','PHIC','Philhealth','2','0','1');
insert into `seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) values('POPCOM','POPCOM','Popcom','10','1','1');

ALTER TABLE `seg_type_charge_pharma` CHANGE `id` `id` VARCHAR(30) CHARSET latin1 COLLATE latin1_swedish_ci NOT NULL, ADD PRIMARY KEY (`id`);

ALTER TABLE `seg_pharma_orders` CHANGE `charge_type` `charge_type` VARCHAR(30) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT 'PERSONAL' NOT NULL;

SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT;
SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS;
SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION;
SET NAMES utf8;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0; 
ALTER TABLE `seg_pharma_orders` ADD CONSTRAINT `FK_seg_pharma_orders_charges` FOREIGN KEY (`charge_type`) REFERENCES `hisdb`.`seg_type_charge_pharma`(`id`);
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT;
SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS;
SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION;
SET SQL_NOTES=@OLD_SQL_NOTES; 