CREATE TABLE `hisdb`.`seg_med_abstract`( `encounter_nr` VARCHAR(12) NOT NULL, `brief_hist` TEXT, `diagnosis` TEXT, `remarks` TEXT, `dr_nr` INT(11), `history` TEXT, `modify_id` VARCHAR(50), `modify_dt` DATETIME, `create_id` VARCHAR(50), `create_dt` DATETIME, PRIMARY KEY (`encounter_nr`) ); 

ALTER TABLE `hisdb`.`seg_med_abstract` ADD COLUMN `mental_status` TEXT NULL AFTER `brief_hist`; 

ALTER TABLE `hisdb`.`seg_med_abstract` ADD COLUMN `abst_nr` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT AFTER `create_dt`, ADD KEY(`abst_nr`), DROP PRIMARY KEY, ADD PRIMARY KEY (`encounter_nr`, `abst_nr`); 
/*[2:37:34 PM][101 ms]*/ ALTER TABLE `hisdb`.`seg_med_abstract` ADD COLUMN `patient_status` VARCHAR(50) NULL AFTER `abst_nr`, ADD COLUMN `age` DOUBLE NULL AFTER `patient_status`; 
ALTER TABLE `hisdb`.`seg_med_abstract` CHANGE `patient_status` `civil_status` VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci NULL; 