DELIMITER $$

USE `hisdb` $$

DROP FUNCTION IF EXISTS `fn_get_pay_account_type3` $$

CREATE DEFINER = `root` @`localhost` FUNCTION `fn_get_pay_account_type3` (
  refSource VARCHAR (5),
  refNo VARCHAR (15),
  itemCode VARCHAR (15),
  orNo VARCHAR (12)
) RETURNS VARCHAR (50) CHARSET latin1 READS SQL DATA 
BEGIN
  CASE
    refSource 
    WHEN 'PH' 
    THEN 
    BEGIN
      DECLARE isFS TINYINT ;
      DECLARE pharmaArea VARCHAR (10) ;
      SELECT 
        oi.is_fs INTO isFS 
      FROM
        seg_pharma_order_items oi 
      WHERE oi.refno = refNo 
        AND oi.bestellnum = itemCode ;
      SELECT 
        o.pharma_area INTO pharmaArea 
      FROM
        seg_pharma_orders o 
      WHERE o.refno = refNo ;
      IF isFS 
      THEN RETURN 'consign' ;
      ELSE 
      CASE
        pharmaArea 
        WHEN 'MG' 
        THEN RETURN 'drugs' ;
        ELSE RETURN 'drugs' ;
      END CASE ;
      END IF ;
    END ;
    WHEN 'LD' 
    THEN 
    BEGIN
      DECLARE groupCode VARCHAR (10) ;
      SELECT 
        IFNULL(ls.group_code, '') INTO groupCode 
      FROM
        seg_lab_services ls 
      WHERE ls.service_code = itemCode ;
      IF groupCode = 'B' 
      THEN RETURN 'blood' ;
      ELSEIF groupCode = 'SPC' 
      AND itemCode NOT LIKE "VEN%" 
      THEN RETURN 'ps' ;
      ELSEIF groupCode = 'SPC' 
      AND itemCode LIKE "VEN%" 
      THEN RETURN 'vent' ;
      ELSE RETURN 'ls' ;
      END IF ;
    END ;
    WHEN 'RD' 
    THEN 
    BEGIN
      DECLARE dept INT ;
      SELECT 
        rg.department_nr INTO dept 
      FROM
        seg_radio_services AS rs 
        INNER JOIN seg_radio_service_groups AS rg 
          ON rg.group_code = rs.group_code 
      WHERE rs.service_code = itemCode ;
      IF dept = '167' 
      THEN RETURN 'ctscan' ;
      ELSEIF dept = '208' 
      THEN RETURN 'mri' ;
      ELSE RETURN 'rs' ;
      END IF ;
    END ;
    WHEN 'FB' 
    THEN 
    BEGIN
      DECLARE wardType VARCHAR (20) ;
      DECLARE encounterType SMALLINT ;
      SELECT 
        e.encounter_type,
        w.prototype INTO encounterType,
        wardType 
      FROM
        seg_pay_request r 
        INNER JOIN seg_billing_encounter b 
          ON b.bill_nr = r.service_code 
        INNER JOIN care_encounter e 
          ON e.encounter_nr = b.encounter_nr 
        LEFT JOIN care_ward w 
          ON w.nr = e.current_ward_nr 
      WHERE r.or_no = orNo 
        AND r.service_code = itemCode 
      LIMIT 1 ;
      IF encounterType = 1 
      THEN RETURN 'hi' ;
      ELSE 
      CASE
        wardType 
        WHEN 'mhc' 
        THEN RETURN 'mhc' ;
        WHEN 'payward' 
        THEN RETURN 'payw' ;
        ELSE RETURN 'hi' ;
      END CASE ;
      END IF ;
    END ;
    WHEN 'PP' 
    THEN 
    BEGIN
      DECLARE wardType VARCHAR (20) ;
      SELECT 
        w.prototype INTO wardType 
      FROM
        seg_pay p 
        LEFT JOIN care_encounter e 
          ON e.encounter_nr = p.encounter_nr 
        LEFT JOIN care_ward w 
          ON w.nr = e.current_ward_nr 
      WHERE p.or_no = orNo ;
      IF itemCode = 'HOI' 
      THEN RETURN 'hi' ;
      ELSE 
      CASE
        wardType 
        WHEN 'mhc' 
        THEN RETURN 'mhc' ;
        WHEN 'payward' 
        THEN RETURN 'payw' ;
        ELSE RETURN 'hi' ;
      END CASE ;
      END IF ;
    END ;
    WHEN 'OTHER' 
    THEN 
    BEGIN
      DECLARE parseCode VARCHAR (12) ;
      DECLARE account VARCHAR (20) ;
      SET parseCode = SUBSTRING(itemCode, 1, LENGTH(itemCode) - 1) ;
      SELECT 
        st.pay_account INTO account 
      FROM
        seg_other_services AS os 
        INNER JOIN seg_cashier_account_subtypes AS st 
          ON os.account_type = st.type_id 
      WHERE os.service_code = parseCode ;
      RETURN IFNULL(account, 'hi') ;
    END ;
    WHEN 'MISC' 
    THEN 
    BEGIN
      DECLARE account VARCHAR (20) ;
      SELECT 
        st.pay_account INTO account 
      FROM
        seg_other_services AS os 
        INNER JOIN seg_cashier_account_subtypes AS st 
          ON os.account_type = st.type_id 
      WHERE os.alt_service_code = itemCode ;
      RETURN IFNULL(account, 'hi') ;
    END ;
    ELSE RETURN 'hi' ;
  END CASE ;
END $$

DELIMITER ;
