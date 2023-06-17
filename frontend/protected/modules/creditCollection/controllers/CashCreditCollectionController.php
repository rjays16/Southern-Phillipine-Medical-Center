<?php
use \SegHis\modules\person\models\Person;
use SegHis\modules\person\models\Encounter;
use SegHis\modules\socialService\models\EncounterCharityGrant;

Yii::import('creditCollection\models\EncounterReferrals');
Yii::import('or_.models.*');

class CashCreditCollectionController extends Controller
{
    public $ER = 1;
    public $OPD = 2;
    public $ER_INPATIENT = 3;
    public $OPD_INPATIENT = 4;
    public $IPBM_IPD = 13;
    public $IPBM_OPD = 14;
    public $amount;

    public $request_status = array(
                array('id' => 'pending', 'name' => 'Pending'),
                array('id' => 'settled', 'name' => 'Settled')
    );

    public function actionIndex()
    {
        if($_GET['encounter_nr'] == '')
            $model = new Person('search');

        $referralModel = new EncounterReferrals('search');
        
        $referralList =  new CArrayDataProvider(
                $referralModel,
                array(
                        'pagination' => array(
                            'pageSize' => 20
                        ),
                        'keyField' => false
                    )
                );

        $model_2 = GrantAccountType::model()->getAllGrantAccountType();
        $grantAccountTypes = CHtml::listData($model_2, 'id', 'alt_name');    

        $costCenters = EncounterReferrals::model()->getCostCenterList();
        $costCentersList = CHtml::listData($costCenters, 'id', 'name');
        $requestStatusList = CHtml::listData($this->request_status, 'id', 'name');
        $modelGrants = new CreditcollectionCashGrants;

        $this->render('index', array(
            'model'             => $model,
            'referrals'         => $referralModel,
            'grantAccountTypes' => $grantAccountTypes,
            'costCentersList'   => $costCentersList,
            'requestStatusList' => $requestStatusList,
            'modelGrants'       => $modelGrants
        ));
    }

    public function actionSearch($hrn, $encounter_nr){
        $person = Person::model()->findByPk($hrn);
        $enc = Encounter::model()->findByPk($encounter_nr);
        $discountdesc = '';

        $pid = ($hrn ? $hrn : $enc->pid);

        $discountdesc = EncounterCharityGrant::model()->getPatientClassification($pid,$encounter_nr);

        switch((int)$enc->encounter_type){
            case $this->ER: $encounter_type = "ER"; break;
            case $this->OPD: $encounter_type = "OPD"; break;
            case $this->ER_INPATIENT: $encounter_type = "ER INPATIENT"; break;
            case $this->OPD_INPATIENT: $encounter_type = "OPD INPATIENT"; break;
            case $this->IPBM_IPD: $encounter_type = "IPBM-IPD"; break;
            case $this->IPBM_OPD: $encounter_type = "IPBM-OPD"; break;
            default: $encounter_type = "Walk-in";
        }

        $middle_name = ($person->name_middle ? substr($person->name_middle,0,1)."." : '' );
        $fullName = $person->name_last.", ".$person->name_first." ".$middle_name;

        $balance = $this->getEncounterBalance($encounter_nr);

        echo CJSON::encode(array(
            'pid' => $pid,
            'encounter_nr' => $encounter_nr,
            'encounter_type' => $encounter_type,
            'classification' => $discountdesc,
            'fullName' => $fullName,
            'actualBal' => $balance['actualBal'],
            'remainBal' => $balance['remainBal']
        ));
    }

    public function actionGetGrantAccount($type_id, $id, $update=0){
        $model = GrantAccount::model()->findAllByAttributes(array(
            'account_type_id' => $type_id,
            'deleted' => 0
        ));

        $actualAccountFund = 0;

        $modelAccount = GrantAccountType::model()->findByAttributes(array(
            'id' => $type_id,
            'deleted' => 0
        ));

        if($modelAccount->with_budget){
            $accountFunds = GrantAccountsAllotment::model()->getAccountFunds($type_id,$id);
            $accountReferrals = EncounterReferrals::model()->getAccountReferrals($type_id, $id);

            $actualAccountFund = (float)$accountFunds[0]['account_fund'] - (float)$accountReferrals[0]['account_referrals'];

            if($actualAccountFund < 0)
                $actualAccountFund = 0;
            
        }else $actualAccountFund = -1; // -1 means budget allocation is not needed

        $results = array('model' => $model, 'actualAccountFund' => $actualAccountFund);

        if($update)
            return $results;
        else
            echo json_encode($results);
    }

