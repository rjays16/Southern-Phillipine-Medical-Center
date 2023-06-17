<?php

require './roots.php';
require_once "../include/inc_environment_global.php";

global $db;

$pid = $_GET['pid'];
$width = (int) $_GET['w'];
//$height = (int) $_GET['h'];

$query = "SELECT photo_filename FROM care_person WHERE pid=".$db->qstr($pid);
$filename = $db->GetOne($query);

if ($filename && file_exists('registration/'.$filename)){
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
