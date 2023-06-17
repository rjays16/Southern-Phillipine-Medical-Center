/*[3:31:10 PM][9851 ms]*/ CREATE TABLE `hisdb`.`seg_ipbm_patient_type_classification`( `id` TINYINT UNSIGNED NOT NULL, `classification_name` VARCHAR(10) NOT NULL, PRIMARY KEY (`id`) ); 

/*[4:08:20 PM][3 ms]*/ INSERT INTO `hisdb`.`seg_ipbm_patient_type_classification` (`id`, `classification_name`) VALUES ('1', 'CIU'); 

/*[4:08:45 PM][6 ms]*/ INSERT INTO `hisdb`.`seg_ipbm_patient_type_classification` (`id`, `classification_name`) VALUES ('2', 'ACUTE'); 

/*[4:09:02 PM][13 ms]*/ INSERT INTO `hisdb`.`seg_ipbm_patient_type_classification` (`id`, `classification_name`) VALUES ('3', 'CHRONIC');

/*[4:09:21 PM][9 ms]*/ INSERT INTO `hisdb`.`seg_ipbm_patient_type_classification` (`id`, `classification_name`) VALUES ('4', 'CUSTODIAL');

/*[7:13:10 PM][81 ms]*/ CREATE TABLE `hisdb`.`seg_ipbm_patient_classification`( `id` INT(11) NOT NULL AUTO_INCREMENT, `classification_type` TINYINT(2) UNSIGNED NOT NULL, `encounter_nr` VARCHAR(12), `history` TEXT, `create_id` VARCHAR(35), `create_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, `modify_id` VARCHAR(35), `modify_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, `is_deleted` TINYINT(1) DEFAULT 0, PRIMARY KEY (`id`) ); 

/*[8:55:22 PM][147 ms]*/ ALTER TABLE `hisdb`.`seg_ipbm_patient_type_classification` ADD COLUMN `legend` VARCHAR(20) NULL AFTER `classification_name`; 

/*[8:58:25 PM][7 ms]*/ UPDATE `hisdb`.`seg_ipbm_patient_type_classification` SET `legend` = 'white' WHERE `id` = '1'; 
/*[8:58:30 PM][3 ms]*/ UPDATE `hisdb`.`seg_ipbm_patient_type_classification` SET `legend` = 'pink' WHERE `id` = '2'; 
/*[8:58:34 PM][790 ms]*/ UPDATE `hisdb`.`seg_ipbm_patient_type_classification` SET `legend` = '#FF4500' WHERE `id` = '3'; 
/*[8:58:36 PM][4 ms]*/ UPDATE `hisdb`.`seg_ipbm_patient_type_classification` SET `legend` = 'yellow' WHERE `id` = '4';

/*[11:19:59 AM][944 ms]*/ ALTER TABLE `hisdb`.`care_ward` ADD COLUMN `mode_of_discharge` TINYINT(1) DEFAULT 0 NULL AFTER `is_category`; 
/*[11:31:52 AM][153 ms]*/ UPDATE `hisdb`.`care_ward` SET `mod` = '1' WHERE `nr` = '212'; 
/*[11:31:53 AM][180 ms]*/ UPDATE `hisdb`.`care_ward` SET `mod` = '1' WHERE `nr` = '213'; 
/*[11:31:55 AM][163 ms]*/ UPDATE `hisdb`.`care_ward` SET `mod` = '1' WHERE `nr` = '215'; 
/*[11:32:49 AM][150 ms]*/ UPDATE `hisdb`.`care_ward` SET `mod` = '1' WHERE `nr` = '423'; 
