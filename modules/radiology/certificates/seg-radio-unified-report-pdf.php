<?php

/*
*  @since 02-12-09
*  @author Raissa
*  @internal This file is used for creating pdf of unified results for radiology
*  @access public
*  @package modules
*  @subpackage radiology/certificates
*  @param string pid
*  @param string batch_nr
*/

include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

define('Ultrasound','165');
define('GeneralRadiography','164');
define('SpecialProcedures','166');
define('ComputedTomography','167');
define('MRI','208');
define('Ultrasound-OB-Gyne','209');
define('MAMO','235'); #added by: syboy 08/11/2015
// added by carriane 03/16/18
define('IPBMIPD_enc', 13);
define('IPBMOPD_enc', 14);
// end carriane

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

include_once($root_path.'include/care_api_classes/class_cert_med.php');
$med_obj= new MedCertificate;

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
						$province = trim($prov_name);
				}
		}
}else{
		$province = "";
}

$sAddress = trim($street_name)." ".trim($brgy_name).trim($mun_name)." ".$province;
$final_reader='';

# Create radiology object


require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

include_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;
#added by: mark 02/07/2016 
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_pacs_parse_hl7_message.php');
$parseObj = new seg_parse_msg_HL7();


$resultBatch = $radio_obj->selectByBatch($batch_nr);
if ($resultBatch) {
	while ($rows = $resultBatch->FetchRow()) {
		if ($rows['status'] !="done" && $rows['is_served'] =='1') {
			 $resultdata = $radio_obj->getHL7Msg($rows['batch_nr']);
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

	  }
}
#end added by MARK


if ($batch_nr){
		if (!($radioResultObj = $radio_obj->getAllInfoUnifiedBatchRequestList($batch_nr))){

				 echo "seg-radio-findings-select-batchNr.php : radio_obj->sql = '".$radio_obj->sql."' <br> \n";
				 echo '<em class="warn"> Cannot continue to display the page! <br> \n NO Result(s) found.</em>';
				exit();
		}
}else{
		echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Batch Number!</em>';
		exit();
}
//dito na ako banda
//echo "<pre>".print_r($radioResultObj->GetRows(),true)."</pre>";
while($radioResultInfo = $radioResultObj->FetchRow()){
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
		} elseif($encounter_type == IPBMOPD_enc){
	                        $area = "IPBM - OPD";
	        } elseif($encounter_type == IPBMIPD_enc){
	                        $area = $ward_name;
		}else{
				$area="WALKIN";
		}
		$rs_doc = $request_doctor_name;

		$rs_dep = $request_dept_name;
		$seg_request_date = 'No Date Requested indicated';
		if($request_date && ($request_date!='0000-00-00')){
				$seg_request_date = @formatDate2Local($request_date,$date_format);
		}
		if($request_date=='')
		{
			$seg_request_date = @formatDate2Local($create_dt,$date_format);
		}
		if($create_dt=='')
		{
			$seg_request_date = 'No Date Requested indicated';
		}

		$findings_array = unserialize($findings);
		#$findings = $findings_array[$findings_nr];
		$findings = $findings_array[count($findings_array)-1];
		$findings_final = utf8_decode($findings_array[count($findings_array)-1]);
		$radio_impression_array = unserialize($radio_impression);
		$radio_impression_final = $radio_impression_array[count($radio_impression_array)-1];
		$doctors_array = unserialize($doctor_in_charge);

		#$doctors_final = $doctors_array[count($doctor_in_charge)-1];
		$doctors_final = $doctors_array[count($doctors_array)-1];

		#edited by VAN 04-28-2011
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

		$doctor_in_charge_array = unserialize($doctor_in_charge);
		$doctor_in_charge_final = $doctor_in_charge_array[count($doctor_in_charge_array)-1];

	 #$doctor_in_charge_final = $doctor_in_charge_array[$findings_nr];
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
		
		
		$batchNrArrayInfo[$id]['service_code'] = $service_code;
		$batchNrArrayInfo[$id]['request_doctor_name'] = $request_doctor_name;
		$batchNrArrayInfo[$id]['request_dept_name'] = $request_dept_name;
		$batchNrArrayInfo[$id]['or_no_final'] = $or_no_final;
		$batchNrArrayInfo[$id]['amount_paid'] = $amount_paid;
		$batchNrArrayInfo[$id]['status'] = $status;

		$batchNrArrayInfo[$id]['seg_request_date'] = $request_date;
		$batchNrArrayInfo[$id]['seg_service_date'] = $seg_service_date;
		$batchNrArrayInfo[$id]['batch_nr'] = $batch_nr;
		$batchNrArrayInfo[$id]['refno'] = $refno;

		#added by Kentoot 05/15/2014
		$batchNrArrayInfo[$id]['service_name'] = $service_name;
		$batchNrArrayInfo[$id]['rad_tech'] = $rad_tech;
		$batchNrArrayInfo[$batch_nr]['default_font'] = $default_font;
		#end by kentoot

		$batchNrArrayInfo[$id]['findings'] = $findings;
		$batchNrArrayInfo[$id]['findings_final'] = $findings_final;
		$batchNrArrayInfo[$id]['radio_impression_final'] = $radio_impression_final;
		$batchNrArrayInfo[$id]['findings_date_final'] = $findings_date_final;
		$batchNrArrayInfo[$id]['doctor_in_charge_final'] = $doctor_in_charge_final;
		$batchNrArrayInfo[$id]['doctor_in_charge_name'] = $doctor_in_charge_final;
		$temp_findings_encoder = $personell_obj->getUserFullName($findings_encoder);

		if($temp_findings_encoder != false)
			$findings_encoder = $temp_findings_encoder;
			
		$batchNrArrayInfo[$id]['findings_encoder'] = $findings_encoder;
		$batchNrArrayInfo[$id]['final_reader'] = $final_reader;
		$batchNrArrayInfo[$id]['group_code'] = $group_code; #added by: syboy 08/03/2015 
		$batchNrArrayInfo[$id]['service_dept_nr'] = $service_dept_nr; #added by: syboy 08/11/2015 

		if($final_reader=='')
				$final_reader = $doctor_in_charge_final;
		else if($final_reader==$doctor_in_charge_final)
				$same = FALSE;
		else
				$same = FALSE;

}#end of while loop 'while($radioResultInfo = $radioResultObj->FetchRow())'