    public function actionSave($data){
        $encoder = utf8_encode(Yii::app()->SESSION['sess_user_name']);

        $model_2 = GrantAccountType::model()->getAllGrantAccountType();
        
        $newdata = json_decode($data);

        $error = 0;
        if($newdata->referral_id != ''){
            $encReferrals = EncounterReferrals::model()->findByPk($newdata->referral_id);

            $getAccountData = $this->actionGetAccountSubCategory($encReferrals->account,$encReferrals->sub_account,1,array('encounter_nr' => $encReferrals->encounter_nr));

            $subAmount = (float) $encReferrals->amount - (float)$newdata->amount;

            if((float) $subAmount > (float)$getAccountData['totalFund']){
                $error = 1;

                echo json_encode(array(
                                'result' => 'failed',
                                'message' => "Unable to update this referral. It has been granted to patient's request/s"
                            ));

            }else{
                $encReferrals->history = $encReferrals->history."\nUpdated ".date("Y-m-d H:i:s")." by ".$encoder;
                $encReferrals->modify_time = date("Y-m-d H:i:s");
                $encReferrals->modify_id = $encoder;
            }
        }else{
            $encReferrals = new EncounterReferrals;

            $encReferrals->encounter_nr = $newdata->encounter_nr;
            $encReferrals->balance = ($newdata->balance < 0 ? NULL : $newdata->balance);
            $encReferrals->history = "Created ".date("Y-m-d H:i:s")." by ".$encoder;
            $encReferrals->create_time = date("Y-m-d H:i:s");
            $encReferrals->create_id = $encoder;
        }

        if(!$error){
            $encReferrals->entry_date = $newdata->entry_date;
            $encReferrals->account = $newdata->type_id;
            $encReferrals->sub_account = ($newdata->id ? $newdata->id : NULL);
            $encReferrals->control_no = $newdata->control_no;
            $encReferrals->amount = $newdata->amount;
            $encReferrals->remarks = $newdata->remarks;
            if ($encReferrals->save()) {
                $balance = $this->getEncounterBalance($encReferrals->encounter_nr);

                echo CJSON::encode(array(
                    'result' => 'success',
                    'actualBal' => $balance['actualBal'],
                    'remainBal' => $balance['remainBal']
                ));
            }else{
                // var_dump($encReferrals->getErrors());die;
                echo CJSON::encode(array(
                    'result' => 'failed',
                    'message' => 'An error occurred upon saving referral'
                ));
            }
        }
    }

    public function actionDelete($id){
        $encoder = utf8_encode(Yii::app()->SESSION['sess_user_name']);

        $referral = EncounterReferrals::model()->findByPk($id);

        $haspermission = $this->getReferralListPermission(array('accountid' => $referral->account));

        if($haspermission){
            $getAccountData = $this->actionGetAccountSubCategory($referral->account,$referral->sub_account,1,array('encounter_nr' => $referral->encounter_nr));

            if((float)$referral->amount > (float)$getAccountData['totalFund']){
                echo json_encode(array(
                                    'result' => 'failed',
                                    'errormsg' => "Unable to delete this referral. It has been granted to patient's request/s"
                                ));
            }else{
                $referral->is_deleted = 1;
                $referral->modify_time = date("Y-m-d H:i:s");
                $referral->modify_id = $encoder;
                $referral->history = $referral->history."\nDeleted ".date("Y-m-d H:i:s")." by ".$encoder;


                if($referral->save()){
                    $balance = $this->getEncounterBalance($referral->encounter_nr);

                    echo json_encode(array('result' => 'success', 'actualBal' => $balance['actualBal'], 'remainBal' => $balance['remainBal']));
                }else echo json_encode(array('result' => 'failed'));
            }
        }else{
            echo json_encode(array(
                'result' => 'failed',
                'errormsg' => 'Sorry you have no permission to delete this referral'
            ));
        }
    }

