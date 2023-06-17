-- Created by Matsuu PHS_summary
INSERT INTO `hisdb`.`seg_rep_templates_registry` (
  `report_id`,
  `rep_group`,
  `rep_name`,
  `rep_description`,
  `rep_script`,
  `rep_dept_nr`,
  `rep_category`,
  `with_template`,
  `template_name`
) 
VALUES
  (
    'PHS_summary',
    'Monitoring',
    'PHS Encoded Census Summary',
    'PHS Encoded Cencus Summary',
    'PHS_summary',
    '133',
    'HOSP',
    '1',
    'PHS_summary'
  ) ;
 -- Ended here...
-- Added by Matsuu  for Radiology Report  for NTP
INSERT INTO seg_rep_templates_registry(
  `report_id`,
  `rep_group`,
  `rep_name`,
  `rep_description`,
  `rep_script`,
  `rep_dept_nr`,
  `rep_category`,
  `with_template`,
  `template_name`
) 
VALUES
  (
    'Served_Charge_Rad',
    'Hospital Operations',
    'Served Charge-type Requests Report',
    'Served Charge-type Request Report',
    'Served_Charge_Rad',
    '158',
    'HOSP',
    '1',
    'Served_Charge_Rad'
  ) ;
  -- Ended here..
  

