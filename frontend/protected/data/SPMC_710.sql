-- SPMC - 710 DB Changes --
INSERT INTO `hisdb`.`seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `with_template`, `template_name`) VALUES ('EHR_User_Log_Monitoring', 'Hospital Operations', 'EHR User Log Monitoring', 'Login monitoring in EHR', 'EHR_User_Log_Monitoring', '0', '1', 'EHR_User_Log_Monitoring');

insert into `hisdb`.`seg_rep_templates_clinic` (`report_id`, `dept_nr`) values ('EHR_User_Log_Monitoring', '0')

insert into `hisdb`.`seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `with_template`, `template_name`) values ('ER_Daily_Transactions_for_docs', 'Hospital Operations', 'Emergency Daily Transactions', 'Emergency Daily Transactions', 'ER_Daily_Transactions', '0', 'HOSP', '1', 'ER_Daily_Transactions')

update `hisdb`.`seg_rep_templates_clinic` set `report_id` = 'ER_Daily_Transactions_for_docs' where `report_id` = 'ER_Daily_Transactions' and `dept_nr` = '0'