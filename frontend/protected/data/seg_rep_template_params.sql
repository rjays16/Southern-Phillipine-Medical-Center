-- Created by Matsuu 
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) 
VALUES
  ('PHS_summary', 'PHS_type') ;
INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) 
VALUES 
  ('PHS_summary', 'PHS_code'); 
-- Ended here...
-- Added by Matsuu 
INSERT INTO seg_rep_template_params (`report_id`, `param_id`) 
VALUES
  (
    'Served_Charge_Rad',
    'rad_chargetype'
  ) ;
-- Ended here...

--Created By Matsuu for SPMC-1095
INSERT INTO `seg_rep_template_params` (`report_id`, `param_id`) 
VALUES
  (
    'Billing_Received_CF1_Summary_Report',
    'billing_categories'
  ) ;
--Ended here..

