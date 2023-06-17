ALTER TABLE `seg_lab_serv` ADD COLUMN `is_repeatcollection` TINYINT(1) DEFAULT 0 NULL AFTER `is_printed`; 
INSERT INTO `seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('lab_pattype', NULL, 'Patient Type', 'option', '\'all-All Patient\',\'1-ER Patient\',\'2-Admitted Patient\',\'3-Outpatient\',\'4-Walk in\',\'5-Outpatient & Walk in\', \'6-RDU\'', '1', '0'); 
INSERT INTO `seg_rep_templates_registry` (`report_id`, `rep_group`, `rep_name`, `rep_description`, `rep_script`, `rep_dept_nr`, `rep_category`, `is_active`, `with_template`, `query_in_jasper`, `template_name`, `exclusive_opd_er`, `exclusive_death`, `w_graphical`) VALUES ('LAB_repeat_collection', 'Hospital Operations', 'Repeat Collection', 'Repeat Collection', 'LAB_repeat_collection', '246', 'HOSP', '1', '1', '0', 'LAB_repeat_collection', '0', '0', '0'); 
INSERT INTO `seg_rep_template_params` (`report_id`, `param_id`) VALUES ('LAB_repeat_collection', 'lab_pattype'); 
INSERT INTO `seg_rep_template_params` (`report_id`, `param_id`) VALUES ('LAB_repeat_collection', 'time'); 

#new Function 
#note apply this also
DELIMITER $$

USE `hisdb`$$

DROP FUNCTION IF EXISTS `fn_get_er_location_name`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_er_location_name`(dept_nr MEDIUMINT(8)) RETURNS VARCHAR(100) CHARSET latin1
    DETERMINISTIC
BEGIN
	DECLARE location_name VARCHAR(100);
	SET location_name := (SELECT area_location
		FROM seg_er_location
		WHERE location_id=dept_nr);
	RETURN (location_name);
END$$

DELIMITER ;


-- Addidional changes
UPDATE `hisdb`.`seg_rep_templates_registry` SET `rep_dept_nr` = '156' WHERE `report_id` = 'LAB_repeat_collection'; 
