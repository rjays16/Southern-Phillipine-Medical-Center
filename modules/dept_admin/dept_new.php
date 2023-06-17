<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/


$dept_logos_path='gui/img/logos_dept/'; # Define the path to the department logos
$lang_tables[]='departments.php';
$lang_tables[]='phone.php';
$lang_tables[]='doctors.php';
define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_comm.php');
require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
$ehr = Ehr::instance();

$breakfile='dept_list.php'.URL_APPEND;

if(!isset($mode)) $mode='';
# Create department object
$dept_obj=new Department;
#create com object
$comm=new Comm;

//$db->debug=1;

# Validate 3 most important inputs
if(isset($mode)&&!empty($mode)&&$mode!='select'){
	if(empty($HTTP_POST_VARS['name_formal'])||empty($HTTP_POST_VARS['id'])||empty($HTTP_POST_VARS['type'])){
		$inputerror=TRUE; # Set error flag
	}
	//if($mode=='update'&&empty($HTTP_POST_VARS['id'])) $inputerror=TRUE;
}

if(!empty($mode)&&!$inputerror){

	$is_img=false;
	# If a pic file is uploaded move it to the right dir
	if(is_uploaded_file($HTTP_POST_FILES['img']['tmp_name']) && $HTTP_POST_FILES['img']['size']){
		$picext=substr($HTTP_POST_FILES['img']['name'],strrpos($HTTP_POST_FILES['img']['name'],'.')+1);
		if(stristr('jpg,gif,png',$picext)){
			$is_img=true;	
			# Forcibly convert file extension to lower case.
			$HTTP_POST_VARS['logo_mime_type']=strtolower($picext);
		}
	}
	
	#echo "create : HTTP_POST_VARS = ";
	#print_r($HTTP_POST_VARS);
	#echo "<br>\n";
	#-----------------///trace this one
			/*if ($is_sub_dept==0){
				echo " \n <script type=\"text/javascript\">alert(\"sulod?!\")</script>";
				$HTTP_POST_VARS['parent_dept_nr']=0;
			}*/
			#------------------
	switch($mode)
	{	
		case 'create': 
		{
			
			$HTTP_POST_VARS['history']='Create: '.date('Y-m-d H:i:s').' '.$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_time']=date('YmdHis');
			$HTTP_POST_VARS['modify_time']=date('YmdHis');
			
			$dept_obj->setDataArray($HTTP_POST_VARS);
			if($dept_obj->insertDataFromInternalArray()){
				
				# Get the inserted primary key as department nr.
				$oid=$db->Insert_ID();
				$dept_nr=$dept_obj->LastInsertPK('nr',$oid);
				
				# If telephone/beeper info available, save into the phone table
				if($HTTP_POST_VARS['inphone1']
					||$HTTP_POST_VARS['inphone2']
					||$HTTP_POST_VARS['inphone3']
					||$HTTP_POST_VARS['funk1']
					||$HTTP_POST_VARS['funk2']){
						$HTTP_POST_VARS['dept_nr']=$dept_nr;
						$HTTP_POST_VARS['name']=$HTTP_POST_VARS['name_formal'];
						$HTTP_POST_VARS['vorname']=$HTTP_POST_VARS['id'];
						$comm->setDataArray($HTTP_POST_VARS);
						if(!@$comm->insertDataFromInternalArray()) echo $comm->getLastQuery()."<br>$LDDbNosave";
				}

                try{
                    $utf8_encode_data = array_map("utf8_encode", $HTTP_POST_VARS);
                    $deptDetails = $ehr->postAreaCatalog($utf8_encode_data);
                } catch (Exception $ex) {
                    error_log("Unable to update to EHR. Error: ".$ex, 0);
                }

				# Save the uploaded image
				if($is_img){
				    $picfilename='dept_'.$dept_nr.'.'.$picext;
			       copy($HTTP_POST_FILES['img']['tmp_name'],$root_path.$dept_logos_path.$picfilename);
				}
				header("location:dept_info.php".URL_REDIRECT_APPEND."&edit=1&mode=newdata&dept_nr=$dept_nr");
				exit;
			}else{
				echo $dept_obj->getLastQuery."<br>$LDDbNoSave";
			}
			break;
		}	
		case 'update':
		{ 
			
			$HTTP_POST_VARS['history']=$dept_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_time']=date('YmdHis');
			
			$dept_obj->setTable('care_department');
			
			$dept_obj->setDataArray($HTTP_POST_VARS);
			$dept_obj->where=' nr='.$dept_nr;
			if($dept_obj->updateDataFromInternalArray($dept_nr)){
				 
				# Update phone data
				if($comm->DeptInfoExists($dept_nr)){
							
					$HTTP_POST_VARS['name']=$HTTP_POST_VARS['name_formal'];
					$HTTP_POST_VARS['vorname']=$HTTP_POST_VARS['id'];
					$comm->setDataArray($HTTP_POST_VARS);
					$comm->setWhereCondition("dept_nr=$dept_nr");
					@$comm->updateDataFromInternalArray($dept_nr);
				}else{
					if($HTTP_POST_VARS['inphone1']
						||$HTTP_POST_VARS['inphone2']
						||$HTTP_POST_VARS['inphone3']
						||$HTTP_POST_VARS['funk1']
						||$HTTP_POST_VARS['funk2']){
							$HTTP_POST_VARS['dept_nr']=$dept_nr;
							$HTTP_POST_VARS['name']=$HTTP_POST_VARS['name_formal'];
							$HTTP_POST_VARS['vorname']=$HTTP_POST_VARS['id'];
							$HTTP_POST_VARS['history']="Create: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
							$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
							$HTTP_POST_VARS['create_time']=date('YmdHis');
							$comm->setDataArray($HTTP_POST_VARS);
						if(!@$comm->insertDataFromInternalArray()) echo $comm->getLastQuery()."<br>$LDDbNoSave";
					}
				}

                try{
                    $ehr = Ehr::instance();
                    $utf8_encode_data = array_map("utf8_encode", $HTTP_POST_VARS);
                    $deptDetails = $ehr->postAreaCatalog($utf8_encode_data);
                } catch (Exception $ex) {
                    error_log("Unable to update to EHR. Error: ".$ex, 0);
                }
				# Save uploaded image
				if($is_img){
				    $picfilename='dept_'.$dept_nr.'.'.$picext;
			        copy($HTTP_POST_FILES['img']['tmp_name'],$root_path.$dept_logos_path.$picfilename);
				}
				header("location:dept_info.php".URL_REDIRECT_APPEND."&edit=1&mode=newdata&dept_nr=$dept_nr");
				exit;
			}else{
				echo $dept_obj->getLastQuery."<br>$LDDbNoSave";
			}
			break;
		}
		case 'select':
		{
			# Get department�s information
			$dept=$dept_obj->getDeptAllInfo($dept_nr);
			//while(list($x,$v)=each($dept)) $$x=$v;
			extract($dept);
			
			# Get departments phone info
			if($dept_phone=$comm->DeptInfo($dept_nr)){
				extract($dept_phone);
			}
		}	
	}// end of switch
}

