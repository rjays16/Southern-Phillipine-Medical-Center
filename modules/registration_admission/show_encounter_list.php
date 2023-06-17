<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','aufnahme.php');
define('NO_2LEVEL_CHK',1);
/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

include_once $root_path . 'include/inc_ipbm_permissions.php';

$thisfile=basename(__FILE__);
if(!isset($mode)) $mode='show';

require('./include/init_show.php');

# Get all encounter records  of this person
$IPBMviewfilter=0;
if($ipbmcanViewAdmit&&!$ipbmcanViewConsult) $IPBMviewfilter=1;
elseif(!$ipbmcanViewAdmit&&$ipbmcanViewConsult) $IPBMviewfilter=2;
// var_dump($ipbmcanViewConsult);die();
if($isIPBM) $list_obj=&$person_obj->EncounterListIPBM($pid,$IPBMviewfilter);
else $list_obj=&$person_obj->EncounterList($pid);
$rows=$person_obj->LastRecordCount();
//echo $obj->getLastQuery();
#echo $person_obj->sql;
# Create encounter object
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter();

# Get all encounter classes & load in array
if($eclass_obj=$enc_obj->AllEncounterClassesObject()){
	while($ec_row=$eclass_obj->FetchRow()) $enc_class[$ec_row['class_nr']]=$ec_row;
}

$subtitle=$LDListEncounters;
$HTTP_SESSION_VARS['sess_file_return']=$thisfile;

$buffer=str_replace('~tag~',$title.' '.$name_last,$LDNoRecordFor);
$norecordyet=str_replace('~obj~',strtolower($subtitle),$buffer); 

if ($_GET['ptype'])
	$ptype = $_GET['ptype'];

/* Load GUI page */
require('./gui_bridge/default/gui_show.php');
?>
