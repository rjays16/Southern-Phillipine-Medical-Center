INSERT INTO `hisdb`.`seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `template_name`) VALUES ('ER_Disposition_Report', 'Hospital Operations', 'Emergency Department Disposition Time Monitoring', 'Emergency Department Disposition Time Monitoring', 'ER_Disposition', '149', 'HOSP', 'ER_Disposition'); 
UPDATE `hisdb`.`seg_rep_templates_registry` SET `with_template` = '1' WHERE `report_id` = 'ER_Disposition_Report'; 
INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `parameter`, `param_type`, `choices`) VALUES ('type_triage', 'Triage Category', 'sql', '(SELECT \r\n  stc.roman_id AS id,\r\n  stc.category  as namedesc\r\nFROM\r\n  seg_triage_category AS stc \r\nORDER BY stc.category_id)'); 
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('ER_Disposition_Report', 'type_triage');
INSERT INTO `hisdb`.`seg_rep_params` (`param_id`, `parameter`, `param_type`, `choices`) VALUES ('er_dept', 'All Department', 'sql', '(SELECT \r\n  nr AS id,\r\n  name_formal AS namedesc\r\nFROM\r\n  care_department \r\nWHERE TYPE = 1 \r\n  AND is_inactive = \'0\' \r\n  AND admit_inpatient = \'1\' \r\n  AND STATUS NOT IN (\r\n    \'deleted\',\r\n    \'hidden\',\r\n    \'inactive\',\r\n    \'void\'\r\n  ) \r\nORDER BY name_formal)'); 
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('ER_Disposition_Report', 'er_dept'); 