$deptarray=$dept_obj->getAllActiveSort('name_formal');
$depttypes=$dept_obj->getTypes();

# Prepare title
$sTitle = "$LDDepartment :: ";
if($mode=='select') $sTitle = $sTitle.$LDUpdate;
	else $sTitle = $sTitle.$LDCreate;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',$sTitle);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_create.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',$sTitle);

# Buffer page output

ob_start();
?>

<style type="text/css" name="formstyle">

td.pblock{ font-family: verdana,arial; font-size: 12}
div.box { border: solid; border-width: thin; width: 100% }
div.pcont{ margin-left: 3; }

</style>

<script language="javascript">
<!-- 

function chkForm(d){

	if(d.name_formal.value==""){
		alert("<?php echo $LDPlsNameFormal ?>");
		d.name_formal.focus();
		return false;
	}else if(d.id.value==""){
		alert("<?php echo $LDPlsDeptID ?>");
		d.id.focus();
		return false;
	}else if(d.type.value==""){
		alert("<?php echo $LDPlsSelectType ?>");
		d.type.focus();
		return false;
	}else if(d.LD_var.value==""){
		alert("<?php echo $LDPlsLDVar ?>");
		d.LD_var.focus();
		return false;
	}
		return true;
	
}

//--------added 02-19-07-----------
function enableParent(id){
	var check = (id=="y") ? true : false;
	if(check){
		document.forms["newstat"].parent_dept_nr2.disabled=false; // yes with parent
		document.forms["newstat"].parent_dept_nr.value=document.forms["newstat"].parent_dept_nr2.value;
		document.forms["newstat"].parent_dept_nr2.focus();
		
	}else{
		document.forms["newstat"].parent_dept_nr2.value=0;
		document.forms["newstat"].parent_dept_nr.value=0;
		document.forms["newstat"].parent_dept_nr2.disabled=true;  // no parent
	}
}

