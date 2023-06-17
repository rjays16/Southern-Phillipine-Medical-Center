<?php 
	include("roots.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/care_api_classes/class_encounter.php');
	require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_med_cert.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	require_once($root_path.'include/care_api_classes/class_department.php');
	global $HTTP_SESSION_VARS;
	global $db;
	$enc_obj =new Encounter;
	$obj_medCert = new SegICCertMed;
	$dept_obj =new Department;
	$pers_obj =new Personell;
	
	$encounter_nr = ($_GET['encounter_nr'] !='' ? $_GET['encounter_nr'] : $_POST['encounter_nr']);
	$refno = ($_GET['refno'] !='' ? $_GET['refno'] : $_POST['refno']);


	if($encounter_nr){
		if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
				echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
				exit();
			}
	}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
			exit();
	}

	if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
	}



/*-----------saving--------------*/

	if (isset($_POST['mode'])) {
		$trans_date = $trans['trxn_date'];
		switch ($_POST['mode']) {
			case 'save':
					$data = array(
							'remarks'=>$_POST['remarks'],
							'dr_nr_med'=>$_POST['dr_nr_med'],
							'dr_nr_dental'=>$_POST['dr_nr_dental'],
							'medcert_date'=>date('Y-m-d H:i:s'),
							'transaction_date'=>date('Y-m-d H:i:s', strtotime($trans_date)),
							'with_medical'=>$_POST['with_medical'],
							'with_dental'=>$_POST['with_dental'],
							'history'=>"Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
							'create_dt'=>date('Y-m-d H:i:s'),
							'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
							'modify_dt'=>date('Y-m-d H:i:s'),
							'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
							'clinic_num'=>$encounter_nr,
							'medical_findings'=>$_POST['medical_findings'],
							'dental_findings'=>$_POST['dental_findings'],
							'with_optha'=>$_POST['with_optha'],
							'with_ent'=>$_POST['with_ent'],
							'optha_findings'=>$_POST['optha_findings'],
							'ent_findings'=>$_POST['ent_findings'],
							'dr_nr_optha'=>$_POST['dr_nr_optha'],
							'dr_nr_ent'=>$_POST['dr_nr_ent'],
							'refno'=>$refno
							);

					$rs = $obj_medCert->saveMedcert($data,'save');
					$rs2 = $obj_medCert->save_medcert_reccomendation($refno,$encounter_nr,$_POST['reccommendation']);
					if($rs){
						$Msg='<div class="" id="notif" style="color:#555;
                            border-radius:10px;
                            font-family:Tahoma,Geneva,Arial,sans-serif;font-size:30px;
                            padding:10px 10px 10px 36px;
                            margin:10px;
                            background:#e9ffd9;
                            border:1px solid #a6ca8a;">&#10004;<span>success: </span>Data Saved</div>';
                		#echo $Msg;
					}else{
						$Msg='<div class="" id="notif" style="color:#555;
                            border-radius:10px;
                            font-family:Tahoma,Geneva,Arial,sans-serif;font-size:30px;
                            padding:10px 10px 10px 36px;
                            margin:10px;
                            background:#e9ffd9;
                            border:1px solid #a6ca8a;">&#10004;<span>success: </span>Failed to Saved</div>';
                			#echo $Msg;
					}
				break;
			
			default:
					$cert_nr = $obj_medCert->getCertNr($refno,'seg_industrial_cert_med');
					$data = array(
									'remarks'=>$_POST['remarks'],
									'dr_nr_med'=>$_POST['dr_nr_med'],
									'dr_nr_dental'=>$_POST['dr_nr_dental'],
									'medcert_date'=>date('Y-m-d H:i:s'),
									'transaction_date'=>date('Y-m-d H:i:s', strtotime($trans_date)),
									'with_medical'=>$_POST['with_medical'],
									'with_dental'=>$_POST['with_dental'],
									'history'=>"Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
									'modify_dt'=>date('Y-m-d H:i:s'),
									'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
									'clinic_num'=>$encounter_nr,
									'medical_findings'=>$_POST['medical_findings'],
									'dental_findings'=>$_POST['dental_findings'],
									'with_optha'=>$_POST['with_optha'],
									'with_ent'=>$_POST['with_ent'],
									'optha_findings'=>$_POST['optha_findings'],
									'ent_findings'=>$_POST['ent_findings'],
									'dr_nr_optha'=>$_POST['dr_nr_optha'],
									'dr_nr_ent'=>$_POST['dr_nr_ent'],
									'refno'=>$refno

								);
						$rs = $obj_medCert->saveMedcert($data,'update');
						$rs2 = $obj_medCert->save_medcert_reccomendation($refno,$encounter_nr,$_POST['reccommendation']);
						if ($rs){
							$Msg='<div class="" id="notif" style="color:#555;
                            border-radius:10px;
                            font-family:Tahoma,Geneva,Arial,sans-serif;font-size:30px;
                            padding:10px 10px 10px 36px;
                            margin:10px;
                            background:#e9ffd9;
                            border:1px solid #a6ca8a;">&#10004;<span>success: </span>Data Updated</div>';
                			#echo $Msg;
						}else{
							$Msg='<div class="" id="notif" style="color:#555;
                            border-radius:10px;
                            font-family:Tahoma,Geneva,Arial,sans-serif;font-size:30px;
                            padding:10px 10px 10px 36px;
                            margin:10px;
                            background:#e9ffd9;
                            border:1px solid #a6ca8a;">&#10004;<span>success: </span>Failed to Update</div>';
                			#echo $Msg;	
						}
				break;
		}
	}
