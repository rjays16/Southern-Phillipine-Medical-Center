#Note: If psy_patienttype is exist in seg_rep_params Table don't execute.
INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('psy_patienttype', NULL, 'IPBM Patient Type', 'option', '\'all-All\',\'opd-OPD\',\'ipd-IPD\'', '1', '0'); 
#End


INSERT INTO `seg_rep_templates_dept` (`id`,`report_id`, `dept_nr`, `template_name`) VALUES ('7','smoking', '182', 'PSY_History_Smoking'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('7', 'diagnosis', 'excluded'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('7', 'psy_patienttype', 'included'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('7', 'patienttype', 'excluded'); 
