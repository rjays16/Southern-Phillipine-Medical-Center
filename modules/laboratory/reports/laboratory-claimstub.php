<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');

include_once("{$root_path}classes/fpdf/fpdf.php");
require_once("{$root_path}include/care_api_classes/class_labservices_transaction.php");
require_once("{$root_path}include/care_api_classes/class_encounter.php");
require_once("{$root_path}include/care_api_classes/class_department.php");
require_once("{$root_path}include/care_api_classes/class_ward.php");
require_once("{$root_path}include/care_api_classes/class_personell.php");

/**
 * @author Nick B. Alcala 7-7-2015
 * Class LaboratoryClaimStub
 */
class LaboratoryClaimStub extends FPDF {

	private $referenceNumber = '';
	private $requestDetails = array();
	private $isPrintingSection = false;
	private $currentSection = '';
	const FONT_FAMILY = 'Arial';
	const FONT_SIZE = 8;
	const FONT_STYLE = 'B';
	const UNIT = 'mm';
	const PAGE_WIDTH = 190;
	const PAGE_HEIGHT = 140;
	const ORIENTATION = 'P';
	const IPBMOPD = 14;
	const IPBMIPD = 13;

	public function __construct(){
		$this->FPDF(self::ORIENTATION,self::UNIT,array(self::PAGE_WIDTH, self::PAGE_HEIGHT));
		$this->SetFont(self::FONT_FAMILY,self::FONT_STYLE,self::FONT_SIZE);

		$this->AliasNbPages();
		$this->SetTopMargin(5);
		$this->SetLeftMargin(10);
		$this->SetRightMargin(10);
		$this->SetAutoPageBreak(true,10);

		$this->referenceNumber = $_GET['refno'];
		$this->requestDetails = $this->getPatientAndRequestInfo();
	}

	private function claimStubHeader(){

		# added by: syboy 10/19/2015 : meow
		if ($this->requestDetails['is_walkin'] == 1) {
			$location = 'WALKIN';
			$patientType = 'WALKIN';
			$dept = '';
		}else{
			$location = $this->requestDetails['location'];
			$patientType = $this->requestDetails['patientType'];
			$dept = $this->requestDetails['department'];
		}
		# ended
        $sections = $this->getSections();
		foreach ($sections as $key => $section){

            if(count($sections) == 1){
                $this->currentSection = $key;
            }
        }
		$this->SetFont(self::FONT_FAMILY,self::FONT_STYLE,10);
		$this->Cell(0,4,'CLAIM STUB (RECEIVED REQUEST)',0,1,'C');
		$this->Ln();

		$this->printInfo('PRIORITY NUMBER',$this->referenceNumber,true);
		$this->printInfo('HOSP #',$this->requestDetails['pid'],false);

		$this->printInfo('NAME',$this->requestDetails['name'],true);
		$this->printInfo('DATE / TIME ENCODED',$this->requestDetails['dateEncoded'],false);

		$this->printInfo('PATIENT TYPE',$patientType,true);
		$this->printInfo('REQUEST DATE',$this->requestDetails['requestDate'],false);

		$this->printInfo('LOCATION/CLINIC',$location,true);
		$this->Ln();
 
		$this->printInfo('DEPARTMENT',$dept,true);
		$this->printInfo('DATE / TIME PRINTED',$this->requestDetails['datePrinted'],false);

		$this->Ln(2);
		$this->Cell(33,4,'CODE',1,0,'L');
		$this->Cell(85,4,'DESCRIPTION',1,0,'L');
		$this->Cell(30,4,'OR NO',1,0,'L');
		$this->Cell(25,4,'WITH SAMPLE',1,1,'L');
	}

