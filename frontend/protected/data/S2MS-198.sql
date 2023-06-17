/*[3:21:06 PM][3 ms]*/ INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`) VALUES ('103974', 'Administrative Officer III'); 
/*[3:21:35 PM][16 ms]*/ INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `document_code`) VALUES ('103974', 'Administrative Officer III', 'birthcert'); 
/*[3:21:40 PM][6 ms]*/ INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`) VALUES ('103974'); 
/*[3:21:42 PM][16 ms]*/ INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`) VALUES ('103974'); 
/*[3:22:09 PM][6 ms]*/ INSERT INTO `hisdb`.`seg_signatory` (`personell_nr`, `signatory_position`, `document_code`) VALUES ('103974', 'Administrative Officer III', 'errorbirth'); 
/*[3:22:25 PM][6 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `is_default` = '0' WHERE `personell_nr` = '100793' AND `document_code` = 'birthcert'; 
/*[3:22:28 PM][59 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `is_default` = '0' , `is_active` = '0' WHERE `personell_nr` = '100793' AND `document_code` = 'errorbirth'; 
/*[3:22:31 PM][7 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `is_active` = '0' WHERE `personell_nr` = '100793' AND `document_code` = 'birthcert'; 
/*[3:22:32 PM][10 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `is_default` = '1' WHERE `personell_nr` = '103974' AND `document_code` = 'birthcert'; 
/*[3:22:35 PM][10 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `is_default` = '1' WHERE `personell_nr` = '103974' AND `document_code` = 'errorbirth'; 
/*[3:23:52 PM][4 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `end_date` = '2020-02-01 07:00:00' WHERE `personell_nr` = '100793' AND `document_code` = 'birthcert'; 
/*[3:23:54 PM][4 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `end_date` = '2020-02-01 07:00:00' WHERE `personell_nr` = '100793' AND `document_code` = 'errorbirth'; 
/*[4:11:17 PM][4 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `signatory_title` = 'Administrative Officer III' WHERE `personell_nr` = '103974' AND `document_code` = 'birthcert'; 
/*[4:11:18 PM][28 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `signatory_title` = 'Administrative Officer III' WHERE `personell_nr` = '103974' AND `document_code` = 'errorbirth'; 
ALTER TABLE `hisdb`.`seg_cert_birth`   
  CHANGE `encoder_title` `encoder_title` VARCHAR(30) CHARSET latin1 COLLATE latin1_swedish_ci NULL;
/*[5:57:16 PM][3 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `is_default` = '0' WHERE `personell_nr` = '101650' AND `document_code` = 'birthcert'; 
/*[5:57:18 PM][3 ms]*/ UPDATE `hisdb`.`seg_signatory` SET `is_default` = '0' WHERE `personell_nr` = '101650' AND `document_code` = 'errorbirth';