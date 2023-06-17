-- Created by Matsuu
DELIMITER $$

USE `hisdb_live`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_seg_dependents_remarks_ai`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_seg_dependents_remarks_ai` AFTER INSERT ON `seg_dependents_remarks` 
    FOR EACH ROW BEGIN
  
  SET @c_field = '';
  SET @old_val = '';
  SET @new_val = '';
  SET @CHECK = '';
  SET @UUID = UUID();
  SET @user = USER();
  SET @mod = (SELECT login_id
		FROM `care_users`
		WHERE NAME=NEW.create_id);
 SET @personnel = (SELECT nr FROM care_personell WHERE pid = NEW.pid);
		
 SET @c_field = CONCAT(@c_field, 'Remark', '+');
 SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
 SET @new_val = CONCAT(@new_val , NEW.remarks,'+');
   INSERT INTO seg_audit_trail
   (ID,date_changed,Action_type,login,table_name,field_c,new_value,old_value,pk_field,pk_value) 
   VALUES 
   (UUID(), NOW(),'Insert',@mod,'seg_dependents_remarks',@c_field,@new_val,@old_val,'pid',@personnel);
 END;
$$

DELIMITER ;

-- Ended here...