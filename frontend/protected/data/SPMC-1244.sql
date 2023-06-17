 /*[5:22:50 PM][4 ms]*/ DELETE FROM `hisdb`.`seg_rep_template_params` WHERE `report_id` = 'MR_Research_Query' AND `param_id` = 'base_date'; 

-- /*[5:23:26 PM][5 ms]*/ INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('MR_Research_Query', 'date_based'); 

-- /*[5:30:08 PM][4 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('21', 'PSY_date_based2', 'included'); 

INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('PSY_date_based2', NULL, 'Report Period based-date', 'option', '\'encounter-Encounter Date\',\'discharge-Discharged Date\'\r\n', '1', '1'); 

/*[9:20:34 PM][53 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `is_active`, `with_template`, `query_in_jasper`, `template_name`, `exclusive_opd_er`, `exclusive_death`, `w_graphical`) VALUES ('PSY_Research_Query', 'Hospital Operations', 'Research and Query', 'Research and Query', 'PSY_Research_Query', '182', NULL, '1', '1', '0', 'PSY_Research_Query', '0', '0', '0'); 

/*[9:22:11 PM][91 ms]*/ UPDATE `hisdb`.`seg_rep_templates_dept` SET `report_id` = 'PSY_Research_Query' , `template_name` = 'PSY_Research_Query' WHERE `id` = '21' AND `report_id` = 'MR_Research_Query' AND `dept_nr` = '182'; 
/*INSERT

/*[9:34:30 PM][3 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('21', 'icd10', 'included'); 
/*[9:34:42 PM][4 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('21', 'icpm', 'included'); 
/*[9:43:46 PM][2 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('21', 'codetype', 'included'); 
/*[9:31:56 PM][3 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`) VALUES ('21', 'type_nr'); 
/*[9:32:39 PM][3 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('21', 'psy_all_patienttype', 'included'); 
/*[9:32:48 PM][2 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('21', 'PSY_date_based2', 'included'); 


/*[3:26:32 PM][6 ms]*/ UPDATE `hisdb`.`seg_rep_templates_registry` SET `rep_dept_nr` = '182' WHERE `report_id` = 'psy_opd_daily_trans'; 
/*[3:31:05 PM][1330 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `is_active`, `with_template`, `query_in_jasper`, `template_name`, `exclusive_opd_er`, `exclusive_death`, `w_graphical`) VALUES ('PSY_Admission_Logbook_For_Docs', 'Hospital Operations', 'Admission Logbook For Doctors', 'Admission Logbook for Doctors', 'admission_logbook_for_docs', '182', 'HOSP', '1', '1', '0', 'admission_logbook_for_docs', '0', '0', '0'); 
/*[3:35:15 PM][2543 ms]*/ UPDATE `hisdb`.`seg_rep_templates_dept` SET `report_id` = 'PSY_Admission_Logbook_For_Docs' WHERE `id` = '1' AND `report_id` = 'Admission_Logbook_For_Docs' AND `dept_nr` = '182'; 
/**[3:54:18 PM][224 ms]*/ UPDATE `hisdb`.`seg_rep_templates_registry` SET `template_name` = 'PSY_OPD_daily_transaction' WHERE `report_id` = 'psy_opd_daily_trans'; 
/*[3:59:57 PM][6 ms]*/ UPDATE `hisdb`.`seg_rep_templates_registry` SET `report_id` = 'PSY_OPD_daily_transaction' WHERE `report_id` = 'psy_opd_daily_trans'; 
/*[4:02:04 PM][2 ms]*/ UPDATE `hisdb`.`seg_rep_templates_registry` SET `is_active` = '1' WHERE `report_id` = 'opd_daily_trans'; 
/*[4:43:43 PM][5 ms]*/ UPDATE `hisdb`.`seg_rep_templates_registry` SET `report_id` = 'PSY_OPD_daily_trans' WHERE `report_id` = 'PSY_OPD_daily_transaction'; 



/*[7:02:46 PM][5 ms]*/ UPDATE `hisdb`.`seg_rep_templates_dept` SET `dept_nr` = '150' WHERE `id` = '2' AND `report_id` = 'opd_daily_trans' AND `dept_nr` = '182'; 

