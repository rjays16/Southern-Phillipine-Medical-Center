DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_care_test_request_radio_au`$$

CREATE /*!50017 DEFINER = 'root'@'localhost' */ TRIGGER `tg_care_test_request_radio_au` AFTER UPDATE
ON `care_test_request_radio` FOR EACH ROW
BEGIN  
  SET @c_field = '' ;
  SET @old_val = '' ;
  SET @new_val = '' ;
  SET @action = '' ;
  SET @CHECK = '' ;
  SET @UUID = UUID() ;
  SET @user = USER() ;
  SET @mod = (SELECT NAME
    FROM `care_users`
    WHERE `login_id`=NEW.modify_id);
  IF (OLD.status <> NEW.status) THEN 
  SET @CHECK = '1' ;
  # SET @c_field = CONCAT(@c_field, 'STATUS','+');
  # SET @action = 'update';
      
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
  SET @action = 'delete' ;
  SET @c_field = '[BLANK]' ;
  SET @old_val = CONCAT(OLD.create_dt,'+','RDsss','+',OLD.service_code);
  SET @new_val = '[BLANK]' ;
  END IF ;
  
  
  #IF (OLD.status = '') THEN 
  #SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  #SET @action = 'update' ;
  #ELSE 
  #SET @old_val = CONCAT(@old_val, OLD.status, '+') ;
  #SET @action = 'update' ;
  #END IF ;
  
  #IF (NEW.status = '') THEN 
  #SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  #SET @action = 'update' ;
  #ELSE 
  #SET @new_val = CONCAT(@new_val, NEW.status, '+') ;
  #SET @action = 'update' ;
  #END IF ;
  
  END IF ;
  IF (
    OLD.clinical_info <> NEW.clinical_info
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'CLINICAL INFO', '+') ;
  SET @action = 'update' ;
  IF (OLD.clinical_info = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.clinical_info, '+') ;
  END IF ;
  IF (NEW.clinical_info = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.clinical_info, '+') ;
  END IF ;
  END IF ;
  /* clinical_info end */
  IF (OLD.price_cash <> NEW.price_cash) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'PRICE CASH', '+') ;
  SET @action = 'update' ;
  IF (OLD.price_cash = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.clinical_info, '+') ;
  END IF ;
  IF (NEW.price_cash = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.price_cash, '+') ;
  END IF ;
  END IF ;
  /* price_cash end */
  IF (
    OLD.price_cash_orig <> NEW.price_cash_orig
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(
    @c_field,
    'ORIGINAL CASH PRICE',
    '+'
  ) ;
  SET @action = 'update' ;
  IF (OLD.price_cash_orig = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(
    @old_val,
    OLD.price_cash_orig,
    '+'
  ) ;
  END IF ;
  IF (NEW.price_cash_orig = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(
    @new_val,
    NEW.price_cash_orig,
    '+'
  ) ;
  END IF ;
  END IF ;
  /* price_cash_orig end */
  IF (
    OLD.price_charge <> NEW.price_charge
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(
    @c_field,
    'ORIGINAL CASH PRICE',
    '+'
  ) ;
  SET @action = 'update' ;
  IF (OLD.price_charge = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.price_charge, '+') ;
  END IF ;
  IF (NEW.price_charge = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.price_charge, '+') ;
  END IF ;
  END IF ;
  /* price_charge end */
  IF (
    OLD.service_date <> NEW.service_date
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'SERVICE DATE', '+') ;
  SET @action = 'update' ;
  IF (OLD.service_date = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.service_date, '+') ;
  END IF ;
  IF (NEW.service_date = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.service_date, '+') ;
  END IF ;
  END IF ;
  /* service_date end */
  IF (
    OLD.is_in_house <> NEW.is_in_house
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'IN HOUSE', '+') ;
  SET @action = 'update' ;
  IF (OLD.is_in_house = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.is_in_house, '+') ;
  END IF ;
  IF (NEW.is_in_house = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.is_in_house, '+') ;
  END IF ;
  END IF ;
  /* is_in_house end */
  IF (
    OLD.request_doctor <> NEW.request_doctor
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'REQUEST DOCTOR', '+') ;
  SET @action = 'update' ;
  IF (OLD.request_doctor = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.request_doctor, '+') ;
  END IF ;
  IF (NEW.request_doctor = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.request_doctor, '+') ;
  END IF ;
  END IF ;
  /* request_doctor end */
  IF (
    OLD.manual_doctor <> NEW.manual_doctor
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'MANUAL DOCTOR', '+') ;
  SET @action = 'update' ;
  IF (OLD.manual_doctor = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.manual_doctor, '+') ;
  END IF ;
  IF (NEW.manual_doctor = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.manual_doctor, '+') ;
  END IF ;
  END IF ;
  /* manual_doctor end */
  IF (
    OLD.request_date <> NEW.request_date
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'REQUEST DATE', '+') ;
  SET @action = 'update' ;
  IF (OLD.request_date = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.request_date, '+') ;
  END IF ;
  IF (NEW.request_date = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.request_date, '+') ;
  END IF ;
  END IF ;
  /* request_date end */
  IF (OLD.encoder <> NEW.encoder && NEW.status != 'deleted') 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'ENCODER', '+') ;
  SET @action = 'update' ;
  IF (OLD.encoder = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.encoder, '+') ;
  END IF ;
  IF (NEW.encoder = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.encoder, '+') ;
  END IF ;
  END IF ;
  /* encoder end */
  IF (
    OLD.approved_by_head <> NEW.approved_by_head
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(
    @c_field,
    'PARENT BATCH NUMBER',
    '+'
  ) ;
  SET @action = 'update' ;
  IF (OLD.approved_by_head = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(
    @old_val,
    OLD.approved_by_head,
    '+'
  ) ;
  END IF ;
  IF (NEW.approved_by_head = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(
    @new_val,
    NEW.approved_by_head,
    '+'
  ) ;
  END IF ;
  END IF ;
  /* approved_by_head end */
  IF (OLD.remarks <> NEW.remarks) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'REMARKS', '+') ;
  SET @action = 'update' ;
  IF (OLD.remarks = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.remarks, '+') ;
  END IF ;
  IF (NEW.remarks = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.remarks, '+') ;
  END IF ;
  END IF ;
  /* remarks end */
  IF (OLD.headID <> NEW.headID && NEW.status != 'deleted') 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'HEAD ID', '+') ;
  SET @action = 'update' ;
  IF (OLD.headID = '' && NEW.status != 'deleted') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.headID, '+') ;
  END IF ;
  IF (NEW.headID = '' && NEW.status != 'deleted') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.headID, '+') ;
  END IF ;
  END IF ;
  /* headID end */
  IF (OLD.headpasswd <> NEW.headpasswd && NEW.status != 'deleted') 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'HEAD ID', '+') ;
  SET @action = 'update' ;
  IF (OLD.headpasswd = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.headpasswd, '+') ;
  END IF ;
  IF (NEW.headpasswd = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.headpasswd, '+') ;
  END IF ;
  END IF ;
  /* headpasswd end */
  IF (
    OLD.request_flag <> NEW.request_flag
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'REQUEST FLAG', '+') ;
  SET @action = 'update' ;
  IF (OLD.request_flag = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.request_flag, '+') ;
  END IF ;
  IF (NEW.request_flag = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.request_flag, '+') ;
  END IF ;
  END IF ;
  /* request_flag end */
  IF (
    OLD.cancel_reason <> NEW.cancel_reason
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'CANCEL REASON', '+') ;
  SET @action = 'update' ;
  IF (OLD.cancel_reason = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.cancel_reason, '+') ;
  END IF ;
  IF (NEW.cancel_reason = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.cancel_reason, '+') ;
  END IF ;
  END IF ;
  /* cancel_reason end */
  IF (OLD.or_number <> NEW.or_number) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'OR NUMBER', '+') ;
  SET @action = 'update' ;
  IF (OLD.or_number = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.or_number, '+') ;
  END IF ;
  IF (NEW.or_number = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.or_number, '+') ;
  END IF ;
  END IF ;
  /* or_number end */
  IF (OLD.is_served <> NEW.is_served) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'SERVED', '+') ;
  SET @action = 'update' ;
  IF (OLD.is_served = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.is_served, '+') ;
  END IF ;
  IF (NEW.is_served = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.is_served, '+') ;
  END IF ;
  END IF ;
  /* is_served end */
  IF (
    OLD.served_date <> NEW.served_date
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'SERVED DATE', '+') ;
  SET @action = 'update' ;
  IF (OLD.served_date = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.served_date, '+') ;
  END IF ;
  IF (NEW.served_date = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.served_date, '+') ;
  END IF ;
  END IF ;
  /* served_date end */
  IF (OLD.rad_tech <> NEW.rad_tech) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'RAD TECH', '+') ;
  SET @action = 'update' ;
  IF (OLD.rad_tech = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.rad_tech, '+') ;
  END IF ;
  IF (NEW.rad_tech = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.rad_tech, '+') ;
  END IF ;
  END IF ;
  /* rad_tech end */
  IF (
    OLD.is_in_outbox <> NEW.is_in_outbox
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'OUTBOX', '+') ;
  SET @action = 'update' ;
  IF (OLD.is_in_outbox = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.is_in_outbox, '+') ;
  END IF ;
  IF (NEW.is_in_outbox = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.is_in_outbox, '+') ;
  END IF ;
  END IF ;
  /* is_in_outbox end */
  IF (
    OLD.save_and_done <> NEW.save_and_done
  ) 
  THEN SET @CHECK = '1' ;
  SET @c_field = CONCAT(@c_field, 'SAVE AND DONE', '+') ;
  SET @action = 'update' ;
  IF (OLD.save_and_done = '') 
  THEN SET @old_val = CONCAT(@old_val, '[BLANK]', '+') ;
  ELSE SET @old_val = CONCAT(@old_val, OLD.save_and_done, '+') ;
  END IF ;
  IF (NEW.save_and_done = '') 
  THEN SET @new_val = CONCAT(@new_val, '[BLANK]', '+') ;
  ELSE SET @new_val = CONCAT(@new_val, NEW.save_and_done, '+') ;
  END IF ;
  END IF ;
  /* save_and_done end */
  IF (@CHECK <> '') 
  THEN 
  INSERT INTO seg_audit_trail (
    ID,
    date_changed,
    Action_type,
    login,
    table_name,
    field_c,
    new_value,
    old_value,
    pk_field,
    pk_value
  ) 
  VALUES
    (
      UUID(),
      NOW(),
      @action,
      @mod,
      'care_test_request_radio',
      @c_field,
      @new_val,
      @old_val,
      'refno',
      NEW.refno
    ) ;
  END IF ;
END;
$$

DELIMITER ;