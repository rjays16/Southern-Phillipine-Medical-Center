<?php
include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person;

include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');
$address_brgy = new Address('barangay');

define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';
require($root_path.'include/inc_front_chain_lang.php');

if(isset($_GET['pid'])&& $_GET['pid']){
	$pid = $_GET['pid'];
}
if(isset($_POST['pid']) && $_POST['pid']){
	$pid = $_POST['pid'];
}

include_once($root_path.'include/care_api_classes/class_cert_death.php');
$obj_deathCert = new DeathCertificate($pid);

if($pid){
	//if(!($basicInfo = $person_obj->BasicDataArray($pid))){
	if(!($basicInfo = $person_obj->getAllInfoArray($pid))){
		echo '<em class="warn">Sorry but the pare cannot be displayed!</em> ';
		exit();
	}
	extract($basicInfo);
	$brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
	if($brgy_info){
		$brgy_row = $brgy_info->FetchRow();
	}
}else{
	echo '<em class="warn"> Sorry but the page cannot be displayed! <br> Invalid PID!</em>';
	exit();
}

$birthYear = intval(substr($date_birth, 0, 4)); 
$birthMonth = intval(substr($date_birth, 5, 7)); 
$birthDay = intval(substr($date_birth, 8, 10)); 
/*
echo "birthYear = '".$birthYear."' <br>\n";
echo "birthMonth = '".$birthMonth."' <br>\n";
echo "birthDay = '".$birthDay."' <br>\n";
*/



//if($basicInfo1 = $person_obj->getAllInfoObject($pid)){
//	extract($basicInfo1);
//}

//if($personInfo = $person_obj->getAllInfoArray($pid)){
//	extract($personInfo);
//}

print_r($basicInfo);

// subject to change.. just a reference.. 
/*
$birthYear = intval(substr($date_birth, 0, 4));
$birthMonth = intval(substr($date_birth, 5, 7));
$birthDay = intval(substr($date_birth, 8, 10));
*/

$errMsg = '';
if(isset($_POST['mode'])){
	switch($_POST['mode']){
	case 'save':
		
		$HTTP_POST_VARS['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
		$HTTP_POST_VARS['create_id'] = $HTTP_SESSION_VARS['sess_user_name'];
		$HTTP_POST_VARS['create_time'] = date('Y-m-d H:i:s');
		//saving data of death info
		if($obj_deathCert->saveDeathCertificateInfoFromArray($HTTP_POST_VARS)){
			$errMsg='<font style="color:#FF0000;font-style:italic">'."Saved successfully!".'</font>';
		}else{
			$errMsg='<font style="color:#FF0000">'.$obj_deathCert->getErrorMsg().'</font>';
		}
		
	break;	
	case 'update':
		//code in here
		 $HTTP_POST_VARS['history'] = "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
		 $HTTP_POST_VARS['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
		 $HTTP_POST_VARS['modify_time'] = date('Y-m-d H:i:s');
		 if($obj_deathCert->updateDeathCertificateInfoFromArray($HTTP_POST_VARS)){
		 	$errMsg='<font style="color:#FF0000;font-style:italic">'."Updated successfully!".'</font>';
		 }else{
		 	$errMsg='<font style="color:#FF0000">'.$obj_deathCert->getErrorMsg().'</font>';
		 }
		 
	break;
	} //end of switch statement
}


#notes
# code for retrieving address  
/*
$deathCertInfo = obj_dethCert->getDeathCertRecord($pid);
if($deathCertInfo){
	extract($deathCertInfo);
}
*/


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Certificate of Death</title>
<style type="text/css">
<!--
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
}
.style2 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.style3 { 
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 18px;
}
body {
	background-color: #CAF0FB;
}
.style4 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style5 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 14px; }
.style7 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #990000; }
.style9 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #FFFFFF; }
-->
</style>
<script language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>

<?php
	echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\n";
	echo '<link  rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
	
	echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";

?>

<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo-min.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/assets/dpSyntaxHighlighter.js"></script>

<script type="text/javascript" src="<?=$root_path?>modules/registration_admission/certificates/js/death_cert.js"></script>


</head>

<body>
<SCRIPT LANGUAGE="JavaScript">

