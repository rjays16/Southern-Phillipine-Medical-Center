<?php
    
    define('PHIC',18); 
    define('PAY_ACCOMMODATION',2); 
    define('SERVICE_ACCOMMODATION',1); 
    define('OWWA',3);
    define('DISCHARGED','2,7,1,6'); #1,6 for admission and opd
    define('RECOVERED','1,2,5,6');
    define('TRANSFERRED','3,8');
    define('HAMA','4,9');
    define('ABSCONDED','5,10');
    define('UNIMPROVED','3,7');
    define('DEATH_CODE','4,8,9,10');
     #Added by Matsuu 12012017
    define('IPBM_DEP', '182');
    define('IPBM_FOOTER', 'SPMC-F-HIM-24');
    define('IPBM_EFFECTIVITY', 'Effectivity: August 1, 2015');
    define('IPBM_REVISION', 'Revision: 0');
    define('IPBM_HEADER','Institute of Psychiatry and Behavioral Medicine'); 
    define('IPBM_patient_type', '13,14');
    define('IPBM_OPD','14');
    define('IPBM_IPD','13');
    define('IPD','3,4');
    define('ER_ADM', '3');
    define('OPD_ADM', '4');
    define('GET_DEPT',$_GET['dept_nr']);
    define('ER_DISP_HEADER','EMERGENCY DEPARTMENT DISPOSITION TIME MONITORING');
    define('ER','1');
    define('OPD','2');
    define('WALK_IN','8');
    define('EMPLOYED_GOVT',1);
    define('EMPLOYED_PRIVATE',2);
    define('OVERSEAS_WORKER',3);
    define('INDIVIDUAL_PAYING_SELF_EMPLOYED',4);
    define('SPONSORED_MEMBER', 5);
    define('LIFETIME_MEMBER', 6);
    define('HOSPITAL_SPONSORED_MEMBER',9);
    define('SENIOR_CITIZEN',10);
    define('KASAMBAHAY',11);
    define('POINT_OF_SERVICE', 13);
    #Ended here..
    define('DIED_E', '4');
    define('DIED_A', '8');
    define('AUTOPSY', '9');
    define('NONAUTOPSY', '10');
    define(IPBMIPD_enc, 13);
    define(IPBMOPD_enc, 14);
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
    
    #added by VAS 10/05/2017
    /*
        1 EMPLOYED-GOV`T
        2 EMPLOYED-PRIVATE
        4 INDIVIDUAL PAYING-SELF EMPLOYED
        6 LIFETIME MEMBER
        8 -- DELETED --
        10 SENIOR CITIZEN
        11 KASAMBAHAY (HOUSEHOLD-HELP)
        13 POINT OF SERVICE
    */
    DEFINE('PHIC_MEMBER','1,2,4,6,8,10,11,13');
    /*
        5 SPONSORED MEMBER
        9 HOSPITAL SPONSORED MEMBER
    */
    DEFINE('PHIC_INDIGENT','5,9');
    /*
        3 OVERSEAS WORKER (OFW)
        7 -- DELETED --
    */
    DEFINE('PHIC_OWWA','3,7');
    
    if ($row = $objInfo->getAllHospitalInfo()) {
        $hosp_country = $row['hosp_country'];
        $hosp_agency = strtoupper($row['hosp_agency']);
        $hosp_name   = strtoupper($row['hosp_name']);
        $hosp_addr1 = $row['hosp_addr1'];

    }else {
        $hosp_country = "Republic of the Philippines";
        $hosp_agency  = "DEPARTMENT OF HEALTH";
        $hosp_name    = "DAVAO MEDICAL CENTER";
        $hosp_addr1   = "JICA Bldg., JP Laurel Avenue, Davao City";
    }
    $ihomp = "Hospital Information System";

    #get report description
    $sql = "SELECT rep_description, exclusive_opd_er, exclusive_death FROM seg_rep_templates_registry 
            WHERE rep_script=".$db->qstr($report_name)." AND is_active=1";
    #$report_title = $db->GetOne($sql);
    $report_info = $db->GetRow($sql);
    $report_title = $report_info['rep_description'];
    $exclusive_opd_er = $report_info['exclusive_opd_er'];
    $exclusive_death = $report_info['exclusive_death'];
    $his_permission = $report_title;
    $ipbm_header = "Institute of Physchiatry and Behavioral Medicine"; #Added by Matsuu 11102017
  
    
    
    #additional parameters
    $paramsarr = explode(",",$param);

    $with_ptype = 0;
    $with_icd10_class = 0;
    $with_surgery = 0;
    $with_dept = 0;
    $with_area = 0;
    $with_location = 0;
    $with_brgy = 0;
    $with_mun = 0;
    $with_prov = 0;
    $with_date_based = 0;
    $with_delivery_type = 0; //added by kenneth 04-28-16
    $with_discharge_days = 0; //added by kenneth 04-28-16
    $with_phic = 0;
    $with_status = 0;
    $with_mode_chart = 0;
    $with_bb_trxn = 0;
    $with_cr_type = 0;
    $with_eeg_emg = 0;
    $with_eeg_reader = 0;
    $with_code_type = 0;
    $with_department = 0;
    $with_phs_status = 0;
    $with_type_or = 0;
    $with_age_dept = 0;
    $with_membership_category=0;
    $with_type_staff = 0;
    $with_type_stock = 0;
    $limit = '50';
    $with_ipbm_patient = 0 ; #Added by Matsuuu 02052018
    $with_mode_discharge = 0 ;
    $with_area_type2 = 0; #Added by Matsuuu 06202019
    $with_encoder = 0;
    if (count($paramsarr)){
        while (list($key,$val) = each($paramsarr))  {
            $val_arr = explode("--", trim($val));
            
            $id = $val_arr[0];
            $value = $val_arr[1];
            
            $param_id = substr($id, 6);

            switch($param_id){
                case 'encoder':
                    $with_encoder = 1;
                    $encoder = $value;
                break;
            }

            switch($param_id){
                case 'PSY_mr_encoder':
                    $with_encoder = 1;
                    $encoder = $value;
                break;
            }
            

            //switch block added by Nick 07-17-2014
            switch($param_id){
                case 'billing_insurance':
                    $billing_insurance = $value;
                break;
                case 'billing_status':
                    $billing_status = $value;
                break;
                case 'billing_encoder':
                    $billing_encoder = $value;
                break;
                case 'billing_category':
                    $billing_category = $value;
                break;
                case 'casetype_confinement':
                    $casetype_confinement = $value;
                break;
            }
            //cashier monthly collection (PARAMETER)
            //Added by: Borj
            switch($param_id){
                case 'cr_type':
                    $cr_type= $value;

                    if(!isset($cr_type) AND $cr_type=='all'){
                        $cr_header = 'MONTHLY COLLECTION (ALL)';
                    }elseif($value=='affiliation'){
                        $cr_header = 'MONTHLY COLLECTION (AFFILIATION)';
                    }elseif($value=='ctscan'){
                        $cr_header = 'MONTHLY COLLECTION (CT SCAN)';
                    }elseif($value=='hi'){
                        $cr_header = 'MONTHLY COLLECTION (HOSPITAL OPERATIONS INCOME)';
                    }elseif($value=='mri'){
                        $cr_header = 'MONTHLY COLLECTION (MRI)';
                    }elseif($value=='payw'){
                        $cr_header = 'MONTHLY COLLECTION (PAY WARD)';
                    }else{
                        $cr_header = 'MONTHLY COLLECTION (ALL)';
                    }
                break;

                case 'cr_encoder':
                    $cr_encoder = $value;
                    if($value=='all' ){
                        $cr_encoder = 'ALL encoders';
                    }
                break;

                case 'cr_start_from':
                    $cr_start_from= $value;
                break;

                case 'cr_start_to':
                    $cr_start_to= $value;
                break;
                
                case 'cr_or_from':
                    $cr_or_from= $value;
                break;

                case 'cr_or_to':
                    $cr_or_to= $value;
                break;
              case 'phar_encoder':
                    $phar_encoder= $value;
                                        if($value=='all' ){
                        $phar_encoder = 'ALL encoders';
                    }
                break;
                                case 'dept_ward':
                    $dept_ward= $value;
                break;

                
                
            }
            //end

            // added by fritz 07/15/19
            if ($param_id=='type_stock'){
                $with_type_stock = 1;
                if ($value=='fs'){
                    $stock_title = "FORWARD STOCKING";
                    $sql_type_stock = " AND prod.is_fs = 1";    
                }elseif ($value=='rg'){
                    $stock_title = "REGULAR ITEM";
                    $sql_type_stock = " AND prod.is_fs = 0";    
                }else{
                    $stock_title = "ALL TYPE OF STOCK";
                    $sql_type_stock = "";
                }
                
            }

            // added by carriane 01/17/19
            switch ($param_id) {
                case 'bb_blood_group':
                    $bb_group = $value;
                    break;
                case 'bb_expiry_date':
                    $bb_exp_date = $value;
                    break;
                case 'bb_component':
                    $blood_component = $value;
                    break;
                case 'bb_source':
                    $blood_source = $value;
                    break;
                case 'bb_encoder':
                    $blood_encoder = $value;
                    break;
                case 'bb_encoder_opcr':
                    $blood_encoder = $value;
                    break;
                case 'bb_donor_unit':
                    $bb_donorunit = $value;
                    break;
            }
            
            // added by carriane 03/26/19
            if($param_id=='claim_status')
                $PHIC_claims_status = $value;
            // end carriane

            #IHOMP STAFF
            if($param_id=='ihomp_orientation')
                $km_ihomp = $value;
            
            #Modules
            if($param_id=='module_orientation'){
                $mod_orient = $value;
            }
            #for beginning census
            if($param_id=='beg_census'){
                $with_beg_census = 1;
                $initial_census = $value;
            }
            
            #for no of holidays
            if($param_id=='no_holidays'){
                $with_no_holidays = 1;
                $no_holidays = $value;
            }
            
            # based date for report period
            if ($param_id=='time_from'){
                $param_time_from = $value;
            }
            if ($param_id=='time_to'){
                $param_time_to = $value;
            }

            # based date for report period
            if ($param_id=='date_based'){
                $with_date_based = 1;
                if ($value=='admission'){
                    $date_based = 'e.admission_dt';
                    $date_based_label = 'Based on Admission Date';
                    $psy_patient_type = IPBM_IPD;
                }elseif ($value=='consultation'){
                    $date_based = 'e.encounter_date';
                    $date_based_label = 'Based on Consultation Date';
                    $psy_patient_type = IPBM_OPD;
                }elseif ($value=='discharged'){
                    $date_based = 'e.discharge_date';
                    $date_based_label = 'Based on Discharged Date';
                    $psy_patient_type = IPBM_patient_type;
                }
            }

            if ($param_id=='psy_date_based' || $param_id=='psy_date_based3'){
                $with_date_based = 1;
                if ($value=='admission'){
                    $date_based = 'e.admission_dt';
                    $date_based_label = 'Admission Date';
                    $psy_patienttype_mortality = IPBM_IPD;
                }elseif ($value=='consultation'){
                    $date_based = 'e.encounter_date';
                    $date_based_label = 'Consultation Date';
                    $psy_patienttype_mortality = IPBM_OPD;
                }elseif ($value=='discharged'){
                    $date_based = 'e.discharge_date';
                    $date_based_label = 'Discharged Date';
                    $psy_patienttype_mortality = IPBM_patient_type;
                }else{
                    $date_based = 'e.admission_dt';
                    $date_based_label = 'Admission Date';
                    $psy_patienttype_mortality = IPBM_IPD;
                }
            }


            // added by Kenneth 09/18/2016
            if ($param_id=='membership_category'){
                $with_membership_category = 1;
                $membership_categories = explode("__", trim($value));
                $mem_cats="";
                // 
                if(in_array("all", $membership_categories)){
                    $mem_cats="all";
                    $mem_cats_details="All";
                }
                else{
                    $mem_cats="'".implode("','",$membership_categories)."'";
                    $sql = "SELECT memcategory_desc AS namedesc FROM seg_memcategory WHERE memcategory_code IN (".$mem_cats.")";
                    $mem_cat_arr = $db->GetAll($sql);
                    $arr = array_map(function($el){ return $el['namedesc']; }, $mem_cat_arr);
                    $mem_cats_details = implode(', ', $arr);
                }
                
                #$report_title = $db->GetOne($sql);               
            }
              if ($param_id=='mem_category'){
                $with_membership_category = 1;
                $membership_categories = explode("__", trim($value));
                $mem_cats="";
                // 
                if(in_array("all", $membership_categories)){
                    $mem_cats="all";
                    $mem_cats_details="All";
                }
                else{
                    $mem_cats="'".implode("','",$membership_categories)."'";
                    $sql = "SELECT memcategory_desc AS namedesc FROM seg_memcategory WHERE memcategory_code IN (".$mem_cats.")";
                    $mem_cat_arr = $db->GetAll($sql);
                    $arr = array_map(function($el){ return $el['namedesc']; }, $mem_cat_arr);
                    $mem_cats_details = implode(', ', $arr);
                }

                #$report_title = $db->GetOne($sql);               
            }


            //added by kenneth 04-28-16
            if ($param_id=='delivery_type'){
                $with_delivery_type = 1;
                if ($value=='normal'){
                    $delivery_type = "SELECT icd_10 FROM seg_icd_10_deliveries WHERE id='forceps' OR id='normal' OR id='vacuum'";
                    $delivery_type_label = 'Normal Delivery';
                }elseif ($value=='ceasarian'){
                    $delivery_type = "SELECT icd_10 FROM seg_icd_10_deliveries WHERE id='breech' OR id='caesarian'";
                    $delivery_type_label = 'Caesarian Delivery';
                }
            }
            if ($param_id=='discharge_days'){
                $with_discharge_days = 1;
                if ($value=='within1'){
                    $discharge_days = '<1';
                    $discharge_days_label = 'Discharged within 1 day upon admission';
                }elseif ($value=='within3'){
                    $discharge_days = '<4';
                    $discharge_days_label = 'Discharged within 3 days upon admission';
                }elseif ($value=='onwards4'){
                    $discharge_days = '>=4';
                    $discharge_days_label = 'Discharged 4 days and onward upon admission';
                }
            }
            if($param_id=='base_date' || $param_id=='PSY_date_based2'){
                if($value=='encounter'){
                    $base_date = $value;
                }
                else if($value=='discharge'){
                    $base_date = $value;
                }
            }
            //end kenneth
            # hours of death
            #'all-All','less48H-Less Than 48 Hours','48Handup-48 Hours and up'
            if ($param_id=='death_hours'){
                $with_death_hours = 1;
                if ($value=='all'){
                    $cond_death_hours = "";
                    $death_hours_label = 'All Deaths';
                }elseif ($value=='less48H'){
                    $cond_death_hours = "AND (DATEDIFF(
                                           (IF(p.death_date='0000-00-00',e.discharge_date, p.death_date)),           
                                            DATE(e.admission_dt))<2)";
                    $death_hours_label = 'Deaths Less Than 48 Hours';
                }elseif ($value=='48Handup'){
                    $cond_death_hours = "AND (DATEDIFF(
                                           (IF(p.death_date='0000-00-00',e.discharge_date, p.death_date)),           
                                            DATE(e.admission_dt))>=2)";
                    $death_hours_label = 'Deaths with 48 Hours and up';
                }
            }
            
            #patient type
            if ($param_id=='patienttype'){
                $with_ptype = 1;
                if ($value=='all'){
                    $patient_type = '1,2,3,4,6';
                    $patient_type_label = "ALL PATIENTS";            
                }elseif ($value=='ipd'){
                    $patient_type = '3,4';            
                    $patient_type_label = "INPATIENTS";            
                }elseif ($value=='er'){
                    $patient_type = '1';
                    $patient_type_label = "ER PATIENTS";            
                }elseif ($value=='opd'){
                    $patient_type = '2';     
                    $patient_type_label = "OUTPATIENTS";                       
                }
                elseif($value=='hssc'){
                    $patient_type = '6';
                    $patient_type_label = "HSSC PATIENTS";
                }else{
                    #walkin
                    $patient_type = '0';      
                    $patient_type_label = "WALKIN PATIENTS";                          
                }    
            }


            if ($param_id=='patienttype_CPS'){
                $with_ptype = 1;
                if ($value=='all'){
                    $patient_type = '1,2,3,4,6,8';
                    $patient_type_label = "ALL PATIENTS";            
                }elseif ($value=='ipd'){
                    $patient_type = '3,4';            
                    $patient_type_label = "INPATIENTS";            
                }elseif ($value=='er'){
                    $patient_type = '1';
                    $patient_type_label = "ER PATIENTS";            
                }elseif ($value=='opd'){
                    $patient_type = '2';     
                    $patient_type_label = "OUTPATIENTS";                       
                }
                elseif($value=='hssc'){
                    $patient_type = '6';
                    $patient_type_label = "HSSC PATIENTS";
                }elseif($value=='dialysis'){
                    $patient_type = '5';
                    $patient_type_label = "DIALYSIS";
                }elseif($value=='wellbaby'){
                    $patient_type = '12';
                    $patient_type_label = "WELL BABY";
                }elseif($value == 'walkin'){
                    #walkin
                    $patient_type = '8';      
                    $patient_type_label = "WALKIN PATIENTS";                          
                }    
            }

            if ($param_id=='test_type_CPS'){
                 $test_type_CPS = $value;
            }
            
            #patient type 2
            if($param_id == 'patient_type'){
                $with_ptype = 1;
                switch($value){
                    case 'all':
                        $patient_type = '1,2,3,4';
                        $patient_type_label = "ALL PATIENTS";
                        break;
                    case 'ipd':
                        $patient_type = '3,4';
                        $patient_type_label = "INPATIENTS";
                        break;
                    case 'er':
                        $patient_type = '1';
                        $patient_type_label = "ER PATIENTS";
                        break;
                    case 'opd':
                        $patient_type = '2';
                        $patient_type_label = "OUTPATIENTS";
                        break;
                    case 'walkin':
                        $patient_type = '8';
                        $patient_type_label = "WALKIN";
                        break;
                    case 'ipbmipd':
                        $patient_type = IPBMIPD_enc;
                        $patient_type_label = "IPBM - IPD";
                        break;
                    case 'ipbmopd':
                        $patient_type = IPBMOPD_enc;
                        $patient_type_label = "IPBM - OPD";
                        break;

                }
            }


            if($param_id == 'codetype'){
                $with_code_type = 1;
                switch($value){
                    case 'icd':
                        $code_type = 'icd';
                        $code_label = 'ICD CODES';
                        break;
                    case 'icp':
                        $code_type = 'icp';
                        $code_label = 'ICPM CODES';
                        break;
                    default:
                        $code_type = 'all';
                        $code_label = 'All CODES';
                        break;
                }
            }
            
            #icd 10 classification 
            if ($param_id=='type_nr'){
                $with_icd10_class = 1;

                if ($value=='all'){
                   $type_nr = '0,1';
                   $icd_class = "(Primary and Secondary)";
                }elseif ($value=='1'){   
                   $type_nr = '1';
                   $icd_class = "(Primary)"; 
                }else{   
                   $type_nr = '0';
                   $icd_class = "(Secondary)";   
                }    
            }
            
            #icd 10 search
            if ($param_id == 'icd10'){
                $icd_code = $value;
            }

            #icpm search
            if ($param_id == 'icpm'){
                $icd_code = $value;
            }
            
            #minor and major surgery
            if ($param_id=='type_surgery'){
                $with_surgery = 1;
                if ($value=='all'){
                   $cond_surgery = '';
                   $sub_caption = "All Minor and Major Operations";
                }elseif ($value=='minor'){   
                   $cond_surgery = " AND c.rvu < 30 ";
                   $sub_caption = "Minor Operations (below 30 RVU)";
                }elseif ($value=='major'){   
                   $cond_surgery = " AND c.rvu >= 30 ";
                   $sub_caption = "Major Operations (30 and above RVU)";
                }    
            }
            
            #phic membeship classification
            if ($param_id=='classification'){
                $with_phic = 1;
                if ($value=='all'){
                   $cond_classification = '';
                   $sub_caption = "All PHIC and NPHIC Patients";
                   $ins_label = "(PHIC & NPHIC)";
                   $ins_label2 = "PHIC & NPHIC Patients";
                }elseif ($value=='phic'){   
                   $cond_classification = " AND ins.hcare_id=18 ";
                   $sub_caption = "All PHIC Patients";
                   $ins_label = "(PHIC)";
                   $ins_label2 = "PHIC Patients";
                }elseif ($value=='nphic'){   
                   $cond_classification = " AND (ins.hcare_id<>18 OR ins.hcare_id IS NULL) ";
                   $sub_caption = "All NPHIC Patients";
                   $ins_label = "(NPHIC)";
                   $ins_label2 = "NPHIC Patients";
                }
            }

            #mode of report
            if ($param_id=='mode'){
                $with_mode_chart = 1;
                if ($value=='all'){
                   $cond_mode_chart = '';
                   $sub_caption = "All Patients";
                }elseif ($value=='notreceived'){   
                   $cond_mode_chart = " AND (e.received_date IS NULL OR e.received_date = '0000-00-00')";
                   $sub_caption = "All PHIC Patients with Chart that is Not Yet Received";
                }elseif ($value=='received'){   
                   $cond_mode_chart = " AND e.received_date IS NOT NULL AND e.received_date !='0000-00-00'";
                   $sub_caption = "All PHIC Patients with Chart that is already Received";
                }
            }

            #patient's birth type
            if($param_id=='birth_type'){
                if($value=='single'){
                        $birthtype = " AND scb.birth_type='1' ";
                    }elseif($value=='twins'){
                        $birthtype = " AND scb.birth_type='2' ";
                      }elseif($value=='triplets'){
                        $birthtype = " AND scb.birth_type='3' ";
                      }else{
                        $birthtype = " AND scb.birth_type='4' ";
                      }
            }

            #patient's gender
            if($param_id=='patient_sex'){
                  if($value=='male'){
                    $sex = " AND cp.sex='M' ";
                  }elseif($value=='female'){
                    $sex = " AND cp.sex='F' ";
                  }else{
                    $sex = " ";
                  }
            }
            
            #patient's status
            if ($param_id=='status'){
                $with_status = 1;
                if ($value=='all'){
                   $cond_status = '';
                   $sub_caption = "All Patients";
                }elseif ($value=='died'){   
                   $cond_status = " AND ser.result_code IN (4,8,9) ";
                   $sub_caption = "All Died Patients";
                }elseif ($value=='alive'){   
                   $cond_status = " AND ser.result_code NOT IN (4,8,9) ";
                   $sub_caption = "All Still Alive Patients";
                }
            } 

            //lechii
            if($param_id == 'phar_list_encoded'){
                if ($value == 'med_encoded') {
                    $prod_class ='AND prod.prod_class = "M"';
                    $list_med_caption = "LIST OF MEDICINES ENCODED";
                    $list_med_caption1 = "MEDICINE NAME";
                }else if($value == 'sup_encoded'){
                    $list_med_caption = "LIST OF SUPPLIES ENCODED";
                    $list_med_caption1 = "SUPPLIES";
                    $prod_class ='AND prod.prod_class = "S"';
                }else{
                    $list_med_caption = "LIST OF MEDICINES AND SUPPLIES ENCODED";
                    $list_med_caption1 = "MEDICINE NAME / \n SUPPLIES";
                    $prod_class ='';
                }
            }

            if($param_id == 'phar_charge_type'){
                if ($value == 'all') {
                    $patient_charge_type = " ";
                    $list_charge_caption = "ALL PATIENT CHARGE TYPE";
                }else{
                    $patient_charge_type = "AND o.charge_type = '".$value."'";
                    if($charge = $objInfo->getChargeName($value)){
                        $list_charge_caption = $charge['charge_name'];
                    }
                }
            }

            
            # Added by Gervie 03/01/2016
            if($param_id == 'department') {
                $with_department = 1;
                $dept_nr = $value;
            }

            # added by carriane 10/25/17
            if($param_id == 'er_sort_name') {
                $sort_by_name = 1;
                $sort = $value;
            } 
            
            #for PHS Status
            if ($param_id == 'emp'){
                $with_phs_status = 1;
                switch($value){
                    case 'all':
                        $phs_status = 'all';
                        $phs_label = 'ALL';
                        break;
                    case 'active':
                        $phs_status = 'active';
                        $phs_label = 'ACTIVE';
                        break;
                    case 'inactive':
                        $phs_status = 'inactive';
                        $phs_label = 'INACTIVE';
                        break;
                }
            }

            if ($param_id == 'dependent'){
                $with_phs_status = 1;
                switch($value){
                    case 'all':
                        $phs_status = 'all';
                        $phs_label = 'ALL';
                        break;
                    case 'active':
                        $phs_status = 'active';
                        $phs_label = 'ACTIVE';
                        break;
                    case 'inactive':
                        $phs_status = 'inactive';
                        $phs_label = 'INACTIVE/DELETED';
                        break;
                }
            }
            
            #for dept or clinic
            if ($param_id=='dept'){
                $with_dept = 1;
                switch($value){
                    case 'dental' :
                                $dept_label = "Dental";
                                $dept_list = '134';
                                break;
                                
                    case 'derma' :
                                $dept_label = "Dermatology";
                                $dept_list = '116';
                                break;            
                                
                    case 'ent' :
                                $dept_label = "ENT-HNS";
                                $dept_list = '136';
                                break;            
                                
                    case 'famed' :
                                $dept_label = "Family Medicine";
                                $dept_list = '133';
                                break;            
                                
                    case 'gyne' :
                                $dept_label = "Gynecology";
                                $dept_list = '124';
                                break;            
                                
                    case 'im' :
                                $dept_label = "Internal Medicine";
                                $dept_list = '154,104';
                                break;                        
                                
                    case 'med' :
                                $dept_label = "Medicines (Family Medicine and Internal Medicine)";
                                $dept_list = '104';
                                break;                        
                             
                    case 'ob' :
                                $dept_label = "Obstetrics";
                                $dept_list = '139';
                                break;            
                                
                    case 'optha' :
                                $dept_label = "Ophthalmology";
                                $dept_list = '131';
                                break;            
                                
                    case 'ortho' :
                                $dept_label = "Orthopedics";
                                $dept_list = '141';
                                break;            
                    case 'pedia' :
                                $dept_label = "Pediatrics";
                                $dept_list = '125';
                                break;                                    
                                
                    case 'surgery' :
                                $dept_label = "Surgery";
                                $dept_list = '117';
                                break;         
                    default :
                                $dept_label = "All Department";
                                $dept_list = '';
                                break;                                                            
                     
                }                                     
                 
                 if ($value){
                    $enc_dept_cond = " AND (e.current_dept_nr IN ($dept_list) \n".
                                        " OR e.current_dept_nr IN ( \n".
                                            " SELECT nr FROM care_department AS d WHERE d.parent_dept_nr IN ($dept_list))) ";                             
                 
                    $census_dept_cond = " AND IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN ($dept_list) ";
                 }else{
                    $enc_dept_cond = " ";
                    $census_dept_cond = " "; 
                 }                                                      
                
                
                #for mortality tabulation code
                if ($value=='pedia'){
                    $table_tab_code = 'seg_icd_10_mortality_pedia_condensed_tabular';
                }else{
                    $table_tab_code = 'seg_icd_10_mortality_condensed_tabular';
                }
                
                            
            }
            
            # for demographic area
            if ($param_id=='location'){
                $with_area = 1;
                $with_location = 1;
                $field_with_municity = 0;
                if ($value=='all'){
                    $loc_area = 'All from Region XI excluding Davao del Sur';
                    $loc_cond = " AND sr.region_name='Region XI' \n
                                   AND sp.prov_name!='DAVAO DEL SUR' \n"; 
                    $field_with_municity = 0;               
                }elseif ($value=='within'){
                    $loc_area = 'Within Davao Del Sur (Davao City is included)';
                    $loc_cond = " AND sr.region_name='Region XI'
                                  AND sp.prov_name='DAVAO DEL SUR' \n";
                    $field_with_municity = 1;              
                }elseif ($value=='withinexcept'){
                    $loc_area = 'Within Davao Del Sur (except Davao City)';
                    $loc_cond = " AND sr.region_name='Region XI'
                                  AND sp.prov_name='DAVAO DEL SUR' 
                                  AND mun.mun_name <> 'DAVAO CITY' \n";
                    $field_with_municity = 1;              
                }elseif ($value=='withincity'){
                    $loc_area = 'Within Davao City';
                    $loc_cond = " AND sr.region_name='Region XI'
                                  AND sp.prov_name='DAVAO DEL SUR' 
                                  AND mun.mun_name = 'DAVAO CITY' \n";                                                                          
                    $field_with_municity = 1;              
                }elseif ($value=='outside'){
                    $loc_area = 'Outside Region XI';
                    $loc_cond = " AND sr.region_name!='Region XI' \n";
                    $field_with_municity = 0;
                }elseif ($value=='both'){
                    #all region
                    $loc_area = 'Within and Outside of Region XI';
                    $loc_cond = " ";
                    $field_with_municity = 0;
                }
            }
            
            #for barangay
            if ($param_id=='brgynr'){
                $with_area = 1;
                $with_brgy = 1;
                $sql_brgy = "SELECT b.brgy_name, b.mun_nr, b.CODE AS brgy_code
                             FROM seg_barangays b
                             WHERE b.brgy_nr=".$db->qstr($value);             
                                
                $row = $db->GetRow($sql_brgy);
                $brgy_name = trim($row['brgy_name']);
                
                $brgy_area = $brgy_name;
                
                #if long int and length = 9
                if ((strlen($brgy_code)==9) && (is_numeric($brgy_code)))
                    $brgy_cond = " AND sb.CODE = '$brgy_code' \n";
                else
                    $brgy_cond = " AND sb.brgy_nr = ".$db->qstr($value)." \n";
            } 
               
            #for municity
            if ($param_id=='munnr'){
                $with_area = 1;
                $with_mun = 1;
                $sql_mun = "SELECT m.mun_name, m.CODE AS mun_code, m.mun_nr
                             FROM seg_municity m 
                             WHERE m.mun_nr=".$db->qstr($value);             
                                
                $row = $db->GetRow($sql_mun);
                $mun_name = trim($row['mun_name']);
                $mun_code = trim($row['mun_code']);
                
                $mun_area = $mun_name;
                
                #if long int and length = 9
                if ((strlen($mun_code)==9) && (is_numeric($mun_code)))
                    $mun_cond = " AND sm.CODE = '$mun_code' \n";
                else
                    $mun_cond = " AND sm.mun_nr = ".$db->qstr($value)." \n";
            }
            #for province
            if ($param_id=='provnr'){
                $with_area = 1;
                $prov = 1;
                $sql_prov = "SELECT p.prov_name, p.CODE AS prov_code, p.prov_nr
                             FROM seg_provinces p 
                             WHERE p.prov_nr=".$db->qstr($value);             
                                
                $row = $db->GetRow($sql_prov);
                $prov_name = trim($row['prov_name']);
                $prov_code = trim($row['prov_code']);
                
                $prov_area = $prov_name;
                
                #if long int and length = 9
                if ((strlen($prov_code)==9) && (is_numeric($prov_code)))
                    $prov_cond = " AND sp.CODE = '$prov_code' \n";
                else
                    $prov_cond = " AND sp.prov_nr = ".$db->qstr($value)." \n";
            }

            /*
             * Added by syboy 05/28/2015
             * For blood transaction type 
             * All - received_date, routine - is_urgent=0, stat - is_urgent=1
             * Red Cell Products
             */ 
            
            if ($param_id=='bb_trxnc'){
                $with_bb_trxnc = 1;

                if ($value=='all'){
                    $bb_based_datec = 'sbrd.received_date';
                    $bb_based_datecc = 'sbrd.is_urgents IN (1,0)';
                    $transactionc ='ALL';

                }elseif ($value=='routine'){
                    $bb_based_datec = 'sbrd.received_date';
                    $bb_based_datecc = 'sbrd.is_urgents= 0';
                    $transactionc ='ROUTINE';

                }elseif ($value=='stat'){
                    $bb_based_datec = 'sbrd.received_date';
                    $bb_based_datecc = 'sbrd.is_urgents= 1';
                    $transactionc ='STAT';

                }else{
                    #default
                    $bb_based_datec = 'sbrd.received_date';
                    $bb_based_datecc = 'sbrd.is_urgents IN (1,0)';
                    $transactionc ='ALL';
                }   

            }
            // end

            #
            if($param_id=="major_department"){
                $department = $value;
            }




            
            if($param_id=='HSM_personnel'){
                $HSM_biller = $value;
            }

            #for blood transaction type : 
            #deposited - received date, crossmatched - date done, transfused - issuance date
            if ($param_id=='bb_trxn'){
                $with_bb_trxn = 1;

                if ($value=='deposited'){
                    $bb_based_date = 'h.serv_dt';
                    $transaction = 'DEPOSITED';
                }elseif ($value=='crossmatched'){
                    $bb_based_date = 's.done_date';
                    $transaction = 'CROSSMATCHED';
                }elseif ($value=='transfused'){
                    $bb_based_date = 's.issuance_date';
                    $transaction = 'TRANSFUSED';
                }else{
                    #default
                    $bb_based_date = 'd.received_date';
                    $transaction = 'DEPOSITED';
                }   

            }

            #---- start radiology
            if ($param_id=='radio_alphabetical'){
                if ($value=='1')
                    $orderby = " ORDER BY p.name_last, p.name_first, p.name_middle, h.request_time";
                elseif ($value=='2')
                    $orderby = " ORDER BY h.request_date, h.request_time, p.name_last, p.name_first, p.name_middle";
            }

            if ($param_id=='radio_doctor'){
                if ($value!='all'){
                    $doctor_cond = "AND CONCAT(dr.con_doctor_nr,',', dr.sen_doctor_nr,',', dr.jun_doctor_nr) LIKE '%".$value."%' ";
                }
            }

            if ($param_id=='radio_pattype'){
                if ($value=='0')
                    $enc_type = "";
                elseif ($value=='1')
                    $enc_type = "AND encounter_type IN (1)";
                elseif ($value=='2')
                    $enc_type = "AND encounter_type IN (3,4)";
                elseif ($value=='3')
                    $enc_type = "AND encounter_type IN (2)";
                elseif ($value=='4')
                    $enc_type = "AND encounter_type IS NULL";
                elseif ($value=='5')
                    $enc_type = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
                elseif ($value=='6')
                    $enc_type = "AND encounter_type IN (1,3,4)";
                elseif ($value=='7')
                    $enc_type = "AND encounter_type IN (6)";
            }
            if($param_id=='rad_chargetype'){
                $rad_charge_type = $value;
                if(!empty($value)){
                     $rad_sql = "AND ctrr.`request_flag`=".$db->qstr($value);
                }
                else{
                    $rad_sql ="";
                }
              
            }

            if($param_id=='alpha'){
                $order = $value;                
            }
            if($param_id=='iso'){
                $footer = $value;
            }
             
            if ($param_id=='radio_radtech'){
                if ($value!='all'){
                    $radtech_cond = " AND d.rad_tech= '".$value."'";
                }
            }

            if ($param_id=='radio_section'){
                $group_cond = " AND g.department_nr='".$value."'";
            }

            if ($param_id=='radio_status'){

                if ($value!='all'){
                    if ($value=='1')
                        $is_served = 1;
                    else if ($value=='2')
                        $is_served = 0;
                    $sql_status = " AND d.is_served= '".$is_served."'";
                }
            }

            if ($param_id=='radio_type'){
                #'1-Without Results','2-Initial Results','3-Official Results'
                if ($value=='1'){
                    $sql_type = " AND f.batch_nr IS NULL ";    
                }elseif ($value=='2'){
                    $sql_type = " AND (f.batch_nr AND d.STATUS='pending') ";
                }elseif ($value=='3'){
                    $sql_type = " AND (f.batch_nr AND d.STATUS='done') ";
                }
            }
            #----end radiology

            #area type
            if ($param_id=='area'){
                $with_area_type = 1;
                if ($value=='ipd'){

                   #query for Discharges based on Accommodation 
                   $query_sub_accom = "SELECT d.name_formal AS Type_Of_Service,
                                    SUM(CASE WHEN ((i.hcare_id IS NULL) OR (i.hcare_id<>18)) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_non_phic, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND (em.memcategory_id IN (".PHIC_MEMBER.") OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS pay_phic_memdep, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND em.memcategory_id IN (".PHIC_INDIGENT.") THEN 1 ELSE 0 END) AS pay_phic_indigent, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND em.memcategory_id IN (".PHIC_OWWA.") THEN 1 ELSE 0 END) AS pay_phic_owwa, 
                                    SUM(CASE WHEN (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_total,

                                    SUM(CASE WHEN ((i.hcare_id IS NULL) OR (i.hcare_id<>18)) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_non_phic, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND (em.memcategory_id IN (".PHIC_MEMBER.") OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS charity_phic_memdep, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND em.memcategory_id IN (".PHIC_INDIGENT.") THEN 1 ELSE 0 END) AS charity_phic_indigent, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND em.memcategory_id IN (".PHIC_OWWA.") THEN 1 ELSE 0 END) AS charity_phic_owwa, 
                                    SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_total,
                                    SUM(CASE WHEN 1 THEN 1 ELSE 0 END) AS total,
                                    SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_len_stay
                                    FROM care_department AS d
                                    INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                    INNER JOIN care_person AS cp ON cp.pid = e.pid
                                    LEFT JOIN care_ward AS w ON w.nr = IF(fn_get_encounter_location_billing (e.encounter_nr),fn_get_encounter_location_billing (e.encounter_nr),e.current_ward_nr) 
                                    LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
                                    #LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
                                    LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                                    LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
                                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                    AND e.discharge_date IS NOT NULL
                                    AND e.encounter_type IN "  . (($dept_nr == IPBM_DEP) ? "(".IPBMIPD_enc.")" : "(".ER_ADM.",".OPD_ADM.")");
                    
                   #query for discharges based on result of treatment                 
                   $query_sub_result = "SELECT d.name_formal AS Type_Of_Service,
                                    SUM(CASE WHEN (sd.disp_code IN (1,6) AND (sr.result_code NOT IN (4,8,9,10) OR sr.result_code IS NULL)) THEN 1 ELSE 0 END) AS disp_admit_opd,
                                    SUM(CASE WHEN (sd.disp_code IN (2,7) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_disch,
                                    SUM(CASE WHEN (sd.disp_code IN (4, 9) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_hama,
                                    SUM(CASE WHEN (sd.disp_code IN (5, 10) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_absc,
                                    SUM(CASE WHEN (sd.disp_code IN (3,8) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_trans,
                                    SUM(CASE WHEN (sd.disp_code IS NULL ) THEN 1 ELSE 0 END) AS disp_none, 
                                    SUM(CASE WHEN (sr.result_code IN (1,5)) THEN 1 ELSE 0 END) AS total_rec,
                                    SUM(CASE WHEN (sr.result_code IN (2,6)) THEN 1 ELSE 0 END) AS total_imp,
                                    SUM(CASE WHEN (sr.result_code IN (3,7)) THEN 1 ELSE 0 END) AS total_unimp,
                                    SUM(CASE WHEN (sr.result_code IS NULL) THEN 1 ELSE 0 END) AS total_noresult,
                                    SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                        AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                        DATE(e.admission_dt))<2)) THEN 1 ELSE 0 END) AS deathbelow48,
                                    SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                        AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                        DATE(e.admission_dt))>=2)) THEN 1 ELSE 0 END) AS deathabove48
                                    FROM care_department AS d
                                    INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                    LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                                    LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                                    INNER JOIN care_person AS cp ON cp.pid = e.pid
                                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                    AND e.discharge_date IS NOT NULL
                                    AND e.encounter_type IN " . (($dept_nr == IPBM_DEP) ? "(13)" : "(3,4)");
                                    
                   $area_type = "Inpatient";
                   $column_name_disp = "No Disp";
                   $column_name_ave = "Admission";
                   $ave_patient_type = "3,4";
                   $ave_based_date = "e.admission_dt";
                }elseif ($value=='er'){   
                   #query for Discharges based on Accommodation  
                   $query_sub_accom = "SELECT d.name_formal AS Type_Of_Service,
                                        SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) THEN 1 ELSE 0 END) AS pay_non_phic,
                                        SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (".PHIC_MEMBER.") OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS pay_phic_memdep,
                                        SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (".PHIC_INDIGENT.") THEN 1 ELSE 0 END) AS pay_phic_indigent,
                                        SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (".PHIC_OWWA.") THEN 1 ELSE 0 END) AS pay_phic_owwa,
                                        0 AS charity_non_phic,
                                        0 AS charity_phic_memdep,
                                        0 AS charity_phic_indigent,
                                        0 AS charity_phic_owwa,
                                        SUM(DATEDIFF(e.discharge_date,e.encounter_date)) AS total_len_stay
                                        FROM care_department AS d
                                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                        LEFT JOIN care_ward AS w ON w.nr = IF(fn_get_encounter_location_billing (e.encounter_nr),fn_get_encounter_location_billing (e.encounter_nr),e.current_ward_nr) 
                                        LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
                                        #LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
                                        LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                                        LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
                                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                        AND e.discharge_date IS NOT NULL
                                        AND e.encounter_type=1 AND e.encounter_class_nr=1";
                   #query for discharges based on result of treatment                                  
                   $query_sub_result = "SELECT d.name_formal AS Type_Of_Service,
                                        SUM(CASE WHEN (sd.disp_code IN (1,6) AND (sr.result_code NOT IN (4,8,9,10) OR sr.result_code IS NULL)) THEN 1 ELSE 0 END) AS disp_admit_opd,
                                        SUM(CASE WHEN (sd.disp_code IN (2,7) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_disch, 
                                        SUM(CASE WHEN (sd.disp_code IN (4, 9) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_hama, 
                                        SUM(CASE WHEN (sd.disp_code IN (5, 10) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_absc, 
                                        SUM(CASE WHEN (sd.disp_code IN (3,8) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_trans, 
                                        SUM(CASE WHEN (sd.disp_code IS NULL ) THEN 1 ELSE 0 END) AS disp_none, 
                                        SUM(CASE WHEN (sr.result_code IN (1,5)) THEN 1 ELSE 0 END) AS total_rec,
                                        SUM(CASE WHEN (sr.result_code IN (2,6)) THEN 1 ELSE 0 END) AS total_imp,
                                        SUM(CASE WHEN (sr.result_code IN (3,7)) THEN 1 ELSE 0 END) AS total_unimp,
                                        SUM(CASE WHEN (sr.result_code IS NULL) THEN 1 ELSE 0 END) AS total_noresult,
                                        SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                            AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                            DATE(e.encounter_date))<2)) THEN 1 ELSE 0 END) AS deathbelow48,
                                        SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                            AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                            DATE(e.encounter_date))>=2)) THEN 1 ELSE 0 END) AS deathabove48
                                        FROM care_department AS d
                                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                                        INNER JOIN care_person AS cp ON cp.pid = e.pid
                                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                        AND e.discharge_date IS NOT NULL
                                        AND e.encounter_type=1 AND e.encounter_class_nr=1";
                   
                   $area_type = "ER Patient";
                   $column_name_disp = "Admitted";
                   $column_name_ave = "ER Consulation";
                   $ave_patient_type = "1";
                   $ave_based_date = "e.encounter_date";
                }    
            }

            if($param_id == 'index_lvl1'){
                $index_lvl_1 = " AND idx.level_01 = $value";
            }

            if($param_id == 'index_lvl2'){
                $index_lvl_2 = " AND idx.level_02 = $value";
            }

            if($param_id == 'index_lvl3'){
                $index_lvl_3 = " AND idx.level_03 = $value";
            }

            if($param_id == 'index_lvl4'){
                $index_lvl_4 = " AND idx.level_04 = $value";
            }

            #ipd_opd
            if($param_id == "ipd_opd")
            {
                if($value == "all") {
                    $ptype = '2,3,4';
                    $ptype_label = "All Patients";
                }
                elseif($value == "ipd") {
                    $ptype = '3,4';
                    $ptype_label = "Inpatients";
                }
                elseif($value == "opd") {
                    $ptype = '2';
                    $ptype_label = "Outpatients";
                }
            }

            #diag_class
            if($param_id == 'diag_class')
            {
                if($value == '1') {
                    $diag_type = '1';
                    $diag_label = 'For Primary Diagnosis';
                }
                elseif($value == '0') {
                    $diag_type = '0';
                    $diag_label = 'For Secondary Diagnosis';
                }
            }

            if ($param_id == 'type_or'){
                $with_type_or = 1;
                switch($value){
                    case 'cash':
                        $charge_title = 'CASH TRANSMITTAL';
                        $charge_type = 'paid';
                        $or_type = 'OR #';
                        break;
                    case 'cmap':
                        $charge_title = 'MEDICAL ASSISTANCE PROGRAM TRANSMITTAL';
                        $charge_type = 'cmap';
                        $or_type = 'MAP/LGU-FC';
                        break;
                    case 'lingap':
                        $charge_title = 'LINGAP TRANSMITTAL';
                        $charge_type = 'lingap';
                        $or_type = 'LINGAP';
                        break;
                    case 'classd':
                        $charge_title = 'CLASS D TRANSMITTAL';
                        $charge_type = 'charity';
                        $or_type = 'SC/CH/PHS';
                        break;
                    case 'dswd':
                        $charge_title = 'DSWD TRANSMITTAL';
                        $charge_type = 'dswd';
                        $or_type = 'DSWD';
                        break;
                    case 'pcso':
                        $charge_title = 'PCSO TRANSMITTAL';
                        $charge_type = 'pcso';
                        $or_type = 'PCSO';
                        break;
                    case 'master':
                        $charge_title = 'MASTERLIST';
                        $charge_type = 'all';
                        $or_type = 'OR/SC/CH/CMAP-LINGAP';
                        break;
                    default:
                        $charge_title = 'MASTERLIST';
                        $charge_type = 'all';
                        $or_type = 'OR/SC/CH/CMAP-LINGAP';
                        break;
                }
            }

            if($param_id=='consul_insti'){
                if($value=='heart'){
                        $consul_insti = " AND d.name_formal LIKE '%Cardiology%' ";
                        $orientation_header='HEART INSTITUTE';
                        $orientation_column = 'DEPARTMENT';
                    }elseif($value=='cancer'){
                        $consul_insti = " AND d.name_formal LIKE '%Oncology%' ";
                        $orientation_header='CANCER INSTITUTE';
                        $orientation_column = 'DEPARTMENT';
                      }elseif($value=='opdj'){
                        $consul_insti = " AND d.name_formal NOT LIKE '%Cardiology%' AND  d.name_formal NOT LIKE '%Oncology%'";
                        $orientation_header='OPD-JICA';
                        $orientation_column = 'DEPARTMENT';
                      }else{
                        $consul_insti = "  ";
                        $orientation_header='ALL';
                        $orientation_column = 'DEPARTMENT (All)';
                      }
            }

            // Added by Matsuu 07120217
    if($param_id=='lab_pattype'){
        switch($value){
                    case '1':
                       $lab_patient_type= "ER PATIENT";
                       $enctype = " AND encounter_type IN ('1')";
                        break;
                    case '2':
                       $lab_patient_type= "ADMITTED PATIENT";
                       $enctype = " AND encounter_type IN ('3','4' )";
                      break;
                    case '3':
                       $lab_patient_type= "OUTPATIENT";
                       $enctype = "  AND encounter_type IN ('2') AND is_rdu = '0'";
                      break;
                    case '4':
                       $lab_patient_type= "WALK IN";
                       $enctype = " AND encounter_type IS NULL AND is_rdu = '0' ";
                      break;
                    case '5':
                       $lab_patient_type= "OUTPATIENT & WALK IN";
                       $enctype = "AND(encounter_type IN ('2') OR encounter_type IS NULL AND is_rdu = '0')";
                      break;
                    case '6':
                       $lab_patient_type= "RDU'";
                       $enctype = "AND encounter_type IN ('5')";
                      break;
                    default:
                        $lab_patient_type= "All PATIENT";
                        $enctype=" ";
                     break;
                }
    }
    if($param_id=='psy_all_patienttype'){
        $with_ipbm_patient = 1;
        switch ($value) {
            case 'all':
            $patient_type = IPBM_patient_type;
            $patient_type_label = "ALL PATIENTS";
                break;
            case 'opd':
             $patient_type = IPBM_OPD;
            $patient_type_label = "OUTPATIENTS";
                break;
            case 'ipd':
             $patient_type = IPBM_IPD;
             $patient_type_label = "INPATIENTS";
                break;
        }
    }

            if($param_id == 'age_classification')
            {
                $with_age_dept = 1;

                switch($value)
                {
                    case 'pedia':
                        $age_cond = " WHERE t.age <= 18";
                        $classification_dept = "PEDIATRICS DEPARTMENT";
                        break;
                    case 'adult':
                        $age_cond = " WHERE t.age > 18";
                        $classification_dept = "INTERNAL MEDICINE DEPARTMENT";
                        break;
                }
            }

            if($param_id == 'eeg_emg'){
                $with_eeg_emg = 1;
                switch($value){
                    case 'eeg':
                        $con_title = "EEG";
                        $service_code = "EEG%";
                        $mf_disc = 0.75;
                        $pf_disc = 0.25;
                        $mf = "75%";
                        $pf = "25%";
                        $join = " LEFT JOIN seg_lab_eeg_result lr ON lr.refno = ls.refno";
                        break;
                    case 'emg':
                        $con_title = "EMG";
                        $service_code = "EMG%";
                        $mf_disc = 0.60;
                        $pf_disc = 0.40;
                        $mf = "60%";
                        $pf = "40%";
                        $join = "";
                        break;
                }
            }
            
            if($param_id == 'eeg_reader'){
                $with_eeg_reader = 1;
                $eeg_reader = $value;
            }
                 if($param_id=='PHS_type'){
                switch($value){
                    case 'all':
                    $phs_type = 'all';
                    break;
                    case 'employee':
                    $phs_type = 'employee';
                    break;
                    case 'dependent':
                    $phs_type = 'dependent';
                    break;
                }
            }
             if($param_id=='PHS_code'){
                $phs_encoder = $value;
            }
            if($param_id=='billing_categories'){
              $billing_categories = $value;
            }
            #IPBM Parameters
            #Added by Matsuu 02132018
            if($param_id=='psy_patienttype'){
                $with_ipbm_patient = 1;
                $age_label_yr= "y.o.";
                $age_label_m = "m.o.";
                switch ($value) {
                    case 'all':
                    $patient_type_ipbm = IPBM_patient_type;
                    $patient_type_label = "ALL PATIENTS";
                        break;
                    case 'opd':
                     $patient_type_ipbm = IPBM_OPD;
                    $patient_type_label = "OUTPATIENTS";
                        break;
                    case 'ipd':
                     $patient_type_ipbm = IPBM_IPD;
                    $patient_type_label = "INPATIENTS";
                        break;
                }
            }
            #Added by Matsuu 04242018
            if($param_id=='type_personnel'){
                $with_type_staff = 1;
                switch ($value) {
                    case '1':
                       $type_personnel = "staff";
                        break;
                   case '2':
                       $type_personnel ="doctor";
                       break;

                   }

            }
            #Added by Matsuu 10152018
            if($param_id=='type_triage'){
                    $category_triage = "AND stc.roman_id=".$db->qstr($value);
                }
            if($param_id=='er_dept'){
                $attending_dept = "AND e.current_dept_nr=".$db->qstr($value);
                $get_dept = $value;
            }
            #Ended here...
            if($param_id=='type_discharged'){
                $with_mode_discharge = 1;
                $mode_discharged  = $value;
            }
            #Added by Matsuu 06202019
            if($param_id=='psy_area2'){
                $with_area_type2 = 1;
                 switch ($value) {
                    case 'opd':
                     $ave_patient_type_IPBM = IPBM_OPD;
                        break;
                    case 'ipd':
                     $ave_patient_type_IPBM = IPBM_IPD;
                        break;
                }
            }

            if($param_id=='billing_icd'){
                if($value=='J18.99'){
                        $value .= ', Y95';
                }
                $icd_value= $value;
            }
            if($param_id=='billing_icp'){
                 if($value=='J18.99'){
                        $value .= ', Y95';
                }
                $icp_value= $value;
            }

            if($param_id=='gyne_procedures'){
                if($value != 'all'){
                    $procedure_code = "AND cttr.service_code = ".$db->qstr($value);    
                }
                
            }

            if($param_id=='gyne_doctor'){
                 if($value != 'all'){
                    $gyne_doctor = "AND srdp.dr_nr = ".$db->qstr($value);    
                }
                

            }
            

        }
    }
    
    #default
    if (!$with_area_type){
        if ($value=='ipd'){
                   #query for Discharges based on Accommodation 
                   $query_sub_accom = "SELECT d.name_formal AS Type_Of_Service,
                                    SUM(CASE WHEN ((i.hcare_id IS NULL) OR (i.hcare_id<>18)) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_non_phic, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND (em.memcategory_id IN (".PHIC_MEMBER.") OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS pay_phic_memdep, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND em.memcategory_id IN (".PHIC_INDIGENT.") THEN 1 ELSE 0 END) AS pay_phic_indigent, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND em.memcategory_id IN (".PHIC_OWWA.") THEN 1 ELSE 0 END) AS pay_phic_owwa, 
                                    SUM(CASE WHEN (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_total,

                                    SUM(CASE WHEN ((i.hcare_id IS NULL) OR (i.hcare_id<>18)) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_non_phic, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND (em.memcategory_id IN (".PHIC_MEMBER.") OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS charity_phic_memdep, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND em.memcategory_id IN (".PHIC_INDIGENT.") THEN 1 ELSE 0 END) AS charity_phic_indigent, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND em.memcategory_id IN (".PHIC_OWWA.") THEN 1 ELSE 0 END) AS charity_phic_owwa, 
                                    SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_total,
                                    SUM(CASE WHEN 1 THEN 1 ELSE 0 END) AS total,
                                    SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_len_stay
                                    FROM care_department AS d
                                    INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                    INNER JOIN care_person AS cp ON cp.pid = e.pid
                                    LEFT JOIN care_ward AS w ON w.nr = IF(fn_get_encounter_location_billing (e.encounter_nr),fn_get_encounter_location_billing (e.encounter_nr),e.current_ward_nr) 
                                    LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
                                    #LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
                                    LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                                    LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
                                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                    AND e.discharge_date IS NOT NULL
                                    AND e.encounter_type IN (3,4)";
                   
                   #query for discharges based on result of treatment                 
                   $query_sub_result = "SELECT d.name_formal AS Type_Of_Service,
                                    SUM(CASE WHEN (sd.disp_code IN (1,6) AND (sr.result_code NOT IN (4,8,9,10) OR sr.result_code IS NULL)) THEN 1 ELSE 0 END) AS disp_admit_opd,
                                    SUM(CASE WHEN (sd.disp_code IN (2,7) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_disch,
                                    SUM(CASE WHEN (sd.disp_code IN (4, 9) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_hama,
                                    SUM(CASE WHEN (sd.disp_code IN (5, 10) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_absc,
                                    SUM(CASE WHEN (sd.disp_code IN (3,8) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_trans,
                                    SUM(CASE WHEN (sd.disp_code IS NULL ) THEN 1 ELSE 0 END) AS disp_none, 
                                    SUM(CASE WHEN (sr.result_code IN (1,5)) THEN 1 ELSE 0 END) AS total_rec,
                                    SUM(CASE WHEN (sr.result_code IN (2,6)) THEN 1 ELSE 0 END) AS total_imp,
                                    SUM(CASE WHEN (sr.result_code IN (3,7)) THEN 1 ELSE 0 END) AS total_unimp,
                                    SUM(CASE WHEN (sr.result_code IS NULL) THEN 1 ELSE 0 END) AS total_noresult,
                                    SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                        AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                        DATE(e.admission_dt))<2)) THEN 1 ELSE 0 END) AS deathbelow48,
                                    SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                        AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                        DATE(e.admission_dt))>=2)) THEN 1 ELSE 0 END) AS deathabove48
                                    FROM care_department AS d
                                    INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                    LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                                    LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                                    INNER JOIN care_person AS cp ON cp.pid = e.pid
                                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                    AND e.discharge_date IS NOT NULL
                                    AND e.encounter_type IN (3,4)";
                                    
                   $area_type = "Inpatient";
                   $column_name_disp = "No Disp";
                   $column_name_ave = "Admission";
                   $ave_patient_type = "3,4";
                   $ave_based_date = "e.admission_dt";
        }elseif ($value=='er'){   
                   #query for Discharges based on Accommodation  
                   $query_sub_accom = "SELECT d.name_formal AS Type_Of_Service,
                                        SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) THEN 1 ELSE 0 END) AS pay_non_phic,
                                        SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (".PHIC_MEMBER.") OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS pay_phic_memdep,
                                        SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (".PHIC_INDIGENT.") THEN 1 ELSE 0 END) AS pay_phic_indigent,
                                        SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (".PHIC_OWWA.") THEN 1 ELSE 0 END) AS pay_phic_owwa,
                                        0 AS charity_non_phic,
                                        0 AS charity_phic_memdep,
                                        0 AS charity_phic_indigent,
                                        0 AS charity_phic_owwa,
                                        SUM(DATEDIFF(e.discharge_date,e.encounter_date)) AS total_len_stay
                                        FROM care_department AS d
                                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                        LEFT JOIN care_ward AS w ON w.nr = IF(fn_get_encounter_location_billing (e.encounter_nr),fn_get_encounter_location_billing (e.encounter_nr),e.current_ward_nr) 
                                        LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
                                        #LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
                                        LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                                        LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
                                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                        AND e.discharge_date IS NOT NULL
                                        AND e.encounter_type=1 AND e.encounter_class_nr=1";
                   #query for discharges based on result of treatment                                  
                   $query_sub_result = "SELECT d.name_formal AS Type_Of_Service,
                                        SUM(CASE WHEN (sd.disp_code IN (1,6) AND (sr.result_code NOT IN (4,8,9,10) OR sr.result_code IS NULL)) THEN 1 ELSE 0 END) AS disp_admit_opd,
                                        SUM(CASE WHEN (sd.disp_code IN (2,7) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_disch, 
                                        SUM(CASE WHEN (sd.disp_code IN (4, 9) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_hama, 
                                        SUM(CASE WHEN (sd.disp_code IN (5, 10) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_absc, 
                                        SUM(CASE WHEN (sd.disp_code IN (3,8) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_trans, 
                                        SUM(CASE WHEN (sd.disp_code IS NULL ) THEN 1 ELSE 0 END) AS disp_none, 
                                        SUM(CASE WHEN (sr.result_code IN (1,5)) THEN 1 ELSE 0 END) AS total_rec,
                                        SUM(CASE WHEN (sr.result_code IN (2,6)) THEN 1 ELSE 0 END) AS total_imp,
                                        SUM(CASE WHEN (sr.result_code IN (3,7)) THEN 1 ELSE 0 END) AS total_unimp,
                                        SUM(CASE WHEN (sr.result_code IS NULL) THEN 1 ELSE 0 END) AS total_noresult,
                                        SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                            AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                            DATE(e.encounter_date))<2)) THEN 1 ELSE 0 END) AS deathbelow48,
                                        SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                            AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                            DATE(e.encounter_date))>=2)) THEN 1 ELSE 0 END) AS deathabove48
                                        FROM care_department AS d
                                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                                        INNER JOIN care_person AS cp ON cp.pid = e.pid
                                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                        AND e.discharge_date IS NOT NULL
                                        AND e.encounter_type=1 AND e.encounter_class_nr=1";
                   
                   $area_type = "ER Patient";
                   $column_name_disp = "Admitted";
                   $column_name_ave = "ER Consulation";
                   $ave_patient_type = "1";
                   $ave_based_date = "e.encounter_date";
        }else{  
                   #query for Discharges based on Accommodation 
                   $query_sub_accom = "SELECT d.name_formal AS Type_Of_Service,
                                SUM(CASE WHEN ((i.hcare_id IS NULL) OR (i.hcare_id<>18)) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_non_phic, 
                                SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND (em.memcategory_id IN (".PHIC_MEMBER.") OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS pay_phic_memdep, 
                                SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND em.memcategory_id IN (".PHIC_INDIGENT.") THEN 1 ELSE 0 END) AS pay_phic_indigent, 
                                SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND em.memcategory_id IN (".PHIC_OWWA.") THEN 1 ELSE 0 END) AS pay_phic_owwa, 
                                SUM(CASE WHEN (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_total,

                                SUM(CASE WHEN ((i.hcare_id IS NULL) OR (i.hcare_id<>18)) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_non_phic, 
                                SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND (em.memcategory_id IN (".PHIC_MEMBER.") OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS charity_phic_memdep, 
                                SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND em.memcategory_id IN (".PHIC_INDIGENT.") THEN 1 ELSE 0 END) AS charity_phic_indigent, 
                                SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND em.memcategory_id IN (".PHIC_OWWA.") THEN 1 ELSE 0 END) AS charity_phic_owwa, 
                                SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_total,
                                SUM(CASE WHEN 1 THEN 1 ELSE 0 END) AS total,
                                SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_len_stay
                                FROM care_department AS d
                                INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                INNER JOIN care_person AS cp ON cp.pid = e.pid
                                LEFT JOIN care_ward AS w ON w.nr = IF(fn_get_encounter_location_billing (e.encounter_nr),fn_get_encounter_location_billing (e.encounter_nr),e.current_ward_nr) 
                                LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
                                #LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
                                LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                                LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
                                WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                AND e.discharge_date IS NOT NULL
                                AND e.encounter_type IN (3,4)";
                   
                   #query for discharges based on result of treatment                              
                   $query_sub_result = "SELECT d.name_formal AS Type_Of_Service,  
                                                SUM(CASE WHEN (sd.disp_code IN (1,6) AND (sr.result_code NOT IN (4,8,9,10) OR sr.result_code IS NULL)) THEN 1 ELSE 0 END) AS disp_admit_opd,
                                                SUM(CASE WHEN (sd.disp_code IN (2,7) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_disch,
                                                SUM(CASE WHEN (sd.disp_code IN (4, 9) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_hama,
                                                SUM(CASE WHEN (sd.disp_code IN (5, 10) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_absc,
                                                SUM(CASE WHEN (sd.disp_code IN (3,8) AND sr.result_code NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) AS disp_trans,
                                                SUM(CASE WHEN (sd.disp_code IS NULL ) THEN 1 ELSE 0 END) AS disp_none, 
                                                SUM(CASE WHEN (sr.result_code IN (1,5)) THEN 1 ELSE 0 END) AS total_rec,
                                                SUM(CASE WHEN (sr.result_code IN (2,6)) THEN 1 ELSE 0 END) AS total_imp,
                                                SUM(CASE WHEN (sr.result_code IN (3,7)) THEN 1 ELSE 0 END) AS total_unimp,
                                                SUM(CASE WHEN (sr.result_code IS NULL) THEN 1 ELSE 0 END) AS total_noresult,
                                                SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                                    AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                                    DATE(e.admission_dt))<2)) THEN 1 ELSE 0 END) AS deathbelow48,
                                                SUM(CASE WHEN (sr.result_code IN (4,8,9,10)
                                                    AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),
                                                    DATE(e.admission_dt))>=2)) THEN 1 ELSE 0 END) AS deathabove48
                                                FROM care_department AS d
                                                INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                                LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                                                LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                                                INNER JOIN care_person AS cp ON cp.pid = e.pid
                                                WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                                AND e.discharge_date IS NOT NULL
                                                AND e.encounter_type IN (3,4)"; 
                                                             
                   $area_type = "Inpatient";
                   $column_name_disp = "No Disp";
                   $column_name_ave = "Admission";
                   $ave_patient_type = "3,4";
                   $ave_based_date = "e.admission_dt";
        }
    }
    
    
    if(!$patient_type_ipbm){
        $patient_type_ipbm = IPBM_patient_type;
        $patient_type_label = "ALL PATIENTS";
    }


    if (!$with_beg_census){
        $initial_census = 0;    
    }
    
    if (!$with_no_holidays){
        $no_holidays = 0;    
    }
    
    if (!$with_date_based){
        $date_based = 'e.admission_dt';
        $date_based_label = 'Based on Admission Date';
        $psy_patient_type = IPBM_IPD;
        $psy_patienttype_mortality = IPBM_patient_type;
    }
    //added by Kenneth 09/18/2016
    if (!$with_membership_category){
        $mem_cats = 'all';
        $mem_cats_details = "All";
    }
    //added by kenneth 04-28-16
    if (!$with_discharge_days){
        $discharge_days = ">0";
        $discharge_days_label = 'All Discharged';
    }
    if (!$with_delivery_type){
        $delivery_type = "SELECT icd_10 FROM seg_icd_10_deliveries";
        $delivery_type_label = 'Normal and Caesarian Delivery';
    }
    //end kenneth
    if(!$with_death_hours){
        $cond_death_hours = "";
        $death_hours_label = 'All Deaths';
    }
                
/*    if (!$with_ptype){
        $patient_type = '3,4';
        $patient_type_label = "INPATIENTS";
    }*/
    
    if(!$with_code_type){
        $code_type = 'all';
        $code_label = 'ALL CODES';
    }
    
    if (!$with_icd10_class){
        $type_nr = '0,1';
        $icd_class = "(Primary and Secondary)";    
    }
    
    if (!$with_surgery){
        $cond_surgery = '';
        $sub_caption = "All Minor and Major Operations";
    }
    
    if (!$with_phic){
        $cond_classification = '';
        $sub_caption = "All Patients";
        $ins_label = "(PHIC & NPHIC)";
        $ins_label2 = "PHIC & NPHIC Patients";
    }
    
    if(!$with_type_stock){
        $stock_title = "ALL TYPE OF STOCK";
        $sql_type_stock = "";
    }

    if (!$with_mode_chart){
        $cond_mode_chart = '';
        $sub_caption = "All Patients";
    }
    
    if (!$with_status){
        $cond_status = '';
        $sub_caption = "All Patients";
    }
    
    if(!$with_phs_status){
        $phs_status = 'active';
        $phs_label = 'ACTIVE';
    }
    
    if(!$with_department) {
        $dept_nr = '';
    }

    // added by carriane 10/25/17
    if(!$sort_by_name){
        $sort = 0;
    }
    
    if (!$with_dept){
        $dept_label = "All Clinics/Department";
        $enc_dept_cond = '';
        $table_tab_code = 'seg_icd_10_mortality_condensed_tabular';
        $census_dept_cond = " ";
    }

    // updated by carriane 04/24/18 changed to serv_dt
    if (!$with_bb_trxn){
        $bb_based_date = 'h.serv_dt';
        $transaction = 'DEPOSITED, TRANSFUSED, CROSSMATCHED';
    }

    if (!$with_location){
        $loc_area = 'Within Davao Del Sur (Davao City is included)';
        $loc_cond = " AND sr.region_name='Region XI'
                      AND sp.prov_name='DAVAO DEL SUR' \n";
        $field_with_municity = 1;              
    }
    
    if (!$with_area){
        #DEFAULT is DAVAO CITY, DAVAO DEL SUR
        $area = "DAVAO CITY";
        # Davao City and Davao del Sur
        $area_cond = " AND sm.CODE = '112402000' \n
                       AND sp.CODE = '112400000' \n";
        
        #$area_cond = " AND sm.mun_nr = '24' \n
        #               AND sp.prov_nr = '3' \n";               
    }else{
        if ($with_brgy){
            $area = $brgy_area.", ".$mun_area.", ".$prov_area;
            $area_cond = $brgy_cond." \n ".$mun_cond." \n ".$prov_cond;
        }elseif ($with_mun){
            $area = $mun_area.", ".$prov_area;
            $area_cond = $mun_cond." \n ".$prov_cond;
        }elseif ($with_prov){
            $area = $prov_area;
            $area_cond = $prov_cond;
        }elseif($with_location){
            $area = $loc_area;
            $area_cond = $loc_cond;
        }else{
            $area = "DAVAO CITY";
            $area_cond = " AND sm.CODE = '112402000' \n
                           AND sp.CODE = '112400000' \n";
        }
    }
    #Added by Matsuu 06202019
    if(!$with_area_type2){
        $ave_patient_type_IPBM = IPBMIPD_enc;
    }
    #Ended here..
    
    if(!$with_age_dept)
    {
        $age_cond = " WHERE t.age > 18";
        $classification_dept = "INTERNAL MEDICINE DEPARTMENT";
    }

    if(!$with_type_or)
    {
        $charge_title = 'MASTERLIST';
        $charge_type = 'all';
        $or_type = 'OR/SC/CH/CMAP-LINGAP';
    }

    if(!$with_eeg_emg){
        $con_title = "EEG";
        $service_code = "EEG%";
        $mf_disc = 0.75;
        $pf_disc = 0.25;
        $mf = "75%";
        $pf = "25%";
        $join = " LEFT JOIN seg_lab_eeg_result lr ON lr.refno = ls.refno ";
    }
    if(!$with_type_staff){
        $type_personnel="staff";
    }

    if(!$with_mode_discharge){
        $mode_discharged = 'ALL';
    }
    
    
    if ($exclusive_opd_er)
      $date_based = 'e.encounter_date'; 
      
    if ($exclusive_death)
      $date_based = 'p.death_date';      
    
    #get age
    $age_bdate = "(FLOOR(IF(fn_calculate_age(DATE($date_based),p.date_birth),(fn_get_ageyr(DATE($date_based),p.date_birth)),p.age))";
                
    #get age bracket
    //edited by KENTOOT 10-21-2014               
    $age_bracket = "SUM(CASE WHEN (p.fromtemp='1' AND p.sex='m') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS male_days_in,
                    SUM(CASE WHEN (p.fromtemp='1' AND p.sex='f') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS female_days_in,
                    
                    SUM(CASE WHEN (p.fromtemp='0' AND p.sex='m') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS male_days_out,
                    SUM(CASE WHEN (p.fromtemp='0' AND p.sex='f') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS female_days_out,
                      /*    Commented and Modified male_below1 AND female_below1 by Matsuu 11172017 
                    SUM(CASE WHEN p.sex='m' AND (fn_calculate_age(p.date_birth,p.death_date))>0.076 AND (fn_calculate_age(p.date_birth,p.death_date))<1 THEN 1 ELSE 0 END) AS male_below1,
                    SUM(CASE WHEN p.sex='f' AND (fn_calculate_age(p.date_birth,p.death_date))>0.076 AND (fn_calculate_age(p.date_birth,p.death_date))<1 THEN 1 ELSE 0 END) AS female_below1, 
                     */

                    SUM(CASE WHEN p.sex='m' AND (SELECT TIMESTAMPDIFF(YEAR,p.date_birth,e.encounter_date))<1 THEN 1 ELSE 0 END) AS male_below1,
                    SUM(CASE WHEN p.sex='f' AND (SELECT TIMESTAMPDIFF(YEAR,p.date_birth,e.encounter_date))<1 THEN 1 ELSE 0 END) AS female_below1, 

                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 1 AND 4) THEN 1 ELSE 0 END) AS male_1to4, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 1 AND 4) THEN 1 ELSE 0 END) AS female_1to4, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 5 AND 9) THEN 1 ELSE 0 END) AS male_5to9, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 5 AND 9) THEN 1 ELSE 0 END) AS female_5to9, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS male_10to14, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS female_10to14, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS male_15to19, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS female_15to19,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 20 AND 44) THEN 1 ELSE 0 END) AS male_20to44, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 20 AND 44) THEN 1 ELSE 0 END) AS female_20to44, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 45 AND 59) THEN 1 ELSE 0 END) AS male_45to59, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 45 AND 59) THEN 1 ELSE 0 END) AS female_45to59, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate > 59) THEN 1 ELSE 0 END) AS male_60up, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate > 59) THEN 1 ELSE 0 END) AS female_60up,
                    SUM(CASE WHEN p.sex='m' then 1 else 0 end) AS male_total,
                    SUM(CASE WHEN p.sex='f' then 1 else 0 end) AS female_total,
                    SUM(CASE WHEN (p.sex='f' OR p.sex = 'm') then 1 else 0 end) AS total";
    

    //added by KENTOOT 08/27/2014
    $age_bracket_pedia = "SUM(CASE WHEN (p.fromtemp='1' AND p.sex='m') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS male_days_in,
                    SUM(CASE WHEN (p.fromtemp='1' AND p.sex='f') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS female_days_in,
                    SUM(CASE WHEN (p.fromtemp='0' AND p.sex='m') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS male_days_out,
                    SUM(CASE WHEN (p.fromtemp='0' AND p.sex='f') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS female_days_out,
                    SUM(CASE WHEN p.sex='m' AND (fn_calculate_age(p.date_birth,p.death_date))>0.076 AND (fn_calculate_age(p.date_birth,p.death_date))<1 THEN 1 ELSE 0 END) AS male_below1,
                    SUM(CASE WHEN p.sex='f' AND (fn_calculate_age(p.date_birth,p.death_date))>0.076 AND (fn_calculate_age(p.date_birth,p.death_date))<1 THEN 1 ELSE 0 END) AS female_below1, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 1 AND 4) THEN 1 ELSE 0 END) AS male_1to4, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 1 AND 4) THEN 1 ELSE 0 END) AS female_1to4, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 5 AND 9) THEN 1 ELSE 0 END) AS male_5to9, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 5 AND 9) THEN 1 ELSE 0 END) AS female_5to9, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS male_10to14, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS female_10to14, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS male_15to19, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS female_15to19,
                    SUM(CASE WHEN p.sex='m' then 1 else 0 end) AS male_total,
                    SUM(CASE WHEN p.sex='f' then 1 else 0 end) AS female_total,
                    SUM(CASE WHEN (p.sex='f' OR p.sex = 'm') then 1 else 0 end) AS total";

    $field_accommodation_type = "SUM(CASE WHEN (i.hcare_id IS NULL) THEN 1 ELSE 0 END) AS non_phic_total,
                    SUM(CASE WHEN (i.hcare_id IS NULL) AND (w.accomodation_type=".PAY_ACCOMMODATION.") THEN 1 ELSE 0 END) AS non_phic_pay,
                    SUM(CASE WHEN (i.hcare_id IS NULL) AND ((w.accomodation_type=".SERVICE_ACCOMMODATION.") OR (w.accomodation_type IS NULL)) THEN 1 ELSE 0 END) AS non_phic_service,
                    SUM(CASE WHEN (i.hcare_id=".PHIC.") AND em.memcategory_id NOT IN (".OWWA.") THEN 1 ELSE 0 END) AS phic_total,
                    SUM(CASE WHEN (i.hcare_id=".PHIC.") AND em.memcategory_id NOT IN (".OWWA.") AND (w.accomodation_type=".PAY_ACCOMMODATION.") THEN 1 ELSE 0 END) AS phic_pay,
                    SUM(CASE WHEN (i.hcare_id=".PHIC.") AND em.memcategory_id NOT IN (".OWWA.") AND ((w.accomodation_type=".SERVICE_ACCOMMODATION.") OR (w.accomodation_type IS NULL)) THEN 1 ELSE 0 END) AS phic_service,
                    SUM(CASE WHEN ((i.hcare_id<>".PHIC.") AND (i.hcare_id IS NOT NULL)) THEN 1 ELSE 0 END) hmo,
                    SUM(CASE WHEN (i.hcare_id=".PHIC.") AND em.memcategory_id IN (".OWWA.") THEN 1 ELSE 0 END) AS owwa";

    $field_discharge_disposition = "SUM(CASE WHEN (fn_get_encounter_disposition(e.encounter_nr) IN (".DISCHARGED.")) THEN 1 ELSE 0 END) AS discharged,
                    SUM(CASE WHEN (fn_get_encounter_disposition(e.encounter_nr) IN (".TRANSFERRED.")) THEN 1 ELSE 0 END) AS transferred,
                    SUM(CASE WHEN (fn_get_encounter_disposition(e.encounter_nr) IN (".HAMA.")) THEN 1 ELSE 0 END) AS hama,
                    SUM(CASE WHEN (fn_get_encounter_disposition(e.encounter_nr) IN (".ABSCONDED.")) THEN 1 ELSE 0 END) AS absconded,
                    SUM(CASE WHEN (fn_get_encounter_disposition(e.encounter_nr) IS NULL) THEN 1 ELSE 0 END) AS no_disposition";                                

    $field_discharge_result = "SUM(CASE WHEN (fn_get_encounter_result(e.encounter_nr) IN (".RECOVERED.")) THEN 1 ELSE 0 END) AS recovered_improved,
                    SUM(CASE WHEN (fn_get_encounter_result(e.encounter_nr) IN (".UNIMPROVED.")) THEN 1 ELSE 0 END) AS unimproved,
                    SUM(CASE WHEN (fn_get_encounter_result(e.encounter_nr) IS NULL) THEN 1 ELSE 0 END) AS no_result";  

    $field_death = "SUM(CASE WHEN (fn_get_encounter_result(e.encounter_nr) IN (".DEATH_CODE.")
                        AND (e.encounter_nr = cp.death_encounter_nr)
                        AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),DATE(e.admission_dt))<2)) THEN 1 ELSE 0 END) AS deathbelow48,
                    SUM(CASE WHEN (fn_get_encounter_result(e.encounter_nr) IN (".DEATH_CODE.")
                        AND (e.encounter_nr = cp.death_encounter_nr)
                        AND (DATEDIFF((IF(cp.death_date='0000-00-00',e.discharge_date, cp.death_date)),DATE(e.admission_dt))>=2)) THEN 1 ELSE 0 END) AS deathabove48";                              

     $table_source_discharge_date = "FROM care_department AS d
                    INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                    INNER JOIN care_person AS cp ON cp.pid = e.pid
                    LEFT JOIN care_ward AS w ON w.nr = IF(fn_get_encounter_location_billing (e.encounter_nr),fn_get_encounter_location_billing (e.encounter_nr),e.current_ward_nr) 
                    LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
                    LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                    LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                    AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
                    AND e.discharge_date IS NOT NULL";                   
