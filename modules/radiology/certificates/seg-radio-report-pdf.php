<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

define('ULTRASOUND','165');
define('GENERALRADIOGRAPHY','164');
define('SPECIALPROCEDURES','166');
define('COMPUTEDTOMOGRAPHY','167');
define('MRI','208');
define('ULTRASOUND-OB-GYNE','209');
//define('MAMO','225'); #added by: syboy 08/11/2015
define('MAMO', '235'); #added by: gelie 09/11/2015
// added by carriane 03/16/18
define('IPBMIPD_enc', 13);
define('IPBMOPD_enc', 14);
// end carriane

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj = new Encounter;

include_once($root_path.'include/care_api_classes/class_cert_med.php');
$med_obj = new MedCertificate;

require_once $root_path.'include/care_api_classes/class_hospital_admin.php';


if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}


if (isset($_GET['batch_nr_grp']) && $_GET['batch_nr_grp']){
	$batch_nr = $_GET['batch_nr_grp'];
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;

if ($pid){
	if (!($basicInfo=$person_obj->getAllInfoArray($pid))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}

	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid HRN!</em>';
	exit();
}
			# burn added : March 26, 2007
			if($date_birth){
				$segBdate = @formatDate2Local($date_birth,$date_format);
				if (!($age = $person_obj->getAge($segBdate))){
					$age = '';
					$segBdate = 'Not Available';
					$segBdateAge = $segBdate;
				}else{
					$age=$age.' year(s) old';
					$segBdateAge = $segBdate.'   / '.$age;
				}
			}
	if ($sex=='f'){
		$gender = "female";
	}else if($sex=='m'){
		$gender = "male";
	}

		 if ($street_name)
				$street_name = "$street_name ";
		else
				$street_name = "";

		if ($brgy_name=='NOT PROVIDED')
			 $brgy_name = "";
		if (!($brgy_name))
				$brgy_name = "";
		else
				$brgy_name = ", ".$brgy_name.", ";
		if ($mun_name=='NOT PROVIDED')
			 $mun_name = "";

	if ($prov_name!='NOT PROVIDED'){
		if(stristr(trim($mun_name), 'city') === FALSE){
				if (!empty($mun_name)){
						$province = ", ".trim($prov_name);
				}else{
						$province = trim($prov_name);;
				}
		}
	}else{
			$province = "";
	}

		$sAddress = trim($street_name)." ".trim($brgy_name).trim($mun_name)." ".$province;

# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

include_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;


#added by: mark 02/07/2016 
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_pacs_parse_hl7_message.php');
$parseObj = new seg_parse_msg_HL7();

	$result_Status = $radio_obj->getHL7MsgByBatch($batch_nr);

		if ($result_Status['status'] !="done" && $result_Status['is_served'] =='1') {
			 $resultdata = $radio_obj->getHL7Msg($batch_nr);
			 $message = $resultdata['hl7_msg'];
			 $segments = explode($parseObj->delimiter, trim($message));
			 foreach($segments as $segment) {
			 	 $data = explode('|', trim($segment));
			 	 if (in_array("OBR", $data)) {
                        $obr = $parseObj->segment_obr($data);
                    }
			 }
			if ($obr['result_status']=='F') 
				  $true = $radio_obj->updateStatus($obr['location']);	
		}
#end added by MARK	
if ($batch_nr){
	if (!($radioResultObj = $radio_obj->getAllRadioInfoByBatch($batch_nr,FALSE))){
		#echo "seg-radio-findings-select-batchNr.php : radio_obj->sql = '".$radio_obj->sql."' <br> \n";

		echo '<em class="warn"> Cannot continue to display the page! <br> \n NO Result(s) found.</em>';
		exit();
	}
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Batch Number!</em>';
	exit();
}

while($radioResultInfo = $radioResultObj->FetchRow()){
	//print_r($radioResultInfo);

	extract($radioResultInfo);


	if ($encounter_type==1){
		$area='ER';
	}elseif ($encounter_type==2){
		$area='OPD';
	}elseif ($encounter_type==3){
		$area='ER - Inpatient '.$ward_id." [".$ward_name."]";
		$area="Inpatient [".$ward_name."]";
		$area=$ward_name;
	}elseif ($encounter_type==4){
		$area='OPD - Inpatient '.$ward_id." [".$ward_name."]";
		$area="Inpatient [".$ward_name."]";
		$area=$ward_name;
	}elseif ($encounter_type==6){ //added by Macoy July 8, 2014
		$area='Industrial Clinic';
	}elseif($encounter_type==IPBMOPD_enc){
		$area='IPBM - OPD';
	}elseif($encounter_type==IPBMIPD_enc){
		$area=$ward_name;
	}else{
		$area="WALKIN";
	}

	$seg_request_date = 'No Date Requested indicated';
	if($request_date && ($request_date!='0000-00-00')){
		$seg_request_date = @formatDate2Local($request_date,$date_format);
	}

	$findings_array = unserialize($findings);
	$findings = $findings_array[count($findings_array)-1];

	$findings_final = utf8_decode($findings_array[count($findings_array)-1]);

	$radio_impression_array = unserialize($radio_impression);
	$radio_impression_final = $radio_impression_array[count($radio_impression_array)-1];

	#added by VAn 10-17-08
	$doctors_array = unserialize($doctor_in_charge);

	$doctors_final = $doctors_array[count($doctors_array)-1];
	#edited by VAN 04-28-2011

	// $findings_date_array = unserialize($findings_date);
	// if (!count($findings_date_array))
	// 	$index = count($findings_date_array);
	// else
	// 	$index = count($findings_date_array)-1;

	// #$seg_service_date = 'No Date Service indicated';
	// if($service_date && ($service_date!='0000-00-00')){
	// 	$seg_service_date = @formatDate2Local($service_date,$date_format);
	// #$service_date = @formatDate2Local($service_date,$date_format);
	// }else{
	// 	$findings_date_final = $findings_date_array[$index];
	// 	#$findings_date_final = $findings_date_array[0];
	// 	if ($findings_date_final)
	// 		$findings_date_final = @formatDate2Local($findings_date_final,$date_format);

	// 	#added by VAN 04-28-2011
	// 	if($findings_date_final && ($findings_date_final!='0000-00-00'))
	// 		$seg_service_date = $findings_date_final;
	// }


	#Added by KENTOOT 06/24/2014 -----------------------------------
		$findings_date_array = unserialize($findings_date);
		if (!count($findings_date_array))
			$index = count($findings_date_array);
		else
			$index = count($findings_date_array)-1;

		#$seg_service_date = 'No Date Service indicated';
		if($service_date && ($service_date!='0000-00-00')){
				$seg_service_date = @formatDate2Local($service_date,$date_format);

		
			//-------------------added by KENTOOT 05/22/2014
			$findings_date_final = $findings_date_array[$index];
			#$findings_date_final = $findings_date_array[0];
			if ($findings_date_final)
				$findings_date_final = @formatDate2Local($findings_date_final,$date_format);
			//------------------------------------end KENTOOT


		}else{

			$findings_date_final = $findings_date_array[$index];
			#$findings_date_final = $findings_date_array[0];

			if ($findings_date_final)
				$findings_date_final = @formatDate2Local($findings_date_final,$date_format);
			else
				$findings_date_final	= 'No Date Service indicated';

			#added by VAN 04-28-2011
			if($findings_date_final && ($findings_date_final!='0000-00-00'))
				$seg_service_date = $findings_date_final;
		}
	#end KENTOOT -----------------------------------------------------



	$doctor_in_charge_array = unserialize($doctor_in_charge);
	$doctor_in_charge_final = $doctor_in_charge_array[count($doctor_in_charge_array)-1];
	#$doctor_in_charge_final = $doctor_in_charge_array[0];

	if ($doctor_in_charge_final){

		if ($reportingDoctorInfo = $personell_obj->getPersonellInfo($doctor_in_charge_final)){
			$doctor_in_charge_name = trim($reportingDoctorInfo['name_first']);
			if (!empty($reportingDoctorInfo['name_middle'])){
				$doctor_in_charge_name .= ' '.substr(trim($reportingDoctorInfo['name_middle']),0,1).'.';
			}
			if (!empty($reportingDoctorInfo['name_last'])){
				$doctor_in_charge_name .= ' '.trim($reportingDoctorInfo['name_last']);
			}
			$doctor_in_charge_name = trim($doctor_in_charge_name.', MD');
		}
	}

	if (!empty($grant_no)){
		$or_no_final = "CHARITY";
		$amount_paid = "0.00";
	}elseif (!empty($or_no)){
		if (floatval($amount_or) > floatval($price_net)){
			$or_no_final = $or_no.' (Subsidized)';
			$amount_paid = $price_net;
		}else{
			$or_no_final = $or_no;
			$amount_paid = $amount_or;
		}
	}else{
		$or_no_final = 'Subsidized';
		$amount_paid = $price_net;
	}


	if(($served_date) && ($served_date!='0000-00-00 00:00:00')){
		$served_date = date("m/d/Y h:i A" , strtotime($served_date));
	}else{
		$served_date = "";
	}

	$req_doc = $personell_obj->get_Person_name3($request_doctor);
	if($req_doc){
		while ( $row_doc = $req_doc->Fetchrow() ) {
			$request_doctor_name = mb_strtoupper($row_doc['dr_name']);
		}
	}

	// added by carriane 10/20/17
	// added checking if saved encoder is login id
	$encoder = $personell_obj->getUserFullName($findings_encoder);

	if($encoder != false)
		$findings_encoder = $encoder;
	// end carriane

	$batchNrArrayInfo[$batch_nr]['service_code'] = $service_code;
	$batchNrArrayInfo[$batch_nr]['request_doctor_name'] = $request_doctor_name;
	$batchNrArrayInfo[$batch_nr]['request_dept_name'] = $request_dept_name;
	$batchNrArrayInfo[$batch_nr]['or_no_final'] = $or_no_final;
	$batchNrArrayInfo[$batch_nr]['amount_paid'] = $amount_paid;

	$batchNrArrayInfo[$batch_nr]['seg_request_date'] = $seg_request_date;
	$batchNrArrayInfo[$batch_nr]['seg_service_date'] = $seg_service_date;
	$batchNrArrayInfo[$batch_nr]['batch_nr'] = $batch_nr;
	$batchNrArrayInfo[$batch_nr]['service_name'] = $service_name;

	$batchNrArrayInfo[$batch_nr]['findings_final'] = $findings_final;
	$batchNrArrayInfo[$batch_nr]['radio_impression_final'] = $radio_impression_final;
	$batchNrArrayInfo[$batch_nr]['findings_date_final'] = $findings_date_final;
	$batchNrArrayInfo[$batch_nr]['doctor_in_charge_final'] = $doctor_in_charge_final;
	$batchNrArrayInfo[$batch_nr]['doctor_in_charge_name'] = $doctor_in_charge_name;
	$batchNrArrayInfo[$batch_nr]['group_code'] = $group_code; # added by: syboy 08/03/2015

	$batchNrArrayInfo[$batch_nr]['findings_encoder'] = $findings_encoder;
}#end of while loop 'while($radioResultInfo = $radioResultObj->FetchRow())'

foreach($batchNrArrayInfo as $batchNrInfo){
# echo "seg-radio-findings-select-batchNr.php : batchNrInfo : <br> \n"; var_dump($batchNrInfo); echo" <br> \n";
}

require_once($root_path.'classes/fpdf/fpdf.php');
function hex2dec($couleur = "#000000"){
		$R = substr($couleur, 1, 2);
		$rouge = hexdec($R);
		$V = substr($couleur, 3, 2);
		$vert = hexdec($V);
		$B = substr($couleur, 5, 2);
		$bleu = hexdec($B);
		$tbl_couleur = array();
		$tbl_couleur['R']=$rouge;
		$tbl_couleur['G']=$vert;
		$tbl_couleur['B']=$bleu;
		return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm($px){
		return $px*25.4/72;
}

function txtentities($html){
		$trans = get_html_translation_table(HTML_ENTITIES);
		$trans = array_flip($trans);
		return strtr($html, $trans);
}
//require_once($root_path.'modules/repgen/html2pdf.php');
class PDF extends FPDF{
	/*
	*	Page footer
	*	override the method in FPDF (the implementation in FPDF is empty)
	*/
	#added by art 02/12/2014
	var $iso;
	function setIso($value){
		$this->iso = $value;
	}
	function getIso(){
		return $this->iso;
	}
	#end art

	var $effectivity;
	function setEffectivity($value){
		$this->effectivity = $value;
	}
	function getEffectivity(){
		return $this->effectivity;
	}

	function Footer(){

		#edited by KENTOOT 06/02/2014
		$this->setY(-20);
		#added by art 02/12/2014
		$this->SetLineWidth(0.2);
		$this->Line($this->getX(), $this->getY(), 203, $this->getY());
       	$this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 4, $this->getIso(), "", 1, 'R');
        $this->SetFont('Arial', '', 8);
        $this->Cell(60, 8, $this->getEffectivity(), 0, 0, 'L');
        $this->Cell(80, 8, 'Revision : 0', 0, 0, 'C');
        $this->Cell(50, 8, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'R');
		#end art
	}

		var $B;
		var $I;
		var $U;
		var $HREF;
		var $fontList;
		var $issetfont;
		var $issetcolor;

		function PDF($orientation='P',$unit='mm',$format=array(215.9,330.2))
		{
				//Call parent constructor
				$this->FPDF($orientation,$unit,$format);

				$this->SetMargins(10, 8, 10, true);
   				//$this->SetAutoPageBreak(TRUE, 20);

				//Initialization
				$this->B=0;
				$this->I=0;
				$this->U=0;
				$this->HREF='';

				$this->tableborder=0;
				$this->tdbegin=false;
				$this->tdwidth=0;
				$this->tdheight=0;
				$this->tdalign="L";
				$this->tdbgcolor=false;

				$this->oldx=0;
				$this->oldy=0;

				$this->fontlist=array("arial","times","courier","helvetica","symbol");
				$this->issetfont=false;
				$this->issetcolor=false;
		}

		//////////////////////////////////////
		//html parser

		function WriteHTML($html)
		{
				$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><hr><td><tr><table><sup>"); //remove all unsupported tags
				$html=str_replace("\n",'',$html); //replace carriage returns by spaces
				$html=str_replace("\t",'',$html); //replace carriage returns by spaces
				$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //explodes the string
				foreach($a as $i=>$e)
				{

					if($this->GetY() >= 275) $this->AddPage(); //added by KENTOOT 06/27/2014

						if($i%2==0)
						{
								//Text
								if($this->HREF)
										$this->PutLink($this->HREF,$e);
								elseif($this->tdbegin) {
										if(trim($e)!='' and $e!="&nbsp;") {
												$this->Cell($this->tdwidth,$this->tdheight,$e,$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
										}
										elseif($e=="&nbsp;") {
												$this->Cell($this->tdwidth,$this->tdheight,'',$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
										}
								}
								else {
									$this->Write(5,stripslashes(txtentities($e)));
                                }    
						}
						else
						{
								//Tag
								if($e{0}=='/')
										$this->CloseTag(strtoupper(substr($e,1)));
								else
								{
										//Extract attributes
										$a2=explode(' ',$e);
										$tag=strtoupper(array_shift($a2));
										$attr=array();
										foreach($a2 as $v)
												if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
														$attr[strtoupper($a3[1])]=$a3[2];
										$this->OpenTag($tag,$attr);
								}
						}
				}
		}

		function OpenTag($tag,$attr)
		{
				//Opening tag
				switch($tag){

						case 'SUP':
								if($attr['SUP'] != '') {
										//Set current font to: Bold, 6pt
										$this->SetFont('','',6);
										//Start 125cm plus width of cell to the right of left margin
										//Superscript "1"
										$this->Cell(2,2,$attr['SUP'],0,0,'L');
								}
								break;

						case 'TABLE': // TABLE-BEGIN
								if( $attr['BORDER'] != '' ) $this->tableborder=$attr['BORDER'];
								else $this->tableborder=0;
								break;
						case 'TR': //TR-BEGIN
								break;
						case 'TD': // TD-BEGIN
								if( $attr['WIDTH'] != '' ) $this->tdwidth=($attr['WIDTH']/4);
								else $this->tdwidth=40; // SET to your own width if you need bigger fixed cells
								if( $attr['HEIGHT'] != '') $this->tdheight=($attr['HEIGHT']/6);
								else $this->tdheight=6; // SET to your own height if you need bigger fixed cells
								if( $attr['ALIGN'] != '' ) {
										$align=$attr['ALIGN'];
										if($align=="LEFT") $this->tdalign="L";
										if($align=="CENTER") $this->tdalign="C";
										if($align=="RIGHT") $this->tdalign="R";
								}
								else $this->tdalign="L"; // SET to your own
								if( $attr['BGCOLOR'] != '' ) {
										$coul=hex2dec($attr['BGCOLOR']);
												$this->SetFillColor($coul['R'],$coul['G'],$coul['B']);
												$this->tdbgcolor=true;
										}
								$this->tdbegin=true;
								break;

						case 'HR':
								if( $attr['WIDTH'] != '' )
										$Width = $attr['WIDTH'];
								else
										$Width = $this->w - $this->lMargin-$this->rMargin;
								$x = $this->GetX();
								$y = $this->GetY();
								$this->SetLineWidth(0.2);
								$this->Line($x,$y,$x+$Width,$y);
								$this->SetLineWidth(0.2);
								$this->Ln(1);
								break;
						case 'STRONG':
								$this->SetStyle('B',true);
								break;
						case 'EM':
								$this->SetStyle('I',true);
								break;
						case 'B':
						case 'I':
						case 'U':
								$this->SetStyle($tag,true);
								break;
						case 'A':
								$this->HREF=$attr['HREF'];
								break;
						case 'IMG':
								if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
										if(!isset($attr['WIDTH']))
												$attr['WIDTH'] = 0;
										if(!isset($attr['HEIGHT']))
												$attr['HEIGHT'] = 0;
										$this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
								}
								break;
						//case 'TR':
						case 'BLOCKQUOTE':
						case 'BR':
								$this->Ln(5);
								break;
						case 'P':
								$this->Ln(10);
								break;
						case 'FONT':
								if (isset($attr['COLOR']) and $attr['COLOR']!='') {
										$coul=hex2dec($attr['COLOR']);
										$this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
										$this->issetcolor=true;
								}
								if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
										$this->SetFont(strtolower($attr['FACE']));
										$this->issetfont=true;
								}
								if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist) and isset($attr['SIZE']) and $attr['SIZE']!='') {
                                    if ($attr['SIZE']<=0) {
                                        $attr['SIZE'] = $fontSizeText;                    
                                    }
									$this->SetFont(strtolower($attr['FACE']),'',$attr['SIZE']);
									$this->issetfont=true;
								}
								break;
				}
		}

		function CloseTag($tag)
		{
				//Closing tag
				if($tag=='SUP') {
				}

				if($tag=='TD') { // TD-END
						$this->tdbegin=false;
						$this->tdwidth=0;
						$this->tdheight=0;
						$this->tdalign="L";
						$this->tdbgcolor=false;
				}
				if($tag=='TR') { // TR-END
						$this->Ln();
				}
				if($tag=='TABLE') { // TABLE-END
						//$this->Ln();
						$this->tableborder=0;
				}

				if($tag=='STRONG')
						$tag='B';
				if($tag=='EM')
						$tag='I';
				if($tag=='B' or $tag=='I' or $tag=='U')
						$this->SetStyle($tag,false);
				if($tag=='A')
						$this->HREF='';
				if($tag=='FONT'){
						if ($this->issetcolor==true) {
								$this->SetTextColor(0);
						}
						if ($this->issetfont) {
								$this->SetFont('arial');
								$this->issetfont=false;
						}
				}
		}

		function SetStyle($tag,$enable)
		{
				//Modify style and select corresponding font
				$this->$tag+=($enable ? 1 : -1);
				$style='';
				foreach(array('B','I','U') as $s)
						if($this->$s>0)
								$style.=$s;
				$this->SetFont('',$style);
		}

		function PutLink($URL,$txt)
		{
				//Put a hyperlink
				$this->SetTextColor(0,0,255);
				$this->SetStyle('U',true);
				$this->Write(5,$txt,$URL);
				$this->SetStyle('U',false);
				$this->SetTextColor(0);
		}

}# end of class PDF


