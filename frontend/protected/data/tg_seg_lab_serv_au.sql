DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_seg_lab_serv_au`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_seg_lab_serv_au` AFTER UPDATE ON `seg_lab_serv` 
    FOR EACH ROW BEGIN
  
  SET @c_field = '';
  SET @old_val = '';
  SET @new_val = '';
  SET @action = '';
  SET @CHECK = '';
  SET @UUID = UUID();
  SET @user = USER();
  
  SET @for_lb = (SELECT NAME
		FROM `care_users`
		WHERE login_id=NEW.modify_id);  
	
  SET @spl_bb = (SELECT login_id
		FROM `care_users`
		WHERE NAME=NEW.modify_id); 
		
  IF (ISNULL(@for_lb)) THEN
	SET @mod = (SELECT NAME
		FROM `care_users`
		WHERE NAME=NEW.modify_id); 
  END IF;
  
  IF (ISNULL(@spl_bb)) THEN
	SET @mod = (SELECT NAME
		FROM `care_users`
		WHERE login_id=NEW.modify_id);
  END IF;
	
	
	
	IF (NEW.status <> '') THEN 
		SET @CHECK = '1';
		SET @action = 'delete';	
		SET @c_field = CONCAT(@c_field, 'STATUS','+');
		SET @new_val = CONCAT(@new_val, NEW.status,'+');
		
	END IF;
	
	
	
	
	IF (OLD.serv_dt <> NEW.serv_dt) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'SERVE DATE','+');
		SET @action = 'update';
		
		IF (OLD.serv_dt = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.serv_dt,'+');
		END IF;
		
		IF (NEW.serv_dt = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.serv_dt,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.serv_tm <> NEW.serv_tm) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'SERVE TIME','+');
		SET @action = 'update';
		
		IF (OLD.serv_tm = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.serv_tm,'+');
		END IF;
		
		IF (NEW.serv_tm = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.serv_tm,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.is_cash <> NEW.is_cash) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IS CASH','+');
		SET @action = 'update';
		
		IF (OLD.is_cash = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_cash,'+');
		END IF;
		
		IF (NEW.is_cash = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_cash,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.type_charge <> NEW.type_charge) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'TYPE CHARGE','+');
		SET @action = 'update';
		
		IF (OLD.type_charge = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.type_charge,'+');
		END IF;
		
		IF (NEW.type_charge = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.type_charge,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.custom_ptype <> NEW.custom_ptype) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'PATIENT TYPE','+');
		SET @action = 'update';
		
		IF (OLD.custom_ptype = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.custom_ptype,'+');
		END IF;
		
		IF (NEW.custom_ptype = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.custom_ptype,'+');
		END IF;
		
	END IF;  
	
	IF (OLD.is_urgent <> NEW.is_urgent) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IS URGENT','+');
		SET @action = 'update';
		
		IF (OLD.is_urgent = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_urgent,'+');
		END IF;
		
		IF (NEW.is_urgent = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_urgent,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.is_tpl <> NEW.is_tpl) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IS TPL','+');
		SET @action = 'update';
		
		IF (OLD.is_tpl = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_tpl,'+');
		END IF;
		
		IF (NEW.is_tpl = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_tpl,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.is_approved <> NEW.is_approved) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IS APPROVED','+');
		SET @action = 'update';
		
		IF (OLD.is_approved = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_approved,'+');
		END IF;
		
		IF (NEW.is_approved = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_approved,'+');
		END IF;
		
	END IF; 
	
	
	
	IF ((OLD.comments IS NULL) OR (OLD.comments <> NEW.comments)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'COMMENTS','+');
		SET @action = 'update';
		
		IF (OLD.comments = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.comments,'+');
		END IF;
		
		IF (NEW.comments = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.comments,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.ordername IS NULL) OR (OLD.ordername <> NEW.ordername)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'ORDER NAME','+');
		SET @action = 'update';
		
		IF (OLD.ordername = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.ordername,'+');
		END IF;
		
		IF (NEW.ordername = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.ordername,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.orderaddress IS NULL) OR (OLD.orderaddress <> NEW.orderaddress)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'ORDER ADDRESS','+');
		SET @action = 'update';
		
		IF (OLD.orderaddress = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.orderaddress,'+');
		END IF;
		
		IF (NEW.orderaddress = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.orderaddress,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.discountid IS NULL) OR (OLD.discountid <> NEW.discountid)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'DISCOUNT ID','+');
		SET @action = 'update';
		
		
		IF (OLD.discountid = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.discountid,'+');
		END IF;
		
		IF (NEW.discountid = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.discountid,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.loc_code IS NULL) OR (OLD.loc_code <> NEW.loc_code)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'Location Code','+');
		SET @action = 'update';
		
		IF (OLD.loc_code = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.loc_code,'+');
		END IF;
		
		IF (NEW.loc_code = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.loc_code,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.parent_refno IS NULL) OR (OLD.parent_refno <> NEW.parent_refno)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'PARENT REF NO','+');
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
	
	IF ((OLD.approved_by_head IS NULL) OR (OLD.approved_by_head <> NEW.approved_by_head)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'APPROVED BY HEAD','+');
		SET @action = 'update';
		
		IF (OLD.approved_by_head = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.approved_by_head,'+');
		END IF;
		
		IF (NEW.approved_by_head = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.approved_by_head,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.remarks IS NULL) OR (OLD.remarks <> NEW.remarks)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'REMARKS','+');
		SET @action = 'update';
		
		IF (OLD.remarks = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.remarks,'+');
		END IF;
		
		IF (NEW.remarks = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.remarks,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.headID IS NULL) OR (OLD.headID <> NEW.headID)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'HEAD ID','+');
		SET @action = 'update';
		
		IF (OLD.headID = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.headID,'+');
		END IF;
		
		IF (NEW.headID = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.headID,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.headpasswd IS NULL) OR (OLD.headpasswd <> NEW.headpasswd)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'HEAD PASSWORD','+');
		SET @action = 'update';
		
		IF (OLD.headpasswd = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.headpasswd,'+');
		END IF;
		
		IF (NEW.headpasswd = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.headpasswd,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.discount IS NULL) OR (OLD.discount <> NEW.discount)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'DISCOUNT','+');
		SET @action = 'update';
		
		IF (OLD.discount = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.discount,'+');
		END IF;
		
		IF (NEW.discount = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.discount,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.fromBB <> NEW.fromBB) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'FROM BLOOD BANK','+');
		SET @action = 'update';
		
		IF (OLD.fromBB = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.fromBB,'+');
		END IF;
		
		IF (NEW.fromBB = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.fromBB,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.walkin_pid <> NEW.walkin_pid) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'WALKIN PATIENT ID','+');
		SET @action = 'update';
		
		IF (OLD.walkin_pid = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.walkin_pid,'+');
		END IF;
		
		IF (NEW.walkin_pid = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.walkin_pid,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.source_req IS NULL) OR (OLD.source_req <> NEW.source_req)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'SOURCE REQUEST','+');
		SET @action = 'update';
		
		IF (OLD.source_req = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.source_req,'+');
		END IF;
		
		IF (NEW.source_req = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.source_req,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.is_repeat <> NEW.is_repeat) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IS REPEAT','+');
		SET @action = 'update';
		
		IF (OLD.is_repeat = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_repeat,'+');
		END IF;
		
		IF (NEW.is_repeat = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_repeat,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.is_rdu <> NEW.is_rdu) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'RDU','+');
		SET @action = 'update';
		
		IF (OLD.is_rdu = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_rdu,'+');
		END IF;
		
		IF (NEW.is_rdu = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_rdu,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.is_walkin <> NEW.is_walkin) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'WALKIN','+');
		SET @action = 'update';
		
		IF (OLD.is_walkin = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_walkin,'+');
		END IF;
		
		IF (NEW.is_walkin = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_walkin,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.is_pe <> NEW.is_pe) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'PE','+');
		SET @action = 'update';
		
		IF (OLD.is_pe = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_pe,'+');
		END IF;
		
		IF (NEW.is_pe = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_pe,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.area_type <> NEW.area_type) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'AREA TYPE','+');
		SET @action = 'update';
		
		IF (OLD.area_type = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.area_type,'+');
		END IF;
		
		IF (NEW.area_type = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.area_type,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.grant_type IS NULL) OR (OLD.grant_type <> NEW.grant_type)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'GRANT TYPE','+');
		SET @action = 'update';
		
		IF (OLD.grant_type = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.grant_type,'+');
		END IF;
		
		IF (NEW.grant_type = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.grant_type,'+');
		END IF;
		
	END IF; 
	
	IF ((OLD.ref_source IS NULL) OR (OLD.ref_source <> NEW.ref_source)) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'REF SOURCE','+');
		SET @action = 'update';
		
		IF (OLD.ref_source = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.ref_source,'+');
		END IF;
		
		IF (NEW.ref_source = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.ref_source,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.emr_orderno <> NEW.emr_orderno) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'EMR ORDERNO','+');
		SET @action = 'update';
		
		IF (OLD.emr_orderno = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.emr_orderno,'+');
		END IF;
		
		IF (NEW.emr_orderno = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.emr_orderno,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.still_in_er <> NEW.still_in_er) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'IN ER','+');
		SET @action = 'update';
		
		IF (OLD.still_in_er = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.still_in_er,'+');
		END IF;
		
		IF (NEW.still_in_er = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.still_in_er,'+');
		END IF;
		
	END IF; 
	
	IF (OLD.is_printed <> NEW.is_printed) THEN
		SET @CHECK = '1';
		SET @c_field = CONCAT(@c_field, 'PRINTED','+');
		SET @action = 'update';
		
		IF (OLD.is_printed = '') THEN 
			SET @old_val = CONCAT(@old_val, '[BLANK]','+');
		ELSE
			SET @old_val = CONCAT(@old_val, OLD.is_printed,'+');
		END IF;
		
		IF (NEW.is_printed = '') THEN 
			SET @new_val = CONCAT(@new_val, '[BLANK]','+');
		ELSE
			SET @new_val = CONCAT(@new_val, NEW.is_printed,'+');
		END IF;
		
	END IF; 
 IF (@CHECK <> '') THEN
   INSERT INTO seg_audit_trail
   (ID,date_changed,Action_type,login,table_name,field_c,new_value,old_value,pk_field,pk_value) 
   VALUES 
   (UUID(), NOW(),@action,@mod,'seg_lab_serv',@c_field,@new_val,@old_val,'refno',NEW.refno); 
   
 END IF;
 END;
$$

DELIMITER ;