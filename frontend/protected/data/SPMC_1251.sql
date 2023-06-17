-- SPMC-1251
insert into `hisdb`.`seg_signatory_document` (`document_code`, `document_name`) values ('confcert-ipbm', 'IPBM Certificate of Confinement')

insert into `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `signatory_title`, `document_code`) values ('101143', 'HIMD In-Charge', 'HIMD In-Charge', 'confcert-ipbm')

-- additional DB Changes as of 11/21/17

insert into `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `signatory_title`, `document_code`) values ('100979', 'Administrative Officer V', 'Administrative Officer V', 'confcert-ipbm')