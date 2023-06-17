CREATE TABLE seg_blood_waiver_details(
  `batch_nr` VARCHAR (20) NOT NULL,
  `pid` VARCHAR (20),
  `details` LONGTEXT,
  `create_time` DATETIME DEFAULT '0000-00-00 00:00:00',
  `create_id` VARCHAR (50),
  `modify_time` DATETIME DEFAULT '0000-00-00 00:00:00',
  `modify_id` VARCHAR (50),
  PRIMARY KEY (`batch_nr`),
  CONSTRAINT `FK_seg_blood_waiver_details_pid` FOREIGN KEY (`pid`) REFERENCES `care_person` (`pid`) ON UPDATE CASCADE
) ;