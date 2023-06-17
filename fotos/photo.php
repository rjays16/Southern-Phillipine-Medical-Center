<?php

require './roots.php';
require_once "../include/inc_environment_global.php";
require_once $root_path . 'include/care_api_classes/class_acl.php';

global $db;

$acl = new Acl($_SESSION['sess_temp_userid']);
$photoViewPermission = $acl->checkPermissionRaw(array('_a_1_ipdpatientphotoview')); #added by Christian 04-04-2020

$pid = $_GET['pid'];
$width = (int) $_GET['w'];
//$height = (int) $_GET['h'];

$query = "SELECT photo_filename FROM care_person WHERE pid=".$db->qstr($pid);
$filename = $db->GetOne($query);

if ($filename && file_exists('registration/'.$filename) && $photoViewPermission){
	$filename = 'registration/'.$filename;
}
else
	$filename = './foto-na.jpg';


// Content type
header('Content-type: image/jpeg');

// Get new sizes
list($oWidth, $oHeight) = getimagesize($filename);

if (!$width) 	$width = 128;
$height = (int) (($width/$oWidth) * $oHeight);

// Load
$resized = imagecreatetruecolor($width, $height);
$source = imagecreatefromjpeg($filename);

// Resize
imagecopyresized($resized, $source, 0, 0, 0, 0, $width, $height, $oWidth, $oHeight);

// Output
imagejpeg($resized);