<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/pharmacy/ajax/return.common.php");
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

require_once($root_path.'include/care_api_classes/class_inventory.php');
$inv_obj = new Inventory;

$inv_area = $inv_obj->getInventoryAreaByPersonnel($_SESSION['sess_login_personell_nr']);
$invArr = array();
if(!empty($inv_area)){
	while ($row = $inv_area->FetchRow()){
		$invArr[] = $row['area_code'];
	}
}
if (!empty($invArr[0]))
	$_GET['area'] = $invArr[0];
$inv_area = json_encode($invArr);

$title=$LDPharmacy;
if (!$_GET['from'])
	$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND."&userck=$userck";
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/pharmacy/apotheke-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}
$imgpath=$root_path."pharma/img/";
$thisfile='seg-pharma-return.php';

$target = $_GET["target"];
if (!isset($_GET["target"])) $target = "edit";
include_once("seg-pharma-return-$target.php");
