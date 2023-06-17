-- Created by Matsuu 
DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_seg_dependents_remarks_au`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_seg_dependents_remarks_au` AFTER UPDATE ON `seg_dependents_remarks` 
    FOR EACH ROW BEGIN
  
  SET @c_field = '';
  SET @old_val = '';
  SET @new_val = '';
  SET @CHECK = '';
  SET @UUID = UUID();
  SET @user = USER();
  SET @mod = (SELECT login_id
    FROM `care_users`
    WHERE NAME=NEW.midify_id LIMIT 1);
  SET @personnel = (SELECT nr FROM care_personell WHERE pid = NEW.pid);
  IF (NEW.remarks <> OLD.remarks) THEN
    SET @CHECK = '1';
    SET @c_field = CONCAT(@c_field, 'Remarks', '+');
    
    IF (OLD.remarks = '') THEN
      SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
    ELSE
      SET @old_val = CONCAT(@old_val,OLD.remarks, '+');
    END IF;
    
    IF (NEW.remarks = '') THEN
      SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
    ELSE
      SET @new_val = CONCAT(@new_val, NEW.remarks,'+');
    END IF;  
  END IF;
  IF (NEW.status <> OLD.status) THEN
    SET @CHECK = '1';
    SET @c_field = CONCAT(@c_field, 'status', '+');
    
    IF (OLD.status = '') THEN
      SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
    ELSE
      SET @old_val = CONCAT(@old_val,OLD.status, '+');
    END IF;
    
    IF (NEW.status = '') THEN
      SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
    ELSE
      SET @new_val = CONCAT(@new_val, NEW.status,'+');
    END IF;  
  END IF;
  
  
  IF (@CHECK <> '') THEN
   INSERT INTO seg_audit_trail
   (ID,date_changed,Action_type,login,table_name,field_c,new_value,old_value,pk_field,pk_value) 
   VALUES 
   (UUID(), NOW(),'Update',@mod,'seg_dependents_remarks',@c_field,@new_val,@old_val,'personell_nr',@personnel); 
 END IF;
 END;
$$

DELIMITER ;

-- Ended here...