-- SPMC - 1190

ALTER TABLE `hisdb`.`seg_opd_or_temp`   
  ADD COLUMN `is_ipbm` TINYINT DEFAULT 0  NULL AFTER `create_id`;

INSERT INTO `seg_opd_or_temp` (`or_desc`,`is_ipbm`)
VALUES ('PDRC', 1),
	   ('PRC', 1),
	   ('PNP', 1),
	   ('DSWD', 1),
	   ('MSWDO', 1),
	   ('CSSDO', 1),
	   ('BRGY', 1),
	   ('RSCC', 1),
	   ('RCYO', 1),
	   ("NGO's", 1),
	   ('Disaster Victim', 1),
	   ('IGDD', 1),
	   ('Lingap', 1),
	   ('DCTRCDD', 1),
	   ('DOH', 1),
	   ('Religious Organization', 1),
	   ('Concern Citizen', 1),
	   ('NBI', 1),
	   ('Ombudsman', 1),
	   ('Government Agency', 1),
	   ('Senior Citizen', 1),
	   ('PHS/Infirmary', 1),
	   ('Government Hospital', 1);