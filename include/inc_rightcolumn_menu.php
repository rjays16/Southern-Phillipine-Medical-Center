<script type="text/javascript">
    //reports
    function viewReportM() {

        window.open("<?=$root_path?>modules/notice_manager/notice_meeting.php") ;
    }
    function viewReportM2() {

        window.open("<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=view_report_meeting&from=home") ;
    }

    //Added by Borj 2014-08-04 ISO
    //IpdUserManual
    function viewReportO() {
        window.open("<?=$root_path?>modules/notice_manager/notice_orientation.php") ;
        // window.open.href = urlholder;
    }
    function viewReportO2() {
         window.open("<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=view_report_orientation&from=home") ;
        // window.open.href = urlholder;
    }


</script>
<?php

/*------begin------ This protection code was suggested by Luki R. luki@karet.org ---- */
if (eregi("inc_rightcolumn_menu.php",$PHP_SELF)) 
	die('<meta http-equiv="refresh" content="0; url=../">');
/*------end------*/

/* Get the main info data */
$config_type='main_info_%';
require($root_path.'include/inc_get_global_config.php');

#Workaround
$main_info_address=nl2br($main_info_address);


/*added Ryan 05/07/2018*/ 
/* Prepare the url links variables for Notice of Meeting & Orientation */
if ($_SESSION['sess_login_username']) {
        $url_open="javascript:viewReportM()";
        $url_mgmt="javascript:viewReportO()";
    } 
    else {
        $url_open="javascript:viewReportM2()";
        $url_mgmt="javascript:viewReportO2()";
    }
 //Redirect to the page Notice of Meeting
 //Redirect to the page Notice of Orientation



//$url_dept="".$root_path.'modules/news/'."departments.php".URL_APPEND;
//$url_cafe="".$root_path.'modules/cafeteria/'."cafenews.php".URL_APPEND;
//$url_adm="newscolumns.php".URL_APPEND."&dept_nr=33&user_origin=dept";
//$url_exh="newscolumns.php".URL_APPEND."&dept_nr=29&user_origin=dept";
//$url_edu="newscolumns.php".URL_APPEND."&dept_nr=30&user_origin=dept";
//$url_stud="newscolumns.php".URL_APPEND."&dept_nr=31&user_origin=dept";
//$url_phys="newscolumns.php".URL_APPEND."&dept_nr=10&user_origin=dept";
//$url_tips="newscolumns.php".URL_APPEND."&dept_nr=32&user_origin=dept";
//$url_calendar=$root_path."modules/calendar/calendar.php".URL_APPEND."&retpath=home";
$url_jshelp="javascript:gethelp()";
//$url_news="editor-pass.php".URL_APPEND;
//$url_jscredits="javascript:openCreditsWindow()";


/*Edit: ended in this line @Ryan*/



#created by Borj, 04/10/2014 Jasper in Segworks and IHOMP Service Request Form Link
//updated by justin 4/4/42016
$url_segworksforms=$root_path."forms/2016/System Service Request-Rev.3.pdf".URL_APPEND;
$url_ihompforms=$root_path."forms/2016/Technical Assistance Request Form .pdf".URL_APPEND;
$url_spmcconsultantform=$root_path."forms/2016/Information Sheet for SPMC Consultant.pdf".URL_APPEND;
$url_postingform=$root_path."forms/2016/Online Posting Form.pdf".URL_APPEND;
$url_feedbackfacilitatorform=$root_path."forms/2016/Project Feedback for Facilitator.pdf".URL_APPEND;
$url_feedbackform=$root_path."forms/2016/Project Feedback.pdf".URL_APPEND;
$url_deactivationformform=$root_path."forms/2016/User's Account Deactivation Form.pdf".URL_APPEND;
$url_wififorms=$root_path."forms/2016/Wireless-WiFi-Service-Request-Form.pdf".URL_APPEND;

//added by Earl Galope 01/29/2018 -test
$url_personnel_health_servicesforms=$root_path."forms/2016/Personnel Health Services (PHS) Form.pdf".URL_APPEND;

//added by carriane 6/23/17
$url_technicalservicerequest = $root_path."forms/2016/Technical Support Request-24-7.pdf".URL_APPEND;
#$url_ihompforms=$root_path."modules/reports/reports/seg_ihomp_service_request_form.php".URL_APPEND;

//end
# added by gelie 09/19/2015
$url_trainorientform=$root_path."forms/2016/HIS Training and Orientation.pdf".URL_APPEND;
$url_registerform=$root_path."forms/2016/User Account Form.pdf".URL_APPEND;
# end gelie

$url_cf4form=$root_path."forms/PHIC_CF4.pdf".URL_APPEND;
$url_surgeryrequestform=$root_path."forms/SURGERY REQUEST FORM.pdf".URL_APPEND;
$url_screeningform=$root_path."forms/MR PROCEDURE SCREENING FORM FOR PATIENTS.docx".URL_APPEND;
$url_histAssessmentform=$root_path."forms/CONTRAST MEDIA HISTORY AND ASSESSMENT FORM.docx".URL_APPEND;
//$url_covid19cifform=$root_path."forms/COVID-19 CIF v.7 editable -with area-case-hrn.pdf".URL_APPEND;
$url_covid19cifform=$root_path."forms/CIF-Version-9-fillable-with-AREA-CASE-NO-HRN.pdf".URL_APPEND;
$url_annex_e = $root_path."forms/ANNEX-E.pdf".URL_APPEND; //Added by aeron
$TP_com_img_path=$root_path.'gui/img/common';

# Load the template
$tp=&$TP_obj->load('tp_rightcolumn_menu.htm');
# Output display
eval ("echo $tp;");
?>
