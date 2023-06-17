<?php

/**
 * Class DefaultController
 * This class handles all listing requests
 * @author michelle 03-02-15
 */
Yii::import('billing.models.HospitalBill');
Yii::import('application.models.PersonInsurance');

class DefaultController extends Controller
{
    /**
     * Returns patient list from ledger
     */
    public function actionIndex()
    {
        Yii::import('collections.models.CreditCollection');

        # added by: syboy 10/15/2015 : meow
        Yii::import('application.models.GrantAccountType');
        $model1 = GrantAccountType::model()->findAll('deleted IN (0)');
        $access = Yii::app()->SESSION['sess_permission'];
        $permissions = explode(" ", $access);
        # ended

        $encounterNr = Yii::app()->request->getQuery('encounter');
        $billNr = Yii::app()->request->getQuery('billNr');

        $model = CreditCollection::findPatientUndeletedItems($encounterNr);

        $data = array();
        foreach ($model as $k => $item) {
            $remarksArr = array('exploded' => explode("=", $item->remarks));
            $data[] = array(
                'id' => $item->id,
                'amount' => number_format($item->total,2),
                'category' => CreditCollection::getCategoryAlias($item->pay_type),
                'control_nr' => $item->control_nr,
                'create_time' => date('F j, Y',strtotime($item->create_time)),     #added by gelie 10-10-2015
                'approved_date' => strtotime($item->approved_date) ? date('F j, Y',strtotime($item->approved_date)) : 'Not Specified',
                'approved_date_raw' => $item->approved_date
            );

            $str = "<div id='remarks-$item->id' data-id='$item->id'><ul>";
            foreach ($remarksArr as $innerRemarks) {
                foreach ($innerRemarks as $remarks) {
                    if ($remarks)
                        $str .= "<li>" . $remarks . "</li>";
                }
                $str .= "</ul></div>";
                $data[$k]['remarks'] = $str;
            }

            # added by: syboy 10/15/2015 : meow
            $alias = '';
            $alias2 = '';

            require_once($root_path . 'include/care_api_classes/class_acl.php');
            $objAcl = new \Acl(Yii::app()->SESSION['sess_temp_userid']);

            $CCAccountsparentOnly = $objAcl->checkPermissionRaw('_a_1_ccaccount');
            $all = $objAcl->checkPermissionRaw('_a_0_all');

            // Check if permission is only "Accounts" (parent) 
            foreach ($model1 as $type) {
                $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$type->id.'_'.$type->type_name);

                if ($haspermission) 
                    $CCAccountsparentOnly = false;
            }
            // end checker

            foreach($model1 as $type){
                $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$type->id.'_'.$type->type_name);

                if(($CCAccountsparentOnly || $all) || ($haspermission && !$CCAccountsparentOnly && !$all)){
                    $alias .= strtoupper($type->type_name).'|';
                    $alias2 .= strtoupper($type->alt_name).'|';
                }

                $data[$k]['alias'] = $alias;
                $data[$k]['alias2'] = $alias2;
            }
            /*foreach ($permissions as $permission) {
                 foreach ($model1 as $type) {
                     if ($permission == "_a_0_all" || $permission == '_a_1_grant_account_'.$type->id.'_'.$type->type_name) {
                          $alias .= strtoupper($type->type_name).'|';
                          $alias2 .= strtoupper($type->alt_name).'|';
                     }
                 }

                 $data[$k]['alias'] = $alias;
                 $data[$k]['alias2'] = $alias2;
            }*/
            # ended
        }

        echo json_encode($data);
    }

    /**
     * This will return cmap accounts non-deleted and non-locked
     */
    public function actionGuarantors()
    {
        Yii::import('application.models.GrantAccount');
       
        $type = @$_GET['type'];

        $model = GrantAccount::model()->findByPaytype($type);

        $data = array();
        if ($model !== false) {
           foreach ($model as $account) {
              $data[] = array(
                'nr' => $account->id,
                'name' => strtoupper($account->name)
              );
          } 
        }

        if (empty($data)) {
            echo json_encode(false);
        } else {
            echo json_encode($data);
        }
    }

    /**
     * Return list of active encounter and already in `isFinal` = 1 state
     */
    public function actionActiveEncounters()
    {
        $callback = Yii::app()->request->getQuery('_');
        if (!$callback)
            throw new CHttpException(405, 'The system has detected an invalid request');

        $model = HospitalBill::model()->final()->unDeleted()->findAll();

        $res = array();
        foreach ($model as $activeEnc) {
            $person = $activeEnc->encounter->person;
            $encounter = $activeEnc->encounter;
            $insurances = PersonInsurance::model()->findInsuranceByPid($person->pid);
            $isPhic = false;
            $insuranceFirm = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);

            foreach ($insurances as $insurance) {
                if ($insurance->hcare_id == $insuranceFirm->hcare_id) { // hack searches on phic hcare_id
                    $isPhic = true;
                    $phicNo = $insurance->insurance_nr;
                }
            }

            $fullName = $person->getFullName();
            $address = $person->getFullAddress();

            /* html elements */
            $detailsB = CHtml::ajaxButton('Details');
            $selectB = <<<html
            <input type="button" value="SELECT" data-pid="$person->pid"
                data-name="$fullName" data-address="$address" data-encounter="$encounter->encounter_nr"
                data-billnr="$activeEnc->bill_nr"
                data-billdte="$activeEnc->bill_dte"
                data-billfrmdte="$activeEnc->bill_frmdte"
                data-insurancenr="$phicNo"
             onclick="javascript:displayPatientInfo(this)" />

html;

            $typeB = <<<a
            <a href='#'>{$encounter->getEncounterType()}</a>
a;

            $res[] = array(
                'hrn' => $person->pid,
                'sex' => strtoUpper($person->sex),
                'name' => $person->getFullName(),
                'confinement' => $activeEnc->bill_frmdte . ' to ' . $activeEnc->bill_dte,
                'phic' => ($isPhic) ? 'YES' : 'NO',
                'type' => $typeB,
                'caseNo' => $encounter->encounter_nr,
                //'details' => $detailsB, //buttons
                'select' => $selectB, //buttons
            );
        }

        echo json_encode($res);
    }

    /**
     * Retrieves `pay_type`
     */
    public function actionPaytypes()
    {
        Yii::import('application.models.GrantAccountType');
        $model = GrantAccountType::model()->findAll('deleted IN (0)');
        $data[] = array('type' => '0', 'alias' => ' - Select Grant Account - ');
        # added by: syboy 08/27/2015
        /*$access = Yii::app()->SESSION['sess_permission'];
        $permissions = explode(" ", $access);*/

        require_once($root_path . 'include/care_api_classes/class_acl.php');
        $objAcl = new \Acl(Yii::app()->SESSION['sess_temp_userid']);

        $CCAccountsparentOnly = $objAcl->checkPermissionRaw('_a_1_ccaccount');
        $all = $objAcl->checkPermissionRaw('_a_0_all');

        // Check if permission is only "Accounts" (parent) 
        foreach ($model as $type) {
            $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$type->id.'_'.$type->type_name);

            if ($haspermission) 
                $CCAccountsparentOnly = false;
        }
        // end checker

        foreach($model as $type){
            $haspermission = $objAcl->checkPermissionRaw('_a_2_grant_account_'.$type->id.'_'.$type->type_name);

            if(($CCAccountsparentOnly || $all) || ($haspermission && !$CCAccountsparentOnly && !$all)){
                $data[] = array(
                    'type' => $type->id,
                    'alias' => strtoupper($type->alt_name),
                    'deleted' => $type->deleted
                );
            }
        }

        /*foreach ($permissions as $permission) {
            foreach ($model as $type) {
                if ($permission == "_a_0_all" || $permission == '_a_1_grant_account_'.$type->id.'_'.$type->type_name) {
                  $data[] = array(
                    'type' => $type->id,
                    'alias' => strtoupper($type->alt_name),
                    'deleted' => $type->deleted
                  );
                }
            }
        }*/
        #end
        if (empty($data)) {
            echo json_encode(false);
        } else {
            echo json_encode($data);
        }
    }
}