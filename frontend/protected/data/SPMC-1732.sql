INSERT INTO `hisdb`.`seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `with_template`, `template_name`) VALUES ('bb_daily_report_deposited', 'Hospital Operations', 'Daily Record of Deposited Blood', 'Daily Record of Deposited Blood', 'BB_Daily_Report_Deposited', '190', 'HOSP', '1', 'BB_Daily_Report_Deposited');

INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('bb_blood_group', NULL, 'Blood Group', 'option', '\'ab-AB\',\'a-A\',\'b-B\',\'o-O\'', '1', '1');
INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('bb_component', NULL, 'Component', 'sql', '(SELECT id AS id, name AS namedesc FROM seg_blood_component)', '1', '3');
INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('bb_expiry_date', NULL, 'Expiry Date', 'date', NULL, '1', '2');
INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('bb_source', NULL, 'Source', 'sql', '(SELECT id AS id, name AS namedesc FROM seg_blood_source)', '1', '4')

INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('BB_Daily_Report_Deposited', 'bb_blood_group');
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('BB_Daily_Report_Deposited', 'bb_component');
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('BB_Daily_Report_Deposited', 'bb_encoder');
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('BB_Daily_Report_Deposited', 'bb_expiry_date');
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('BB_Daily_Report_Deposited', 'bb_source');