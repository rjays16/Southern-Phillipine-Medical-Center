-- Added by Matsuu 

DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_seg_dependents_au`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_seg_dependents_au` AFTER UPDATE ON `seg_dependents` 
    FOR EACH ROW BEGIN
  
 
  SET @CHECK = '';
  SET @relationship = '';
  SET @c_field= '';
  SET @mod = (SELECT login_id
    FROM `care_users`
    WHERE NAME = NEW.modify_id);
 
  
   IF (NEW.relationship <> OLD.relationship) THEN
    SET @CHECK = '1';    
    SET @relationship = OLD.relationship;
    SET @c_field = CONCAT(@c_field, 'Relationship', '+') ;
  END IF;
     
    
IF (@CHECK <> '') 
  THEN 
  INSERT INTO seg_audit_trail (ID,date_changed,Action_type,login,table_name,field_c,new_value,old_value,pk_field,pk_value) 
  VALUES
    (UUID(),NOW(),'Update',@mod,'seg_dependents',@c_field,@relationship,OLD.relationship,'Dependent ID',NEW.dependent_pid) ;
  END IF ;
END;
$$

DELIMITER ;

-- Ended here..