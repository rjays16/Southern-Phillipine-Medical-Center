<?php
/* Created by Nick on 9/1/14 */
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_insurance.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');
$objIns = new Insurance;

define(cutoffdate, 20140800);
define(Others,8);
$searchKey = $_REQUEST['searchKey'];
$page = (int)$_REQUEST['page'];
$maxRows = (int)$_REQUEST['mr'];
$offset = ($page - 1) * $maxRows;
$sortDir = $_REQUEST['dir'] == '1' ? 'ASC' : 'DESC';
$sortName = $_REQUEST['sort'];

$params = array(
    'searchKey' => $searchKey,
    'page' => $page,
    'maxRows' => $maxRows,
    'offset' => $offset,
    'sortDir' => $sortDir,
    'sortName' => $sortName
);

$data = array();

switch ($_REQUEST['action']){
    case 'encounter-insurance-list':
            getEncounterInsuranceItems($params);
        break;
    case 'person-insurance-list':
            getPersonInsuranceItems($params);
        break;
    case 'delete-encounter-insurance':
        deleteEncounterInsurance();
        break;
    case 'mother-birth-cert-list':
            getBirthCerth($params);
        break;
}

function deleteEncounterInsurance(){
    global $db;
    $db->BeginTrans();

    $sql = $db->Prepare("DELETE FROM seg_encounter_insurance WHERE encounter_nr = ? AND hcare_id = ?");
    $rs1 = $db->Execute($sql,array($_REQUEST['encounter_nr'],$_REQUEST['hcare_id']));

    // Comment by jeff 02-09-18 for aletring deletion of insurance on table seg_insurance_member_info.
    // $sql = $db->Prepare("DELETE FROM seg_insurance_member_info WHERE pid = ? AND hcare_id = ?");
    // $rs2 = $db->Execute($sql,array($_REQUEST['pid'],$_REQUEST['hcare_id']));

    $sql = $db->Prepare("DELETE FROM seg_encounter_insurance_memberinfo_first WHERE pid = ? AND hcare_id = ? AND encounter_nr = ?");//created by Kenneth 09/28/2016
    $rs3 = $db->Execute($sql,array($_REQUEST['pid'],$_REQUEST['hcare_id'],$_REQUEST['encounter_nr']));//created by Kenneth 09/28/2016

    if($rs1 && $rs3){//created by Kenneth 09/28/2016 | Mod by jeff 02-09-18
       $updatehis = UpdateAuditEncounterInsurance();
       if($updatehis){
          $db->CommitTrans();
          $response = array('result'=>true);
       }else{
          $db->RollbackTrans();
          $response = array('result'=>false,'error'=>$db->ErrorMsg());
       }
    }else{
        $db->RollbackTrans();
        $response = array('result'=>false,'error'=>$db->ErrorMsg());
    }
    echo json_encode($response);
}

function UpdateAuditEncounterInsurance(){
    global $db;

    $getsql  = "SELECT history FROM seg_encounter_insurance_memberinfo WHERE encounter_nr = " . $db->qstr($_REQUEST['encounter_nr']) . " LIMIT 1";
    $HistoryResult =  $db->GetRow($getsql);
    $getReason = "SELECT reason_description FROM seg_insurance_delete_reasons WHERE reason_id = ".$_REQUEST['reason']."";
    $gotReason = $db->GetRow($getReason);
    $data = "encounter_nr=".$_REQUEST['encounter_nr'].",pid=".$_REQUEST['pid'].",modify_id=".$_SESSION['sess_login_userid'];

    if(isset($HistoryResult['history']) && !empty($HistoryResult['history']) && $_REQUEST['reason'] != Others){
      $history = $HistoryResult['history'] . "Deleted by " . $_SESSION['sess_user_name'] . " on " . date('Y-m-d H:i:s'). " Reason: ". $gotReason['reason_description'] . "\n". $data."\n\n";
    }
    elseif($_REQUEST['reason'] == Others){
      if (empty($_REQUEST['other_reason'])) {
        return false;
      }
      else{
       $history = $HistoryResult['history'] . "Deleted by " . $_SESSION['sess_user_name'] . " on " . date('Y-m-d H:i:s'). " Reason: ". $gotReason['reason_description'] . ": " .trim(preg_replace('/\s+/', ' ',utf8_decode($_REQUEST['other_reason']))). "\n". $data."\n\n";
      }

      }
    
    else{
      $history =" ";
    }

   /* $string = trim(preg_replace('/\s+/', ' ', $string));*/
    $sql = "UPDATE seg_encounter_insurance_memberinfo `seim` 
            SET
            history =".$db->qstr($history)."
            WHERE pid = ".$db->qstr($_REQUEST['pid'])."
            AND encounter_nr = ".$db->qstr($_REQUEST['encounter_nr']);
           # echo "" .$sql; die();
    $result = $db->Execute($sql);
    if($result){
      return true;
    }else{
      return false;
    }
}

