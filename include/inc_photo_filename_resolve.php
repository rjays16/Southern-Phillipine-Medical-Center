<?php
require_once $root_path . 'include/care_api_classes/class_acl.php';
# Prepare the photo filename
$acl = new Acl($_SESSION['sess_temp_userid']);
$photoViewPermission = $acl->checkPermissionRaw(array('_a_1_ipdpatientphotoview')); //Added by Christian 04-04-2020
if ($photo_filename=='' || $photo_filename=='nopic' || !file_exists($root_path.$photo_path.'/'.$photo_filename) || !$photoViewPermission){
   $img_source=createLDImgSrc($root_path,'x-blank.gif','0');
}else{
	$img_source='src="'.$root_path.$photo_path.'/'.$photo_filename.'" border=0 ';
	$img_size=GetImageSize($root_path.$photo_path.'/'.$photo_filename);
	
	/*
	if ($img_size[0]>137||$img_size[1]>150){
		if($img_size[1]>150) $buf=' height=150';
		if ($img_size[0]>137) $buf=' width=137';
		$img_source.=$buf;
	}else{ $img_source.=$img_size[3];}
	*/
	$img_source.=" height=200 width=200";
}

#if there is duplicates
if ((is_object($duperson)) && ($photo_filename)){
	$img_source='src="'.$photo_filename.'" border=0 ';
	$img_size=GetImageSize($photo_filename);
	$img_source.=" width=200 height=200";
}

if ($fpimage_filename=='' || $fpimage_filename=='nopic' || !file_exists($root_path.$fpimage_path.'/'.$fpimage_filename)){
   $fpimg_source=createLDImgSrc($root_path,'fp-blank.gif','0');
}else{
    $fpimg_source='src="'.$root_path.$fpimage_path.'/'.$fpimage_filename.'" border=0 ';
//    $fpimg_size=GetImageSize($root_path.$fpimage_path.'/'.$fpimage_filename);
    
    /*
    if ($img_size[0]>137||$img_size[1]>150){
        if($img_size[1]>150) $buf=' height=150';
        if ($img_size[0]>137) $buf=' width=137';
        $img_source.=$buf;
    }else{ $img_source.=$img_size[3];}
    */
    $fpimg_source.=" height=200 width=200";
}
?>
