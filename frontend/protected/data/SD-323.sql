INSERT INTO `hisdb`.`seg_type_charge_pharma` (
  `id`,
  `charge_name`,
  `description`,
  `ordering`,
  `is_excludedfrombilling`,
  `in_pharmacy`
) 
VALUES
  (
    'LPM_PCSO',
    'LPM/PCSO',
    'w/ LPM/PCSO',
    '17',
    '0',
    '1'
  ) ;

INSERT INTO `hisdb`.`seg_type_charge_pharma` (
  `id`,
  `charge_name`,
  `description`,
  `ordering`,
  `is_excludedfrombilling`,
  `in_pharmacy`
) 
VALUES
  (
    'PHS_APPROVED',
    'PHS APPROVED',
    'w/ PHS APPROVED',
    '18',
    '0',
    '1'
  );
  
  ALTER TABLE `hisdb`.`seg_type_charge_pharma`   
  ADD COLUMN `for_walkin` TINYINT(1) DEFAULT 0 AFTER `in_pharmacy`;
  
  UPDATE seg_type_charge_pharma SET for_walkin = 1 WHERE ordering IN(15, 13, 12, 14, 16, 17,18,4);
