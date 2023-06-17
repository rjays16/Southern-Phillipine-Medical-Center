<?php

/**
 * Class IndexController
 * This holds the operation queries of Collection module
 * @author michelle 03-04-15
 */
Yii::import('billing.models.HospitalBill');
Yii::import('application.models.PersonInsurance');
Yii::import('cashier.models.Cashier');
Yii::import('cashier.models.SegPayRequest');
Yii::import('collections.models.CreditCollection');

class IndexController extends Controller
{
    /**
     * This will calculate patient's bill
     */
    public function actionCalculateBill()
    {
        Yii::import('application.models.Encounter');
       
        $encounterNr = Yii::app()->request->getQuery('encounter');
        $billNr = Yii::app()->request->getQuery('billnr');

        $model = HospitalBill::model()->findAllSaveBillByEncr(array($encounterNr));

        $grossAmount = 0;
        $totalCoverage = 0;
        $totalDiscounts = 0;
        $totalNetAmount = 0;
        $totalDeposits = 0;
        $balance = 0;

        /*$findOR = Cashier::model()->findByEncounter($encounterNr);
        if (!is_null($findOR)) {
            foreach ($findOR->cashierReq as $request) {
                if ($request->service_code == 'DEPOSIT') {
                    $totalDeposits += $request->amount_due;
                }
            }
        }*/

        if (!is_null($model)) {
          $billInfo = $this->getBillInfo($model);
          $grossAmount = $billInfo['grossAmount'];
          $totalCoverage = $billInfo['totalCoverage'];
          $totalDiscounts = $billInfo['totalDiscounts'];
          $totalNetAmount = $billInfo['totalNetAmount'];
          $totalDeposits = $billInfo['totalDeposits'];
        }

        $billInfo['info'] = array(
          'gross' => number_format($grossAmount, 2),
          'coverage' => number_format($totalCoverage, 2),
          'discounts' => number_format($totalDiscounts, 2),
          'deposit' => number_format($totalDeposits, 2),
          'net' => number_format($totalNetAmount, 2)
        );

        $balance = number_format($totalNetAmount, 2);
        $entryItems = CreditCollection::findPatientUndeletedItems($encounterNr);

        // Billing details with discounts, coverages
        $data = array(
          'gross' => number_format($grossAmount, 2),
          'coverage' => number_format($totalCoverage, 2),
          'discounts' => number_format($totalDiscounts, 2),
          'deposit' => number_format($totalDeposits, 2),
          'net' => number_format($totalNetAmount, 2),
          'less' => "(".number_format(CreditCollection::computeBalance($entryItems, $totalNetAmount, false),2).")", // count total grants
          'balance' => number_format(CreditCollection::computeBalance($entryItems, $totalNetAmount, true), 2) // running total balance
        );

        if (isset($_GET['view'])) {

            // Get Person details
            $personObj = Encounter::model()->findByPk($encounterNr)->person;
            $personData = array(
              'pid' => $personObj->pid,
              'fullname' => $personObj->getFullName(),
              'address' => $personObj->getFullAddress(),
              'caseNo' => $encounterNr,
              'bill_nr' => @$_GET['billNr']
            );

            $personDetails = array_merge($personData, $data);

            // Get Collection details
            $collections = CreditCollection::findPatientUndeletedItems($encounterNr);

            $colData = array();
            foreach ($collections as $collection) {
                //if (!$collection->is_deleted) {
                    $colData[] = array(
                      'pay_type' => strtoupper($collection->pay_type),
                      'amount' => number_format($collection->total,2)
                    );
                //}
            }

            echo json_encode(array('person' => $personDetails, 'collections' => $colData));
           // $this->display(array('person' => $personDetails, 'collections' => $colData));

        } else {
            echo json_encode($data);
        }
    }

