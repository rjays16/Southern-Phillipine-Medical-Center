/*[3:23:22 AM][307 ms]*/ ALTER TABLE `hisdb`.`seg_lab_service_groups` ADD COLUMN `category` VARCHAR(10) NULL AFTER `iso_nr`; 
/*[3:25:08 AM][56 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'BB' WHERE `group_code` = 'B'; 
/*[3:25:08 AM][56 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'BB' WHERE `group_code` = 'B'; 
/*[3:25:30 AM][53 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'SPL' WHERE `group_code` = 'SPL'; 
/*[3:25:37 AM][56 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'SPL' WHERE `group_code` = 'CATH'; 
/*[3:25:43 AM][52 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'SPL' WHERE `group_code` = 'ECHO'; 
/*[3:25:51 AM][52 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'SPL' WHERE `group_code` = 'POC'; 
/*[3:27:36 AM][54 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'SPL' WHERE `group_code` = 'SPC';
/*[3:28:29 AM][54 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'LB' WHERE `group_code` = 'C'; 
/*[3:28:32 AM][90 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'LB' WHERE `group_code` = 'H'; 
/*[3:28:34 AM][51 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'LB' WHERE `group_code` = 'HIV'; 
/*[3:28:37 AM][53 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'LB' WHERE `group_code` = 'HP'; 
/*[3:28:39 AM][54 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'LB' WHERE `group_code` = 'I'; 
/*[3:28:42 AM][51 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'LB' WHERE `group_code` = 'MB'; 
/*[3:28:44 AM][54 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'LB' WHERE `group_code` = 'ML'; 
/*[3:28:46 AM][55 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'LB' WHERE `group_code` = 'PCR'; 
/*[3:28:49 AM][53 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'LB' WHERE `group_code` = 'U';  
/*[4:48:16 AM][75 ms]*/ UPDATE `hisdb`.`seg_lab_service_groups` SET `category` = 'LB' WHERE `group_code` = 'DT'; 