 <?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once($root_path . 'include/inc_environment_global.php');
#$top_dir='modules/registration_admission/certificates';


$PMRF_Report = new PhilhealthPMRF();
$PMRF_Report->processPDFOutput();

/**
 * Handles PMRF form printing with PHIC Member and Patient's Personal details.
 * @author Macoy
 */
class PhilhealthPMRF
{
    var $is_member;
    var $encounter_nr = '';
    var $hcare_id = 0;
    var $base_url = '';

    public function __construct()
    {
        global $db;
        $this->encounter_nr = $db->qstr($_GET['encounter_nr']);
        $this->is_member = $this->checkIfMember();
        $this->hcare_id = $db->qstr($_GET['id']);
    }

    /**
     * checks if phic member
     * @return array
     */

    public function checkIfMember()
    {
        global $db;
        $sql = "SELECT 
                    i.is_principal AS Member 
                FROM 
                    care_person_insurance AS i
                LEFT JOIN care_encounter e 
                    ON e.pid = i.pid
                WHERE e.encounter_nr = $this->encounter_nr";

        $result = $db->Execute($sql);
        if($result)
            $row = $result->FetchRow();
        return $row['Member'];
    }

    function getPrincipalNmFromTmp(){
        global $db;
        $strSQL = "SELECT 
						mi.member_lname AS LastName,
						mi.member_fname AS FirstName,
						mi.member_mname AS MiddleName,
						mi.suffix AS suffix,
						mi.sex AS sex,
						mi.birth_date AS date_birth,
						mi.street_name AS Street,
						sb.brgy_name AS Barangay,
						sg.mun_name AS Municity,
						sg.zipcode AS Zipcode,
						sp.prov_name AS Province,
						sc.country_name AS Country
					FROM
						  seg_insurance_member_info AS mi 
						INNER JOIN care_encounter AS ce 
						    ON mi.pid = ce.pid 
						LEFT JOIN care_person AS p 
						    ON p.pid = mi.pid 
						LEFT JOIN seg_country AS sc 
						    ON p.citizenship = sc.country_code 
						LEFT JOIN seg_barangays AS sb 
						    ON sb.brgy_nr = mi.brgy_nr 
						LEFT JOIN seg_municity AS sg 
						    ON sg.mun_nr = mi.mun_nr 
						LEFT JOIN seg_provinces AS sp 
						    ON sp.prov_nr = sg.prov_nr 
					WHERE ce.encounter_nr = $this->encounter_nr";
        #echo $strSQL; die();
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                return $result;
            }
        }
        return false;
    }

    function getMembersData(){
        global $db;
        $sql_1 = "SELECT 
				  	p.name_last AS LastName,
				  	p.name_first AS FirstName,
				  	p.name_2 AS SecondName,
				  	p.name_middle AS MiddleName,
				  	p.suffix AS suffix,
				  	p.sex AS sex,
				  	p.date_birth AS date_birth,
				  	p.place_birth AS place_birth,
				  	p.civil_status AS civil_status,
				  	p.citizenship AS citizenship,
				  	p.street_name AS Street,
				  	sb.brgy_name AS Barangay,
				  	sg.mun_name AS Municity,
				  	sg.zipcode AS Zipcode,
				  	sp.prov_name AS Province,
				  	sc.country_name AS Country
				FROM care_person AS p 
				  	INNER JOIN care_person_insurance AS i 
				    	ON i.pid = p.pid 
				  	LEFT JOIN care_encounter AS e 
				    	ON e.pid = p.pid 
				  	LEFT JOIN seg_barangays AS sb 
				    	ON sb.brgy_nr = p.brgy_nr 
				  	LEFT JOIN seg_municity AS sg 
				    	ON sg.mun_nr = sb.mun_nr 
				  	LEFT JOIN seg_provinces AS sp 
				    	ON sp.prov_nr = sg.prov_nr 
				  	LEFT JOIN seg_country AS sc 
				    	ON p.citizenship = sc.country_code 
				WHERE i.hcare_id = $this->hcare_id
				  	AND i.is_principal = 1
				  	AND e.encounter_nr = $this->encounter_nr";
        #echo $sql_1; die();
        if ($result = $db->Execute($sql_1)) {
            if ($result->RecordCount()) {
                return $result;
            }
        }
        return false;
    }

    function processPDFOutput(){

        if ($this->is_member) {
            $result = $this->getMembersData();
            #echo '1'; exit();
        }else{
                if (!($result = $this->getPrincipalNmFromTmp())) 
                    $result = false;
                     #echo '2'; exit();
        }
        $top_dir = 'modules';
        $baseurl = sprintf(
			"%s://%s%s",
			isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
			$_SERVER['SERVER_ADDR'],
			substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
		);
        #Initializations
        $params = array();
        $data[0] = array();
        $data[0]['image_01'] = $baseurl . "gui/img/logos/PHIC_PMRF.png";    
		
        if ($result) {

            $mem = $result->FetchRow(); 

            if(($mem['Barangay'] == 'NOT PROVIDED') || ($mem['Province'] == 'NOT PROVIDED')){
                    $Barangay= '';
                    $Province= '';
            }else{
                    $Barangay = $mem['Barangay']; 
                    $Province = $mem['Province'];
            }

            if($mem['citizenship']=='PH'){
					$nationality = 'Filipino';
			}

			if($mem['sex']=='m'){
					$isMale = 'X';
			}elseif($mem['sex']=='f'){
					$isFemale = 'X';
			}

			if($mem['civil_status']=='single'){
					$isSingle = 'X';
			}elseif($mem['civil_status']=='married'){
					$isMarried = 'X';
			}elseif($mem['civil_status']=='widowed'){
					$isWidow = 'X';
			}elseif(($mem['civil_status']=='divorced')||($mem['civil_status']=='separated')){
					$isLegal = 'X';
			}

           $params = array(
						"lastname" 		=> strtoupper($mem['LastName']),
						"firstname" 	=> strtoupper($mem['FirstName'] . ' ' . $mem['SecondName']),
						"suffix" 		=> strtoupper($mem['suffix']),
						"middlename" 	=> strtoupper($mem['MiddleName']),
						"dateofbirth" 	=> date("m/d/Y", strtotime($mem['date_birth'])),
						"placeofbirth" 	=> strtoupper($mem['place_birth']),
						"sexM" 			=> $isMale,
						"sexF" 			=> $isFemale,
						"civilstatusS" 	=> $isSingle,
						"civilstatusM" 	=> $isMarried,
						"civilstatusW" 	=> $isWidow,
						"civilstatusL" 	=> $isLegal,
						"nationality" 	=> strtoupper($nationality),
						"street" 		=> strtoupper($mem['Street']),
						"barangay" 		=> strtoupper($Barangay),
						"municipality" 	=> strtoupper($mem['Municity']),
						"province" 		=> strtoupper($Province),
						"country" 		=> strtoupper($mem['Country']),
						"zipcode" 		=> strtoupper($mem['Zipcode'])
			);


        } else { 
            #if failed, or other scenario..
        }
		#render report -------------
        showReport('PMRF_Report', $params, $data, 'PDF');
    }

}
?>