ALTER TABLE `hisdb`.`seg_affidavit_father_surename` ADD COLUMN `child_relationship` VARCHAR(255) NULL AFTER `is_other`, ADD COLUMN `child_fullname` VARCHAR(255) NULL AFTER `child_relationship`

ALTER TABLE `hisdb`.`seg_affidavit_father_surename` CHANGE `child_relationship` `child_relationship` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL AFTER `affiant_address`, CHANGE `child_fullname` `child_fullname` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL AFTER `child_relationship`

ALTER TABLE `hisdb`.`seg_affidavit_father_surename` ADD COLUMN `father_surename` VARCHAR(255) NULL AFTER `is_other`

ALTER TABLE `hisdb`.`seg_affidavit_father_surename` CHANGE `father_surename` `father_surename` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL AFTER `affiant_address`