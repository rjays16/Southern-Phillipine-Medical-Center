create table `seg_pharma_iso_footer` (
	`area_code` varchar (30),
	`iso_footer` varchar (150)
); 
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('2E','SPMC-F-PHA-03A');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('AMB','SPMC-F-PHA-03A');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('BB','SPMC-F-PHA-03A');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('C/MO','SPMC-F-PHA-03A');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('ER','SPMC-F-PHA-03A');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('H','SPMC-F-PHA-03E');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('I','SPMC-F-PHA-03A');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('IP','SPMC-F-PHA-03A');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('IP1','SPMC-F-PHA-03D');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('M','SPMC-F-PHA-03B');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('MG','SPMC-F-PHA-03B');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('MHC','SPMC-F-PHA-03E');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('O','SPMC-F-PHA-03C');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('OR','SPMC-F-PHA-03C');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('OPP','SPMC-F-PHA-03C');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('O1','SPMC-F-PHA-03C');
insert into `seg_pharma_iso_footer` (`area_code`, `iso_footer`) values('WD','SPMC-F-PHA-03A');

insert into `seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `is_active`, `with_template`, `query_in_jasper`, `template_name`, `exclusive_opd_er`, `exclusive_death`, `w_graphical`) values ('Pharma_issuance_report', 'Hospital Operations', 'Pharmacy Daily Issuance Report', 'Pharmacy Daily Issuance Report', 'Pharma_issuance_report', '169', 'HOSP', '1', '1', '0', 'Pharma_issuance_report', '0', '0', '0');

insert into `seg_rep_template_params` (`report_id`, `param_id`) values ('Pharma_issuance_report', 'pharma_dept');

insert into `seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) values ('pharma_dept', NULL, 'Pharmacy Areas', 'sql', 'SELECT area_code AS id,area_name AS namedesc FROM seg_pharma_areas WHERE NOT(is_deleted)', '1', '0');

ALTER TABLE `seg_pharma_areas` CHANGE `is_deleted` `is_deleted` TINYINT(1) DEFAULT 0 NOT NULL;