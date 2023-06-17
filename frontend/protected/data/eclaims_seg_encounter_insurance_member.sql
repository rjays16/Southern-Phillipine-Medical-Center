ALTER TABLE `hisdb`.`seg_encounter_insurance_memberinfo` ADD COLUMN `patient_pin` VARCHAR(25) DEFAULT '000000000000' NULL AFTER `parent_pid`; 
