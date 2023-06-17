 INSERT INTO `care_config_global` (`type`, `value`) VALUES ('dietary_cutoff_lunch_from', '05:01'); 
 INSERT INTO `care_config_global` (`type`, `value`) VALUES ('dietary_cutoff_lunch_to', '10:00'); 
 INSERT INTO `care_config_global` (`type`, `value`) VALUES ('dietary_cutoff_dinner_from', '10:01');
 INSERT INTO `care_config_global` (`type`, `value`) VALUES ('dietary_cutoff_dinner_to', '15:00'); 

 UPDATE `seg_diet_cutoff` SET `start_time` = '10:01:00' , `end_time` = '15:00:00' WHERE `id` = '3';
 UPDATE `seg_diet_cutoff` SET `start_time` = '15:01:00' WHERE `id` = '1';
 UPDATE `seg_diet_cutoff` SET `end_time` = '10:00:00' WHERE `id` = '2'; 
