-- Added by MAtsuu SPMC-688

CREATE TABLE `seg_charity_grants_expiry` (
  `encounter_nr` VARCHAR(12) NOT NULL,
  `grant_dte` DATETIME NOT NULL,
  `pwd_id` VARCHAR(30) DEFAULT NULL COMMENT 'PWD ID Number',
  `pwd_expiry` DATE DEFAULT NULL COMMENT 'PWD ID Expiry Date',
  PRIMARY KEY (`encounter_nr`,`grant_dte`),
  CONSTRAINT `seg_charity_grants_expiry_ibfk_1` FOREIGN KEY (`encounter_nr`) REFERENCES `care_encounter` (`encounter_nr`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;


CREATE TABLE `seg_charity_grants_expiry_pid` (
  `pid` VARCHAR(12) NOT NULL,
  `grant_dte` DATETIME NOT NULL,
  `pwd_id` VARCHAR(30) DEFAULT NULL COMMENT 'PWD ID Number',
  `pwd_expiry` DATE DEFAULT NULL COMMENT 'PWD ID Expiry Date',
  PRIMARY KEY (`pid`,`grant_dte`),
  CONSTRAINT `seg_charity_grants_expiry_pid_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `care_person` (`pid`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;



-- Added by Matsuu for SPMC-688 
-- For PWD-A
INSERT INTO `seg_discount` (
  `discountid`,
  `discountdesc`,
  `discount`,
  `is_forall`,
  `parentid`,
  `modify_id`,
  `create_id`,
  `non_social_discount`
) 
VALUES
  (
    'PWD-A',
    'A (PWD)',
    '0.20000000',
    '1',
    'A',
    'Administrator',
    'admin',
    '0.00000000'
  ) ;
-- For PWD-B
INSERT INTO `seg_discount` (
  `discountid`,
  `discountdesc`,
  `discount`,
  `is_forall`,
  `parentid`,
  `modify_id`,
  `create_id`,
  `non_social_discount`
) 
VALUES
  (
    'PWD(B)',
    'B (PWD)',
    '0.20000000',
    '1',
    'B',
    'Administrator',
    'admin',
    '0.00000000'
  ) ;

-- For PWD-C1
INSERT INTO `seg_discount` (
  `discountid`,
  `discountdesc`,
  `discount`,
  `is_forall`,
  `parentid`,
  `modify_id`,
  `create_id`,
  `non_social_discount`
) 
VALUES
  (
    'PWD(C1)',
    'C1 (PWD)',
    '0.25000000',
    '1',
    'C1',
    'Administrator',
    'admin',
    '0.00000000'
  ) ;

-- For PWD-C2
  INSERT INTO `seg_discount` (
  `discountid`,
  `discountdesc`,
  `discount`,
  `is_forall`,
  `parentid`,
  `modify_id`,
  `create_id`,
  `non_social_discount`
) 
VALUES
  (
    'PWD(C2)',
    'C2 (PWD)',
    '0.50000000',
    '1',
    'C1',
    'Administrator',
    'admin',
    '0.00000000'
  ) ;

 -- For PWD-C3
 INSERT INTO `seg_discount` (
  `discountid`,
  `discountdesc`,
  `discount`,
  `is_forall`,
  `parentid`,
  `modify_id`,
  `create_id`,
  `non_social_discount`
) 
VALUES
  (
    'PWD(C3)',
    'C3 (PWD)',
    '0.60000000',
    '1',
    'C1',
    'Administrator',
    'admin',
    '0.00000000'
  ) ;

  UPDATE `hisdb`.`seg_discount` SET `non_social_discount` = '0.20000000' WHERE `discountid` = 'PWD(C2)'; 
  UPDATE `hisdb`.`seg_discount` SET `non_social_discount` = '0.20000000' WHERE `discountid` = 'PWD(B)'; 
  UPDATE `hisdb`.`seg_discount` SET `non_social_discount` = '0.20000000' WHERE `discountid` = 'PWD(C1)'; 
  UPDATE `hisdb`.`seg_discount` SET `non_social_discount` = '0.20000000' WHERE `discountid` = 'PWD(C3)'; 
  UPDATE `hisdb`.`seg_discount` SET `non_social_discount` = '0.20000000' WHERE `discountid` = 'PWD-A';
  UPDATE `hisdb`.`seg_discount` SET `non_social_discount` = '0.20000000' WHERE `discountid` = 'SC'; 
  UPDATE `hisdb`.`seg_discount` SET `non_social_discount` = '0.20000000' WHERE `discountid` = 'PWD'; 
  UPDATE `hisdb`.`seg_discount` SET `discount` = '0.200000000'  WHERE `discountid` = 'PWD'; 
-- Ended here for SPMC-688



CREATE TABLE `seg_social_expiry` (
  `id` INT(15) NOT NULL AUTO_INCREMENT,
  `pid` VARCHAR(12) NOT NULL DEFAULT '0',
  `discountid` VARCHAR(10) NOT NULL,
  `discount` DECIMAL(10,8) NOT NULL,
  `pwd_id` VARCHAR(30) NOT NULL COMMENT 'PWD ID Number',
  `pwd_expiry` DATE DEFAULT NULL COMMENT 'PWD ID Expiry Date',
  `create_id` VARCHAR(35) DEFAULT NULL,
  `create_dt` TIMESTAMP NULL DEFAULT NULL,
  `modify_id` VARCHAR(35) DEFAULT NULL,
  `modify_dt` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seg_social_expiry_pid` (`pid`),
  KEY `seg_social_expiry_discountid` (`discountid`),
  CONSTRAINT `seg_social_expiry_discountid` FOREIGN KEY (`discountid`) REFERENCES `seg_discount` (`discountid`),
  CONSTRAINT `seg_social_expiry_pid` FOREIGN KEY (`pid`) REFERENCES `care_person` (`pid`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;


UPDATE `seg_discount` SET `discountid` = 'B-PWD' , `discountdesc` = 'B-PWD' WHERE `discountid` = 'PWD(B)'; 
UPDATE `seg_discount` SET `discountid` = 'C1-PWD' , `discountdesc` = 'C1-PWD' WHERE `discountid` = 'PWD(C1)'; 
UPDATE `seg_discount` SET `discountid` = 'C2-PWD' , `discountdesc` = 'C2-PWD' WHERE `discountid` = 'PWD(C2)'; 
UPDATE `seg_discount` SET `discountid` = 'C3-PWD' , `discountdesc` = 'C3-PWD' WHERE `discountid` = 'PWD(C3)'; 
UPDATE `seg_discount` SET `discountid` = 'A-PWD' , `discountdesc` = 'A-PWD' WHERE `discountid` = 'PWD-A'; 


