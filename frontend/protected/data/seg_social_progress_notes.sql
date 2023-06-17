-- Created by Matsuu
CREATE TABLE seg_social_progress_notes (
  `pid` VARCHAR (12) NOT NULL,
  `encounter_nr` VARCHAR (12),
  `progress_date` DATETIME DEFAULT '0000-00-00 00:00:00',
  `ward` SMALLINT (3),
  `diagnosis` TEXT,
  `referral` TEXT,
  `informant`TEXT,
  `relationship` TEXT,
  `purpose` TEXT,
  `action_taken` TEXT,
  `is_deleted` TINYINT (1) DEFAULT 0,
  `create_id` VARCHAR (35),
  `create_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_id` VARCHAR (35),
  `modify_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `history` TEXT
) ;

ALTER TABLE `seg_social_progress_notes` ADD COLUMN `notes_id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`notes_id`)

-- Ended here
