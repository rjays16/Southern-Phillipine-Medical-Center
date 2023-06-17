INSERT INTO `hisdb`.`seg_type_request_source` (`id`, `source_name`) VALUES ('OBGUSD', 'OB-Gyne Ultrasound'); 
-- Ended here..

-- Added by Matsuu 08152018
INSERT INTO `seg_dashlet_classes` (`id`, `name`, `icon`, `category`, `class_path`, `class_file`) VALUES ('PatientOBGyneResults', 'OB Gyne Results', 'ob.png', 'Patient', 'modules/dashboard/dashlets/PatientOBGyneResults/', 'PatientOBGyneResults.php'); 
-- Ended here...

-- Added by Matsuu 08232018
ALTER TABLE `seg_radio_services` ADD COLUMN `pf` DECIMAL(10,2) DEFAULT 0.00 NOT NULL AFTER `is_IC`; 
ALTER TABLE `seg_charity_amount` CHANGE `ref_source` `ref_source` ENUM('PP','FB','LD','RD','OR','PH','MD','OB') CHARSET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `hisdb`.`seg_pay_request` CHANGE `ref_source` `ref_source` ENUM('PP','FB','LD','RD','OR','PH','MD','OTHER','IC','MISC','ICB','MDC','MHC','OB','POC') CHARSET latin1 COLLATE latin1_swedish_ci NOT NULL; 
-- Ended here...

-- Added by Matsuu 09132018 