    public function actionUpdate($id){
        $referral = EncounterReferrals::model()->findByPk($id);
        $details = array();

        $haspermission = $this->getReferralListPermission(array('accountid' => $referral->account));

        if($haspermission){
            $getAccountData = $this->actionGetGrantAccount($referral->account,$referral->sub_account,1);

            $details['encounter_nr'] = $referral->encounter_nr;
            $details['entry_date'] = $referral->entry_date;
            $details['account'] = $referral->account;
            $details['sub_account'] = $referral->sub_account;
            $details['control_no'] = $referral->control_no;
            $details['amount'] = $referral->amount;
            $details['balance'] = $referral->balance;
            $details['remarks'] = $referral->remarks;
            $details['id'] = $id;

            $getAccountData['details'] = $details;

            $model_2 = GrantAccountType::model()->getAllGrantAccountType();
            //$grantAccountTypes = CHtml::listData($model_2, 'id', 'alt_name');
            $model = new EncounterReferrals('search');

            $grantAccountTypes = $this->getReferralListPermission(array('referrals' => $model_2));

            $data = $this->renderPartial('modals/_referralModal',array(
                'grantAccountTypes'=>$grantAccountTypes,
                'model' => $model,
                'encounter_nr' => $referral->encounter_nr
            ), true, true);

            $getAccountData['modal'] = $data;

            echo json_encode($getAccountData);
        }else{
            echo CJSON::encode(array(
                'result' => 'failed',
                'errormsg' => 'Sorry you have no permission to update this referral'
            ));
        }
    }

    /* Modal Triggers */
    public function actionOpenReferralModal($encounter_nr){
        $model_2 = GrantAccountType::model()->getAllGrantAccountType();
        //$grantAccountTypes = CHtml::listData($model_2, 'id', 'alt_name');
        $model = new EncounterReferrals('search');

        $data = $this->getReferralListPermission(array('referrals' => $model_2));

        /*require_once($root_path . 'include/care_api_classes/class_acl.php');
        $objAcl = new \Acl(Yii::app()->SESSION['sess_temp_userid']);

        $CCAccountsparentOnly = $objAcl->checkPermissionRaw('_a_1_ccaccount');
        $all = $objAcl->checkPermissionRaw('_a_0_all');

        // Check if permission is only "Accounts" (parent) 
        foreach($model_2 as $accounts){
            $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$accounts->id.'_'.$accounts->type_name);

            if ($haspermission) 
                $CCAccountsparentOnly = false;
        }
        // end checker

        foreach($model_2 as $accounts){
            $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$accounts->id.'_'.$accounts->type_name);
            
            if(($CCAccountsparentOnly || $all) || ($haspermission && !$CCAccountsparentOnly && !$all)){
                $data[$accounts->id] = $accounts->alt_name;
            }
        }*/

        $this->renderPartial('modals/_referralModal',array(
            'grantAccountTypes'=> $data,
            'model' => $model,
            'encounter_nr' => $encounter_nr
        ), false, true);
    }

    public function actionOpenGrantRequestModal($isOpened=0){
        $refno = $_GET['refno'];
        $costcenter = $_GET['costcenter'];
        $itemcode = $_GET['itemcode'];
        $totalAmount = 0;
        $subaccountcat = array();
        $countSub = 0;

        /* Fetch accounts based on encounter referrals */
        $encounterReferrals = EncounterReferrals::model()->getEncounterReferrals($_GET['encounter_nr']);

        /* Fetch accounts based on permission */
        $listReferrals = $this->getReferralListPermission(
                                array('referrals'   => $encounterReferrals,
                                      'from_grant'  => 1
                                )
                            );

        if(!$isOpened){
            /* Fetch data needed upon opening grant request modal */
            $requestDetails = CreditcollectionCashGrants::model()->getRequestDetails($refno,$costcenter, $itemcode);

            foreach($requestDetails as $requestDetail){
                $detail['refno'] = $requestDetail->refno;
                $detail['itemcharge'] = $requestDetail->itemcharge;
                $detail['item_name'] = $requestDetail->item_name;

                if(isset($requestDetail->quantity))
                    $detail['quantity'] = $requestDetail->quantity;
                else $detail['quantity'] = 1;
            }

            $grants = CreditcollectionCashGrants::model()->findAllByAttributes(array(
                        'refno' => $refno,
                        'itemcode' => $itemcode,
                        'req_source' => $costcenter,
                        'is_deleted' => 0
                    ));

            $price = $detail['itemcharge'];
            if(strpos($price, ',') !== false){
                $price = str_replace( ',', '', $price );
            }

            if($_GET['costcenter'] != 'MISC')
                $totalBal = $detail['quantity']*$price;
            else $totalBal = $price;
           
            if($grants){
                foreach($grants as $grant){
                    $totalAmount = (float)$totalAmount + (float)$grant['amount'];

                    $subcategory = $this->actionGetAccountSubCategory($grant['account'], '', 1);

                    if(count($subcategory['subcategoryref'])){
                        foreach($subcategory['subcategoryref'] as $sub){
                            if(!$this->isRefExists($sub['subAccountid'],$subaccountcat)){
                                $subaccountcat[$countSub]['id'] = $sub['subAccountid'];
                                $subaccountcat[$countSub]['subaccountname'] = $sub['sub_account'];
                                $subaccountcat[$countSub]['account'] = $grant['account'];
                                $subaccountcat[$countSub]['amount'] = (float)$grant['amount'] - (float)$totalAmount;
                                $countSub++;
                            }
                        }  
                    }
                }
            }

            $totalDue = (float)$totalBal;
            $totalBal = (float)$totalBal - (float)$totalAmount;

            $params = array(
                'details'   => $detail,
                'referralAccount' => $listReferrals,
                'costcenter' => $_GET['costcenter'],
                'itemcode' => $_GET['itemcode'],
                'balance' => $totalBal,
                'totaldue' => $totalDue,
                'grants' => $grants,
                'subaccountcat' => $subaccountcat
            );

            /*if(isset($_GET['view'])){
                $params['totaldue'] = $totalDue;
                $params['grants'] = $grants;
                $params['subaccountcat'] = $subaccountcat;
            }*/

            $data = $this->renderPartial('modals/_requestsModal',$params, true, true);

            $getAccountData['modal'] = $data;

            echo json_encode($getAccountData);
        }else{
            /* For adding new row in grant details table */
            echo json_encode($listReferrals);
        }
    }

