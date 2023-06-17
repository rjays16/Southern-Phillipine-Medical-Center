INSERT INTO `seg_rep_templates_dept` (`id`, `report_id`, `dept_nr`, `template_name`) VALUES ('17', 'notifiable', '182', 'PSY_Notifiable_Diseases');
INSERT INTO  `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('17', 'date_based', 'included');
ALTER TABLE `seg_encounter_profile` ADD COLUMN `street_name` TEXT NULL AFTER `region_nr`; 
ALTER TABLE `seg_encounter_profile` ADD COLUMN `zip_code` VARCHAR(15) NULL AFTER `street_name`;
ALTER TABLE  `seg_encounter_profile` ADD COLUMN `brgy_nr` INT(11) NULL AFTER `zip_code`;
ALTER TABLE `seg_encounter_profile` ADD COLUMN `is_new` TINYINT(1) DEFAULT 0 NULL AFTER `is_discharged`;




CREATE TABLE `seg_encounter_profile_old` (
  `encounter_nr` VARCHAR(20) DEFAULT NULL,
  `pid` VARCHAR(12) DEFAULT NULL,
  `civil_status` VARCHAR(35) DEFAULT NULL,
  `date_birth` DATE DEFAULT '0000-00-00',
  `mun_nr` INT(11) DEFAULT NULL,
  `prov_nr` INT(11) DEFAULT NULL,
  `region_nr` INT(11) DEFAULT NULL,
  `street_name` TEXT,
  `zip_code` VARCHAR(15) DEFAULT NULL,
  `brgy_nr` INT(11) DEFAULT NULL
) ENGINE=INNODB DEFAULT CHARSET=latin1;

ALTER TABLE `hisdb`.`seg_encounter_profile_old` ADD COLUMN `sex` CHAR(1) NULL AFTER `brgy_nr`


DELIMITER $$


DROP PROCEDURE IF EXISTS `sp_populate_full_dataperson`$$

CREATE DEFINER=`seniordev`@`%` PROCEDURE `sp_populate_full_dataperson`()
BEGIN

INSERT INTO `seg_encounter_profile_old` 
  SELECT 
    ce.encounter_nr,
    cp.pid,
    cp.civil_status,
    cp.date_birth,
    cp.mun_nr,
    (SELECT 
      sp.`prov_nr` 
    FROM
      `seg_regions` AS sr 
      INNER JOIN `seg_provinces` AS sp 
        ON sr.`region_nr` = sp.`region_nr` 
      INNER JOIN `seg_municity` AS sm 
        ON sm.`prov_nr` = sp.`prov_nr` 
    WHERE sm.`mun_nr` = cp.mun_nr) AS prov_nr,
    (SELECT 
      sr.`region_nr` 
    FROM
      `seg_regions` AS sr 
      INNER JOIN `seg_provinces` AS sp 
        ON sr.`region_nr` = sp.`region_nr` 
      INNER JOIN `seg_municity` AS sm 
        ON sm.`prov_nr` = sp.`prov_nr` 
    WHERE sm.`mun_nr` = cp.mun_nr) AS region_nr,
    cp.street_name,
    (SELECT 
      sm.`zipcode` 
    FROM
      `seg_regions` AS sr 
      INNER JOIN `seg_provinces` AS sp 
        ON sr.`region_nr` = sp.`region_nr` 
      INNER JOIN `seg_municity` AS sm 
        ON sm.`prov_nr` = sp.`prov_nr` 
    WHERE sm.`mun_nr` = cp.mun_nr) AS zipcode,
    cp.brgy_nr,
    cp.sex
  FROM
    care_person AS cp 
    INNER JOIN `care_encounter` AS ce 
      ON ce.`pid` = cp.`pid` 
  WHERE ce.`encounter_type` IN ('13', '14') ;
  
  END$$

DELIMITER ;




CALL `sp_populate_full_dataperson`