function getParent_dept(){
	document.forms["newstat"].parent_dept_nr.value=document.forms["newstat"].parent_dept_nr2.value
}

function get_Admit_status(value){
	//alert("value ="+value);
	if (value==1)
		document.forms["newstat"].admit_inpatient.value = 1;
	else
		document.forms["newstat"].admit_inpatient.value = 0;	
}

function preset(){
var admit_inpatient

    //document.forms["newstat"].admit_inpatient2.checked = 1;
	 hide_Admit();
	 //admit_inpatient = <?php echo $admit_inpatient; ?>;
	 //document.forms["newstat"].admit_inpatient.value = admit_inpatient;
	 //alert("preset : "+document.forms["newstat"].admit_inpatient[1].checked);
	 //document.forms["newstat"].admit_inpatient[0].checked = false;  // yes
	 //document.forms["newstat"].admit_inpatient[1].checked = true;   // no 
	 if (document.forms["newstat"].parent_dept_nr2.value != 0){ 
	 	document.forms["newstat"].parent_dept_nr2.disabled=false;
		document.forms["newstat"].parent_dept_nr.value = document.forms["newstat"].parent_dept_nr2.value;
	 }else{
	 	document.forms["newstat"].parent_dept_nr2.disabled=true;
		document.forms["newstat"].parent_dept_nr2.value=0;
		document.forms["newstat"].parent_dept_nr.value = document.forms["newstat"].parent_dept_nr2.value;
	 }
}

function blocking(objectName, flag){
   if (document.layers) {
      document.layers[objectName].display = (flag) ? '' : 'none';
   } else if (document.all) {
      document.all[objectName].style.display = (flag) ? '' : 'none';
   } else if (document.getElementById) {
      document.getElementById(objectName).style.display = (flag) ? '' : 'none';
   }
}/* end of function blocking*/

function hide_Admit(){
	
	//admit_inpatient = <?php echo $admit_inpatient; ?>;
	//document.forms["newstat"].admit_inpatient.value = admit_inpatient;
	
	if (document.forms["newstat"].type.value==1){
   	blocking("option_admit",true); /* display */ 
   	blocking("option_admit2",true); /* display */ 
   } else {
	 	blocking("option_admit",false);  /* not display */
		document.forms["newstat"].admit_inpatient[0].checked = false;  // yes
	   document.forms["newstat"].admit_inpatient[1].checked = true;   // no
	 	blocking("option_admit2",false);  /* not display */
		document.forms["newstat"].admit_outpatient[0].checked = false;  // yes
	   document.forms["newstat"].admit_outpatient[1].checked = true;   // no
   }
}/*end of function hideThis*/

//---------------------------------
// -->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

 <ul>

 <?php
 if(isset($inputerror)&&$inputerror){
 	echo "<font color=#ff0000 face='verdana,arial' size=2>$LDInputError</font>";
 }
 ?>
 <body onLoad="preset();">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<form action="dept_new.php" method="post" name="newstat" ENCTYPE="multipart/form-data" onSubmit="return chkForm(this)">
<table border=0>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b><?php echo $LDFormalName ?></font>: </td>
    <td class=pblock><input type="text" name="name_formal" size=40 maxlength=40 value="<?php echo trim($name_formal); ?>"><br>
