<?php

if(isset($cfg['css']) && !empty($cfg['css']) && file_exists($root_path.'css/themes/'.$cfg['css'])){
	$sCssFile =$root_path.'css/themes/'.$cfg['css'].'?t='.time();
}else{
	$sCssFile=$root_path.'css/themes/default/default.css?t='.time();
}

echo '<link rel="stylesheet" href="'.$sCssFile.'" type="text/css">';
echo '<link rel="stylesheet" href="'.$root_path.'css/sticky/sticky.full.css" type="text/css" />';
//echo '<link rel="stylesheet" href="'.$root_path.'images/template_css.css" type="text/css" />';

if($cfg['dhtml']){

echo '
<script language="javascript" src="'.$root_path.'js/hilitebu.js"></script>

<STYLE TYPE="text/css">
A:link  {color: '.$cfg['body_txtcolor'].';}
A:hover {color: '.$cfg['body_hover'].';}
A:active {color: '.$cfg['body_alink'].';}
A:visited {color: '.$cfg['body_txtcolor'].';}
A:visited:active {color: '.$cfg['body_alink'].';}
A:visited:hover {color: '.$cfg['body_hover'].';}
</style>';
}
?>

<script language="JavaScript">
	// $j = jQuery.noConflict();
<!--
function popPic(pid,nm){

 if(pid!="") regpicwindow = window.open("<?php echo $root_path ?>main/pop_reg_pic.php<?php echo URL_REDIRECT_APPEND ?>&pid="+pid+"&nm="+nm,"regpicwin","toolbar=no,scrollbars,width=180,height=250");

}
// -->
</script>
