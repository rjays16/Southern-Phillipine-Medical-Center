 ALTER TABLE `hisdb`.`seg_dependents_monitoring` ADD COLUMN `parent_expired` SMALLINT DEFAULT 0 NULL AFTER `action_id`; 
 ALTER TABLE `hisdb`.`seg_dependents` ADD COLUMN `parent_expired` SMALLINT DEFAULT 0 NULL AFTER `create_dt`; 
 ALTER TABLE `hisdb`.`seg_encounter_result` ADD COLUMN `frombilling` TINYINT(1) DEFAULT 0 NULL COMMENT 'flag as dead from billing' AFTER `create_time`; 
