ALTER TABLE hisdb.`care_encounter_notes` ADD COLUMN nBmi DOUBLE NULL AFTER is_deleted;
ALTER TABLE `hisdb`.`seg_diet` CHANGE `diet_code` `diet_code` VARCHAR(30) CHARSET latin1 COLLATE latin1_swedish_ci NOT NULL, ADD PRIMARY KEY (`diet_code`);

INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('LFLS', 'Low Fat And Low Salt', 'active'); 
INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('HA', 'Hypoallergenic', 'active');
INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('BRF', 'Breastfeeding', 'active'); 
INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('FTW', 'Food to Watcher', 'active');
INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('ReD', 'Regular Diet', 'active'); 
INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('F75', 'F-75', 'active'); 
INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('F100', 'F-100', 'active'); 
INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('SuP', 'Supplementary', 'active'); 
INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('JeJu', 'Jejunostomy', 'active');
INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('FTPed', 'Food To Patient', 'active'); 
INSERT INTO `hisdb`.`seg_diet` (`diet_code`, `diet_name`, `status`) VALUES ('LAC', 'Lactose Free', 'active'); 



#Dietary Module
CREATE TABLE `hisdb`.`seg_counseled`( `id` INT(11) NOT NULL, `pid` VARCHAR(12) NOT NULL, `encounter_nr` VARCHAR(12) NOT NULL, `visited_dt` DATE, `assessment` TEXT, `plan` TEXT, `suggested_diet` TEXT, `updated_diet` TEXT, `in_charged` VARCHAR(35) DEFAULT '', `followup_dt` DATE, `create_id` VARCHAR(35) DEFAULT '', `create_dt` TIMESTAMP NOT NULL DEFAULT '0000-00-00', `modify_id` VARCHAR(35) DEFAULT '', `modify_dt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ); 
CREATE TABLE `hisdb`.`seg_counseled_discharged` (
  `id` INT (11) NOT NULL,
  `pid` VARCHAR (12) NOT NULL,
  `encounter_nr` VARCHAR (12) NOT NULL,
  `visited_dt` DATE,
  `assessment` TEXT,
  `plan` TEXT,
  `status` ENUM('seen','managed'),
  `isreferral` TINYINT(1) DEFAULT 0 COMMENT 'if 0 with referral, 1 without referral',
  `suggested_diet` TEXT,
  `updated_diet` TEXT,
  `in_charged` VARCHAR (35) DEFAULT '',
  `followup_dt` DATE,
  `create_id` VARCHAR (35) DEFAULT '',
  `create_dt` TIMESTAMP NOT NULL DEFAULT '0000-00-00',
  `modify_id` VARCHAR (35) DEFAULT '',
  `modify_dt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ;


CREATE TABLE `hisdb`.`seg_counseled_monitoring` (
  `id` INT (11) NOT NULL,
  `pid` VARCHAR (12) NOT NULL,
  `encounter_nr` VARCHAR (12) NOT NULL,
  `visited_dt` DATE,
  `assessment` TEXT,
  `plan` TEXT,
  `status` ENUM('seen','managed'),
  `isreferral` TINYINT(1) DEFAULT 0  COMMENT 'if 0 with referral, 1 without referral',
  `suggested_diet` TEXT,
  `updated_diet` TEXT,
  `in_charged` VARCHAR (35) DEFAULT '',
  `followup_dt` DATE,
  `create_id` VARCHAR (35) DEFAULT '',
  `create_dt` TIMESTAMP NOT NULL DEFAULT '0000-00-00',
  `modify_id` VARCHAR (35) DEFAULT '',
  `modify_dt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ;




ALTER TABLE `care_ward` ADD COLUMN `is_nourish_rm` TINYINT(1) DEFAULT 0 NULL AFTER `create_time`; 

CREATE TABLE `seg_diet_type`( `code` VARCHAR(30) NOT NULL, `type` VARCHAR(300), `status` CHAR(18), PRIMARY KEY (`code`) ); 

INSERT INTO `seg_diet_type` (`code`, `type`, `status`) VALUES ('ODL', 'Oral Diet List', 'active');

INSERT INTO `seg_diet_type` (`code`, `type`, `status`) VALUES ('TFL', 'Tubefeeding Diet', 'active'); 

 INSERT INTO `seg_diet_type` (`code`, `type`, `status`) VALUES ('HL', 'Halal List', 'active');

INSERT INTO `seg_diet_type` (`code`, `type`, `status`) VALUES ('NL', 'Nourishment List', 'active');

INSERT INTO `seg_diet_type` (`code`, `type`, `status`) VALUES ('WLD', 'Ward List Diet', 'active');


ALTER TABLE `seg_diet` ADD COLUMN `is_oral` TINYINT(1) DEFAULT 0 NULL COMMENT 'for oral diet only' AFTER `status`, ADD COLUMN `is_tubefeeding` TINYINT(1) DEFAULT 0 NULL COMMENT 'for tubefeeding diet only' AFTER `is_oral`; 
ALTER TABLE `care_ward` ADD COLUMN `is_nourish_rm` TINYINT(1) DEFAULT 0 NULL COMMENT 'for nourishment diet only' AFTER `create_time`; 


#12272017 Addto Nourishment
CREATE TABLE `seg_nourishment`( `id` INT(11) NOT NULL, `encounter_nr` VARCHAR(12) NOT NULL, `is_nourish` TINYINT(1) DEFAULT 0, PRIMARY KEY (`id`) ); 
ALTER TABLE `seg_nourishment` ADD COLUMN `updated_at` TIMESTAMP NULL AFTER `is_nourish`, ADD COLUMN `created_at` TIMESTAMP NULL AFTER `updated_at`; 
ALTER TABLE `seg_nourishment` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT; 

CREATE TABLE `seg_diet_order_cut_off` (
  `refno` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `encounter_nr` VARCHAR(12) NOT NULL,
  `created_by` VARCHAR(35) NOT NULL,
  `updated_by` VARCHAR(35) NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `selected_type` ENUM('breakfast','lunch','dinner') DEFAULT 'breakfast' COMMENT 'breakfast,lunch,dinner',
  PRIMARY KEY (`refno`),
  KEY `seg_diet_order_cutoff_encounter_nr_foreign` (`encounter_nr`),
  CONSTRAINT `seg_diet_order_cutoff_encounter_nr_foreign` FOREIGN KEY (`encounter_nr`) REFERENCES `care_encounter` (`encounter_nr`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE `seg_diet_order_item_cut_off` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `refno` INT(10) UNSIGNED NOT NULL,
  `b` VARCHAR(55) NOT NULL DEFAULT 'FD' COMMENT 'Diet Assigned for Breakfast',
  `l` VARCHAR(55) NOT NULL DEFAULT 'FD' COMMENT 'Diet Assigned for Lunch',
  `d` VARCHAR(55) NOT NULL DEFAULT 'FD' COMMENT 'Diet Assigned for Dinner',
  `medical_diagnosis` TEXT NOT NULL COMMENT 'Medical diagnosis of the Patient',
  `assessment_date` DATETIME NOT NULL COMMENT 'Assessment Date of the diet',
  `is_counseled` TINYINT(1) NOT NULL COMMENT 'If the Patient Is counseled',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seg_diet_order_cut_off_refno` (`refno`),
  CONSTRAINT `seg_diet_order_cut_off_refno` FOREIGN KEY (`refno`) REFERENCES `seg_diet_order_cut_off` (`refno`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


ALTER TABLE `seg_diet_order_item_cut_off` DROP COLUMN `medical_diagnosis`, DROP COLUMN `assessment_date`, DROP COLUMN `is_counseled`; 
INSERT INTO `seg_dashlet_classes` (`id`, `name`, `icon`, `category`, `class_path`, `class_file`) VALUES ('CounselingNotes', 'Counseling Notes', 'page_edit.png', 'Patient', 'modules/dashboard/dashlets/CounselingNotes/', 'CounselingNotes.php'); 