$field_age_per_enc = "SUM(CASE WHEN p.sex='m' AND $age_bdate < 2) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_male_2below,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate < 2) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_male_2below,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 2 AND 5) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_male_2to5,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 2 AND 5) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_male_2to5,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 6 AND 11) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_male_6to11,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 6 AND 11) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_male_6to11,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 12 AND 18) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_male_12to18,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 12 AND 18) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_male_12to18,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 19 AND 59) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_male_19to59,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate BETWEEN 19 AND 59) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_male_19to59,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate >=60) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_male_60,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate >=60) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_male_60,

                    SUM(CASE WHEN p.sex='f' AND $age_bdate < 2) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_female_2below,
                    SUM(CASE WHEN p.sex='f' AND $age_bdate < 2) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_female_2below,

                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 2 AND 5) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_female_2to5,
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 2 AND 5) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_female_2to5,
                     SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 6 AND 11) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_female_6to11,
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 6 AND 11) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_female_6to11,
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 12 AND 18) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_female_12to18,
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 12 AND 18) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_female_12to18,
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 19 AND 59) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_female_19to59,
                    SUM(CASE WHEN p.sex='f' AND $age_bdate BETWEEN 19 AND 59) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_female_19to59,
                    SUM(CASE WHEN p.sex='f' AND $age_bdate >=60) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) <= 1 THEN 1 ELSE 0 END) AS new_female_60,
                    SUM(CASE WHEN p.sex='f' AND $age_bdate >=60) AND (SELECT COUNT(ce.encounter_nr) FROM care_encounter AS ce WHERE (CONCAT(CAST(ce.encounter_date AS DATE),'00:00:00') < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)) AND ce.pid = p.`pid`) > 1 THEN 1 ELSE 0 END) AS old_female_60
                    ";
