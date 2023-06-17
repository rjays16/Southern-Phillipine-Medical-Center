ALTER TABLE `hisdb`.`seg_areas_duration_time` ADD COLUMN `reason` VARCHAR(255) NULL AFTER `create_dt`;
CREATE TABLE `hisdb`.`care_lock_access_reason`( `id` VARCHAR(12), `description` VARCHAR(255) );
INSERT INTO `hisdb`.`care_lock_access_reason` (`id`, `description`) VALUES ('RES', 'Resignation'); 
INSERT INTO `hisdb`.`care_lock_access_reason` (`id`, `description`) VALUES ('TER', 'Terminal Leave'); 
INSERT INTO `hisdb`.`care_lock_access_reason` (`id`, `description`) VALUES ('NEW', 'New Assignment'); 
INSERT INTO `hisdb`.`care_lock_access_reason` (`id`, `description`) VALUES ('INA', 'Inactive Employee'); 
INSERT INTO `hisdb`.`care_lock_access_reason` (`id`, `description`) VALUES ('OT', 'Others');
