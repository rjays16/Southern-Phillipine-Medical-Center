INSERT INTO `seg_rep_templates_dept` (`report_id`,`dept_nr`,`template_name`) VALUES('Admission_Logbook_For_Docs','182','admission_logbook_for_ipbm') ;
INSERT INTO `seg_rep_templates_dept` (`report_id`, `dept_nr`, `template_name`) VALUES ('report_discharges', '182', 'PSY_Patient_Discharges'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('5', 'dept', 'excluded'); 




#Note Run this query if param doesn't exist in seg_rep_params table
 INSERT INTO `seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('psy_date_based', NULL, 'Report Period based-date', 'option', '\'admission-Admission Date\',\'discharged-Discharged Date\'', '1', '1'); 
#end here

INSERT INTO `seg_rep_templates_dept` (`report_id`, `dept_nr`) VALUES ('causes_confinement', '182'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('3', 'date_based', 'excluded'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('3', 'psy_date_based', 'included'); 
