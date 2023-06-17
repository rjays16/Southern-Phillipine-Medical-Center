INSERT INTO `seg_rep_templates_dept` (`id`, `report_id`, `dept_nr`, `template_name`) VALUES ('31', 'leading_morbidity_oveall', '182', 'PSY_Leading_Causes_Morbidity_Overall'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('31', 'dept', 'excluded'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('31', 'patienttype', 'excluded');
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('31', 'psy_all_patienttype', 'included'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('31', 'date_based', 'included'); 
ALTER TABLE `seg_encounter_profile` ADD COLUMN `date_birth` DATE DEFAULT '0000-00-00' NULL AFTER `civil_status`;
ALTER TABLE `seg_encounter_profile` ADD COLUMN `is_discharged` TINYINT(1) DEFAULT 0 NULL AFTER `date_birth`; 