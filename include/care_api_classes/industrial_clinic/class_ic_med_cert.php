<?php
require('./roots.php');
require_once($root_path . 'include/care_api_classes/class_core.php');

class SegICCertMed extends Core
{
    var $tb_cert_med = "seg_industrial_cert_med";
    var $tb_cert_med_driver = "seg_industrial_cert_med_driver";
    var $tb_industrial_transaction = "seg_industrial_transaction";
    var $tb_work_status = "seg_work_status";
    var $tb_vehicle_driven = "seg_vehicle_driven";
    var $tb_type_employment = "seg_type_employment";
    var $tb_educational_attainment = "seg_educational_attainment";
    var $tb_type_exam = "seg_industrial_med_chart_type_exam";
    var $tb_chart_list = "seg_industrial_med_chart_list";
    var $tb_med_chart = "seg_industrial_med_chart";
    var $tb_vitalsigns = "seg_industrial_vitalsigns";
    var $tb_cert_vacc = 'seg_industrial_cert_vaccine';

    var $fld_cert_med =
        array(
            'cert_nr',
            'refno',
            'remarks',
            'dr_nr_med',
            'history',
            'status',
            'modify_id',
            'modify_dt',
            'create_id',
            'create_dt',
            'medcert_date',
            'transaction_date',
            'with_medical',
            'with_dental',
            'dr_nr_dental',
            'clinic_num',
            'medical_findings',
            'dental_findings',
            #added by art 3/06/2014
            'with_optha',
            'with_ent',
            'optha_findings',
            'ent_findings',
            'dr_nr_optha',
            'dr_nr_ent'
            #end art
        );

    var $fld_cert_med_driver =
        array(
            'cert_nr',
            'refno',
            'height',
            'height_unit',
            'weight',
            'weight_unit',
            'systole',
            'diastole',
            'general_physique',
            'abnormality',
            'contagious_diseases',
            'left_eye_snellen',
            'right_eye_snellen',
            'left_eye_acuity',
            'right_eye_acuity',
            'left_ear',
            'right_ear',
            'left_lower_extremities',
            'right_lower_extremities',
            'remarks',
            'comment_drive',
            'conditions',
            'educ_attain_nr',
            'work_status_nr',
            'employ_nr',
            'vehicle_nr',
            'create_dt',
            'create_id',
            'modify_dt',
            'modify_id',
            'history',
            'left_eye_other',
            'right_eye_other',
            'left_upper_extremities',
            'right_upper_extremities',
            'exam_physician',
            'with_disease',
            'control_number'
        );

    var $fld_med_chart = array(
        'exam_nr',
        'pid',
        'encounter_nr',
        'refno',
        'diagnosis',
        'remarks', # added by: syboy 10/26/2015 : meow
        'recommendation',
        'physician_nr',
        'history',
        'create_id',
        'create_dt',
        'modify_id',
        'modify_dt',
        'treatment'
    );

    var $fld_vitalsigns = array('exam_nr',
        // 'systole',
        // 'diastole',
        // 'cardiac_rate',
        'encounter_nr',
        'blood_pressure',
        'pulse_rate',
        'resp_rate',
        'temperature',
        'weight',
        'height',
        'bmi',
        'visual_acuity',
        'ishihara',
        'hearing',
        'speech',
    );

    var $fld_comp_emp =
        array(
            'company_id',
            'pid',
            'employee_id',
            'position',
            'job_status',
            'status',
            'modify_id',
            'modify_dt',
            'create_id',
            'create_dt'
        );

    // Added by gervie 07/12/2015
    var $fld_vacc_cert =
        array(
            'cert_nr',
            'refno',
            'first_tetanus',
            'first_tetanus_deltoid',
            'second_tetanus',
            'second_tetanus_deltoid',
            'third_tetanus',
            'third_tetanus_deltoid',
            'tetanus_dose',
            'first_hepatitis',
            'first_hepatitis_deltoid',
            'second_hepatitis',
            'second_hepatitis_deltoid',
            'third_hepatitis',
            'third_hepatitis_deltoid',
            'hepatitis_dose',
            'create_dt',
            'create_id',
            'modify_dt',
            'modify_id',
            'history'
        );

    function _useCertMedRegular()
    {
        $this->coretable = $this->tb_cert_med;
        $this->ref_array = $this->fld_cert_med;
    }

    function _useCertMedDriver()
    {
        $this->coretable = $this->tb_cert_med_driver;
        $this->ref_array = $this->fld_cert_med_driver;
    }

    # Added by gervie 07/12/2015
    function _useCertVaccine()
    {
        $this->coretable = $this->tb_cert_vacc;
        $this->ref_array = $this->fld_vacc_cert;
    }

    function _useMedChart()
    {
        $this->coretable = $this->tb_med_chart;
        $this->ref_array = $this->fld_med_chart;
    }

    function _useVitalsigns()
    {
        $this->coretable = $this->tb_vitalsigns;
        $this->ref_array = $this->fld_vitalsigns;
    }

    function useCompany()
    {
        $this->coretable = $this->tb_company;
        $this->ref_array = $this->fld_company;
    }

    function useCompEmployee()
    {
        $this->coretable = $this->tb_comp_emp;
        $this->ref_array = $this->fld_comp_emp;
    }