function getEncounterInsuranceItems($params){
    global $db;

    extract($params);
    $data = array();
    $total = 0;


    $InsuranceEncounterItemSql = "SELECT cif.`firm_id`,
                                      IF(sei.`remarks` = '1' OR sei.`remarks` IS NULL, seim.`insurance_nr`, siro.`title`) AS insurance_nr,
                                      IF(seim.`relation` = 'M', '1','0') AS is_principal,
                                      seim.`pid`,
                                      sei.`hcare_id`
                              FROM seg_encounter_insurance `sei`
                              INNER JOIN seg_encounter_insurance_memberinfo `seim`
                              ON sei.`encounter_nr` = seim.`encounter_nr`
                              INNER JOIN care_insurance_firm `cif`
                              ON cif.`hcare_id` = sei.`hcare_id`
                              LEFT JOIN seg_insurance_remarks_options siro
                              ON sei.`remarks` = siro.`id`
                              WHERE sei.`encounter_nr` = ".$db->qstr($_REQUEST['encounter_nr'])."
                              ORDER BY $sortName $sortDir LIMIT $offset, $maxRows";
    if($InsranceEncounterItemResult = $db->GetAll($InsuranceEncounterItemSql)){
        $total = count($InsranceEncounterItemResult);
        for ($i=0; $i < count($InsranceEncounterItemResult); $i++) { 
            $data[] = array(
                  'firm_id' => $InsranceEncounterItemResult[$i]["firm_id"],
                  'insurance_nr' => $InsranceEncounterItemResult[$i]['insurance_nr'],
                  'is_principal' => $InsranceEncounterItemResult[$i]["is_principal"],
                  'hcare_id' => $InsranceEncounterItemResult[$i]['hcare_id'],
              );
        }
    }else{
        $BillDate = $db->GetOne("SELECT bill_frmdte FROM seg_billing_encounter WHERE is_final = '1' AND ISNULL(is_deleted) AND encounter_nr = ".$db->qstr($_REQUEST['encounter_nr']));

        $BillDate = date("Ymd", strtotime($BillDate));

        if($BillDate == '19700101'){
          $BillDate = 20140801;
        }

        if(cutoffdate > $BillDate){
              $OldInsurancePidItemSql = "SELECT cif.`firm_id`,
                                         cpi.`insurance_nr`,
                                         cpi.`is_principal`,
                                         cpi.`pid`,
                                         sei.`hcare_id`
                                  FROM seg_encounter_insurance `sei`
                                  INNER JOIN care_encounter `ce`
                                  ON ce.`encounter_nr` = sei.`encounter_nr`
                                  INNER JOIN care_person_insurance `cpi`
                                  ON cpi.`pid` = ce.`pid`
                                  INNER JOIN care_insurance_firm `cif`
                                  ON cif.`hcare_id` = sei.`hcare_id`
                                  WHERE sei.`encounter_nr` = ".$db->qstr($_REQUEST['encounter_nr']);

          if($OldInsurancePidItemResult = $db->GetAll($OldInsurancePidItemSql)){
            $total = count($OldInsurancePidItemResult);

            for ($i=0; $i < count($OldInsurancePidItemResult); $i++) { 
              $data[] = array(
                  'firm_id' => $OldInsurancePidItemResult[$i]["firm_id"],
                  'insurance_nr' => $OldInsurancePidItemResult[$i]['insurance_nr'],
                  'is_principal' => $OldInsurancePidItemResult[$i]["is_principal"],
                  'hcare_id' => $OldInsurancePidItemResult[$i]['hcare_id'],
              );
            }

          }

      }


    }

    $response = array(
        'currentPage' => $page,
        'total' => $total,
        'data' => $data
    );

    echo json_encode($response);
}

