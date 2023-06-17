<?php

include 'roots.php';
include $root_path.'include/care_api_classes/billing/class_billing_new.php';
include_once $root_path.'include/inc_environment_global.php';

$instance = new AjaxBilling;
$instance->call();

/**
 * @author Nick B. Alcala 5-31-2015
 */
class AjaxBilling {

    public function call(){
        $functionName = self::getFunctionName();
        if(method_exists($this,$functionName))
            $this->$functionName();
        else {
            echo 'Page not found';
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    }

    private static function getFunctionName(){
        return 'action'.strtoupper(substr($_GET['request'],0,1)).substr($_GET['request'],1);
    }

    public function actionGetLaboratoryItems(){
        $bill = self::getBillingInstance($_GET['encounterNr'],$_GET['billDate'],$_GET['billFromDate'],$_GET['deathDate']);
        $items = $bill->getLaboratoryItems();
        $result = array();
        $total = 0;
        foreach($items as $item){
            $result[] = array(
                'refno' => $item['refno'],
                'description' => $item['service_desc'],
                'group' => $item['group_desc'],
                'source' => $item['source'],
                'service_code' => $item['service_code'],
                'encoder' => $item['encoder'] . " - " . $item['time_encoded'],
                'quantity' => $item['qty'],
                'price' => number_format($item['serv_charge'],2),
                'charge' => number_format($item['qty'] * $item['serv_charge'],2)
            );
            $total += $item['qty'] * $item['serv_charge'];
        }
        echo json_encode(array('items' => $result, 'total' => $total));
    }

    public function actionGetRadiologyItems(){
        $bill = self::getBillingInstance($_GET['encounterNr'],$_GET['billDate'],$_GET['billFromDate'],$_GET['deathDate']);
        $items = $bill->getRadiologyItems();
        $result = array();
        $total = 0;
        foreach($items as $item){
            $result[] = array(
                'refno' => $item['refno'],
                'description' => $item['service_desc'],
                'group' => $item['group_desc'],
                'source' => $item['source'],
                'service_code' => $item['service_code'],
                'encoder' => $item['encoder'] . " - " . $item['time_encoded'],
                'quantity' => $item['qty'],
                'price' => number_format($item['serv_charge'],2),
                'charge' => number_format($item['qty'] * $item['serv_charge'],2)
            );
            $total += $item['qty'] * $item['serv_charge'];
        }
        echo json_encode(array('items' => $result,'total' => $total));
    }
     public function actionGetOBGyneItems(){
        $bill = self::getBillingInstance($_GET['encounterNr'],$_GET['billDate'],$_GET['billFromDate'],$_GET['deathDate']);
        $items = $bill->getOBGyneItems();
        $result = array();
        $total = 0;
        foreach($items as $item){
            $result[] = array(
                'refno' => $item['refno'],
                'description' => $item['service_desc'],
                'group' => $item['group_desc'],
                'source' => $item['source'],
                'service_code' => $item['service_code'],
                'encoder' => $item['encoder'] . " - " . $item['time_encoded'],
                'quantity' => $item['qty'],
                'price' => number_format($item['serv_charge'],2),
                'charge' => number_format($item['qty'] * $item['serv_charge'],2)
            );
            $total += $item['qty'] * $item['serv_charge'];
        }
        echo json_encode(array('items' => $result,'total' => $total));
    }

    public function actionGetSupplyItems(){
        $bill = self::getBillingInstance($_GET['encounterNr'],$_GET['billDate'],$_GET['billFromDate'],$_GET['deathDate']);
        $items = $bill->getSupplyItems();
        $result = array();
        $total = 0;
        foreach($items as $item){
            $result[] = array(
                'deleteButton' => null,
                'refno' => $item['refno'],
                'description' => $item['service_desc'],
                'group' => $item['group_desc'],
                'source' => $item['source'],
                'service_code' => $item['service_code'],
                'encoder' => $item['encoder'] . " - " . $item['time_encoded'],
                'quantity' => $item['qty'],
                'price' => number_format($item['serv_charge'],2),
                'charge' => number_format($item['qty'] * $item['serv_charge'],2)
            );
            $total += $item['qty'] * $item['serv_charge'];
        }
        echo json_encode(array('items' => $result, 'total' => $total));
    }

    public function actionGetOtherItems(){
        $bill = self::getBillingInstance($_GET['encounterNr'],$_GET['billDate'],$_GET['billFromDate'],$_GET['deathDate']);
        $items = $bill->getOtherItems();
        $result = array();
        $total = 0;
        foreach($items as $item){
            $result[] = array(
                'deleteButton' => null,
                'refno' => $item['refno'],
                'description' => $item['service_desc'],
                'group' => $item['group_desc'],
                'source' => $item['source'],
                'service_code' => $item['service_code'],
                'encoder' => $item['encoder'] . " - " . $item['time_encoded'],
                'quantity' => $item['qty'],
                'price' => number_format($item['serv_charge'],2),
                'charge' => number_format($item['qty'] * $item['serv_charge'],2)
            );
            $total += $item['qty'] * $item['serv_charge'];
        }
        echo json_encode(array('items' => $result, 'total' => $total));
    }

    private static function getBillingInstance($encounterNr,$billDate,$billFromDate,$deathDate){
        $bill = new Billing;
        $billFromDate = date('Y-m-d H:i:s',strtotime($billFromDate));
        if($deathDate=='')
            $deathDate = $bill->getDeathDate($encounterNr);
        if ($billInfo = $bill->hasSavedBill($encounterNr))
            $bill->setBillArgs($encounterNr,$billInfo['bill_dte'],$billInfo['bill_frmdte'],$deathDate,$billInfo['bill_nr']);
        else
            $bill->setBillArgs($encounterNr,$billDate,$billFromDate,$deathDate);
        return $bill;
    }

}//end AjaxBilling