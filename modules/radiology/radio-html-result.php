<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_cert_med.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';

//create objects
include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;
include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj = new Ward;
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
require_once($root_path.'include/care_api_classes/class_social_service.php');
$objSS = new SocialService;
require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person;
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;
include_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;

extract($_GET);

//get patient data
$patient=$person_obj->getAllInfoArray($pid);
//radio result data
$radioResultObj = $radio_obj->getAllRadioInfoByBatch($batch_nr_grp,FALSE);
$radioResults = $radioResultObj->GetRows();


$patientInformation['full_name'] = mb_strtoupper($patient['name_last'] . ", " . $patient['name_first'] . " " . $patient['name_middle']);
$patientInformation['pid'] = $patient['pid'];

if ($patient['street_name']){
	$street_name = $patient['street_name'];
}else{
	$street_name = "";
}

if(mb_strtoupper(trim($patient['brgy_name'])) == 'NOT PROVIDED'){
	$brgy_name = "";
}else{
	$brgy_name = ", ". $patient['brgy_name'] .", ";
}
if (mb_strtoupper(trim($patient['mun_name']))=='NOT PROVIDED'){
	 $mun_name = "";
}
if ($patient['prov_name']!='NOT PROVIDED'){
	if(stristr(trim($patient['mun_name']), 'city') === FALSE){
			if (!empty($patient['mun_name'])){
					$province = ", ".trim($patient['prov_name']);
			}else{
					$province = trim($patient['prov_name']);;
			}
	}
}else{
		$province = "";
}

$patientInformation['address'] = $street_name . " " . $brgy_name . $patient['mun_name'] . " " . $province;
$patientInformation['gender'] = $patient['sex'] == 'm' ? 'Male' : "Female";
$patientInformation['date_birth'] = date('m/d/Y',strtotime($patient['date_birth']));
$patientInformation['age'] = $patient['age'];
$patientInformation['batch_nr'] = $batch_nr_grp;

