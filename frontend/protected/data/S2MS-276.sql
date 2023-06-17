ALTER TABLE `hisdb`.`seg_grant_account_type`   
  ADD COLUMN `with_budget` TINYINT DEFAULT 1  NULL AFTER `discount`,
  ADD COLUMN `modify_id` VARCHAR(35) NULL AFTER `date_modified`,
  ADD COLUMN `created_id` VARCHAR(35) NULL AFTER `modify_id`;

ALTER TABLE `hisdb`.`seg_grant_account_type`   
  CHANGE `date_created` `date_created` DATETIME NULL,
  CHANGE `date_modified` `date_modified` DATETIME NULL;