INSERT INTO `hisdb`.`care_config_global` (`type`, `value`, `history`, `modify_time`, `create_id`, `create_time`) VALUES ('repetitive_multiplier', '90935,90945,36430,77761,77776,77781,77789,96408,77401,77418', '', '2019-07-18 11:33:27', 'medocs', '2019-07-18 11:33:34');
DELETE FROM `hisdb`.`seg_caserate_acr` WHERE `package_id` = '77401' AND `acr_groupid` = 'CR0392';
UPDATE `hisdb`.`seg_case_rate_packages` SET `date_to` = '2019-06-17' WHERE `package_id` = '8830';
UPDATE `hisdb`.`seg_case_rate_packages` SET `date_from` = '2019-06-17' WHERE `package_id` = '8829';