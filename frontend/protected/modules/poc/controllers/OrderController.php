<?php
use SegHis\models\encounter\Encounter;
use SegHis\modules\poc\models\TypeCharge;
use SegHis\modules\socialService\models\EncounterCharityGrant;
use SegHEIRS\modules\integrations\events\CbgOrderedEvent;
use SegHis\modules\poc\models\PocOrder;
use SegHis\modules\poc\models\PocOrderDetail;
use SegHis\modules\admission\models\assignment\Ward;
use \EncounterType;

Yii::import('application.modules.packageManager.models.LabServices');

/**
 * Description of OrderController
 *
 * @author Bong
 */
class OrderController extends \Controller {
    //put your code here

    public function filters()
    {
        return array(
            array('bootstrap.filters.BootstrapFilter')
        );
    }        

    /**
     *
     */
    public function actionOrderCare($encounter_nr)
    {
        // Check first if there is an outstanding POC not yet closed ...
        $order = new PocOrder();
        $pocOrder = $order->getLatestPocOrder($encounter_nr);               
        if ( is_null($pocOrder) || (!is_null($pocOrder) && ($pocOrder->order_type == PocOrder::STOP)) ) {
            // if none or most recent is STOP order ... proceed to new POC order ...               
            $encounter = Encounter::model()->findByPk($encounter_nr);                  
            $pocService = LabServices::model()->findByPk(LabServices::CBG);
            $discount = EncounterCharityGrant::getRecentCharityGrant($encounter_nr);

            $ward = Ward::model()->findByPk($encounter->current_ward_nr);            
            if ($ward) {
                $ward_id = $ward->ward_id;
            }
            else {
                switch ($encounter->type->type_nr) {
                    case EncounterType::TYPE_OUTPATIENT:
                        $ward_id = Ward::OPD_WARD;
                        break;
                    
                    case EncounterType::TYPE_EMERGENCY:
                        $ward_id = Ward::ER_WARD;
                        break;
                    
                    default:
                        throw new CHttpException(480, "Point of Care is not available yet for patient's encounter type!");
                }                                
            }
            
            $currentOrder = PocOrder::START;
            
            $quantity = "";

            $ispercent = true;
            if ($discount) {
                if (empty($discount[0]->discount_amnt)) {
                    $ndiscount = $discount[0]->discount;                    
                }
                else {
                    $ndiscount = $discount[0]->discount_amnt;
                    $ispercent = false;
                }                
            }
            $discountid = ($discount) ? $discount[0]->discountid : null;
            $ndiscount = is_null($ndiscount) ? 0.0 : $ndiscount;                        
            
            $pocOrder = null;
        }
        else {
            // if there is ... retrieve latest POC order.
            $encounter = $pocOrder->encounterNr;                  
            $pocService = $pocOrder->pocOrderDetails[0]->serviceCode;
            $quantity = $pocOrder->pocOrderDetails[0]->quantity;
            
            $discountid =$pocOrder->discountid;
            $ndiscount = is_null($pocOrder->discount) ? 0.0 : $pocOrder->discount;
            $ispercent = false;
            
            $ward_id = $pocOrder->ward_id;
            $currentOrder = PocOrder::STOP;            
        }

        // renders a view
        $html = $this->renderPartial('/default/poc_modal', array('encounter'     => $encounter,
                                                                 'poc_service'   => $pocService,
                                                                 'quantity'      => $quantity,
                                                                 'discountid'    => $discountid,
                                                                 'discount'      => $ndiscount,
                                                                 'ward_id'       => $ward_id,
                                                                 'current_order' => $currentOrder,
                                                                 'pocOrder'      => $pocOrder,
                                                                 'is_percent'    => $ispercent), true, true);                        
        echo CJSON::encode($html);
    }    
    
    /**
     * 
     */
    public function actionGetChargeTypes() 
    {
        $chargeTypes = TypeCharge::getTypeCharges();                        
        $list = CHtml::listData($chargeTypes, 'id', 'charge_name');        
        $html = $this->renderPartial('/default/_chargetypes', array('chargelist' => $list), true);
        echo CJSON::encode($html);
    }
    