	private function sectionsHeader(){
		
		# added by: syboy 10/19/2015 : meow
		if ($this->requestDetails['is_walkin'] == 1) {
			$location = 'WALKIN';
			$patientType = 'WALKIN';
			$dept = '';
		}else{
			$location = $this->requestDetails['location'];
			$patientType = $this->requestDetails['patientType'];
			$dept = $this->requestDetails['department'];
		}
		# ended

		$this->printInfo('PRIORITY NUMBER',$this->referenceNumber,true);
		$this->printInfo('CASE #',$this->requestDetails['encounter_nr'],false);

		$this->printInfo('NAME',$this->requestDetails['name'],true);

		$this->SetFont(self::FONT_FAMILY,'',8);
		$this->Cell(8,4,'AGE',0,0,'L');
		$this->Cell(3,4,':',0,0,'C');
		$this->SetFont(self::FONT_FAMILY,'B',9);
		$this->Cell(15,4,$this->requestDetails['age2'],0,0,'L');

		$this->SetFont(self::FONT_FAMILY,'',8);
		$this->Cell(13,4,'HOSP #',0,0,'L');
		$this->Cell(3,4,':',0,0,'C');
		$this->SetFont(self::FONT_FAMILY,'B',9);
		$this->Cell(20,4,$this->requestDetails['pid'],0,1,'L');

		$this->SetFont(self::FONT_FAMILY,'',9);
		$this->Cell(30,4,'ADDRESS             :');
		$this->SetFont(self::FONT_FAMILY,'B',9);
		$this->MultiCell(0, 4, $this->requestDetails['address'],0, 'L',false);

		$this->SetFont(self::FONT_FAMILY,'',9);
		$this->Cell(30,4,'IMPRESSION        :');
		$this->SetFont(self::FONT_FAMILY,'B',9);
		$this->MultiCell(150, 4, $this->requestDetails['impression'],0, 'L',false);

		//var_dump($this->requestDetails['impression']);die();
		$this->printInfo('DATE / TIME PRINTED',$this->requestDetails['datePrinted'],false);

		$this->printInfo('COMMENT',$this->requestDetails['comment'],true);
		$this->printInfo('DATE / TIME ENCODED',$this->requestDetails['dateEncoded'],false);

		$this->printInfo('REQUEST DATE',$this->requestDetails['requestDate'],true);
		$this->printInfo('REQUESTING PHYSICIAN',$this->requestDetails['doctorName'],false);

		$this->printInfo('PATIENT TYPE',$patientType,true);
		$this->printInfo('DEPARTMENT',$dept,false);

		$this->printInfo('LOCATION/CLINIC',$location,true);
		$this->Ln();

		$this->printInfo('PAYMENT TYPE',$this->requestDetails['is_cash'] ? 'CASH' : 'CHARGE',true);

		$this->Ln();

		$this->Cell(20,4,'SECTION',0,0,'L');
		$this->Cell(3,4,':',0,0,'L');
		$this->Cell(40,4,$this->currentSection,0,1,'L');

		$this->Ln(2);
		$this->Cell(75,4,'DESCRIPTION',1,0,'C');
		$this->Cell(45,4,'SECTION',1,0,'C');
		$this->Cell(28,4,'OR NO.',1,0,'C');
		$this->Cell(25,4,'W/ SAMPLE',1,1,'C');
	}

