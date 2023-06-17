CREATE TABLE `seg_pdpu_progress_notes` (
  `notes_id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` varchar(12) NOT NULL,
  `encounter_nr` varchar(12) NOT NULL,
  `progress_date_time` datetime DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `ward` varchar(50) DEFAULT NULL,
  `date_admission` datetime DEFAULT NULL,
  `final_diagnosis` text,
  `informant` varchar(50) DEFAULT NULL,
  `venue` varchar(50) DEFAULT NULL,
  `purpose_reasons` text,
  `action_taken` text,
  `problem_encountered` text,
  `plan` text,
  `address` text,
  `date_birth` date DEFAULT NULL,
  `sex` char(1) DEFAULT NULL,
  `civil_status` varchar(35) DEFAULT NULL,
  `attending_physician` int(11) DEFAULT NULL,
  `classification` varchar(10) DEFAULT NULL,
  `create_id` varchar(35) DEFAULT NULL,
  `create_dt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modify_id` varchar(35) DEFAULT NULL,
  `modify_dt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `history` text,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`notes_id`),
  KEY `FK_seg_pdpu_progress_notes_encounter_nr` (`encounter_nr`),
  KEY `FK_seg_pdpu_progress_notes_pid` (`pid`),
  CONSTRAINT `FK_seg_pdpu_progress_notes_encounter_nr` FOREIGN KEY (`encounter_nr`) REFERENCES `care_encounter` (`encounter_nr`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_seg_pdpu_progress_notes_pid` FOREIGN KEY (`pid`) REFERENCES `care_person` (`pid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1


CREATE TABLE `seg_pdpu_progress_n_audit_trail` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `notes_id` int(10) NOT NULL,
  `date_changed` datetime NOT NULL,
  `action_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_progress_audit_trail_id` (`notes_id`),
  CONSTRAINT `fk_progress_audit_trail_id` FOREIGN KEY (`notes_id`) REFERENCES `seg_pdpu_progress_notes` (`notes_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci


