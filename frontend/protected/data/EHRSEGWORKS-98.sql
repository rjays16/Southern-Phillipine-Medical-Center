ALTER TABLE `care_pharma_outside_order` ADD COLUMN `route` TEXT NULL AFTER `brand_name`, ADD COLUMN `frequency` TEXT NULL AFTER `route`; 
CREATE TABLE `hisdb`.`seg_pharma_items_cf4`( `id` INT(5) NOT NULL AUTO_INCREMENT, `refno` VARCHAR(10), `bestellnum` VARCHAR(25), `route` TEXT, `frequency` TEXT, PRIMARY KEY (`id`) ) ENGINE=INNODB CHARSET=latin1 COLLATE=latin1_swedish_ci;
ALTER TABLE `hisdb`.`seg_eclaims_cf4` CHANGE `xml` `xml` LONGBLOB NOT NULL;
