<?php
if(!isset($notabs)||!$notabs){
	
//	if($target=="entry")  $img='document-blue.gif'; //echo '<img '.createLDImgSrc($root_path,'admit-blue.gif','0').' alt="'.$LDAdmit.'">';
//		else{ $img='document-gray.gif';}
//	
	//$smarty->assign('pbNew','<a href="medocs_start.php'.URL_APPEND.'&target=entry"><img '.createLDImgSrc($root_path,$img,'0').' title="'.$LDAdmit.'" style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)></a>');
	
	ob_start();
		include_once($root_path.'modules/registration_admission/include/yh_script.php');
		$sTemp1 = ob_get_contents();
	ob_end_clean();
	$smarty->assign('yhScript',$sTemp1);
	
	if($target=="search") $img='such-b.gif';
		else{ $img='such-gray.gif'; }

	$isipbm = ((!empty($_GET['from']) && $_GET['from'] == 'ipbm') ? 1 : 0);
	$ipbmextend = ( ($isipbm) ? "&from=ipbm" : "");

	$smarty->assign('pbSearch','<a href="medocs_data_search.php'.URL_APPEND.'&target=search'.$ipbmextend.'"><img '.createLDImgSrc($root_path,$img,'0').' title="'.$LDSearch.'"  style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)></a>');
	$yhMedSearch = 'medocs_data_search.php'.URL_APPEND.'&target=search';
	ob_start();
		include_once('include/yh_medocs.php');
		$sTemp2 = ob_get_contents();
	ob_end_clean();
	$smarty->assign('yhMedocs',$sTemp2);
	
}

if(!empty($subtitle)) $smarty->assign('subtitle','<font color="#fefefe" SIZE=3  FACE="verdana,Arial"><b>:: '.$subtitle);
?>