<?php
$root_path='../../';
$top_dir='modules/ic_lab/';
#echo "from = ".$_GET['view_from'];
if (($_GET['popUp']!='1')&&(empty($_GET['view_from']))){
$QuickMenu = array(
	array('icon'=>'patdata.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=ICLab&user_origin=iclab',
				'label'=>'New'),

	array('icon'=>'statbel2.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php?{{$URL_APPEND}}&target=ICLab_list&user_origin=iclab',
				'label'=>'List'),

	array('icon'=>'hfolder.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=ICLab_result&done=0&user_origin=iclab',
				'label'=>'Undone'),

	array('icon'=>'task_tree.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=ICLab_result&done=1&user_origin=iclab',
				'label'=>'Done'),

	array('label'=>'|'),

	/*array('icon'=>'chart.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=seglabreports',
				'label'=>'Reports'),
	*/
	array('label'=>'|')
);
}else{
	if ($_GET['ptype']){
		$QuickMenu = array(
			array('icon'=>'patdata.gif',
					'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=ICLab&popUp=1&user_origin=iclab',
					'label'=>'New')
		 );
	}
}
?>
