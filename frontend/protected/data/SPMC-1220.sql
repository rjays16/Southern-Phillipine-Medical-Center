
/*[3:15:53 PM][14 ms]*/ INSERT INTO `seg_rep_params` (`param_id`, `parameter`, `param_type`, `choices`) VALUES ('PSY_mr_encoder', 'Encoder', 'sql', "SELECT 
  u.personell_nr AS id,
  fn_get_personellname_lastfirstmi (u.personell_nr) AS namedesc 
FROM
  care_users u 
  JOIN care_personell_assignment pa 
    ON u.`personell_nr` = pa.`personell_nr` 
  LEFT JOIN care_personell cp 
    ON u.`personell_nr` = cp.`nr` 
WHERE (
    (
  u.permission LIKE '%_a_1_ipbmmedicalrecords%'
  AND u.permission NOT LIKE '%_a_2_ipbmcanAccessICDICPM%' 
  AND u.permission NOT LIKE '%_a_2_ipbmcanceldeath%' 
  AND u.permission NOT LIKE '%_a_2_ipbmcanceldischarge%' 
  AND u.permission NOT LIKE '%_a_2_ipbmviewdeathcertificate%' 
  AND u.permission NOT LIKE '%_a_2_ipbmviewreceivedpatientschart%' 
  AND u.permission NOT LIKE '%_a_2_ipbmmedcert%' 
  AND u.permission NOT LIKE '%_a_2_ipbmconcert%' 
  AND u.permission NOT LIKE '%_a_2_ipbmmedicalabstract%'
    )
    OR (u.permission LIKE '%_a_2_ipbmcanAccessICDICPM%' 
  ) )
  AND (
    pa.`location_nr` = '182' 
    AND pa.status NOT IN (
      'deleted',
      'hidden',
      'inactive',
      'void'
    ) 
    AND cp.status = ''
  )"); 
//* NOTE seg_rep_templates_dept_params ID based on seg_rep_templates_dept auto increment id
/*
/*[3:07:36 PM][3 ms]*/ INSERT INTO `seg_rep_templates_dept` (`report_id`, `dept_nr`, `template_name`) VALUES ('icd_encoded_stat', '182', 'PSY_ICD_Encoded_Stat'); 
/*[7:56:32 PM][18 ms]*/ INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('42', 'psy_all_patienttype', 'included'); 
/*[8:00:01 PM][4 ms]*/ INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('42', 'type_nr', 'included'); 
/*[8:01:25 PM][3 ms]*/ INSERT INTO `seg_rep_templates_dept_params` (`id`, `param`, `status`) VALUES ('42', 'PSY_mr_encoder', 'included'); 
/*=========================================================================================================
