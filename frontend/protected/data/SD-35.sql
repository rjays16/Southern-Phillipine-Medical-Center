/*[4:22:02 PM][3 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `with_template`, `template_name`) VALUES ('PSY_leading_mortality', 'Hospital Operations', 'Leading Causes of Mortality', 'Leading Causes of Mortality - Cause of Death', 'Leading_Causes_Mortality', '182', 'CHO', '1', 'leading_mortality'); 
/*[4:22:59 PM][3 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept` (`report_id`, `dept_nr`, `template_name`) VALUES ('PSY_leading_mortality', '182', 'Leading_Causes_Mortality'); 
/*[5:25:01 PM][2 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('74', 'type_nr', 'included'); 
/*[5:29:11 PM][8 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('74', 'psy_date_based', 'included'); 