insert into `seg_pay_accounts` (`id`, `short_name`, `formal_name`, `description`, `priority`) values ('rs', 'Radiology', 'Radiology Service', NULL, '60');
insert into `seg_pay_accounts` (`id`, `short_name`, `formal_name`, `description`, `priority`) values ('ls', 'Laboratory', 'Laboratory Service', NULL, '70');
insert into `seg_pay_accounts` (`id`, `short_name`, `formal_name`, `description`, `priority`) values ('ps', 'Pulmonary', 'Pulmonary Service', NULL, '80');
insert into `seg_pay_accounts` (`id`, `short_name`, `formal_name`, `description`, `priority`) values ('si', 'Service', 'Service Income', NULL, '90');
insert into `seg_pay_accounts` (`id`, `short_name`, `formal_name`, `description`, `priority`) values ('bc', 'Blood', 'Blood Center', NULL, '100');
INSERT INTO `seg_pay_accounts` (`id`, `short_name`, `formal_name`, `priority`) VALUES ('mhc', 'MHC', 'Heart Center', '110'); 
update `seg_pay_subaccounts` set `parent_account` = 'bc' where `id` = 'blood';
UPDATE `seg_pay_subaccounts` SET `parent_account` = 'mhc' WHERE `id` = 'mhc'; 
insert into `seg_pay_subaccounts` (`id`, `parent_account`, `short_name`, `formal_name`, `description`, `priority`) values ('rs', 'rs', 'Radio', 'Radiology Service', NULL, '170');
insert into `seg_pay_subaccounts` (`id`, `parent_account`, `short_name`, `formal_name`, `description`, `priority`) values ('ls', 'ls', 'Lab', 'Laboratory Service', NULL, '180');
insert into `seg_pay_subaccounts` (`id`, `parent_account`, `short_name`, `formal_name`, `description`, `priority`) values ('ps', 'ps', 'Pulmo', 'Pulmonary Service', NULL, '190');
insert into `seg_pay_subaccounts` (`id`, `parent_account`, `short_name`, `formal_name`, `description`, `priority`) values ('con', 'si', 'Consul', 'Consultation Fees', NULL, '200');
INSERT INTO `seg_cashier_account_types` (`name_short`, `name_long`) VALUES ('SI', 'Service Income'); 
INSERT INTO `seg_cashier_account_types` (`name_short`, `name_long`) VALUES ('RS', 'Radiology Service'); 
INSERT INTO `seg_cashier_account_types` (`name_short`, `name_long`) VALUES ('LS', 'Laboratory Service'); 
INSERT INTO `seg_cashier_account_types` (`name_short`, `name_long`) VALUES ('PS', 'Pulmonary Service'); 
update `seg_cashier_account_subtypes` set `pay_account` = 'con' where `type_id` = '33';
UPDATE `seg_cashier_account_subtypes` SET `parent_type` = '16' WHERE `type_id` = '33'; 
update `seg_cashier_account_subtypes` set `parent_type` = '9' where `type_id` = '55';
UPDATE `seg_cashier_account_subtypes` SET `pay_account` = 'mhc' WHERE `type_id` = '29';
UPDATE `seg_cashier_account_subtypes` SET `parent_type` = '18' WHERE `type_id` = '1'; 
UPDATE `seg_cashier_account_subtypes` SET `parent_type` = '19' WHERE `type_id` = '2'; 
insert into `seg_cashier_account_subtypes` (`name_short`, `name_long`, `parent_type`, `pay_account`) values ('Pulmo', 'Pulmonary Services', '19', 'ps');