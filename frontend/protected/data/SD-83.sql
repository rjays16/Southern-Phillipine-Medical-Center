INSERT INTO `hisdb`.`seg_rep_params` (
  `param_id`,
  `variable`,
  `parameter`,
  `param_type`,
  `choices`,
  `is_active`,
  `ordering`
) 
VALUES
  (
    'bb_donor_unit',
    NULL,
    'Donor Unit',
    'option',
    '\'all-All\',\'bdu-Blood Donor Unit\',\'pu-Pooled Unit\'',
    '1',
    '5'
  );

INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) 
VALUES
  (
    'BB_Daily_Report_Deposited',
    'bb_donor_unit'
  );