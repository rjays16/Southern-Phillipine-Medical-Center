DELIMITER $$

USE `hisdb_live`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_seg_radio_serv_ai`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_seg_radio_serv_ai` AFTER INSERT ON `seg_radio_serv` 
    FOR EACH ROW BEGIN
  
  SET @c_field = '';
  SET @old_val = '';
  SET @new_val = '';
  SET @CHECK = '';
  SET @UUID = UUID();
  SET @user = USER();
  
  
	
  
    SET @for_lb = (SELECT NAME
		FROM `care_users`
		WHERE login_id=NEW.create_id);  
	
   SET @spl_bb = (SELECT login_id
		FROM `care_users`
		WHERE NAME=NEW.create_id); 
		
  IF (ISNULL(@for_lb)) THEN
	SET @mod = (SELECT login_id
		FROM `care_users`
		WHERE NAME=NEW.create_id); 
  END IF;
  
  IF (ISNULL(@spl_bb)) THEN
	SET @mod = (SELECT login_id
		FROM `care_users`
		WHERE login_id=NEW.create_id);
  END IF;
  
  		
 SET @c_field = CONCAT(@c_field, 'All Field', '+');
 SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
 SET @new_val = CONCAT(@new_val , 'NEW','+');
 
   INSERT INTO seg_audit_trail
   (ID,date_changed,Action_type,login,table_name,field_c,new_value,old_value,pk_field,pk_value) 
   VALUES 
   (UUID(), NOW(),'Insert',@mod,'seg_radio_serv',@c_field,@new_val,@old_val,'refno',NEW.refno);	
 
 END;
$$

DELIMITER ;