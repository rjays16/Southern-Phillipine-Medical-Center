INSERT INTO `hisdb`.`seg_type_charge` (
  `id`,
  `charge_name`,
  `description`,
  `ordering`,
  `is_excludedfrombilling`
) 
VALUES
  (
    'dbc',
    'DBC',
    'Davao Blood Center',
    '30',
    '1'
  ) ;

INSERT INTO `hisdb`.`care_config_global` (
  `type`,
  `value`,
  `create_id`,
  `create_time`
) 
VALUES
  (
    'mainlab_only_chargetypes',
    'sdnph,dbc',
    'medocs',
    '2020-07-08 09:33:02'
  ) ;