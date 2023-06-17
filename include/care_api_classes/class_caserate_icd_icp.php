<?php
/*
 * @package care_api
 */

require_once($root_path.'include/care_api_classes/class_core.php');

define('CHEMO',96408);
define('NEWBORN',99432);
define('NEWBORN_2',99460);
define('PRENATAL',59400);
//define('DEB',11000);

class Icd_Icp extends Core{

    public $special_procedures;
    public $tables;

    //added by Nick 07-15-2014
    function __construct($data=array()){

        global $db;

        $this->special_procedures = array(
            NEWBORN => array(
                'table_name' => 'seg_caserate_hearing_test',
                'description' => 'New born with hearing test',
                'is_for_availed' => false
            ),
            NEWBORN_2 => array(
                'table_name' => 'seg_caserate_hearing_test',
                'description' => 'New born with hearing test',
                'is_for_availed' => false
            ),
            PRENATAL => array(
                'table_name' => 'seg_billing_caserate_details',
                'description' => 'Prenatal',
                'is_for_availed' => false
            ),
        );

        $this->tables = array(
            'seg_caserate_hearing_test' => array(
                'fields' => array(
                    'encounter_nr' => $db->qstr($data['encounter_nr']),
                    'is_availed' => $db->qstr($data['is_availed'])
                ),
                'primary_keys' => array(
                    'encounter_nr'
                )
            ),
            'seg_billing_caserate_details' => array(
                'fields' => array(
                    'encounter_nr' => $db->qstr($data['encounter_nr']),
                    'package_id' => $db->qstr($data['code']),
                    'is_availed' => $data['is_availed']
                ),
                'primary_keys' => array(
                    'encounter_nr'
                )
            )
        );

    }//end function __construct

    function getInfo($code){
        global $db;

        if(empty($code)) return FALSE;

        $this->sql="SELECT * FROM seg_case_rate_packages WHERE code=".$db->qstr($code);

        if($this->res['info']=$db->Execute($this->sql)){
            if($this->res['info']->RecordCount()){
                return $this->res['info'];
            }else{return FALSE; };
        }else{return FALSE; }
    }//end function getInfo

    function removeICDCode($diagnosis_nr, $create_id){
        global $db;

        $history =$this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$create_id."\n");
        $this->sql = "UPDATE seg_encounter_diagnosis SET status='deleted',history=".$history." ".
            "\n WHERE diagnosis_nr = $diagnosis_nr";

