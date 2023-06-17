ALTER TABLE `hisdb`.`seg_signatory`   
  ADD COLUMN `is_active` TINYINT(1) DEFAULT 1  NULL AFTER `is_default`;

INSERT INTO `hisdb`.`care_config_global` (`type`, `value`, `create_time`) 
VALUES
  (
    'new_sig_medrec_effec',
    '2019-06-13 14:00:00',
    '2019-06-13 16:32:36'
  );

INSERT INTO `hisdb`.`seg_signatory` (
  `personell_nr`,
  `signatory_position`,
  `signatory_title`,
  `document_code`,
  `title`,
  `is_default`,
  `is_active`
) 
VALUES
  (
    '102691',
    'ADMINISTRATIVE OFFICER V',
    'OIC. HEALTH INFORMATION MGT. DEPT.',
    'confcert',
    'RN, MAN',
    1,
    1
  );

INSERT INTO `hisdb`.`seg_signatory` (
  `personell_nr`,
  `signatory_position`,
  `signatory_title`,
  `document_code`,
  `title`,
  `is_default`,
  `is_active`
) 
VALUES
  (
    '102691',
    'ADMINISTRATIVE OFFICER V',
    'OIC. HEALTH INFORMATION MGT. DEPT.',
    'doa',
    'RN, MAN',
    '1',
    '1'
);

UPDATE `hisdb`.`seg_signatory` SET `is_active` = '0' WHERE `personell_nr` = '100349' AND `document_code` = 'doa';
UPDATE `hisdb`.`seg_signatory` SET `is_active` = '0' WHERE `personell_nr` = '100349' AND `document_code` = 'confcert';
UPDATE `hisdb`.`seg_signatory` SET `is_default` = '1' WHERE `personell_nr` = '100349' AND `document_code` = 'confcert';

-- UPDATE TRIGGER fn_get_personell_name2 --

DELIMITER $$

USE `hisdb`$$

DROP FUNCTION IF EXISTS `fn_get_personell_name2`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_personell_name2`(personell_nr VARCHAR(50)) RETURNS VARCHAR(100) CHARSET latin1
    DETERMINISTIC
BEGIN
	DECLARE personell_name VARCHAR(100);
	SET personell_name := (SELECT CONCAT(TRIM(cp_2.name_first),' ', 
		IF(TRIM(cp_2.name_last) LIKE '%-%','',CONCAT(LEFT(TRIM(cp_2.name_middle),1),'. ')), 
		TRIM(cp_2.name_last)) AS fullname
		FROM care_personell AS cpl_2, care_person AS cp_2
		WHERE cpl_2.nr = personell_nr AND cp_2.pid=cpl_2.pid);
	RETURN (personell_name);
END$$

DELIMITER ;

-- END TRIGGER fn_get_personell_name2 --