/*------------------------------*/

	$name = stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle'])).' '.stripslashes(strtoupper($encInfo['name_last']));
	$sex = ($encInfo['sex'] == 'f' ? 'Female' : 'Male');
	$case = $encInfo['encounter_nr'];
	$age = floor((time() - strtotime($encInfo['date_birth']))/31556926).' yrs old';
	$hrn = $encInfo['pid'];

	$medcertInfo = $obj_medCert->getAllInfoCertMed($refno);

	$listDoctors=array();
		if ($encInfo['current_dept_nr'])
			$dept_nr = $encInfo['current_dept_nr'];
		else
			$dept_nr = $encInfo['consulting_dept_nr'];
				 $doctors = $pers_obj->getDoctors(1);
		if (is_object($doctors)){
			while($drInfo=$doctors->FetchRow()){
					$doctitle=$pers_obj->get_Person_name($drInfo['personell_nr']);
					$middleInitial = "";
					if (trim($drInfo['name_middle'])!=""){
							$thisMI=split(" ",$drInfo['name_middle']);
							foreach($thisMI as $value){
									if (!trim($value)=="")
											$middleInitial .= $value[0];
							}
							if (trim($middleInitial)!="")
									$middleInitial .= ". ";
					}
					$name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial;
					$name_doctor = ucwords(strtolower($name_doctor)).", ".$doctitle['drtitle'];
					$listDoctors[$drInfo["personell_nr"]]=$name_doctor;
			}
	 }

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>MEDICAL CERTIFICATE</title>
	<link rel="stylesheet" href="css/med-cert.css">
	<?php echo '<script type="text/javascript" src="'.$root_path.'js/jquery/jquery-1.8.2.js"></script>'."\n";  
    echo '<script type="text/javascript" src="js/med-cert2.js"></script>';?>

