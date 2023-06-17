-- Created by Matsuu
DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_care_users_au`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_care_users_au` AFTER UPDATE ON `care_users` 
    FOR EACH ROW BEGIN
  
  SET @password = '';
  SET @mod = (SELECT login_id
    FROM `care_users`
    WHERE NAME = NEW.modify_id);
  
  IF (NEW.password <> OLD.password) THEN
    SET @CHECK = '1';
    SET @password = OLD.password;
  END IF;
  
IF (@CHECK <> '') THEN
 INSERT INTO seg_audit_trail
   (ID,date_changed,Action_type,login,table_name,field_c,new_value,old_value,pk_field,pk_value) 
   VALUES 
   (UUID(), NOW(),'Update',@mod,'care_users_trail',@c_field,@new_val,@old_val,'personell_nr',NEW.personell_nr); 
   END IF; 
END;
$$

DELIMITER ;

-- Ended here..