	private function printClaimStub(){
		global $db;
		$laboratory = new SegLab;

		$rs = $laboratory->getRequestedServices($this->referenceNumber, 'LB');
		$rows = $rs->GetRows();
		$index = 0;
		foreach ($rows as $key => $item) {
			if ($item['is_forward'])
				$withSample = "YES";
			else
				$withSample = "FW";

			$orNumber = '';

			if($item['is_cash']){
				if ($item["request_flag"]) {
					if ($item["request_flag"] == 'paid') {
						$parameters = array($item["pid"],trim($item["refno"]));
						$payInfo = $db->GetRow("SELECT pr.or_no, pr.ref_no,pr.service_code
												FROM seg_pay_request AS pr
												INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid=?
												WHERE pr.ref_source = 'LD' AND pr.ref_no=?
												AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')",$parameters);
						$orNumber = $payInfo['or_no'];
					} elseif ($item["request_flag"] == 'charity') {
						$payInfo = $db->GetRow("SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
												FROM seg_granted_request AS pr
												WHERE pr.ref_source = 'LD' AND pr.ref_no=?",trim($item["refno"]));
						if($payInfo){
							$orNumber = 'CLASS D';
						}
					} else {
						$orNumber = $item["charge_name"];
					}
				} else
					$orNumber = 'unpaid';
			}else{
				$orNumber = 'charge';
			}

			if($this->GetY() == 5){
				$this->claimStubHeader();
			}

			$this->SetFont(self::FONT_FAMILY,'',8);
			$this->Cell(33,4,$item['service_code'],1,0,'L');
			$this->Cell(85,4,$item['name'],1,0,'L');
			$this->Cell(30,4,$orNumber,1,0,'L');
			$this->Cell(25,4,$withSample,1,1,'L');

			if(($index%15==0 && $index > 0) || $index == count($rows)-1){
				$this->printPageNumber();
				$this->AddPage();
			}

			$index++;
		}
	}

	private function getSections(){

		$sections = array();

		global $db;
		$laboratory = new SegLab;

		$rs = $laboratory->getRequestedServices($this->referenceNumber, 'LB');
		$rows = $rs->GetRows();

		foreach ($rows as $key => $item) {
			if ($item['is_forward'])
				$withSample = "YES";
			else
				$withSample = "FW";

			$orNumber = '';

			if ($item["request_flag"]) {
				if ($item["request_flag"] == 'paid') {
					$parameters = array($item["pid"],trim($item["refno"]));
					$payInfo = $db->GetRow("SELECT pr.or_no, pr.ref_no,pr.service_code
												FROM seg_pay_request AS pr
												INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid=?
												WHERE pr.ref_source = 'LD' AND pr.ref_no=?
												AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')",$parameters);
					$orNumber = $payInfo['or_no'];
				} elseif ($item["request_flag"] == 'charity') {
					$payInfo = $db->GetRow("SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
												FROM seg_granted_request AS pr
												WHERE pr.ref_source = 'LD' AND pr.ref_no=?",trim($item["refno"]));
					if($payInfo){
						$orNumber = 'CLASS D';
					}
				} else {
					$orNumber = $item["charge_name"];
				}
			} else {
				if($item['is_cash']){
					$orNumber = 'unpaid';
				}else{
					$orNumber = 'charge';
				}
			}

			$sections[$item['groupnm']][] = array(
				'name' => $item['name'],
				'section' => $item['groupnm'],
				'or' => $orNumber,
				'withSample' => $withSample
			);
		}
		return $sections;
	}

	private function printPageNumber(){
		$this->SetY(-20);
		$this->addFooter();
	}

	private function printInfo($label,$value,$isLeft){
		if($isLeft){
			$this->SetFont(self::FONT_FAMILY,'',8);
			$this->Cell(27,4,$label,0,0,'L');
			$this->Cell(3,4,':',0,0,'C');
			$this->SetFont(self::FONT_FAMILY,'B',9);
			$this->Cell(65,4,$value,0,0,'L');
		}else{
			$this->SetFont(self::FONT_FAMILY,'',8);
			$this->Cell(33,4,$label,0,0,'L');
			$this->Cell(3,4,':',0,0,'C');
			$this->SetFont(self::FONT_FAMILY,'B',9);
			$this->Cell(39,4,$value,0,1,'L');
		}
	}

	private function printSections(){
		
		$sections = $this->getSections();
		//var_dump($sections);
		foreach ($sections as $key => $section) {
			$this->currentSection = $key;
			$this->sectionsHeader();
			$index = 0;
			foreach($section as $key => $item){
				
				/* TODO Refactor */
				$clinicalMicroscopyItems = array(
					'FECALYSIS (KATO-THICK) - ROUTINE',
					'Urinalysis - ROUTINE'
				);

				$this->SetFont(self::FONT_FAMILY,'',self::FONT_SIZE);
				$this->printSectionRows($item);

				if(
					(($index+1)%10==0 && count($section)-1 > $index) || //if the item is the tenth and last item in a section (10 items per page)
					($section[$key+1]['section'] == 'CLINICAL MICROSCOPY' && in_array($section[$key+1]['name'], $clinicalMicroscopyItems))//if the next item is CLINICAL MICROSCOPY and the name is either 'FECALYSIS (KATO-THICK) - ROUTINE' or 'Urinalysis - ROUTINE'
				) {
					//add new page
					$this->addFooter();
					$this->AddPage();
					$this->sectionsHeader();
				}

				if(count($section)-1 == $index && !(($index+1)%6==0 && count($section)-1 > $index)){
					$this->addFooter();
				}
				$index++;
			}//end foreach($section as $key => $item)
		}//end foreach ($sections as $key => $section)

	}

	private function printSectionRows($item){
		$this->Cell(75,4,$item['name'],1,0,'C');
		$this->Cell(45,4,$item['section'],1,0,'C');
		$this->Cell(28,4,$item['or'],1,0,'C');
		$this->Cell(25,4,$item['withSample'],1,1,'C');
	}

	public function generate(){
		$this->AddPage();
		$this->printClaimStub();
		$this->isPrintingSection = true;
		$this->printSections();
	}

	/**
	 * TODO get the iso numbers from database
	 */
	public function addFooter(){
		$isoCode = null;
		$showOtherInfo = false;

		switch(trim(strtoupper($this->currentSection))){
			case 'CLINICAL CHEMISTRY' :
				$isoCode = "SPMC-F-LAB-10";	// updated by gelie 09/23/2015
				$showOtherInfo = true;
				break;
			case 'HEMATOLOGY' :
				$isoCode = "SPMC-F-LAB-09";	// updated by gelie 09/23/2015
				$showOtherInfo = true;
				break;
			case 'SEROLOGY AND IMMUNOLOGY' :
				$isoCode = "SPMC-F-LAB-13";	// updated by gelie 09/23/2015
				$showOtherInfo = true;
				break;
			case 'HISTOPATHOLOGY' :
				$isoCode = "SPMC-F-LAB-15";	// updated by gelie 09/23/2015
				$showOtherInfo = false;
				break;
			case 'MICROBIOLOGY' :
				$isoCode = "SPMC-F-LAB-16";	// updated by gelie 09/23/2015
				$showOtherInfo = false;
				break;
			case 'CLINICAL MICROSCOPY' :
				$isoCode = "SPMC-F-LAB-11";	// updated by gelie 09/23/2015
				$showOtherInfo = false;
				break;
			// added by gelie 09/23/2015
			case 'POLYMERASE CHAIN REACTION(LAB)' :
				$isoCode = "SPMC-F-LAB-17";
				$showOtherInfo = false;
				break;
			case 'DRUG TEST' :
				$isoCode = "SPMC-F-LAB-14";
				$showOtherInfo = false;
				break;
			case 'MYCOBACTERIOLOGY LABORATORY' :
				$isoCode = "SPMC-F-LAB-18";
				$showOtherInfo = false;
				break;
			// end gelie 
			default:
				$isoCode = "SPMC-F-LAB";
		}

		$this->SetFont("Arial", "", "8");

		if($showOtherInfo){
			$this->SetY(-54);
			$this->Ln(5);
			$this->Cell(0,10,'DATE & TIME REQUEST COLLECTED: __________ / ________ AM/PM',0,0,'L');
			$this->Ln(4);
			$this->Cell(0,10,'DATE & TIME OF SAMPLE EXTRACTION: __________ / ________ AM/PM',0,0,'L');
			$this->Ln(6);
			$this->Cell(0,10,'________________________________________',0,0,'L');
			$this->Ln(4);
			$this->Cell(0,10,'EXTRACTED BY (TRODAT AND SIGNATURE)',0,0,'L');
			$this->Ln(4);
		}else{
			$this->SetY(-32);
		}

		$this->Ln(5);
		$this->Ln(4);


        $width = ($this->w - $this->lMargin - $this->rMargin) / 4;
        $this->Cell($width, 10, $isoCode, 0, 0, 'L');
        $this->Cell($width, 10, "Effectivity: July 1, 2015", 0, 0, 'L');
        $this->Cell($width, 10, "Rev. 1", 0, 0, 'C');
        $this->Cell($width, 10, 'Page ' . $this->PageNo() . ' of {nb}', 0, 1, 'R');

	}

	private function getPatientAndRequestInfo(){
		global $db;

		$laboratory = new SegLab;
		$encounter = new Encounter;
		$department = new Department;
		$ward = new Ward;
		$person = new Personell;

		$request = $laboratory->getLabServiceReqInfo($this->referenceNumber);
		$requestDetails = $laboratory->getRequestInfo($this->referenceNumber);
		$encounterInfo = $encounter->getEncounterInfo($request['encounter_nr']);

		$doctor = $person->get_Person_name($requestDetails['request_doctor']);
		$doctorName = $doctor['name_first'] . " " . $doctor['name_2'] . " " . $doctor['name_last'];
		$doctorName = ucwords(strtolower($doctorName));
		$doctorName = htmlspecialchars($doctorName);

		$patientDepartment = '';

		if($request['encounter_nr'] != ''){
			$personName = $db->GetOne("SELECT fn_get_person_lastname_first(?)",$encounterInfo['pid']);
			switch($encounterInfo['encounter_type']){
				case 1 :
					$encounterType = 'ER PATIENT';
					
					$sql_loc = "SELECT el.area_location FROM seg_er_location el WHERE el.location_id = ".$encounterInfo['er_location'];
					$er_location = $db->GetOne($sql_loc);

					if($er_location != '') {
						$sql_lobby = "SELECT eb.lobby_name FROM seg_er_lobby eb WHERE eb.lobby_id = ".$encounterInfo['er_location_lobby'];
						$er_lobby = $db->GetOne($sql_lobby);

						if($er_lobby != '') {
							$location = strtoupper('ER - ' . $er_location . " (" . $er_lobby . ")");
						}
						else {
							$location = strtoupper('ER - ' . $er_location);
						}
					}
					else{
						$location = 'EMERGENCY ROOM';
					}
					
					$currentDept = $ward->getCurrentDept($this->referenceNumber);
					if ($currentDept) {
						while ($result = $currentDept->FetchRow()) {
							$patientDepartment = $result['currentDepartment'];
						}
					}
					break;
				case 2 :
				case self::IPBMOPD:
					if($encounterInfo['encounter_type'] == self::IPBMOPD) $encounterType = "IPBM - OPD";
					else $encounterType = 'OUTPATIENT (OPD)';
					$personDepartment = $department->getDeptAllInfo($encounterInfo['current_dept_nr']);
					$location = strtoupper(strtolower(stripslashes($personDepartment['name_formal'])));
					break;
				case 3:
				case 4:
				case self::IPBMIPD:
					if($encounterInfo['encounter_type'] == self::IPBMIPD) $encounterType = "IPBM - IPD";
					else $encounterType = 'INPATIENT';
					$wardInfo = $ward->getWardInfo($encounterInfo['current_ward_nr']);
					$room_nr = " ROOM #: " . $encounterInfo['current_room_nr'];
					$bed_nr = $ward->getCurrentBedNr($encounterInfo['encounter_nr']);
					$bed = ($bed_nr) ? " BED #: " . $bed_nr : '';
					$location = strtoupper(strtolower(stripslashes($wardInfo['name']))) . $room_nr . $bed;
					$currentDept = $ward->getConsultingDept($this->referenceNumber);
					if ($currentDept) {
						while ($result = $currentDept->FetchRow()) {
							$patientDepartment = $result['currentDepartment'];
						}
					}
					break;
				default:
					$encounterType = '';
					$location = '';
			}
		}else{
			$personName = $db->GetOne("SELECT fn_get_person_lastname_first(?)",$request['pid']);
			$encounterType = "WALKIN";
			$location = "WALKIN";
			$patientDepartment = '';
		}

		$headerInfo = array(
			'referenceNumber' => $this->referenceNumber,
			'name' => $personName,
			'pid' => $request['pid'],
			'age2' => $encounterInfo['age'],
			'patientType' => $encounterType,
			'dateEncoded' => $request['modify_dt']=="0000-00-00 00:00:00" ? $request['create_dt'] : $request['modify_dt'],
			'requestDate' => date("F j, Y", strtotime($request['serv_dt'])) . " at " . date("h:i A", strtotime($request['serv_tm'])),
			'location' => $location,
			'department' => $patientDepartment,
			'datePrinted' => date("Y-m-d H:i:s"),
			'address' => trim($db->GetOne("SELECT fn_get_complete_address(?)",$encounterInfo['pid'])),
			'impression' => $requestDetails['clinical_info'],
			'doctorName' => $doctorName
		);

		return array_merge($encounterInfo,$headerInfo,$request);
	}

}//end class

$claimStub = new LaboratoryClaimStub;
$claimStub->generate();
$claimStub->output();