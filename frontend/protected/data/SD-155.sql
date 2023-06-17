INSERT INTO `hisdb`.`seg_opdarea` (
  `id`,
  `opd_name`,
  `accomodation_type`,
  `opd_code`
) 
VALUES
  ('9', 'RDU - Service', '1', '9') ;

INSERT INTO `hisdb`.`seg_opdarea` (
  `id`,
  `opd_name`,
  `accomodation_type`,
  `opd_code`
) 
VALUES
  ('10', 'RDU - Pay', '2', '10') ;

ALTER TABLE `hisdb`.`seg_opdarea`   
  CHANGE `opd_code` `opd_code` TINYINT NOT NULL;
