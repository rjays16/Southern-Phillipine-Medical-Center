<?php
$root_path = '../../';
$top_dir = 'modules/industrial_clinic/';


$QuickMenu = array(

	array('icon'=>'newpatient.gif',
				'url'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_reg&from=ic",
				'label'=>'Register'),

	array('icon'=>'search.gif',
				'url'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_searchpatient&from=ic",
				'label'=>'Search'),

	array('icon'=>'consultation.gif',
				'url'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_transactions_hist",
				'label'=>'Trxn'),

	array('icon'=>'calculator_edit.png',
				'url'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_billing",
				'label'=>'Bill'),

    #commented by art 06/26/2014
	/*array('icon'=>'report.png',
				'url'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php'. URL_APPEND."&userck=$userck&target=ic_transaction_daily_report",
				'label'=>'Reports'),*/

    #added by art 06/26/2014
    array('icon'=>'icon-reports.png',
        'url'=>$root_path.'modules/industrial_clinic/seg-ic-pass.php?sid='.$sid.'&lang='.$lang.'&userck='.$userck.'&target=reportgen&from=ic',
        'label'=>'Reports'),

	array('label'=>'|')
)
//
;