    /***
     * 
     */
    public function actionGetPocOrders($encounter_nr)
    {
        $orders = Yii::app()->db->createCommand()
          ->select('CONCAT(ph.refno, \' [\', IF(ph.is_cash, \'CASH\', \'CHARGE\'),\']\') refno, CONCAT(ph.order_dt, \'/\', \'<br>\', (SELECT name FROM care_users WHERE login_id = ph.create_id)) order_dt, ph.encounter_nr, ph.pid, ph.is_cash, ph.order_type, 
              (SELECT ack_response FROM seg_hl7_message_log WHERE ref_no = ph.refno AND message_type LIKE \'ADT^A0%\' LIMIT 1) status, 
              CONCAT((SELECT `name` FROM seg_lab_services WHERE service_code = pd.service_code), \' [\',ph.order_type, \']\')  service_name,
              IF(ph.is_cash, pd.quantity, \'N/A\') quantity, pd.unit_price, format(pd.quantity * (pd.unit_price - IFNULL(ph.discount, 0)), 2) total')
          ->from('seg_poc_order ph')
          ->join('seg_poc_order_detail pd', 'pd.refno = ph.refno')
          ->where('ph.encounter_nr = :enc_nr', array(':enc_nr' => $encounter_nr))
          ->order('order_dt DESC')
          ->queryAll();                 
        echo CJSON::encode($orders);
    }
    
    /***
     * 
     */
    public function actionGetPocDiscountInfo($refno)
    {        
        $pocOrder = PocOrder::model()->findByPk($refno);
        if (!empty($pocOrder)) {            
            // if there is ... retrieve latest POC order.
            $quantity = $pocOrder->pocOrderDetails[0]->quantity;
            $uprice = $pocOrder->pocOrderDetails[0]->unit_price;
                                    
            $discount = EncounterCharityGrant::getRecentCharityGrant($pocOrder->encounter_nr);                                    
            $ispercent = false;
            if ($discount) {
                if (empty($discount[0]->discount_amnt)) {
                    $ndiscount = $discount[0]->discount; 
                    $ispercent = true;
                }
                else {
                    $ndiscount = $discount[0]->discount_amnt;
                }                
            }
            else {
                $ndiscount = is_null($pocOrder->discount) ? 0.0 : $pocOrder->discount;
            }            
            $ndiscount = is_null($ndiscount) ? 0.0 : $ndiscount;
            if ($ispercent) {
                $ndiscount = ($quantity * $uprice) * $ndiscount;
            }
            
            $pocService = $pocOrder->pocOrderDetails[0]->serviceCode;                                    
            $pocDiscount = array(
                "service" => $pocService->name,
                "quantity" => $quantity,
                "uprice" => $uprice,
                "total" => $quantity * $uprice,
                "discount" => $ndiscount,
                "net" => ($quantity * $uprice) - $ndiscount
            );                    
        }
        else {
            $pocDiscount = array(
                "service" => "",
                "quantity" => 0,
                "uprice" => 0,
                "total" => 0,
                "discount" => 0,
                "net" => 0
            );                                    
        }
        
        echo CJSON::encode($pocDiscount);
    }
    
    /***
     * 
     */
    public function actionSetPocOrderDiscount()
    {
        $data = json_decode($_POST["discountData"]);        
        $pocOrder = PocOrder::model()->findByPk($data->refno);                
        if (!empty($pocOrder)) {
            $transaction = Yii::app()->getDb()->beginTransaction();            
            try {
                $pocOrder->discountid = $data->discountid;

                $discount = EncounterCharityGrant::getRecentCharityGrant($pocOrder->encounter_nr);
                $ispercent = false;
                if ($discount) {
                    if (empty($discount[0]->discount_amnt)) {
                        $ndiscount = $discount[0]->discount; 
                        $ispercent = true;
                    }
                    else {
                        $ndiscount = $discount[0]->discount_amnt;
                    }                
                }
                else {
                    $ndiscount = is_null($pocOrder->discount) ? 0.0 : $pocOrder->discount;
                }            
                $ndiscount = is_null($ndiscount) ? 0.0 : $ndiscount;
                if ($ispercent) {
                    $quantity = $pocOrder->pocOrderDetails[0]->quantity;
                    $uprice = $pocOrder->pocOrderDetails[0]->unit_price;                    
                    
                    $ndiscount = ($quantity * $uprice) * $ndiscount;
                }

                $pocOrder->discount = $ndiscount;

                $pocOrder->modify_id = $_SESSION['sess_temp_userid'];
                $pocOrder->modify_dt = new CDbExpression('NOW()');

                $bsuccess = $pocOrder->save();
                
                if ($bsuccess) {                                
                    $transaction->commit();        
                    echo CJSON::encode(true);        
                }
                else {
                    $errors = $pocOrder->getErrors();
                    $errmsg = implode("|",$errors);                    
                    
                    $transaction->rollback();
                    throw new Exception($errmsg);   
                }                                                
            } catch (Exception $e) {

                if ($transaction->active) {
                    $transaction->rollback();
                }

                throw new CHttpException(500, 'Unable to save the POC Order Discount:  ' . $e->getMessage());
            }            
        }
        else {
            throw new CHttpException(500, 'POC Order '.$data->refno.' to be discounted does not exist!');            
        }
    }
    
    /***
     * 
     */
    public function actionSavePocOrder() 
    {        
        $orderH = json_decode($_POST["pocH"]);
        $orderD = json_decode($_POST["pocD"]);           
            
        $transaction = Yii::app()->getDb()->beginTransaction();           
        try {
            $pocOrder = new PocOrder();
            
            if ($orderH->order_type == PocOrder::STOP) {                                        
                $prevOrder = $pocOrder->getLatestPocOrder($orderH->encounter_nr);
            }
            
            $refno = $pocOrder->getNextPocOrderNo();                                    
            $pocOrder->refno = $refno;
            $pocOrder->order_dt = new CDbExpression('NOW()');
            $pocOrder->encounter_nr = $orderH->encounter_nr;
            $pocOrder->pid = $orderH->pid;
            
            $is_charge = !$orderH->is_cash;
            $pocOrder->is_cash = $orderH->is_cash ? 1 : 0;
            
            if (!empty($orderH->settlement_type)) {
                $pocOrder->settlement_type = $orderH->settlement_type;
            }
            $pocOrder->order_type = $orderH->order_type;        
            $pocOrder->ward_id = $orderH->ward_id;
            $pocOrder->source_req = $orderH->source_req;
            if (!empty($orderH->discountid)) {
                $pocOrder->discountid = $orderH->discountid;
            }
            if (!empty($orderH->discount)) {
                $pocOrder->discount = $orderH->discount;
            }
            $pocOrder->modify_id = $_SESSION['sess_temp_userid'];
            $pocOrder->create_id = $_SESSION['sess_temp_userid'];
            $pocOrder->modify_dt = new CDbExpression('NOW()');
            $pocOrder->create_dt = new CDbExpression('NOW()');                                               
            
            $bsuccess = $pocOrder->save();
            if ($bsuccess) {
                $pocDetail = new PocOrderDetail();        
                $pocDetail->id = new CDbExpression('UUID()');
                $pocDetail->refno = $refno;
                $pocDetail->service_code = $orderD->service_code;
                $pocDetail->unit_price = $orderD->unit_price;
                $pocDetail->quantity = $orderD->quantity;                                    
                $bsuccess = $pocDetail->save();
                if (!$bsuccess) {
                    $errors = $pocDetail->getErrors();
                    $errmsg = implode("|",$errors);                    
                }
            }
            else {
                $errors = $pocOrder->getErrors();
                $errmsg = implode("|",$errors);
            }
            
            if ($bsuccess) {
                if ($pocOrder->order_type == PocOrder::STOP) {                                        
                    // Void previous cash START order ...                         
                    if (!empty($prevOrder)) {
                        $bsuccess = $pocOrder->cancelCashStartOrder($prevOrder->refno);
                    }
                }
            }
            
            if ($bsuccess) {                                
                $transaction->commit();

                // If charge, send ADT message to POC system ...
                if ( $is_charge || ($pocOrder->order_type == PocOrder::STOP) ) {
                    $emitter = Yii::app()->emitter;
                    $emitter->emit(new CbgOrderedEvent($pocOrder, $pocDetail));
                }

                echo CJSON::encode(true);
            }
            else {
                $transaction->rollback();
                throw new Exception($errmsg);
            }
            
        } catch (Exception $e) {
            
            if ($transaction->active) {
                $transaction->rollback();
            }
            
            throw new CHttpException(500, 'Unable to save the POC Order:  ' . $e->getMessage());
        }                                  
    }    
    
    /***
     * 
     */
    public function actionTriggerCbgOrder()
    {
        $order = json_decode($_POST["test"]);        
        try {
            if (!empty($order)) {
                $pocOrder = PocOrder::model()->findByPk($order->refno);
                $pocDetail = PocOrderDetail::model()->findAllByAttributes(array('refno'=>$order->refno, 'service_code'=>$order->service_code));

                if (!empty($pocDetail)) {
                    $emitter = Yii::app()->emitter;
                    $emitter->emit(new CbgOrderedEvent($pocOrder, $pocDetail[0]));
                    
                    echo CJSON::encode(true);                    
                }
                else {
                    throw new Exception('No order sent!');
                }
            }
            else {
                throw new Exception('No order sent!');
            }
        } catch (Exception $e) {            
            
            throw new CHttpException(500, 'Unable to send order to POC device:  ' . $e->getMessage());
            
        }        
    }
    
    /***
     * 
     */
    public function actionTriggerCbgCancel() {                        
        $order = json_decode($_POST["test"]);        
        try {
            if (!empty($order)) {
                $pocOrder = PocOrder::model()->findByPk($order->refno);
                $pocDetail = PocOrderDetail::model()->findAllByAttributes(array('refno'=>$order->refno, 'service_code'=>$order->service_code));

                if (!empty($pocDetail)) {
                    $pocOrder->order_type = PocOrder::STOP;                  
                    
                    $emitter = Yii::app()->emitter;
                    $emitter->emit(new CbgOrderedEvent($pocOrder, $pocDetail[0]));
                    
                    echo CJSON::encode(true);                    
                }
                else {
                    throw new Exception('No stop POC Order sent!');
                }
            }
            else {
                throw new Exception('No stop POC order sent!');
            }
        } catch (Exception $e) {            
            
            throw new CHttpException(500, 'Unable to send stop order to POC device:  ' . $e->getMessage());
            
        }          
    }    
}