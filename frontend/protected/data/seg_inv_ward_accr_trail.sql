-- Created by Matsuu 
CREATE TABLE `hisdb`.`seg_inv_ward_accr_trail` (
  `action_id` INT (255) NOT NULL AUTO_INCREMENT,
  `personell_nr` INT (11),
  `item` VARCHAR (255),
  `action_personell` VARCHAR (20),
  `action_abbr` VARCHAR (10),
  `group_action` VARCHAR (50),
  `date_action` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`action_id`)
) ;
-- Ended here..
