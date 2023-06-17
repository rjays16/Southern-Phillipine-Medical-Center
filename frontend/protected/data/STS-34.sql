CREATE TABLE `seg_consult_request` (
  `consult_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_last` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_first` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_middle` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_birth` date NOT NULL,
  `contact_no` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sex` enum('F','M') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `religion` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_dt` datetime DEFAULT NULL,
  `onesignal_player_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `onesignal_push_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `device_model` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_uuid` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_unique_id` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_platform` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` mediumtext NOT NULL,
  `chief_complaint` mediumtext NOT NULL,
  `yellow_card` tinytext,
  `areRelated` tinyint(1) DEFAULT '0',
  `waiverAproved` tinyint(1) DEFAULT '1',
  `is_expired` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`consult_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1


CREATE TABLE `seg_consult_meeting` (
  `id` varchar(36) NOT NULL,
  `consult_id` varchar(40) DEFAULT NULL,
  `encounter_nr` varchar(12) DEFAULT NULL,
  `doctor_id` int(11) NOT NULL,
  `meeting_url` text,
  `meeting_id` varchar(40) DEFAULT NULL,
  `is_valid` tinyint(1) DEFAULT '1',
  `status` enum('pending','done','confirmed','cancelled') DEFAULT 'pending',
  `create_dt` datetime DEFAULT NULL,
  `create_id` varchar(35) DEFAULT NULL,
  `modify_dt` datetime DEFAULT NULL,
  `modify_id` varchar(35) DEFAULT NULL,
  `history` text,
  `conf_notif_sent` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_doctorid_meeting_personell` (`doctor_id`),
  KEY `FK_encounter_nr_consult_meeting` (`encounter_nr`),
  CONSTRAINT `FK_doctorid_meeting_personell` FOREIGN KEY (`doctor_id`) REFERENCES `seg_doctor_meeting` (`doctor_id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_encounter_nr_consult_meeting` FOREIGN KEY (`encounter_nr`) REFERENCES `care_encounter` (`encounter_nr`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1


CREATE TABLE `seg_doctor_meeting` (
  `doctor_id` int(11) DEFAULT NULL,
  `site_name` text COLLATE utf8mb4_unicode_ci,
  `webex_id` text COLLATE utf8mb4_unicode_ci,
  `password` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_dt` datetime DEFAULT NULL,
  `create_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_dt` datetime DEFAULT NULL,
  `modify_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  KEY `FK_dr_meeting_nr` (`doctor_id`),
  CONSTRAINT `FK_dr_meeting_nr` FOREIGN KEY (`doctor_id`) REFERENCES `care_personell` (`nr`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hisdb`.`seg_opd_or_temp` (
  `or_id`,
  `or_desc`,
  `modify_id`,
  `modify_time`,
  `create_time`,
  `create_id`,
  `status`,
  `is_ipbm`
) 
VALUES
  (
    '12',
    'FREE CONSULTATION',
    NULL,
    '2020-06-06 15:49:54',
    '0000-00-00 00:00:00',
    NULL,
    'active',
    '0'
  );

  