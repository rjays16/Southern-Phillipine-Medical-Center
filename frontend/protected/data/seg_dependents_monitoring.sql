-- Added by column actiod_id by Matsuu for audit trial in PHS-Report
ALTER TABLE `hisdb`.`seg_dependents_monitoring` 
  ADD COLUMN `action_id` VARCHAR (100) NULL AFTER `action_date` ;
-- Ended here...
