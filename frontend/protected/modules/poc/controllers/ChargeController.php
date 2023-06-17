<?php
//use SegHis\modules\phic\models\AdditionalLimit;
use SegHis\models\encounter\Encounter;
use SegHis\models\Bill;

class ChargeController extends \Controller {    
    
    public function actionGetPhicCoverage()
    {        
        $baseUrl = Yii::app()->basePath;
        $baseUrl = str_replace("\\", "/", $baseUrl);
        
        $j = stripos($baseUrl, "frontend");
        $baseUrl = substr($baseUrl, 0, $j);        
        
        include $baseUrl.'include/care_api_classes/billing/class_billing_new.php';        
                
        $encounter_nr = json_decode($_POST["encNo"]);
        
        // Compute PHIC limits ...
        $bill = new Billing();
        $bill_dt = strftime("%Y-%m-%d %H:%M:%S");
        $bill_frmdt = Encounter::getEncounterDate($encounter_nr);
        $death_dt = $bill->getDeathDate($encounter_nr);
        
        $billinfo = $bill->hasSavedBill($encounter_nr);
        if ($billinfo) {
            if ($billinfo['is_final'] == 1) {
                $bill->setBillArgs($encounter_nr, $billinfo['bill_dte'], $billinfo['bill_frmdte'], $death_dt, $billinfo['bill_nr']);
            }
            else {
                $bill->setBillArgs($encounter_nr, $bill_dt, $billinfo['bill_frmdte'], $death_dt, $billinfo['bill_nr']);
            }
        } 
        else {
            $bill->setBillArgs($encounter_nr, $bill_dt, $bill_frmdt, $death_dt);
        }        
                
        if ($bill->isPHIC()) {
            $limit = $bill->getEncounterLimit($encounter_nr);
            $def_limit = $bill->getDefaultLimit();
            $additional = $bill->getTotalAdditionalLimit($encounter_nr);
            $xlo_covered = $bill->getActualSrvCoverage(Bill::PHIC_ID);
            
            if ($limit) {
                if($limit['xlo'] != false) {
                    $xlo = $limit['xlo'] + $additional['xlo'];
                }
                else{
                    $xlo = $def_limit['xlo'] + $additional['xlo'];
                }
                $xloCov = $xlo - $xlo_covered;
            }
            else {
                $xloCov = 0;
            }
        }
        else {
            $xloCov = NULL;
        }
        
        echo CJSON::encode(is_null($xloCov) ? 'NULL' : (float)$xloCov);
        
//        $bill_date = strftime("%Y-%m-%d %H:%M:%S");        
//        $bc = new Billing($encounter_nr, $bill_date);
//        
//        $bc->getConfinementType();
//
//        $total_coverage = $bc->getActualSrvCoverage(Yii::app()->params['PHIC']);
//        $total_benefits = $bc->getConfineBenefits('HS', NULL, 0, TRUE);                
//        
//        $additional = AdditionalLimit::getXLOLimitAdded($encounter_nr);        
//        echo CJSON::encode((float)$additional + (float)$total_benefits-(float)$total_coverage);
    }                   
}