    function getInfoTrans($encounter_nr)
    {
        global $db;
        $this->sql = "SELECT * FROM $this->tb_industrial_transaction WHERE encounter_nr = '$encounter_nr'";
        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    //function that gets all info in seg_industrial_cert_med
    function getAllInfoCertMed($refno)
    {
        global $db;

        $this->sql = "SELECT cm.*,mc.`recommendation` FROM $this->tb_cert_med as cm LEFT JOIN $this->tb_med_chart as mc on cm.`refno` = mc.`refno` WHERE cm.`refno` = '$refno'";
        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }//end of getAllInfoCertMed

    function getCertNrDriver($refno)
    {
        global $db;

        $this->sql = "SELECT cert_nr FROM $this->tb_cert_med_driver WHERE refno = '$refno'";
        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    //function that gets all info in seg_industrial_cert_med_driver
    function getAllInfoCertMedDriver($refno)
    {
        global $db;

        $this->sql = "SELECT * FROM $this->tb_cert_med_driver WHERE refno = '$refno'";
        if ($ret = $db->Execute($this->sql)) {
            if ($ret->RecordCount()) {
                //echo "pasok!";
                return $ret->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }// end of getAllInfoCertMedDriver

    // Added by gervie 07/12/2015
    function getAllinfoCertVaccine($refno)
    {
        global $db;

        $this->sql = "SELECT * FROM $this->tb_cert_vacc WHERE refno = $db->qstr($refno)";
        if ($row = $db->Execute($this->sql)) {
            if ($row->RecordCount()) {
                return $row->FetchRow();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /*
    * Insert new medical certificate info into table seg_industrial_cert_med
    * @param Array Data to by reference
    * @return boolean
    */
    function saveCertMedInfoFromArray(&$data)
    {
        $this->_useCertMedRegular();
        $this->data_array = $data;
        //$this->data_array['description']=$HTTP_POST_VARS['description'];
        return $this->insertDataFromInternalArray();
    }

    /*
   * Update medical certificate info in table seg_cert_med
   * @param Array Data to by reference
   * @return boolean
   */
    function updateCertMedInfoFromArray(&$data)
    {

        $this->_useCertMedRegular();
        $this->data_array = $data;
        if (isset($this->data_array['refno']))
            unset($this->data_array['refno']);
        //if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
        //$this->where='';
        $sql_ref .= " AND cert_nr='" . $data['cert_nr'] . "' ";

        $this->where = "refno='" . $data['refno'] . "' $sql_ref";
        return $this->updateDataFromInternalArray($data['refno'], FALSE);
    }# end function updateMedCertificateInfoFromArray

    /*
     * Added by gervie 07/12/2015
     * Insert data to table seg_industrial_cert_vaccine
     */
    function saveCertVaccineFromArray(&$data)
    {
        $this->_useCertVaccine();
        $this->data_array = $data;
        return $this->insertDataFromInternalArray();
    }

    function updateCertVaccineFromArray(&$data)
    {

        $this->_useCertVaccine();
        $data['history'] = $this->ConcatHistory("Update " . date('Y-m-d H:i:s') . " " . $_SESSION['sess_user_name'] . " \n");
        $this->data_array = $data;
        if (isset($this->data_array['refno']))
            unset($this->data_array['refno']);

        $sql_ref .= " AND cert_nr='" . $data['cert_nr'] . "' ";

        $this->where = "refno='" . $data['refno'] . "' $sql_ref";
        return $this->updateDataFromInternalArray($data['refno'], FALSE);
    }


    function getDriverCondList()
    {
        global $db;

        $this->sql = "SELECT * FROM seg_industrial_med_driver_cond_list";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }
    }

    function getDriverPhysicalExamList()
    {
        global $db;

        $this->sql = "SELECT * FROM seg_industrial_med_driver_phys_list";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }
    }

    function getDriverDiagnosisList()
    {
        global $db;

        $this->sql = "SELECT * FROM seg_industrial_med_driver_diag_list";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }
    }

    /*
    * Insert new medical certificate info into table seg_industrial_cert_med_driver
    * @param Array Data to by reference
    * @return boolean
    */
    function saveCertMedDriverInfoFromArray(&$data)
    {
        $this->_useCertMedDriver();
        $this->data_array = $data;
        //$this->data_array['description']=$HTTP_POST_VARS['description'];
        return $this->insertDataFromInternalArray();
    }

    /*
    * Update medical certificate info in table seg_cert_med_driver
    * @param Array Data to by reference
    * @return boolean
    */
    function updateCertMedDriverInfoFromArray(&$data)
    {

        $this->_useCertMedDriver();
        $this->data_array = $data;
        #print_r($data);
        if (isset($this->data_array['refno']))
            unset($this->data_array['refno']);
        //if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
        //$this->where='';
        $sql_ref .= " AND cert_nr='" . $data['cert_nr'] . "' ";

        $this->where = "refno='" . $data['refno'] . "' $sql_ref";
        return $this->updateDataFromInternalArray($data['refno'], FALSE);
    }# end function updateMedCertificateInfoFromArray

    /*
     * Added by gervie 07/12/2015
     * Insert data to table seg_industrial_cert_vaccine
     */


    /**
     * Gets all data in seg_work_status table
     *
     */
    function getWorkStatus()
    {
        global $db;

        $this->sql = "SELECT * FROM $this->tb_work_status";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }
    }//end getWorkStatus function

    /**
     * Gets all data in seg_vehicle_driven
     *
     */
    function getVehicleDriven()
    {
        global $db;

        $this->sql = "SELECT * FROM $this->tb_vehicle_driven";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }
    }//end getVehicleDriven function

    /**
     * Gets all data in seg_type_employment
     *
     */
    function getTypeEmployment()
    {
        global $db;

        $this->sql = "SELECT * FROM $this->tb_type_employment";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }
    }//end getTypeEmployment function

    /**
     * Gets all data in seg_educational_attainment
     *
     */
    function getEducationalAttainment()
    {
        global $db;

        $this->sql = "SELECT educ_attain_nr, educ_attain_name FROM $this->tb_educational_attainment";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }

    }//end getEducationalAttainment function

