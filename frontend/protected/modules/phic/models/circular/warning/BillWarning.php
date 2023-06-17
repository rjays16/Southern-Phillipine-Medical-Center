<?php
namespace SegHis\modules\phic\models\circular\warning;

use SegHis\models\encounter\Diagnosis;
use SegHis\models\encounter\Encounter;
use SegHis\modules\phic\models\EncounterInsurance;
use SegHis\models\Bill;
use \EncounterType;
use \Config;

/**
 * Class BillWarning
 * @package SegHis\modules\phic\models\circular\warning
 * @author Nick B. Alcala 3-2-2016
 */
class BillWarning
{

    /**
     * @var BaseBillWarning[]
     */
    public static $warningObjects = array();

    public static function add(BaseBillWarning $warning)
    {
        BillWarning::$warningObjects[] = $warning;
    }

    public static function getWarnings($encounterNr, $billInfo)
    {

        /* @var $encounter Encounter */
        $encounter = Encounter::model()->find(array(
            'condition' => 'encounter_nr = :encounterNr',
            'params' => array(':encounterNr' => $encounterNr),
            'select' => 'pid,encounter_date,encounter_type,er_opd_diagnosis,parent_encounter_nr,admission_dt'
        ));

        /* @var $insurance EncounterInsurance */
        $insurance = EncounterInsurance::model()->find(array(
            'condition' => 'encounter_nr = :encounterNr',
            'params' => array(':encounterNr' => $encounterNr)
        ));

        /**
         * Query all diagnosis so that all other subclasses of BaseBillWarning
         * don't have to query specific diagnosis for their validation.
         * @var $diagnosis Diagnosis[]
         */
        $diagnosis = Diagnosis::model()
            ->with('caseRate')
            ->filterByEncounter($encounterNr)
            ->filterActive()
            ->findAll();
        //edited and added by Kenneth 04-29-16
        $withInsurance=true;
        $forDiagnosis = false;
        $iswellbaby = false;
        $warnings = array();

        /* no warning when no insurance */
        $row = \Yii::app()->db->createCommand("SELECT encounter_type FROM care_encounter WHERE encounter_nr='" . $encounterNr . "'")->queryRow(); //edited by Kenneth 04-23-2016

        if(!($row['encounter_type']==3||$row['encounter_type']==4||$row['encounter_type']==1||$row['encounter_type']== (int)EncounterType::TYPE_IPBM_IPD)){ $withInsurance = false;}
        
        if(!($row['encounter_type'] == (int)EncounterType::TYPE_DIALYSIS||$row['encounter_type']== (int)EncounterType::TYPE_IC)){ $forDiagnosis = true; }

        if(($row['encounter_type']==3||$row['encounter_type']==4||$row['encounter_type']==(int)EncounterType::TYPE_IPBM_IPD)){ $isIPD = true;}

        if (!$insurance && $row['encounter_type']!=1) {
             $withInsurance=false;
        }

        /* no warning when the insurance used is not PhilHealth */
        if ($insurance->hcare_id != 18 && $row['encounter_type']!=1) {
             $withInsurance=false;
        }

        if($withInsurance){
            foreach (BillWarning::$warningObjects as $warning) {
                // pede pud himuon ug properties instead of parameters lang
                
                $class = explode('\\', get_class($warning));
                if((end($class) != 'HasOverlappingOfDates')&&(end($class) != 'HasMissingDates')){
                    if (!$warning->validate($encounter, $insurance, $diagnosis, $billInfo)) {

                        $warnings[] = $warning->getWarningMessage();
                    }
                }
            }
        }elseif($forDiagnosis){
            

            foreach (BillWarning::$warningObjects as $warning) {
                // pede pud himuon ug properties instead of parameters lang
                
                $class = explode('\\', get_class($warning));

                if($row['encounter_type']==(int)EncounterType::TYPE_WELLBABY)
                    $condition = (end($class) == 'NoAdmittingDiagnosis');
                else
                    $condition = ((end($class) == 'NoAdmittingDiagnosis')||(end($class) == 'NoFinalDiagnosis'));
                
                if($condition){
                    if (!$warning->validate($encounter, $insurance, $diagnosis, $billInfo)) {

                        $warnings[] = $warning->getWarningMessage();
                    }
                }
            }
        }
        //end Kenneth

        // added by carriane 03/04/19; for Overlapping and Missing of Dates in accommodation prompt
        $accom_effectivity = \Config::model()->findByPk('ACCOMMODATION_REVISION');
        $patientBill = Bill::model()->patientbillInfo($encounterNr);

        $bill_dte = $billInfo["billDate"];

        /*if($patientBill->bill_dte == NULL)
            $patientBill->bill_dte = date('Y-m-d H:i:s');*/

        if($isIPD && ($encounter->admission_dt > $accom_effectivity->value)){

            /*if($patientBill->bill_frmdte != '') $bill_frmdte = $patientBill->bill_frmdte;
            else $bill_frmdte = $encounter->admission_dt;

            if($patientBill->bill_dte != '') $billInfo["billDate"] = $patientBill->bill_dte;*/
            if($patientBill->is_final){
                $billInfo["billDate"] = $patientBill->bill_dte;
                $bill_frmdte = $patientBill->bill_frmdte;
            }else{
                $bill_frmdte = $encounter->admission_dt;
            }


            $getDeathDate = \Yii::app()->db->createCommand("SELECT CONCAT(p.death_date,' ',p.death_time) as deathdate FROM care_person p WHERE death_encounter_nr ='".$encounterNr."'")->queryAll();

            if($getDeathDate){
                $bill_dte = $getDeathDate[0]['deathdate'];
                $details['deathdate'] = $getDeathDate[0]['deathdate'];

                $deathcondiUnion = " AND (IF(sel.is_per_hour, STR_TO_DATE(CONCAT(DATE_FORMAT(occupy_date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(occupy_time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" .$bill_frmdte. "' "." AND STR_TO_DATE(CONCAT(DATE_FORMAT(occupy_date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(occupy_time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= '".$bill_dte."', STR_TO_DATE(DATE_FORMAT(occupy_date_from, '%Y-%m-%d'), '%Y-%m-%d') >= '" .date("Y-m-d",strtotime($bill_frmdte)). "' "." AND STR_TO_DATE(DATE_FORMAT(occupy_date_from, '%Y-%m-%d'), '%Y-%m-%d') <= '" . date("Y-m-d",strtotime($bill_dte)) . "'))";
            }

            $deathcondi = " AND (STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" .$bill_frmdte. "' " .
                    "and STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= '" . $bill_dte."')";

            $accomSql = "SELECT location_nr AS room, cw.nr AS ward_id, CONCAT(ctr.name, ' (', cw.name, ')') AS NAME, date_from, date_to, time_from, time_to, cel.status, 'AD' AS source FROM ((care_encounter_location AS cel INNER JOIN care_ward AS cw on cel.group_nr = cw.nr INNER JOIN care_encounter AS ce ON ce.encounter_nr = cel.encounter_nr) LEFT JOIN seg_encounter_location_rate AS selr ON cel.nr = selr.loc_enc_nr AND cel.encounter_nr = selr.encounter_nr) INNER JOIN (care_room AS cr INNER JOIN care_type_room AS ctr ON cr.type_nr = ctr.nr) ON cel.location_nr = cr.room_nr AND cel.group_nr = cr.ward_nr LEFT JOIN seg_encounter_location_addtl `sela` ON cel.encounter_nr = sela.encounter_nr WHERE (cel.encounter_nr ='".$encounterNr."') AND cel.is_deleted <> 1 AND EXISTS (SELECT nr FROM care_type_location AS ctl WHERE UPPER(type) = 'ROOM' AND ctl.nr = cel.type_nr) AND ((STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' AND STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '".$bill_dte."') OR (STR_TO_DATE(CONCAT(DATE_FORMAT(date_to, '%Y-%m-%d'), ' ', DATE_FORMAT(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' AND STR_TO_DATE(CONCAT(DATE_FORMAT(date_to, '%Y-%m-%d'), ' ', DATE_FORMAT(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '".$bill_dte."') OR (STR_TO_DATE(CONCAT(DATE_FORMAT(IFNULL(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', DATE_FORMAT(IFNULL(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00')".$deathcondi.")";

            $accomSql .= "UNION SELECT cr.room_nr, sel.group_nr AS ward_id, CONCAT(ctr.name, ' (', cw.name, ')') AS NAME, sel.occupy_date_from AS date_from, sel.occupy_date_to AS date_to, '00:00:00' AS time_from, '00:00:00' AS time_to, '', 'BL' AS source FROM (seg_encounter_location_addtl AS sel INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr) INNER JOIN (care_room as cr INNER JOIN care_type_room AS ctr ON cr.type_nr = ctr.nr) ON sel.room_nr = cr.nr AND sel.group_nr = cr.ward_nr WHERE (sel.encounter_nr = '".$encounterNr."') AND sel.is_deleted <> 1 ".$deathcondiUnion." ORDER BY STR_TO_DATE(CONCAT(date_from, ' ', time_from),'%Y-%m-%d %H:%i:%s') ASC, STR_TO_DATE(CONCAT(IF(date_to = '0000-00-00',DATE_FORMAT(NOW(), '%Y-%m-%d'), date_to),' ',DATE_FORMAT(IFNULL(time_to, '00:00:00'),'%H:%i:%s')),'%Y-%m-%d %H:%i:%s') ASC";

            $accommodations = \Yii::app()->db->createCommand($accomSql)->queryAll();
            
            $a = 0;
            $ward_room = array();

            foreach($accommodations as $i => $accommodation){
                if($accommodations[$i]['NAME'] != NULL){
                    $temp_ward_room = $accommodations[$i]['ward_id']."_".$accommodations[$i]['room'];

                    if(!in_array($temp_ward_room, $ward_room)){
                        $ward_room[$a] = $temp_ward_room;
                        $details[$a] = $accommodations[$i];
                        $a++;
                    }else{
                        $index = array_keys($ward_room, $temp_ward_room);
                        $found = 0;
                        if(count($index) > 1){
                            foreach($index as $data){
                                if((strtotime($details[$data]['date_to']) == strtotime($accommodations[$i]['date_from'])) && ($temp_ward_room == $accommodations[$i-1]['ward_id']."_".$accommodations[$i-1]['room'])){
                                    if($accommodations[$i]['date_to'] == '0000-00-00'){
                                        $accommodations[$i]['date_to'] = date("Y-m-d");
                                        $accommodations[$i]['time_to'] = date("H:m:s");
                                    }

                                    $details[$data]['date_to'] = $accommodations[$i]['date_to'];
                                    $details[$data]['hrs_stay'] += $accommodations[$i]['hrs_stay']; 
                                    $details[$data]['source'] = $accommodations[$i]['source'];
                                    $details[$data]['status'] = $accommodations[$i]['status'];
                                    $found = 1;
                                }
                            }
                            
                            if(!$found){
                                $details[$a] = $accommodations[$i];
                                $ward_room[$a] = $temp_ward_room;
                                $a++;
                            }

                        }else{
                            if((strtotime($details[$index[0]]['date_to']) == strtotime($accommodations[$i]['date_from'])) && ($temp_ward_room == $accommodations[$i-1]['ward_id']."_".$accommodations[$i-1]['room'])){

                                $temp_date_to = $accommodations[$i]['date_to'];
                                $temp_time_to = $accommodations[$i]['time_to'];
                                if($accommodations[$i]['date_to'] == '0000-00-00'){
                                    $temp_date_to = date("Y-m-d");
                                    $temp_time_to = date("H:m:s");
                                }
                                
                                if($temp_date_to > $details[$index[0]]['date_to']){
                                    $details[$index[0]]['date_to'] = $temp_date_to;
                                    $details[$index[0]]['time_to'] = $temp_time_to;
                                    $details[$index[0]]['hrs_stay'] += $accommodations[$i]['hrs_stay'];
                                }
                                $details[$index[0]]['source'] = $accommodations[$i]['source'];
                                $details[$index[0]]['status'] = $accommodations[$i]['status'];
                            }else{
                                $details[$a] = $accommodations[$i];
                                $ward_room[$a] = $temp_ward_room;
                                $a++;
                            }
                        }
                    }
                }
            }
            
            if(count($details) > 1){
                $tempdetails = array();
                foreach($details as $key => $detail){
                    $c = $key;
                    $b=$key-1;
                    while ($b > -2) {
                        $change = 0;
                        if($details[$c]['date_from'] <= $details[$b]['date_from'] && $details[$b]['date_from'] != NULL){
                            
                            $tempdateto = $details[$c]['date_to'];
                            if($details[$c]['date_to'] == '0000-00-00')
                                $tempdateto = strftime("Y-m-d");

                            if($details[$c]['date_from'] == $details[$b]['date_from'] && $tempdateto  < $details[$b]['date_to']){
                                $change = 1;
                            }
                            elseif($details[$c]['date_from'] < $details[$b]['date_from']){
                                $change = 1;
                            }
                            if($change){
                                $tempdetails[$key] = $details[$c];
                                $details[$c] = $details[$b];
                                $details[$b] = $tempdetails[$key];
                                $c--;
                            }
                        }else if($details[$c]['date_from'] > $details[$b]['date_from']){
                            break;
                        }
                        $b--;
                    }
                }
            }
            // echo "<pre>" . print_r($details,true) . "</pre>";die;
            foreach (BillWarning::$warningObjects as $warning) {
                $class = explode('\\', get_class($warning));
                if((end($class) == 'HasOverlappingOfDates')||(end($class) == 'HasMissingDates')){
                    if (!$warning->validate($encounter, $insurance, $details, $billInfo)) {
                        $warnings[] = $warning->getWarningMessage();
                    }
                }
            }
        }
        // end carriane

        return $warnings;
    }

 public static function getValidateCovid($encounterNr, $billInfo)
    {

        if(Config::model()->getValidateCovid($billInfo['billDate'])){
            return true;
        }else{
            return false;
        }
      
    }


}