INSERT INTO `hisdb`.`seg_signatory_document` (`document_code`, `document_name`) VALUES ('vacc_cert', 'Vaccination Certificate'); 
INSERT INTO `hisdb`.`seg_signatory_document` (`document_code`, `document_name`) VALUES ('vacc_cert2', 'Vaccination Certificate');


INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `signatory_title`, `document_code`, `title`) VALUES ('101155', 'Physician In-Charge', 'Physician In-Charge', 'vacc_cert', 'MD'); 
INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `signatory_title`, `document_code`, `title`) VALUES ('101728', 'Nurse II - IC Nurse-In-Charge', 'Nurse II - IC Nurse-In-Charge', 'vacc_cert2', 'RN'); 

INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `signatory_title`, `document_code`, `title`) VALUES ('100340', 'Physician In-Charge', 'Physician In-Charge', 'vacc_cert', 'MD');
INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `signatory_title`, `document_code`) VALUES ('100845', 'Physician In-Charge', 'Physician In-Charge', 'vacc_cert');
INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `signatory_title`, `document_code`) VALUES ('103004', 'Physician In-Charge', 'Physician In-Charge', 'vacc_cert');
INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `signatory_title`, `document_code`, `title`) VALUES ('105326', 'Nurse I - IC Nurse-In-Charge', 'Nurse I - IC Nurse-In-Charge', 'vacc_cert2', 'RN');

