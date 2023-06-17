INSERT INTO `hisdb`.`seg_rep_params` (
  `param_id`,
  `parameter`,
  `param_type`,
  `choices`
) 
VALUES
  (
    "er_sort_name",
    "Sort by Patient's Name",
    "option",
    "'1-Yes','0-No'"
  ) ;
    
UPDATE `hisdb`.`seg_rep_params` SET `ordering` = '1' WHERE `param_id` = 'department';
UPDATE `hisdb`.`seg_rep_params` SET `ordering` = '2' WHERE `param_id` = 'er_sort_name';

INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('ER_Daily_Transactions', 'er_sort_name');