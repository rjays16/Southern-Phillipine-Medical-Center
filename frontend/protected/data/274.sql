/*[12:52:05 AM][114 ms]*/ CREATE TABLE `hisdb`.`care_effective` ( `id` TINYINT (5) NOT NULL AUTO_INCREMENT, `value` TEXT, `start_date` DATETIME, `end_date` DATETIME, `create_id` VARCHAR (50), `create_dt` DATETIME, `modify_id` VARCHAR (50), `modify_dt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, `is_deleted` TINYINT (1) DEFAULT 0, PRIMARY KEY (`id`) ) ENGINE = INNODB CHARSET = latin1 COLLATE = latin1_swedish_ci;
/*[12:52:34 AM][58 ms]*/ INSERT INTO `hisdb`.`care_effective` (`value`, `start_date`, `create_id`, `create_dt`) VALUES ('new_circular', '2020-03-30 00:00:00', 'medocs', '2020-03-25 00:52:31');
/*[11:32:50 PM][90 ms]*/ INSERT INTO `hisdb`.`care_config_global` (`type`, `value`, `modify_time`, `create_id`, `create_time`) VALUES ('days_allowed', '60', '2020-03-27 23:32:39', 'medocs', '2020-03-27 23:32:45');
/*[11:33:15 PM][62 ms]*/ INSERT INTO `hisdb`.`care_config_global` (`type`, `value`, `modify_time`, `create_time`) VALUES ('new_days_allowed', '120', '2020-03-27 23:33:07', '2020-03-27 23:33:10');