//function expandcontract(tbodyid,dis) {
//  document.getElementById(tbodyid).style.display = dis;
//}
</SCRIPT>
<form id="death_certificate" name="death_certificate" method="post" onsubmit="return chckForm(this);">
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#13b1de">
  <tr>
    <td bgcolor="#D9F4FD">
      <center>
        <span class="style2">Republic of the Phillippines</span><BR /> 
        <span class="style1">OFFICE OF THE CIVIL REGISTRAR GENERAL</span><BR /> 
        <span class="style3">CERTIFICATE OF DEATH </span><br />
        <span class="style2">Davao Medical Center        </span>
      </center>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" height="58" border="0" cellpadding="2" cellspacing="1">
        <tr>
          <td width="9%" bgcolor="#F9FFFB">
              <span class="style2">Province:</span>
          </td>
          <td width="63%" bgcolor="#D9F4FD">
            <input name="province" type="hidden" class="style2" id="province" value="Davao del Sur">
            	<b>Davao del Sur</b>
          </td>
          <td width="28%" rowspan="2" bgcolor="#FFEFB0">
            <span class="style2">Registry no.
            </span>
            <input name="registry_nr" id="registry_nr" type="text" class="style2" value="<?= $registry_nr ?>" onblur="trimString(this)">            
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
              <span class="style2">City/Municipality</span>
          </td>
          <td bgcolor="#D9F4FD">
            <input name="muncity" type="hidden" class="style2" id="muncity" value="Davao City" />
            	<b>Davao City</b>
          </td>
        </tr>		
		</table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="2" cellspacing="1">
        <tr>
          <td colspan="6" bgcolor="#FFEFB0" class="style5">1. Name </td>
        </tr>
        <tr>
          <td width="8%" bgcolor="#F9FFFB">
            Name            
          </td>
          <td width="20%" bgcolor="#F9FFFB">
            <input name="name_first" type="hidden" class="style2" id="name_first" value="<?= ucwords(strtolower($name_first)) ?>" />
            <b><?= ucwords(strtolower($name_first)) ?></b>
          </td>
          <td width="8%" bgcolor="#F9FFFB">
            Middle Name            
          </td>
          <td width="26%" bgcolor="#F9FFFB">
            <input name="name_middle" type="hidden" class="style2" id="name_middle" value="<?= ucwords(strtolower($name_middle)) ?>" />
            <b><?= ucwords(strtolower($name_middle)) ?></b>
          </td>
          <td width="9%" bgcolor="#F9FFFB">
            Last Name            
          </td>
          <td width="29%" bgcolor="#F9FFFB">
            <input name="name_last" type="hidden" class="style2" id="name_last" value="<?= ucwords(strtolower($name_last)) ?>" />
			<b><?= ucwords(strtolower($name_last)) ?></b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height="123" class="style2">
      <table width="100%" border="0" cellpadding="2" cellspacing="1">
        <tr>
          <td width="14%" height="117"  valign="top" bgcolor="#ABE7FA">
            <table width="100%" border="0" cellpadding="1" cellspacing="1">
              <tr>
                <td colspan="2" bgcolor="#FFEFB0" class="style5">2. Sex </td>
              </tr>
              <tr>
                <td width="37%" bgcolor="#F9FFFB"><input type="hidden" name="sex" id="sex" value="<?= $sex ?>">
                  
                  <input name="sex" id="sex" type="radio" value="radiobutton" disabled <?php if ($sex == 'm') echo "checked"; ?> />
                  Male
                </td>
                <td width="63%" bgcolor="#F9FFFB">
                  
                  <input name="sex" id="sex" type="radio" value="radiobutton" disabled <?php if ($sex =='f') echo "checked"; ?> />
                  Female
                </td>
              </tr>
            </table>
          </td>
          <td width="20%" valign="top" bgcolor="#ABE7FA">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
              <tr>
                <td bgcolor="#FFEFB0">
                  <span class="style5">3. Religion</span>
                  	<select name="d_religion" id="d_religion" >
                  <?php
				  		$religion_obj = $person_obj->getReligion();
						if (empty($religion)|| !$religion) 
							$religion=1;   # default religion, 'Not Indicated'
						while ($result=$religion_obj->FetchRow()){
							$selected='';
							if ($religion==$result['religion_nr'])
							$selected='selected';
							echo '<option value="'.$result['religion_nr'].'" '.$selected.'>'.$result['religion_name']."</option> \n";
						}
                  ?>
                  	</select>	
                  
                </td>
              </tr>
            </table>
          </td>
          <td width="13%" valign="top" bgcolor="#ABE7FA">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
              <tr>
                <td bgcolor="#FFEFB0">
                  <span class="style5">4. Age</span>
                  
                </td>
              </tr>
            </table>
          </td>
          <td width="53%" valign="top" bgcolor="#ABE7FA">
            <table width="100%" border="0" cellpadding="2" cellspacing="1">
              <tr>
                <td width="169" bgcolor="#FFEFB0">a. 1 year or above </td>
                <td width="255" bgcolor="#FFEFB0">b. under 1 year </td>
                <td width="197" bgcolor="#FFEFB0">c. under 1 day </td>
              </tr>
              <tr>
                <td height="93" bgcolor="#F9FFFB">
                  Completed Years
                  <input name="death_age" type="text" class="style2" id="death_age" maxlength="5" size="5" readonly="1">  
                  
                </td>
                <td bgcolor="#F9FFFB">
                  <table width="100%" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                      <td width="114" height="59">
                        Months
                        <input name="death_months" type="text" class="style2" id="death_months" size="5" readonly="1">
                        
                      </td>
                      <td width="84">
                        Days
                        <input name="death_days" type="text" class="style2" id="death_days" size="5" readonly="1">
                        
                      </td>
                    </tr>
                  </table>
                </td>
                <td bgcolor="#F9FFFB">
                  <table width="165" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                      <td width="37">
                        Hours
                           <input name="hours" type="text" class="style2" id="hours" size="3" <?php if($bolTime){ echo "disabled"; }else{ echo " value=\"\""; }?> />
                      </td>
                      <td width="22">
                        Min
                        <input name="minutes" type="text" class="style2" id="minutes" size="3" <?php if($bolTime){echo "disabled";}else{ echo " value=\"\""; }?> />
                        
                      </td>
                      <td width="44" class="style2">
                        Sec
                        <input name="sec" type="text" class="style2" id="sec" size="3" <?php if($bolTime){echo "disabled";}else{ echo "value=\"\""; } ?> />
                        
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="2" cellspacing="1">
        <tr>
          <td colspan="3" bgcolor="#FFEFB0"><b>5. Place of death </b></td>
        </tr>
        <tr>
          <td width="34%" bgcolor="#F9FFFB">
            Street/Baranggay <br>
            <input name="death_place_basic" id="death_place_basic" type="hidden" value="Davao Medical Center" />
            <b>DAVAO MEDICAL CENTER </b>
            
          </td>
          <td width="29%" bgcolor="#F9FFFB">City/Municipality <br>
            <input name="death_place_mun" id="death_place_mun" type="hidden" value="Davao City" />
            <b>Davao City </b>
		  </td>
          <td width="37%" bgcolor="#F9FFFB">
            Province <br>
            <input name="death_place_prov" id="death_place_prov" type="hidden" value="Davao del Sur" />
            <b>Davao del Sur</b>
            
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="0" cellspacing="2">
        <tr>
          <td width="55%" bgcolor="#ABE7FA">
            <table width="100%" border="0" cellpadding="2" cellspacing="1">
              <tr>
                <td colspan="3" bgcolor="#FFEFB0" class="style5">6. Date of Death </td>
              </tr>
              <tr>
              	<input type="hidden" name="date_birth" id="date_birth" value ="<?= @formatDate2Local($date_birth, $date_format)?>"/>
              	<td>
              		<?php
			 	        $phpfd=$date_format;
						$phpfd=str_replace("dd", "%d", strtolower($phpfd));
						$phpfd=str_replace("mm", "%m", strtolower($phpfd));
						$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
				
						if (($death_date!='0000-00-00')  && ($death_date!=""))
							$death_date = @formatDate2Local($death_date,$date_format);
						else
							$death_date='';
					
						$sDeathDate= '<input name="death_date" type="text" size="15" maxlength=10 value="'.$death_date.'"'. 
									'onFocus="this.select();"  
									id = "death_date" 
									onBlur="getAge(this); IsValidDate(this,\'MM/dd/yyyy\'); "
									onChange="getAge(this); IsValidDate(this,\'MM/dd/yyyy\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="death_date_trigger" style="cursor:pointer" >
									<font size=3>['; 			
						ob_start();
					?>
					<script type="text/javascript">
						Calendar.setup ({
								inputField : "death_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "death_date_trigger", singleClick : true, step : 1
						});
					</script>
					<?php
						$calendarSetup = ob_get_contents();
						ob_end_clean();
				
						$sDeathDate .= $calendarSetup;
						/**/
						$dfbuffer="LD_".strtr($date_format,".-/","phs");
						$sDeathDate = $sDeathDate.$$dfbuffer.']';
					?>
              	</td>
              	<td><?= $sDeathDate ?></td>
              </tr>
            </table>
          </td>
          <td width="45%" valign="top" bgcolor="#ABE7FA">
            <table width="100%" border="0" cellpadding="2" cellspacing="2">
              <tr>
                <td bgcolor="#FFEFB0">
                  <span class="style5">7. Citizenship</span><br>
                  <select name="d_citizenship" id="d_citizenship">
                  <?php
                  		$country_obj = $address_country->getAllAddress();
                  		while($result = $country_obj->FetchRow()){
                  			$selected ='';
                  			if($citizenship==$result['country_code'])
                  				$selected = 'selected';
                  			echo '<option value="'.$result['country_code'].'" '.$selected.'>'.$result['country_name']."</option> \n";	
                  		}
                  ?>
                  </select>
                  <!-- <input name="citizenship" id="citizenship" type="hidden" value="<?= ucwords(strtolower($citizenship))?>" />
      			        --> 
                  
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="0" cellspacing="2">
        <tr>
          <td colspan="3" bgcolor="#FFEFB0" class="style5">8. Residence </td>
        </tr>
        <tr>
          <td width="32%" height="30" bgcolor="#F9FFFB">
            Street/Baranggay: <br> 
			<b><?= ucwords(strtolower($street_name.", ".$brgy_row['brgy_name']))?> </b>
              <input name="residence_basic" id="residence_basic" type="hidden" class="style2" value="<?= $brgy_nr ?>"  />
            
          </td>
          <td width="42%" bgcolor="#F9FFFB">City/Municipality <br>
          	<b><?= ucwords(strtolower($brgy_row['mun_name'])) ?></b>
            <input name="residence_mun" type="hidden" class="style2" id="residence_mun" value="<?= $mun_nr ?>" />
          </td>
          <td width="26%" bgcolor="#F9FFFB">
            Province: <br>
            <b><?= ucwords(strtolower($brgy_row['prov_name'])) ?></b>
              <input name="residence_prov" type="hidden" class="style2" id="residence_prov" value="<?= $prov_nr ?>" />
            
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height="82" valign="top" class="style2">
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td width="48%" height="76" valign="top" bgcolor="#ABE7FA">
            <table width="100%" border="0" cellpadding="2" cellspacing="1">
              <tr>
                <td colspan="3" bgcolor="#FFEFB0" class="style5">9. Civil Status </td>
                <input name="civil_status" id="civil_status" type="hidden" value ="<?=$civil_status?>"/>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB">
                  
                  <input name="decease_civilstatus" type="radio" value="radiobutton" disabled <?php if($civil_status == 'single') echo "checked"; ?> />
                  Single
                </td>
                <td bgcolor="#F9FFFB">
                  
                  <input name="decease_civilstatus" type="radio" value="radiobutton" disabled <?php if($civil_status == 'widowed') echo "checked"; ?> />
                  Widowed
                </td>
                <td bgcolor="#F9FFFB">
                  
                  <input name="decease_civilstatus" type="radio" value="radiobutton" disabled <?php if($civil_status == 'unknown') echo "checked"; ?> />
                  Unknown
                </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB">
                  
                  <input name="decease_civilstatus" type="radio" value="radiobutton" disabled <?php if($civil_status == 'married') echo "checked"; ?>/>
                  Married
                </td>
                <td bgcolor="#F9FFFB">
                  
                  <input name="decease_civilstatus" type="radio" value="radiobutton" disabled <?php if($civil_status == 'others') echo "checked"; ?> />
                  Others
                </td>
                <td bgcolor="#F9FFFB">&nbsp;</td>
              </tr>
            </table>
          </td>
          <td width="52%" valign="top" bgcolor="#ABE7FA">
            <table width="100%" border="0" cellpadding="2" cellspacing="1">
              <tr>
                <td bgcolor="#FFEFB0">
                  <span class="style5">10. Occupation</span>
                  <select name = "occupation" id="occupation">
                  <?php
						$occupation_obj = $person_obj->getOccupation($occupation);
						if($occupation_obj){
							while($result=$occupation_obj->FetchRow()){
								$selected = '';
								if($occupation == $result['occupation_nr'])
									$selected = 'selected';
								echo '<option value="'.$result['occupation_nr'].'" '.$selected.'>'.$result['occupation_name']."</option> \n";                 
							}
						}
                  ?>
                  </select>
                  
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<!-- FOR AGES 0 TO 7 DAYS  -->
<table width="100%" border="0" cellpadding="2" cellspacing="1">
  <thead>
  <tr>
    <td width="12%" style="cursor: pointer; cursor: hand;">
	 	  <img src="images/ok.gif" onclick="expandcontract('Row1','')">
	     <img src="images/x.gif"onclick="expandcontract('Row1','none')">	
	 </td>
    <td width="88%" bgcolor="#FF6600">
      <center>
        <span class="style9">For Ages 0 to 7 days (Click the check button on the left side.) </span>
      </center>
    </td>
  </tr>
  </thead>
  <tbody id="Row1" style="display:none">
	 	            <!-- START FOR AGES 0 TO 7 DAYS -->
  	 <tr>
	 	<td colspan="2">
			<table width="100%" bgcolor="#6666FF" border="0" cellpadding="2" cellspacing="1">
			  <tr>  
					<td width="33%" bgcolor="#FFDDFF">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr bgcolor="#CCCCFF">
								<td colspan="3"> <span class="style5">11. DATE OF BIRTH</span> </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td align="center"> (day)  </td>
								<td align="center"> (month)  </td>
								<td align="center"> (year)  </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td align="center">
									
<?php
	if ($birthDay<10)
		$birthDay = "0".$birthDay;
	echo "									$birthDay";
?>
								</td>
								<td align="center">
<?php
	if ($birthMonth<10)
		$birthMonth = "0".$birthMonth;
	echo "									$birthMonth";
?>
								</td>
								<td align="center">
								<?= $birthYear ?>
								</td>
							</tr>
						</table>
					</td>
					<td width="25%" bgcolor="#FFDDFF">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr bgcolor="#CCCCFF">
								<td> <span class="style5">12. AGE OF THE MOTHER</span> </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td align="center">&nbsp; 	</td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td align="center"> 
					            <input name="m_age" id="m_age" type="text" size="5" onblur="trimString(this); chkInteger(this,countingNumber);" value="<?= $m_age ?>">
								</td>
							</tr>
						</table>
					</td>
					<td width="*" bgcolor="#FFDDFF">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr bgcolor="#CCCCFF">
								<td> <span class="style5">13. METHOD OF DELIVERY</span> </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td> 
									<input type="hidden" name="delivery_method"  id="delivery_method" value="<?= $delivery_method ?>">
									<input name="delivery_method_tmp"  id="delivery_method_tmp" type="radio" value="1 - Normal; spontaeneous vertex" <?php if ((!$delivery_method)||($delivery_method=="1")) echo "checked"; ?>>
                          Normal; spontaeneous vertex
								</td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td> 
		 						   <input name="delivery_method_tmp" id="delivery_method_tmp" type="radio" value="2" <?php if ($delivery_method=="2") echo "checked"; ?>>
									Others (Specify) &nbsp;
									<input type="text" name="delivery_method_info" id="delivery_method_info"  onblur="trimString(this); $('delivery_method').value= '2 - '+this.value;" value="<?php if ($delivery_method=="2") echo $delivery_method_info; ?>">
								</td>
							</tr>
						</table>
					</td>
			  </tr>
			</table>
	 	</td>
  	 </tr>
	 <tr>
	 	<td colspan="2">
			<table width="100%" bgcolor="#6666FF" border="0" cellpadding="2" cellspacing="1">
				<tr bgcolor="#FFDDFF">
					<td>
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr>
								<td bgcolor="#CCCCFF" width="30%">					
									<span class="style5">14. LENGTH OF PREGNANCY :</span>
								</td>
								<td bgcolor="#EAEAFF">					
									<input name="pregnancy_length" type="text" id="pregnancy_length" onblur="trimString(this); chkInteger(this,countingNumber);" value="<?= $pregnancy_length ?>">
									&nbsp; completed weeks
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	 </tr>
  	 <tr>
	 	<td colspan="2">
			<table width="100%" bgcolor="#6666FF" border="0" cellpadding="2" cellspacing="1">
			  <tr>  
					<td width="40%" bgcolor="#FFDDFF">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr bgcolor="#CCCCFF">
								<td> <span class="style5">15. TYPE OF BIRTH</span> </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td> 
									<input type="hidden" name="birth_type" id="birth_type" value="<?php if ($birth_type) echo $birth_type; else echo "1" ?>">
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="1" onclick="typeOfBirth(this)" <?php if (($birth_type=='1')||(!$birth_type)) echo "checked"; ?>>
									Single
									<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="2" onclick="typeOfBirth(this)" <?php if ($birth_type=='2') echo "checked"; ?>>
									Twin
									<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="3" onclick="typeOfBirth(this)" <?php if ($birth_type=='3') echo "checked"; ?>>
									Triplet, etc.
								</td>
							</tr>
						</table>
					</td>
					<td width="*" bgcolor="#FFDDFF">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr bgcolor="#CCCCFF">
								<td> <span class="style5">16. IF MULTIPLE BIRTH, CHILD WAS </span> </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td> 
									<input type="hidden" name="birth_rank" id="birth_rank" value="<?= $birth_rank ?>">
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this);" value="1" <?php if ($birth_rank=='1') echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?>>
									First
									<input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)" value="2" <?php if ($birth_rank=='2') echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?>>
									Second
									<input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)" value="" <?php if (intval($birth_rank) > 2) echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?>>
									Others (Specify) &nbsp;
									<input type="text" name="birth_rank_others" id="birth_rank_others" onblur="trimString(this); if(chkInteger(this,countingNumber)){ $('birth_rank').value=this.value; }" <?php if ($birth_rank > 2) echo 'value="'.$birth_rank.'"'; else echo "disabled"; ?>>
								</td>
							</tr>
						</table>
					</td>
			  </tr>
			</table>
	 	</td>
  	 </tr>
	 <tr>
			<!-- START FOR CAUSES OF DEATH FOR AGES 0 TO 7 DAYS -->		
	 	<td colspan="2">
			<table width="100%" bgcolor="#6666FF" border="0" cellpadding="2" cellspacing="1">
				<tr bgcolor="#FFDDFF">
					<td>
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr>
								<td bgcolor="#CCCCFF">					
									<span class="style5">17. CAUSES OF DEATH </span>
								</td>
							</tr>
							<tr>
								<td bgcolor="#EAEAFF">			
								  a. Main disease/condition of infant &nbsp;
								     <input name="cause1" type="text" size="50" value="<?= $tmp_death_cause['cause1'] ?>">
								</td>
							</tr>
							<tr>
								<td bgcolor="#EAEAFF">					
								  b. Other diseases/conditions of infant 
								  <input name="cause2" type="text" size="50" value="<?= $tmp_death_cause['cause2'] ?>">
								</td>
							</tr>
							<tr>
								<td bgcolor="#EAEAFF">					
								  c. Main maternal disease/condition of affecting infant 
								  <input name="cause3" type="type" size="50" value="<?= $tmp_death_cause['cause3'] ?>">
								</td>
							</tr>
							<tr>
								<td bgcolor="#EAEAFF">					
								  d. Other maternal disease/condition of affecting infant 
								  <input name="cause4" type="text" size="50" value="<?= $tmp_death_cause['cause4'] ?>">
								</td>
							</tr>
							<tr>
								<td bgcolor="#EAEAFF">					
								  e. Other relevant circumstances 
								  <input name="cause5" type="text" size="50" value="<?= $tmp_death_cause['cause5'] ?>">
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
			<!-- END FOR CAUSES OF DEATH FOR AGES 0 TO 7 DAYS -->		
	 </tr>
	 	            <!-- END FOR AGES 0 TO 7 DAYS -->
  </tbody>
