-- SPMC-779 DB Changes --
CREATE TABLE `hisdb`.`seg_death_cause`(  
  `pid` VARCHAR(55) NOT NULL,
  `encounter_nr` VARCHAR(55),
  `death_cause` TEXT,
  `history` TEXT,
  PRIMARY KEY (`pid`)
);
