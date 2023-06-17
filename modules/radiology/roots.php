<?php
$root_path='../../';
$top_dir='modules/radiology/';
// var_dump($_GET['ob']);exit();
#if ($_GET['popUp']!='1'){
$getOB=($_GET['ob']=='OB' ?"&ob=OB" :"");
// var_dump($getOB);exit();

if (($_GET['popUp']!='1')&&(empty($_GET['view_from']))){
	if($_GET['ob']=='OB'){
		$QuickMenu = array(array('icon'=>'patdata.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_test'.$getOB,
				'label'=>'New'),
	array('icon'=>'waiting.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php?{{$URL_APPEND}}&target=radiorequestlist&dept_nr=158'.$getOB,
				'label'=>'List'),
    array('icon'=>'book_hotel.gif',
                'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_cal_list&dept_nr=158'.$getOB,
                'label'=>'Serve/Schedule'),
    
    array('icon'=>'bestell.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_undone&dept_nr=158'.$getOB,
				'label'=>'Undone'),
	array('label'=>'|'),
	
	array('icon'=>'book_go.png',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_reader_fee&user_origin=radio&dept_nr=158'.$getOB,
				'label'=>'Readers Fee'),
	
	array('label'=>'|'),			


);
		}else{
	$QuickMenu = array(array('icon'=>'patdata.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_test'.$getOB,
				'label'=>'New'),

	array('icon'=>'waiting.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php?{{$URL_APPEND}}&target=radiorequestlist&dept_nr=158'.$getOB,
				'label'=>'List'),
	
    /*array('icon'=>'disc_unrd.gif',
                'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=segradiotech&dept_nr=158',
                'label'=>'Serve'),            
    
	array('icon'=>'calmonth.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_cal&dept_nr=158',
				'label'=>'Schedule'),
	
	array('icon'=>'book_hotel.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_cal_list&dept_nr=158',
				'label'=>'Schedule List'),
    */
    array('icon'=>'book_hotel.gif',
                'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_cal_list&dept_nr=158'.$getOB,
                'label'=>'Serve/Schedule'),
    
    array('icon'=>'bestell.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_undone&dept_nr=158'.$getOB,
				'label'=>'Undone'),

	array('icon'=>'documents.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_done&dept_nr=158'.$getOB,
				'label'=>'Done'),

	array('icon'=>'file_update.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_unified&dept_nr=158'.$getOB,
				'label'=>'Unified'),

	array('label'=>'|'),

	array('icon'=>'torso.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_patient&dept_nr=158'.$getOB,
				'label'=>'Film'),

	array('icon'=>'torso_br.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_borrow&dept_nr=158'.$getOB,
				'label'=>'Borrowers'),

	array('icon'=>'book_go.png',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_reader_fee&user_origin=radio&dept_nr=158'.$getOB,
				'label'=>'Readers Fee'),

	array('label'=>'|'),

	array('icon'=>'chart.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=segradioreports'.$getOB,
				'label'=>'Reports'),

	array('label'=>'|')
);

			}

}else{
		if ($_GET['ptype']){
			$QuickMenu = array(
				array('icon'=>'patdata.gif',
					'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_test&popUp=1',
					'label'=>'New')
			);
		}
}
?>
