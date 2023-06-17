CREATE TABLE `hisdb`.`seg_pledge_commitment_details`(  
  `batch_nr` VARCHAR(20) NOT NULL,
  `encounter_nr` VARCHAR(12),
  `pid` VARCHAR(20),
  `donated_to` VARCHAR(50),
  `blood_type` VARCHAR(12),
  `no_of_units` INT(11),
  `components` VARCHAR(255),
  `watcher_name` VARCHAR(255),
  `create_time` DATETIME DEFAULT '0000-00-00 00:00:00',
  `create_id` VARCHAR(100),
  `modify_time` DATETIME DEFAULT '0000-00-00 00:00:00',
  `modify_id` VARCHAR(100),
  PRIMARY KEY (`batch_nr`),
  CONSTRAINT `FK_seg_pledge_commitment_details_pid` FOREIGN KEY (`pid`) REFERENCES `hisdb`.`care_person`(`pid`) ON UPDATE CASCADE ON DELETE NO ACTION
);
