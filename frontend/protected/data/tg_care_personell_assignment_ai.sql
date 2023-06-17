-- Added by Matsuu 
DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_care_personell_assignment_au`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_care_personell_assignment_au` AFTER UPDATE ON `care_personell_assignment` 
    FOR EACH ROW BEGIN
  
 
  SET @CHECK = '';
  SET @department = '';
  SET @status = '';
  SET @c_field= '';
  SET @mod = (SELECT login_id
    FROM `care_users`
    WHERE NAME = NEW.modify_id);
 
  
   IF (NEW.location_nr <> OLD.location_nr) THEN
    SET @CHECK = '1';    
    SET @department = OLD.location_nr;
    SET @c_field = CONCAT(@c_field, 'Department', '+') ;
  END IF;
  
   IF (NEW.status <> OLD.status) THEN
    SET @CHECK = '1';    
    SET @department = OLD.status;
    SET @c_field = CONCAT(@c_field, 'Status', '+') ;
  END IF;
     
    
IF (@CHECK <> '') 
  THEN 
  INSERT INTO seg_audit_trail (ID,date_changed,Action_type,login,table_name,field_c,new_value,old_value,pk_field,pk_value) 
  VALUES
    (UUID(),NOW(),'Update',@mod,'care_personell_assignment',@c_field,@department,OLD.location_nr,'personell_nr',NEW.personell_nr) ;
  END IF ;
END;
$$

DELIMITER ;