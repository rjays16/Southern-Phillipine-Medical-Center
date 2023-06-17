CREATE TABLE `seg_grant_accounts_allotment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grant_account` int(10) unsigned DEFAULT NULL,
  `grant_account_type` int(10) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `remarks` tinytext,
  `history` tinytext,
  `create_id` varchar(35) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `modify_id` varchar(35) DEFAULT NULL,
  `modify_time` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_grant_accounts_id` (`grant_account`),
  KEY `fk_allotment_grant_account_type_id` (`grant_account_type`),
  CONSTRAINT `fk_allotment_grant_account_type_id` FOREIGN KEY (`grant_account_type`) REFERENCES `seg_grant_account_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_grant_accounts_id` FOREIGN KEY (`grant_account`) REFERENCES `seg_grant_accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1


CREATE TABLE `care_encounter_referrals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `encounter_nr` varchar(12) NOT NULL,
  `entry_date` date DEFAULT NULL,
  `account` int(10) unsigned DEFAULT NULL,
  `sub_account` int(10) unsigned DEFAULT NULL,
  `control_no` varchar(100) DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `balance` decimal(18,2) DEFAULT NULL,
  `remarks` tinytext,
  `history` tinytext,
  `create_id` varchar(35) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `modify_id` varchar(35) DEFAULT NULL,
  `modify_time` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_referrals_encounter_nr` (`encounter_nr`),
  KEY `fk_grant_accounts_sub_id` (`sub_account`),
  KEY `fk_grant_accounts_type_account_id` (`account`),
  CONSTRAINT `fk_grant_accounts_sub_id` FOREIGN KEY (`sub_account`) REFERENCES `seg_grant_accounts` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_grant_accounts_type_account_id` FOREIGN KEY (`account`) REFERENCES `seg_grant_account_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_referrals_encounter_nr` FOREIGN KEY (`encounter_nr`) REFERENCES `care_encounter` (`encounter_nr`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1


ALTER TABLE `hisdb`.`seg_type_request_source`   
  ADD COLUMN `is_costcenter` TINYINT(1) DEFAULT 0  NULL AFTER `source_name`;

UPDATE `seg_type_request_source` SET `is_costcenter` = '1' WHERE `id` = 'RD';
UPDATE `seg_type_request_source` SET `is_costcenter` = '1' WHERE `id` = 'SPL';
UPDATE `seg_type_request_source` SET `is_costcenter` = '1' WHERE `id` = 'PHARMA';
UPDATE `seg_type_request_source` SET `is_costcenter` = '1' WHERE `id` = 'MISC';
UPDATE `seg_type_request_source` SET `is_costcenter` = '1' WHERE `id` = 'LD';
UPDATE `seg_type_request_source` SET `is_costcenter` = '1' WHERE `id` = 'BB';
  
INSERT INTO `seg_type_charge` (
  `id`,
  `charge_name`,
  `description`,
  `ordering`,
  `is_excludedfrombilling`,
  `in_pharmacy`
) 
VALUES
  (
    'crcu',
    'CrCU',
    'Covered by Cash Credit and Collection',
    NULL,
    '1',
    '0'
  )

CREATE TABLE `seg_creditcollection_cash_grants` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `encounter_nr` varchar(12) NOT NULL,
  `refno` varchar(12) NOT NULL,
  `req_source` varchar(20) NOT NULL,
  `itemcode` varchar(20) NOT NULL,
  `account` int(10) unsigned DEFAULT NULL,
  `sub_account` int(10) unsigned DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `balance` decimal(18,2) DEFAULT NULL,
  `control_no` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `history` tinytext,
  `create_id` varchar(35) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `modify_id` varchar(35) DEFAULT NULL,
  `modify_time` datetime DEFAULT NULL,
  `is_full` tinyint(1) DEFAULT '0',
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_creditcollection_account` (`account`),
  KEY `fk_creditcollection_subaccount` (`sub_account`),
  KEY `fk_creditcollection_encounter_nr` (`encounter_nr`),
  CONSTRAINT `fk_creditcollection_account` FOREIGN KEY (`account`) REFERENCES `seg_grant_account_type` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_creditcollection_encounter_nr` FOREIGN KEY (`encounter_nr`) REFERENCES `care_encounter` (`encounter_nr`) ON UPDATE CASCADE,
  CONSTRAINT `fk_creditcollection_subaccount` FOREIGN KEY (`sub_account`) REFERENCES `seg_grant_accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1

UPDATE `hisdb`.`seg_grant_account_type` SET `type_name` = 'hi-5-program' WHERE `id` = '19'

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_1_coh2",
    "_a_2_grant_account_1_coh2"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_1_coh2%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_2_dswd",
    "_a_2_grant_account_2_dswd"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_2_dswd%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_3_fund_checks",
    "_a_2_grant_account_3_fund_checks"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_3_fund_checks%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_4_lingap-emergency",
    "_a_2_grant_account_4_lingap-emergency"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_4_lingap-emergency%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_5_lingap-recommendation",
    "_a_2_grant_account_5_lingap-recommendation"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_5_lingap-recommendation%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_6_map",
    "_a_2_grant_account_6_map"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_6_map%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_7_pcso",
    "_a_2_grant_account_7_pcso"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_7_pcso%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_8_pn",
    "_a_2_grant_account_8_pn"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_8_pn%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_9_doh",
    "_a_2_grant_account_9_doh"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_9_doh%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_10_neda",
    "_a_2_grant_account_10_neda"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_10_neda%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_11_pnp",
    "_a_2_grant_account_11_pnp"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_11_pnp%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_12_private-companies",
    "_a_2_grant_account_12_private-companies"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_12_private-companies%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_13_coh",
    "_a_2_grant_account_13_coh"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_13_coh%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_14_nbb",
    "_a_2_grant_account_14_nbb"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_14_nbb%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_15_infirmary",
    "_a_2_grant_account_15_infirmary"
  ) 
WHERE cu.`permission` LIKE "%_a_2_grant_account_15_infirmary%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_16_dependent",
    "_a_2_grant_account_16_dependent"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_16_dependent%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_17_government-agencies",
    "_a_2_grant_account_17_government-agencies"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_17_government-agencies%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_18_pmdt",
    "_a_2_grant_account_18_pmdt"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_18_pmdt%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_19_hi-5 program",
    "_a_2_grant_account_19_hi-5-program"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_19_hi-5 program%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_20_return-meds",
    "_a_2_grant_account_20_return-meds"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_20_return-meds%";

UPDATE 
  care_users cu 
SET
  cu.`permission` = REPLACE(
    cu.`permission`,
    "_a_1_grant_account_21_test",
    "_a_2_grant_account_21_test"
  ) 
WHERE cu.`permission` LIKE "%_a_1_grant_account_21_test%";