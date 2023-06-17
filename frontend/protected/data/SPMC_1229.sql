INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('psy_encoder', NULL, 'Encoder', 'sql', 'SELECT a.personell_nr AS id, fn_get_personellname_lastfirstmi(a.personell_nr) AS namedesc FROM care_personell_assignment AS a, care_personell AS ps, care_person AS p WHERE (ps.short_id LIKE \'G%\') AND a.location_nr=182 AND (a.date_end=\'0000-00-00\' OR a.date_end>=DATE(NOW())) AND a.STATUS NOT IN (\'deleted\',\'hidden\',\'inactive\',\'void\') AND a.personell_nr=ps.nr AND ps.pid=p.pid ORDER BY p.name_last, p.name_first, p.name_middle', '1', '0'); 
INSERT INTO `hisdb`.`seg_rep_templates_dept` (`id`, `report_id`, `dept_nr`, `template_name`) VALUES ('8', 'report_icd_encoded', '182', 'PSY_icd_encoded'); 
INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('psy_all_patienttype', NULL, 'IPBM Patient Type', 'option', '\'all-All\',\'opd-OPD\',\'ipd-IPD\'', '1', '0'); 
INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('8', 'psy_encoder', 'included'); 
INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('8', 'encoder', 'excluded'); 
INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('8', 'patienttype', 'excluded'); 
INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('8', 'psy_all_patienttype', 'included'); 



