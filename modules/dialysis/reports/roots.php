<?php
$root_path = '../../../';
$top_dir = 'modules/dialysis/';
$userck="ck_dialysis_user";
$from="&from=dialysis";

// Edited QuickMenu. Jayson Garcia -OJT 2/10/2014

$QuickMenu = array(
	array('icon'=>'door_in.png',
				'url'=>$root_path.'modules/dialysis/seg-dialysis-request-new.php{{$URL_APPEND}}',
				'label'=>'Request'),

	// array('icon'=>'group_edit.png',
	// 			'url'=>$root_path.'modules/dialysis/seg-dialysis-request-list.php{{$URL_APPEND}}',
	// 			'label'=>'List'),

	array('icon'=>'statbel2.gif',
				'url'=>$root_path.'modules/dialysis/seg-dialysis-machine-list.php{{$URL_APPEND}}',
				'label'=>'List of Patients'),

	/*array('icon'=>'folder_user.png',
				'url'=>$root_path.'modules/dialysis/seg-dialysis-billing.php{{$URL_APPEND}}',
				'label'=>'Billing'),*/
	
	array('icon'=>'file_update.gif',
				'url'=>$root_path.'modules/billing/billing-transmittal.php{{$URL_APPEND}}'.$userck.$from,
				'label'=>'Transmittal'),


	array('icon'=>'chart_bar.png',
				'url'=>$root_path.'modules/dialysis/seg-dialysis-reports.php{{$URL_APPEND}}',
				'label'=>'Reports'),

	array('label'=>'|')
);

