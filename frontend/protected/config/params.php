<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$_request = new CHttpRequest;
$hostInfo = $_request->getHostInfo();

return array(
    // this is used in contact page
    'adminEmail' => 'webmaster@example.com',

    // Application Name ...
    'APP_NAME' => 'SegHIS',
    'HEIRS_ALERT' => false,

    // System transaction codes for inventory management ...
    'UNPACK' => 'UPK',
    'REPACK' => 'RPK',
    'RECEIVE' => 'RCV',
    'ADJUST' => 'ADJ',
    'SALE' => 'SLE',
    'RETURN' => 'RET',
    'TRANSFER' => 'TRA',
    'ISSUANCE' => 'ISS',
    'CONVERT' => 'CON',
    'MANUFACTURE' => 'MNF',
    'MANUFACTURE_BLD' => 'MNB',
    'COSTADJUST' => 'CDJ',

    'FIS_COMPANY' => 0,
    'FIS_UID' => 'admin',
    'FIS_PWD' => 'admin',

    'FIS_ACTIVE' => false,

    'PACS_ACTIVE' => false,
    'FIS_PARTIAL_ACTIVE' => false,
    'LIS_ACTIVE' => false,
    'SMS_ACTIVE' => false,
    'BARCODE_REQUIRED' => false,
    'HEIRS_ALERT' => false,

    'PACS_CONFIG' => array(
        'applet' => "{$hostInfo}/dicom_viewer/applet.jar",
        'dicom_dict' => "{$hostInfo}/dicom_viewer/Dicom.dic",
        'imagePath' => "{$hostInfo}/dicom_viewer/dicomimages",
    ),

    // Transaction Codes ...
    'ST_SALESINVOICE' => 10,
    'ST_CUSTCREDIT' => 11,
    'ST_CUSTPAYMENT' => 12,
    'ST_CUSTDELIVERY' => 13,
    'ST_DISCOUNT' => 9,
    'ST_ARASSIGN' => 14,
    'ST_ARDEDUCT' => 15,
    'ST_SUPPINVOICE' => 20,
    'DOCTOR_CREDIT' => 200000,
    'PATIENT' => 'P',
    'DOCTOR' => 'D',
    'CHARGE' => 'CHRGE',
    'CASH' => 'CASHP',
    'BILL_PAYMENT' => 'CHRGP',
    'AR_ASSIGN' => 'ARASN',
    'DEPOSIT' => 'DEPST',
    'PF_CHARGE' => 'PFCHG',
    'DISCOUNT' => 'DSCNT',
    'DOCTOR_TERM' => 3,
    'RFUND' => 'RFUND',


    'FIS_URL' => "http://58.69.23.159/mmfis/modules/api",
    'FIS_PATH_FOR_JS' => "/mmfis/modules/api",

    'HIS_URL' => "http://10.1.80.20/hisspmc4ihomp/include/care_api_classes/ehrhisservice",
    'HIS_TOKEN' => "sampletoken",

    'TELEMED_URL' => "http://10.1.80.34:8074/api",
    'TELEMED_TOKEN' => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hMmQ1NTM4Mi5uZ3Jvay5pb1wvZWhyLWJhY2tlbmQtYXdzXC9wdWJsaWNcL2FwaVwvbG9naW4iLCJpYXQiOjE1NzMyMTU1ODYsImV4cCI6MTU3MzIxOTE4NiwibmJmIjoxNTczMjE1NTg2LCJqdGkiOiIzNmZlcmY3N0NoVmFtZDduIiwic3ViIjo1MDQsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.99DA-Qk7cW0eR369-d3cJm9apmzDVfupmRL_1IkTCFI",

    'NOTIFICATION_SOCKET' => "http://10.1.80.34:6001",
    'NOTIFICATION_URL' => "http://10.1.80.34:8074/api",
    'NOTIFICATION_TOKEN' => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hMmQ1NTM4Mi5uZ3Jvay5pb1wvZWhyLWJhY2tlbmQtYXdzXC9wdWJsaWNcL2FwaVwvbG9naW4iLCJpYXQiOjE1NzMyMTU1ODYsImV4cCI6MTU3MzIxOTE4NiwibmJmIjoxNTczMjE1NTg2LCJqdGkiOiIzNmZlcmY3N0NoVmFtZDduIiwic3ViIjo1MDQsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.99DA-Qk7cW0eR369-d3cJm9apmzDVfupmRL_1IkTCFI",

    // Sources of transactions .... 
    'Laboratory' => 'LAB',
    'Radiology' => 'RAD',
    'Pharmacy' => 'PHA',
    'Miscellaneous' => 'MSC',
    'Central Supply Room' => 'CSR',
    'Operating Room' => 'OPR',
    'Accommodation' => 'ACC',
    'Credit Memo' => 'CMO',
//      'General Practitioner' => 'PF1',
//      'Specialist'           => 'PF2',
//      'Surgeon'              => 'PF3',
//      'Anaesthesiologist'    => 'PF4',
    'Professional Fees' => 'PRF',
    'AR Assignment' => 'ARA',
    'Insurance Coverage' => 'ARD',
    'Bill Discount' => 'DSC',
    'Cash Payment' => 'CSH',
    'Bill Payment' => 'PBP',

    'PATIENT_TERM' => 5,
    'PATIENT_CREDIT' => 100000,

    'PO_LOOKUP' => 3,

    // Host and Port of Worklist Server and WADO
    'WRKLIST_HOST' => '192.168.0.75',
    'WRKLIST_PORT' => 2100,
    'PACS_PORT' => 1234,
    'HL7LISTEN_HOST' => '172.20.20.132',
    'HL7LISTEN_PORT' => 5000,
    'PACS_AET' => 'IQVIEW',
    'DICOM_AET' => 'CARALOSSRV1',


    /*FTP ask for FTP*/
    'LIS_FTPURL' => 'ftp://172.20.20.132',
    'LIS_USER' => 'lisftpuser',
    'LIS_PWD' => 'lisftppwd',

    'RAD_FTPURL' => 'ftp://172.20.20.3',
    'RAD_USER' => 'intuser',
    'RAD_PWD' => '1nTuser',

    'PACS_INPUTPATH' => '/srv/www/pacs/input',
    'LIS_INPUTPATH' => '/srv/www/lis/input',

    'DEPTENCOUNTER' => 7,
    'HOSPENCOUNTER' => 8,
    'SPIN_NO' => 5,

    //Header for reports
    'COUNTRY_DESC' => 'Republic of the Philippines',
    'HOSPITAL_NAME' => 'MAAYO MEDICAL',
    'HOSPITAL_ADDRESS' => 'Plaridel Street, Alang Alang, Mandaue City',
    'HOSPITAL_ADDRESS2' => 'Cebu Province, Philippines',

    //INVENTORY TYPES
    'INVTYPE_MEDS' => 1,
    'INVTYPE_MEDSIV' => 4,

    //DEPARTMENTS
    'LRDR' => 41,
    'CSSD' => 75, // for sterile department

    //INSURANCE
    'PHIC_ID' => 1,

    // Do not edit ...
    'EMERGENCY' => 'ERE',
    'OUT-PATIENT' => 'OPE',
    'IN-PATIENT' => 'IPE',

    // Patient Deposit Limit
    'WARD_LIMIT' => 10000,
    'OTHER_LIMIT' => 30000,
    'DEFAULT_PROVINCE' => '6000', //Cebu


    'SERVICES' => array(
        'DIALYSIS' => '50003'
    ),

    /** FIS **/
    // CASH REQUEST / Cost centers use in FIS
    'AUX' => 'Auxillary',
    'BB' => 'BB',
    'CARDIO' => 'CARDIO', // WALA SA `smed_departments_catalog`
    'DIALYSIS' => 'DIALYSIS',
    'DNC' => 'DNC',
    'LAB' => 'LAB',
    'PACKAGE' => 'PACKAGE',
    'PHA' => 'Pharmacy',
    'PR' => 'PR',
    'RAD' => 'RADIO',
    'RHB' => 'RHB',
    'RDU' => 'RDU',
    'SEOPE' => 'SEOPE',
    'SUOPE' => 'SUOPE',
    'WHC' => 'WHC',
    'DEP' => 'DEP',

    // COGS
    'COGS_ACCOUNT' => '8101', //debit
    'INVENTORY_ACCOUNT' => '1402', //credit

    'COGS_SUPP_ACCOUNT' => '8103',
    'INVENTORY_SUPPLIES_ACCOUNT' => '1404', //credit

    'DOC_PAY_ACCOUNT' => '3102',

    'AP_PATIENT_REFUND' => '3105', //debiit
    'AR-PATIENT' => '1301', //credit charges
    /*7/21/2016 updated by Nato*/
    'AR-OPD-PATIENT' => '1302',
    'AR-ER-PATIENT' => '1704',

    'PRONOTE' => '1303', //Promissory
    'PATIENT_REFUND' => '3105',

    'COH' => '1202', //cash-on-hand
    'SUOPE_ACCOUNT' => '7123',
    'SEOPE_ACCOUNT' => '7124', // cash transaction
    'SRVC_ACCOUNT' => '7124',
    'PACKAGE_ACCOUNT' => '7130', // for the meantime `aux` | dnc
    'AUX_ACCOUNT' => '7122',
    'LAB_ACCOUNT' => '7104',
    'RADIO_ACCOUNT' => '7103', //xray
    'CT-SCAN_ACCOUNT' => '7106', //ctscan
    'ULTRASOUND_ACCOUNT' => '7110', //ultrasound
    'MRI_ACCOUNT' => '7103', //mri temp for xray
    '2D_ECHO_ACCOUNT' => '7103', //mri temp for xray
    'PHARMACY_ACCOUNT' => '7102',
    'CARDIO_ACCOUNT' => '7128', // for the meantime `aux`
    'RDU_ACCOUNT' => '7109', // for the meantime `aux`
    'OR_ACCOUNT' => '7116',
    'XLO_ACCOUNT' => '7122', // for the meantime `aux`
    'DIETARY_ACCOUNT' => '7107', // for the meantime `aux`
    'DISCOUNT_ACCOUNT' => '8104', // discounts & others
    'SR_DISCOUNT_ACCOUNT' => '9172', // sr citizen discounts
    'PHIC_ACCOUNT' => '1307',
    'INSURANCE_ACCOUNT' => '1306',
    'HOSP_ACC_ACCOUNT' => '7113',
    'OUTPUTTAX_ACCOUNT' => '3119',
    'PR_ACCOUNT' => '7116', // OR procedures / Endo
    'DIA_ACCOUNT' => '7109', // Dialysis
    'WELLNESS_ACCOUNT' => '7127', // Wellness
    'DNC_ACCOUNT' => '7129', // Diabetis and Nutrition
    'WHC_ACCOUNT' => '7126', // women's health
    'ENDO_ACCOUNT' => '7108', // endoscopy
    'DEPOSIT_ACCOUNT' => '3123',
    'PEME_ACCOUNT' => '7131',
    'DR_ACCOUNT' => '7114',
    /*7/21/2016 updated by Nato*/
    'OTHER_DISCOUNT' => '9171',
    'PWD_DISCOUNT' => '9173',
    'CTL_ACCOUNT' => '7131',
    'PHIL_CASH_BOND_ACCOUNT' => '3125',
    'MEDICINE_ACCOUNT' => '7133',
    'PHARMACY_VAT_EXCEMPT_ACCOUNT' => '7134',
    'PHARMACY_WALKIN_ACCOUNT' => '7135',
    'MSC_ACCOUNT' => '7132',
    'HSP_ACCOUNT' => '7124',
    'NB_ACCOUNT' => '7104',

    // DIMENSIONS
    'LAB_DIMENSION' => '4',
    'RAD_DIMENSION' => '2',
    'PHARMA_DIMENSION' => '3',

    // Area Catalog
    'PHARMA_AREA' => '30',
    'CSR_AREA' => '31',
    'VAT' => 1.12,
    // end FIS
    // PHIC related details
    'HOSPITAL_PHIC_ACCREDITATION_NO' => 'H07028098',
    'HOSPITAL_AUTHORIZED_BED_CAPACITY' => 150,
    'HOSPITAL_PHIC_CATEGORY' => 'TERTIARY',
    'HOSPITAL_PHIC_EMPLOYER_NO' => '14-022410003-2',
    'HOSPITAL_PHIC_REPRESENTATIVE_NAME' => 'Dr. Joselito D. Almendras',
    'HOSPITAL_PHIC_REPRESENTATIVE_POSITION' => 'HCI Authorized Representative',
    //'HOSPITAL_ADDRESS' => 'OUANO AVE., CITY SOUTH SPECIAL ECONOMIC ADMINISTRATIVE ZONE, MANDAUE CITY,CEBU',
    // 'HOSPITAL_ADDRESS' => 'Plaridel Street, Alang Alang Mandaue City, Cebu Province, Philippines',
    'HOSPITAL_ADDRESS' => 'Skyline Drive cor. Quirino Highway, Brgy. Tungkong Mangga City of San Jose del Monte, Bulacan',
    'HOSPITAL_TRUNKLINE' => '260-0808 loc. 1161 to 71',
    'HOSPITAL_TAX' => '24255525',

    //Header for reports
    'COUNTRY_DESC' => 'Republic of the Philippines',
    // 'HOSPITAL_NAME' => 'MAAYO MEDICAL CENTER',
    'HOSPITAL_NAME' => 'SKYLINE HOSPITAL AND MEDICAL CENTER',

);