</table>
<!-- END - FOR AGES 0 TO 7 DAYS  -->

<!-- CAUSES OF DEATH //ABOVE 7 DAYS -->
<table id="Row2" width="100%" border="0" cellpadding="2" cellspacing="2" bgcolor="#13b1de">
  <tr>
    <td height="120">
      <table width="100%" border="0" cellpadding="2" cellspacing="1">
        <tr>
          <td colspan="2" bgcolor="#FFEFB0" class="style5">17. Causes of Death </td>
        </tr>
        <tr>
          <td width="51%" bgcolor="#F9FFFB" class="style2">
            
            I.
            Immediate cause: a.
            <input name="cause6" type="text" value ="<?= $tmp_death_cause['cause6'] ?>">
          </td>
          <td width="49%" valign="top" bgcolor="#F9FFFB" class="style2">Interval Between Onset and Death<br />
            <input name="interval1" type="text" value="<?= $tmp_death_cause['interval1'] ?>">
		  </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">&nbsp;&nbsp;&nbsp; 
            
            Antecendent cause : b.
            <input name="cause7" type="text" value="<?= $tmp_death_cause['cause7'] ?>">
          </td>
          <td width="49%" vlign="top" bgcolor="#F9FFFB" class="style2">
          	<input name="interval2" type="text" value="<?= $tmp_death_cause['interval2'] ?>">
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">&nbsp;&nbsp;&nbsp;
            
            Underlying cause: c.
            <input name="cause8" type="text" value="<?= $tmp_death_cause['cause8'] ?>">
          </td>
          <td width="49%" vlign="top" bgcolor="#F9FFFB" class="style2">
          	<input name="interval3" type="text" value="<?= $tmp_death_cause['interval3'] ?>">
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">II. 
            
            Other significant conditions contributing to death
            <input name="cause9" type="text" value="<?= $tmp_death_cause['cause9'] ?>">
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!--ENDS - CAUSES OF DEATH //ABOVE 7 DAYS -->

