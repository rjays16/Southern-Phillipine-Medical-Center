INSERT INTO `hisdb`.`seg_type_charge` (`id`, `charge_name`, `description`, `is_excludedfrombilling`) VALUES ('sdnph', 'SDNPH', 'sdnph', '1'); 
UPDATE `hisdb`.`seg_type_charge` SET `ordering` = '29' WHERE `id` = 'sdnph'; 


INSERT INTO `hisdb`.`care_config_global` (`type`, `value`) VALUES ('charge_type', 'paid,phs,charity,cmap,lingap,dost'); 