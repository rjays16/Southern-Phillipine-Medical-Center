DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_seg_lab_servdetails_au`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_seg_lab_servdetails_au` AFTER UPDATE ON `seg_lab_servdetails` 
    FOR EACH ROW BEGIN
  
  SET @c_field = '';
  SET @old_val = '';
  SET @new_val = '';
  SET @action = '';
  SET @CHECK = '';
  SET @UUID = UUID();
  SET @user = USER();
  
  SET @create_id_by = (SELECT modify_id FROM `seg_lab_serv` WHERE refno=New.refno);
	
  SET @mod = (SELECT NAME
		FROM `care_users`
		WHERE login_id=NEW.modify_id);
		
SET @src = (SELECT ref_source
		FROM `seg_lab_serv`
		WHERE refno=NEW.refno);
		
IF (OLD.status <> NEW.status) THEN 
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'STATUS','+');
		SET @action = 'update';
			
		IF (OLD.status = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.status,'+');
		END IF;
		
		IF (NEW.status = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.status,'+');
		END IF;
		
		IF (NEW.status = 'deleted') THEN
			SET @action = 'delete';
			SET @c_field = '[BLANK]';
			SET @old_val = CONCAT(OLD.create_dt,'+',@src,'+',OLD.service_code);
			SET @new_val = '[BLANK]';
		END IF;
END IF;
IF (OLD.price_cash <> NEW.price_cash) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'PRICE CASH','+');
		SET @action = 'update';
		
		IF (OLD.price_cash = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.price_cash,'+');
		END IF;
		
		IF (NEW.price_cash = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.price_cash,'+');
		END IF;
		
END IF; 
IF (OLD.price_cash_orig <> NEW.price_cash_orig) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'ORIGINAL CASH PRICE','+');
		SET @action = 'update';
		
		IF (OLD.price_cash_orig = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.price_cash_orig,'+');
		END IF;
		
		IF (NEW.price_cash_orig = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.price_cash_orig,'+');
		END IF;
		
END IF; 
IF (OLD.price_charge <> NEW.price_charge) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'PRICE CHARGE','+');
		SET @action = 'update';
		
		IF (OLD.price_charge = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.price_charge,'+');
		END IF;
		
		IF (NEW.price_charge = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.price_charge,'+');
		END IF;
		
END IF; 
IF (OLD.request_doctor <> NEW.request_doctor) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'REQUEST DOCTOR','+');
		SET @action = 'update';
		
		IF (OLD.request_doctor = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.request_doctor,'+');
		END IF;
		
		IF (NEW.request_doctor = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.request_doctor,'+');
		END IF;
		
END IF; 
IF (OLD.manual_doctor <> NEW.manual_doctor) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'MANUAL DOCTOR','+');
		SET @action = 'update';
		
		IF (OLD.manual_doctor = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.manual_doctor,'+');
		END IF;
		
		IF (NEW.manual_doctor = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.manual_doctor,'+');
		END IF;
		
END IF; 
IF (OLD.request_dept <> NEW.request_dept) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'REQUEST DEPT','+');
		SET @action = 'update';
		
		IF (OLD.request_dept = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.request_dept,'+');
		END IF;
		
		IF (NEW.request_dept = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.request_dept,'+');
		END IF;
		
END IF; 
IF (OLD.is_in_house <> NEW.is_in_house) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IN HOUSE','+');
		SET @action = 'update';
		
		IF (OLD.is_in_house = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_in_house,'+');
		END IF;
		
		IF (NEW.is_in_house = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_in_house,'+');
		END IF;
		
END IF; 
IF (OLD.clinical_info <> NEW.clinical_info) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'CLINICAL INFO','+');
		SET @action = 'update';
		
		IF (OLD.clinical_info = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.clinical_info,'+');
		END IF;
		
		IF (NEW.clinical_info = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.clinical_info,'+');
		END IF;
		
END IF; 
IF (OLD.is_forward <> NEW.is_forward) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IS FORWARD','+');
		SET @action = 'update';
		
		IF (OLD.is_forward = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_forward,'+');
		END IF;
		
		IF (NEW.is_forward = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_forward,'+');
		END IF;
		
END IF; 
IF (OLD.is_served <> NEW.is_served) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IS SERVED','+');
		SET @action = 'update';
		
		IF (OLD.is_served = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_served,'+');
		END IF;
		
		IF (NEW.is_served = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_served,'+');
		END IF;
		
END IF; 
IF (OLD.is_converted <> NEW.is_converted) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IS CONVERTED','+');
		SET @action = 'update';
		
		IF (OLD.is_converted = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_converted,'+');
		END IF;
		
		IF (NEW.is_converted = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_converted,'+');
		END IF;
		
