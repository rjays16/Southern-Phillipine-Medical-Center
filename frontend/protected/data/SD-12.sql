ALTER TABLE `hisdb`.`seg_prescription_template_items`   
  ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0  NULL AFTER `frequency_time`;