#Added by Matsuu 032
$age_bdate_ipbm = "(FLOOR(IF(p.date_birth <> '0000-00-00',(fn_get_ageyr(DATE($date_based),IFNULL(sep.date_birth,p.date_birth))),p.age))";
$age_bracket_ipbm = "SUM(CASE WHEN (p.fromtemp='1' AND p.sex='m') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS male_days_in,
                    SUM(CASE WHEN (p.fromtemp='1' AND p.sex='f') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS female_days_in,
                    SUM(CASE WHEN (p.fromtemp='0' AND p.sex='m') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS male_days_out,
                    SUM(CASE WHEN (p.fromtemp='0' AND p.sex='f') AND (fn_calculate_age(p.date_birth,p.death_date))<=0.076 THEN 1 ELSE 0 END) AS female_days_out,
                    SUM(CASE WHEN p.sex='m' AND (SELECT TIMESTAMPDIFF(YEAR,IFNULL(sep.date_birth,p.date_birth),$date_based))<1 THEN 1 ELSE 0 END) AS male_below1,
                    SUM(CASE WHEN p.sex='f' AND (SELECT TIMESTAMPDIFF(YEAR,IFNULL(sep.date_birth,p.date_birth),$date_based))<1 THEN 1 ELSE 0 END) AS female_below1, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate_ipbm BETWEEN 1 AND 4) THEN 1 ELSE 0 END) AS male_1to4, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate_ipbm BETWEEN 1 AND 4) THEN 1 ELSE 0 END) AS female_1to4, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate_ipbm BETWEEN 5 AND 9) THEN 1 ELSE 0 END) AS male_5to9, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate_ipbm BETWEEN 5 AND 9) THEN 1 ELSE 0 END) AS female_5to9, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate_ipbm BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS male_10to14, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate_ipbm BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS female_10to14, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate_ipbm BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS male_15to19, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate_ipbm BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS female_15to19,
                    SUM(CASE WHEN p.sex='m' AND $age_bdate_ipbm BETWEEN 20 AND 44) THEN 1 ELSE 0 END) AS male_20to44, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate_ipbm BETWEEN 20 AND 44) THEN 1 ELSE 0 END) AS female_20to44, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate_ipbm BETWEEN 45 AND 59) THEN 1 ELSE 0 END) AS male_45to59, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate_ipbm BETWEEN 45 AND 59) THEN 1 ELSE 0 END) AS female_45to59, 
                    SUM(CASE WHEN p.sex='m' AND $age_bdate_ipbm > 59) THEN 1 ELSE 0 END) AS male_60up, 
                    SUM(CASE WHEN p.sex='f' AND $age_bdate_ipbm > 59) THEN 1 ELSE 0 END) AS female_60up,
                    SUM(CASE WHEN p.sex='m' then 1 else 0 end) AS male_total,
                    SUM(CASE WHEN p.sex='f' then 1 else 0 end) AS female_total,
                    SUM(CASE WHEN (p.sex='f' OR p.sex = 'm') then 1 else 0 end) AS total";
