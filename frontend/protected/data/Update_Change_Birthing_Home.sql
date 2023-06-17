/*Code below is for updating the table to add a column for status:*/


ALTER TABLE `hisdb`.`seg_opd_or_temp` ADD COLUMN `status` ENUM('active','deleted') NULL AFTER `create_id`;

UPDATE `hisdb`.`seg_opd_or_temp` SET `status` = 'active' WHERE `or_id` = '1';
UPDATE `hisdb`.`seg_opd_or_temp` SET `status` = 'active' WHERE `or_id` = '2';
UPDATE `hisdb`.`seg_opd_or_temp` SET `status` = 'active' WHERE `or_id` = '3';
UPDATE `hisdb`.`seg_opd_or_temp` SET `status` = 'active' WHERE `or_id` = '4';
UPDATE `hisdb`.`seg_opd_or_temp` SET `status` = 'active' WHERE `or_id` = '8';
UPDATE `hisdb`.`seg_opd_or_temp` SET `status` = 'active' WHERE `or_id` = '7';
UPDATE `hisdb`.`seg_opd_or_temp` SET `status` = 'active' WHERE `or_id` = '6';
UPDATE `hisdb`.`seg_opd_or_temp` SET `status` = 'active' WHERE `or_id` = '5';
UPDATE `hisdb`.`seg_opd_or_temp` SET `status` = 'deleted' WHERE `or_id` = '9';
UPDATE `hisdb`.`seg_opd_or_temp` SET `status` = 'active' WHERE `or_id` = '10'; 



/* Code below is for updating all of official reciept number to number instead of string values:
( WARNING! RISKY CODE! ) */

UPDATE `care_encounter` AS ce SET ce.`official_receipt_nr` = '1' WHERE ce.`official_receipt_nr` = 'WCPU';

UPDATE `care_encounter` AS ce SET ce.`official_receipt_nr` = '2' WHERE ce.`official_receipt_nr` = 'BJMP';

UPDATE `care_encounter` AS ce SET ce.`official_receipt_nr` = '3' WHERE ce.`official_receipt_nr` = 'DMH';

UPDATE `care_encounter` AS ce SET ce.`official_receipt_nr` = '4' WHERE ce.`official_receipt_nr` = 'AMBULATORY SURGERY';

UPDATE `care_encounter` AS ce SET ce.`official_receipt_nr` = '5' WHERE ce.`official_receipt_nr` = 'CMAP';

UPDATE `care_encounter` AS ce SET ce.`official_receipt_nr` = '6' WHERE ce.`official_receipt_nr` = 'PREPAID';

UPDATE `care_encounter` AS ce SET ce.`official_receipt_nr` = '7' WHERE ce.`official_receipt_nr` = 'Z-PACKAGE';

UPDATE `care_encounter` AS ce SET ce.`official_receipt_nr` = '8' WHERE ce.`official_receipt_nr` = 'NOT SERVED';

UPDATE `care_encounter` AS ce SET ce.`official_receipt_nr` = '9' WHERE ce.`official_receipt_nr` = 'Birthing Home (BH)';

UPDATE `care_encounter` AS ce SET ce.`official_receipt_nr` = '10' WHERE ce.`official_receipt_nr` = 'ABTC-PHIC'; 