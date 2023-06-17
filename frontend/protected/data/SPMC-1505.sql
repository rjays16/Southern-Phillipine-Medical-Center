-- SPMC - 1505 DB Changes --

-- start here --
UPDATE seg_rep_templates_clinic SET report_id = 'MR_Pediatrics_Reports' WHERE report_id = 'mr_pediatrics_month_hospital_ward_discharges';

UPDATE `hisdb`.`seg_rep_templates_registry` SET `report_id` = 'MR_Pediatrics_Reports' , `rep_group` = 'Hospital Operations' WHERE `report_id` = 'MR_Hospital_Ward_Discharges';

-- end here --