<?php

use SegHis\models\Hospital;
use SegHis\modules\dialysis\models\DialysisTransaction;
use SegHis\modules\dialysis\models\DialysisPreBill;

class DialysisController extends Controller
{
    public function actionPrintTransactionHistory($caseNr)
    {
        $criteria = new CDbCriteria();
        $criteria->with = array('preBill');
        $criteria->order = 'transaction_date ASC';
        $criteria->addColumnCondition(array(
            'encounter_nr' => $caseNr,
        ));

        /* @var $transactions DialysisTransaction[] */
        $transactions = DialysisTransaction::model()->findAll($criteria);

        /* @var $preBill DialysisPreBill */
        $preBill = DialysisPreBill::model()->findByAttributes(array(
            'encounter_nr' => $caseNr
        ));

        $data = array();
        if(!empty($transactions)) {
            foreach ($transactions as $key => $transaction) {
                /* @var $transaction DialysisTransaction */
                $data[] = array(
                    'number' => $key+1,
                    'time_admitted' => date('F j, Y h:i A', strtotime($transaction->transaction_date)),
                    'time_discharged' => date('F j, Y h:i A', strtotime($transaction->datetime_out)),
                );
            }
        }

        $iReport = new IReport;
        $iReport->format = IReport::PDF;
        $iReport->template = 'DialysisPersonTransactionHistory';

        $hospitalInfo = Hospital::info();
        
        $baseUrl = sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_ADDR'],
            Yii::app()->baseUrl
        );

        $iReport->parameters = array(
            'hosp_agency' => $hospitalInfo->hosp_agency,
            'hosp_addr1' => $hospitalInfo->hosp_addr1,
            'hosp_name' => $hospitalInfo->hosp_name,
            'patient_name' => $preBill->request->person->getFullName(),
            'physician_name' => $preBill->request->doctor->person->getFullName(),
            'prepared_by' => strtoupper($_SESSION['sess_user_name']),
            'doh_logo' => $baseUrl . '/modules/registration_admission/image/Logo_DOH.jpg',
            'spmc_logo' => $baseUrl . '/modules/registration_admission/image/dmc_logo.jpg',
        );


        $iReport->data = $data;
        $iReport->encoding = 'UTF-8';
        $iReport->show();
    }
}