<!-- DEATH BY NON-NATURAL CAUSES -->
<table width="100%" border="0" cellpadding="2" cellspacing="2" bgcolor="#13b1de">
  <tr>
    <td bgcolor="#FFEFB0" class="style5">18. Death by non-natural causes </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">a. Manner of Death </td>
  </tr>
  <tr>
    <td bgcolor="#CAF0FB" class="style2">
      <table width="100%" border="0" cellpadding="2" cellspacing="2">
        <tr>
          <td width="20%" bgcolor="#F9FFFB">
            
            <input name="death_manner" id="death_manner" type="hidden" value="<?php if($death_manner) echo $death_manner; ?>">
            <input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);" value="1" <?php if($death_manner_tmp=='1') echo "checked"; ?>>
            1. Homicide
          </td>
          <td width="20%" bgcolor="#F9FFFB">
            
            <input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);" value="2" <?php if($death_manner_tmp=='2') echo "checked"; ?>>
            2. Suicide
          </td>
          <td width="20%" bgcolor="#F9FFFB">
            
            <input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);" value="3" <?php if($death_manner_tmp=='3') echo "checked"; ?>>
            3. Accident
          </td>
          <td width="*" bgcolor="#F9FFFB">
            <input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);" value="4" <?php if($death_manner_tmp=='4') echo "checked"; ?>>
            Others (Specify) &nbsp;
            <input name="death_manner_info" type="text" size="40" id="death_manner_info" onblur="trimString(this); $('death_manner').value='4 - '+this.value;" <?php if($death_manner_tmp=='4') echo 'value="'.$death_manner_info.'"'; else echo "disabled" ?> >
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      b. Place of Occurence (e.g. home, farm, factory, street, sea, etc.)
      <input name="place_occurence" type="text" size="40" class="style2" id="place_occurence" value="<?php $place_occurence ?>" />
      
    </td>
  </tr>