/*
foreach($batchNrArrayInfo as $batchNrInfo){
 echo "seg-radio-findings-select-batchNr.php : batchNrInfo : <br> \n"; print_r($batchNrInfo); echo" <br> \n";
}*/

/*
echo "seg-radio-report-pdf.php : radio_obj->sql = '".$radio_obj->sql."' <br> \n";

echo " Exam taken : '".$service_code."' <br> \n";
echo " Requesting Doctor : '".$request_doctor_name."' <br> \n";
echo " Dept : '".$request_dept_name."' <br> \n";
echo " O.R. No. : '".$or_no."' <br> \n";
echo " Amount Paid (amount_or) : '".$amount_or."' <br> \n";
echo " Amount Paid (amount_charity) : '".$amount_charity."' <br> \n";
echo " Amount Paid (price_net) : '".$price_net."' <br> \n";

echo " Area : '".$encounter_type."' '".$area."' <br> \n";

echo " date_format : '".$date_format."' <br> \n";
echo " Date Requested : '".$seg_request_date."' <br> \n";
echo " Date Service : '".$seg_service_date."' <br> \n";

echo " Batch Number : '".$batch_nr."' <br> \n";
echo " Service Name : '".$service_name."' <br> \n";

echo " findings_array : <br> \n"; print_r($findings_array); echo"<br> \n";
echo " radio_impression_array : <br> \n"; print_r($radio_impression_array); echo"<br> \n";
echo " findings_date_array : <br> \n"; print_r($findings_date_array); echo"<br> \n";
echo " doctor_in_charge_array : <br> \n"; print_r($doctor_in_charge_array); echo"<br> \n";

echo " Final Findings : '".$findings_final."' <br> \n";
echo " Final Radio Impression : '".$radio_impression_final."' <br> \n";
echo " Final Findings Date : '".$findings_date_final."' <br> \n";
echo " Final Reporting Doctor : '".$doctor_in_charge_name."' ('".$doctor_in_charge_final."') <br> \n";

echo " Findings Encoder : '".$findings_encoder."' <br> \n";
*/


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

#---added by Kentoot 05/15/2015
function getServedBy($rad_tech){
	$personell_obj=new Personell;

	$radtech = $personell_obj->get_Person_name3($rad_tech,1);
	if($radtech){
		while ( $row_tech = $radtech->Fetchrow() ) {
			$radtech_name = mb_strtoupper($row_tech['dr_name']).", RRT";
		}
	}	
		return $radtech_name;
}
#---end by Kentoot

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
		*    Page header
		*    override the method in FPDF (the implementation in FPDF is empty)
		*/
