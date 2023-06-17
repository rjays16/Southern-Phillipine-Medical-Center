-- Added by Matsuu for SPMC-758
-- ALTER TABLE `seg_radio_serv` ADD COLUMN `fromobgyne` TINYINT(1) DEFAULT 0 NULL AFTER `is_printed`; 
-- ALTER TABLE `hisdb`.`seg_radio_serv` CHANGE `fromobgyne` `ref_source` VARCHAR(12) DEFAULT 'RD' NULL; 
ALTER TABLE `hisdb`.`seg_radio_serv` ADD COLUMN `fromdept` VARCHAR(12) DEFAULT 'RD' NULL AFTER `is_printed`; 
-- Ended here..