ALTER TABLE `hisdb`.`seg_pharma_areas` ADD COLUMN `exclude_if_phic` TINYINT(1) DEFAULT 0 NULL AFTER `is_deleted`; 
UPDATE `hisdb`.`seg_pharma_areas` SET `exclude_if_phic` = '1' WHERE `area_code` = 'H'; 
UPDATE `hisdb`.`seg_pharma_areas` SET `exclude_if_phic` = '1' WHERE `area_code` = 'O1'; 
UPDATE `hisdb`.`seg_pharma_areas` SET `exclude_if_phic` = '1' WHERE `area_code` = 'O2'; 
UPDATE `hisdb`.`seg_pharma_areas` SET `exclude_if_phic` = '1' WHERE `area_code` = 'MHC'; 
UPDATE `hisdb`.`seg_pharma_areas` SET `exclude_if_phic` = '1' WHERE `area_code` = 'OR'; 
UPDATE `hisdb`.`seg_pharma_areas` SET `exclude_if_phic` = '1' WHERE `area_code` = 'IP4';