</table>
<!--ENDS -//DEATH BY NON-NATURAL CAUSES -->

<!-- ATTENDANT -->
<table width="100%" border="0" cellpadding="2" cellspacing="2" bgcolor="#13b1de">
  <tr>
    <td colspan="2" bgcolor="#FFEFB0" class="style5">19. Attendant </td>
  </tr>
  <tr>
    <td width="41%" bgcolor="#F9FFFB" class="style2">
      <input name="attendant_type" id="attendant_type" type="hidden" value="<?php if($attendant_type) echo $attendant_type; else echo "1 - Private Physician";  ?>">
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="1" <?php if(($attendant_type == '1')|| (!$attendant_type)) echo "checked"; ?>>
      1. Private Physician
    </td>
    <td width="59%" bgcolor="#F9FFFB" class="style2">
      <input name="attendant_type_tmp" id="attendant_type_tmp"  onclick="typeOfAttendant(this)" type="radio" value="4" <?php if($attendant_type=='4'){ echo "checked";} ?>>
      4. None
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="2" <?php if($attendant_type == '2') echo "checked"; ?>>
      2. Public Health Officer
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="5" <?php if($attendant_type == '5') echo "checked"; ?>>
      5. Others (Specify)
      <input type="text" size="50" name="attendant_type_others" id="attendant_type_others" onblur="trimString(this); $('attendant_type').value= '5 - '+this.value;" <?php if ($attendant_type==5) echo 'value="'.$attendant_type_others.'"'; else echo "disabled"; ?>>      
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="3" <?php if($attendant_type == '3') echo "checked"; ?>>
      3. Hospital Authority
    </td>
    <td bgcolor="#F9FFFB" class="style2">&nbsp;</td>
  </tr>
  <tr id="if_attendant" <?php	if($attendant_type=='4') echo 'style="display:none"'; ?>> 
    <td colspan="2" bgcolor="#ABE7FA" class="style2">
      <table width="100%" border="0" cellpadding="2" cellspacing="2">
        <tr>
          <td colspan="2" bgcolor="#FFEFB0" class="style5">If attended, state duration : </td>
        </tr>
        <tr>
          <td width="8%" bgcolor="#F9FFFB">
            From            
          </td>
          <td width="92%" bgcolor="#F9FFFB">
            <input name="attended_from_date" type="text" class="style2" id="attended_from_date">
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            To
          </td>
          <td bgcolor="#F9FFFB">
            <input name="attended_to_date" type="text" class="style2" id="attended_to_date">
          </td>
        </tr>
      </table>
    </td>  
  </tr>
  
