update `hisdb`.`seg_blood_source` set `id` = 'DRMC' , `name` = 'DRMC' , `long_name` = 'DRMC' where `id` = 'DRHBB';
update `hisdb`.`seg_blood_source` set `id` = 'PEEDO' , `name` = 'PEEDO' , `long_name` = 'PEEDO' where `id` = 'PHOBB';

ALTER TABLE `hisdb`.`seg_blood_waiver_details`   
  ADD COLUMN `encounter_nr` VARCHAR(12) NULL AFTER `batch_nr`;
