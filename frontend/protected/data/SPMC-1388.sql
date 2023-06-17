UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'ASU - Pay' WHERE `id` = '2'; 
UPDATE `hisdb`.`seg_opdarea` SET `opd_name` = 'ASU - Service' , `accomodation_type` = '1' WHERE `id` = '3'; 
INSERT INTO `hisdb`.`seg_opdarea` (`id`, `opd_name`, `accomodation_type`) VALUES ('4', 'HI - Pay', '2');
INSERT INTO `hisdb`.`seg_opdarea` (`id`, `opd_name`, `accomodation_type`) VALUES ('5', 'HI - Service', '1'); 
INSERT INTO `hisdb`.`seg_opdarea` (`id`, `opd_name`, `accomodation_type`) VALUES ('6', 'ONCO - Pay', '2'); 
INSERT INTO `hisdb`.`seg_opdarea` (`id`, `opd_name`, `accomodation_type`) VALUES ('7', 'ONCO - Service', '1'); 