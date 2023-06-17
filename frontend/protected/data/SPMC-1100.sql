


update care_encounter SET admission_dt=null WHERE encounter_type='14' AND admission_dt IS NOT NULL





INSERT INTO `hisdb`.`seg_rep_templates_dept` (`id`, `report_id`, `dept_nr`, `template_name`) VALUES ('23', 'top_10', '182', 'PSY_Top_Leading_Diseases');





INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('23', 'brgynr', 'excluded'); 



INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('23', 'brgynr', 'included'); 



INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('23', 'munnr', 'excluded'); 



INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('23', 'munnr', 'included'); 



INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('23', 'provnr', 'excluded'); 



INSERT INTO `hisdb`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('23', 'provnr', 'included'); 

