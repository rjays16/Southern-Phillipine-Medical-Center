CREATE TABLE `hisdb`.`seg_notice_tbl`(  
  `note_id` INT(11) NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(50) NOT NULL,
  `date_published` VARCHAR(50) NOT NULL,
  `note_date` VARCHAR(50) NOT NULL,
  `time_from` VARCHAR(50) NOT NULL,
  `time_to` VARCHAR(50) NOT NULL,
  `venue` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(100) NOT NULL,
  `status` INT(11) NOT NULL,
  `notice_attchmnt` VARCHAR(255) NOT NULL,
  `is_deleted` INT(11) NOT NULL,
  `date_created` DATETIME,
  PRIMARY KEY (`note_id`)
);


CREATE TABLE `hisdb`.`seg_notice_acknledgmnts`(  
  `ack_id` INT(11) NOT NULL AUTO_INCREMENT,
  `sess_user` VARCHAR(100) NOT NULL,
  `notice_id` INT(11) NOT NULL,
  `date_ack` VARCHAR(50) NOT NULL,
  `departmnt` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`ack_id`),
  CONSTRAINT `note_id` FOREIGN KEY (`notice_id`) REFERENCES `hisdb`.`seg_notice_tbl`(`note_id`) ON UPDATE CASCADE ON DELETE NO ACTION 
  
);

ALTER TABLE `hisdb`.`seg_notice_tbl` CHANGE `venue` `venue` VARBINARY(100) NOT NULL, CHANGE `subject` `subject` VARBINARY(100) NOT NULL; 