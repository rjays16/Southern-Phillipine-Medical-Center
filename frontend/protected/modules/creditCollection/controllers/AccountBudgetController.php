<?php

class AccountBudgetController extends Controller
{

    public $amount;

    public function actionIndex()
    {
        $model=new GrantAccountType;
        $allotModel = new GrantAccountsAllotment;
        $allotmentList='';
        $allotModel_2 = $allotModel::model()->getAllAllotment();
        $allotmentList =  new CArrayDataProvider(
                $allotModel_2,
                array(
                        'pagination' => array(
                            'pageSize' => 20
                        ),
                        'keyField' => false
                    )
                );

        $model_2 = GrantAccountType::model()->getAllGrantAccountType();

        $data = array();

        require_once($root_path . 'include/care_api_classes/class_acl.php');
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
        }

        $this->render('index',array(
            'model' => $model,
            'grantAccountTypes' => $data,
            'allotModel'=> $allotmentList
        ));
    }

    public function actionSave($data){
        $model=new GrantAccountType;
        $model_2 = GrantAccountType::model()->getAllGrantAccountType();
        
        $newdata = json_decode($data);

        $encoder = utf8_encode(Yii::app()->SESSION['sess_user_name']);

        $error = 0;

        if($newdata->allotment_id != ''){
            $allotModel = GrantAccountsAllotment::model()->findByPk($newdata->allotment_id);

            $getamounts = $this->actionGetGrantAccounts($newdata->type_id, $newdata->id, 1);

            $subAmount = (float) $allotModel->amount - (float) $newdata->amount;

            if((float) $subAmount > (float) $getamounts['remaining']){
                $error = 1;

                echo json_encode(array(
                                'result' => 'failed',
                                'message' => 'Remaining balance is exhausted'
                            ));

            }else{
                $allotModel->history = $allotModel->history."\nUpdated ".date("Y-m-d H:i:s")." by ".$encoder;
                $allotModel->modify_time = date("Y-m-d H:i:s");
                $allotModel->modify_id = $encoder;
            }
        }else{
            $allotModel = new GrantAccountsAllotment;

            $allotModel->grant_account_type = $newdata->type_id;
            $allotModel->grant_account = ($newdata->id ? $newdata->id : NULL);
            $allotModel->history = "Created ".date("Y-m-d H:i:s")." by ".$encoder;
            $allotModel->create_time = date("Y-m-d H:i:s");
            $allotModel->create_id = $encoder;
            
        }

        if(!$error){
            $allotModel->date = date('Y-m-d',strtotime($newdata->allotmentDate));
            $allotModel->amount = $newdata->amount;
            $allotModel->remarks = $newdata->remarks;

            if ($allotModel->save()) {
                $getamounts = $this->actionGetGrantAccounts($newdata->type_id, $newdata->id, 1);

                $results = array('result' => 'success',
                                'remaining' => $getamounts['remaining'],
                                'actual' => $getamounts['actual']
                            );

                echo json_encode($results);
            }else echo json_encode(array('result' => 'failed',
                                        'message' => 'An error occurred upon saving allotment'));
        }
    }

    public function actionDelete($id,$typeid,$subid){
        $encoder = utf8_encode(Yii::app()->SESSION['sess_user_name']);
        
        $getamounts = $this->actionGetGrantAccounts($typeid, $subid, 1);

        $allotModel = GrantAccountsAllotment::model()->findByPk($id);

        if((float)$allotModel->amount > (float)$getamounts['remaining']){
            echo json_encode(array(
                                'result' => 'failed',
                                'message' => 'Unable to delete allotment greater than the remaining balance'
                            ));
        }else{
            $allotModel->is_deleted = 1;
            $allotModel->modify_time = date("Y-m-d H:i:s");
            $allotModel->modify_id = $encoder;
            $allotModel->history = $allotModel->history."\nDeleted ".date("Y-m-d H:i:s")." by ".$encoder;

            if($allotModel->save()){

                $getamounts = $this->actionGetGrantAccounts($typeid, $subid, 1);

                $results = array('result' => 'success',
                                'remaining' => $getamounts['remaining'],
                                'actual' => $getamounts['actual']
                            );

                echo json_encode($results);

            }else{
              echo json_encode(array('result' => 'failed',
                                    'message' => 'An error occurred upon deleting allotment. Please contact your service provider.'
                                ));  
            }
        }
    }

    public function actionUpdate($id){
        $allotModel = GrantAccountsAllotment::model()->findByPk($id);
        $details = new stdClass();
        $details->allotmentDate = date("m/d/Y", strtotime($allotModel->date));
        $details->amount = $allotModel->amount;
        $details->remarks = $allotModel->remarks;
        $details->id = $id;

        echo json_encode($details);
    }

    public function actionDisplayBudget($type_id, $id){
        $model=new GrantAccountType;
        $model_2 = GrantAccountType::model()->getAllGrantAccountType();
        $allotModel = new GrantAccountsAllotment;
       
        $criteria = new CDbCriteria;
        $criteria->select = array("t.*", "FORMAT(amount, 2) AS amount");

        if($id != ''){
            $condition = "grant_account_type = ".$type_id." AND grant_account=".$id." AND is_deleted <> 1";
        }else{
            $condition = "grant_account_type = ".$type_id." AND (grant_account IS NULL OR grant_account = '') AND is_deleted <> 1";
        }
            
        $criteria->condition = $condition;

        $allotModel_2 = GrantAccountsAllotment::model()->bydate()->findAll($criteria);
        $allotmentList =  new CArrayDataProvider(
                                    $allotModel_2,
                                    array(
                                        'pagination' => array(
                                            'pageSize' => 20
                                        ),
                                        'keyField' => false
                                    )
                                );

        $grantAccountTypes = CHtml::listData($model_2, 'id', 'alt_name');

        $this->render('index',array(
            'model' => $model,
            'grantAccountTypes' => $grantAccountTypes,
            'allotModel'=> $allotmentList,
            'id' => $id,
            'balance' => $balance
        ));
        
    }

    public function actionGetGrantAccounts($account_id, $sub_account,$new=0){
        $model = GrantAccount::model()->findAllByAttributes(array(
            'account_type_id' => $account_id,
            'deleted' => 0
        ));

        $accountFunds = GrantAccountsAllotment::model()->getAccountFunds($account_id,$sub_account);
        $accountReferrals = EncounterReferrals::model()->getAccountReferrals($account_id, $sub_account);

        $remaining = (float)$accountFunds[0]['account_fund'] - (float)$accountReferrals[0]['account_referrals'];

        if($actualAccountFund < 0)
            $actualAccountFund = 0;

        $data = array('remaining' => $remaining, 'actual' => (float)$accountFunds[0]['account_fund'], 'model' => $model);

        if($new)
            return $data;
        else echo json_encode($data);
    }
}