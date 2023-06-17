INSERT INTO `hisdb`.`seg_rep_params` (
  `param_id`,
  `parameter`,
  `param_type`,
  `choices`
) 
VALUES
  (
    'claim_status',
    'Transmittal Status',
    'option',
    '\'all-All\',\'mapped-Mapped\',\'notuploaded-Not Uploaded\''
  ) ;

INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('Billing_PHIC_Claims_Transmitted', 'claim_status');
