/*[2:58:35 PM][49 ms]*/ INSERT INTO `hisdb`.`seg_report_dept_bed_allocation` (`id`, `dept_nr`, `dept_name`, `pay_allocated_bed`, `service_allocated_bed`, `ordering`) VALUES ('IPBMCIU', '182', 'IPBM CIU', '0', '35', '13'); 
/*[2:59:43 PM][138 ms]*/ INSERT INTO `hisdb`.`seg_report_dept_bed_allocation` (`id`, `dept_nr`, `dept_name`, `pay_allocated_bed`, `service_allocated_bed`, `ordering`) VALUES ('IPBMMW', '182', 'IPBM Male Ward', '0', '100', '14'); 
/*[3:00:17 PM][204 ms]*/ INSERT INTO `hisdb`.`seg_report_dept_bed_allocation` (`id`, `dept_nr`, `dept_name`, `pay_allocated_bed`, `service_allocated_bed`, `ordering`) VALUES ('IPBMFW', '182', 'IPBM FEMALE WARD', '0', '65', '15'); 


/*[4:42:15 PM][32 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `with_template`, `template_name`) VALUES ('PSY_bor', 'Hospital Operations', 'Distribution of Beds and Bed Occupancy Rate', 'Distribution of Beds and Bed Occupancy Rate', 'Distribution_Beds', '182', 'HOSP', '1', 'Distribution_Beds');
/*[4:42:59 PM][4 ms]*/ INSERT INTO `hisdb`.`seg_rep_templates_dept` (`report_id`, `dept_nr`, `template_name`) VALUES ('PSY_bor', '182', 'Distribution_Beds');