        if($result=$db->Execute($this->sql)){
            if($result->RecordCount()){
                return TRUE;
            }else{return FALSE; };
        }else{return FALSE; }
    }//end function removeICDCode

    /**
     * Updated by Jarel
     * Updated on 03/05/2014
     * Get Package details based on diagnosis and procedure encoded
     * @param string enc
     * @return result
     *
     * Updated by Nick
     * 05/07/2014 :
     * Sum all num_sessions for rvs, for multiple special procedures
     * 05/30/2014 :
     * Added array of allowed and not allowed multiple procedures
     * 06/05/2015
     * Added date_from and date_to range
     * @param string enc
     * @return AdoDbRecordSet | bool
     **/
    function searchIcdIcp($enc){
        global $db;

        $list = Config::get('repetitive_multiplier');

        $arrayList = explode(',', $list);

        $allowed_multiple = "('".implode("','", $arrayList)."')";

        $not_allowed_multiple = implode(',',array(CHEMO));
        $admissionDate = self::getAdmissionDate($enc);

        //commented by Nick 05-07-2014
        // $this->sql = "SELECT sed.`code` AS code , 1 as num_sessions, '' as laterality, sp.*, spe.*
        // 				FROM seg_encounter_diagnosis sed
        // 				INNER JOIN seg_case_rate_packages sp
        // 					ON sp.code = sed.code
        // 				LEFT JOIN seg_case_rate_special spe
        // 					ON sp.code = spe.sp_package_id
        // 				WHERE sed.`encounter_nr` = ".$db->qstr($enc)."
        // 				AND sed.`is_deleted` = 0
        // 			 UNION
        // 			 SELECT smod.`ops_code` AS code, num_sessions, laterality, p.*, spe.*
        // 				FROM seg_misc_ops smo
        // 				INNER JOIN seg_misc_ops_details smod
        // 					ON smo.`refno` = smod.`refno`
        // 				INNER JOIN seg_case_rate_packages p
        // 					ON p.code = smod.ops_code
        // 				LEFT JOIN seg_case_rate_special spe
        // 					ON p.code = spe.sp_package_id
        // 				WHERE smo.`encounter_nr` = ".$db->qstr($enc)."\n
        // 			  ORDER BY (package * num_sessions) DESC";

        //added by Nick 05-07-2014
        $this->sql = "SELECT
						  sed.code AS CODE,
						  1 AS num_sessions,
						  '' AS laterality,
						  sp.*,
						  spe.*,
						  sca.acr_groupid,
                          sorp.rvu, NULL AS operation_date
						FROM
						  seg_encounter_diagnosis sed
						  INNER JOIN seg_case_rate_packages sp
						    ON sp.code = sed.code AND
                            (
                                STR_TO_DATE(sp.date_from,'%Y-%m-%d') <= STR_TO_DATE(?,'%Y-%m-%d') AND
                                STR_TO_DATE(sp.date_to,'%Y-%m-%d') >= STR_TO_DATE(?,'%Y-%m-%d')
                            )
						  LEFT JOIN seg_case_rate_special spe
						    ON sp.code = spe.sp_package_id
						  LEFT JOIN seg_caserate_acr sca
						    ON sca.package_id = sp.code
                          LEFT JOIN seg_ops_rvu_phic sorp 
                            ON sp.code = sorp.code 
						WHERE sed.encounter_nr = ?
						  AND sed.is_deleted = 0
						
						UNION
						
						SELECT
						  smod.ops_code AS CODE,
						  IF(
						    smod.ops_code IN ($not_allowed_multiple)
						    OR smod.ops_code NOT IN $allowed_multiple
						    AND p.description NOT LIKE '%Debridement%',
						    1,
						    SUM(num_sessions)
						  ) AS num_sessions,
						  laterality,
						  p.*,
						  spe.*,
						  sca.acr_groupid,
                          sorp.rvu, smod.op_date AS operation_date
						FROM
						  seg_misc_ops smo
						  INNER JOIN seg_misc_ops_details smod
						    ON smo.refno = smod.refno
						  INNER JOIN seg_case_rate_packages p
    					    ON p.code = smod.ops_code AND
                            (
                                STR_TO_DATE(p.date_from,'%Y-%m-%d') <= STR_TO_DATE(?,'%Y-%m-%d') AND
                                STR_TO_DATE(p.date_to,'%Y-%m-%d') >= STR_TO_DATE(?,'%Y-%m-%d')
                            )
						  LEFT JOIN seg_case_rate_special spe
						    ON p.code = spe.sp_package_id
						  LEFT JOIN seg_caserate_acr sca
						    ON sca.package_id = smod.ops_code
                          LEFT JOIN seg_ops_rvu_phic sorp 
                            ON p.code = sorp.code 
						WHERE smo.encounter_nr = ?
						GROUP BY smod.ops_code, laterality
						ORDER BY (package * num_sessions) DESC ;";

        $parameters = array(
            $admissionDate,
            $admissionDate,
            $enc,
            $admissionDate,
            $admissionDate,
            $enc
        );
        // var_dump($this->sql);var_dump($parameters);
        if($result = $db->Execute($this->sql,$parameters)){
            if($result->RecordCount()){
                return $result;
            }
        }

        return false;
    }

    #updated by Nick, 4/15/2014 - order by entry_no
    function searchIcd($enc){
        global $db;

        $this->sql = "SELECT
						  sd.code,
						  sd.description AS description,
						  sd.diagnosis_nr AS diagnosis_nr,
						  sd.type_nr AS type_nr,
						  sd.code_alt AS code_alt,
						  e.`consulting_dr_nr` AS dr,
						  e.is_confidential AS conf,
              sd.entry_no as entry_no,
              sd.modify_id as modify_id,
              sd.create_id as create_id,
              sd.modify_time as modify_time,
              sd.create_time as create_time
						FROM
						  seg_encounter_diagnosis AS sd
						  INNER JOIN care_encounter AS e
						    ON e.encounter_nr = sd.encounter_nr
						WHERE sd.encounter_nr = ".$db->qstr($enc)."
						AND e.status NOT IN ('deleted','hidden','inactive','void')
						AND sd.is_deleted = 0 ORDER BY sd.entry_no ASC";

        if($this->res['info']=$db->Execute($this->sql)){
            if($this->res['info']->RecordCount()){
                return $this->res['info'];
            }else{return FALSE; };
        }else{return FALSE; }
    }



    function delICD($diagnosis_nr, $create_id){
        //$this->useCode('icd',$tabs);

        $history =$this->ConcatHistory("Deleted ".date('Y-m-d H:i:s')." ".$create_id."\n");
        $this->sql = "UPDATE seg_encounter_diagnosis SET status='deleted',history=".$history." ".
            "\n WHERE diagnosis_nr = $diagnosis_nr";
        return $this->Transact();
    }

    function getSavedICDinfo($code,$enc){
        global $db;

        $this->sql = "SELECT diagnosis_nr FROM seg_encounter_diagnosis WHERE is_deleted='0' AND encounter_nr=".$db->qstr($enc)." AND code=".$db->qstr($code);

        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }
    }


    /**
     * Created By Jarel
     * Created On 02/19/2014
     * Get the automatic excess amount of specific procedures
     * @param string code
     * @return string $amount
     **/
    function getOpsAdditional($code)
    {
        global $db;

        $sql = "SELECT * FROM seg_ops_auto_excess WHERE code = ".$db->qstr($code);

        if($result = $db->Execute($sql)){
            if($result->RecordCount()){
                while($row = $result->FetchRow()){
                    return $row['amount'];
                }
            }else{return 0;}
        }else{return 0;}
    }

    //added by Nick 07-15-2014
    function getPatientSpecialProcedures($encounter_nr){
        global $db;
        $sql = "SELECT
                  smo.encounter_nr,
                  smop.ops_code AS package_id,
                  IFNULL(sbcd_scht.is_availed, 0) AS is_availed,
                  scrp.case_type,
                  sbe.`is_final`
                FROM
                  seg_misc_ops AS smo
                  INNER JOIN seg_misc_ops_details AS smop
                    ON smo.refno = smop.refno
                  INNER JOIN seg_case_rate_special AS scrs
                    ON scrs.sp_package_id = smop.ops_code
                  INNER JOIN seg_case_rate_packages as scrp
                    ON scrp.code = smop.ops_code
                  LEFT JOIN seg_billing_encounter sbe 
                    ON sbe.`encounter_nr` = smo.`encounter_nr` AND sbe.is_deleted IS NULL
                  LEFT JOIN
                    (SELECT
                      encounter_nr,
                      '99432' AS package_id,
                      is_availed
                    FROM
                      seg_caserate_hearing_test AS scht
                    UNION
                    ALL
                    SELECT
                      encounter_nr,
                      '99460' AS package_id,
                      is_availed
                    FROM
                      seg_caserate_hearing_test AS scht2
                    UNION
                    ALL
                    SELECT
                      encounter_nr,
                      package_id,
                      is_availed
                    FROM
                      seg_billing_caserate_details AS sbcd) AS sbcd_scht
                    ON sbcd_scht.encounter_nr = smo.encounter_nr
                    AND sbcd_scht.package_id = smop.ops_code
                WHERE smo.encounter_nr = ?";
        $rs = $db->Execute($sql,$encounter_nr);
        if($rs){
            $response = $this->getDetailedProcedures($rs->GetRows());
            return $response;
        }else{
            return null;
        }
    }

    //added by Nick 07-15-2014
    function getDetailedProcedures($rows){
        $ret = array();
        foreach ($rows as $key => $row) {
            foreach ($this->special_procedures as $spKey => $sp) {
                if($row['package_id'] == $spKey){
                    array_push($ret,array_merge($row,$sp));
                }
            }
        }
        return $ret;
    }

    //added by Nick 07-15-2014
    function setPatientSpecialProcedureDetails($code){
        global $db;
        $table = $this->getTableByCode($code);
        $tableName = $this->getTableNameByCode($code);
        if(is_array($table) && count($table)){
            $rs = $db->Replace($tableName,$table['fields'],$table['primary_keys']);
            if($rs){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    //added by Nick 07-15-2014
    function getTableByCode($code){
        $tableName = $this->getTableNameByCode($code);
        $table = $this->tables[$tableName];
        return $table;
    }

    //added by Nick 07-15-2014
    function getTableNameByCode($code){
        $specialProcedures = new Icd_Icp();
        $tableName = $specialProcedures->special_procedures[$code]['table_name'];
        return $tableName;
    }

    /*
    *@author : art 02/03/15 for spmc145
    */
    function getProcedureDetails($encounter_nr,$code, $refno){
        global $db;
        $sql = "SELECT 
                  smod.`refno`,
                  smod.`entry_no`,
                  smod.`ops_code`,
                  smod.`op_date`,
                  smod.`lmp_date`,
                  smod.`prenatal_dates`,
                  scrp.`is_prenatal` 
                FROM
                  seg_misc_ops smo 
                  INNER JOIN seg_misc_ops_details smod 
                    ON smo.`refno` = smod.`refno`
                  INNER JOIN seg_case_rate_packages scrp 
                    ON smod.`ops_code` = scrp.`code`  
                WHERE smo.`encounter_nr` = ".$db->qstr($encounter_nr)."
                  AND smod.`ops_code` =" .$db->qstr($code)."
                  AND smo.`refno` = ".$db->qstr($refno);
        if ($rs = $db->GetAll($sql)) {
            return $rs;
        }else{
            return false;
        } 
    }
    /*
    *@author : art 02/03/15 for spmc145
    */
    function updateLmpDate($op_date,$lmp_date,$prenate_dates,$ops_code,$refno,$entry_no){
        global $db;
        $params = array($op_date,$lmp_date,$prenate_dates,$ops_code,$refno,$entry_no);
        $sql = $db->Prepare('UPDATE seg_misc_ops_details SET op_date = ?, lmp_date = ?, prenatal_dates = ? WHERE ops_code = ? AND refno = ? AND entry_no = ?');
        
        if ($db->Execute($sql,$params)) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * @author Nick 6-4-2015
     */
    public static function getAdmissionDate($encounterNr){
        global $db;
        return $db->GetOne("SELECT encounter_date FROM care_encounter WHERE encounter_nr = ?",$encounterNr);
    }

    /**
     * @author Gervie 09/15/2015
     *
     * Additional conditions in claiming ICP code: 67010 and 67005 as first case rate.
     * This is applicable only for patients that has encounter date from June 01, 2015 onwards.
     * Base on PHIC-Circular 008-2015.
     */
    function additionalCondition($encNr, $code, $laterality){
        global $db;

        $encounter = $db->qstr($encNr);
        $ops_code = $db->qstr($code);
        $lateral = $db->qstr($laterality);

        if($laterality == 'B'){
            $smod = "(".$lateral.")";
        }
        else{
            $smod = "(".$lateral.",'B')";
        }

        $sql = "SELECT * FROM care_encounter
                WHERE encounter_nr = " . $encounter . "
                AND encounter_date >= STR_TO_DATE('2015-06-01', '%Y-%m-%d')";

        if($result = $db->Execute($sql)){
            if($result->RecordCount() >= 1){
                $sql2 = "SELECT DISTINCT
                           sod.ops_code,
                           sod.laterality
                         FROM
                           seg_misc_ops so
                         INNER JOIN seg_misc_ops_details sod
                           ON so.refno = sod.refno
                           AND sod.laterality IN ". $smod ."
                         WHERE so.encounter_nr = ". $encounter ."
                         AND sod.ops_code IN (". $ops_code .",'65710', '65730', '65750', '65755')";

                if($res = $db->Execute($sql2)){
                    if($res->RecordCount() <= 1){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
            }
            else{
                return false;
            }
        }
        else {
            return false;
        }
    }

    function getTempMultiplier($enc, $code){
      global $db;

      $encounter = $db->qstr($enc);
      $ops_code = $db->qstr($code);

      $sql = "SELECT 
                  sct.`saved_multiplier`
              FROM
                  seg_caserate_trail sct  
              WHERE sct.`encounter_nr` = ".$encounter." 
              AND sct.`package_id` = ".$ops_code;

      if($result = $db->GetOne($sql)) {
          if($result) {
              return $result;
          }
          else {
              return false;
          }
      }
      else {
          return false;
      }
    }

    /**
     * @author Gervie 06/02/2016
     */
    function getMultiplier($enc_nr, $code) {
        global $db;

        $encounter = $db->qstr($enc_nr);
        $ops_code = $db->qstr($code);

        $sql = "SELECT 
                    sbc.`saved_multiplier`
                FROM
                    seg_billing_caserate sbc 
                INNER JOIN seg_billing_encounter sbe 
                    ON sbe.`bill_nr` = sbc.`bill_nr` 
                WHERE sbe.`encounter_nr` = ".$encounter." 
                AND sbc.`package_id` = ".$ops_code."
                ANd sbe.`is_deleted` IS NULL";

        if($result = $db->GetOne($sql)) {
            if($result) {
                return $result;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

}//end class icd_icp
