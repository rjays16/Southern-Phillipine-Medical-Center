/*
SQLyog Ultimate v11.33 (64 bit)
MySQL - 5.6.28-76.1-log 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `seg_diet` (
	`diet_code` varchar (30),
	`diet_name` varchar (300),
	`status` char (18)
); 
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('+CHON','High Protein','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('BF','Blenderized Feeding','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('BL','Bland Diet','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('CLD','Clear Liquid Diet','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('DD','Diabetic Diet','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('FD','Full Diet','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('GLD','General Liquid Diet','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('HD','Hypersensivity','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('LC','Low Cholesterol','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('LD','Low Purine','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('LF','Low Fat','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('LR','Low Residue','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('LS','Low Salt','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('MF','Material Feeding','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('NDCF','No Dark Colored Foods','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('NGLV','No Green Leafy Vegetables','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('NPO','Nothing Per Orem','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('NTP','Neutropenic','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('RD','Renal Diet','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('SD','Soft Diet','active');
insert into `seg_diet` (`diet_code`, `diet_name`, `status`) values('SP','Special Diet','active');

ALTER TABLE `care_encounter_notes` ADD COLUMN `nRemarks` TEXT NULL AFTER `create_time`, ADD COLUMN `nIVF` TEXT NULL AFTER `nRemarks`, ADD COLUMN `nHeight` DOUBLE NULL AFTER `nIVF`, ADD COLUMN `nWeight` DOUBLE NULL AFTER `nHeight`, ADD COLUMN `nDiet` VARCHAR(25) NULL AFTER `nWeight`;