    /*
    * Insert new medical examination info into table seg_industrial_med_chart
    * @param Array Data to by reference
    * @return boolean
    */
    function saveMedChartFromArray(&$data)
    {
        $this->_useMedChart();
        $this->data_array = $data;
        //$this->data_array['description']=$HTTP_POST_VARS['description'];
        return $this->insertDataFromInternalArray();
    }

    /*
        * Insert new vitalsigns info into table seg_industrial_vitalsigns
        * @param Array Data to by reference
        * @return boolean
        */
    function saveVitalsignsFromArray(&$data)
    {
        $this->_useVitalsigns();
        $this->data_array = $data;
        //$this->data_array['description']=$HTTP_POST_VARS['description'];
        return $this->insertDataFromInternalArray();
    }

    /*
    * Update medical chart info in table seg_industrial_med_chart
    * @param Array Data to by reference
    * @return boolean
    */
    function updateMedChartFromArray(&$data)
    {

        $this->_useMedChart();
        $data['history'] = $this->ConcatHistory("Update " . date('Y-m-d H:i:s') . " " . $HTTP_SESSION_VARS['sess_user_name'] . " \n");
        $this->data_array = $data;

        // 	if(isset($this->data_array['exam_nr']))
        // 		unset($this->data_array['exam_nr']);
        // 	//if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
        // 	//$this->where='';
        // 			//$sql_ref .= " AND cert_nr='".$data['cert_nr']."' ";

        // 	$this->where="exam_nr='".$data['exam_nr']."'";
        // 	return $this->updateDataFromInternalArray($data['exam_nr'],FALSE);
        // }

        if (isset($this->data_array['encounter_nr']))
            unset($this->data_array['encounter_nr']);
        //if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
        //$this->where='';
        //$sql_ref .= " AND cert_nr='".$data['cert_nr']."' ";

        $this->where = "encounter_nr='" . $data['encounter_nr'] . "'";
        return $this->updateDataFromInternalArray($data['encounter_nr'], FALSE);
    }


    /*
   * Update vitalsigns info in table seg_industrial_vitalsigns
   * @param Array Data to by reference
   * @return boolean
   */
    function updateVitalsignsFromArray(&$data)
    {

        $this->_useVitalsigns();
        $this->data_array = $data;

        /*if(isset($this->data_array['exam_nr']))
            unset($this->data_array['exam_nr']);
        //if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
        //$this->where='';
                //$sql_ref .= " AND cert_nr='".$data['cert_nr']."' ";

        $this->where="exam_nr='".$data['exam_nr']."'";
        return $this->updateDataFromInternalArray($data['exam_nr'],FALSE);*/
        if (isset($this->data_array['encounter_nr']))
            unset($this->data_array['encounter_nr']);
        //if(isset($this->data_array['create_code'])) unset($this->data_array['create_code']);
        //$this->where='';
        //$sql_ref .= " AND cert_nr='".$data['cert_nr']."' ";

        $this->where = "encounter_nr='" . $data['encounter_nr'] . "'";
        return $this->updateDataFromInternalArray($data['encounter_nr'], FALSE);
    }

    function updateMedChartDetails($nr, $string)
    {
        global $db;
        #echo "nr= ".$nr."<br>";
        $query = "DELETE FROM seg_industrial_med_chart_details WHERE exam_nr='$nr'";
        #echo $query;
        $result = $db->Execute($query);
        if ($result) {
            return $this->saveMedChartDetails($string);
        } else {
            return FALSE;
        }
        /*if ($db->Execute($this->sql)) {
            return $this->saveMedChartDetails($string);
                        #return TRUE;
        }else{ return FALSE; }  */

    }

    function getMedChartInfo($pid, $refno)
    {
        global $db;

        // $this->sql = "SELECT smc.*, vit.* FROM seg_industrial_med_chart AS smc
        // 							INNER JOIN seg_industrial_vitalsigns AS vit ON vit.exam_nr = smc.exam_nr
        // 							WHERE refno = '$refno'";

        # added by: syboy 10/26/2015 : meow
        // $sql = "SELECT * FROM seg_industrial_med_chart WHERE pid = $pid ORDER BY create_dt DESC LIMIT 1";
        // $result = $db->GetAll($sql);
        $previous = $db->GetAll("SELECT refno FROM seg_industrial_med_chart WHERE pid = ?", $pid);
        $enc_prev = array();
        foreach ($previous as $value) {
            $enc_prev[$value['refno']] = $value['refno'];
        }
        foreach ($enc_prev as $key) {
            if ($key == $refno) {
                $true = $key;
                break;
            }else{
                $true = $key;
            }
            
        }

        if ($true == null || $true == FALSE) {
            $previous = $db->GetOne("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ? ORDER BY create_dt DESC", $pid);
            if ($previous == false || $previous == null) {
                $param = array($pid, $true);
                $this->sql = $db->Prepare('SELECT * FROM seg_industrial_med_chart WHERE refno=? AND refno = ?');
            }else{
                $param = array($previous, $true);
                $this->sql = $db->Prepare('SELECT * FROM seg_industrial_med_chart WHERE refno=? AND refno = ?');
            }
            
            if ($ret = $db->Execute($this->sql, $param)) {
                if ($ret->RecordCount()) {
                    $fetch_data = $ret->FetchRow();
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }else{
            $param = array($pid, $true);
            $sql = "SELECT * FROM seg_industrial_med_chart WHERE pid = ? AND refno = ?";
            $fetch_data = $db->GetAll($sql, $param);

        }
        # ended syboy

        # commented by: syboy 10/26/2015 : old codes ; meow
        /*
        $param = array($refno);
        $this->sql = $db->Prepare('SELECT * FROM seg_industrial_med_chart WHERE refno=?');
        if ($ret = $db->Execute($this->sql, $param)) {
            if ($ret->RecordCount()) {
                //echo "pasok!";
                return $ret->FetchRow();

            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
        */ 
        # ended
        return $fetch_data;
    }

    function getMedChartDetails($refno)
    {
        global $db;

        $this->sql = "SELECT det.* FROM seg_industrial_med_chart_details AS det
										INNER JOIN seg_industrial_med_chart AS smc ON smc.exam_nr = det.exam_nr
										WHERE smc.refno = '$refno'
										ORDER BY exam_type ASC";
        if ($ret = $db->Execute($this->sql)) {
            if ($ret->RecordCount()) {
                //echo "pasok!";
                return $ret;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }

    }

    function getTypeExam()
    {
        global $db;

        $this->sql = "SELECT * FROM $this->tb_type_exam";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }
    }

    function getChartList()
    {
        global $db;

        $this->sql = "SELECT * FROM $this->tb_chart_list";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }

    }

    function getUnitsHeight()
    {
        global $db;

        $this->sql = "SELECT unit_id, unit_name FROM seg_encounter_vitalsigns_unit WHERE class='h'";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }
    }

