ALTER TABLE `hisdb`.`seg_define_config` CHANGE `value` `value` TEXT CHARSET latin1 COLLATE latin1_bin NULL; 
INSERT INTO `seg_define_config` (`param`, `value`) VALUES ('BB_COLOR_BLUE', '(For Neonates and Infants less than 4 months old - BLUE)'); 
ALTER TABLE `seg_blood_compatibility` ADD COLUMN `child` VARCHAR(255) NULL AFTER `adult`;
UPDATE `seg_blood_compatibility` SET `child` = 'SPMC-F-BTS-10B' WHERE `pedia` = 'SPMC-F-BTS-10C' AND `adult` = 'SPMC-F-BTS-10A' AND `child` IS NULL;
UPDATE `seg_blood_compatibility` SET `pedia` = 'SPMC-F-BTS-10C' WHERE `pedia` = 'SPMC-F-BTS-10B' AND `adult` = 'SPMC-F-BTS-10A'; 


-- Update 09/06/2018
INSERT INTO `hisdb`.`seg_opdarea` (`id`, `opd_name`, `accomodation_type`) VALUES ('8', 'OPD - Pay', '2'); 
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'OPD - Service' WHERE `id` = '1'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'OPD - Pay' WHERE `id` = '2'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'ASU - Service' WHERE `id` = '3';
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'ASU - Pay' WHERE `id` = '4'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'HI - Pay' WHERE `id` = '6'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'ONCO - Pay' WHERE `id` = '8';