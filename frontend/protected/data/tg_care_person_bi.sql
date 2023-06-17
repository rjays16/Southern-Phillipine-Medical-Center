DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_care_person_bi`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_care_person_bi` BEFORE INSERT ON `care_person` 
    FOR EACH ROW BEGIN
  /*added by kemps 04/27/201818*/
  IF EXISTS 
  (SELECT 
    * 
  FROM
    care_person AS cp 
  WHERE UPPER(cp.name_first) = UPPER(new.name_first) 
    AND UPPER(cp.name_middle) = UPPER(new.name_middle) 
    AND UPPER(cp.name_last) = UPPER(new.name_last) 
    AND cp.date_birth = new.date_birth) 
  THEN CALL Fail ('Already Exists') ;
  ELSE SET new.name_search = CONCAT(
    new.name_last,
    ', ',
    new.name_first
  ) ;
  SET new.soundex_namelast = SOUNDEX(new.name_last) ;
  SET new.soundex_namefirst = SOUNDEX(new.name_first) ;
  END IF ;
  /* end kemps*/
END;
$$

DELIMITER ;