</head>
<body>
	<?php echo $Msg; ?>
	<div class="divstlye" id="div">
		<fieldset>
			<ul class="two-col-special">
			    <li>Name : <?php echo $name ;?></li>
			    <li>Sex : <?php echo $sex ;?></li>
			    <li>age : <?php echo $age ;?></li>
			    <li>Case # : <?php echo $case ;?></li>
			    <li>Hospital # : <?php echo $hrn ;?></li>
			</ul>
		</fieldset>
	</div>
	<span id="notif"></span>
	<div class="details" id="div">
			<form id="cert_med" name="cert_med" method="post" action="">
				<fieldset><legend>MEDICAL / DENTAL CERTIFICATE</legend>
					<fieldset>
						<div class="divstlye" id="div">
							<input id="with_dental" name="with_dental" type="checkbox" value="1" <?php echo ($medcertInfo['with_dental'] == 1? 'checked' : '');?>>Dental<br>
							<input type="hidden" id="with_dental2" value="<?php echo ($medcertInfo['with_dental'] == 1? 'checked' : '');?>">
							<div id="dentaldiv">	
								<table>
									<tr>
										<td class="style1">Dental Findings:</td>

										<td>
											<textarea name="dental_findings" class="textbox" id="dental_findings" cols="40" rows="5" wrap="physical"><?php echo $medcertInfo['dental_findings']?></textarea>
											<select name="dr_nr_dental" id="dr_nr_dental" class="textbox">
												<option value='0'>-Select a doctor-</option>
												<?php
													$listDoctors = array_unique($listDoctors);
													if (empty($medCertInfo['dr_nr_dental']))
														$medCertInfo['dr_nr_dental'] = 0;
													foreach($listDoctors as $key=>$value){
														if ($medcertInfo['dr_nr_dental']==$key){
															echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
														}else{
															echo "				<option value='".$key."'>".$value."</option> \n";
														}
													}
												?>
											</select>
										<!--Added by Borj 2014-09-08 font size-->
										<!-- <tr>
										<td align="left" id="dental" name="dental">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										Font Size:
										</td>

										<td>
										
										<select id="font_sizedental" name="font_sizedental" class="textbox">
											<option value="12" >12</option>
											<option value="8" >8</option>
											<option value="5" >5</option>
										</select>
										</td>
										</tr> -->
										
										</td>
									</tr>
								</table>
							</div>
						</div>
					</fieldset>
					
					<fieldset>
						<div class="divstlye" id="div">
							<input id="with_optha" name="with_optha" type="checkbox" value ="1" <?php echo ($medcertInfo['with_optha'] == 1? 'checked' : '');?>>Opthalmology<br>
							<div id="opthadiv">
								<table>
									<tr>
										<td class="style1">Opthalmology Findings:</td>
										<td>
											<textarea name="optha_findings"  class="textbox" id="optha_findings" cols="40" rows="5"><?php echo $medcertInfo['optha_findings']?></textarea>
											<select name="dr_nr_optha" id="dr_nr_optha" class="textbox">
												<option value='0'>-Select a doctor-</option>
												<?php
													$listDoctors = array_unique($listDoctors);
													if (empty($medCertInfo['dr_nr_optha']))
														$medCertInfo['dr_nr_optha'] = 0;
													foreach($listDoctors as $key=>$value){
														if ($medcertInfo['dr_nr_optha']==$key){
															echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
														}else{
															echo "				<option value='".$key."'>".$value."</option> \n";
														}
													}
												?>
											</select>
											<!--Added by Borj 2014-09-08 font size-->
										<!-- <tr>
										<td align="left" id="dental" name="dental">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										Font Size:
										</td>
										<td>
										<select id="font_sizeoptha" name="font_sizeoptha" class="textbox">
											<option value="12" >12</option>
											<option value="8" >8</option>
											<option value="5" >5</option>
										</select>
										</td>
										</tr> -->

										</td>
									</tr>
								</table>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="divstlye" id="div">
							<input id="with_ent" name="with_ent" type="checkbox" value ="1" <?php echo ($medcertInfo['with_ent'] == 1? 'checked' : '');?>>ENT<br>
							<div id="entdiv">	
								<table>
									<tr>
										<td class="style1">ENT Findings:</td>
										<td>
											<textarea name="ent_findings"  class="textbox" id="ent_findings" cols="40" rows="5"><?php echo $medcertInfo['ent_findings']?></textarea>
											<select name="dr_nr_ent" id="dr_nr_ent" class="textbox">
											<option value='0'>-Select a doctor-</option>
												<?php
													$listDoctors = array_unique($listDoctors);
													if (empty($medCertInfo['dr_nr_ent']))
														$medCertInfo['dr_nr_ent'] = 0;
													foreach($listDoctors as $key=>$value){
														if ($medcertInfo['dr_nr_ent']==$key){
															echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
														}else{
															echo "				<option value='".$key."'>".$value."</option> \n";
														}
													}
												?>
											</select>
											<!--Added by Borj 2014-09-08 font size-->
										<!-- <tr>
										<td align="left" id="dental" name="dental">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										Font Size:
										</td>
										<td>
										<select id="font_sizedent" name="font_sizedent" class="textbox">
											<option value="12" >12</option>
											<option value="8" >8</option>
											<option value="5" >5</option>
										</select>
										</td>
										</tr> -->

										</td>
									</tr>
								</table>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="divstlye" id="div">
							<input id="with_medical" name="with_medical" type="checkbox" value ="1" <?php echo ($medcertInfo['with_medical'] == 1? 'checked' : '');?>>Medical<br>
							<div id="medicaldiv">
								<table>
									<tr>
										<td class="style1">Medical Findings:</td>
										<td>
											<textarea name="medical_findings"  class="textbox" id="medical_findings" cols="40" rows="5"><?php echo $medcertInfo['medical_findings']?></textarea>
											<select name="dr_nr_med" id="dr_nr_med" class="textbox">
												<option value='0'>-Select a doctor-</option>
												<?php
													$listDoctors = array_unique($listDoctors);
													if (empty($medCertInfo['dr_nr_med']))
														$medCertInfo['dr_nr_med'] = 0;
													foreach($listDoctors as $key=>$value){
														if ($medcertInfo['dr_nr_med']==$key){
															echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
														}else{
															echo "				<option value='".$key."'>".$value."</option> \n";
														}
													}
												?>
											</select>
											<!--Added by Borj 2014-09-08 font size-->
										<!-- <tr>
										<td align="left" id="dental" name="dental">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										Font Size:
										</td>
										<td>
										<select id="font_sizemed" name="font_sizemed" class="textbox">
											<option value="12" >12</option>
											<option value="8" >8</option>
											<option value="5" >5</option>
										</select>
										</td>
										</tr> -->

										</td>
									</tr>
								</table>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="divstlye" id="div">
							<table>
								<tr>
									<td class="style1">Remarks/Recommendation: </td>
									<td><textarea name="remarks" id="remarks" class="textbox" id="note" cols="40" rows="5"><?php echo $medcertInfo['remarks']; ?></textarea></td>
								</tr>
							</table>
						</div>
					</fieldset>
					<fieldset>
						<div class="divstlye" id="div">
							<ul>
								<li><input type="radio" name="reccommendation" value="1" <?php echo $medcertInfo['recommendation'] == 1?'checked':''; ?>><span>Class A</span></li>
								<li><input type="radio" name="reccommendation" value="2" <?php echo $medcertInfo['recommendation'] == 2?'checked':''; ?>><span>Class B</span></li>
								<li><input type="radio" name="reccommendation" value="3" <?php echo $medcertInfo['recommendation'] == 3?'checked':''; ?>><span>Class C</span></li>
								<li><input type="radio" name="reccommendation" value="4" <?php echo $medcertInfo['recommendation'] == 4?'checked':''; ?>><span>Class D</span></li>
								<li><input type="radio" name="reccommendation" value="5" <?php echo $medcertInfo['recommendation'] == 5?'checked':''; ?>><span>Pending, for further evaluation</span></li>
							</ul> 
						</div>
					</fieldset>
										<!--Added by Borj 2014-09-08 font size-->
										<tr>
										<td align="align" id="dental" name="dental">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										Font Size:
										</td>
										<td>
										<select id="font_sizerem"  name="font_sizerem" class="textbox">
											<option value="12" >12</option>
											<option value="8" >8</option>
											<option value="5" >5</option>
										</select>
										</td>
										</tr>
										<!--end-->

					<div class="divstlye" id="savediv" align="center">
						<a href="#" class="myButton" id="save">SAVE</a>
						<a href="#" class="myButton" id="update">UPDATE</a>
						<a class="myButton" id="print">PRINT</a>
						<?php 
							if (!$medcertInfo || empty($medcertInfo)){
									echo '<input type="hidden" name="mode" id="mode" value="save">'."\n";
							}else{
									echo '<input type="hidden" name="mode" id="mode" value="update">'."\n";
							}
							echo '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
							
						 ?>
					</div>
				</fieldset>
			</form>
	</div>

</body>
</html>