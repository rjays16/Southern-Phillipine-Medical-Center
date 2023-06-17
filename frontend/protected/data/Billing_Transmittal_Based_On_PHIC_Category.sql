INSERT INTO `seg_rep_templates_registry` (
  `report_id`,
  `rep_group`,
  `rep_name`,
  `rep_description`,
  `rep_script`,
  `rep_dept_nr`,
  `rep_category`,
  `is_active`,
  `with_template`,
  `query_in_jasper`,
  `template_name`,
  `exclusive_opd_er`,
  `exclusive_death`,
  `w_graphical`
)
VALUES
  (
    'Billing_Transmittal_Based_On_PHIC_Category',
    'Hospital Operations',
    'Total no. of Transmittal Based on PhilHealth Category',
    'Total no. of Transmittal Based on PhilHealth Category',
    'Billing_Transmittal_Based_On_PHIC_Category',
    '152',
    'HOSP',
    '1',
    '1',
    '0',
    'Billing_Transmittal_Based_On_PHIC_Category',
    '0',
    '0',
    '0'
  ) ;