/*
		function Header(){
						//Logo
				$this->Image('logo_pb.png',10,8,33);
						//Arial bold 15
				$this->SetFont('Arial','B',15);
						//Move to the right
				$this->Cell(80);
						//Title
				$this->Cell(30,10,'Title',1,0,'C');
						//Line break
				$this->Ln(20);
		}
*/
		/*
		*    Page footer
		*    override the method in FPDF (the implementation in FPDF is empty)
		*/		
		#added by art 02/22/2014
		var $iso;
		function setIso($value){
			$this->iso = $value;
		}
		function getIso(){
			return $this->iso;
		}
		#end art

		# added by Gervie 02/24/2016
		var $effectivity;
		function setEffectivity($value){
			$this->effectivity = $value;
		}
		function getEffectivity(){
			return $this->effectivity;
		}

		function Footer(){

			#added by art 02/12/2014
			#edited by KENTOOT 06/02/2014
			$this->setY(-20);
			$this->SetLineWidth(0.2);
			$this->Line($this->getX(), $this->getY(), 203, $this->getY());
			$this->SetFont('Arial', 'B', 8);
	        $this->Cell(0, 4, $this->getIso(), "", 1, 'R');
	        $this->SetFont('Arial', '', 8);
	        $this->Cell(60, 8, 'Effectivity : ' . $this->getEffectivity(), 0, 0, 'L');
	        $this->Cell(80, 8, 'Revision : 0', 0, 0, 'C');
	        $this->Cell(56, 8, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'R');
			#end art
		}

		var $B;
		var $I;
		var $U;
		var $HREF;
		var $fontList;
		var $issetfont;
		var $issetcolor;

		#edited by KENTOOT 06/20/2014
		function PDF($orientation='P',$unit='mm',$format=array(215.9,330.2))
		{
				//Call parent constructor
				$this->FPDF($orientation,$unit,$format);

				$this->SetMargins(10, 8, 10, true); //added by KENTOOT 06/20/2014
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
								else
										$this->Write(5,stripslashes(txtentities($e)));
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
                                
                                if ($attr['SIZE']<=0) {
                                    $attr['SIZE'] = $fontSizeText;                    
                                }
                                    
								if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist) and isset($attr['SIZE']) and $attr['SIZE']!='') {
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
//$pdf->SetAutoPageBreak(true , 15);
$pdf->AliasNbPages();
$pdf->AddPage("P");

//$pdf->SetLeftMargin($my_add_left_margin);

#Edited by Kentoot 05/15/2014 --------------
//echo count($batchNrArrayInfo);
$counter=0;
//echo "<tt><pep>";
//echo print_r($batchNrArrayInfo);
//echo "</tt></pep>";
foreach($batchNrArrayInfo as $batchNrInfo){
	$counter++;
	extract($batchNrInfo);
	
# Hospital Logo
$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,5,25,30);
$pdf->Image($root_path.'modules/radiology/images/rad_logo.jpg',170,5,25,30);

$pdf->SetFont($fontStyle,"I","$fontSizeInput)");

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
/*$pdf->Image($root_path.'gui/img/logos/radio_logo.jpg',165,10,25,30);

#Header - Republic of the Philippines / Department of Health
$pdf->SetFont($fontStyle, "I", $fonSizeInput);
$pdf->Cell(0,4,'Republic of the Philippines', $border_0,1,'C');
$pdf->Ln(1);
$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');

#Hospital name- Davao Medical Center
$pdf->Ln(1);
$pdf->setFont($fontStyle,"B", $fontSizeHeader-2);
$pdf->Cell(0,4,'DAVAO MEDICAL CENTER',$border_0, 1, 'C');

#Hospital Address
$pdf->Ln(1);
$pdf->setFont($fontStyle,"", $fontSizeInput);
$pdf->Cell(0,4,'Bajada, Davao City, Philippines',$border_0, 1, 'C');
 */
#Department Name
$pdf->Ln(2);
$pdf->setFont($fontStyle,"B", $fontSizeInput+1);
$pdf->Cell(0,4,'Department of Radiological & Imaging Sciences',$border_0, 1, 'C');

#edited by VAN
/*
#Batch number
$pdf->Ln(10);
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell(31, 3 ,'Batch Number : ', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel+2);
$pdf->Cell(0, 3 ,$refno, "",0,'');

#Patient name and PID
$pdf->Ln(7);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(15, 3 ,'Patient : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(102, 3 ,strtoupper($name_last.', '.$name_first.' '.$name_middle), "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(12, 3 , 'HRN :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(40, 3 ,$pid, "",0,'');

#Address
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(18, 3 ,'Address : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(150, 3 ,$sAddress, "",0,'');

#Birthdate and Sex
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(30, 3 ,'Birthdate / Age : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(87, 3 ,$segBdateAge, "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(12, 3 , 'Sex :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(50, 3 ,strtoupper($sex), "",0,'');

#Area
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(12, 3 , 'Area :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(105, 3 ,strtoupper($area), "",0,'');

#Date Requested
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(30, 3 , 'Date Requested :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(40, 3 ,$seg_request_date, "",0,'');
*/

//RID
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
$pdf->Cell(92, 3 ,$served_date, "",0,'');

#Area
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->Cell(12, 3 , 'Area :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
$pdf->multiCell(50, 3 ,strtoupper($area), "",2,'');

//$pdf->Ln(5);
/*commented by aty 02/12/2014
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(40, 3 , 'Clinical Impression :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(50, 3 ,$clinical_info, "",0,'');


#Date Requested
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(33, 3 , 'Date Served :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(50, 3 ,strtoupper($seg_request_date), "",0,'');
*/
//Document Title - Roentgenological Repor
#edited by: syboy 08/11/2015
#set ISO
$i = $med_obj->_setIso($service_dept_nr);
$rValue= $i['iso_number'];
if (strtoupper($group_code) == 'MAMO') {
	$report = 'DIGITAL MAMMOGRAPHY REPORT'; # added by: syboy 08/02/2015
	$rEffectivity = "August 11, 2015"; # added by: Gervie 02/24/2016
} else {

	if ($service_dept_nr==Ultrasound|| $service_dept_nr==SpecialProcedures || $service_dept_nr==ComputedTomography || $service_dept_nr==MAMO){
			$note_msg="";
	}else{
			$note_msg="NOTE: This result is based on radiographic findings & must be correlated clinically.";
	}
	$report= $i['document_name'];
	$rEffectivity = "October 1, 2013"; # added by: Gervie 02/24/2016
	// $report = $service_dept_nr;
	// $report = $batch_nr;
}
#end

$pdf->setIso($rValue);#added by art 02/22/2014
$pdf->setEffectivity($rEffectivity); # added by Gervie 02/24/2016
	 # added by MARK  02/07/2017
	$result ="";
	$foot_result ="";
if ($status=='pending'){
	$result = " INITIAL READING";
	$foot_result = " / Initially Read";
}elseif ($status=='done' && $is_served==1){
	$result = " OFFICIAL READING";
	$foot_result = " / Officially Read";
}elseif ($status=='referral')
	$result = " FOR REFERRAL";

$pdf->Ln(2);
$pdf->SetFont($fontStyle,"B", $fontSizeHeader-3);
$pdf->Cell(0, 5 , strtoupper($report), $border_0,1,'C');
$pdf->Ln(2);
$pdf->SetFont($fontStyle,"", $fontSizeHeader-3);
$pdf->Cell(0, 5 , strtoupper($result), $border_0,1,'C');
if ($note_msg){
	$pdf->Ln(2);
	$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
	$pdf->Cell(0,3 , $note_msg, $border_0,1,'C');
}
/*---------added by art 02/12/2014-----------*/
/*$pdf->Ln(2);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(53, 3 , 'Clinical Indication/Impression : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(50, 3 ,$clinical_info, "",0,'');*/
/*---------end----------*/
//foreach($batchNrArrayInfo as $batchNrInfo){
//		extract($batchNrInfo);

		//DATE, Reference Number and 'INITIAL READING'
		// $pdf->Ln(2);
		/*$pdf->SetFont($fontStyle,"", $fontSizeLabel);
		if($findings_date_final!="//")
				$pdf->Cell(80, 3 , "Date Encoded : ".$findings_date_final, "", 0,'');
		else
				$pdf->Cell(80, 3 , "", "", 0,'');
		//$pdf->SetFont($fontStyle,"", $fontSizeLabel);
		//$pdf->Cell(90, 3 ,"FILM NO. ".$batch_nr, "",0,'');*/
/*				if($status == 'pending'){
					$pdf->Cell(80, 3 , ""."", "", 0,'');   # date of service
				}
				else
		$pdf->Cell(80, 3 , "Date Officially Read : ".$seg_service_date, "", 0,'');   # date of service*/

/*		$pdf->SetFont($fontStyle,"IB", $fontSizeLabel);

		if ($status=='pending')
				$result = "- INITIAL READING";
		elseif ($status=='done')
				$result = "- OFFICIAL READING";
		elseif ($status=='referral')
				$result = "- FOR REFERRAL";

		$pdf->Cell(0, 3 ,$result, "",0,'R');*/

		$pdf->Ln(5);
		$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
		$pdf->Cell(0, 3 , strtoupper($service_name), "", 0,'');   # service name

		//Findings
		/*$findings_temp = $findings_final.$findings_final.$findings_final.$findings_final.$findings_final.
														$findings_final.$findings_final.$findings_final.$findings_final.$findings_final.
														$findings_final.$findings_final.$findings_final.$findings_final.$findings_final;*/
		/*$findings_temp = "Matter is the stuff which things are made of and consists of chemical substances. These are made of atoms, which are made of protons,                         neutrons and electrons. In this way, matter is contrasted with 'energy' inversely 'energy' is an expression of matter.
												In physics, there is no broad consensus as to an exact definition of matter. Physicists generally do not use the word when precision                        is needed, preferring instead to speak of the more clearly defined concepts of mass, energy and particles.
												A possible definition of matter which at least some physicists use [1] is that it is everything that is constituted of elementary                           fermions. These are the leptons, including the electron, and the quarks, including the up and down quarks of which protons and                               neutrons are made. Since protons, neutrons and electrons combine to form atoms, atoms, molecules and the bulk substances which they                          make up are all matter. Matter also includes the various baryons and mesons. Things which are not matter include light (photons) and                          the other gauge bosons.";*/
		#----------------------edited by KENTOOT 05/30/2015

		$pdf->Ln(10);
		if ($findings_final){
				$pdf->SetFont($fontStyle,"", $fontSizeLabel);
				$pdf->Cell(0, 3 , strtoupper('Findings : '), "", 1,'');
				//$pdf->Ln(1);
		}
		$pdf->SetFont($fontStyle,"", $fontSizeLabel-2);
		$findings_final = ($findings!='') ? trim($findings) : '';
		$findings_final = explode("*",$fontSizeLabel);
		if (empty($findings_final[0]))
				$cntb = 1;
		else
				$cntb = 0;
		/*
		for($i=$cntb;$i<sizeof($findings_final);$i++){
				$pdf->MultiCell(185,5,$findings_final[$i],0,'J',0);
				$pdf->Ln(2);
		}*/

		//$findings = preg_replace('/\s+/', '', $findings); //added by KENTOOT 06/07/2014

		$pdf->Ln(5);
		$pdf->SetFont($fontStyle,"", $fontSizeLabel-2);
		$pdf->WriteHTML($findings);

		//Radiographic Impression
		$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
		$radio_impression_final = trim($radio_impression_final);
		if ($radio_impression_final){
				$pdf->Ln(15);
				$pdf->Cell(0, 3 , strtoupper('Impressions : '), "", 1,'');
				$pdf->Ln();
		}
		
		#-------------------edited by celsy 08/18/10-----------------#

		$pdf->SetFont($fontStyle,"B", $fontSizeLabel-2);
		//$pdf->WriteHTML($radio_impression_final);
		//$pdf->WriteHTML('<strong>'.$radio_impression_final.'</strong>');
		$pdf->WriteHTML($radio_impression_final);
		if($radio_impression_final)
			$pdf->Ln(1);
		#print_r($radio_impression_final);
		#$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
//		$radio_impression_final = str_replace("<br>","",$radio_impression_final);
//		$radio_impression_final = explode(">",$radio_impression_final);

//		if (empty($radio_impression_final[0]))
//				$cnt = 1;
//		else
//				$cnt = 0;

//		for($i=$cnt;$i<sizeof($radio_impression_final);$i++){
//				#$pdf->MultiCell(185,5,"> ".$radio_impression_final[$i],0,'J',0);   # Radiographic Impression
//				$pdf->WriteHTML('<strong>'.$radio_impression_final[$i].'</strong>');
//				$pdf->Ln(4);
//		}

	$pdf->Ln(5);
 	$pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
 	#-------------------------------------------end KENTOOT

	#------------------------------end celsy-----------------------#
    
 	$null = 0;
     if($radio_obj->hasBatchNR($batch_nr,count($findings_array))){
     	$c=0;
		$Fdoc = ''; $Fpos = ''; $Sdoc ='';$Spos = '';$Tdoc = '';$Tpos = '';
		$doc4 = '';$pos4 = '';$doc5 = ''; $pos5 = '';
        $docNR = &$radio_obj->getDoctorNR($batch_nr,count($findings_array)); 
        $doc_NR = $docNR->Fetchrow();
        $docs[0]=  $doc_NR['con_doctor_nr'];
        $docs[1]=  $doc_NR['sen_doctor_nr'];
        $docs[2]=  $doc_NR['jun_doctor_nr'];
       // echo "<tt><pre>".print_r($personell_obj)."</tt></pre>";
        for($x=0;$x<=2;$x++){
            if($docs[$x] != ''){
                $rs_pr=$personell_obj->get_Person_name3($docs[$x]);
                while($row_pr = $rs_pr->Fetchrow()){
                    $dr_name = mb_strtoupper($row_pr['dr_name']).", ".$row_pr['drtitle'];
                    $pos =  mb_strtoupper(trim($row_pr['job_position']));   
                    $c += 1;
                    
                    $encoding_type = mb_detect_encoding($dr_name);
                    if($encoding_type!='UTF-8'){
                    	
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
                    else{
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

           
        }

    }else{
    	$null = 1;
       	$Fdoc = mb_strtoupper(mb_convert_encoding($doctors_final, "ISO-8859-1", 'UTF-8'));
    }

     $space=" "; 
     $Fcell =''; $Scell='';$Tcell='';
     $cellpos = '';$Scellpos='';$Tcellpos ='';

    if($null == 0){
	    if($c==5){
	        $Fcell = $Fdoc."\n".$Fpos;
	        $Scell = $Sdoc." / ".$Tdoc;
	        $Tcell = $doc4." / ".$doc5;
	        $pos = strpos($Scell,"/");
	        for($x=0;$x<=$pos-strlen($Spos);$x++){
	            $space .= "  "; 
	        }
	        $Scellpos = $Spos.$space." ".$Tpos;
	        $pos1 = strpos($Tcell,"/");
	        for($x=0;$x<=$pos1-strlen($pos4);$x++){
	            $space1 .= "  "; 
	        }
	        $Tcellpos = $pos4.$space1." ".$pos5;
	    }elseif($c==4){
	        $Fcell = $Fdoc." / ".$Sdoc;
	        $Scell = $Tdoc." / ".$doc4; 
	        $pos = strpos($Fcell,"/");
	        for($x=0;$x<=$pos-strlen($Fpos);$x++){
	            $space .= "  "; 
	        }
	        $cellpos = $Fpos.$space." ".$Spos;
	        $pos1 = strpos($Scell,"/");
	        for($x=0;$x<=$pos1-strlen($Tpos);$x++){
	            $space1 .= "  "; 
	        }
	        $Scellpos = $Tpos.$space1." ".$pos4;
	    }elseif($c==3){
	        $Fcell = $Fdoc."\n".$Fpos;
	        $Scell = $Sdoc." / ".$Tdoc;
	        $pos = strpos($Scell,"/");
	        for($x=0;$x<=$pos-strlen($Spos);$x++){
	            $space .= "  "; 
	        }
	        $Scellpos = $Spos.$space." ".$Tpos;
	    }elseif($c==2){
	       $Fcell = $Fdoc." / ".$Sdoc;
	       $pos = strpos($Fcell,"/");
	       for($x=0;$x<=$pos-strlen($Fpos);$x++){
	            $space .= "  "; 
	       }
	       $cellpos = $Fpos.$space." ".$Spos; 
	    }else{
	        $Fcell = $Fdoc."\n".$Fpos; 
	    } 
    }

        if(!$same){
            $pdf->Ln(3);
		    $pdf->SetFont($fontStyle,"", $fontSizeLabel-1);
		    $y = $pdf->getY();
		    $pdf->MultiCell(0,5,mb_strtoupper($Fcell),0,'L',0);
		    $pdf->MultiCell(0,5,mb_strtoupper($cellpos),0,'L',0);               
		    $pdf->Ln(2);
		    $pdf->MultiCell(0,5,mb_strtoupper($Scell),0,'L',0);
		    $pdf->MultiCell(0,5,mb_strtoupper($Scellpos),0,'L',0);
		    $pdf->Ln(2);
		    $pdf->MultiCell(0,5,mb_strtoupper($Tcell),0,'L',0);
		    $pdf->MultiCell(0,5,mb_strtoupper($Tcellpos),0,'L',0);

         }
	#----------------------added by Kentoot 05/13/2014

	//Retreive encoder
	$pdf->setY(-35);

	//Retreive encoder
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(134, 3 , '', "", 0,'');
	$pdf->Cell(134, 3 , 'Served by : '.ucwords(getServedBy($rad_tech)) ,"", 0,'');
	$pdf->Ln(3);
	$pdf->Cell(134, 3 , '', "", 0,'');
	$pdf->Cell(134, 3 , 'Result Encoded by : '.ucwords($findings_encoder), "", 0,'');
	$pdf->Ln(3);
	$pdf->Cell(134, 3 , '', "", 0,'');
	$pdf->Cell(134, 3 ,"Date Encoded".$foot_result." : ".$findings_date_final, "", 0,'');
	#end KENTOOT

	#----------------------added by Sherry 04/25/2015

	// adding text before the horizontal line 

	$pdf-> setY(-24);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(100, 3 ,"**This is electronically generated official report. No signature is required. **Certified by:");
	
	#end Sherry

	//generate exact page(s)
	if ($counter!=count($batchNrArrayInfo)){
	$pdf->AddPage();
	}

	#--------------------------end by Kentoot 05/13/2014
                                       
}

/*if($same){
    $pdf->Ln(8);
    $pdf->SetFont($fontStyle,"", $fontSizeLabel);
    $y = $pdf->getY();
    $pdf->MultiCell(0,5,mb_strtoupper(mb_convert_encoding($Fcell, "ISO-8859-1", 'UTF-8')),0,'L',0);
    $pdf->MultiCell(0,5,mb_strtoupper(mb_convert_encoding($cellpos, "ISO-8859-1", 'UTF-8')),0,'L',0);
    $pdf->Ln(8);
    $pdf->MultiCell(0,5,mb_strtoupper(mb_convert_encoding($Scell, "ISO-8859-1", 'UTF-8')),0,'L',0);
    $pdf->MultiCell(0,5,mb_strtoupper(mb_convert_encoding($Scellpos, "ISO-8859-1", 'UTF-8')),0,'L',0);
    $pdf->Ln(8);
    $pdf->MultiCell(0,5,mb_strtoupper(mb_convert_encoding($Tcell, "ISO-8859-1", 'UTF-8')),0,'L',0);
    $pdf->MultiCell(0,5,mb_strtoupper(mb_convert_encoding($Tcellpos, "ISO-8859-1", 'UTF-8')),0,'L',0);
    $pdf->Ln(9);
   
}*/

/*Commented by Kentoot 05/15/2014
//Encoder
	$radtech = $personell_obj->get_Person_name3($rad_tech);
	if($radtech){
		while ( $row_tech = $radtech->Fetchrow() ) {
			$radtech_name = mb_strtoupper($row_tech['dr_name']).", RRT";
		}
	}
	//Encoder
	$pdf->Ln(28);
	$pdf->SetFont($fontStyle,"", $fontSizeLabel-4);
	$pdf->Cell(140, 3 , '', "", 0,'');
	$pdf->Cell(0, 3 , 'Served by : '.ucwords($radtech_name) ,"", 0,'');
	$pdf->Ln(3);
	$pdf->Cell(140, 3 , '', "", 0,'');
	$pdf->Cell(0, 3 , 'Result Encoded by : '.ucwords($findings_encoder), "", 0,'');
	$pdf->Ln(3);
	$pdf->Cell(140, 3 , '', "", 0,'');
	$pdf->Cell(0, 3 ,"Date Encoded".$foot_result." : ".$findings_date_final, "", 0,'');
#$pdf->Cell(0, 3 ,$rValue.date("F d, o")." REVO", "", 0,'');commented by art 02/12/2014
#$pdf->Cell(0, 3 ,$rValue." October 1, 2013  Rev.0", "", 0,'');#added by art 02/12/2014
//print pdf
*/
$pdf->Output();

?>