    /**
     * This will add items to ledger
     */
    public function actionCreate()
    {
        Yii::import('collections.models.CreditCollection');
        Yii::import('application.models.GrantAccountType');

        /*if (!Yii::app()->getRequest()->isAjaxRequest) {
          throw new CHttpException(405, 'The system has detected an invalid request');
        }*/

        $req = Yii::app()->request;
        $encounterNr = Yii::app()->request->getQuery('encounterNr');
        $billNr = $req->getQuery('billNr');
        $user = $_SESSION['sess_temp_userid'];

        //$payType = GrantAccountType::model()->findTypeById($req->getQuery('payType'));
        $payType = $req->getQuery('payType');
        $amount = $req->getQuery('amount');
        $controlNo = $req->getQuery('control_no');
        $approvedDate = $req->getQuery('approved_date');
        $remarks = $req->getQuery('remarks');
        $cmapAccounts = $req->getQuery('selectEl');
        $guarantor = $req->getQuery('hidGuarantor');
        $counter = count($amount);

        $billing = HospitalBill::findAllSaveBillByEncr(array($encounterNr));
        /*
        foreach ($billing as $bill) {
          if ($bill->request_flag == 'paid') {
              throw new CHttpException(500, 'This entry has been already paid. Please coordinate to billing. Thank you.');
          }
        }*/

        // Validates running balance
        $entryItems = CreditCollection::findPatientUndeletedItems($encounterNr);
        $tot = 0;
        if ($entryItems) {
          foreach ($entryItems as $item) {
            $tot += $item->total;
          }
        }

        $res = $this->getBillInfo($billing);
        $runningBalance = 0.00;
        $runningBalance = (float) CreditCollection::computeBalance($entryItems, $res['totalNetAmount'], true);
        //end

        /**
         * @TODO
         * //$isFinal = $req->getQuery('is_final'); this will be the basis what to add
         */
        $i = 0;
        $data = array();
        $history = array();
        $total = 0.00; //amount to be added

        while ($i < $counter) {
            if ($amount[$i] != '') {
          
                $type = GrantAccountType::model()->findTypeById($payType[$i]);
                if ($type == 'fund_checks') {
                  $history[$i] = "Grant Amount Php " . number_format($amount[$i], 2) . " Created on " . date("Y-m-d h:i:s A") . ' by ' . $_SESSION['sess_user_name'] . " with Guarantor " . $controlNo[$i] .  "\n";
                } else {
                  $history[$i] = "Grant Amount Php " . number_format($amount[$i], 2) . " Created on " . date("Y-m-d h:i:s A") . ' by ' . $_SESSION['sess_user_name'] . " with remarks " . $controlNo[$i] .  "\n";
                }

                $total += $amount[$i];
                $data[$i] = array(
                  'encounter_nr' => $encounterNr,
                  'bill_nr' => $billNr,
                  'entry_type' => 'debit',
                  'amount' => $amount[$i],
                  'pay_type' => $type,
                  'control_nr' => $controlNo[$i],
                  'approved_date' => strtotime($approvedDate[$i]) ? date('Y-m-d',strtotime($approvedDate[$i])) : '',
                  'description' => $remarks[$i],
                  'history' => $history[$i],
                  'create_id' => $user,
                  'create_time' => date('YmdHis'),
                  'is_deleted' => 0,
                  'account_nr' => ($guarantor[$i] == '') ? NULL : $guarantor[$i]
                );
            }
            $i++;
        }

        if ($total > $runningBalance) {
          throw new CHttpException(500, 'Amount added exceeded!');
        }

        $isSave = CreditCollection::saveMultipleRecord('seg_credit_collection_ledger', $data);
        if ($isSave) {
          echo json_encode($data);
        } else {
          throw new CHttpException(500, 'Failed to Save Operation');
        }
    }