    /* Get account sub category by encounter */
    public function actionGetAccountSubCategory($type_id='', $id='', $update=0, $data=array()){

        $criteria = new CDbCriteria;
        $criteria->select = "t.*";

        if($id != ''){
            $condition = " AND account = ".$type_id." AND sub_account=".$id." AND is_deleted <> 1";
        }else{
            $condition = " AND account = ".$type_id." AND (sub_account IS NULL OR sub_account = '') AND is_deleted <> 1";
        }
            
        if($_GET['encounter_nr'])
            $encounter_nr = $_GET['encounter_nr'];
        elseif($data['encounter_nr'] != ''){
            $encounter_nr = $data['encounter_nr'];
        }else $encounter_nr = '';

        $criteria->condition = "encounter_nr='".$encounter_nr."'".$condition;

        $getreferralFund = EncounterReferrals::model()->byentrydate()->findAll($criteria);
        
        $totalFund = 0;
        $totalGrant = 0;
        foreach($getreferralFund as $fund){
            $totalFund += (float)$fund->amount;
        }
        
        $grants = CreditcollectionCashGrants::model()->findAllByAttributes(array(
                        'account' => $type_id,
                        'sub_account' => ($id !='' ? $id : NULL),
                        'is_deleted' => 0,
                        'encounter_nr' => $encounter_nr
                    ));
        
        if($grants){
            foreach($grants as $grant){
                $totalGrant = (float)$totalGrant + (float)$grant['amount'];
            }
        }

        $totalFund = (float)$totalFund - (float)$totalGrant;

        if($totalFund < 0)
            $totalFund = 0;

        $model = EncounterReferrals::model()->getEncounterReferrals($encounter_nr, $type_id);

        $results = array('subcategoryref' => $model, 'totalFund' => $totalFund);

        if($update)
            return $results;
        else
            echo json_encode($results);
    }

