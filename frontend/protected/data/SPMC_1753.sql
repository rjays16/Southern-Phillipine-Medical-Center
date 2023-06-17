INSERT INTO `care_config_global` (`type`, `value`, `notes`, `status`, `history`, `modify_id`, `modify_time`, `create_id`, `create_time`) VALUES ('limit_access_permission_bb', '190', NULL, '', '', '', '2017-07-13 05:02:41', '', '0000-00-00 00:00:00'); 

UPDATE `hisdb`.`seg_rep_templates_registry` SET `is_active` = '0' WHERE `report_id` = 'bb_stat_summary'; 
UPDATE `hisdb`.`seg_rep_templates_registry` SET `is_active` = '0' WHERE `report_id` = 'bb_utilization_component_source_type'; 
UPDATE `hisdb`.`seg_rep_templates_registry` SET `is_active` = '0' WHERE `report_id` = 'bb_stat_worksheet'; 
UPDATE `hisdb`.`seg_rep_templates_registry` SET `is_active` = '0' WHERE `report_id` = 'bb_utilization_component_type'; 

INSERT INTO `hisdb`.`seg_rep_template_params` (`report_id`, `param_id`) VALUES ('bb_daily_transac_monitoring', 'bb_encoder');


CREATE TABLE `hisdb`.`seg_blood_monitoring` (
  `pid` VARCHAR (12) NOT NULL,
  `refno` VARCHAR (12) NOT NULL,
  `service_code` VARCHAR (100) NOT NULL,
  `blood_type` VARCHAR (12),
  `component` VARCHAR (20),
  `ordered_qty` DECIMAL (2, 0),
  `serial_no` VARCHAR (100),
  `status` ENUM ('complete', 'lack', 'none'),
  `date_created` DATE,
  `date_released` DATETIME,
  `date_received` DATETIME,
  `date_started` DATETIME,
  `date_done` DATETIME,
  `date_issuance` DATETIME,
  `date_return` DATETIME,
  `date_reissue` DATETIME,
  `date_consumed` DATETIME,
  `create_id` VARCHAR (60),
  `create_dt` DATETIME,
  `modify_id` VARCHAR (60),
  `modify_dt` DATETIME,
  PRIMARY KEY (`pid`, `refno`, `service_code`)
) ;


UPDATE
  `hisdb`.`seg_rep_params`
SET
  `param_type` = 'sql',
  `choices` = '(SELECT \r\n  \'all\' AS id,\r\n  \'All\' AS namedesc) \r\nUNION\r\n(SELECT \r\n  a.personell_nr AS id,\r\n  fn_get_personellname_lastfirstmi (a.personell_nr) AS namedesc\r\nFROM\r\n  care_personell_assignment AS a,\r\n  care_personell AS ps,\r\n  care_person AS p \r\nWHERE (ps.short_id LIKE \'G%\') \r\n  AND a.location_nr = 190 \r\n  AND (\r\n    a.date_end = \'0000-00-00\' \r\n    OR a.date_end >= DATE(NOW())\r\n  ) \r\n  AND a.STATUS NOT IN (\r\n    \'deleted\',\r\n    \'hidden\',\r\n    \'inactive\',\r\n    \'void\'\r\n  ) \r\n  AND a.personell_nr = ps.nr \r\n  AND ps.pid = p.pid) ORDER BY namedesc;'
WHERE `param_id` = 'bb_encoder_opcr' ;


UPDATE `hisdb`.`seg_rep_template_params` SET `param_id` = 'bb_encoder_opcr' WHERE `report_id` = 'bb_daily_transac_monitoring' AND `param_id` = 'bb_encoder';
UPDATE `hisdb`.`seg_rep_params` SET `choices` = '(SELECT \r\n  \'all\' AS id,\r\n  \'All\' AS namedesc, \'aaaa\' AS lastname) \r\nUNION\r\n(SELECT \r\n  a.personell_nr AS id,\r\n  fn_get_personellname_lastfirstmi (a.personell_nr) AS namedesc,\r\n  p.name_last AS ss\r\nFROM\r\n  care_personell_assignment AS a,\r\n  care_personell AS ps,\r\n  care_person AS p \r\nWHERE (ps.short_id LIKE \'G%\') \r\n  AND a.location_nr = 190 \r\n  AND (\r\n    a.date_end = \'0000-00-00\' \r\n    OR a.date_end >= DATE(NOW())\r\n  ) \r\n  AND a.STATUS NOT IN (\r\n    \'deleted\',\r\n    \'hidden\',\r\n    \'inactive\',\r\n    \'void\'\r\n  ) \r\n  AND a.personell_nr = ps.nr \r\n  AND ps.pid = p.pid) ORDER BY lastname;' WHERE `param_id` = 'bb_encoder_opcr';