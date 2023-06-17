
/*[3:35:48 PM][3 ms]*/ INSERT INTO `hisdb4dev`.`seg_rep_params` (`param_id`, `parameter`, `param_type`, `choices`) VALUES ('psy_area', 'Area', 'option', '\'ipd-IPBM IPD\''); 

/**  NOTE seg_rep_templates_dept_params INSERTED ID BASE ON THE INCREMENTAL ID GENERATED FROM seg_rep_templates_dept
 /*[6:23:07 PM][4 ms]*/ INSERT INTO `hisdb4dev`.`seg_rep_templates_dept` (`report_id`, `dept_nr`, `template_name`) VALUES ('discharge_treatment', '182', 'PSY_Discharges_Result_Treatment');
 /*[6:24:02 PM][6 ms]*/ INSERT INTO `hisdb4dev`.`seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('40', 'psy_area', 'included');  
 //*========================