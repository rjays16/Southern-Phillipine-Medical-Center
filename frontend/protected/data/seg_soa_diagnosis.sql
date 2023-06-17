/*
SQLyog Ultimate v11.33 (64 bit)
MySQL - 5.6.28-76.1-log 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `seg_soa_diagnosis` (
	`diag_id` int (15),
	`encounter_nr` varchar (45),
	`final_diagnosis` text ,
	`other_diagnosis` text ,
	`create_date` datetime ,
	`create_id` varchar (90),
	`modify_date` datetime ,
	`modify_id` varchar (90),
	`history` text 
); 