function getPersonInsuranceItems($params){
    global $db;
    $encounter = new Encounter();

    extract($params);
    $data = array();
    $total = 0;

    $encounterType = $encounter->EncounterType($_REQUEST['encounter_nr']);
    

    $InsuranceEncounterSql = "SELECT IF(sei.`remarks` = '1' OR sei.`remarks` IS NULL, seim.`insurance_nr`, siro.`title`) AS insurance_nr,
                                      IF(seim.`relation` = 'M', '1','0') AS is_principal,
                                      seim.`pid`,
                                      cif.`firm_id`
                              FROM seg_encounter_insurance_memberinfo `seim`
                              INNER JOIN care_insurance_firm `cif`
                              ON cif.`hcare_id` = seim.`hcare_id`
                              INNER JOIN seg_encounter_insurance sei
                              ON sei.`encounter_nr` = seim.`encounter_nr`
                              LEFT JOIN seg_insurance_remarks_options siro
                              ON sei.`remarks` = siro.`id`
                              WHERE seim.`encounter_nr` = ".$db->qstr($_REQUEST['encounter_nr'])."
                              AND seim.`pid` = ".$db->qstr($_REQUEST['pid'])."
                              ORDER BY $sortName $sortDir LIMIT $offset, $maxRows";
    
    if($InsranceEncounterResult = $db->GetAll($InsuranceEncounterSql)){
      
      $total = count($InsranceEncounterResult);
      if($total){
        for ($i=0; $i < count($InsranceEncounterResult) ; $i++) { 
              $data[] = array(
                  'firm_id' => $InsranceEncounterResult[$i]["firm_id"],
                  'insurance_nr' => $InsranceEncounterResult[$i]['insurance_nr'],
                  'is_principal' => $InsranceEncounterResult[$i]["is_principal"],
              );
        }
      }
        
    }
    else if($encounterType == '5') {
      $dialysisInsurance = "SELECT IF(sei.`remarks` = '1' OR sei.`remarks` IS NULL, seim.`insurance_nr`, siro.`title`) AS insurance_nr,
                              IF(seim.`relation` = 'M', '1', '0') AS is_principal, seim.`pid`, cif.`firm_id` 
                            FROM seg_encounter_insurance_memberinfo `seim` 
                            INNER JOIN care_insurance_firm `cif` 
                              ON cif.`hcare_id` = seim.`hcare_id` 
                            INNER JOIN seg_encounter_insurance sei 
                              ON sei.`encounter_nr` = seim.`encounter_nr` 
                            LEFT JOIN seg_insurance_remarks_options siro 
                              ON sei.`remarks` = siro.`id` 
                            INNER JOIN care_encounter ce 
                              ON ce.`encounter_nr` = seim.`encounter_nr` 
                              AND ce.`encounter_type` = '5' 
                            WHERE seim.`pid` = ".$db->qstr($_REQUEST['pid'])." 
                            ORDER BY seim.`encounter_nr` DESC 
                            LIMIT 1";

      if($dialysisInsuranceResult = $db->GetAll($dialysisInsurance)) {
        $total = count($dialysisInsuranceResult);
        if($total){
          for ($i=0; $i < count($dialysisInsuranceResult) ; $i++) { 
                $data[] = array(
                    'firm_id' => $dialysisInsuranceResult[$i]["firm_id"],
                    'insurance_nr' => $dialysisInsuranceResult[$i]['insurance_nr'],
                    'is_principal' => $dialysisInsuranceResult[$i]["is_principal"],
                );
          }
        }
      }
    }
    else{

        $BillDate = $db->GetOne("SELECT bill_frmdte FROM seg_billing_encounter WHERE is_final = '1' AND ISNULL(is_deleted) AND encounter_nr = ".$db->qstr($_REQUEST['encounter_nr']));
        
        $BillDate = date("Ymd", strtotime($BillDate));

        if($BillDate == '19700101'){
          $BillDate = 20140801;
        }


        if(cutoffdate > $BillDate){
            
            $OldInsurancePidSql = "SELECT cpi.`insurance_nr`,
                                          cpi.`is_principal`,
                                          cif.`firm_id`
                                    FROM care_person_insurance `cpi`
                                    INNER JOIN care_insurance_firm `cif`
                                    ON cif.`hcare_id` = cpi.`hcare_id`
                                    WHERE cpi.`pid` = ".$db->qstr($_REQUEST['pid']);

            if($OldInsurancePidResult = $db->GetAll($OldInsurancePidSql)){

              $total = count($OldInsurancePidResult);

              for ($i=0; $i < count($OldInsurancePidResult) ; $i++) { 
                  $data[] = array(
                        'firm_id' => $OldInsurancePidResult[$i]["firm_id"],
                        'insurance_nr' => $OldInsurancePidResult[$i]['insurance_nr'],
                        'is_principal' => $OldInsurancePidResult[$i]["is_principal"],
                     );
              }

            }
        }
    }

    $response = array(
        'currentPage' => $page,
        'total' => $total,
        'data' => $data
    );

    echo json_encode($response);
}