    function actionSaveGrantDetails($deleteRows=array(),$details,$isfull,$update=0){
        $encoder = utf8_encode(Yii::app()->SESSION['sess_user_name']);
        $data = json_decode($details);
        $deleteRows = json_decode($deleteRows);

        $error = 0;
        $encounter_nr = $_GET['encounter_nr'];
        $refno = $_GET['refno'];
        $costcenter = $_GET['costcenter'];
        $itemcode = $_GET['itemcode'];
        $hashistory = 1;

        if(count($deleteRows)){
            foreach($deleteRows as $deleted){
                $cashGrants = CreditcollectionCashGrants::model()->findByPk($deleted);
                $cashGrants->is_deleted = 1;
                $cashGrants->history = $cashGrants->history."\nRemoved ".date("Y-m-d H:i:s")." by ".$encoder;
                $cashGrants->modify_time = date("Y-m-d H:i:s");
                $cashGrants->modify_id = $encoder;

                if(!$cashGrants->save()){
                    $error = 1;
                }
            }
        }

        foreach($data as $value){
            if($value->row_id != '' && $value->row_id != 'new'){
                $cashGrants = CreditcollectionCashGrants::model()->findByPk($value->row_id);

                $cashGrants->history = $cashGrants->history."\nUpdated ".date("Y-m-d H:i:s")." by ".$encoder;
                $cashGrants->modify_time = date("Y-m-d H:i:s");
                $cashGrants->modify_id = $encoder;

            }else{
                $cashGrants = new CreditcollectionCashGrants;

                $cashGrants->encounter_nr = $encounter_nr;
                $cashGrants->refno = $refno;
                $cashGrants->req_source = $costcenter;
                $cashGrants->itemcode = $itemcode;
                $cashGrants->history = "Created ".date("Y-m-d H:i:s")." by ".$encoder;
                $cashGrants->create_time = date("Y-m-d H:i:s");
                $cashGrants->create_id = $encoder;
            }

            $cashGrants->account = $value->account;
            $cashGrants->amount = $value->gdamount;
            $cashGrants->control_no = $value->gdcontrolno;
            $cashGrants->date = $value->gddategrant;
            $cashGrants->balance = $value->gdrefbal;
            $cashGrants->is_full = $isfull;

            if($value->subaccount != '')
                $cashGrants->sub_account = $value->subaccount;

            if(!$cashGrants->save()){
                $error = 1;
            }
        }

        if($isfull || $update){
            if($costcenter == 'LAB'){
                $modelsCC = LabServdetails::model()->findByAttributes(array(
                    'refno' => $refno,
                    'service_code' => $itemcode
                ));
            }elseif($costcenter == 'MISC'){
                $modelsCC = MiscServiceDetails::model()->findByAttributes(array(
                    'refno' => $refno,
                    'service_code' => $itemcode
                ));
            }elseif($costcenter == 'RAD'){
                $modelsCC = CareTestRequestRadio::model()->findByAttributes(array(
                    'refno' => $refno,
                    'service_code' => $itemcode
                ));
            }elseif($costcenter == 'PHARMA'){
                $modelsCC = PharmaOrderItems::model()->findByAttributes(array(
                    'refno' => $refno,
                    'bestellnum' => $itemcode
                ));

                $hashistory = 0;
            }

            $modelsCC->request_flag = 'crcu';
            if($update && !$isfull)
                $modelsCC->request_flag = NULL;

            if($hashistory){
                $modelsCC->history = $modelsCC->history."\nRequest paid tru Cash credit collection ".date("Y-m-d H:i:s")." by ".$encoder;
                $modelsCC->modify_dt = date("Y-m-d H:i:s");
                $modelsCC->modify_id = $encoder;
            }

            if(!$modelsCC->save()){
                $error = 1;
            }
        }

        if(!$error){
            $balance = $this->getEncounterBalance($encounter_nr);

            echo CJSON::encode(array(
                'result' => 'success',
                'actualBal' => $balance['actualBal'],
                'remainBal' => $balance['remainBal']
            ));
        }else{
            echo CJSON::encode(array(
                'result' => 'failed'
            ));
        }

    }

    /*  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/

    public function isRefExists($value='', $arrayVar=array()){
        foreach ($arrayVar as $item) {
            if ($item['id']===$value) {
                return true;
            }
        }
        return false;
    }

    public function getEncounterBalance($encounter_nr){
        $actualBal = 0;
        $remainBal = 0;
        $getactualBal = EncounterReferrals::model()->getEncounterReferrals($encounter_nr);

        foreach ($getactualBal as $key => $value) {
            $actualBal += $getactualBal[$key]->amountformat;
        }

        $getEncGrants = CreditcollectionCashGrants::model()->findAllByAttributes(array(
                    'encounter_nr' => $encounter_nr,
                    'is_deleted' => 0
                ));

        foreach ($getEncGrants as $key => $value) {
            $remainBal += $getEncGrants[$key]->amount;
        }

        $remainBal = (float)$actualBal - (float)$remainBal;
        $data = array('actualBal' => $actualBal, 'remainBal' => $remainBal);

        return $data;
    }

    /* Get referral list based on access permission */
    private function getReferralListPermission($params = array()){
        $listReferrals = array();

        require_once($root_path . 'include/care_api_classes/class_acl.php');
        $objAcl = new \Acl(Yii::app()->SESSION['sess_temp_userid']);

        $CCAccountsparentOnly = $objAcl->checkPermissionRaw('_a_1_ccaccount');
        $all = $objAcl->checkPermissionRaw('_a_0_all');

        if($params['accountid']){
            $accdetails = GrantAccountType::model()->findByPk($params['accountid']);

            $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$accdetails->id.'_'.$accdetails->type_name);

            return $haspermission;
        }else{
            // Check if permission is only "Accounts" (parent) 
            foreach($params['referrals'] as $accounts){

                if($params['from_grant'])
                    $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$accounts->account0->id.'_'.$accounts->account0->type_name);
                else $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$accounts->id.'_'.$accounts->type_name);
                
