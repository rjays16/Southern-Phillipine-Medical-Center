CREATE TABLE `hisdb`.`seg_orientation_list`(  
  `orientation_list_id` BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_number` INT(11),
  `time_of_orientation` TIME,
  `module_orientation` VARCHAR(50),
  `date_of_orientation` DATE,
  `title_orientation` VARCHAR(50),
  `venue` VARCHAR(50),
  `added_by` VARCHAR(50),
  PRIMARY KEY (`orientation_list_id`)
) ENGINE=INNODB;

  ALTER TABLE `hisdb`.`seg_orientation_list`   
  ADD COLUMN `created_at` DATETIME NULL AFTER `is_deleted`,
  ADD COLUMN `modify_id` VARCHAR(255) NULL AFTER `create_at`,
  ADD COLUMN `modify_date` DATETIME NULL AFTER `modify_id`,
  ADD COLUMN `history` TEXT NULL AFTER `modify_date`;
 

CREATE TABLE `hisdb`.`seg_orientation_venue`(  
  `orientation_venue_id` INT(11) NOT NULL AUTO_INCREMENT,
  `orientation_venue_name` TEXT,
  PRIMARY KEY (`orientation_venue_id`)
);

INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('IHOMP OFFICE'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('MIS'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('KAMAGONG ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('IPBM');  

ALTER TABLE `hisdb`.`seg_orientation_list` ADD COLUMN `is_deleted` INT(1) DEFAULT 0 NULL AFTER `added_by`; 