    function getUnitsWeight()
    {
        global $db;

        $this->sql = "SELECT unit_id, unit_name FROM seg_encounter_vitalsigns_unit WHERE class='w'";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }
    }

    //function to save exam chart details (for exams)
    function saveMedChartDetails($string)
    {
        global $db;

        $this->sql = "INSERT INTO seg_industrial_med_chart_details (exam_nr ,exam_type, exam_type_list, remarks, dr_nr) VALUES $string";
        if ($buf = $db->Execute($this->sql)) {
            return $buf;
        } else {
            return FALSE;
        }
    }

    function getExamNr()
    {
        global $db;
        #echo "haller!<br>";
        $this->sql = "SELECT exam_nr FROM seg_industrial_med_chart ORDER BY exam_nr DESC";
        $result = $db->Execute($this->sql);
        if ($result) {
            $row = $result->FetchRow();
            #echo "exam nr = ".$row['exam_nr']."<br>";
            return $row['exam_nr'];
        } else {
            $val = 0;
            return $val;
        }
        /*if($buf=$db->Execute($this->sql)){
            $row = $buf->FetchRow;
            print_r($row);
            return $row['exam_nr'];
        }else{
            $val = 0;
            return $val;
        }     */
    }

    function getNewId()
    {
        global $db;
        $id = date('Y') . '000001';
        $temp_id = date('Y') . "%";
        $row = array();
        $this->sql = "SELECT company_id FROM $this->tb_company WHERE company_id LIKE '$temp_id' ORDER BY company_id DESC";
        if ($this->res['gnpn'] = $db->SelectLimit($this->sql, 1)) {
            if ($this->res['gnpn']->RecordCount()) {
                $row = $this->res['gnpn']->FetchRow();
                return $row['company_id'] + 1;
            } else {
                return $id;
            }
        } else {
            return $id;
        }
    }

    function saveCompany($data)
    {
        global $db;
        $this->setDataArray($data);
        return $this->insertDataFromInternalArray();
    }

    function updateCompany($data, $nr)
    {
        global $db;
        $this->sql = "UPDATE $this->tb_company SET name=" . $db->qstr($data['name']) . ", \n" .
            "address=" . $db->qstr($data['address']) . ", contact_no=" . $db->qstr($data['contact_no']) . ", \n" .
            "short_id=" . $db->qstr($data['short_id']) . ", president=" . $db->qstr($data['president']) . ", \n" .
            "hr_manager=" . $db->qstr($data['hr_manager']) . ", hosp_acct_no=" . $db->qstr($data['hosp_acct_no']) . ", \n" .
            "history=CONCAT(history,'\nUpdated: " . date('Y-m-d H:i:s') . " [" . addslashes($_SESSION['sess_temp_userid']) . "]'),\n" .
            "modify_id=" . $db->qstr($_SESSION['sess_temp_userid']) . ", modify_dt=" . $db->qstr(date('Y-m-d H:i:s')) . " \n" .
            "WHERE company_id=" . $db->qstr($nr);
        $saveok = $db->Execute($this->sql);
        if ($saveok !== FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function deleteCompany($nr)
    {
        global $db;
        $this->sql = "UPDATE $this->tb_company SET status='deleted' WHERE company_id=" . $db->qstr($nr);
        $saveok = $db->Execute($this->sql);
        if ($saveok !== FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getCompanyDetails($nr)
    {
        global $db;
        $this->sql = "SELECT SQL_CALC_FOUND_ROWS ia.* FROM seg_industrial_company AS ia WHERE status <> 'deleted' \n" .
            " AND company_id=" . $db->qstr($nr);
        $this->result = $db->Execute($this->sql);
        if ($this->result !== FALSE) {
            return $this->result->FetchRow();
        } else {
            return FALSE;
        }
    }

    function assignCompanyEmployee($data)
    {
        global $db;
        $this->setDataArray($data);
        return $this->insertDataFromInternalArray();
    }

    function deleteEmployeeAssignment($pid, $nr)
    {
        global $db;
        $this->sql = "UPDATE $this->tb_comp_emp SET status='deleted', \n" .
            " modify_id=" . $db->qstr($_SESSION['sess_temp_userid']) . ", \n" .
            " modify_dt=" . $db->qstr(date('Y-m-d H:i:s')) . " \n" .
            " WHERE pid=" . $db->qstr($pid) . " AND company_id=" . $db->qstr($nr);
        $this->result = $db->Execute($this->sql);
        if ($this->result !== FALSE) {
            return TRUE;
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function getEmployeeDetails($pid, $nr)
    {
        global $db;
        $this->sql = "SELECT SQL_CALC_FOUND_ROWS ia.* FROM seg_industrial_comp_emp AS ia WHERE ia.status <> 'deleted' \n" .
            " AND ia.company_id=" . $db->qstr($nr) . " AND ia.pid=" . $db->qstr($pid);
        $this->result = $db->Execute($this->sql);
        if ($this->result !== FALSE) {
            return $this->result->FetchRow();
        } else {
            return FALSE;
        }
    }

    function updateEmployeeData($data)
    {
        global $db;
        $this->sql = "UPDATE $this->tb_comp_emp SET employee_id=" . $db->qstr($data['employee_id']) . ", \n" .
            "position=" . $db->qstr($data['position']) . ", job_status=" . $db->qstr($data['job_status']) . ", \n" .
            "modify_id=" . $db->qstr($_SESSION['sess_temp_userid']) . ", modify_dt=" . $db->qstr(date('Y-m-d H:i:s')) . " \n" .
            "WHERE company_id=" . $db->qstr($data['company_id']) . " AND pid=" . $db->qstr($data['pid']);
        $saveok = $db->Execute($this->sql);
        if ($saveok !== FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
    ** get patient data for medical record
    **added by art 03/06/2014
    **edited by Macoy August 05,2014
    */
    function getPatientData($encounter_nr)
    {
        global $db;
        $this->sql = "SELECT
							  c.*,
							  CONCAT(
							    IFNULL(p.name_last, ''),
							    IFNULL(CONCAT(', ', p.name_first), ''),
							    IFNULL(CONCAT(' ', p.name_middle), '')
							  ) AS patient_name,
							  fn_calculate_age (p.date_birth, NOW()) AS patient_age,
							  t.trxn_date,
							  p.date_birth,
							  p.civil_status,
							  p.pid,
							  UPPER(p.sex) AS patient_sex,
							  p.street_name,
							  sb.brgy_name,
							  sm.mun_name,
							  sm.zipcode,
							  sp.prov_name,
							  mc.recommendation
							FROM
							  seg_industrial_cert_med AS c 
							  INNER JOIN seg_industrial_transaction AS t 
							    ON t.refno = c.refno 
							  INNER JOIN care_person AS p 
							    ON p.pid = t.pid 
							  LEFT JOIN seg_barangays AS sb 
							    ON sb.brgy_nr = p.brgy_nr 
							  LEFT JOIN seg_municity AS sm 
							    ON sm.mun_nr = p.mun_nr 
							  LEFT JOIN seg_provinces AS sp 
							    ON sp.prov_nr = sm.prov_nr
							  LEFT JOIN seg_industrial_med_chart mc ON c.refno = mc.refno
							WHERE t.encounter_nr =" . $encounter_nr;

        $result = $db->Execute($this->sql);
        if (!$result) {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        } else {
            return $result->FetchRow();
        }
    }
    /*****************************************************************************/
    /*
    **	added by art 03/07/2014
    */
    function getCertNr($refno, $tbl)
    {
        global $db;
        $this->sql = "SELECT cert_nr FROM " . $tbl . " WHERE refno =" . $refno;

        if ($this->result = $db->Execute($this->sql)) {
            return $this->result->FetchRow();
        } else {
            return FALSE;
        }
    }


    function getConditionsMedchart($pid, $enc, $cond_nr)
    {
        global $db;
        # added by: syboy 10/26/2015 : meow
        $previous = $db->GetAll("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ?", $pid);
        $enc_prev = array();
        foreach ($previous as $value) {
            $enc_prev[$value['encounter_nr']] = $value['encounter_nr'];
        }
        foreach ($enc_prev as $key) {
            if ($key == $enc) {
                $true = $key;
                break;
            }else{
                $true = $key;
            }
            
        }

        if ($true == false || $true == null) {
            $previous = $db->GetOne("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ? ORDER BY create_dt DESC", $pid);
            if ($previous == false || $previous == null) {
        $param = array($enc, $cond_nr);
        $this->sql = $db->Prepare("SELECT is_selected FROM seg_industrial_med_chart_conditions WHERE encounter_nr=? AND condition_nr =?");
            }else{
                $param = array($previous, $cond_nr);
                $this->sql = $db->Prepare("SELECT is_selected FROM seg_industrial_med_chart_conditions WHERE encounter_nr=? AND condition_nr =?");
            }

        if ($this->result = $db->Execute($this->sql, $param)) {
            $row = $this->result->FetchRow();
                $prevselect = $row['is_selected'];
        } else {
            return FALSE;
        }
        }else{
            $param = array($true, $cond_nr);
            $this->sql = $db->Prepare("SELECT is_selected FROM seg_industrial_med_chart_conditions WHERE encounter_nr=? AND condition_nr =?");
            if ($this->result = $db->Execute($this->sql, $param)) {
                $row = $this->result->FetchRow();
                $prevselect = $row['is_selected'];
        } else {
            return FALSE;
        }
    }
        #ended syboy
        return $prevselect;
    }

    function getHistorySmoker($pid)
    {
        global $db;
        $this->sql  = "SELECT smoker_history FROM care_encounter WHERE pid = '".$pid."' ORDER BY encounter_nr DESC LIMIT 1 ";

        if ($this->result = $db->Execute($this->sql)) {
            return $this->result->FetchRow();
        } else {
            return FALSE;
        }
        
    }

    function getHistoryDrinker($pid)
    {
        global $db;
        $this->sql  = "SELECT drinker_history FROM care_encounter WHERE pid = '".$pid."' ORDER BY encounter_nr DESC LIMIT 1 ";

        if ($this->result = $db->Execute($this->sql)) {
            return $this->result->FetchRow();
        } else {
            return FALSE;
        }
        
    }

    function getVitalSignsMedchart($pid, $enc)
    {
        global $db;
        # added by: syboy 10/28/2015 : meow
        $previous = $db->GetAll("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ?", $pid);

        $enc_prev = array();
        foreach ($previous as $value) {
            $enc_prev[$value['encounter_nr']] = $value['encounter_nr'];
        }
        foreach ($enc_prev as $key) {
            if ($key == $enc) {
                $true = $key;
                break;
            }else{
                $true = $key;
            }
            
        }

        if ($true == false || $true == null) {
            $previous = $db->GetOne("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ? ORDER BY create_dt DESC", $pid);
            if ($previous == false || $previous == null) {
        $param = array($enc);
        $this->sql = $db->Prepare('SELECT * FROM seg_industrial_vitalsigns WHERE encounter_nr =?');
            }else{
                $param = array($previous);
                $this->sql = $db->Prepare('SELECT * FROM seg_industrial_vitalsigns WHERE encounter_nr =?');
            }
            
        if ($this->result = $db->Execute($this->sql, $param)) {
            $row = $this->result->FetchRow();
        } else {
            return FALSE;
        }
        }else {
            $param = array($true);
            $this->sql = $db->Prepare('SELECT * FROM seg_industrial_vitalsigns WHERE encounter_nr =?');
        if ($this->result = $db->Execute($this->sql, $param)) {
            $row = $this->result->FetchRow();
        } else {
            return FALSE;
        }
    }
        # ended syboy
        return $row;
    }

    function getContentMedchart($pid, $enc, $nr)
    {
        global $db;
        # added by: syboy 10/26/2015 : meow
        $previous = $db->GetAll("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ?", $pid);

        $enc_prev = array();
        foreach ($previous as $value) {
            $enc_prev[$value['encounter_nr']] = $value['encounter_nr'];
        }
        foreach ($enc_prev as $key) {
            if ($key == $enc) {
                $true = $key;
                break;
            }else{
                $true = $key;
            }
            
        }
        if ($true == false || $true == null) {
            $previous = $db->GetOne("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ? ORDER BY create_dt DESC", $pid);
            if ($previous == false || $previous == null) {
        $param = array($enc, $nr);
        $this->sql = $db->Prepare('SELECT content FROM seg_industrial_med_chart_content WHERE encounter_nr=? AND content_nr=?');
            }else{
                $param = array($previous, $nr);
                $this->sql = $db->Prepare('SELECT content FROM seg_industrial_med_chart_content WHERE encounter_nr=? AND content_nr=?');
            }

        if ($this->result = $db->Execute($this->sql, $param)) {
            $row = $this->result->FetchRow();
                $content = $row['content'];
            } else {
                return FALSE;
            }
        }else{
            $param = array($true, $nr);
            $this->sql = $db->Prepare('SELECT content FROM seg_industrial_med_chart_content WHERE encounter_nr=? AND content_nr=?');
            if ($this->result = $db->Execute($this->sql, $param)) {
                $row = $this->result->FetchRow();
                $content = $row['content'];
        } else {
            return FALSE;
        }
    }
        # ended
        return $content;
    }

    function getPhysicalMedchart($pid, $enc, $nr)
    {
        global $db;
        # added by: syboy 10/28/2015 : meow
        $previous = $db->GetAll("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ?", $pid);

        $enc_prev = array();
        foreach ($previous as $value) {
            $enc_prev[$value['encounter_nr']] = $value['encounter_nr'];
        }
        foreach ($enc_prev as $key) {
            if ($key == $enc) {
                $true = $key;
                break;
            }else{
                $true = $key;
            }
            
        }

        if ($true == false || $true == null ) {
            $previous = $db->GetOne("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ? ORDER BY create_dt DESC", $pid);
            if ($previous == false || $previous == null) {
        $param = array($enc, $nr);
        $this->sql = $db->Prepare('SELECT is_selected,remarks FROM seg_industrial_med_chart_physical WHERE encounter_nr=? AND phy_nr=?');
            }else{
                $param = array($previous, $nr);
                $this->sql = $db->Prepare('SELECT is_selected,remarks FROM seg_industrial_med_chart_physical WHERE encounter_nr=? AND phy_nr=?');
            }
            
            if ($this->result = $db->Execute($this->sql, $param)) {
                $row = $this->result->FetchRow();
            } else {
                return FALSE;
            }
        }else{
            $param = array($true, $nr);
            $this->sql = $db->Prepare('SELECT is_selected,remarks FROM seg_industrial_med_chart_physical WHERE encounter_nr=? AND phy_nr=?');
        if ($this->result = $db->Execute($this->sql, $param)) {
            $row = $this->result->FetchRow();
        } else {
            return FALSE;
        }
    }
        return $row;
        # ended syboy
    }


    function getDiagnosticMedchart($pid, $enc, $nr)
    {
        global $db;
        # added by: syboy 10/28/2015 : meow
        $previous = $db->GetAll("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ?", $pid);

        $enc_prev = array();
        foreach ($previous as $value) {
            $enc_prev[$value['encounter_nr']] = $value['encounter_nr'];
        }
        foreach ($enc_prev as $key) {
            if ($key == $enc) {
                $true = $key;
                break;
            }else{
                $true = $key;
            }
            
        }

        if ($true == false || $true == null ) {
            $previous = $db->GetOne("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ? ORDER BY create_dt DESC", $pid);
            if ($previous == false || $previous == null) {
        $param = array($enc, $nr);
        $this->sql = $db->Prepare('SELECT is_selected,remarks FROM seg_industrial_med_chart_diagnostic WHERE encounter_nr=? AND diag_nr=?');
            }else{
                $param = array($previous, $nr);
                $this->sql = $db->Prepare('SELECT is_selected,remarks FROM seg_industrial_med_chart_diagnostic WHERE encounter_nr=? AND diag_nr=?');
            }

        if ($this->result = $db->Execute($this->sql, $param)) {
            $row = $this->result->FetchRow();
                
        } else {
            return FALSE;
        }
        }else{
            $param = array($true, $nr);
            $this->sql = $db->Prepare('SELECT is_selected,remarks FROM seg_industrial_med_chart_diagnostic WHERE encounter_nr=? AND diag_nr=?');
        if ($this->result = $db->Execute($this->sql, $param)) {
            $row = $this->result->FetchRow();
                
        } else {
            return FALSE;
        }
    }
        return $row;
        # ended syboy
    }

    #added by art 07/24/2014
    function saveMedcert($data, $mode)
    {
        global $db;
        if ($mode == 'save') {
            #$param = array();
            $this->sql = $db->Prepare('INSERT INTO seg_industrial_cert_med (
										  
										  remarks,
										  dr_nr_med,
										  dr_nr_dental,
										  medcert_date,
										  transaction_date,
										  with_medical,
										  with_dental,
										  history,
										  create_dt,
										  create_id,
										  modify_dt,
										  modify_id,
										  clinic_num,
										  medical_findings,
										  dental_findings,
										  with_optha,
										  with_ent,
										  optha_findings,
										  ent_findings,
										  dr_nr_optha,
										  dr_nr_ent,
										  refno
										) 
										VALUES
										  (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        } else {
            #$param = array();
            $this->sql = $db->Prepare('UPDATE
											  seg_industrial_cert_med 
											SET
											  remarks = ?, 
											  dr_nr_med = ?, 
											  dr_nr_dental = ?, 
											  medcert_date = ?, 
											  transaction_date = ?, 
											  with_medical = ?,
											  with_dental = ?, 
											  history = ?, 
											  modify_dt = ?, 
											  modify_id = ?, 
											  clinic_num = ?,
											  medical_findings = ?,
											  dental_findings = ?,
											  with_optha = ?,
											  with_ent = ?,
											  optha_findings = ?,
											  ent_findings = ?,
											  dr_nr_optha = ?,
											  dr_nr_ent = ?
											WHERE refno = ?');
        }
        if ($rs = $db->Execute($this->sql, $data)) {
            return true;
        } else {
            return false;
        }
    }#end art

    function save_medcert_reccomendation($ref, $enc, $rec)
    {
        global $db;
        $fldarray = array('encounter_nr' => $db->qstr($enc), 'refno' => $db->qstr($ref), 'recommendation' => $db->qstr($rec));
        $rs = $db->Replace('seg_industrial_med_chart', $fldarray, array('encounter_nr', 'refno'));
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @author Nick B. Alcala 7-11-2015
     * Get the person's other clinical findings with array keys equal to
     * the exam_id and child elements as its values
     * @param string $encounterNr
     * @return array
     */
    public static function getPersonOtherClinicalFindings($pid, $encounterNr)
    {
        global $db;
        # added by: syboy 10/28/2015 : meow
        $previous = $db->GetAll("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ?", $pid);

        $enc_prev = array();
        foreach ($previous as $value) {
            $enc_prev[$value['encounter_nr']] = $value['encounter_nr'];
        }
        foreach ($enc_prev as $key) {
            if ($key == $encounterNr) {
                $true = $key;
                break;
            }else{
                $true = $key;
            }
            
        }

        if ($true == false || $true == null) {
            $previous2 = $db->GetOne("SELECT encounter_nr FROM seg_industrial_med_chart WHERE pid = ? ORDER BY create_dt DESC", $pid);
            if ($previous2 == false || $previous2 == null) {
        $result = array();
        $findings = $db->GetAll("SELECT
                                    exam_id,result,remark, left_remark,
                                    right_remark, glass_left_remark,
                                    glass_right_remark, physician_nr,
                                    fn_get_personell_firstname_last(physician_nr) AS physician_name,
                                    personell.license_nr
                                 FROM seg_industrial_med_chart_findings
                                 LEFT JOIN care_personell AS personell
                                    ON personell.nr = physician_nr
                                 WHERE encounter_nr=?", $encounterNr);
        foreach ($findings as $finding) {
            #added by: syboy 09/07/2015
            #if findings result is null
            if ($finding['result'] != null)
                $finding_result = $finding['result'];
            else
                $finding_result = '0';
            #end
            $result[$finding['exam_id']] = array(
                'result' => $finding_result,
                'remark' => $finding['remark'],
                'left_remark' => $finding['left_remark'],
                'right_remark' => $finding['right_remark'],
                'glass_left_remark' => $finding['glass_left_remark'],
                'glass_right_remark' => $finding['glass_right_remark'],
                'physician_nr' => $finding['physician_nr'],
                'physician_name' => $finding['physician_name'],
                'license_nr' => $finding['license_nr'],
            );
        }
            }else{
                $result = array();
                $findings = $db->GetAll("SELECT
                                        exam_id,result,remark, left_remark,
                                        right_remark, glass_left_remark,
                                        glass_right_remark, physician_nr,
                                        fn_get_personell_firstname_last(physician_nr) AS physician_name,
                                        personell.license_nr
                                     FROM seg_industrial_med_chart_findings
                                     LEFT JOIN care_personell AS personell
                                        ON personell.nr = physician_nr
                                     WHERE encounter_nr=?", $previous);
                foreach ($findings as $finding) {
                    #added by: syboy 09/07/2015
                    #if findings result is null
                    if ($finding['result'] != null)
                        $finding_result = $finding['result'];
                    else
                        $finding_result = '0';
                    #end
                    $result[$finding['exam_id']] = array(
                        'result' => $finding_result,
                        'remark' => $finding['remark'],
                        'left_remark' => $finding['left_remark'],
                        'right_remark' => $finding['right_remark'],
                        'glass_left_remark' => $finding['glass_left_remark'],
                        'glass_right_remark' => $finding['glass_right_remark'],
                        'physician_nr' => $finding['physician_nr'],
                        'physician_name' => $finding['physician_name'],
                        'license_nr' => $finding['license_nr'],
                    );
                }
            }
            
        }else{
            $result = array();
            $findings = $db->GetAll("SELECT
                                        exam_id,result,remark, left_remark,
                                        right_remark, glass_left_remark,
                                        glass_right_remark, physician_nr,
                                        fn_get_personell_firstname_last(physician_nr) AS physician_name,
                                        personell.license_nr
                                     FROM seg_industrial_med_chart_findings
                                     LEFT JOIN care_personell AS personell
                                        ON personell.nr = physician_nr
                                     WHERE encounter_nr=?", $true);
            foreach ($findings as $finding) {
                #added by: syboy 09/07/2015
                #if findings result is null
                if ($finding['result'] != null)
                    $finding_result = $finding['result'];
                else
                    $finding_result = '0';
                #end
                $result[$finding['exam_id']] = array(
                    'result' => $finding_result,
                    'remark' => $finding['remark'],
                    'left_remark' => $finding['left_remark'],
                    'right_remark' => $finding['right_remark'],
                    'glass_left_remark' => $finding['glass_left_remark'],
                    'glass_right_remark' => $finding['glass_right_remark'],
                    'physician_nr' => $finding['physician_nr'],
                    'physician_name' => $finding['physician_name'],
                    'license_nr' => $finding['license_nr'],
                );
            }
        }
        # ended syboy
        return $result;
    }

    /**
     * @author Nick B. Alcala 7-11-2015
     * Get all Other Clinical Examinations
     * @return array
     */
    public static function getOtherClinicalFindings()
    {
        global $db;
        return $db->GetAll("SELECT id,code,name,with_dr_sig,left_remark,right_remark,has_result FROM seg_industrial_med_chart_type_exam WHERE with_dr_sig=1;");
    }

    /**
     * @author Nick B. Alcala 7-11-2015
     * Saves or Updates the persons Other Clinical Findings
     * returns 0 on failure, 1 on update, and 2 on insert
     * @param int $examId
     * @param string $encounterNr
     * @param int $result
     * @param string $remark
     * @param string $leftRemark
     * @param string $rightRemark
     * @param string $glassRightRemark
     * @param string $glassLeftRemark
     * @param string $physicianNr
     * @return int
     */
    public static function saveOtherClinicalFindings($examId, $encounterNr, $result, $remark, $leftRemark, $rightRemark, $glassRightRemark, $glassLeftRemark, $physicianNr)
    {
        global $db;
        $previous = $db->GetOne("SELECT exam_id
                                    FROM seg_industrial_med_chart_findings
                                 WHERE exam_id=? AND encounter_nr=?", array($examId, $encounterNr));
        $fields = array(
            'encounter_nr' => $db->qstr($encounterNr),
            'exam_id' => $db->qstr($examId),
            'result' => $db->qstr($result),
            'remark' => $db->qstr($remark),
            'left_remark' => $db->qstr($leftRemark),
            'right_remark' => $db->qstr($rightRemark),
            'glass_right_remark' => $db->qstr($glassRightRemark),
            'glass_left_remark' => $db->qstr($glassLeftRemark),
            'physician_nr' => $db->qstr($physicianNr),
        );
        $userName = $_SESSION['sess_user_name'];
        $dateTime = date('Y-m-d h:i:s a');

        if (!$previous) {
            $fields = array_merge($fields, array(
                'history' => $db->qstr("Created by {$userName} at {$dateTime}\n"),
                'create_id' => $db->qstr($userName),
                'create_time' => $db->qstr($dateTime),
            ));
        } else {
            $fields = array_merge($fields, array(
                'history' => "CONCAT(history,{$db->qstr("Updated by {$userName} at {$dateTime}\n")})",
                'modify_id' => $db->qstr($userName),
                'modify_time' => $db->qstr($dateTime),
            ));
        }
        return $db->Replace('seg_industrial_med_chart_findings', $fields, array('encounter_nr', 'exam_id'));
    }

# added by: syboy 07/15/2015
        # return all data in seg_industrial_med_chart_follow_up
        function getIcCertMedXsamFollowUp($pid, $nr){
        	global $db;
        	$sql = $db->GetAll("SELECT id, date_request, vshtwt, hxpe, remarks FROM seg_industrial_med_chart_follow_up WHERE pid= '$pid' AND encounter_nr= '$nr' AND is_deleted = 0 ORDER BY date_request "); //updated by Kenneth 04-07-2016
        	return $sql;        	
        }
        // end

} #end class