-- Trigger for SPMC-594 & SPMC - 660
DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_care_test_request_radio_ai`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_care_test_request_radio_ai` AFTER INSERT ON `care_test_request_radio` 
    FOR EACH ROW BEGIN
SET @c_field = '';
SET @old_val = '';
SET @new_val = '';
SET @CHECK = '';
SET @mod = '';
SET @UUID = UUID();
SET @user = USER();
SET @mod = (SELECT login_id
FROM `care_users`
WHERE NAME=NEW.create_id);
SET @c_field = CONCAT(@c_field, 'All Field', '+');
SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
SET @new_val = CONCAT(@new_val , 'NEW','+');
INSERT INTO seg_audit_trail(ID,date_changed,Action_type,login,table_name,field_c,new_value,old_value,pk_field,pk_value)
VALUES
(UUID(), NOW(),'INSERT',@mod,'care_test_request_radio',@c_field,@new_val,@old_val,'refno',NEW.refno);
END;
$$

DELIMITER ;