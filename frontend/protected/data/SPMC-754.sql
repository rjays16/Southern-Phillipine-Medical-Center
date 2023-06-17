-- query starts here --
DELIMITER $$

USE `hisdb`$$

DROP FUNCTION IF EXISTS `fn_get_account_type2`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_account_type2`(s_ref_no VARCHAR(12), s_ref_src VARCHAR(5), s_code VARCHAR(12), s_ret_type ENUM('N','S')) RETURNS VARCHAR(50) CHARSET latin1
    DETERMINISTIC
BEGIN
	DECLARE ti TINYINT;
	DECLARE v VARCHAR(50);
	DECLARE fs TINYINT;
	DECLARE i INT;
  IF s_ref_src = 'PH' THEN
		SELECT oi.is_consigned,oi.is_fs INTO ti,fs FROM seg_pharma_order_items AS oi WHERE oi.refno=s_ref_no AND oi.bestellnum=s_code;
		SELECT o.pharma_area INTO v FROM seg_pharma_orders AS o WHERE o.refno=s_ref_no;
		IF ti=1 THEN
			RETURN IF(s_ret_type='S','CMeds|CMeds',7);
		ELSEIF fs=1 THEN
			RETURN IF(s_ret_type='S','CMeds|CMeds',7);
		ELSE
			IF v='MG' THEN
				RETURN IF(s_ret_type='S','Drugs|Drugs',13);
			ELSE
				RETURN IF(s_ret_type='S','Drugs|Drugs',5);
			END IF;
		END IF;
	ELSEIF s_ref_src = 'LD' THEN
		SELECT IFNULL(ls.group_code,'') INTO v FROM seg_lab_services AS ls WHERE ls.service_code=s_code;
		IF v='B' THEN
			RETURN IF(s_ret_type='S','BC|Lab',9);
		ELSE
			RETURN IF(s_ret_type='S','HOI|Lab',1);
		END IF;
	ELSEIF s_ref_src = 'RD' THEN
		SELECT rg.department_nr INTO i FROM seg_radio_services AS rs 
			LEFT JOIN seg_radio_service_groups AS rg ON rg.group_code=rs.group_code
			WHERE rs.service_code=s_code;
		IF i=167 THEN
			RETURN IF(s_ret_type='S','CT Scan',10);
		ELSE RETURN IF(s_ret_type='S','HOI|Rad',1);
		END IF;
	ELSEIF s_ref_src = 'FB' THEN
		RETURN IF(s_ret_type='S','Pay|Hospital Bill',4);
	ELSEIF s_ref_src = 'PP' THEN
		IF (s_code='DEPOSIT')  THEN RETURN IF(s_ret_type='S','Pay|Deposit',4);
		ELSEIF (s_code='PARTIAL')  THEN RETURN IF(s_ret_type='S','Pay|Partial',4);
		ELSE RETURN IF(s_ret_type='S','Pay|Deposit',4);
		END IF;
	ELSEIF s_ref_src = 'OTHER' THEN
		SET v = SUBSTRING(s_code,1,LENGTH(s_code)-1);
		SET i=NULL;
    
		SELECT CONCAT(IFNULL(t.name_short,'No account'),'|',os.name_short),t.type_id INTO v,i
			FROM seg_other_services AS os
			INNER JOIN seg_cashier_account_subtypes AS st ON os.account_type=st.type_id
			INNER JOIN seg_cashier_account_types AS t ON st.parent_type=t.type_id
			WHERE os.service_code=v;
		RETURN IF(s_ret_type='S',v,IFNULL(i,1));
	ELSE
		RETURN NULL;
	END IF;
END$$

DELIMITER ;
-- ends here --