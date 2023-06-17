<?php 
	require_once("./roots.php");
	require_once($root_path.'include/inc_environment_global.php');
	require($root_path."modules/registration_admission/ajax/affidavit.common.php");
	include_once($root_path.'include/care_api_classes/class_affidavit_father_surename.php');
	include_once($root_path.'include/care_api_classes/class_address.php');
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
?>

    <script type="text/javascript" src="<?= $root_path ?>js/gen_routines.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/datefuncs.js"></script>

    <link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
    <script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
    <script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
    <script type="text/javascript"
            src="<?= $root_path ?>js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js"></script>
    <script type="text/javascript"
            src="<?= $root_path ?>js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js"></script>
    <script type="text/javascript">var $j = jQuery.noConflict();</script>
    <script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery.numeric.js?t=<?= time() ?>"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/mustache.js"></script>

<?php
	global  $db;
	define(DAVAO_CITY, 24);
	if(isset($_GET['pid'])) {
		$pid = $_GET['pid'];
	}

	$objHospitalInfo = new Hospital_Admin;
	$address_country = new Address('country');
	// $address_muncity = new Address('municity');
	$obj_affidavit = new AffidavitFatherSurename($pid);
	$personell = new Personell;

	$row = $objHospitalInfo->getAllHospitalInfo(); # event place default to the location of hospital
	$display = 'display:none';
	$user_session = $_SESSION['sess_user_name'];
	$created_dt = date('Y-m-d H:i:s');
	
							
	$curTme  = strftime("%Y-%m-%d %H:%M:%S");
	$curDate = strftime("%b %d, %Y %I:%M%p", strtotime($curTme));

	if(isset($_POST['mode'])) {
		$sample = trim($_POST['admission_of_paternity_barangay']).", ".trim($_POST['admission_of_paternity_city']).", ".trim($_POST['admission_of_paternity_prob']).", ".trim($_POST['admission_of_paternity_country']);
		
		switch ($_POST['mode']) {
			case 'save':
				$_POST['child_birth_date'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['child_birth_date']));
				$_POST['child_birth_reg_date'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['child_birth_reg_date']));
				$_POST['paternity_reg_date'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['paternity_reg_date']));
				$_POST['date_ausf_cert'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['date_ausf_cert']));
				$_POST['administer_date'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($administer_date));
				$_POST['paternity_reg_place'] = $sample;
				$_POST['affiant_fname'] = strtoupper($_POST['affiant_fname']);
				$_POST['affiant_lname'] = strtoupper($_POST['affiant_lname']);
				$_POST['affiant_mname'] = strtoupper($_POST['affiant_mname']);
				$_POST['affiant_address'] = strtoupper($_POST['affiant_address_basic'].','.$_POST['affiant_address_barangay'].','.$_POST['affiant_address_city'].','.$_POST['affiant_address_prob'].','.$_POST['affiant_address_country']);
				$_POST['affiant_status'] = strtoupper($_POST['affiant_status']);
				$_POST['father_surename'] = strtoupper($_POST['father_surename']);

				$_POST['paternity_reg_num'] = strtoupper($_POST['paternity_reg_num']);
				$_POST['administer_place'] = strtoupper($_POST['administer_place']);

				$_POST['aff_street'] = strtoupper($_POST['affiant_address_basic']);
				$_POST['aff_brgy'] = strtoupper($_POST['affiant_address_barangay']);
				$_POST['aff_city'] = strtoupper($_POST['affiant_address_city']);
				$_POST['province_lcro_cert'] = strtoupper($_POST['affiant_address_prob']); // Province of affiant
				$_POST['aff_country'] = strtoupper($_POST['affiant_address_country']);

				if (isset($_POST['is_self']) && !isset($_POST['is_other'])) {
					$_POST['is_other'] = 0;
					$_POST['is_self'] = 1;
				}

				if (isset($_POST['is_other']) && !isset($_POST['is_self'])) {
					$_POST['is_self'] = 0;
					$_POST['is_other'] = 1;
				}

				$obj_affidavit->saveNewAffidavitFatherSurename($_POST, $user_session, $created_dt);
				$display = 'display:block';
				break;
			case 'update':
				$_POST['child_birth_date'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['child_birth_date']));
				$_POST['child_birth_reg_date'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['child_birth_reg_date']));
				$_POST['paternity_reg_date'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['paternity_reg_date']));
				$_POST['date_ausf_cert'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['date_ausf_cert']));
				$_POST['administer_date'] = strftime("%Y-%m-%d %H:%M:%S", strtotime($administer_date));
				$_POST['paternity_reg_place'] = $sample;
				$_POST['affiant_fname'] = strtoupper($_POST['affiant_fname']);
				$_POST['affiant_lname'] = strtoupper($_POST['affiant_lname']);
				$_POST['affiant_mname'] = strtoupper($_POST['affiant_mname']);
				$_POST['affiant_address'] = strtoupper($_POST['affiant_address_basic'].','.$_POST['affiant_address_barangay'].','.$_POST['affiant_address_city'].','.$_POST['affiant_address_prob'].','.$_POST['affiant_address_country']);
				$_POST['affiant_status'] = strtoupper($_POST['affiant_status']);
				$_POST['father_surename'] = strtoupper($_POST['father_surename']);

				$_POST['paternity_reg_num'] = strtoupper($_POST['paternity_reg_num']);
				$_POST['administer_place'] = strtoupper($_POST['administer_place']);

				$_POST['aff_street'] = strtoupper($_POST['affiant_address_basic']);
				$_POST['aff_brgy'] = strtoupper($_POST['affiant_address_barangay']);
				$_POST['aff_city'] = strtoupper($_POST['affiant_address_city']);
				$_POST['province_lcro_cert'] = strtoupper($_POST['affiant_address_prob']); // Province of affiant
				$_POST['aff_country'] = strtoupper($_POST['affiant_address_country']);

				if (isset($_POST['is_self']) && !isset($_POST['is_other'])) {
					$_POST['is_other'] = 0;
					$_POST['is_self'] = 1;
				}

				if (isset($_POST['is_other']) && !isset($_POST['is_self'])) {
					$_POST['is_self'] = 0;
					$_POST['is_other'] = 1;
				}

				if($obj_affidavit->updateAffidavitFatherSurname($_POST, $user_session)){
					$error = false;
				} else {
					$error = true;
				}
				$display = 'display:block';
				break;
		}
	}

	$recentInfo = $obj_affidavit->getRecentData($_POST['pid']);
	include_once($root_path.'include/care_api_classes/class_person.php');
	$objPerson = new Person($pid);
	$name_f = $objPerson->Mother_FirstName();
	$name_m = $objPerson->Mother_MiddleName();
	$name_l = $objPerson->Mother_LastName();
	$child_fn = $objPerson->LastName() . ", " . $objPerson->FirstName() . " " . $objPerson->MiddleName();
	$addressInfo = explode(',', $recentInfo['affiant_address']);
	$addressInfo_strt =  $recentInfo['aff_street'];
	$addressInfo_brgy =  $recentInfo['aff_brgy'];
	$addressInfo_city =  $recentInfo['aff_city'];
	$addressInfo_prov =  $recentInfo['province_lcro_cert'];
	$addressInfo_country =  $recentInfo['aff_country'];
	$addressInfo_paternity = explode(',', $recentInfo['paternity_reg_place']);
	$child_municity_nr = $recentInfo['child_birth_mun_cty'];
	$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

	$baby_info = $objPerson->getAllInfoArray($pid);

	if ($baby_info) {
		$b_gender = $baby_info['sex'];
	}

	// ADDED by: JEFF @ 07-23-17
	// updated by carriane 03/26/18
	//if (!$recentInfo) {

		$sql_affadd="SELECT
				  cb.`m_residence_basic`, 
				  sb.`brgy_name`,
				  sm.`mun_name`,
				  sm.`mun_nr`,
				  sc.`country_name`,
				  sp.`prov_name`,
  				  cb.`m_age`,
  				  cb.`f_name_last`,
  				  cb.m_name_first,
  				  cb.m_name_middle,
  				  cb.m_name_last,
  				  cb.m_citizenship,
  				  cb.m_age
				FROM
				  `seg_cert_birth` AS cb 
				  LEFT JOIN `seg_municity` AS sm 
				    ON cb.`m_residence_mun` = sm.`mun_nr` 
				  LEFT JOIN `seg_barangays` AS sb 
				    ON cb.`m_residence_brgy` = sb.`brgy_nr`
				  LEFT JOIN `seg_country` AS sc 
				    ON cb.`m_residence_country` = sc.`country_code` 
				  LEFT JOIN `seg_provinces` AS sp 
    				ON cb.`m_residence_prov` = sp.`prov_nr`  
				WHERE cb.`pid` =".$db->qstr($pid);

		$res_affadd = $db->Execute($sql_affadd);

		if ($res_affadd){
			while($row = $res_affadd->FetchRow()){
				$res_basic = $row['m_residence_basic'];
				$res_brgy   = $row['brgy_name'];
				$res_mun   = $row['mun_name'];
				$res_prov   = $row['prov_name'];
				$res_country = $row['country_name'];
				$municity_nr = $row['mun_nr'];
				$m_name_first = $row['m_name_first'];
				$m_name_middle = $row['m_name_middle'];
				$m_name_last = $row['m_name_last'];
				$m_age = $row['m_age'];
				$affiant_citizenship = $row['m_citizenship'];
				$f_surname = $row['f_name_last'];
				if(!$recentInfo){
					$aff_birth_age = $row['m_age'];
					
				}
			}
		}
	/*}
	else{
		$municity_nr = $child_municity_nr;
		$res_basic = $addressInfo_strt; 
		$res_brgy = $addressInfo_brgy;
    	$res_mun  = $addressInfo_city;
    	$res_prov   = $addressInfo_prov;
    	$res_country  = $addressInfo_country;
	}*/
	// END of Fetching Address

	$checker_other = '';
	$checker_self = '';
	$subject_other = $recentInfo['is_other'];
	$subject_self = $recentInfo['is_self'];

	if ($subject_other != '1'){
		$checker_other = '';
	}else{
		$checker_other = 'checked';
	}

	if ($subject_self != '1'){
		$checker_self = '';
	}else{
		$checker_self = 'checked';
	}
