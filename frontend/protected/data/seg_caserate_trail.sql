CREATE TABLE `hisdb`.`seg_caserate_trail`(  
  `encounter_nr` BIGINT(20) NOT NULL,
  `modified_by` VARCHAR(50),
  `date_modified` DATETIME,
  `history` TEXT,
  `package_id` VARCHAR(12) NOT NULL,
  `rate_type` TINYINT(1) NOT NULL,
  `amount` DECIMAL(20,4) DEFAULT 0.0000,
  `saved_multiplier` SMALLINT(5) NOT NULL,
  PRIMARY KEY (`encounter_nr`, `package_id`, `rate_type`, `saved_multiplier`)
);