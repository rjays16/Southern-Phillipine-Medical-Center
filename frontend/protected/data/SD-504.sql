CREATE TABLE `hisdb`.`seg_deactivate_remarks` (
  `code` VARCHAR (25) NOT NULL,
  `name` VARCHAR (100),
  `create_dt` DATETIME,
  `create_id` VARCHAR (100),
  `modify_dt` DATETIME,
  `modify_id` VARCHAR (100),
  `is_deleted` TINYINT (1) DEFAULT 0,
  PRIMARY KEY (`code`)
) ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  ('awol', 'Awol', 'medocs') ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  ('deceased', 'Deceased', 'medocs') ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  (
    'dismissal',
    'Dismissal',
    'medocs'
  ) ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  (
    'doubleentry',
    'Double Entry/Error',
    'medocs'
  ) ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  (
    'eoc',
    'End of Contract',
    'medocs'
  ) ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  ('eod', 'End of Duty', 'medocs') ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  (
    'rescomplete',
    'Residency Completed/Graduated',
    'medocs'
  ) ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  ('resign', 'Resignation', 'medocs') ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  ('retired', 'Retired', 'medcos') ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  (
    'termleave',
    'Terminal Leave',
    'medocs'
  ) ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  (
    'terminate',
    'Termination',
    'medocs'
  ) ;

INSERT INTO `hisdb`.`seg_deactivate_remarks` (`code`, `name`, `create_id`) 
VALUES
  (
    'retracted',
    'Retracted',
    'medocs'
  ) ;
