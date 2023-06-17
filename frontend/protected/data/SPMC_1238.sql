INSERT INTO `seg_rep_templates_dept` (`id`,`report_id`, `dept_nr`, `template_name`) VALUES ('15','leading_discharges', '182', 'PSY_Leading_Causes_Discharges'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('15', 'dept', 'excluded'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('15', 'patienttype', 'excluded');
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('15', 'psy_patienttype', 'included'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('15', 'date_based', 'included'); 
