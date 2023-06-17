-- SPMC - 1091 DB Changes

-- start here
ALTER TABLE `hisdb`.`seg_encounter_location_addtl`   
  ADD COLUMN `is_deleted` INT DEFAULT 0  NULL AFTER `occupy_date_from`;
-- end here

-- start here
DROP TABLE IF EXISTS `seg_accommodation_trail`;

CREATE TABLE `seg_accommodation_trail` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `encounter_nr` VARCHAR(12) NOT NULL,
  `history` TEXT NOT NULL,
  `create_id` VARCHAR(35) NOT NULL DEFAULT '',
  `create_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_id` VARCHAR(35) NOT NULL DEFAULT '',
  `modify_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_deleted` TINYINT(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_care_encounter_trail` (`encounter_nr`),
  CONSTRAINT `FK_care_encounter_trail` FOREIGN KEY (`encounter_nr`) REFERENCES `care_encounter` (`encounter_nr`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;
-- end here

-- start here
ALTER TABLE `hisdb`.`seg_encounter_location_addtl`   
  ADD COLUMN `is_greater_twelve` TINYINT(1) NULL AFTER `occupy_date_from`;
-- end here

-- start here
ALTER TABLE `hisdb`.`care_ward` 
  ADD COLUMN `can_be_half` TINYINT (1) DEFAULT 0 NULL AFTER `prototype` ;
-- end here

-- start here
ALTER TABLE `hisdb`.`seg_encounter_location_addtl` 
  CHANGE `is_greater_twelve` `is_greater_twelve` INT (11) NULL ;
-- end here

-- start here 
ALTER TABLE `hisdb`.`care_ward`   
  DROP COLUMN `can_be_half`;
-- end here

-- start here
ALTER TABLE `hisdb`.`care_type_room`   
  ADD COLUMN `can_be_half` TINYINT(1) DEFAULT 0  NULL AFTER `status`;
-- end here

-- start here
UPDATE care_type_room SET can_be_half = 1 WHERE nr IN ('2','8','11', '12', '19', '34', '37', '40', '43');
-- end here

-- start here
INSERT INTO `hisdb`.`care_config_global` (`type`, `value`) VALUES ('ACCOMMODATION_REVISION', '2019-03-22 14:00:00');
-- end here

-- start here
UPDATE `hisdb`.`care_type_room` SET `can_be_half` = '0' WHERE `nr` = '2';
-- end here

-- start here
UPDATE `hisdb`.`care_type_room` SET `can_be_half` = '0' WHERE `nr` = '12';
-- end here

-- start here
UPDATE `hisdb`.`care_type_room` SET `can_be_half` = '0' WHERE `nr` = '43';
-- end here

-- start here
ALTER TABLE `hisdb`.`care_type_room`   
  CHANGE `can_be_half` `is_per_hour` TINYINT(1) DEFAULT 0  NULL;
-- end here

-- start here
ALTER TABLE `hisdb`.`seg_encounter_location_addtl`   
  CHANGE `is_greater_twelve` `is_per_hour` INT(11) NULL;
-- end here

-- start here
ALTER TABLE `hisdb`.`seg_encounter_location_addtl`   
  CHANGE `occupy_date_from` `occupy_date_from` DATE NULL  AFTER `occupy_date`,
  ADD COLUMN `occupy_time_from` TIME NULL AFTER `occupy_date_from`,
  ADD COLUMN `occupy_time_to` TIME NULL AFTER `occupy_date_to`,
  CHANGE `modify_id` `modify_id` VARCHAR(35) CHARSET latin1 COLLATE latin1_swedish_ci NULL  AFTER `occupy_time_to`;
-- end here
