-- copy from here --
ALTER TABLE `hisdb`.`seg_cert_med`   
  ADD COLUMN `civil_case_no` VARCHAR(100) NULL AFTER `dr_nr`,
  ADD COLUMN `court` VARCHAR(400) NULL AFTER `civil_case_no`,
  ADD COLUMN `judge` VARCHAR(200) NULL AFTER `court`;
--  up to here --