//set border
$border_0="0";
$border_1="1";
$spacing =2;
// font setup
$fontSizeLabel = 8+3;
$fontSizeInput = 11;
$fontSizeText = 12;
$fontSizeHeader = 14;
//fontstyle setup
$fontStyle = "Arial";
$fontStyle2 = "Times";
$my_add_left_margin=10; # additional left margin


//instantiate fpdf class
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage("P");

//$pdf->SetAutoPageBreak(TRUE ,20);

#added by VAN 07-11-08
//$pdf->SetLeftMargin($my_add_left_margin);
// Hospital Logo
$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,5,25,30);
$pdf->Image($root_path.'modules/radiology/images/rad_logo.jpg',170,5,25,30);

$pdf->SetFont($fontStyle,"I","$fonSizeInput)");

	$hospital = new Hospital_Admin();
	$hospitalInfo = $hospital->getAllHospitalInfo();
	$total_w = 0;
	$pdf->Cell(0,4,$hospitalInfo['hosp_country'],$border2,1,'C');
	$pdf->Cell(0,4,$hospitalInfo['hosp_agency'],$border2,1,'C');
	$pdf->Ln(1);
	$pdf->SetFont($fontStyle,"B",$fontSizeHeader-2);
	$pdf->Cell(0,4,$hospitalInfo['hosp_name'],$border2,1,'C');
	$pdf->SetFont($fontStyle,"",$fontSizeInput);
	$pdf->Cell(0,4,$hospitalInfo['hosp_addr1'],$border2,1,'C');

