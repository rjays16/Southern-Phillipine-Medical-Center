INSERT INTO `seg_rep_templates_dept` (`report_id`, `dept_nr`, `template_name`) VALUES ('opd_daily_trans', '182', 'PSY_OPD_daily_transaction'); 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('2', 'department', 'excluded'); 

#Note: Already Exexcuted in .72 
INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('2', 'check_type_dept', 'excluded'); 



#New function for description
DELIMITER $$

USE `hisdb`$$

DROP FUNCTION IF EXISTS `fn_get_icd_name_encounter`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_icd_name_encounter`(encounter_nr VARCHAR(50)) RETURNS VARCHAR(100) CHARSET latin1
    DETERMINISTIC
BEGIN
  DECLARE c_icd VARCHAR(50) DEFAULT NULL;
  DECLARE icd_list VARCHAR(100);
        DECLARE done INT DEFAULT 0;
        DECLARE cursor1 CURSOR FOR
  
        SELECT cie.description FROM care_encounter_diagnosis AS ced 
    INNER JOIN care_icd10_en AS cie ON ced.code = cie.diagnosis_code
                         WHERE ced.encounter_nr=encounter_nr
                         AND ced.status NOT IN ('deleted','hidden','inactive','void');
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=1;
        OPEN cursor1;
        SET icd_list = '';  
  REPEAT
     FETCH cursor1 INTO c_icd;
           IF NOT done THEN
             BEGIN
                SET icd_list = TRIM(CONCAT(icd_list,',',c_icd));
             END; 
           END IF;
  UNTIL done  END REPEAT;
        CLOSE cursor1;
  
  RETURN (SUBSTRING(TRIM(icd_list),2));
END$$

DELIMITER ;

-- End here..