END IF; 
IF (OLD.date_served <> NEW.date_served) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'DATE SERVED','+');
		SET @action = 'update';
		
		IF (OLD.date_served = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.date_served,'+');
		END IF;
		
		IF (NEW.date_served = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.date_served,'+');
		END IF;
		
END IF; 
IF (OLD.clerk_served_by <> NEW.clerk_served_by) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'SERVED BY','+');
		SET @action = 'update';
		
		IF (OLD.clerk_served_by = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.clerk_served_by,'+');
		END IF;
		
		IF (NEW.clerk_served_by = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.clerk_served_by,'+');
		END IF;
		
END IF; 
IF (OLD.clerk_served_date <> NEW.clerk_served_date) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'SERVED DATE','+');
		SET @action = 'update';
		
		IF (OLD.clerk_served_date = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.clerk_served_date,'+');
		END IF;
		
		IF (NEW.clerk_served_date = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.clerk_served_date,'+');
		END IF;
		
END IF; 
IF (OLD.quantity <> NEW.quantity) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'QUANTITY','+');
		SET @action = 'update';
		
		IF (OLD.quantity = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.quantity,'+');
		END IF;
		
		IF (NEW.quantity = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.quantity,'+');
		END IF;
		
END IF; 
IF (OLD.old_qty_request <> NEW.old_qty_request) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'OLD QUANTITY','+');
		SET @action = 'update';
		
		IF (OLD.old_qty_request = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.old_qty_request,'+');
		END IF;
		
		IF (NEW.old_qty_request = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.old_qty_request,'+');
		END IF;
		
END IF; 
IF (OLD.reason_sent_out <> NEW.reason_sent_out) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'REASON SENT OUT','+');
		SET @action = 'update';
		
		IF (OLD.reason_sent_out = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.reason_sent_out,'+');
		END IF;
		
		IF (NEW.reason_sent_out = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.reason_sent_out,'+');
		END IF;
		
END IF; 
IF (OLD.sent_out_date <> NEW.sent_out_date) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'SENT OUT DATE','+');
		SET @action = 'update';
		
		IF (OLD.sent_out_date = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.sent_out_date,'+');
		END IF;
		
		IF (NEW.sent_out_date = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.sent_out_date,'+');
		END IF;
		
END IF; 
IF (OLD.sent_out_by <> NEW.sent_out_by) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'SENT OUT BY','+');
		SET @action = 'update';
		
		IF (OLD.sent_out_by = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.sent_out_by,'+');
		END IF;
		
		IF (NEW.sent_out_by = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.sent_out_by,'+');
		END IF;
		
END IF; 
IF (OLD.is_monitor <> NEW.is_monitor) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IS MONITORED','+');
		SET @action = 'update';
		
		IF (OLD.is_monitor = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_monitor,'+');
		END IF;
		
		IF (NEW.is_monitor = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_monitor,'+');
		END IF;
		
END IF; 
IF (OLD.parent_refno <> NEW.parent_refno) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'PARENT REFNO','+');
		SET @action = 'update';
		
		IF (OLD.parent_refno = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.parent_refno,'+');
		END IF;
		
		IF (NEW.parent_refno = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.parent_refno,'+');
		END IF;
		
END IF; 
IF (OLD.request_flag <> NEW.request_flag) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'REQUEST FLAG','+');
		SET @action = 'update';
		
		IF (OLD.request_flag = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.request_flag,'+');
		END IF;
		
		IF (NEW.request_flag = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.request_flag,'+');
		END IF;
		
END IF; 
IF (OLD.no_gel_tubes <> NEW.no_gel_tubes) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'GEL TUBES','+');
		SET @action = 'update';
		
		IF (OLD.no_gel_tubes = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.no_gel_tubes,'+');
		END IF;
		
		IF (NEW.no_gel_tubes = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.no_gel_tubes,'+');
		END IF;
		
END IF; 
IF (OLD.cancel_reason <> NEW.cancel_reason) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'CANCEL REASON','+');
		SET @action = 'update';
		
		IF (OLD.cancel_reason = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.cancel_reason,'+');
		END IF;
		
		IF (NEW.cancel_reason = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.cancel_reason,'+');
		END IF;
		
END IF; 
IF (OLD.is_posted_lis <> NEW.is_posted_lis) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'POSTED LIS','+');
		SET @action = 'update';
		
		IF (OLD.is_posted_lis = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_posted_lis,'+');
		END IF;
		
		IF (NEW.is_posted_lis = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_posted_lis,'+');
		END IF;
		
END IF; 
		
 IF (@CHECK <> '') THEN
   INSERT INTO seg_audit_trail
   (ID,date_changed,Action_type,login,table_name,field_c,new_value,old_value,pk_field,pk_value) 
   VALUES 
   (UUID(), NOW(),@action,@mod,'seg_lab_servdetails',@c_field,@new_val,@old_val,'refno',NEW.refno);	
 END IF;
 END;
$$

DELIMITER ;