UPDATE `hisdb`.`seg_rep_templates_registry` SET `is_active` = '0' WHERE `report_id` = 'opddental_procedure'; 



-- UPDATE `hisdb`.`seg_rep_params` SET `choices` = '\'134-Dental\',\'116-Dermatology\',\'136-ENT(HNS)\',\'133-Family Medicine\',\'236-Family Medicine(Animal Bite)\',\'213-Family Medicine(Anxiety)\',\'212-Family Medicine(Palliative)\',\'234-Family Medicine(PHS)\',\'221-Family Medicine(Smoking Cessation)\',\'222-Family Medicine(TBDC/NTP)\',\'124-Gynecology\',\'123-Gynecology(Oncology)\',\'154-Internal Medicine\',\'182-IPBM\',\'219-Medicine(Anxiety)\',\'216-Medicine(Gastro Clinic)\',\'217-Medicine(Oncology Clinic)\',\'215-Medicine(Pulmo Clinic)\',\'218-Medicine(Rheuma Clinic)\',\'231-Medicine(Special Clinic)\',\'105-Medicine(Cardiology)\',\'135-Medicine(Diabetic Clinic)\',\'108-Medicine(Endocrine)\',\'109-Medicine(Gastroenterology)\',\'113-Medicine(Hematology)\',\'110-Medicine(Nephrology)\',\'107-Medicine(Neurology)\',\'112-Medicine(Oncology)\',\'111-Medicine(Pulmonology)\',\'106-Medicine(Rheumatology)\',\'144-Mindanao Dialysis Center\',\'139-Obstetrics\',\'155-Obstetrics(Gynecology)\',\'131-Ophthalmology\',\'141-Orthopedics\',\'142-Orthopedics(PT)\',\'125-Pediatrics\',\'127-Pediatrics(Cardiology)\',\'206-Pediatrics(Endocrine)\',\'129-Pediatrics(Gastroenterology)\',\'137-Pediatrics(Hematology)\',\'143-Pediatrics(Nephrology)\',\'128-Pediatrics(Neurology)\',\'126-Pediatrics(Oncology)\',\'132-Pediatrics(Pulmonary)\',\'157-Psychiatry\',\'117-Surgery\',\'248-Surgery(Oncology)\',\'121-Surgery(Neurology)\',\'122-Urology\'' WHERE `param_id` = 'OPDdeptt'; 

UPDATE `hisdb`.`seg_rep_params` SET `choices` = '\'134-Dental\',\'116-Dermatology\',\'136-ENT(HNS)\',\'133-Family Medicine\',\'236-Family Medicine(Animal Bite)\',\'213-Family Medicine(Anxiety)\',\'212-Family Medicine(Palliative)\',\'234-Family Medicine(PHS)\',\'221-Family Medicine(Smoking Cessation)\',\'222-Family Medicine(TBDC/NTP)\',\'124-Gynecology\',\'123-Gynecology(Oncology)\',\'154-Internal Medicine\',\'182-IPBM\',\'219-Medicine(Anxiety)\',\'216-Medicine(Gastro Clinic)\',\'217-Medicine(Oncology Clinic)\',\'215-Medicine(Pulmo Clinic)\',\'218-Medicine(Rheuma Clinic)\',\'231-Medicine(Special Clinic)\',\'105-Medicine(Cardiology)\',\'135-Medicine(Diabetic Clinic)\',\'108-Medicine(Endocrine)\',\'109-Medicine(Gastroenterology)\',\'113-Medicine(Hematology)\',\'110-Medicine(Nephrology)\',\'107-Medicine(Neurology)\',\'112-Medicine(Oncology)\',\'111-Medicine(Pulmonology)\',\'106-Medicine(Rheumatology)\',\'144-Mindanao Dialysis Center\',\'139-Obstetrics\',\'155-Obstetrics(Gynecology)\',\'131-Ophthalmology\',\'141-Orthopedics\',\'142-Orthopedics(PT)\',\'125-Pediatrics\',\'127-Pediatrics(Cardiology)\',\'206-Pediatrics(Endocrine)\',\'129-Pediatrics(Gastroenterology)\',\'137-Pediatrics(Hematology)\',\'143-Pediatrics(Nephrology)\',\'128-Pediatrics(Neurology)\',\'126-Pediatrics(Oncology)\',\'132-Pediatrics(Pulmonary)\',\'157-Psychiatry\',\'117-Surgery\',\'248-Surgery(Oncology)\',\'121-Surgery(Neurology)\',\'122-Surgery(Urology)\'' WHERE `param_id` = 'OPDdeptt'; 
CREATE TABLE `hisdb`.`seg_dept_parent`( `id` INT(11) NOT NULL, `name` VARCHAR(50), PRIMARY KEY (`id`) ); 
CREATE TABLE `hisdb`.`seg_dept_child`( `id` INT(11) NOT NULL, `parent_id` INT(11), `name` VARCHAR(50), PRIMARY KEY (`id`) ); 

