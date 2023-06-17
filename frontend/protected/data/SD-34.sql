/* Alter table in target */
ALTER TABLE `seg_blood_compatibility` 
	ADD COLUMN `generic` varchar(50)  COLLATE latin1_swedish_ci NULL after `child` , 
	ADD COLUMN `generic_effectivity` varchar(100)  COLLATE latin1_swedish_ci NULL after `generic` , 
	ADD COLUMN `generic_rev` smallint(2)   NULL after `generic_effectivity` ;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;


UPDATE seg_blood_compatibility
SET generic='SPMC-F-BTS-9A',
generic_effectivity='October 01, 2013',
generic_rev='0'
WHERE adult='SPMC-F-BTS-10A'