    /**
     * This will delete (logical) items from ledger
     * @TODO: ajax perform validation. Only ajax request
     * can perform this action.
     */
    public function actionDelete()
    {
        Yii::import('collections.models.CreditCollection');

//        if (!Yii::app()->getRequest()->isAjaxRequest) {
//          throw new CHttpException(405, 'The system has detected an invalid request');
//        }

        $category = CreditCollection::payTypeValues(strtolower(Yii::app()->request->getQuery('category')));
        $encounterNr = Yii::app()->request->getQuery('encounter');
        $remarks = Yii::app()->request->getQuery('remarks'); //reason for deletion

        $user =$_SESSION['sess_temp_userid'];

        $billing = HospitalBill::findAllSaveBillByEncr(array($encounterNr));
        /*foreach ($billing as $bill) {
          if (!is_null($bill->request_flag)) {
            echo json_encode(false);
            return false;
          }
        }*/

        $debitIds = CreditCollection::getItemIdsByEncrAndType(@$_GET['encounter'], CreditCollection::payTypeValues(strtolower(@$_GET['category'])), 'debit',$_GET['controlNr'],$_GET['approvedDate']);
        $creditIds = CreditCollection::getItemIdsByEncrAndType(@$_GET['encounter'], CreditCollection::payTypeValues(strtolower(@$_GET['category'])), 'credit',$_GET['controlNr'],$_GET['approvedDate']);

        $needles = array();
        foreach ($creditIds as $creditObj) {
          $needles[] = $creditObj['refno'];
        }

        $toBeSaved = array();
        foreach ($debitIds as $debitObj) {
          if (!in_array($debitObj['id'], $needles))
            $toBeSaved[] = $debitObj['id'];
        }

        foreach ($toBeSaved as $id) {
            $model = CreditCollection::model()->findByPk($id);
            $data[] = array(
              'ref_no' => $model->id,
              'encounter_nr' => $model->encounter_nr,
              'bill_nr' => $model->bill_nr,
              'entry_type' => 'credit',
              'amount' => $model->amount,
              'pay_type' => $model->pay_type,
              'control_nr' => $model->control_nr,
              'approved_date' => $model->approved_date,
              'description' => 'Revoked PHP ' . $model->amount,
              'history' => 'Deleted amount Php ' . number_format($model->amount,2) . ' on ' . date("Y-m-d h:i:s A") . ' by ' . $_SESSION['sess_user_name'] . ' with reason ' . $remarks,
              'create_id' => $user,
              'create_time' => date('YmdHis'),
              'is_deleted' => 0,
              'account_nr' => $model->account_nr
            );
        }

        $isSave = CreditCollection::saveMultipleRecord('seg_credit_collection_ledger', $data);
        if ($isSave) {
          echo json_encode($data);
        } else {
          echo json_encode(false);
        }
    }

    /**
     * This will handle searching by pid, firstname, lastname
     * Expected return active encounter details
     */
    public function actionSearch()
    {
        $query = $_GET['q'];
        Yii::import('application.models.Person');
        Yii::import('billing.models.HospitalBill');

        $person = new Person('search');

        if (is_numeric($query)) {
          $person->pid = $query;
        } elseif (!Person::isValidQuery($query)) {
          throw new CHttpException(500, 'Search query is not in the recommended format');
        } else {
          $names = explode(',', $query);
          $person->name_last = trim($names[0]);
          $person->name_first = trim($names[1]);
        }

        $dp = $person->search();

        if (isset($_GET['page'])) {
          $page = $_GET['page'] - 1;
          $dp->pagination->setCurrentPage($page);
        }

        $data = array();
        $dataEnc = array();
        foreach ($dp->getData() as $item) {
            $insurances = PersonInsurance::model()->findInsuranceByPid($item->pid);
            $isPhic = false;
            foreach ($insurances as $insurance) {
                if ($insurance->hcare_id == $insuranceFirm->hcare_id) {
                  $isPhic = true;
                  $phicNo = $insurance->insurance_nr;
                }
            }

            $pid = $item->pid;
            $fullName = $item->getFullName();
            $address = $item->getFullAddress();

            foreach ($item->encounter as $enc) {
                $type = $enc->getEncounterType();
                $typeB = <<<html
                    <label style="text-decoration: underline; color: #03C">$type</label>
html;

                $result = HospitalBill::model()->findByAttributes(array('encounter_nr' => $enc->encounter_nr));
                if ($result) {

                    $selectB = <<<html
                    <input type="button" class="segButton" value="Select" data-pid="$pid" style="cursor: pointer;
                        color: #006;
                        font-weight: bold;
                        padding: 0px 2px;"
                        data-name="$fullName" data-address="$address" data-encounter="$enc->encounter_nr"
                        data-billnr="$result->bill_nr"
                        data-billdte="$result->bill_dte"
                       data-billfrmdte="$result->bill_frmdte"
                       data-insurancenr="$phicNo"
                    onclick="javascript:displayPatientInfo(this)" />
html;
                    $icon = "spm.gif";
                    if ($enc->person->sex == 'f')
                      $icon = "spf.gif";

                    $genderIcon = <<<html
                      <img src="../../gui/img/common/default/$icon" border="0">
html;

                    $data[] = array(
                        'hrn' => $item->pid,
                        'sex' => $genderIcon,
                        'name' => $item->getFullName(),
                        'confinement' => date('Y-m-d h:i:s A', strtotime($result->bill_frmdte)) . ' to ' . date('Y-m-d h:i:s A', strtotime($result->bill_dte)),
                        'phic' => ($isPhic) ? 'YES' : 'NO',
                        'type' => $typeB,
                        'caseNo' => $enc->encounter_nr,
                        'select' => $selectB, //buttons
                    );
                }

            }
        }

        echo json_encode(array(
            'persons' => $data,
            'total' => $dp->getTotalItemCount()
        ));
    }

