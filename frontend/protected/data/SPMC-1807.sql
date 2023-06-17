DELIMITER $$

USE `hisdb`$$

DROP FUNCTION IF EXISTS `fn_get_age_wrist`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_age_wrist`(cur_dte DATE, prev_dte DATE) RETURNS VARCHAR(25) CHARSET latin1
    DETERMINISTIC
BEGIN
	DECLARE n_yr INT(11);
	DECLARE n_mon INT(4);
	DECLARE n_day INT(4);	
	
	
	
	SELECT TIMESTAMPDIFF( YEAR, prev_dte, cur_dte ) INTO n_yr;
	SELECT TIMESTAMPDIFF( MONTH, prev_dte, cur_dte ) % 12 INTO n_mon;
        SELECT FLOOR( TIMESTAMPDIFF( DAY, prev_dte, cur_dte ) % 30.5375 ) INTO n_day;
	
	IF n_yr THEN
		RETURN CONCAT(n_yr, ' y/o');
	ELSEIF n_mon THEN
		RETURN CONCAT(n_mon, ' m/o');
	ELSE
		RETURN CONCAT(n_day, ' d/o');
	END IF;
	
    END$$

DELIMITER ;