                if ($haspermission) 
                    $CCAccountsparentOnly = false;
            }
            // end checker
            foreach($params['referrals'] as $accounts){

                if($params['from_grant']){
                    $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$accounts->account0->id.'_'.$accounts->account0->type_name);

                    if(($CCAccountsparentOnly || $all) || ($haspermission && !$CCAccountsparentOnly && !$all)){
                        $has_permission = 1;
                    }else $has_permission = 0;

                    $listReferrals[$accounts->account] = array('alt_name'       => $accounts->alt_name,
                                                               'has_permission' => $has_permission
                                                           );
                }else{
                    $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$accounts->id.'_'.$accounts->type_name);

                    if(($CCAccountsparentOnly || $all) || ($haspermission && !$CCAccountsparentOnly && !$all)){
                        $listReferrals[$accounts->id] = $accounts->alt_name;
                    }
                }
            }
            /* end fetching */

            return $listReferrals;

        }
    }

    /*function actionVoidGrantDetails(){
        $encoder = utf8_encode(Yii::app()->SESSION['sess_user_name']);
        
        $refno = $_GET['refno'];
        $costcenter = $_GET['costcenter'];
        $itemcode = $_GET['itemcode'];
        $hashistory = 1;
        $error = 0;

        $grants = CreditcollectionCashGrants::model()->findAllByAttributes(array(
                        'refno' => $refno,
                        'req_source' => $costcenter,
                        'itemcode' => $itemcode,
                        'is_deleted' => 0
                    ));
        
        $transaction = \Yii::app()->getDb()->beginTransaction();
        
        
        foreach($grants as $grant){
            $grantbyid = CreditcollectionCashGrants::model()->findByPk($grant->id);

            $grantbyid->is_deleted = 1;
            $grantbyid->history = $grantbyid->history."\nVoided request's grant details ".date("Y-m-d H:i:s")." by ".$encoder;
            $grantbyid->modify_time = date("Y-m-d H:i:s");
            $grantbyid->modify_id = $encoder;

            if(!$grantbyid->save()){
                $error = 1;
            }

            $encounter_nr = $grantbyid->encounter_nr;
        }

        if($costcenter == 'LAB'){
            $modelsCC = LabServdetails::model()->findByAttributes(array(
                'refno' => $refno,
                'service_code' => $itemcode
            ));
        }elseif($costcenter == 'MISC'){
            $modelsCC = MiscServiceDetails::model()->findByAttributes(array(
                'refno' => $refno,
                'service_code' => $itemcode
            ));
        }elseif($costcenter == 'RAD'){
            $modelsCC = CareTestRequestRadio::model()->findByAttributes(array(
                'refno' => $refno,
                'service_code' => $itemcode
            ));
        }elseif($costcenter == 'PHARMA'){
            $modelsCC = PharmaOrderItems::model()->findByAttributes(array(
                'refno' => $refno,
                'bestellnum' => $itemcode
            ));

            $hashistory = 0;
        }

        $modelsCC->request_flag = NULL;

        if($hashistory){
            $modelsCC->history = $modelsCC->history."\nVoided request's grant detail ".date("Y-m-d H:i:s")." by ".$encoder;
            $modelsCC->modify_dt = date("Y-m-d H:i:s");
            $modelsCC->modify_id = $encoder;
        }

        if(!$modelsCC->save()){
            $error = 1;
        }

        if(!$error){
            $transaction->commit();

            $balance = $this->getEncounterBalance($encounter_nr);

            echo CJSON::encode(array(
                'result' => 'success',
                'actualBal' => $balance['actualBal'],
                'remainBal' => $balance['remainBal'],
            ));
        } 
        else{
            $transaction->rollback();

            echo CJSON::encode(array(
                'result' => 'error'
            ));
        }
    }*/
}