//Department Name
$pdf->Ln(2);
$pdf->setFont($fontStyle,"B", $fontSizeInput+1);
$pdf->Cell(0,4,'Department of Radiological & Imaging Sciences',$border_0, 1, 'C');


//Patient name and PID

#$pdf->Cell(10, 3 ,'', "",0,''); # left margin
$rect_h = 16;
if(strlen($sAddress)>60){
	$rect_h = 20;
}
$pdf->Ln(10);
$pdf->SetFont($fontStyle,"B", $fontSizeLabel+20);
$pdf->SetLineWidth(0.6);
$pdf->Rect( $pdf->getX(), $pdf->getY(), 185, $rect_h);
$pdf->Ln(2);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(15, 3 ,'Patient : ', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell(135, 3 ,strtoupper($name_last.', '.$name_first.' '.$name_middle), "",0,'');

$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell(12, 3 , 'HRN :', "",0,'L');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell(40, 3 ,$pid, "",0,'');

//Address
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(18, 3 ,'Address : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->multiCell(140, 4 ,ucwords($sAddress), "",2,'');

//RID
//$pdf->Ln(10);
#$pdf->Cell(10, 3 ,'', "",0,''); # left margin
$pdf->setXY(160,$pdf->getY()-4);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(10, 3 ,'RID : ', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell(0, 3 ,$rid, "",1,'');

$pdf->Ln(2);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(10, 3 , 'Sex :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(18, 3 ,strtoupper($gender), "",0,'');
 //Birthdate and Area
//$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(20, 3 ,'Birthdate : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(25, 3 ,$segBdate, "",0,'');

$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(10, 3 ,'Age : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(67, 3 ,$age, "",0,'');

$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(10, 3 ,'BN : ', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell(0, 3 ,$batch_nr, "",1,'');

#edited by VAN 07-11-08
#Requesting Doctor
$pdf->Ln(3);
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(37, 3 , 'Requesting Doctor :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(103, 3 ,$request_doctor_name, "",0,'');

#Exam Taken
//$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(25, 3 , 'Exam Taken :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(50, 3 ,strtoupper($service_code), "",1,'');

#Impression
$pdf->Ln(2);
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(55, 3 , 'Clinical Indication/Impression : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
//$pdf->Cell(85, 3 ,$clinical_info, "",0,'');
$pdf->multiCell(80, 4 ,strtoupper($clinical_info), "",2,'');

#Dept
$pdf->setXY(150,$pdf->getY()-4);
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(12, 3 , 'Dept :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(50, 3 ,$request_dept_name, "",1,'');


#Examination
$pdf->Ln(2);
//$pdf->setXY(20,$pdf->getY());
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(48, 3 , 'Date/Time of Examination :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(92, 3 ,$served_date , "",0,'');

#Area
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(12, 3 , 'Area :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->multiCell(50, 3 ,strtoupper($area), "",2,'');


//Document Title - Roentgenological Report
$i = $med_obj->_setIso($service_dept_nr);
$rValue= $i['iso_number'];

if ($service_dept_nr==MAMO) {
	$rEffectivity = "Effectivity : August 11, 2015";
}
else {
	$rEffectivity = "Effectivity : October 1, 2013";
}

if ($service_dept_nr==ULTRASOUND|| $service_dept_nr==SPECIALPROCEDURES || $service_dept_nr==COMPUTEDTOMOGRAPHY || $service_dept_nr==MRI || $service_dept_nr==ULTRASOUND-OB-GYNE || $service_dept_nr==MAMO){
		$note_msg="";
}else{
		$note_msg="NOTE: This result is based on radiographic findings & must be correlated clinically.";
}

#set ISO for footer 
$pdf->setIso($rValue);#added by art 02/22/2014
$pdf->setEffectivity($rEffectivity); #added by Gervie 02/19/2016

if ($status=='pending'){
	$result = " INITIAL READING";
	$foot_result = " / Initially Read";
}elseif ($status=='done' && $is_served==1){
	$result = " OFFICIAL READING";
	$foot_result = " / Officially Read";
}elseif ($status=='referral')
	$result = " FOR REFERRAL";

# added by: syboy 08/03/2015
if (strtoupper($group_code) == 'MAMO') {
	$report = 'DIGITAL MAMMOGRAPHY REPORT';
} else {
	$report= $i['document_name'];
}
# end

$pdf->Ln(3);
$pdf->SetFont($fontStyle,"B", $fontSizeHeader-3);
$pdf->Cell(0, 5 , strtoupper($report), $border_0,1,'C');
$pdf->Ln(2);
$pdf->SetFont($fontStyle,"B", $fontSizeHeader-3);
$pdf->Cell(0, 5 , strtoupper($result), $border_0,1,'C');
if ($note_msg){
	$pdf->Ln(2);
	$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
	$pdf->Cell(0,3 , $note_msg, $border_0,1,'C');
}

function getSignatureImage($nr,$root_path){
	$signature = false;
	$imageFiles = scandir("{$root_path}fotos/registration");
	foreach ($imageFiles as $key => $fileName) {
		if(strpos($fileName, $nr) > -1){
			$signature = "{$root_path}fotos/registration/{$fileName}";
		}
	}
	return $signature;
}

function showSignatureImage($nr,$root_path,$pdf){
	if(is_array($nr)){
		foreach ($nr as $key => $pnr) {
			$signature = getSignatureImage($pnr,$root_path);
			if($signature!==false){
				$pdf->Image($signature,$pdf->GetX()+(50*$key),$pdf->GetY() - 5,25,5);
			}
		}
	}else{
		$signature = getSignatureImage($nr,$root_path);
		if($signature!==false){
			$pdf->Image($signature,$pdf->GetX(),$pdf->GetY() - 5,25,5);
		}
	}
}

foreach($batchNrArrayInfo as $batchNrInfo){
	extract($batchNrInfo);

	//DATE, Batch Number and 'INITIAL READING'
	/*$pdf->Ln(5);
	$pdf->SetFont($fontStyle,"", $fontSizeLabel);
	$pdf->Cell(80, 3 , "Clinical Indication/Impression : ".$clinical_info, "", 0,'');
	$pdf->Ln(5);
	if($status=='pending')
	{
	$pdf->Cell(80, 3 , "". "", "", 0,'');   # date of service
	}
	else
	{
	$pdf->Cell(80, 3 , "Date Officially Read : ".$seg_service_date, "", 0,'');   # date of service
	}*/
	

	//$pdf->SetFont($fontStyle,"", $fontSizeLabel);
	#edited by VAN 07-11-08
	//$pdf->Cell(90, 3 ,"Reference # ".$refno, "",0,'');

	//$pdf->SetFont($fontStyle,"IB", $fontSizeLabel);



	//Service name
	$pdf->Ln(5);
	$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
	$pdf->Cell(0, 3 , strtoupper($service_name), "", 0,'');   # service name

	//Findings
	$pdf->Ln(10);
	if ($findings){
		$pdf->SetFont($fontStyle,"", $fontSizeLabel);
		$pdf->Cell(0, 3 , strtoupper('Findings : '), "", 1,'');
	}

	//$findings = str_replace('/\s+/', '', $findings); //added by KENTOOT 06/07/2014

	$pdf->Ln(5);
	$pdf->SetFont($fontStyle,"", $fontSizeLabel-2);
	$pdf->WriteHTML($findings);

	//Radiographic Impression
	$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
	$radio_impression_final = trim($radio_impression_final);
	if ($radio_impression_final){
		$pdf->Ln(12);
		$pdf->Cell(0, 3 , strtoupper('Impressions : '), "", 1,'');
		$pdf->Ln();
	}

	#-------------------edited by celsy 08/18/10-----------------#
	#for displaying tables from R impressions
    #edited by KENTOOT 05/30/2014
    $pdf->Ln(1);
    $pdf->SetFont($fontStyle,"B", $fontSizeLabel-2);
    $pdf->WriteHTML($radio_impression_final);
    
    $pdf->Ln(8);
    // $pdf->SetFont($fontStyle,"", $fontSizeLabel-10);

	if ($radio_obj->hasBatchNR($batch_nr, count($findings_array))) {
		$docNR = &$radio_obj->getDoctorNR($batch_nr, count($findings_array));
		$doc_NR = $docNR->Fetchrow();
		$docs[0] = $doc_NR['con_doctor_nr'];
		$docs[1] = $doc_NR['sen_doctor_nr'];
		$docs[2] = $doc_NR['jun_doctor_nr'];

		for ($x = 0; $x <= 2; $x++) {
			if ($docs[$x] != '') {
				$rs_pr = Personell::getDoctorInfo($docs[$x]);
				while ($row_pr = $rs_pr->Fetchrow()) {
					$dr_name = mb_strtoupper($row_pr['dr_name']) . ", " . $row_pr['drtitle'];
					$pos = mb_strtoupper(trim($row_pr['job_position']));
					$c += 1;
					$encoding_type = mb_detect_encoding($dr_name);
					if ($encoding_type != 'UTF-8')
						$dr_name = mb_convert_encoding($dr_name, 'UTF-8', $encoding_type);

					if ($c == 1) {
						$Fsign = Personell::getPersonnelSignature($row_pr['personell_nr'], $root_path);
						$Fdoc = $dr_name;
						$Fpos = $pos;
					} elseif ($c == 2) {
						$Ssign = Personell::getPersonnelSignature($row_pr['personell_nr'], $root_path);
						$Sdoc = $dr_name;
						$Spos = $pos;
					} elseif ($c == 3) {
						$Tsign = Personell::getPersonnelSignature($row_pr['personell_nr'], $root_path);
						$Tdoc = $dr_name;
						$Tpos = $pos;
					} elseif ($c == 4) {
						$sign4 = Personell::getPersonnelSignature($row_pr['personell_nr'], $root_path);
						$doc4 = $dr_name;
						$pos4 = $pos;
					} else {
						$sign5 = Personell::getPersonnelSignature($row_pr['personell_nr'], $root_path);
						$doc5 = $dr_name;
						$pos5 = $pos;
					}
				}
			}
		}
	} else {
		#print_r($doctors_final);
		if (empty($doctors_final)) {
			$Fdoc = "No Doctor Selected";
		} else {
			$Fdoc = mb_strtoupper(mb_convert_encoding($doctors_final, "ISO-8859-1", 'UTF-8'));
		}
	}
      
    $space=" "; 
    if($c==5){
		$Fcell = $Fdoc." / ".$Sdoc;
		$Scell = $Tdoc." / ".$doc4;
		$Tcell = $doc5;
		$cellpos = $Fpos.str_repeat(' ',strpos($Fcell,'/')-strlen($Spos)+2).$Spos;
		$Scellpos = $Tpos.str_repeat(' ',strpos($Scell,'/')-strlen($pos4)+2).$pos4;
		$Tcellpos = $pos;

		$Fsignatures = array(0 => $Fsign,intval($pdf->GetStringWidth($Fdoc." / "))+10 => $Ssign);
		$Ssignatures = array(0 => $Tsign,intval($pdf->GetStringWidth($Tdoc." / "))+10 => $sign4);

		$Tsignatures = array(0 => $sign5);
    }elseif($c==4){
		$Fcell = $Fdoc." / ".$Sdoc;
        $Scell = $Tdoc." / ".$doc4;
		$cellpos = $Fpos.str_repeat(' ',strpos($Fcell,'/')-strlen($Spos)+2).$Spos;
		$Scellpos = $Tpos.str_repeat(' ',strpos($Scell,'/')-strlen($pos4)+2).$pos4;

		$Fsignatures = array(0 => $Fsign,intval($pdf->GetStringWidth($Fdoc." / "))+10 => $Ssign);
		$Ssignatures = array(0 => $Tsign,intval($pdf->GetStringWidth($Tdoc." / "))+10 => $sign4);
    }elseif($c==3){
		$Fsignatures = array($Fsign);
		$Ssignatures = array($Ssign,$Tsign);
        $Fcell = $Fdoc."\n".$Fpos;
        $Scell = $Sdoc." / ".$Tdoc;
		$Scellpos = $Spos.str_repeat(' ',strpos($Scell,'/')-strlen($Spos)+2).$Tpos;

		$Fsignatures = array($Fsign);
		$Ssignatures = array(0 => $Ssign,intval($pdf->GetStringWidth($Sdoc." / "))+10 => $Tsign);
    }elseif($c==2){
		$Fsignatures = array($Fsign,$Ssign);
        $Fcell = $Fdoc." / ".$Sdoc;
		$cellpos = $Fpos.str_repeat(' ',strpos($Fcell,'/')-strlen($Fpos)+2).$Spos;
		$Fsignatures = array(0 => $Fsign,intval($pdf->GetStringWidth($Fdoc." / "))+10 => $Ssign);
    }else{
		$Fsignatures = array($Fsign);
        $Fcell = $Fdoc."\n".$Fpos;
    } 

    $pdf->Ln(3);
    $pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
    $y = $pdf->getY();

	if(!empty($Fsignatures)){
		$left = $pdf->lMargin;
		$isSignaturePrinted = false;
		foreach($Fsignatures as $key => $signature){
			$left+=$key;
			if($signature){
				$pdf->Image($signature,$left,$pdf->GetY(),30,8);
				$isSignaturePrinted = true;
			}
		}
		if($isSignaturePrinted)
			$pdf->Ln(10);
	}

    $pdf->MultiCell(0,5,mb_strtoupper($Fcell),0,'L',0);
    $pdf->MultiCell(0,5,mb_strtoupper($cellpos),0,'L',0);
    $pdf->Ln(2);

	if(!empty($Ssignatures)){
		$left = $pdf->lMargin;
		$isSignaturePrinted = false;
		foreach($Ssignatures as $key => $signature){
			$left+=$key;
			if($signature){
				$pdf->Image($signature,$left,$pdf->GetY(),30,8);
				$isSignaturePrinted = true;
			}
		}
		if($isSignaturePrinted)
			$pdf->Ln(10);
	}

    $pdf->MultiCell(0,5,mb_strtoupper($Scell),0,'L',0);
    $pdf->MultiCell(0,5,mb_strtoupper($Scellpos),0,'L',0);
	$pdf->Ln(2);

	if(!empty($Tsignatures)){
		$left = $pdf->lMargin;
		$isSignaturePrinted = false;
		foreach($Tsignatures as $key => $signature){
			$left+=$key;
			if($signature){
				$pdf->Image($signature,$left,$pdf->GetY(),30,8);
				$isSignaturePrinted = true;
			}
		}
		if($isSignaturePrinted)
			$pdf->Ln(10);
	}

    $pdf->MultiCell(0,5,mb_strtoupper($Tcell),0,'L',0);
    $pdf->MultiCell(0,5,mb_strtoupper($Tcellpos),0,'L',0);

	$radtech = $personell_obj->get_Person_name3($rad_tech,1);

	if($radtech){
		while ( $row_tech = $radtech->FetchRow() ) {
			$radtech_name = mb_strtoupper($row_tech['dr_name']).", RRT";
		}
	}


	#edited by KENTOOT 05/29/2014
	$pdf->setY(-35);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(134, 3 , '', "", 0,'');
	$pdf->Cell(0, 3 , 'Served by : '.ucwords($radtech_name) ,"", 0,'');
	$pdf->Ln(3);
	$pdf->Cell(134, 3 , '', "", 0,'');
	$pdf->Cell(0, 3 , 'Result Encoded by : '.ucwords($findings_encoder), "", 0,'');
	$pdf->Ln(3);
	$pdf->Cell(134, 3 , '', "", 0,'');
	$pdf->Cell(0, 3 ,"Date Encoded".$foot_result." : ".$findings_date_final, "", 0,'');

	$pdf-> setY(-24);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(100, 3 ,"**This is electronically generated official report. No signature is required. **Certified by:");
}

//print pdf
$pdf->Output();