CREATE TABLE `seg_phic_requirement`( `pid` VARCHAR(12) NOT NULL, 
									`requirement` text, 
									`modify_id` VARCHAR(60) );

CREATE TABLE `seg_audit_phic` (
  `date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `encoder` varchar(35) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `pid` varchar(12) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `old_requirement` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;