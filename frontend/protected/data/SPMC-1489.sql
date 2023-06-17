ALTER TABLE `seg_encounter_privy_dr` ADD COLUMN `caserate` INT(10) DEFAULT 0 NOT NULL AFTER `days_attended`; 
CREATE TABLE `hisdb`.`seg_billing_pf_breakdown`( `bill_nr` VARCHAR(13) NOT NULL, `hcare_id` INT(8) UNSIGNED NOT NULL, `dr_nr` INT(11) NOT NULL, `role_area` ENUM('D1','D2','D3','D4') NOT NULL, `dr_claim` DECIMAL(20,4), `first_claim` DECIMAL(20,4), `second_claim` DECIMAL(20,4), PRIMARY KEY (`bill_nr`, `hcare_id`, `dr_nr`, `role_area`) ); 