INSERT INTO `hisdb`.`seg_dept_parent` (`id`, `name`) VALUES ('117', 'Surgery'); 
INSERT INTO `hisdb`.`seg_dept_parent` (`id`, `name`) VALUES ('124', 'Gynecology'); 
INSERT INTO `hisdb`.`seg_dept_parent` (`id`) VALUES ('125'); 
UPDATE `hisdb`.`seg_dept_parent` SET `name` = 'Pediatrics' WHERE `id` = '125'; 
INSERT INTO `hisdb`.`seg_dept_parent` (`id`, `name`) VALUES ('139', 'Obstetrics'); 
INSERT INTO `hisdb`.`seg_dept_parent` (`id`, `name`) VALUES ('141', 'Orthopedics'); 
INSERT INTO `hisdb`.`seg_dept_parent` (`id`, `name`) VALUES ('133', 'Family Medicine'); 



INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('121', '117', 'Surgery(Neurology)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`) VALUES ('122'); 
UPDATE `hisdb`.`seg_dept_child` SET `parent_id` = '117' , `name` = 'Surgery(Urology)' WHERE `id` = '122'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `name`) VALUES ('251', 'SurgOncology'); 
UPDATE `hisdb`.`seg_dept_child` SET `parent_id` = '117' , `name` = 'Surgery(Oncology)' WHERE `id` = '251'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('123', '124', 'Gynecology(Oncology)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('126', '125', 'Pediatrics(Oncology)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`) VALUES ('127'); 
UPDATE `hisdb`.`seg_dept_child` SET `parent_id` = '125' WHERE `id` = '127'; 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Pediatrics(Cardiology)' WHERE `id` = '127'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`) VALUES ('128', '125'); 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Pediatrics(Neurology)' WHERE `id` = '128'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`) VALUES ('129'); 
UPDATE `hisdb`.`seg_dept_child` SET `parent_id` = '125' WHERE `id` = '129'; 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Pediatrics(Gastroenterology)' WHERE `id` = '129'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`) VALUES ('132', '125'); 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Pediatrics(Pulmonary)' WHERE `id` = '132'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`) VALUES ('137', '125'); 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Pediatrics(Hematology)' WHERE `id` = '137'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`) VALUES ('143', '125'); 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Pediatrics(Nephrology)' WHERE `id` = '143'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`) VALUES ('206', '125'); 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Pediatrics(Endocrine)' WHERE `id` = '206'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('155', '139', 'Obstetrics(Gynecology)'); 
INSERT INTO `hisdb`.`seg_dept_child` () VALUES (); 
UPDATE `hisdb`.`seg_dept_child` SET `id` = '142' , `parent_id` = '141' , `name` = 'Orthopedics(PT)' WHERE `id` IS NULL; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('212', '133', 'Family Medicine(Palliative)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`parent_id`) VALUES ('133'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`) VALUES ('213', '133'); 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Family Medicine(Anxiety)' WHERE `id` = '213'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`) VALUES ('221'); 
UPDATE `hisdb`.`seg_dept_child` SET `parent_id` = '133' WHERE `id` = '221'; 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Family Medicine(Smoking Cessation)' WHERE `id` = '221'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`) VALUES ('222'); 
UPDATE `hisdb`.`seg_dept_child` SET `parent_id` = '133' WHERE `id` = '222'; 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Family Medicine(TBDC/NTP)' WHERE `id` = '222'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`) VALUES ('234', '133'); 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Family Medicine(PHS)' WHERE `id` = '234'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`) VALUES ('236', '133'); 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Family Medicine(Animal Bite)' WHERE `id` = '236'; 
UPDATE `hisdb`.`seg_dept_child` SET `id` = '248' WHERE `id` = '251'; 

INSERT INTO `hisdb`.`seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `is_active`, `with_template`, `query_in_jasper`, `template_name`, `exclusive_opd_er`, `exclusive_death`, `w_graphical`) VALUES ('psy_opd_daily_trans', 'Hospital Operations', 'OPD Daily Transaction', 'Outpatient Preventive Care Center Daily Transactions', 'OPD_daily_transaction', '150', 'HOSP', '1', '1', '0', 'MR_OPD_daily_transaction', '0', '0', '0'); 
INSERT INTO `hisdb`.`seg_rep_templates_dept` (`id`, `report_id`, `dept_nr`, `template_name`) VALUES (NULL, 'psy_opd_daily_trans', '182', 'PSY_OPD_daily_transaction'); 
UPDATE `hisdb`.`seg_rep_templates_registry` SET `is_active` = '0' WHERE `report_id` = 'opd_daily_trans'; 



-- 10-30-2018




