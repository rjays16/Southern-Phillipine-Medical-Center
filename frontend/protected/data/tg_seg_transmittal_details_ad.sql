DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_seg_transmittal_details_ad`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_seg_transmittal_details_ad` AFTER DELETE ON `seg_transmittal_details` 
    FOR EACH ROW BEGIN
  SET @c_field = '';
  SET @old_val = '';
  SET @new_val = '';
  SET @CHECK = '';
  SET @UUID = UUID();
  SET @user = USER();
		
	SET @c_field = CONCAT(@c_field, 'All Field', '+');
	SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
	SET @new_val = CONCAT(@new_val , 'NEW','+');
	SET @mod = (SELECT cu.name
		FROM `care_users` AS cu
		WHERE cu.name = OLD.modify_id);
		
	SET @reason = (SELECT rd.reason
			FROM `seg_transmittal_reason_delete` AS rd
			WHERE rd.enc_nr = OLD.encounter_nr
			AND rd.transmit_no = OLD.transmit_no
			ORDER BY del_date DESC LIMIT 1);
			
	IF (@reason = 'Others') THEN
		SET @reason = (SELECT rd.other_reason
				FROM `seg_transmittal_reason_delete` AS rd
				WHERE rd.`enc_nr` = OLD.encounter_nr
				AND rd.`transmit_no` = OLD.transmit_no
				ORDER BY del_date DESC LIMIT 1);
	END IF;
		
   INSERT INTO seg_transmittal_trail
   (id,trans_date,action_type,transmit_no,encounter_nr,login_id,field_change,reason_delete) 
   VALUES 
   (UUID(), NOW(),'Delete',OLD.transmit_no,OLD.encounter_nr,@mod,'Case No.',@reason);	
    END;
$$

DELIMITER ;