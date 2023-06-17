
INSERT INTO `hisdb`.`seg_rep_templates_registry` (`report_id`) VALUES ('PH_list_meds'); 
UPDATE `hisdb`.`seg_rep_templates_registry` SET `rep_group` = 'Hospital Operations' , `rep_name` = 'List of Medicines and Supplies Encoded' , `rep_description` = 'List of Medicines and Supplies Encoded' , `rep_script` = 'PH_list_meds' WHERE `report_id` = 'PH_list_meds'; 
UPDATE `hisdb`.`seg_rep_templates_registry` SET `rep_dept_nr` = '169' , `rep_category` = 'HOSP' , `with_template` = '1' , `template_name` = 'PH_list_meds' WHERE `report_id` = 'PH_list_meds'; 
UPDATE `hisdb`.`seg_rep_templates_registry` SET `rep_script` = 'list_meds' , `template_name` = 'Pharmacy_list_meds' WHERE `report_id` = 'PH_list_meds'; 
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`) VALUES ('PH_list_meds'); 
UPDATE `hisdb`.`seg_rep_template_params` SET `param_id` = 'pharma_dept' WHERE `report_id` = 'PH_list_meds' AND `param_id` IS NULL; 
UPDATE `hisdb`.`seg_rep_template_params` SET `param_id` = 'pharma_dept' WHERE `report_id` = 'PH_list_meds' AND `param_id` = ''; 
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`) VALUES ('PH_list_meds'); 
UPDATE `hisdb`.`seg_rep_template_params` SET `param_id` = 'phar_encoder' WHERE `report_id` = 'PH_list_meds' AND `param_id` IS NULL; 
UPDATE `hisdb`.`seg_rep_template_params` SET `param_id` = 'phar_encoder' WHERE `report_id` = 'PH_list_meds' AND `param_id` = ''; 
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`) VALUES ('PH_list_meds'); 
UPDATE `hisdb`.`seg_rep_template_params` SET `param_id` = 'time' WHERE `report_id` = 'PH_list_meds' AND `param_id` IS NULL; 
UPDATE `hisdb`.`seg_rep_template_params` SET `param_id` = 'time' WHERE `report_id` = 'PH_list_meds' AND `param_id` = ''; 
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`) VALUES ('PH_list_meds'); 
UPDATE `hisdb`.`seg_rep_template_params` SET `param_id` = 'patient_type' WHERE `report_id` = 'PH_list_meds' AND `param_id` IS NULL; 
UPDATE `hisdb`.`seg_rep_template_params` SET `param_id` = 'patient_type' WHERE `report_id` = 'PH_list_meds' AND `param_id` = ''; 
UPDATE `hisdb`.`seg_rep_template_params` SET `param_id` = 'dept_ward' WHERE `report_id` = 'PH_list_meds' AND `param_id` = 'pharma_dept'; 
INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `parameter`, `param_type`, `choices`) VALUES ('phar_charge_type', 'Patient Charge Type', 'sql', 'SELECT id,charge_name FROM seg_type_charge_pharma WHERE in_pharmacy = 1 ORDER BY ordering ASC'); 
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('PH_list_meds', 'phar_charge_type'); 
INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `parameter`, `param_type`, `choices`) VALUES ('phar_list_encoded', 'Type of List Encoded', 'option', '\'all-All\', \'med_encoded-List of Medicines Encoded\', \'sup_encoded-List of Supplies Encoded\''); 
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('PH_list_meds', 'phar_list_encoded'); 
UPDATE `hisdb`.`seg_rep_params` SET `choices` = 'SELECT \r\n  id AS id,\r\n  charge_name AS NAME\r\nFROM\r\n  seg_type_charge_pharma \r\nWHERE in_pharmacy = 1 \r\nORDER BY ordering ASC ' WHERE `param_id` = 'phar_charge_type'; 
UPDATE `hisdb`.`seg_rep_params` SET `choices` = 'SELECT \r\n  id AS id,\r\n  charge_name AS NAME\r\nFROM\r\n  seg_type_charge_pharma \r\nWHERE in_pharmacy = 1 \r\nORDER BY NAME ' WHERE `param_id` = 'phar_charge_type'; 
UPDATE `hisdb`.`seg_rep_params` SET `choices` = 'SELECT \r\n  id AS id,\r\n  charge_name AS NAME\r\nFROM\r\n  seg_type_charge_pharma \r\nWHERE in_pharmacy = 1 \r\nORDER BY ordering ASC ' WHERE `param_id` = 'phar_charge_type'; 

UPDATE `hisdb`.`seg_rep_params` SET `choices` = '(SELECT \r\n  \'all\' AS id,\r\n  \'All\' AS namedesc)\r\nUNION(\r\nSELECT \r\n  id AS id,\r\n  charge_name AS namedesc\r\nFROM\r\n  seg_type_charge_pharma \r\nWHERE in_pharmacy = 1\r\nORDER BY ordering ASC);\r\n' WHERE `param_id` = 'phar_charge_type'; 
UPDATE `hisdb`.`seg_rep_params` SET `choices` = '(SELECT \r\n  \'all\' AS id,\r\n  \'All\' AS namedesc)\r\nUNION(\r\nSELECT \r\n  id AS id,\r\n  description AS namedesc\r\nFROM\r\n  seg_type_charge_pharma \r\nWHERE in_pharmacy = 1\r\nORDER BY ordering ASC);\r\n' WHERE `param_id` = 'phar_charge_type';

UPDATE `hisdb`.`seg_rep_templates_registry` SET `rep_script` = 'PH_list_meds' WHERE `report_id` = 'PH_list_meds'; 
DELETE FROM `hisdb`.`seg_rep_template_params` WHERE `report_id` = 'PH_list_meds' AND `param_id` = ''; 
DELETE FROM `hisdb`.`seg_rep_template_params` WHERE `report_id` = 'PH_list_meds' AND `param_id` = 'pharma_dept';