foreach ($radioResults as $key => $radioResult) {
    $patientInformation['rid'] = $radioResult['rid'];
    $req_doc = $personell_obj->get_Person_name3($radioResult['request_doctor']);
    $row_doc = $req_doc->Fetchrow();
    $patientInformation['requesting_doctor'] = mb_strtoupper($row_doc['dr_name']);
    $patientInformation['exam_taken'] = $radioResult['service_code'];
    $patientInformation['clinical_info'] = mb_strtoupper($radioResult['clinical_info']);
    $patientInformation['department'] = $radioResult['request_dept_name'];
    $patientInformation['served_date'] = date('m/d/Y h:i A',strtotime($radioResult['served_date']));

    $encounter_type = $radioResult['encounter_type'];
    if ($encounter_type==1){
        $area='ER';
    }elseif ($encounter_type==2){
        $area='OPD';
    }elseif ($encounter_type==3){
        $area='ER - Inpatient '. $radioResult['ward_id'] ." [". $radioResult['ward_name'] ."]";
        $area="Inpatient [". $radioResult['ward_name'] ."]";
        $area=$radioResult['ward_name'];
    }elseif ($encounter_type==4){
        $area='OPD - Inpatient '. $radioResult['ward_id'] ." [". $radioResult['ward_name'] ."]";
        $area="Inpatient [". $radioResult['ward_name'] ."]";
        $area=$radioResult['ward_name'];
    }else{
        $area="WALKIN";
    }
    
    $patientInformation['area'] = mb_strtoupper($area);
    $service_dept_nr = $radioResult['service_dept_nr'];
    if ($service_dept_nr=='165'){
        #Ultrasound
        $report="Ultrasound Report";
        $rValue= "SPMC-RAD-18 ";
        $note_msg="";
    }else if ($service_dept_nr=='166'){
        #Special Procedures
        $report="Special Procedures Report";
        $rValue= "SPMC-RAD-17 ";
        $note_msg="";
    }else if ($service_dept_nr=='167'){
        #Computed Tomography
        #$report="Computed Tomography (CT) Scan Report";
        $report="Computed Tomography Scan Report";
        $rValue= "SPMC-RAD-14 ";
        $note_msg="";
    }else if ($service_dept_nr=='208'){
        #MRI
        $report="Magnetic Resonance Imaging Report";
        $rValue= "SPMC-RAD-16 ";
        $note_msg="";
    }else if ($service_dept_nr=='209'){
        #Ultrasound-OB-Gyne
        #$report="Ultrasound OB-Gyne Report";
        $report="Ultrasound Report";
        $rValue= "SPMC-RAD-18 ";
        $note_msg="";
    }else{
        #General Radiography
        $report="Roentgenological Report";
        $rValue= "SPMC-RAD-15 ";
        $note_msg="NOTE: This result is based on radiographic findings & must be correlated clinically.";
    }

    if ($radioResult['status']=='pending'){
        $result = " INITIAL READING";
        $foot_result = " / Initially Read";
    }elseif ($radioResult['status']=='done'){
        $result = " OFFICIAL READING";
        $foot_result = " / Officially Read";
    }elseif ($radioResult['status']=='referral')
        $result = " FOR REFERRAL";

    $patientInformation['report'] = mb_strtoupper($report);
    $patientInformation['rValue'] = $rValue;
    $patientInformation['note_msg'] = $note_msg;
    $patientInformation['result'] = $result;
    $patientInformation['foot_result'] = $foot_result;
    $patientInformation['service_name'] = $radioResult['service_name'];
    $findings = unserialize($radioResult['findings']);
    $patientInformation['findings'] = end($findings);    
    $impression = unserialize($radioResult['radio_impression']);
    $patientInformation['impressions'] = end($impression);
    $findings_array = $findings;
    $doctors_array = unserialize($radioResult['doctor_in_charge']);
    $doctors_final = $doctors_array[count($doctors_array)-1];

    if($radio_obj->hasBatchNR($batch_nr_grp,count($findings_array))){
        $docNR = &$radio_obj->getDoctorNR($batch_nr_grp,count($findings_array));
		  $doc_NR = $docNR->Fetchrow();

        $docs[0]=  $doc_NR['con_doctor_nr'];
        $docs[1]=  $doc_NR['sen_doctor_nr'];
        $docs[2]=  $doc_NR['jun_doctor_nr'];

        for($x=0;$x<=2;$x++){
            if($docs[$x] != ''){
                $rs_pr=$personell_obj->get_Person_name3($docs[$x]);
                while($row_pr = $rs_pr->Fetchrow()){
                    $dr_name = mb_strtoupper($row_pr['dr_name']). ", " .$row_pr['drtitle'];
                    $pos =  mb_strtoupper(trim($row_pr['job_position']));
                    $c += 1;
                    $encoding_type = mb_detect_encoding($dr_name);
                    if($encoding_type!='UTF-8')
                        $dr_name = mb_convert_encoding($dr_name, 'UTF-8',$encoding_type);

                    if($c==1){
                        $Fdoc = $dr_name;
                        $Fpos = $pos;
                    }elseif($c==2){
                        $Sdoc = $dr_name;
                        $Spos = $pos;
                    }elseif($c==3){
                        $Tdoc = $dr_name;
                        $Tpos = $pos;
                    }elseif($c==4){
                        $doc4 = $dr_name;
                        $pos4 = $pos;
                    }else{
                        $doc5 = $dr_name;
                        $pos5 = $pos;
                    }
                }
            }
        }
    }else{
        $Fdoc = mb_strtoupper(mb_convert_encoding($doctors_final, "ISO-8859-1", 'UTF-8'));
    }
    $signatories = array(
        array($Fdoc,$Fpos),
        array($Sdoc,$Spos),
        array($Tdoc,$Tpos),
        array($doc4,$pos4),
        array($doc5,$pos5)
    );
    $radtech = $personell_obj->get_Person_name3($radioResult['rad_tech']);
    if ($radtech) {
    	$radtech_row = $radtech->FetchRow();
    }
    $patientInformation['served_by'] = mb_strtoupper($radtech_row['dr_name']);
    $patientInformation['findings_encoder'] = $radioResult['findings_encoder'];

    // added by carriane 11/23/17
	// added checking if saved encoder is login id
	$encoder = $personell_obj->getUserFullName($radioResult['findings_encoder']);

	if($encoder != false)
		$patientInformation['findings_encoder'] = $encoder;
	// end carriane

    $findings_date_array = unserialize($radioResult['findings_date']);
    $findings_date_final = $findings_date_array[count($findings_date_array) - 1];
    $patientInformation['findings_date_final'] = $findings_date_final;
    $final_signatory = "";
    
    $final_signatory = "<table style='font-family:arial; font-size:10pt;'>";
    //get all signatory names

    $final_signatory .= "<tr>";
    $final_signatory_position .= "<tr>";
    foreach ($signatories as $key => $data) {
    	if(trim($data[0]) == '') continue;
    	$slash = ($key != 0) ? "/" : "";
		$final_signatory .= "<td>" . $slash . " " . trim($data[0],", ") . "<td>";
		$final_signatory_position .= "<td>" . $data[1] . "<td>";	
    }
    $final_signatory_position .= "</tr>";
    $final_signatory .= "</tr>";
	 $final_signatory .= $final_signatory_position;

    $final_signatory .= "</table>";
    $patientInformation['final_signatory'] = trim($final_signatory," / ");
    //echo "<pre>".print_r($findings[count($findings) - 1],true)."</pre>";
}

//------------------------------------------------------------------------

?>
<style type="text/css">
body {
background: #FFFFE0;
margin:15px;
}
.seg{
border:2px solid #282832;
font-family: arial;
height:52px;
padding-bottom: 3px;
}
.footer{
font-family: arial;
font-size:7pt;	
padding-top: 10px;
float:right;
}
.div-right{
font-family: arial;
width: 23%;
float:right;
font-size:10pt;
}
.div-Left{
font-family: arial;
width: 77%;
float:left;
font-size:10pt;
}

</style>
<html>
<head>
	<title>Radiology Results</title>
