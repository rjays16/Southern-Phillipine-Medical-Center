UPDATE `hisdb`.`seg_orientation_venue` SET `orientation_venue_name` = 'IHOMP CONFERENCE ROOM' WHERE `orientation_venue_id` = '1'; 
UPDATE `hisdb`.`seg_orientation_venue` SET `orientation_venue_name` = 'IHOMP OFFICE' WHERE `orientation_venue_id` = '2'; 
UPDATE `hisdb`.`seg_orientation_venue` SET `orientation_venue_name` = 'ANESTHESIA CONFERENCE ROOM' WHERE `orientation_venue_id` = '3'; 
UPDATE `hisdb`.`seg_orientation_venue` SET `orientation_venue_name` = 'CI ONCOLOGY CONFERENCE ROOM' WHERE `orientation_venue_id` = '4'; 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('DORIS CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('ER CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('ENT – HNS OFFICE'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('IM CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('IPBM CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('IWNH CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('KAMAGONG ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('LAWAAN ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('MAHOGANY ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('MIS SKILLS LABORATORY'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('NURSING OFFICE CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('OB-GYNE CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('ORTHOPEDICS CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('PEDIA CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('SURGERY CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('TIONKO HALL CONFERENCE ROOM'); 
INSERT INTO `hisdb`.`seg_orientation_venue` (`orientation_venue_name`) VALUES ('WARD');
INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('ihomp_orientation', NULL, 'IHOMP Staff', 'sql', '(SELECT cp.nr as id, fn_get_person_name_first_mi_last(cp.pid) as namedesc FROM care_personell cp WHERE cp.is_KM = 1) UNION (SELECT \'others\' as id, \'Others\' as namedesc)', '1', '0');

INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('module_orientation', NULL, 'Modules', 'option', '\'admision-ADMISSION\', \'er-ER\', \'opd-OPD\', \'phs-PHS\', \'ipbm-IPBM\', \'mr-MEDICAL RECORDS\', \'doc-DOCTORS\', \'nursing-NURSING\', \'or-OR\', \'laboratories-LABORATORIES\', \'Bbank-BLOOD BANK\', \'radiology-RADIOLOGY\', \'obgyne-OB GYNE\', \'pharmacy-PHARMACY\', \'dialysis-DIALYSIS\', \'sservice-SOCIAL SERVICE\', \'pdpu-PDPU\', \'hssc-HSSC\', \'billing-BILLING\', \'pad-PAD\', \'eclaims-ECLAIMS\', \'cashier-CASHIER\', \'reports-REPORTS\', \'sysadmin-SYSTEM ADMIN\', \'special-SPECIAL TOOLS\'', '1', '0')

INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) 
VALUES
  (
    'Hospital_System_Orientation',
    'ihomp_orientation'
  ) ;

INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('Hospital_System_Orientation', 'module_orientation'); 
UPDATE `hisdb`.`care_menu_main` SET `sort_nr` = '9' WHERE `nr` = '7';

ALTER TABLE `hisdb`.`seg_orientation_list`   
  CHANGE `title_orientation` `title_orientation` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL;
  
ALTER TABLE `hisdb`.`care_personell`   
  ADD COLUMN `is_KM` TINYINT(1) DEFAULT 0  NULL AFTER `remarks`;
  
UPDATE `hisdb`.`care_personell` SET `is_KM` = '1' WHERE `nr` = '100352';
UPDATE `hisdb`.`care_personell` SET `is_KM` = '1' WHERE `nr` = '104253';

INSERT INTO `hisdb`.`seg_signatory_document` (`document_code`, `document_name`) VALUES ('hosp_sys_orientation', 'Hospital System Orientation');
INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `signatory_title`, `document_code`) VALUES ('100669', 'IHOMP Head', 'IHOMP Head', 'hosp_sys_orientation');

ALTER TABLE `hisdb`.`seg_orientation_venue`   
  ADD COLUMN `is_default` TINYINT(1) DEFAULT 0  NULL AFTER `orientation_venue_name`;

UPDATE `hisdb`.`seg_orientation_venue` SET `is_default` = '1' WHERE `orientation_venue_id` = '1';

ALTER TABLE `hisdb`.`care_menu_main`   
  ADD COLUMN `orientation_sort` TINYINT(1) DEFAULT 0  NULL AFTER `status`;

UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '1' WHERE `nr` = '7';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '2' WHERE `nr` = '3';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '3' WHERE `nr` = '26';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '4' WHERE `nr` = '27';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '5' WHERE `nr` = '30';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '6' WHERE `nr` = '37';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '7' WHERE `nr` = '5';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '8' WHERE `nr` = '6';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '9' WHERE `nr` = '8';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '10' WHERE `nr` = '9';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '11' WHERE `nr` = '36';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '12' WHERE `nr` = '10';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '14' WHERE `nr` = '11';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '15' WHERE `nr` = '34';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '16' WHERE `nr` = '23';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '17' WHERE `nr` = '35';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '18' WHERE `nr` = '32';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '19' WHERE `nr` = '25';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '20' WHERE `nr` = '31';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '21' WHERE `nr` = '33';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '22' WHERE `nr` = '24';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '23' WHERE `nr` = '21';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '24' WHERE `nr` = '15';
UPDATE `hisdb`.`care_menu_main` SET `orientation_sort` = '25' WHERE `nr` = '18';