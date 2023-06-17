CREATE TABLE `hisdb`.`seg_soa_diagnosis_new`( `diag_id` INT(15) NOT NULL AUTO_INCREMENT, `encounter_nr` VARCHAR(45) NOT NULL, `final_diagnosis` TEXT, `create_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00', `create_id` VARCHAR(35), `modify_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `modify_id` VARCHAR(35), `history` TEXT, PRIMARY KEY (`diag_id`, `encounter_nr`) ) ENGINE=INNODB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 
ALTER TABLE `hisdb`.`seg_soa_diagnosis_new` ADD COLUMN `other_diagnosis` TEXT NULL AFTER `final_diagnosis`; 

CREATE TABLE `hisdb`.`seg_audit_diagnosis`( `id` CHAR(15) NOT NULL, `date_changed` DATETIME NOT NULL, `encoder` VARCHAR(35) NOT NULL, `encounter_nr` VARCHAR(45) NOT NULL, `old_final_diagnosis` TEXT, `old_other_diagnosis` TEXT, PRIMARY KEY (`id`) ); 
ALTER TABLE `hisdb`.`seg_audit_diagnosis` ADD COLUMN `type` VARCHAR(45) NULL AFTER `old_other_diagnosis`; 
ALTER TABLE `hisdb`.`seg_audit_diagnosis` CHANGE `type` `tod` VARCHAR(45) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Type of Diagnosis'; 

DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_seg_soa_diagnosis_new_au`$$

CREATE
    /*!50017 DEFINER = 'seniordev'@'%' */
    TRIGGER `tg_seg_soa_diagnosis_new_au` AFTER UPDATE ON `seg_soa_diagnosis_new` 
    FOR EACH ROW BEGIN
  SET @final_diagnosis = '' ;
  SET @other_diagnosis = '' ;
  SET @CHECK = '' ;
  SET @mod = NEW.modify_id ;
  SET @type = '' ;
  IF (
    NEW.final_diagnosis <> OLD.final_diagnosis
  ) 
  THEN SET @CHECK = '1' ;
  SET @final_diagnosis = NEW.final_diagnosis ;
  SET @type = 'Final Diagnosis' ;
  END IF ;
  IF (
    NEW.other_diagnosis <> OLD.other_diagnosis
  ) 
  THEN SET @CHECK = '1' ;
  SET @other_diagnosis = NEW.other_diagnosis ;
  SET @type = 'Other Diagnosis' ;
  END IF ;
  IF (
    OLD.other_diagnosis IS NULL 
    AND NEW.other_diagnosis IS NOT NULL
  ) 
  THEN SET @CHECK = '1' ;
  SET @other_diagnosis = NEW.other_diagnosis ;
  SET @type = 'Other Diagnosis' ;
  END IF ;
  IF (
    OLD.final_diagnosis IS NULL 
    AND NEW.final_diagnosis IS NOT NULL
  ) 
  THEN SET @CHECK = '1' ;
  SET @final_diagnosis = NEW.final_diagnosis ;
  SET @type = 'Final Diagnosis' ;
  END IF ;
  IF (@CHECK <> '') 
  THEN 
  INSERT INTO seg_audit_diagnosis (
    id,
    date_changed,
    encoder,
    encounter_nr,
    old_final_diagnosis,
    old_other_diagnosis,
    tod
  ) 
  VALUES
    (
      UUID(),
      NOW(),
      @mod,
      NEW.encounter_nr,
      @final_diagnosis,
      @other_diagnosis,
      @type
    ) ;
  END IF ;
END;
$$

DELIMITER ;


DELIMITER $$

USE `hisdb` $$

DROP FUNCTION IF EXISTS `fn_get_personell_lastname_first_by_loginid` $$

CREATE DEFINER = `root` @`localhost` FUNCTION `fn_get_personell_lastname_first_by_loginid` (personell_nr VARCHAR (50)) RETURNS VARCHAR (100) CHARSET latin1 DETERMINISTIC 
BEGIN
  DECLARE personell_name VARCHAR (100) ;
  SET personell_name := 
  (SELECT 
    CONCAT(
      TRIM(cp_2.name_last),
      ', ',
      TRIM(cp_2.name_first),
      ' ',
      IF(
        TRIM(cp_2.name_middle) <> '',
        CONCAT(
          LEFT(TRIM(cp_2.name_middle), 1),
          '. '
        ),
        ''
      )
    ) AS fullname 
  FROM
    care_personell AS cpl_2,
    care_person AS cp_2,
    care_users AS cu 
  WHERE cu.login_id = personell_nr 
    AND cu.personell_nr = cpl_2.nr 
    AND cp_2.pid = cpl_2.pid) ;
  RETURN (personell_name) ;
END $$

DELIMITER ;



DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_seg_soa_diagnosis_new_ai`$$

CREATE
    /*!50017 DEFINER = 'seniordev'@'%' */
    TRIGGER `tg_seg_soa_diagnosis_new_ai` AFTER INSERT ON `seg_soa_diagnosis_new` 
    FOR EACH ROW BEGIN
  SET @tod = '' ;
  SET @mod = NEW.create_id ;
  SET @CHECK = '' ;
  IF(
    new.final_diagnosis IS NOT NULL 
    AND TRIM(new.final_diagnosis) <> ''
  ) 
  THEN SET @tod = 'Final Diagnosis' ;
  SET @CHECK = '1' ;
  END IF ;
  IF(
    new.other_diagnosis IS NOT NULL 
    AND TRIM(new.other_diagnosis) <> ''
  ) 
  THEN SET @tod = 'Other Diagnosis' ;
  SET @CHECK = '1' ;
  END IF ;
  IF (@CHECK <> '') 
  THEN 
  INSERT INTO seg_audit_diagnosis (
    id,
    date_changed,
    encoder,
    encounter_nr,
    old_final_diagnosis,
    old_other_diagnosis,
    tod
  ) 
  VALUES
    (
      UUID(),
      NOW(),
      @mod,
      new.encounter_nr,
      new.final_diagnosis,
      new.other_diagnosis,
      @tod
    ) ;
  END IF ;
END;
$$

DELIMITER ;



