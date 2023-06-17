-- SPMC - 1125 DB Changes --
-- Start here --
UPDATE  `hisdb`.`seg_type_request_source` set `id` = 'IPBM' , `source_name` = 'Institute of Psychiatry and Behavioral Medicine' where `id` = 'IPBMIPD'
DELETE FROM `hisdb`.`seg_type_request_source` WHERE `id` = 'IPBMOPD';
ALTER TABLE `hisdb`.`seg_type_request_source`   
  CHANGE `source_name` `source_name` VARCHAR(50) CHARSET latin1 COLLATE latin1_swedish_ci NULL;