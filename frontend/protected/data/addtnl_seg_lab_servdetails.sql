ALTER TABLE `hisdb`.`seg_lab_servdetails` ADD COLUMN `create_id` VARCHAR(35) NULL AFTER `history`


ALTER TABLE `hisdb`.`seg_lab_servdetails` ADD COLUMN `create_dt` DATETIME DEFAULT CURRENT_TIMESTAMP NULL AFTER `create_id`

ALTER TABLE `hisdb`.`seg_lab_servdetails` ADD COLUMN `modify_id` VARCHAR(35) NULL AFTER `create_dt`

ALTER TABLE `hisdb`.`seg_lab_servdetails` ADD COLUMN `modify_dt` DATETIME DEFAULT CURRENT_TIMESTAMP NULL AFTER `modify_id`