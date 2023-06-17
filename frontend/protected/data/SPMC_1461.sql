UPDATE `hisdb`.`seg_rep_templates_dept` SET `template_name` = 'PSY_OPD_Summary' WHERE `id` = '4' AND `report_id` = 'opd_summary' AND `dept_nr` = '182'; 
 INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `parameter`, `param_type`, `choices`) VALUES ('type_personnel', 'Type of Summary', 'option', '\'1-Age Distribution of Patients\',\'2-Stages\''); 
 INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('4', 'type_personnel', 'included'); 
