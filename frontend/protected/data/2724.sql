/*[4:17:40 AM][138 ms]*/ CREATE TABLE `hisdb`.`seg_encounter_vital_sign_bmi`( `id` VARCHAR(36) NOT NULL, `pid` VARCHAR(12) NOT NULL, `encounter_nr` VARCHAR(12) NOT NULL, `bmi_date` DATETIME NOT NULL, `weight` INT(11) DEFAULT 0, `height` INT(11) DEFAULT 0, `hip_line` INT(11) DEFAULT 0, `waist_line` INT(11) DEFAULT 0, `abdominal_girth` INT(11) DEFAULT 0, `history` TEXT, `create_id` TEXT, `create_dt` DATETIME NOT NULL, `modify_id` TEXT, `modify_dt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `is_deleted` TINYINT(1) DEFAULT 0, PRIMARY KEY (`id`) ) CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*[4:10:09 AM][64596 ms]*/ ALTER TABLE `hisdb`.`care_encounter_notes` ADD COLUMN `is_vital` TINYINT(1) DEFAULT 0 NULL AFTER `nBmi`;
/*[8:18:41 AM][93 ms]*/ ALTER TABLE `hisdb`.`seg_encounter_vital_sign_bmi` CHANGE `weight` `weight` DOUBLE(15,2) NULL, CHANGE `height` `height` DOUBLE(15,2) NULL, CHANGE `hip_line` `hip_line` DOUBLE(15,2) NULL, CHANGE `waist_line` `waist_line` DOUBLE(15,2) NULL, CHANGE `abdominal_girth` `abdominal_girth` DOUBLE(15,2) NULL;