CREATE TABLE `seg_radio_doctors_pf` (
  `pf_nr` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  `refno` VARCHAR(12) NOT NULL,
  `dr_nr` INT(11) NOT NULL COMMENT 'nr in care_personnel',
  `pf_amount` DECIMAL(10,2) DEFAULT NULL COMMENT 'column(pf) in seg_radio_services',
  `accomodation_type` TINYINT(1) DEFAULT '2',
  `service_code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` TINYINT(1) DEFAULT '0',
  `create_id` VARCHAR(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'base on login name',
  `create_dt` DATETIME DEFAULT NULL,
  `modify_id` VARCHAR(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'base on login name',
  `modify_dt` DATETIME DEFAULT NULL,
  PRIMARY KEY (`pf_nr`),
  KEY `FK_seg_radio_doctors_pf_refno` (`refno`),
  CONSTRAINT `FK_seg_radio_doctors_pf_refno` FOREIGN KEY (`refno`) REFERENCES `seg_radio_serv` (`refno`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;



ALTER TABLE `care_test_request_radio` ADD COLUMN `pf` DECIMAL(10,2) DEFAULT 0.00 NULL AFTER `save_and_done`

ALTER TABLE `seg_radio_services` ADD COLUMN `is_socialized_pf` TINYINT(1) DEFAULT 1 NOT NULL AFTER `pf`; 

CREATE TABLE `seg_lingap_entries_obgyne`( `entry_id` CHAR(36) NOT NULL, `ref_no` VARCHAR(10) NOT NULL, `service_code` VARCHAR(15) NOT NULL, `service_name` VARCHAR(100) NOT NULL, `quantity` DECIMAL(18,4) NOT NULL DEFAULT 1.0000, `amount` DECIMAL(18,4) NOT NULL DEFAULT 0.0000, PRIMARY KEY (`ref_no`, `service_code`), INDEX `FK_seg_lingap_entries_obgnye` (`entry_id`), CONSTRAINT `FK_seg_lingap_entries_obgyne` FOREIGN KEY (`entry_id`) REFERENCES `hisdb`.`seg_lingap_entries`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, CONSTRAINT `FK_seg_lingap_entries_obgyne_items` FOREIGN KEY (`ref_no`, `service_code`) REFERENCES `hisdb`.`care_test_request_radio`(`refno`, `service_code`) ON UPDATE CASCADE ) ENGINE=INNODB CHARSET=latin1 COLLATE=latin1_swedish_ci; 
CREATE TABLE `seg_cmap_entries_obgyne`( `id` CHAR(36) NOT NULL, `referral_id` CHAR(36) NOT NULL, `ref_no` VARCHAR(12) NOT NULL, `pid` VARCHAR(12), `walkin_pid` VARCHAR(12), `service_code` VARCHAR(15) NOT NULL, `service_name` VARCHAR(100) NOT NULL, `quantity` DECIMAL(10,4) NOT NULL DEFAULT 1.0000, `amount` DECIMAL(10,4) NOT NULL DEFAULT 0.0000, `remarks` TINYTEXT NOT NULL, `create_id` VARCHAR(35), `create_time` DATETIME DEFAULT '0000-00-00 00:00:00', `modify_id` VARCHAR(35), `modify_time` DATETIME DEFAULT '0000-00-0000:00:00', PRIMARY KEY (`id`), UNIQUE INDEX `NewIndex1` (`referral_id`, `ref_no`, `service_code`), INDEX `FK_seg_cmap_entries_ob` (`ref_no`, `service_code`), INDEX `FK_seg_cmap_entries_ob_person` (`pid`), INDEX `FK_seg_cmap_entries_obgyne_referral` (`referral_id`), INDEX `FK_seg_cmap_entries_obgyne_creator` (`create_id`), INDEX `FK_seg_cmap_obgyne_modifier` (`modify_id`), INDEX `FK_seg_cmap_entries_obgyne_walkin` (`walkin_pid`), CONSTRAINT `FK_seg_cmap_entries_obgyne_creator` FOREIGN KEY (`create_id`) REFERENCES `hisdb`.`care_users`(`login_id`) ON UPDATE CASCADE ON DELETE SET NULL, CONSTRAINT `FK_seg_cmap_obgyne_modifier` FOREIGN KEY (`modify_id`) REFERENCES `hisdb`.`care_users`(`login_id`) ON UPDATE CASCADE ON DELETE SET NULL, CONSTRAINT `FK_seg_cmap_entries_obgyne_referral` FOREIGN KEY (`referral_id`) REFERENCES `hisdb`.`seg_cmap_referrals`(`id`) ON UPDATE CASCADE, CONSTRAINT `FK_seg_cmap_entries_obgyne_walkin` FOREIGN KEY (`walkin_pid`) REFERENCES `hisdb`.`seg_walkin`(`pid`) ON UPDATE CASCADE, CONSTRAINT `FK_seg_cmap_entries_ob` FOREIGN KEY (`ref_no`, `service_code`) REFERENCES `hisdb`.`care_test_request_radio`(`refno`, `service_code`) ON UPDATE CASCADE ON DELETE CASCADE, CONSTRAINT `FK_seg_cmap_entries_ob_person` FOREIGN KEY (`pid`) REFERENCES `hisdb`.`care_person`(`pid`) ON UPDATE CASCADE ON DELETE CASCADE ) ENGINE=INNODB CHARSET=latin1 COLLATE=latin1_swedish_ci; 
ALTER TABLE `seg_credit_memo_details` CHANGE `ref_source` `ref_source` ENUM('PH','LD','RD','OR','OTHER','FB','PP','OB') CHARSET latin1 COLLATE latin1_swedish_ci NOT NULL; 


ALTER TABLE `seg_radio_service_groups` ADD COLUMN `fromdept` VARCHAR(10) DEFAULT 'RD' NULL AFTER `create_dt`; 



ALTER TABLE `seg_granted_request` CHANGE `ref_source` `ref_source` ENUM('PP','FB','LD','RD','OR','PH','MD','OB') CHARSET latin1 COLLATE latin1_swedish_ci NOT NULL; 
ALTER TABLE `seg_payment_workaround` CHANGE `service_area` `service_area` ENUM('LB','RD','PH','OB') CHARSET latin1 COLLATE latin1_swedish_ci NOT NULL; 


DELIMITER $$

USE `hidb`$$

DROP FUNCTION IF EXISTS `fn_get_personell_lastname_first_by_loginid`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_personell_lastname_first_by_loginid`(personell_nr VARCHAR (50)) RETURNS VARCHAR(100) CHARSET latin1
    DETERMINISTIC
BEGIN
  DECLARE personell_name VARCHAR (100) ;
  SET personell_name := 
  (SELECT 
    CONCAT(
      TRIM(cp_2.name_last),
      ', ',
      TRIM(cp_2.name_first),
      ' ',
      IF(
        TRIM(cp_2.name_middle) <> '',
        CONCAT(
          LEFT(TRIM(cp_2.name_middle), 1),
          '. '
        ),
        ''
      )
    ) AS fullname 
  FROM
    care_personell AS cpl_2,
    care_person AS cp_2,
    care_users AS cu 
  WHERE cu.login_id = personell_nr 
    AND cu.personell_nr = cpl_2.nr 
    AND cp_2.pid = cpl_2.pid) ;
  RETURN (personell_name) ;
END$$

DELIMITER ;


 ALTER TABLE `seg_radio_impression_code` ADD COLUMN `fromdept` VARCHAR(35) DEFAULT 'RD' NULL AFTER `create_dt`; 
 ALTER TABLE `seg_radio_findings_code` ADD COLUMN `fromdept` VARCHAR(35) DEFAULT 'RD' NULL AFTER `create_dt`; 


INSERT INTO `care_config_global` (`type`, `value`) VALUES ('all_sonologist', '\'100540\',\'106360\',\'111472\',\'100545\',\'100456\',\'109421\',\'100693\''); 


INSERT INTO `hisdb`.`seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `with_template`, `template_name`) VALUES ('OBGyne_professional_fee_report', 'Hospital Operations', 'Professional Fee Report', 'Professional Fee Report', 'OBGyne_professional_fee_report', '209', 'HOSP', '1', 'OBGyne_professional_fee_report'); 

INSERT INTO `seg_rep_template_params` (`report_id`, `param_id`) VALUES ('OBGyne_professional_fee_report', 'alpha'); 



INSERT INTO `seg_rep_params` (`param_id`, `parameter`, `param_type`, `choices`) VALUES ('gyne_procedures', 'OB-GYN Ultrasound Procedures', 'sql', '(SELECT \r\n  \'all\' AS id,\r\n  \'All\' AS namedesc) UNION ALL \r\n  (\r\nSELECT \r\n  srs.`service_code` AS id,\r\n  srs.`name` AS namedesc \r\nFROM\r\n  `seg_radio_services` AS srs \r\n  INNER JOIN `seg_radio_service_groups` AS srsg \r\n    ON srs.`group_code` = srsg.`group_code` \r\nWHERE srs.`status` NOT IN (\'deleted\') \r\n  AND srsg.`fromdept` = \'OB\' \r\n      ) '); 
INSERT INTO `seg_rep_template_params` (`report_id`, `param_id`) VALUES ('OBGyne_professional_fee_report', 'gyne_procedures'); 


INSERT INTO `seg_rep_params` (`param_id`, `parameter`, `param_type`, `choices`) VALUES ('gyne_doctor', 'OB-GYN Ultrasound Doctors', 'sql', '(SELECT \r\n  \'all\' AS id,\r\n  \'All\' AS namedesc) \r\nUNION\r\nALL \r\n(SELECT \r\n  cp.nr AS id,\r\n  `fn_get_person_lastname_first` (cp.`pid`) AS namedesc \r\nFROM\r\n  `care_personell` AS cp \r\nWHERE cp.`nr` IN (\r\n    \'100540\',\r\n    \'106360\',\r\n    \'110312\',\r\n    \'100545\',\r\n    \'100456\',\r\n    \'109421\',\r\n    \'100693\')) ;\r\n\r\n'); 
INSERT INTO `seg_rep_template_params` (`report_id`, `param_id`) VALUES ('OBGyne_professional_fee_report', 'gyne_doctor'); 


#note d pa ni final na changes
ALTER TABLE `hisdb`.`seg_encounter_privy_dr` ADD COLUMN `from_ob` TINYINT(1) DEFAULT 0 NULL AFTER `is_deleted`; 



CREATE TABLE `seg_encounter_pf_dr` (
  `encounter_nr` VARCHAR(12) NOT NULL,
  `dr_nr` INT(11) NOT NULL,
  `dr_role_type_nr` SMALLINT(5) UNSIGNED NOT NULL,
  `entry_no` SMALLINT(5) UNSIGNED NOT NULL,
  `dr_level` SMALLINT(1) NOT NULL DEFAULT '1',
  `days_attended` INT(10) UNSIGNED NOT NULL,
  `caserate` INT(10) NOT NULL DEFAULT '0',
  `dr_charge` DECIMAL(20,4) NOT NULL DEFAULT '0.0000',
  `is_excluded` TINYINT(1) NOT NULL DEFAULT '0',
  `modify_id` VARCHAR(35) NOT NULL,
  `modify_dt` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `create_id` VARCHAR(35) NOT NULL,
  `create_dt` TIMESTAMP NULL DEFAULT '0000-00-00 00:00:00',
  `from_date` DATE DEFAULT NULL,
  `to_date` DATE DEFAULT NULL,
  `service_code` VARCHAR(10) DEFAULT NULL,
  `is_served` TINYINT(1) DEFAULT NULL,
  `history` TEXT,
  `is_deleted` TINYINT(1) DEFAULT '0',
  PRIMARY KEY (`encounter_nr`,`dr_nr`,`dr_role_type_nr`,`entry_no`)
) ENGINE=INNODB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