DELETE FROM `hisdb`.`seg_dept_parent` WHERE `id` = '124'; 
/*[2:44:41 PM][4 ms]*/ INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('124', '155', 'Gynecology'); 
UPDATE `hisdb`.`seg_dept_child` SET `parent_id` = '139' WHERE `id` = '123'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('219', '133', 'Medicine(Anxiety)'); 
INSERT INTO `hisdb`.`seg_dept_parent` (`id`, `name`) VALUES ('154', 'Internal Medicine'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('216', '154', 'Medicine(Gastro Clinic)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('217', '154', 'Medicine(Oncology Clinic)'); 
UPDATE `hisdb`.`seg_dept_child` SET `id` = '215' , `parent_id` = '154' , `name` = 'Medicine(Pulmo Clinic)' WHERE `id` = '0'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('218', '154', 'Medicine(Rheuma Clinic)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('231', '154', 'Medicine(Special Clinic)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`) VALUES ('105', '154'); 
/*[3:02:14 PM][5 ms]*/ UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Medicine(Cardiology)' WHERE `id` = '105'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`) VALUES ('105'); 
/*[3:03:03 PM][51 ms]*/ INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('106', '154', 'Medicine(Rheumatology)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`) VALUES ('107'); 
/*[3:03:53 PM][10 ms]*/ UPDATE `hisdb`.`seg_dept_child` SET `parent_id` = '154' , `name` = 'Medicine(Neurology)' WHERE `id` = '107'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('108', '154', 'Medicine-Endocrine'); 
/*[3:04:49 PM][5 ms]*/ UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Medicine(Endocrine)' WHERE `id` = '108'; 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('109', '154', 'Medicine(Gastroenterology)'); 
/*[3:06:10 PM][3 ms]*/ INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('110', '154', 'Medicine(Nephrology)'); 
/*[3:06:35 PM][86 ms]*/ INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('111', '154', 'Medicine-Pulmonology'); 
/*[3:06:53 PM][3 ms]*/ INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('112', '154', 'Medicine(Oncology)'); 
/*[3:07:04 PM][5 ms]*/ INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('113', '154', 'Medicine(Hematology)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('135', '154', 'Medicine(Diabetic Clinic)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('139', '155', 'Obstetrics'); 
INSERT INTO `hisdb`.`seg_dept_parent` (`id`, `name`) VALUES ('155', 'Obstetrics(Gynecology)'); 
INSERT INTO `hisdb`.`seg_dept_child` (`id`, `parent_id`, `name`) VALUES ('142', '141', 'Orthopedics(PT)'); 
UPDATE `hisdb`.`seg_rep_params` SET `choices` = '\'134-Dental\',\'116-Dermatology\',\'136-ENT(HNS)\',\'133-Family Medicine\',\'236-Family Medicine(Animal Bite)\',\'213-Family Medicine(Anxiety)\',\'212-Family Medicine(Palliative)\',\'234-Family Medicine(PHS)\',\'221-Family Medicine(Smoking Cessation)\',\'222-Family Medicine(TBDC/NTP)\',\'124-Gynecology\',\'123-Gynecology(Oncology)\',\'154-Internal Medicine\',\'182-IPBM\',\'219-Medicine(Anxiety)\',\'231-Medicine(Special Clinic)\',\'105-Medicine(Cardiology)\',\'135-Medicine(Diabetic Clinic)\',\'108-Medicine(Endocrine)\',\'109-Medicine(Gastroenterology)\',\'113-Medicine(Hematology)\',\'110-Medicine(Nephrology)\',\'107-Medicine(Neurology)\',\'112-Medicine(Oncology)\',\'111-Medicine(Pulmonology)\',\'106-Medicine(Rheumatology)\',\'144-Mindanao Dialysis Center\',\'139-Obstetrics\',\'155-Obstetrics(Gynecology)\',\'131-Ophthalmology\',\'141-Orthopedics\',\'142-Orthopedics(PT)\',\'125-Pediatrics\',\'127-Pediatrics(Cardiology)\',\'206-Pediatrics(Endocrine)\',\'129-Pediatrics(Gastroenterology)\',\'137-Pediatrics(Hematology)\',\'143-Pediatrics(Nephrology)\',\'128-Pediatrics(Neurology)\',\'126-Pediatrics(Oncology)\',\'132-Pediatrics(Pulmonary)\',\'157-Psychiatry\',\'117-Surgery\',\'248-Surgery(Oncology)\',\'121-Surgery(Neurology)\',\'122-Surgery(Urology)\'' WHERE `param_id` = 'OPDdeptt'; 
DELETE FROM `hisdb`.`seg_dept_child` WHERE `id` = '215'; 
/*[6:11:54 PM][12 ms]*/ DELETE FROM `hisdb`.`seg_dept_child` WHERE `id` = '216'; 
/*[6:11:54 PM][9 ms]*/ DELETE FROM `hisdb`.`seg_dept_child` WHERE `id` = '217'; 
/*[6:11:54 PM][4 ms]*/ DELETE FROM `hisdb`.`seg_dept_child` WHERE `id` = '218'; 
UPDATE `hisdb`.`seg_dept_child` SET `name` = 'Medicine(Pulmonology)' WHERE `id` = '111'; 