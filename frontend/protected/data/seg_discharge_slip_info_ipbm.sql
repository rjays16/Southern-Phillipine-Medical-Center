CREATE TABLE `hisdb`.`seg_discharge_slip_info_ipbm` (
  `encounter_nr` VARCHAR (12) NOT NULL,
  `pid` VARCHAR (12),
  `checkup_date` DATE,
  `checkup_place` VARCHAR (200),
  `medications` TEXT,
  `injection` TEXT,
  `schedule` DATE,
  `notes` VARCHAR (20),
  `side_effects` TEXT,
  `discharge_date` DATE,
  `discharge_time` TIME,
  `dept_nr` VARCHAR (200),
  `personnel_nr` VARCHAR (200),
  `create_id` VARCHAR (35) NOT NULL,
  `create_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00',
  `modify_id` VARCHAR (35) NOT NULL,
  `modify_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unit_nr` VARCHAR (200),
  `chkuptime` VARCHAR (200),
  `medtime` VARCHAR (600),
  
  PRIMARY KEY (
    `encounter_nr`,
    `create_id`,
    `create_time`,
    `modify_id`,
    `modify_time`
  )
)