</head>
<body>
	<hr>

	<div>
		<table style="width:100%;">
			<tr>
				<td align="center">
					<img src="../../gui/img/logos/dmc_logo.jpg" border='1' height='110' width='100'>
				</td>
				<td align="center" style="font-family:arial; padding-top: 5px; font-size:11pt; padding-bottom:7px;" >
					<i>Republic of the Philippines</i><br>
					<i>Department of Health</i><br>
					<strong>Southern Philippines Medical Center</strong><br>
					<i>J.P. Laurel Bajada, Davao City</i><br><br>
					<strong>Department of Radiological & Imaging Sciences</strong>
				</td>
				<td align="center">
					<img src="../../modules/radiology/images/rad_logo.jpg" border='1' height='110' width='100'>
				</td>
			</tr>
		</table>
	</div>
	<div class="seg" >
		<div style="float:Left; width: 74%;">
			<table style="width:98%">
				<tr style="float:Left; font-family: arial; font-size:10pt; ">
					<td>
				<div>
					<span> Patient: 
						<strong><?= $patientInformation['full_name'] ?></strong>
					</span>
				</div>
				<div>	
					<span> Address: 
						<?= $patientInformation['address']?>
					</span>
				</div>
				<div>
					<span> Sex:
						<?= $patientInformation['gender'] ?></span>
					<span style="padding-left: 20px">Birthdate:
						<?= $patientInformation['date_birth'] ?>
					</span>	
					<span style="padding-left: 20px">Age:
						<?= $patientInformation['age'] . ' old'?>
					</span>	
				</div>
					</td>			
				</tr>
			</table>			
		</div>

		<div style="float:Right; width: 26%;">
			<table style="width:100%">
				<tr style="float:right; font-family: arial; font-size:10pt; ">
					<td>
				<div>
					<span><strong> HRN:
						<?= $patientInformation['pid']?></strong>
					</span>
				</div>
				<div>	
					<span> RID: 
						<strong><?= $patientInformation['rid'] ?></strong>
					</span>
				</div>
				<div>
					<span> BN: 
						<strong><?= $patientInformation['batch_nr'] ?></strong>
					</span>
				</div>
					</td>			
				</tr>
			</table>				
		</div>

		<div class="div-left">
			<span> Requesting Doctor : 
				<?= $patientInformation['requesting_doctor']  ?>
			</span>
		</div>
		<div class="div-right">
			<span>Exam Taken: 
				<?= $patientInformation['exam_taken']?>
			</span>
		</div>
		<div class="div-left">
			<span> Clinical Indication/Impression: 
				<?= $patientInformation['clinical_info'] ?>
			</span>
		</div>
		<div class="div-right">
			<span> Dept: 
				<?= $patientInformation['department'] ?>
			</span>
		</div>
		<div class="div-left">
			<span> Date/Time of Examination :
				<?= $patientInformation['served_date'] ?></span>
		</div>
		<div class="div-right">
			<span> Area: 
				<?= $patientInformation['area'] ?>
			</span>
		</div>
	</div>	
	
	<div>
		<div align="center" style="font-family:arial; margin-top:100px">
			<?php
				echo "<strong style='font-size: 14pt;'>" . $patientInformation['report'] .
				 "<br>" . $patientInformation['result'] . "</strong><br><span style='font-size: 9pt'>". $patientInformation['note_msg'] ."</span>";
			?>
		</div>
		<div style="font-family:arial; margin-top:20px; font-size:10pt;">
				<span> <strong>
					<?= mb_strtoupper($patientInformation['service_name']) ?></strong>
				</span><br>
				<span>
					<?=strtoupper('Findings : ')?>
				<p style="font-size: 9pt"><br>
					<?= $patientInformation['findings']?>
				</p>
				</span>
		</div>

		<div style="font-family:arial; margin-top:20px; font-size:10pt;">
			<span>
				<?=strtoupper('Impressions : ')?>
			<strong><p style="font-size: 10pt">
				<?= $patientInformation['impressions'] ?>
			</p></strong>
			</span>
		</div>
		<div>
				<?= $patientInformation['final_signatory']; ?>
		</div>
				
		<div >
		<table style="width:99%">
			<tr style="float:right;">
				<td><span style="font-family: arial; font-size:7pt;">					
					<?php 
						echo "Served by: " . $patientInformation['served_by'].", RRT" . "<br>";
						echo "Result Encoded by: " . $patientInformation['findings_encoder']. "<br>";
						echo "Date Encoded" . $patientInformation['foot_result'] .": ". date('m/d/Y',strtotime($patientInformation['findings_date_final'])). "<br>";
					?>				
				</span></td>			
			</tr>
		</table>
		</div>
		
	</div>
	<hr>
		<div style="font-size:8pt; font-family:arial;">
			<span style="width:11%; float:right;"><strong>
				<?= mb_strtoupper($patientInformation['rValue']) ?></strong>
			</span>
			<span style="width:45%; float:left;">
					Effectivity : October 1, 2013
			</span>
			<span style="width:31%;">
					Revision : 0
			</span>
		</div>
</body>
</html>