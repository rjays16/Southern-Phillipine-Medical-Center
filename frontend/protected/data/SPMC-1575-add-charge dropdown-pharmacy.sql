INSERT INTO `hisdb`.`seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) VALUES ('FMAP', 'FOR MAP', 'No guarantee letter yet', '12', '1', '1');
INSERT INTO `hisdb`.`seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) VALUES ('OP', 'OP', 'Office of the President', '13', '1', '1');
INSERT INTO `hisdb`.`seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) VALUES ('SCPF', 'SCPF', 'Socio Civic Project Fund', '15', '1', '1');

INSERT INTO `hisdb`.`seg_type_charge_pharma` (`id`, `charge_name`, `description`, `is_excludedfrombilling`, `in_pharmacy`) VALUES ('WALK-IN(MAP)', 'WALK-IN (MAP)', 'From other hospital w/ MAP', '1', '1'); 
UPDATE `hisdb`.`seg_type_charge_pharma` SET `ordering` = '14' WHERE `id` = 'WALK-IN(MAP)'; 
INSERT INTO `hisdb`.`seg_type_charge_pharma` (`id`, `charge_name`, `description`, `ordering`, `is_excludedfrombilling`, `in_pharmacy`) VALUES ('WALK-IN(LINGAP)', 'WALK-IN (LINGAP)', 'From other hospital w/ LINGAP', '16', '1', '1'); 

UPDATE `hisdb`.`seg_type_charge_pharma` SET `description` = 'w/ LINGAP' WHERE `id` = 'WALK-IN(LINGAP)';
UPDATE `hisdb`.`seg_type_charge_pharma` SET `description` = 'w/ MAP' WHERE `id` = 'WALK-IN(MAP)';

UPDATE `hisdb`.`seg_rep_params` SET `choices` = '(SELECT \r\n  \'all\' AS id,\r\n  \'All\' AS namedesc)\r\nUNION(\r\nSELECT \r\n  id AS id,\r\n  charge_name AS namedesc\r\nFROM\r\n  seg_type_charge_pharma \r\nWHERE in_pharmacy = 1\r\nORDER BY ordering ASC);\r\n' WHERE `param_id` = 'phar_charge_type'; 