</td>
  </tr> 
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
	<?php echo $LDInternalID ?></font>: 
	</td>
    <td class=pblock>
	<?php
		if($mode=='select') { echo '<input type="hidden" name="id"  value="'.$id.'">'.$id; } else {
	?>
	<input type="text" name="id" size=40 maxlength=40 value="<?php echo trim($id); ?>">
	<?php
	}
	?>
</td>
  </tr> 

<tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b><?php echo $LDTypeDept ?></font>: </td>
    
	 <td class=pblock><select name="type" id="type" onChange="hide_Admit();">
	<?php
		echo "x : '".$x."' \n<br>";
		echo "v : '".$v."' \n<br>";
		echo "depttypes : '".trim($depttypes)."' \n<br>";
		while(list($x,$v)=each($depttypes)){
			echo '
				<option value="'.$v['nr'].'" ';
			if($v['nr']==$type) echo 'selected';
			echo ' >';
			if(isset($$v['LD_var'])&&$$v['LD_var']) echo $$v['LD_var'];
				else echo $v['name'];
			echo '</option>';
		}
	?>
                     </select>
		<img <?php echo createComIcon($root_path,'l_arrowgrnsm.gif','0') ?>> <?php echo $LDPlsSelect ?>
		
</td>
  </tr>
  
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDDescription ?>: </td>
    <td class=pblock><textarea name="description" cols=40 rows=4 wrap="physical"><?php echo trim($description); ?></textarea>
</td>
  </tr>
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b></font><?php echo $LDIsSubDept ?>: </td>
    <td class=pblock>	
	 <!-- -----edited 02-19-07---- -->
	 <input type="radio" name="is_sub_dept" value="1"  <?php if($is_sub_dept) echo 'checked'; ?> onClick="enableParent('y');"> <?php echo $LDYes ?> 
	 <input type="radio" name="is_sub_dept" value="0"  <?php if(!$is_sub_dept) echo 'checked'; ?> onClick="enableParent('n');"> <?php echo $LDNo ?>
	 
	 <!-- -----edited 02-19-07---- -->
</td>
  </tr> 
<tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b></font><?php echo $LDParentDept; ?>: </td>
    <td class=pblock><select name="parent_dept_nr2" id="parent_dept_nr2" onChange="getParent_dept();" disabled>
	<option value="0"> </option>
	<?php
		
		while(list($x,$v)=each($deptarray)){
			echo '
				<option value="'.$v['nr'].'" ';
			if($v['nr']==$parent_dept_nr) echo 'selected';
			echo ' >';
			if(isset($$v['LD_var'])&&$$v['LD_var']) echo $$v['LD_var'];
				else echo $v['name_formal'];
			echo '</option>';
		}
	?>
                     </select>
		<img <?php echo createComIcon($root_path,'l_arrowgrnsm.gif','0') ?>> <?php echo $LDPlsSelect ?>
		<input type="hidden" name="parent_dept_nr" id="parent_dept_nr">
</td>
  </tr>
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">
	<?php if($mode!='select') echo '<font color=#ff0000><b>*</b></font>'; ?>
	<?php echo $LDLangVariable ?>: 
	</td>
    <td class=pblock>
	<?php
		if($mode=='select'){
			echo $LD_var;
		}else{
	?>
	<input type="text" name="LD_var" size=40 maxlength=40 value="<?php echo trim($LD_var); ?>"><br>
	<?php
		}
	?>
</td>
  </tr> 
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDShortName ?>: </td>
    <td class=pblock><input type="text" name="name_short" size=40 maxlength=40 value="<?php echo trim($name_short); ?>"><br>
</td>
  </tr> 
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDAlternateName ?>: </td>
    <td class=pblock><input type="text" name="name_alternate" size=40 maxlength=40 value="<?php echo trim($name_alternate); ?>"><br>
</td>
  </tr> 
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b></font><?php echo $LDDoesSurgeryOp ?>: </td>
    <td class=pblock>	<input type="radio" name="does_surgery" value="1" <?php if($does_surgery) echo 'checked'; ?>> <?php echo $LDYes ?> <input type="radio" name="does_surgery" value="0" <?php if(!$does_surgery) echo 'checked'; ?>> <?php echo $LDNo ?>