?>
<!-- Modified by JEFF 11-26-17  -->
<!DOCTYPE html>
<html>
<head>
	<title>Affidavit to Use the Surname of the Father</title>
	<link rel="stylesheet" type="text/css" href="<?php echo($root_path.'css/bootstrap/bootstrap1.css') ?>">
</head>
<body onload="preset()">
	<br>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<?php 
				if($error)  {
					$msg = '<center><h5>Error On Updating!</h5></center>';
					echo '<div style="'.$display.'" class="alert alert-danger">'.$msg.'</div>';	
				} else {
					$msg = '<center><h5>Successfully Updated!</h5></center>';
					echo '<div style="'.$display.'" class="alert alert-success">'.$msg.'</div>';
				}
			 ?>
			<div class="panel panel-warning">
				<div class="panel-heading">
					<h1 class="panel-title font-balance"><center>AFFIDAVIT TO USE THE SURNAME OF THE FATHER</center></h1>
				</div>
				<div class="panel-body">
					<form 
						id="affidavitForm"
						name="affidavitForm" 
						action="" 
						method="POST" 
						onsubmit="return processForm()"
					> 
					<div class="col-md-12 noting">
						<h6>Note: Fields with ( * ) is required.</h6>
					</div>
					<div class="row">
						<br>
						<h4 class="text-primary font-balance"><center>Affiant's Information</center></h4>

						<div class="form-group col-md-4">
							<input type="text" 
								name="affiant_fname" 
								id="affiant_fname" 
								class="form-control"
								placeholder="Firstname"
								value="<?php echo (isset($m_name_first)) ? $m_name_first : $name_f; ?>"
							>
							<center><label>Affiant's First Name<span class="important-color">*</span></label></center>
						</div>
						<div class="form-group col-md-4">
							<input type="text" 
								name="affiant_mname"
								id="affiant_mname" 
								class="form-control"
								placeholder="Middlename"
								value="<?php echo (isset($m_name_middle)) ? $m_name_middle : $name_m; ?>" 
							>
							<center><label>Affiant's Middle Name<span class="important-color">*</span></label></center>
						</div>
						<div class="form-group col-md-4">
							<input type="text" 
								name="affiant_lname"
								id="affiant_lname" 
								class="form-control"
								placeholder="Lastname"
								value="<?php echo (isset($m_name_last)) ? $m_name_last : $name_l; ?>" 
							>
							<center><label>Affiant's Last Name<span class="important-color">*</span></label></center>
						</div>
					</div>
					<div class="row">
						<br>
						<?php 
							// $affiant_citizenship = 'FILIPINO';
						 ?>
						<div class="form-group col-md-4">
							<input type="text" 
								name="affiant_citizenship"
								id="affiant_citizenship" 
								class="form-control"
								placeholder="Citizenship"
								value="<?php echo (isset($affiant_citizenship)) ? strtoupper($affiant_citizenship) : ' '; ?>" 
							>
							<center><label>Citizenship<span class="important-color">*</span></label></center>
						</div>
						<div class="form-group col-md-4">
							<select class="form-control" name="affiant_status" id="affiant_status">
								<option 
									<?php if($affiant_status == 'Single') { ?>
									selected="true" 
									<?php }; ?>
									value="Single"
								>
									Single
								</option>

								<option 
									<?php if($affiant_status == 'Married') { ?>
									selected="true" 
									<?php }; ?>
									value="Married"
								>
									Married
								</option>

								<option 
									<?php if($affiant_status == 'Divorced') { ?>
									selected="true" 
									<?php }; ?>
									value="Divorced"
								>
									Divorced
								</option>

								<option 
									<?php if($affiant_status == 'Widowed') { ?>
									selected="true" 
									<?php }; ?>
									value="Widowed"
								>
									Widowed
								</option>

								<option 
									<?php if($affiant_status == 'Separated') { ?>
									selected="true" 
									<?php }; ?>
									value="Separated"
								>
									Separated
								</option>
							</select>
							<center><label>Civil Status<span class="important-color">*</span></label></center>
						</div>
						<div class="form-group col-md-4 aff_age">
							<input type="text" 
								name="affiant_age"
								id="affiant_age" 
								class="form-control"
								value="<?php echo (isset($m_age)) ? $m_age : $aff_birth_age; ?>"
								onblur="numberOnly()"
							>
							<center><label>Age<span class="important-color">*</span></label></center>
						</div>
					</div>
					<hr>
					<!-- Start of AFFIANT'S ADDRESS -->
					<div class="row">
						<br>
						<center class="text-primary font-balance">Affiant Address</center>
						<br>
						<!-- Modified by JEFF 07-11-17 and Modified again @ 07-24-17-->
						<div class="form-group col-md-4">
							<input
								type="text"
								name="affiant_address_basic"
								id="affiant_address_basic" 
								class="form-control"
								value="<?php echo $res_basic; ?>"
							>
							<center><label>Street<span class="important-color">*</span></label></center>
						</div>
						<div class="form-group col-md-4">
							<input
								type="text"
								name="affiant_address_barangay"
								id="affiant_address_barangay" 
								class="form-control"
								value="<?php echo $res_brgy; ?>"
							>
							
							<center><label>Barangay<span class="important-color">*</span></label></center>
						</div>
						<!-- Modified by JEFF 07-11-17 -->
						<div class="form-group col-md-4">
							<input
								type="text"
								name="affiant_address_city"
								id="affiant_address_city" 
								class="form-control"
								value="<?php echo $res_mun;?>"
							>
							
							<center><label>Mun/City<span class="important-color">*</span></label></center>
						</div>
						<input
								type="hidden"
								name="affiant_address_country"
								id="affiant_address_country" 
								value="<?php echo $res_country;?>"
							>
						<input
								type="hidden"
								name="affiant_address_prob"
								id="affiant_address_prob" 
								value="<?php echo $res_prov;?>"
							>
					</div>
					<!-- End of AFFIANT ADDRESS -->
					<hr>
					<div class="row">
						<br>
						<h4 class="text-primary"><center class="font-balance">Purpose</center></h4>
						<div class="form-group col-md-6 col-md-offset-3">
							<input type="text" 
								name="father_surename"
								id="father_surename"
								class="form-control"
								placeholder="Surname of Father"
								value="<?php echo strtoupper($f_surname); ?>"
							>
							<center><label>Surname of the Father<span class="important-color">*</span></label></center>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-6">
							<?php
								if($is_self) {
									$checked = 'checked';
								} else {
									$checked = '';
								}
							 ?>
							<div class="checkbox">
								<label>
							      <input 
							      	type="checkbox" 
							      	id="is_self" 
							      	name="is_self"
							      	onclick="affiantsPurpose(<?php echo $pid;?>)"
							      	value="1"
							      	<?php echo $checker_self; ?>
							      	<?php echo $checked; ?>
						      	  />
							      <b>My Certificate of Live Birth/Report of Birth</b>
							    </label>
							</div>
						</div>
						<div class="form-group col-md-6">
							<?php
								if($is_other) {
									$checked = 'checked';
								} else {
									$checked = '';
								}
							 ?>
							<div class="checkbox">
								<label>
							      <input 
							      	type="checkbox" 
							      	id="is_other" 
							      	name="is_other"
							      	onclick="affiantsPurpose(<?php echo $pid;?>)"
							      	value="1"
							      	<?php echo $checker_other; ?>
							      	<?php echo $checked; ?>
							      />
							      <b>The Certificate of Live Birth/Report of Birth of</b>
							    </label>
							</div>
						</div>
					</div>
					<div id="other" style="display: none;" class="row">
						<div class="form-group col-md-6">
							<input type="text" 
								name="child_fullname"
								id="child_fullname"
								class="form-control"
								value="<?php echo (isset($child_fullname)) ? strtoupper($child_fullname) : ' '; ?>" 
							>
							<center><label>Subject Fullname<span class="important-color">*</span></label></center>
						</div>
						<div class="form-group col-md-6">
							<?php 
								$sql = 'SELECT * FROM seg_social_relationships WHERE id IN (3,15)';
								$res = $db->Execute($sql);
							 ?>
								<select 
									name="child_relationship"
									id="child_relationship"
									class="form-control"	
								>

								<?php 

									if(empty($child_relationship) || !$child_relationship) {
										// $child_relationship = (int) 3;
										if ($b_gender == "f") {
											$child_relationship = (int) 15;
										}
										else{
											$child_relationship = (int) 3;
										}
									}

									while ( $rel = $res->FetchRow()) {

										$selected = '';
		                                if ($child_relationship == $rel['id']){
		                                	$selected = 'selected';
		                                }
		                                echo '<option value="'.$rel['id'].'" '.$selected.'>'.strtoupper($rel['name'])."</option> \n";
									}
								 ?>

								</select>
							<center><label>Subject Relation<span class="important-color">*</span></label></center>
							
						</div>
					</div>
					<hr>
					<br>
					<!-- Start of Child's information -->
					<div class="row">
						<br>
						<h4 class="text-primary"><center class="font-balance">Child</center></h4>
						<div class="form-group col-md-4">
							<input type="text" 
								name="child_birth_date"
								id="child_birth_date" 
								class="form-control"
								size = "40"
								value="<?php echo (isset($recentInfo['child_birth_date'])) ? strftime("%Y-%m-%d", strtotime($recentInfo['child_birth_date'])) : ' '; ?>" 
							>
							<center><label>Date of Birth<span class="important-color">*</span></label></center>
						</div>



						<div class="form-group col-md-4">
							
							<?php $mun_obj = $address_country->getMunicipal(); ?>

							<select 
								class="form-control"
								id="child_birth_mun_cty"
								name="child_birth_mun_cty"
							>
								<?php
		                            while ($result = $mun_obj->FetchRow())
		                            {
		                                $selected = '';
		                                if (DAVAO_CITY == $result['mun_nr'])
			                                {
			                                	$selected = 'selected';
			                                }
		                                echo '<option value="'.$result['mun_nr'].'" '.$selected.'>'.$result['mun_name']."</option> \n";
	                                }
								 ?>
							</select>
							<center><label>City/Municipality<span class="important-color">*</span></label></center>
						</div>
						<div class="form-group col-md-4">
							<select
								class="form-control"
								id="child_birth_country"
								name="child_birth_country"
							>
								<?php                   
                                    $country_obj = $address_country->getCountry();

                                    if (empty($child_birth_country)|| !$child_birth_country){
                                        $child_birth_country = 'PH';
                                    }
                                    while ($result = $country_obj->FetchRow()){

                                        $selected = '';

                                        if ($child_birth_country == $result['country_code']){

                                        	$selected = 'selected';
                                        }
                                        echo '<option value="'.$result['country_code'].'" '.$selected.'>'.$result['country_name']."</option> \n";
                                    }
                                ?>
							</select>
							<center><label>Country<span class="important-color">*</span></label></center>
						</div>
							<div class="form-group col-md-3">
								<input type="hidden" 
									name="child_bdate"
									id="child_bdate" 
									class="segInput"
									size = "1"
								>
							</div>		
						<div class="col-md-8 col-md-offset-2">
								<div class="form-group col-md-6">
									<input 
										type="hidden" 
										name="reg_date"
										id="reg_date" 
										class="segInput"
									>
								</div>
						</div>
					</div>
					<!-- End of Child's Information -->

					<!-- Start of Exihibiting Documents -->
					<hr>
					<div class="row">
						<h4 class="text-primary"><center class="font-balance">Exhibiting Document</center></h4>
						<br>
						<div class="form-group col-md-4 col-md-offset-2">
							<center>
								<input
								type="text" 
								name="paternity_reg_num" 
								id="paternity_reg_num" 
								class="form-control" 
								placeholder="Exhibiting ID number"
								value="<?php echo (isset($recentInfo['paternity_reg_num'])) ? strtoupper($recentInfo['paternity_reg_num']) : ''; ?>">
								<label>ID Number<span class="important-color">*</span></label>
							</center>
						</div>
						<div class="form-group col-md-4 ">
							<center>
								<input 
								type="text" 
								name="administer_place" 
								id="administer_place" 
								class="form-control"
								placeholder="Exhibiting City"
								value="<?php echo (isset($recentInfo['administer_place'])) ? strtoupper($recentInfo['administer_place']) : ''; ?>">
								<label>Mun/City<span class="important-color">*</span></label>
							</center>
						</div>
					</div>
					<hr>
					<!-- End of Exhibiting Documents -->

					<div class="col-md-12 noting">
							<h6>Note: After any changes please click SUBMIT or UPDATE before printing.</h6>
					</div>
					<hr>
					<hr>
					<br>
				<?php 
					if(!$recentInfo) {
						echo '<input type="hidden" name="mode" id="mode" value="save">';
						echo '<input type="submit" name="submit" class="btn btn-primary" value="Submit" style="height:40px; width:260px;">';
					} else {
						echo '<input type="hidden" name="mode" id="mode" value="update">';
						echo '<input
								type="button" 
								name="print" 
								class="btn btn-info" 
								value="Print" 
								onclick="print_affidavit(\''.$pid.'\')"
								style="height:40px; width:130px;"
							  >&nbsp;';
						echo '<input type="submit" name="submit" class="btn btn-success" value="Update" style="height:40px; width:130px;">';
					}
				 ?>
				</form>
					<button onclick="window.close()" class="btn btn-danger"  style="height:40px; width:260px;">Cancel</button>
				</div>
				
			</div>
		</div>
	</div>
	<script type="text/javascript">
	// name_first
	var n_f = "<?php echo $name_f; ?>";
	//name_middle
	var n_m = "<?php echo $name_m; ?>";
	//name_last
	var n_l = "<?php echo $name_l; ?>";
	//full_name
	// var name_full = n_l + ", " + n_f + " " + n_m;
	name_full = encodeURI("<?php echo $child_fn; ?>");
	</script>
	<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>
	<script 
		src="
		<?php echo($root_path.'modules/registration_admission/js/affidavit_father_surename.js'); ?>
		"
	>	
	</script>
	<script type="text/javascript">
		var p_url = "<?php echo $root_path.'modules/reports/reports'; ?>";
	</script>
</body>
</html>