    /**
     * This will search by encounter
     */
    public function actionSearchByEncounter()
    {
        $query = $_GET['q'];
        Yii::import('billing.models.HospitalBill');
        Yii::import('application.models.Person');

        $billing = new HospitalBill('search');

        if (is_numeric($query))
            $billing->encounter_nr = $query;
        else
            throw new CHttpException(500, 'Search query is not in the recommended format');

        $dp = $billing->search();

        $data = array();
        foreach ($dp->getData() as $item) {
            if (!$item->is_deleted) {
                $pid = $item->encounter->person->pid;
                $fullName = $item->encounter->person->getFullName();
                $address = $item->encounter->person->getFullAddress();

                $insurances = PersonInsurance::model()->findInsuranceByPid($item->encounter->person->pid);
                $isPhic = false;
                $insuranceFirm = InsuranceProvider::getProviderByShortFirmId(InsuranceProvider::INSURANCE_PHIC);

                foreach ($insurances as $insurance) {
                    if ($insurance->hcare_id == $insuranceFirm->hcare_id) { // hack searches on phic hcare_id
                        $isPhic = true;
                        $phicNo = $insurance->insurance_nr;
                    }
                }

                $selectB = <<<html
            <input type="button" value="Select" data-pid="$pid" style="cursor: pointer;
                color: #006;
                font-weight: bold;
                padding: 0px 2px;"
                data-name="$fullName" data-address="$address" data-encounter="$item->encounter_nr"
                data-billnr="$item->bill_nr"
                data-billdte="$item->bill_dte"
                data-billfrmdte="$item->bill_frmdte"
                data-insurancenr="$phicNo"
             onclick="javascript:displayPatientInfo(this)" />
html;
                $type = $item->encounter->getEncounterType();
                $typeB = <<<html
                    <label style="text-decoration: underline; color: #03C">$type</label>
html;

                $icon = "spm.gif";
                if ($item->encounter->person->sex == 'f')
                  $icon = "spf.gif";

                $genderIcon = <<<html
                        <img src="../../gui/img/common/default/$icon" border="0">
html;

                $data[] = array(
                    'hrn' => $pid,
                    'sex' => $genderIcon,
                    'name' => $item->encounter->person->getFullName(),
                    'confinement' => date('Y-m-d h:i:s A', strtotime($item->bill_frmdte)) . ' to ' . date('Y-m-d h:i:s A', strtotime($item->bill_dte)),
                    'phic' => ($isPhic) ? 'YES' : 'NO',
                    'type' => $typeB,
                    'caseNo' => $item->encounter_nr,
                    //'details' => $detailsB, //buttons
                    'select' => $selectB, //buttons
                );
            }
        }

        echo json_encode(array(
            'persons' => $data,
            'total' => $dp->getTotalItemCount()
        ));

    }

    /**
     * This will return billing info
     */
    public function getBillInfo($model)
    { 
       $grossAmount = 0;
       $totalCoverage = 0;
       $totalDiscounts = 0;
       $totalDeposits = 0;
       $totalNetAmount = 0;

       foreach ($model as $amount) {
          $grossAmount += $amount->getTotalCharges();
          $totalCoverage += $amount->getTotalCoverage();
          $totalDiscounts += $amount->getTotalDiscounts();
          $totalDeposits += $amount->total_prevpayments;
          $totalNetAmount += $grossAmount - $totalCoverage - $totalDiscounts - $totalDeposits;
        }

        return array(
          'grossAmount' => $grossAmount,
          'totalCoverage' => $totalCoverage,
          'totalDiscounts' => $totalDiscounts,
          'totalDeposits' => $totalDeposits,
          'totalNetAmount' => $totalNetAmount
        );
    }
}