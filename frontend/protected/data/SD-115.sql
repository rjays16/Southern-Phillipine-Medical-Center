ALTER TABLE `seg_diet` ADD COLUMN `alt_code` VARCHAR(30) NULL AFTER `sort_by`; 
UPDATE `seg_diet` SET `alt_code` = 'F-100' WHERE `diet_code` = 'F100'; 
UPDATE `seg_diet` SET `alt_code` = 'F-75' WHERE `diet_code` = 'F75';
UPDATE `seg_diet` SET `alt_code` = 'HAD' WHERE `diet_code` = 'HA';
UPDATE `seg_diet` SET `alt_code` = 'HAD' WHERE `diet_code` = 'HD';
UPDATE `seg_diet` SET `alt_code` = 'Jejunostomy' WHERE `diet_code` = 'JeJu';
UPDATE `seg_diet` SET `alt_code` = 'LChol' WHERE `diet_code` = 'LC';
UPDATE `seg_diet` SET `alt_code` = 'LChol' WHERE `diet_code` = 'LD';
UPDATE `seg_diet` SET `alt_code` = 'LSLF' WHERE `diet_code` = 'LFLS'; 



INSERT INTO `seg_diet` (`diet_code`, `diet_name`, `status`, `sort_by`) VALUES ('+EW', '+Egg Whites', 'active', '32'); 
INSERT INTO `seg_diet` (`diet_code`, `diet_name`, `status`, `sort_by`) VALUES ('SF', 'Strawfeeding', 'active', '33'); 
INSERT INTO `seg_diet` (`diet_code`, `diet_name`, `status`, `sort_by`) VALUES ('Banana-based-BF', 'Banana-based Blenderized Feeding', 'active', '34'); 
INSERT INTO `seg_diet` (`diet_code`, `diet_name`, `status`, `sort_by`) VALUES ('Payaya-based-BF', 'Payaya-based Blenderized Feeding', 'active', '35'); 
INSERT INTO `seg_diet` (`diet_code`, `diet_name`, `status`, `sort_by`) VALUES ('Commercial-BF', 'Pure Commercial Feeding', 'active', '36'); 
INSERT INTO `seg_diet` (`diet_code`, `diet_name`, `status`, `sort_by`) VALUES ('Gastrostomy', 'Gastrostomy', 'active', '37'); 
INSERT INTO `seg_diet` (`diet_code`, `diet_name`, `status`, `sort_by`) VALUES ('OGT', 'Orogastric Tubefeeding', 'active', '38'); 
INSERT INTO `seg_diet` (`diet_code`, `diet_name`, `status`, `sort_by`) VALUES ('Milk-Feeding', 'Milk Feeding', 'active', '39'); 




ALTER TABLE `seg_diet_order_item` CHANGE `b` `b` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NULL COMMENT 'Diet Assigned for Breakfast', CHANGE `l` `l` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NULL COMMENT 'Diet Assigned for Lunch', CHANGE `d` `d` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NULL COMMENT 'Diet Assigned for Dinner'; 
ALTER TABLE `seg_diet_order_item_cut_off` CHANGE `b` `b` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NULL COMMENT 'Diet Assigned for Breakfast', CHANGE `l` `l` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NULL COMMENT 'Diet Assigned for Lunch', CHANGE `d` `d` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '' NULL COMMENT 'Diet Assigned for Dinner'; 


 UPDATE `seg_diet` SET `status` = 'inactive' WHERE `diet_code` = 'SuP'; 
 UPDATE `seg_diet` SET `status` = 'inactive' WHERE `diet_code` = 'MF'; 
 UPDATE `hisdb`.`seg_diet` SET `status` = 'inactive' WHERE `diet_code` = 'ReD'; 
 UPDATE `hisdb`.`seg_diet` SET `status` = 'inactive' WHERE `diet_code` = 'HD'; 
