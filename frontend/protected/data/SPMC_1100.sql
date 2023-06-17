CREATE TABLE `seg_rep_templates_dept`( `id` INT(255) NOT NULL, `report_id` VARCHAR(100) NOT NULL, `dept_nr` MEDIUMINT(8) NOT NULL, `template_name` VARCHAR(100), PRIMARY KEY (`id`, `report_id`, `dept_nr`) ); 
ALTER TABLE `seg_rep_templates_dept` CHANGE `id` `id` INT(255) NOT NULL AUTO_INCREMENT;
CREATE TABLE `seg_rep_templates_dept_params`( `id` INT(255) NOT NULL, `param` VARCHAR(100), `status` ENUM('excluded','included') ); 
ALTER TABLE `seg_rep_templates_dept` ADD CONSTRAINT `FK_report_id` FOREIGN KEY (`report_id`) REFERENCES `seg_rep_templates_registry`(`report_id`) ON UPDATE CASCADE ON DELETE CASCADE; 
ALTER TABLE `seg_rep_templates_dept_params` ADD CONSTRAINT `FK_id` FOREIGN KEY (`id`) REFERENCES `seg_rep_templates_dept`(`id`) ON UPDATE CASCADE ON DELETE CASCADE; 
UPDATE `seg_rep_templates_registry` SET `rep_name` = 'Admission Logbook' WHERE `report_id` = 'Admission_Logbook_For_Docs'; 
