<?php
$LDMainTitle='Segworks Hospital Information System';

/*$LDMainTitle='Marienhospital a virtual Integrated Hospital Information System in Internet';
*/
$LDInviteAskMe='Enter your query, for example: "What is the phone number of M9d?"';
$LDTipsLink='Tips:';
$LDTipsAskMe='Try synonyms, for example type "station" in place of "ward".';
$LDTipsImgAlt='Click here for tips on how to get the best results.';
$LDSubmitBut='Send query';

$LDQuickInfo='Quick Informer';
$LDPhonePolice='Police';
$LDPhoneFire='Fire Dept.';
$LDAmbulance='Emergency';
$LDPhone='Phone (Hospital)';
$LDFax='Fax';
$LDAddress='Address';
$LDEmail='Email';

/*Start line*/
/*Edited by Ryan 05/07/2018*/

/*Start line*/
/*Added by Ryan 05/08/2018*/
	
	$curdate = date('Y-m-d h:i A');

	mysql_query("UPDATE seg_notice_tbl SET status='0' WHERE concat(note_date,' ',time_to) < '$curdate' ");

	if($connection = 1) {
	$active   = 1;
	$delete   = 1;
	$categoryMet = 'Meeting';
	$categoryOr = 'Orientation';
		$countMeeting = mysql_query("SELECT count(note_id) FROM seg_notice_tbl WHERE is_deleted = '$delete' AND status = '$active' AND category ='$categoryMet' ") or die(mysql_error());
		while ($rows = mysql_fetch_array($countMeeting)) {
		$total_active_meeting = $rows['count(note_id)'];	
		}

		$countOrientation = mysql_query("SELECT count(note_id) FROM seg_notice_tbl WHERE is_deleted = '$delete' AND status = '$active' AND category ='$categoryOr' ") or die(mysql_error());
		while ($rows = mysql_fetch_array($countOrientation)) {
		$total_active_orientation = $rows['count(note_id)'];	
		}
	}


$LDOpenTimes ='Notice of Meeting'.' '.'<strong>('.$total_active_meeting.')</strong>';
$LDManagement ='Notice of Orientation'.' '.'<strong>('.$total_active_orientation.')</strong>';
/*end line*/

//$LDDept ='Departments';
//$LDCafenews ='Cafeteria News';
//$LDAdmission ='Admission';
//$LDExhibition ='Exhibitions';
//$LDEducation ='Education';
//$LDAdvStudies ='Studies';
//$LDPhyTherapy ='Physical Therapy';
//$LDHealthTips ='Health tips';
//$LDCalendar ='Calendar';
$LDHelp='Help';
$LDMore='more to article';
//$LDSubmitNews='Submit News';

/*Edit ended in this line*/

$LDEditTitle='Headline';
$LDNewsDummy=array(1=>'first',2=>'second',3=>'third',4=>'fourth');

$LDNoFrame='This website uses frames for optimal functioning. Please install a proper browser.  Please activate the Javascript (JScript) and 
						turn on the automatic cookie reception';
						
$LDClk2Write='Click me to submit news';

$LDAlertOldBrowser='Your browser version is older than 5.0!<br> We recommend the version 5.0 or later. <br> Using older versions might lead to
								functional unreliability of the program.';
$LDAlertNoCookie='You or your  browser might have rejected the cookie(s).<br>This program is dependent on them. Otherwise the program will not
							function properly.<br>Please set your browser to automatically accept cookies.<br>';
$LDClkAfter='Afterwards click this.';
$LDGoAheadEgal='Click here to start the program.';
$LDGoAheadEgalCookie='I don\'t want to accept cookies. Go ahead and start the program anyway.';
$LDCookieRef='If you want to know more about cookies you can read the following documents:<br>
						<a href=\'http://www.dtp-aus.com/cookies.htm\' target=\'pp\'>A few words about Cookies (Security and the lies)</a><br>
						<a href=\'http://www.cookiecentral.com/content.phtml?area=4&id=10\' target=\'pp\'>Cookies and Privacy FAQ</a><br>';
$LDPrivPolicy='If you want to read our privacy policy please <a href=\'language/en/en_privacy.htm\' target=\'pp\'>click this:</a>';
$LDOurPrivPolicy='Our Privacy Policy';


#added by Earl 01/29/2018
$LDPersonnelHealthServicesForms='Personnel Health Services (PHS) Form';
#created by Borj, 04/10/2014 Jasper in Segworks and IHOMP Service Request Form

$LDdownload='Downloadable Forms'; #updated by carriane 6/23/17

$LDSegworksRequestForm='System Service Request';
$LDIHOMPRequestForm='ICT and Technical Assistance Request Form';
//end
# added by gelie 09/19/2015
$LDTrainOrientForm='Hospital System Training and Orientation';
$LDRegisterForm="User Account Form"; #updated by carriane 6/23/17
# end gelie
# added by Justin 04/04/2016
// $LDSPMCConsultant		= "Information Sheet for SPMC Consultant"; #updated by carriane 6/23/17
#$LDPostingForm			= "Online Posting Form";
$LDFeedbackFacilitator	= "Training & Orientation Feedback Form";
$LDFeedback 			= "Project Feedback";
// $LDDeactivationForm		= "User's Account Deactivation Form"; #updated by carriane 6/23/17
$LDWifiForm				= "Wireless (WiFi) Service Request Form";
# end gelie

$LDTechnicalSupport = "24/7 Software Maintenance & Support"; # added by Carriane 6/23/18\7
$LDEditNews='Edit & submit news via online editor';
/* 2002-10-17 EL */
$LDCredits='Credits';
/* 2003-05-24 EL */
$LDPublicItems='Public items';
# 2003-08-28 EL
$LDHeadline='Headline';
$LDCF4Form='CF4 Form';
$LDSurgeryRequestForm='Surgery Request Form';
$LDScreeningForm='MR Procedure Screening Form for Patients';
$LDHistAssessmentForm='Contrast Media History & Assessment Form';
$LDCovid19CifForm='COVID-19 CIF v.9 editable'; //Added by Fritz 01/20/2021
$LDAnnexE = 'ANNEX E Form';
?>
