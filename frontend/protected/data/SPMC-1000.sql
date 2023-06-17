-- SPMC-1000 DB Changes --
ALTER TABLE `hisdb`.`care_encounter_notes`   
  ADD COLUMN `is_deleted` INT DEFAULT 0  NULL AFTER `nDiet`;