</table>
<!--ENDS //ATTENDANT -->

<!-- ATTENDANT -->
<table width="100%" border="0" cellpadding="2" cellspacing="2" bgcolor="#13b1de">
  <tr>
    <td colspan="2" bgcolor="#FFEFB0" class="style5">19. Attendant </td>
    <td rowspan="4" class="style5" id="if_attendant02"> 
      <table width="100%" border="0" cellpadding="2" cellspacing="2">
        <tr>
          <td colspan="2" bgcolor="#FFEFB0" class="style5">If attended, state duration : </td>
        </tr>
        <tr>
          <td width="8%" bgcolor="#F9FFFB">
            From            
          </td>
          <td width="92%" bgcolor="#F9FFFB">
            <input name="attended_from_date" type="text" class="style2" id="attended_from_date">
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            To
          </td>
          <td bgcolor="#F9FFFB">
            <input name="attended_to_date" type="text" class="style2" id="attended_to_date">
          </td>
        </tr>
        <tr>
          <td colspan="2"bgcolor="#F9FFFB" class="style5"> &nbsp; </td>
        </tr>
      </table>
	 </td>
  </tr>
  <tr>
    <td width="25%" bgcolor="#F9FFFB" class="style2">
      <input name="attendant_type" id="attendant_type" type="hidden" value="<?php if($attendant_type) echo $attendant_type; else echo "1 - Private Physician";  ?>">
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="1" <?php if(($attendant_type == '1')|| (!$attendant_type)) echo "checked"; ?>>
      1. Private Physician
    </td>
    <td width="45%" bgcolor="#F9FFFB" class="style2">
      <input name="attendant_type_tmp" id="attendant_type_tmp"  onclick="typeOfAttendant(this)" type="radio" value="4" <?php if($attendant_type=='4'){ echo "checked";} ?>>
      4. None
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="2" <?php if($attendant_type == '2') echo "checked"; ?>>
      2. Public Health Officer
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="5" <?php if($attendant_type == '5') echo "checked"; ?>>
      5. Others (Specify)
      <input type="text" size="50" name="attendant_type_others" id="attendant_type_others" onblur="trimString(this); $('attendant_type').value= '5 - '+this.value;" <?php if ($attendant_type==5) echo 'value="'.$attendant_type_others.'"'; else echo "disabled"; ?>>      
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="3" <?php if($attendant_type == '3') echo "checked"; ?>>
      3. Hospital Authority
    </td>
    <td bgcolor="#F9FFFB" class="style2">&nbsp;</td>
  </tr>
</table>
<!--ENDS //ATTENDANT -->

