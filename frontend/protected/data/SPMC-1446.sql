
DELIMITER $$ /*tg_care_person_au*/

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
    SET @name_first = OLD.name_first;
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
  (UUID(), (SELECT MAX(sap.date_changed)FROM seg_audit_phic sap WHERE sap.pid = NEW.pid), @mod, NEW.pid, @name_last, @name_first, @name_middle, @date_birth, @sex, @street_name, @brgy_nr, @mun_nr);
END IF; 
END;
$$

DELIMITER ;

ALTER TABLE `hisdb`.`seg_discharge_slip_info_ipbm` CHANGE `notes` `notes` TEXT CHARSET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hisdb`.`seg_discharge_slip_info_ipbm` CHANGE `medications` `medications` LONGTEXT NULL, CHANGE `medtime` `medtime` VARCHAR(100) CHARSET latin1 COLLATE latin1_swedish_ci DEFAULT '-' NULL;

