-- Created PHS_code and PHS_type by Matsuu
INSERT INTO `hisdb`.`seg_rep_params` (
  `param_id`,
  `parameter`,
  `param_type`,
  `choices`
) 
VALUES
  (
    'PHS_type',
    'PHS Type',
    'option',
    '\'all-All\', \'employee-Employee\', \'dependent-Dependent\''
  ) ;

  INSERT INTO `hisdb`.`seg_rep_params` (
  `param_id`,
  `parameter`,
  `param_type`,
  `choices`
) 
VALUES
  (
    'PHS_code',
    'Encoder',
    'sql',
    '(SELECT \r\n  \'all\' AS id,\r\n  \'All\' AS namedesc) \r\nUNION\r\nALL \r\n(SELECT DISTINCT \r\n  sat.`login` AS id,\r\n  cu.name AS namedesc \r\nFROM\r\n  seg_audit_trail AS sat \r\n  INNER JOIN care_users AS cu \r\n    ON cu.login_id = sat.login\r\nWHERE sat.`table_name` = \'care_personell\' \r\n  AND sat.`login` IS NOT NULL GROUP BY namedesc)\r\n  \r\n'
  ) ;
-- Ended here...
-- Added by Matsuu  for Radiology
INSERT INTO seg_rep_params (
  `param_id`,
  `parameter`,
  `param_type`,
  `choices`
) 
VALUES
  (
    'rad_chargetype',
    'Charge Type',
    'sql',
    'SELECT \r\n  id AS id,\r\n  charge_name AS namedesc \r\nFROM\r\n  seg_type_charge \r\nORDER BY charge_name '
  ) ;
-- Ended here...
--Created parameter for Billing Categories - SPMC-1095
 INSERT INTO `seg_rep_params` (`param_id`, `variable`, `parameter`, `param_type`, `choices`, `is_active`, `ordering`) VALUES ('billing_categories', NULL, 'Categories', 'option', '\'OBMAIN-OB MAIN\',\'OBPOC-OB POC\',\'RM-REGULAR MAIN\',\'RP-REGULAR POC\',\'POS-POS\'', '1', '0'); 
-- Ended here






