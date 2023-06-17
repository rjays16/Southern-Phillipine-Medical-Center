-- updated trigger `tg_care_person_au` --

DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_care_person_au`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_care_person_au` AFTER UPDATE ON `care_person` 
    FOR EACH ROW BEGIN
  
  SET @name_last = '';
  SET @name_first = '';
  SET @name_middle = '';
  SET @date_birth = '';
  SET @sex = '';
  SET @CHECK = '';
  SET @street_name = '';
  SET @brgy_nr = '';
  SET @mun_nr = '';
  SET @mod = (SELECT login_id
    FROM `care_users`
    WHERE NAME = NEW.modify_id);
  IF (NEW.name_last <> OLD.name_last) THEN
    SET @CHECK = '1';
    SET @name_last = OLD.name_last;
  END IF;
  
  IF (NEW.name_first <> OLD.name_first) THEN
    SET @CHECK = '1';
    
    IF (OLD.suffix <> '') THEN
	SET @name_first = REPLACE(OLD.name_first, CONCAT(' ',OLD.suffix), CONCAT(', ',OLD.suffix));
    ELSE
	SET @name_first = OLD.name_first;
    END IF;
    
  END IF;
  
  IF (NEW.name_middle <> OLD.name_middle) THEN
    SET @CHECK = '1';    
    SET @name_middle = OLD.name_middle;
  END IF;
 
 
  IF (NEW.date_birth <> OLD.date_birth) THEN
    SET @CHECK = '1';    
    SET @date_birth = OLD.date_birth;
  END IF;
 
  
  IF (NEW.sex <> OLD.sex) THEN
    SET @CHECK = '1';    
    SET @sex = OLD.sex;
  END IF; 
    	
  
  IF (NEW.street_name <> OLD.street_name) THEN
    SET @CHECK = '1';    
    SET @street_name = OLD.street_name;
  END IF;
  
  
  IF (NEW.brgy_nr <> OLD.brgy_nr) THEN
    SET @CHECK = '1';    
    SET @brgy_nr = OLD.brgy_nr;
  END IF;
  
  
  IF (NEW.mun_nr <> OLD.mun_nr) THEN
    SET @CHECK = '1';    
    SET @mun_nr = OLD.mun_nr;
  END IF;
    
IF (@CHECK <> '') THEN
  INSERT INTO seg_audit_name
  (id, date_changed, encoder, pid, old_name_last, old_name_first, old_name_middle, old_date_birth, old_sex, old_street_name, old_brgy_nr, old_mun_nr) 
  VALUES 
  (UUID(), NOW(), @mod, NEW.pid, @name_last, @name_first, @name_middle, @date_birth, @sex, @street_name, @brgy_nr, @mun_nr);
END IF; 
END;
$$

DELIMITER ;

-- end trigger `tg_care_person_au` --


-- update function `fn_get_person_name_mname` --

DELIMITER $$

USE `hisdb`$$

DROP FUNCTION IF EXISTS `fn_get_person_name_mname`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_person_name_mname`(pid VARCHAR(50)) RETURNS VARCHAR(255) CHARSET latin1
    DETERMINISTIC
BEGIN
  DECLARE person_name VARCHAR(100);
  IF (LEFT(pid,1)='W') THEN
    SET person_name := 
    (SELECT CONCAT(IF (TRIM(p.name_last) IS NULL,'',CONCAT(TRIM(p.name_last),', ')),
                IF(TRIM(p.name_first) IS NULL ,'',CONCAT(TRIM(p.name_first))), 
          IF(TRIM(p.name_middle) IS NULL,'',CONCAT(TRIM(p.name_middle))))
    FROM seg_walkin p
    WHERE p.pid=SUBSTRING(pid,2));
  ELSE
    SET person_name := 
    (SELECT CONCAT(IF (TRIM(p.name_last) IS NULL,'',CONCAT(TRIM(p.name_last),', ')),
                IF(TRIM(p.name_first) IS NULL ,'',CONCAT(TRIM(p.name_first), ' ')),
          IF(TRIM(p.name_middle) IS NULL,'',CONCAT(TRIM(p.name_middle))))
    FROM care_person AS p
    WHERE p.pid=pid);
  END IF;
  RETURN (person_name);
END$$

DELIMITER ;

-- end function `fn_get_person_name_mname` --