</td>
  </tr> 
  
  <tr id="option_admit">
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b></font><?php echo $LDAdmitsInpatients ?>: </td>
    <td class=pblock>	<input type="radio" name="admit_inpatient" id="admit_inpatient" value="1" <?php if($admit_inpatient) echo 'checked'; ?> > <?php echo $LDYes ?> 
	                     <input type="radio" name="admit_inpatient" id="admit_inpatient" value="0" <?php if(!$admit_inpatient) echo 'checked'; ?> > <?php echo $LDNo ?> 
								<!--<input type="hidden" name="admit_inpatient">--></td>
  </tr> 

  <tr id="option_admit2">
		<td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b></font><?php echo $LDAdmitsOutpatients ?>: </td>
		<td class=pblock>	
			<input type="radio" name="admit_outpatient" id=="admit_outpatient" value="1" <?php if($admit_outpatient) echo 'checked'; ?>> <?php echo $LDYes ?> 
			<input type="radio" name="admit_outpatient" id="admit_outpatient" value="0" <?php if(!$admit_outpatient) echo 'checked'; ?>> <?php echo $LDNo ?>
		</td>
  </tr> 
 <!-- 
    <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b></font><?php echo $LDBelongsToInst ?>: </td>
    <td class=pblock>	<input type="radio" name="this_institution" value="1" <?php if($this_institution) echo 'checked'; ?>> <?php echo $LDYes ?> <input type="radio" name="this_institution" value="0" <?php if(!$this_institution) echo 'checked'; ?>> <?php echo $LDNo ?>
</td>
  </tr> 
  -->
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDWorkHrs ?>: </td>
    <td class=pblock><input type="text" name="work_hours" size=40 maxlength=40 value="<?php echo trim($work_hours); ?>"><br>
</td>
  </tr> 

  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDConsultationHrs ?>: </td>
    <td class=pblock><input type="text" name="consult_hours" size=40 maxlength=40 value="<?php echo trim($consult_hours); ?>"><br>
</td>
  </tr> 
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDTelephone ?> 1: </td>
    <td class=pblock><input type="text" name="inphone1" size=40 maxlength=15 value="<?php echo trim($inphone1); ?>"><br>
</td>
  </tr> 
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDTelephone ?> 2: </td>
    <td class=pblock><input type="text" name="inphone2" size=40 maxlength=15 value="<?php echo trim($inphone2); ?>"><br>
</td>
  </tr> 
  
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDTelephone ?> 3: </td>
    <td class=pblock><input type="text" name="inphone3" size=40 maxlength=15 value="<?php echo trim($inphone3); ?>"><br>
</td>
  </tr> 
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo "$LDBeeper ($LDOnCall)" ?> 1: </td>
    <td class=pblock><input type="text" name="funk1" size=40 maxlength=15 value="<?php echo trim($funk1); ?>"><br>
</td>
  </tr> 
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo "$LDBeeper ($LDOnCall)" ?> 2: </td>
    <td class=pblock><input type="text" name="funk2" size=40 maxlength=15 value="<?php echo trim($funk2); ?>"><br>
</td>
  </tr> 
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDSigLine ?>: </td>
    <td class=pblock><input type="text" name="sig_line" size=40 maxlength=40 value="<?php echo trim($sig_line); ?>"><br>
</td>
  </tr> 
 
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDSigStampTxt ?>: </td>
    <td class=pblock><textarea name="sig_stamp" cols=40 rows=4 wrap="physical"><?php echo trim($sig_stamp); ?></textarea>
</td>
  </tr>
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDDeptLogo ?>: </td>
    <td class=pblock><input type="file" name="img" ><br>
</td>
  </tr> 

 
</table>
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="1000000">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="edit" value="<?php echo $edit ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<!--<input type="hidden" name="parent_dept_nr" value="" >-->
<?php
 if($mode=='select') {
?>
<input type="hidden" name="mode" value="update">
<input type="hidden" name="dept_nr" value="<?php echo $nr ?>">  <!-- department nr , not parent nr-->

<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>>
<?php
}
else
{
?>
<input type="hidden" name="mode" value="create">
 
<input type="submit" value="<?php echo $LDCreate ?>">
<?php
}
?>
</form>
<p>

<a href="javascript:history.back()"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a>

</ul>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
</body>