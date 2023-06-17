CREATE TABLE `hisdb`.`claim_status_catalog`( `id` INT(11) NOT NULL AUTO_INCREMENT, `status_name` VARCHAR(50), `is_deleted` INT(1) DEFAULT 0, `created_at` TIMESTAMP, `updated_at` TIMESTAMP, `created_by` VARCHAR(50), PRIMARY KEY (`id`) );
INSERT INTO `hisdb`.`claim_status_catalog` (`status_name`, `created_by`) VALUES ('PENDING', '');
INSERT INTO `hisdb`.`claim_status_catalog` (`status_name`, `created_by`) VALUES ('IN PROCESS', '');
INSERT INTO `hisdb`.`claim_status_catalog` (`status_name`, `created_by`) VALUES ('RETURN', '');
INSERT INTO `hisdb`.`claim_status_catalog` (`status_name`, `created_by`) VALUES ('DENIED', ''); 
INSERT INTO `hisdb`.`claim_status_catalog` (`status_name`, `created_by`) VALUES ('WITH VOUCHER', '');
INSERT INTO `hisdb`.`claim_status_catalog` (`status_name`, `created_by`) VALUES ('VOUCHERING', ''); 
INSERT INTO `hisdb`.`claim_status_catalog` (`status_name`, `created_by`) VALUES ('WITH CHEQUE', '');
INSERT INTO `hisdb`.`claim_status_catalog` (`status_name`, `created_by`) VALUES ('CLAIM SERIES NOT FOUND', '');
