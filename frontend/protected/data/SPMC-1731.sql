INSERT INTO `hisdb`.`seg_rep_templates_registry` (
  `report_id`,
  `rep_group`,
  `rep_name`,
  `rep_description`,
  `rep_script`,
  `rep_dept_nr`,
  `rep_category`,
  `with_template`,
  `template_name`
) 
VALUES
  (
    'MR_Book_Report',
    'Hospital Operations',
    'Birth Registry',
    'Birth Registry',
    'MR_Book_Registry_Report',
    '151',
    'HOSP',
    1,
    'MR_Book_Registry_Report'
  ) ;

INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) 
VALUES
  ('MR_Book_Report', 'alpha') ;

INSERT INTO `hisdb`.`seg_rep_params` (
  `param_id`,
  `parameter`,
  `param_type`,
  `choices`
) 
VALUES
  ('patient_sex', 'Patient\'s Sex', 'option','\'male-Male\',\'female-Female\'') ;


INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) 
VALUES
  ('MR_Book_Report', 'patient_sex') ;
  
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) 
VALUES
  ('MR_Book_Report', 'alpha') ;

INSERT INTO `hisdb`.`seg_rep_params` (
  `param_id`,
  `parameter`,
  `param_type`,
  `choices`
) 
VALUES
  (
    'birth_type',
    'Type of Birth',
    'option',
    '\'single-Single\',\'twins-Twin\',\'triplets-Triplets\',\'others-Others\''
  ) ;

INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) 
VALUES
  ('MR_Book_Report', 'birth_type') ;

INSERT INTO `hisdb`.`care_config_global` (
  `type`,
  `value`,
  `notes`,
  `status`,
  `history`,
  `modify_id`,
  `modify_time`,
  `create_id`,
  `create_time`
) 
VALUES
  (
    'limit_access_permission_himd',
    '151',
    NULL,
    '',
    '',
    '',
    '2018-09-11 20:40:25',
    'medocs',
    '2019-07-02 11:44:03'
  ) ;


  -- New Function fn_get_personellname_lastfirstmiddle
DELIMITER $$

USE `hisdb`$$

DROP FUNCTION IF EXISTS `fn_get_personellname_lastfirstmiddle`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_personellname_lastfirstmiddle`(personell_nr INT(11)) RETURNS VARCHAR(180) CHARSET latin1
    DETERMINISTIC
BEGIN
    DECLARE personell_name VARCHAR(180);
    SET personell_name := (SELECT CONCAT(TRIM(cp_2.name_last), IF(TRIM(cp_2.name_first)<>'', CONCAT(', ',TRIM(cp_2.name_first)), ' '), IF(TRIM(cp_2.name_middle)<>'', CONCAT(' ',TRIM(cp_2.name_middle)),'')) AS fullname 
      FROM care_personell AS cpl_2, care_person AS cp_2
      WHERE cpl_2.nr = personell_nr AND cp_2.pid=cpl_2.pid);
    RETURN (personell_name);
    END$$

DELIMITER ;
-- end fn_get_personellname_lastfirstmiddle