# added by: syboy 03/16/2016 : meow
function getBirthCerth($params){
  global $db;

  extract($params);
  $data = array();
  $total = 0;

    $birthCert = "SELECT 
                  cp.pid,
                  fn_get_person_name(cp.pid) AS NAME,
                  fn_get_birth_date(cp.pid) AS bday
                FROM
                  care_person AS cp
                  INNER JOIN seg_cert_birth scb
                  ON scb.pid = cp.`pid`
                WHERE cp.mother_pid = '".$_REQUEST['pid']."' 
                ORDER BY $sortName $sortDir LIMIT $offset, $maxRows";
    
    if($birthCertResult = $db->GetAll($birthCert)){
      
      $total = count($birthCertResult);
      if($total){
        for ($i=0; $i < count($birthCertResult); $i++) { 
              $data[] = array(
                  'pid' => $birthCertResult[$i]["pid"],
                  'NAME' => $birthCertResult[$i]['NAME'],
                  'bday' => $birthCertResult[$i]["bday"],
              );
        }
      }
        
    }else{
        $birthCert = "SELECT 
                        cp.pid,
                        fn_get_person_name(cp.pid) AS NAME,
                        fn_get_birth_date(cp.pid) AS bday
                      FROM
                        care_person AS cp
                        INNER JOIN seg_cert_birth scb
                        ON scb.pid = cp.`pid`
                      WHERE cp.pid = ".$db->qstr($_REQUEST['pid'])." 
                      ORDER BY $sortName $sortDir LIMIT $offset, $maxRows";

        if($birthCertResult = $db->GetAll($birthCert)){
      
            $total = count($birthCertResult);
            if($total){
              for ($i=0; $i < count($birthCertResult); $i++) { 
                    $data[] = array(
                        'pid' => $birthCertResult[$i]["pid"],
                        'NAME' => $birthCertResult[$i]['NAME'],
                        'bday' => $birthCertResult[$i]["bday"],
                    );
              }
            }
              
        }
    }

    $response = array(
        'currentPage' => $page,
        'total' => $total,
        'data' => $data
    );

    echo json_encode($response);
}
# ended syboy
