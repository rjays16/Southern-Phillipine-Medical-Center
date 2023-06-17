<?php
$root_path='../../';
$top_dir='modules/ipbm/';

// $QuickMenu = array(
// 	array('icon'=>'newpatient.gif', 
// 				'url'=>$root_path.'modules/ipbm/seg-ipbm-pass.php'. URL_APPEND."&userck=$userck".'&target=ipbmreg',
// 				'label'=>'Register'),
				
// 	array('icon'=>'search.gif', 
// 				'url'=>$root_path.'modules/ipbm/seg-ipbm-pass.php'. URL_APPEND."&userck=$userck".'&target=ipbmsearchpatient',
// 				'label'=>'Search'),
	
// 	array('icon'=>'search_plus.gif', 
// 				'url'=>$root_path.'modules/ipbm/seg-ipbm-pass.php'. URL_APPEND."&userck=$userck".'&target=ipbmsearchpatient',
// 				'label'=>'Advance'),
	
// 	array('icon'=>'patdata.gif', 
// 				'url'=>$root_path.'modules/ipbm/seg-ipbm-pass.php'. URL_APPEND."&userck=$userck".'&target=ipbmsearchpatient',
// 				'label'=>'Comprehensive'),						
				
// 	array('label'=>'|'),
	
// 	array('icon'=>'consultation.gif', 
// 				'url'=>$root_path.'modules/ipbm/seg-ipbm-pass.php'. URL_APPEND."&userck=$userck".'&target=ipbmsearchpatient',
// 				'label'=>'Consultation'),	
				
// 	array('label'=>'|'),
	
// 	array('icon'=>'icd10.gif', 
// 				'url'=>$root_path.'modules/ipbmseg-ipbm-pass.php'. URL_APPEND."&userck=$userck".'&target=ipbmsearchpatient',
// 				'label'=>'ICD/ICPM'),									
	
// 	array('label'=>'|'),
				
// 	array('icon'=>'chart.gif', 
// 				'url'=>$root_path.'modules/ipbm/seg-ipbm-pass.php'. URL_APPEND."&userck=$userck".'&target=ipbmsearchpatient',
// 				'label'=>'Reports'),
				
// 	array('label'=>'|')
// );

if (get_magic_quotes_gpc()) {
	define('__ALVIN_GPC_STRIPPED',1);
	$gpc_in = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	while (list($gpc_k,$gpc_v) = each($gpc_in)) {
		foreach ($gpc_v as $gpc_key => $gpc_val) {
			if (!is_array($gpc_val)) {
				$gpc_in[$gpc_k][$gpc_key] = stripslashes($gpc_val);
				continue;
			}
			$gpc_in[] =& $in[$gpc_k][$gpc_key];
		}
	}
	unset($gpc_in);
}

global $GPC;

ob_start();
/*echo "<hr>GET<br>";
print_r($_GET);
echo "<hr>POST<br>";
print_r($_POST);
echo "<hr>COOKIE<br>";
print_r($_COOKIE);*/
$GPC = ob_get_contents();

ob_end_clean();

?>