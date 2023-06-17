/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/* Create table in target */
CREATE TABLE `seg_eclaims_document_type`(
`id` varchar(10) COLLATE latin1_swedish_ci NOT NULL , 
`name` varchar(100) COLLATE latin1_swedish_ci NULL , 
`existing` tinyint(1) NULL DEFAULT 0 , 
PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='latin1' COLLATE='latin1_swedish_ci';

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;



/* Queries to be executed on gsmc_hisdb_test - 122.3.176.81:3306 */

SET FOREIGN_KEY_CHECKS = 0;

insert into `seg_eclaims_document_type` values('ANR', 'Anesthesia Record', '1');

insert into `seg_eclaims_document_type` values('CAB', 'Clinical Abstract', '1');

insert into `seg_eclaims_document_type` values('CAE', 'Certification of Approval/Agreement from the Employer', '1');

insert into `seg_eclaims_document_type` values('CF1', 'Claim Form 1', '1');

insert into `seg_eclaims_document_type` values('CF2', 'Claim Form 2', '1');

insert into `seg_eclaims_document_type` values('CF3', 'Claim Form 3', '1');

insert into `seg_eclaims_document_type` values('CNC', 'Clinical Charts', '0');

insert into `seg_eclaims_document_type` values('CNS', 'Clinical Summary', '0');

insert into `seg_eclaims_document_type` values('COE', 'Certificate of Eligibility', '1');

insert into `seg_eclaims_document_type` values('CPE', 'Certification of Payment from Employer', '0');

insert into `seg_eclaims_document_type` values('CSF', 'Claim Signature Form', '1');

insert into `seg_eclaims_document_type` values('CTR', 'Confirmatory Test Result by SACCL or RITM', '1');

insert into `seg_eclaims_document_type` values('DTR', 'Diagnostic Test Result', '1');

insert into `seg_eclaims_document_type` values('EPR', 'EPRS Contribution', '0');

insert into `seg_eclaims_document_type` values('HDR', 'Hemodialysis Record', '1');

insert into `seg_eclaims_document_type` values('MBC', 'Member Birth Certificate', '1');

insert into `seg_eclaims_document_type` values('MCC', 'MCC from PhilHealth', '0');

insert into `seg_eclaims_document_type` values('MDR', 'Member Data Record', '1');

insert into `seg_eclaims_document_type` values('MEF', 'Member Empowerment Form', '1');

insert into `seg_eclaims_document_type` values('MMC', 'Member Marriage Contract', '1');

insert into `seg_eclaims_document_type` values('MRF', 'PhilHealth Member Registration Form', '1');

insert into `seg_eclaims_document_type` values('MSR', 'Malarial Smear Result', '1');

insert into `seg_eclaims_document_type` values('MWV', 'Waiver for Consent for Release of Confidential Patient Health Information', '1');

insert into `seg_eclaims_document_type` values('NGR', 'Neurological Report', '0');

insert into `seg_eclaims_document_type` values('NTP', 'NTP Registry Card', '1');

insert into `seg_eclaims_document_type` values('OPR', 'Operative Record', '1');

insert into `seg_eclaims_document_type` values('ORB', 'Official Receipt from Bank/ Bayad Center', '0');

insert into `seg_eclaims_document_type` values('ORS', 'Official Receipt', '1');

insert into `seg_eclaims_document_type` values('PAC', 'Pre-authorization Clearance', '1');

insert into `seg_eclaims_document_type` values('PBC', 'Patient Birth Certificate   ', '1');

insert into `seg_eclaims_document_type` values('PBF', 'Philhealth Benefit of Eligibility Form', '0');

insert into `seg_eclaims_document_type` values('PIC', 'Valid PhilHealth Indigent ID', '1');

insert into `seg_eclaims_document_type` values('POR', 'PhilHealth Official Receipt', '1');

insert into `seg_eclaims_document_type` values('SCI', 'Senior Citizen ID', '0');

insert into `seg_eclaims_document_type` values('SGS', 'Surgical Summary', '0');

insert into `seg_eclaims_document_type` values('SOA', 'Statement of Account', '1');

insert into `seg_eclaims_document_type` values('STR', 'HIV Screening Test Result', '1');

insert into `seg_eclaims_document_type` values('TCC', 'TB-Diagnostic Committee Certification', '1');

insert into `seg_eclaims_document_type` values('TYP', 'Three Years Payment of (2400 x 3 years of proof of payment)', '1');

insert into `seg_eclaims_document_type` values('VID', 'Valid ID', '0');

