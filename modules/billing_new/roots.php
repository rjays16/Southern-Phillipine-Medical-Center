<?php
	$root_path='../../';
	$top_dir='modules/billing_new/';
	
	$QuickMenu = array(

	/* Commented by carriane 10/08/19; Refer BUG 2561
	array('label'=>'|'),
	array('icon'=>'patdata.gif', 
				'url'=>$root_path.'modules/billing/bill-pass.php{{$URL_APPEND}}&target=seg_billing',
				'label'=>'Process'),
	end carriane */
#added by shand 01/02/2014
	array('label'=>'|'),
	array('icon'=>'patdata.gif', 
				'url'=>$root_path.'modules/billing/bill-pass.php{{$URL_APPEND}}&target=seg_billing_PHIC',
				'label'=>'Process(New)'),
#end by shand 01/02/2014	
	array('label'=>'|'),
	array('icon'=>'statbel2.gif', 
				'url'=>$root_path.'modules/billing/bill-pass.php?{{$URL_APPEND}}&target=seg_billing_list',
				'label'=>'List'),
				
	array('label'=>'|'),

	array('icon'=>'file_update.gif',
				'url'=>$root_path.'modules/billing/bill-pass.php{{$URL_APPEND}}&target=seg_billing_transmittal',
				'label'=>'Transmittal'),
				
	array('label'=>'|'),
	
	array('icon'=>'report.png',
				'url'=>$root_path.'modules/billing/bill-pass.php?{{$URL_APPEND}}&target=seg_billing_reports',
				'label'=>'Reports'),
				
	array('label'=>'|')
	
);
?>