<!-- CERTIFICATION OF DEATH -->
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#13b1de">
  <!--DWLayoutTable-->
  <tr> </tr>
  <tr>
    <td valign="top">
      <table width="100%" border="0" cellpadding="2" cellspacing="1">
        <!--DWLayoutTable-->
        <tr>
          <td width="1202" bgcolor="#ABE7FA">
            <table width="100%" border="0" cellpadding="2" cellspacing="1">
              <tr>
                <td bgcolor="#FFEFB0" class="style5">20. Certification Of Death </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2">I hereby certify that the foregoing particulars are correct as near as same can be ascertained and further certify that I </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2">
                  
                    <input name="death_cert_attended_tmp" type="radio" value="radiobutton" />
                    Have not attended the deceased
                </td>
              </tr>
              <tr>
              	<td bgcolor="#F9FFFB" class="style2">
                    <input name="death_cert_attended_tmp" type="radio" value="radiobutton" />
                    Have attended the deceased and that death occured at 
                   	<input name="death_time" type="text" class="style2" id="death_time" onchange="setFormatTime(this);" value="<?= $death_time ?>" />
                	<select id="selAMPM" name="selAMPM">
                    	<option value="A.M." <?php if ($death_time_period == 'AM') echo "selected";?> >A.M.</option>
                    	<option value="P.M." <?php if ($death_time_period == 'PM') echo "selected";?> >P.M.</option>
                    </select>
                    on the date indicated above.                
	            </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td height="167" valign="top" bgcolor="#ABE7FA">
            <table width="100%" border="0" cellpadding="2" cellspacing="2">
              <tr>
                <td colspan="2" bgcolor="#F9FFFB" class="style2">Signature</td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2">
                  Name in Print                  
                </td>
                <td bgcolor="#F9FFFB" class="style2">
                  <input name="attendant_name" type="text" class="style2" id="attendant_name" value="<?= $attendant_name ?>"  />
                </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2">
                  Title or Position                  
                </td>
                <td bgcolor="#F9FFFB" class="style2">
                  <input name="attendant_title" type="text" class="style2" id="attendant_title" value="<?= $attendant_title ?>" />
                </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2">
                  Address<br />
                  
                </td>
                <td bgcolor="#F9FFFB" class="style2">
                  <textarea name="attendant_address" cols="50" class="style2" id="attendant_address" value="<?= $attendant_address ?>"></textarea>
                </td>
              </tr>
              <tr>
                <td width="7%" bgcolor="#F9FFFB" class="style2">
                  Date                  
                </td>
                <td width="93%" bgcolor="#F9FFFB" class="style2">
	                <?php
			        	$phpfd=$date_format;
						$phpfd=str_replace("dd", "%d", strtolower($phpfd));
						$phpfd=str_replace("mm", "%m", strtolower($phpfd));
						$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
					
						if (($attendant_date_sign!='0000-00-00')  && ($attendant_date_sign!=""))
							$attendant_date_sign = @formatDate2Local($attendant_date_sign,$date_format);
						else
							$attendant_date_sign='';
					
						$sAttendant_date= '<input name="attendant_date_sign" type="text" size="15" maxlength=10 value="'.$attendant_date_sign.'"'. 
									'onFocus="this.select();" 
									id = "attendant_date_sign"
									onBlur="IsValidDate(this,\''.$date_format.'\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="attendant_date_sign_trigger" style="cursor:pointer" >
									<font size=3>['; 			
						ob_start();
					?>
					<script type="text/javascript">
						Calendar.setup ({
								inputField : "attendant_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "attendant_date_sign_trigger", singleClick : true, step : 1
						});
					</script>
					<?php
						$calendarSetup = ob_get_contents();
						ob_end_clean();
					
						$sAttendant_date .= $calendarSetup;
						/**/
						$dfbuffer="LD_".strtr($date_format,".-/","phs");
						$sAttendant_date = $sAttendant_date.$$dfbuffer.']';
					?> 
					<?= $sAttendant_date ?>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!--ENDS -  //CERTIFICATION OF DEATH -->

<table width="100%" border="0" cellpadding="2" cellspacing="2" bgcolor="#13b1de">
  <tr>
    <!-- CORPSE DISPOSAL -->
    <td width="40%" height="129" valign="top" bgcolor="#ABE7FA">
      <table width="100%" border="0" cellpadding="2" cellspacing="1">
        <tr>
          <td bgcolor="#FFEFB0" class="style5">21. Corpse Disposal </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">
            
            <input type="hidden" name="corpse_disposal" id="corpse_disposal" value="<?php if($corpse_disposal) echo $corpse_disposal; else echo "1 - burial"; ?>" />
            <input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" value="1" <?php if($corpse_disposal == '1') echo "checked"; ?> />
            1. Burial
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">
            
            <input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" value="2" <?php if($corpse_disposal == '2') echo "checked"; ?> />
            2. Cremation
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">
            
            <input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" value="3" <?php if($corpse_disposal == '3') echo "checked";?> />
            3. Others
	        
			(Specify)
            <input name="corpse_disposal_others" id="corpse_disposal_others" type="text" class="style2" />
       
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">&nbsp;</td>
        </tr>
      </table>
    </td>
    <!-- ENDS - //CORPSE DISPOSAL -->
    
    <!-- BURIAL / CREMATION PERMIT -->
    <td width="30%" valign="top" bgcolor="#ABE7FA">
      
      <table width="100%" border="0" cellpadding="2" cellspacing="1">
        <tr>
          <td colspan="2" bgcolor="#FFEFB0" class="style5">22. Burial/Cremation Permit </td>
        </tr>
        <tr>
          <td width="27%" bgcolor="#F9FFFB" class="style2">
            Number
          </td>
          <td width="73%" bgcolor="#F9FFFB" class="style2">
            <input name="burial_permit" type="text" class="style2" id="burial_permit" />
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">
            Date Issued
			<?php
		        $phpfd=$date_format;
				$phpfd=str_replace("dd", "%d", strtolower($phpfd));
				$phpfd=str_replace("mm", "%m", strtolower($phpfd));
				$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		
				if (($burial_date_issued!='0000-00-00')  && ($burial_date_issued!=""))
					$burial_date_issued = @formatDate2Local($burial_date_issued,$date_format);
				else
					$burial_date_issued='';
			
				$sDateBurial= '<input name="burial_date_issued" type="text" size="15" maxlength=10 value="'.$burial_date_issued.'"'. 
							'onFocus="this.select();" 
							id = "burial_date_issued"
							onBlur="IsValidDate(this,\''.$date_format.'\'); "
							onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
							<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="burial_date_issued_trigger" style="cursor:pointer" >
							<font size=3>['; 			
				ob_start();
				?>
				<script type="text/javascript">
					Calendar.setup ({
							inputField : "burial_date_issued", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "burial_date_issued_trigger", singleClick : true, step : 1
					});
				</script>
				<?php
					$calendarSetup = ob_get_contents();
					ob_end_clean();
			
					$sDateBurial .= $calendarSetup;
					/**/
					$dfbuffer="LD_".strtr($date_format,".-/","phs");
					$sDateBurial = $sDateBurial.$$dfbuffer.']';
				?>
          </td>
          <td bgcolor="#F9FFFB" class="style2">
          	<?= $sDateBurial ?>
          </td>
        </tr>
      </table>
    </td>
	<!--ENDS - //BURIAL / CREMATION PERMIT -->    
    
    <!-- AUTOPSY -->
    <td width="30%" valign="top" bgcolor="#ABE7FA">
      <table width="100%" border="0" cellpadding="2" cellspacing="1" class="style2">
        <tr>
          <td bgcolor="#FFEFB0" class="style5">23. Autopsy </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            
            <input name="is_autopsy" type="hidden" value="<?= $is_autopsy ?>" />
            <input name="autopsy_tmp" type="radio" value="radiobutton" <?php if($is_autopsy==1) echo "checked"; ?> />
            1. Yes
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            
            <input name="autopsy_tmp" type="radio" value="radiobutton" <?php if($is_autopsy==0) echo "checked"; ?> />
            2. No
          </td>
        </tr>
      </table>
      <!--ENDS- //AUTOPSY -->
    </td>
  </tr>
</table>

<!-- NAME AND ADDRESS OF CEMETERY OR CREMATORY -->
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#13b1de">
  <tr>
    <td bgcolor="#FFEFB0">
      <label class="style2"><span class="style5">24. Name and Address of Cemetery or Crematory
      </span>
      <input name="cemetery_name_address" type="text" class="style2" id="cemetery_name_address" size="50" value="<?= $cemetery_name_address ?>" />
      
    </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#13b1de">
  <tr>
    <td colspan="2" bgcolor="#FFEFB0"><b class="style5">25. Informant</b></td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      Name in Print      
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      <input name="informant_name" type="text" class="style2" id="informant_name" value="<?= $informant_name?>" />
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      Relationshipto the deceased      
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      <input name="informant_relation" type="text" class="style2" id="informant_relation" value="<?= $informant_relation?>" />
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      Address<br />
      
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      <textarea name="informant_address" cols="50" class="style2" id="informant_address"><?= $informant_address ?></textarea>
    </td>
  </tr>
  <tr>
    <td width="9%" bgcolor="#F9FFFB" class="style2">
      Date      
 		<?php
        $phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

		if (($informant_date_sign!='0000-00-00')  && ($informant_date_sign!=""))
			$informant_date_sign = @formatDate2Local($informant_date_sign,$date_format);
		else
			$informant_date_sign='';
	
		$sDateInformant= '<input name="informant_date_sign" type="text" size="15" maxlength=10 value="'.$informant_date_sign.'"'. 
					'onFocus="this.select();" 
					id = "informant_date_sign"
					onBlur="IsValidDate(this,\''.$date_format.'\'); "
					onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
					<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="informant_date_sign_trigger" style="cursor:pointer" >
					<font size=3>['; 			
		ob_start();
		?>
		<script type="text/javascript">
			Calendar.setup ({
					inputField : "informant_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "informant_date_sign_trigger", singleClick : true, step : 1
			});
		</script>
		<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
	
			$sDateInformant .= $calendarSetup;
			/**/
			$dfbuffer="LD_".strtr($date_format,".-/","phs");
			$sDateInformant = $sDateInformant.$$dfbuffer.']';
		?>
    </td>
    <td width="91%" bgcolor="#F9FFFB" class="style2">
      <?= $sDateInformant ?>
    </td>
  </tr>
</table>
<!--ENDS - //NAME AND ADDRESS OF CEMETERY OR CREMATORY -->

<!-- PREPARED BY -->
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#13b1de">
  <tr>
    <td colspan="2" bgcolor="#FFEFB0" class="style5">26. Prepared by </td>
  </tr>
  <tr>
    <td width="9%" bgcolor="#F9FFFB" class="style2">
      Name in Print      
    </td>
    <td width="91%" bgcolor="#F9FFFB" class="style2">
      <input name="encoder_name" type="text" class="style2" id="encoder_name" value ="<?= $encoder_name ?>" />
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      Title or Position      
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      <input name="encoder_position" type="text" class="style2" id="encoder_position" value="<?= $encoder_position ?>" />
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      Address<br />
      
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      <textarea name="encoder_address" cols="50" class="style2" id="encoder_address"><?= encoder_address?></textarea>
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      Date
      <?php
        $phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

		if (($prepared_date!='0000-00-00')  && ($prepared_date!=""))
			$prepared_date = @formatDate2Local($prepared_date,$date_format);
		else
			$prepared_date='';
	
		$sDatePrepared= '<input name="encoder_date_sign" type="text" size="15" maxlength=10 value="'.$encoder_date_sign.'"'. 
					'onFocus="this.select();" 
					id = "encoder_date_sign"
					onBlur="IsValidDate(this,\''.$date_format.'\'); "
					onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
					<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="encoder_date_sign_trigger" style="cursor:pointer" >
					<font size=3>['; 			
		ob_start();
	?>
	<script type="text/javascript">
		Calendar.setup ({
				inputField : "encoder_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "encoder_date_sign_trigger", singleClick : true, step : 1
		});
	</script>
	<?php
		$calendarSetup = ob_get_contents();
		ob_end_clean();

		$sDatePrepared .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDatePrepared = $sDatePrepared.$$dfbuffer.']';
	?>
    
    </td>
    <td bgcolor="#F9FFFB" class="style2">
       <?= $sDatePrepared ?>
    </td>
  </tr>
</table>
<!--ENDS -//PREPARED BY -->

<div align="center">
<?php
			if (!$deathCertInfo || empty($deathCertInfo)){
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save">'."\n";
			}else{
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" value="Print" onClick="printBirthCert('.$pid.')">'."\n &nbsp; &nbsp;";
				echo '			<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '			<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
?>
			&nbsp; &nbsp;
			<input type="button" name="Cancel" value="Cancel"  onclick="window.close()">
</div>

</form>
<p>&nbsp;</p>
</body>
</html>