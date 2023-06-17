ALTER TABLE `hisdb`.`seg_opdarea` ADD COLUMN `opd_code` VARCHAR(100) NOT NULL AFTER `accomodation_type`; 

UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'ASU - Pay' WHERE `id` = '2'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'HI - Pay' WHERE `id` = '3'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'OPD - Pay' WHERE `id` = '4';
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'ASU - Service' WHERE `id` = '5'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'HI - Service' WHERE `id` = '6'; 

UPDATE `hisdb`.`seg_opdarea` SET `opd_code` = '1' WHERE `id` = '1'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_code` = '4' WHERE `id` = '2'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_code` = '6' WHERE `id` = '3'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_code` = '2' WHERE `id` = '4'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_code` = '3' WHERE `id` = '5'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_code` = '5' WHERE `id` = '6'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_code` = '7' WHERE `id` = '7'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_code` = '8' WHERE `id` = '8'; 