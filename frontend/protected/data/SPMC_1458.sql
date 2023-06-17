DELIMITER $$

USE `hisdb`$$

DROP FUNCTION IF EXISTS `fn_get_mode_of_discharge`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_mode_of_discharge`(cur_dt DATE, enc_date DATE) RETURNS VARCHAR(25) CHARSET latin1
    DETERMINISTIC
BEGIN
  DECLARE date_day INT (4) ;
  DECLARE str_day VARCHAR (25) ;
  DECLARE mode_of_discharge VARCHAR (25) ;
  DECLARE s VARCHAR (25) ;
  
  
  SET str_day := 
  (SELECT 
    '') ;
    IF (YEAR(cur_dt)<YEAR(enc_date))THEN
    SET date_day := DATEDIFF(cur_dt, enc_date)* (-1);
    ELSE
    SET date_day := DATEDIFF(cur_dt, enc_date) ;
    END IF;
  
  SET str_day := date_day ;
 
  IF (date_day >= 1 
    AND date_day <= 14) 
  THEN SET mode_of_discharge = "CIU" ;
  ELSEIF (date_day >= 15 
    AND date_day <= 89) 
  THEN SET mode_of_discharge = "ACUTE" ;
  ELSEIF (date_day >= 90 AND date_day < 3650) 
  THEN SET mode_of_discharge = "CHRONIC" ;
  ELSEIF(date_day>=3650)
  THEN SET mode_of_discharge = "CUSTODIAL";
  END IF ;
  RETURN (mode_of_discharge) ;
END$$

DELIMITER ;