<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/*
CARE2X Integrated Information System Deployment 2.1 - 2004-10-02 for Hospitals and Health Care Organizations and Services
Copyright (C) 2002,2003,2004,2005  Elpidio Latorilla & Intellin.org

GNU GPL. For details read file "copy_notice.txt".
*/
# added by : syboy 02/08/2016 : meow
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
# ended syboy
$lang_tables=array('personell.php');
define('LANG_FILE','aufnahme.php'); 
$local_user='aufnahme_user';
// var_dump(); die();
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');
// start modified by Mark Ryan Guerra 3/15/2018 -->
require_once($root_path.'include/care_api_classes/class_access.php');


global $db;
$sql = "SELECT * FROM care_users WHERE personell_nr='".$personell_nr."' LIMIT 1";
$rs = $db->Execute($sql);
$row = $rs->FetchRow();  
if(!empty($row['login_id'])){
        $with_access = 1;
        $userid = $row['login_id'];
        $username = '';
        $lockflag = $row['lockflag'];
}

//echo $userid;
$user = & new Access($userid);

//var_dump($user);
//locked if zero
//var_dump( $usernamex);

//var_dump($userid);
//var_dump($user->isNotLocked());

   if($lockflag==1)
        $cheker = 1;
    else
        $cheker= 0;

 // end modified by Mark Ryan Guerra -->

//require_once($root_path.'include/care_api_classes/class_person.php');
//require_once($root_path.'include/care_api_classes/class_insurance.php');
//require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
$_SESSION['DEACTIVATION_TIME_IN'] = new DateTime();

$GLOBAL_CONFIG=array();

$thisfile=basename(__FILE__);
if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $breakfile=$root_path.'main/spediens.php'.URL_APPEND;
	else $breakfile='personell_admin_pass.php'.URL_APPEND.'&target='.$target;

$personell_obj=new Personell();
$dept_obj=new Department;
//$person_obj=new Person();
//$insurance_obj=new Insurance;
//$ward_obj=new Ward;
/* Get the personell  global configs */
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('personell_%');
$glob_obj->getConfig('person_foto_path');

$updatefile='personell_register.php';

/* Default path for fotos. Make sure that this directory exists! */
$default_photo_path=$root_path.'fotos/registration';
$photo_filename='nopic';

#Check whether the origin is phone directory and if session personnel nr. is ok
if($HTTP_SESSION_VARS['sess_user_origin']=='phonedir'&&$HTTP_SESSION_VARS['sess_personell_nr']){
	$personell_nr=$HTTP_SESSION_VARS['sess_personell_nr'];
}else{
	$HTTP_SESSION_VARS['sess_personell_nr']=$personell_nr;
}

	//if(!empty($GLOBAL_CONFIG['patient_financial_class_single_result'])) $encounter_obj->setSingleResult(true);
	$personell_obj->loadPersonellData($personell_nr);
	if($personell_obj->is_loaded) {
		$row=&$personell_obj->personell_data;
#echo "personell_register_show.php : row : <br> \n"; print_r($row); echo" <br> \n";
		//load data
		//while(list($x,$v)=each($row)) {$$x=$v;}
		extract($row);
		$deptOfDoc = $dept_obj->getDeptofDoctor($personell_nr); # burn added: May 28, 2007
        $personell_status  = $personell_obj->getStatusPersonnel($personell_nr);
#echo "personell_register_show.php : deptOfDoc : <br> \n"; print_r($deptOfDoc); echo" <br> \n";
		//$insurance_class=&$encounter_obj->getInsuranceClassInfo($insurance_class_nr);
		//$encounter_class=&$encounter_obj->getEncounterClassInfo($encounter_class_nr);

		//if($data_obj=&$person_obj->getAllInfoObject($pid))
/*		$list='title,name_first,name_last,name_2,name_3,name_middle,name_maiden,name_others,date_birth,
						 sex,addr_str,addr_str_nr,addr_zip,addr_citytown_nr,photo_filename';

		$person_obj->setPID($pid);
		if($row=&$person_obj->getValueByList($list))
		{
			while(list($x,$v)=each($row))	$$x=$v;
		}

		$addr_citytown_name=$person_obj->CityTownName($addr_citytown_nr);
		$encoder=$encounter_obj->RecordModifierID();

*/	}

	include_once($root_path.'include/inc_date_format_functions.php');

	/* Update History */
	//if(!$newdata) $encounter_obj->setHistorySeen($HTTP_SESSION_VARS['sess_user_name'],$encounter_nr);
	/* Get insurance firm name*/
	//$insurance_firm_name=$insurance_obj->getFirmName($insurance_firm_id);
	/* Get ward name */
	//$current_ward_name=$ward_obj->WardName($current_ward_nr);
	/* Check whether config path exists, else use default path */
	$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;


/* Prepare text and resolve the numbers */
require_once($root_path.'include/inc_patient_encounter_type.php');

if(!session_is_registered('sess_parent_mod')) session_register('sess_parent_mod');
if(!session_is_registered('sess_user_origin')) session_register('sess_user_origin');

/* Save encounter nrs to session */
$HTTP_SESSION_VARS['sess_pid']=$pid;
//$HTTP_SESSION_VARS['sess_en']=$encounter_nr;
//$HTTP_SESSION_VARS['sess_full_en']=$full_en;
$HTTP_SESSION_VARS['sess_parent_mod']='admission';
$HTTP_SESSION_VARS['sess_pnr']=$personell_nr;
//$full_pnr=$personell_nr+$GLOBAL_CONFIG['personell_nr_adder'];
$full_pnr=$personell_nr;
$HTTP_SESSION_VARS['sess_full_pnr']=$full_pnr;
$HTTP_SESSION_VARS['sess_user_origin']='personell_admin';

/* Prepare the photo filename */
require_once($root_path.'include/inc_photo_filename_resolve.php');

#added by VAN 11-04-09
#$sql = "SELECT p.* FROM care_personell_assignment as p where personell_nr='$personell_nr'
#				ORDER BY modify_time DESC LIMIT 1";
// $sql = "SELECT p.* FROM care_personell as p where pid='$pid'
// 				ORDER BY modify_time DESC LIMIT 1";
#updated by Carriane 07/05/17
$sql = "SELECT cp.death_date AS death_status, p.* FROM care_personell as p LEFT JOIN care_person cp ON p.pid = cp.pid WHERE p.pid='$pid' ORDER BY modify_time DESC LIMIT 1";


#echo "string".$sql;
$rs = $db->Execute($sql);

$row_per = $rs->FetchRow();

// end




$personnel_type = substr($short_id,0,1);

require($root_path.'modules/personell_admin/ajax/accre-insurance.common.php');
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>

<!---------added by VAN----------->
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<link type="text/css" href="<?=$root_path?>js/jquery/css/jquery-ui.css" rel="stylesheet">
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<input type="hidden" id="accsp" name="accsp" value="<? echo $accessPermission ?>">

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
// var $J = jQuery.noConflict();
// added by : syboy 03/30/2016 : meow
$(function() {
    (function($){
        $.extend({
            APP : {                
                formatTimer : function(a) {
                    if (a < 10) {
                        a = '0' + a;
                    }                              
                    return a;
                },    
                startTimer : function(dir) {
                    var a;
                    $.APP.dir = dir;
                    $.APP.d1 = new Date();
                    switch($.APP.state) {
                        default :
                            $.APP.t1 = $.APP.d1.getTime(); 
                        break;
                    }                                   
                    $.APP.state = 'alive';   
                    $.APP.loopTimer();
                },
                
                loopTimer : function() {
                    var td;
                    var d2,t2;
                    var ms = 0;
                    var s  = 0;
                    var m  = 0;
                    var h  = 0;
                    
                    if ($.APP.state === 'alive') {
                        d2 = new Date();
                        t2 = d2.getTime();   
                        if ($.APP.dir === 'sw') {
                            td = t2 - $.APP.t1;
                        } else {
                            td = $.APP.t1 - t2;
                        }    
                        ms = td%1000;
                        if (ms < 1) {
                            ms = 0;
                        } else {    
                            s = (td-ms)/1000;
                            if (s < 1) {
                                s = 0;
                            } else {
                                var m = (s-(s%60))/60;
                                if (m < 1) {
                                    m = 0;
                                } else {
                                    var h = (m-(m%60))/60;
                                    if (h < 1) {
                                        h = 0;
                                    }                             
                                }    
                            }
                        }
                        ms = Math.round(ms/100);
                        s  = s-(m*60);
                        m  = m-(h*60);                                
                        $('#' + $.APP.dir + '_ms').html($.APP.formatTimer(ms));
                        $('#' + $.APP.dir + '_s').html($.APP.formatTimer(s));
                        $('#' + $.APP.dir + '_m').html($.APP.formatTimer(m));
                        $('#' + $.APP.dir + '_h').html($.APP.formatTimer(h));
                        $.APP.t = setTimeout($.APP.loopTimer,1);
                        $('#durationTime').val($.APP.formatTimer(h)+':'+$.APP.formatTimer(m)+':'+$.APP.formatTimer(s)+' '+$.APP.formatTimer(ms));
                        $('#durationTime2').val($.APP.formatTimer(h)+':'+$.APP.formatTimer(m)+':'+$.APP.formatTimer(s)+' '+$.APP.formatTimer(ms));
                    
                    } else {
                        clearTimeout($.APP.t);
                        return true;
                    }  
                }
            }    
        });
              
    })(jQuery);
});
// ended syboy
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa;
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc;
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px;
	font-weight:bold;
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

/*a {color:#338855;font-weight:bold;}*/
a {color:#338855;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style>


<script  language="javascript">
	function Dependents(){
		/*
		return overlib(
					OLiframeContent('../../modules/dependents/seg-dependents.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&pid=<?=$pid?>',
									800, 440, 'fGroupTray', 0, 'auto'),
											WIDTH,800, TEXTPADDING,0, BORDER,0,
									STICKY, SCROLL, CLOSECLICK, MODAL,
									CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
										 CAPTIONPADDING,2, CAPTION,'Dependents',
										 MIDX,0, MIDY,0,
										 STATUS,'Dependents');
						*/
		return overlib(
					OLiframeContent('../../modules/dependents/seg-dependent-pass.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&target=dependents&popUp=1&pid=<?=$pid?>&department=<?php echo $_GET['department'];?>',
									800, 440, 'fGroupTray', 0, 'auto'),
											WIDTH,800, TEXTPADDING,0, BORDER,0,
									STICKY, SCROLL, CLOSECLICK, MODAL,
									CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="ReloadWindow();">',
										 CAPTIONPADDING,2, CAPTION,'Dependents',
										 MIDX,0, MIDY,0,
										 STATUS,'Dependents');
	}
	// UPDATED by: syboy 03/30/2016 : meow
	function deactivatePersonnel(personell_nr,deactivate){
		var prompt;
		var remarks;
		var remarks_txt;
		$.APP.startTimer('sw');

		if (deactivate==1)
			prompt = 'deactivate';
		else
			prompt = 'activate';

		res = confirm('Are you really sure to '+prompt+' the personnel\'s employment status?');
		durationTime = $("#durationTime2").val();

		if (res){
            if (deactivate==1) {
				$( "#remarksDialog" ).dialog({
                    autoOpen: true,
                    modal:true,
                    show: "blind",
                    hide: "explode",
                    title: "Remarks",
                    position: "top", //added by VAN 12-19-2012 
                    buttons: {
                        OK: function() {
                            alert("The personnel's employment status is successfully change.");
                            // Modified by: JEFF
                        	// Date: August 10, 2017
                        	// Purpose: To trap "Blank" values in remarks text area
                            remarks = $("#remarks").val();
                            remarks_txt = $("#txtRemarks").val();

                            if (remarks_txt != "") {
                            	remarks_txt = " - " + remarks_txt;
                            }else{
                                remarks_txt = remarks_txt;
                            }
			                
                            xajax_setDeactivatePersonnel(personell_nr,deactivate,remarks,remarks_txt,durationTime);
                        },
                        Cancel: function() {
                            $( this ).dialog( "close" );
                        }
                    },
            		// Added by: JEFF
					// Date: August 10, 2017
					// Purpose: To use JQuery for tagging the textfield for remarks
                    open: function(){

                    	$('#remarks').val('resign');
                    	$('#txtRemarks').hide();
	                        
						$('#remarks').change(function(){

							var remarks_holder = $('#remarks').val();

							if (remarks_holder == 'doubleentry') {

								$('#txtRemarks').show();
								$('#txtRemarks').val("");
								$('#remarks').val(remarks_holder);	
							}
							else{
								$('#txtRemarks').hide();
							}
						});
						// End by: JEFF
                    },
                    close: function() {
                         $( this ).dialog( "close" );
                    }
                        
                });
			}else{
                $( "#remarksDialogActivate" ).dialog({
                    autoOpen: true,
                    modal:true,
                    show: "blind",
                    hide: "explode",
                    title: "Remarks",
                    position: "top", //added by VAN 12-19-2012 
                    buttons: {
                        OK: function() {
                            alert("The personnel's employment status is successfully change.");
                            
                            remarks = $("#activateRemarks").val()
                            // Modified by: JEFF
                            // Date: 08-12-17
                            // Purpose: remarks typed in double entry/error textfield get data
                            remarks_txt = "";
                                xajax_setDeactivatePersonnel(personell_nr,deactivate,remarks,remarks_txt,durationTime);
                        },
                        Cancel: function() {
                            $( this ).dialog( "close" );
                        }
                    },
                    close: function() {
                         $( this ).dialog( "close" );
                    }
                        
                });
			}
		}
	}

	function changePassword(personell_nr){
			var password;
			res = confirm('Are you really sure to change the user\'s password?'); 
               if (res){
               		$.APP.startTimer('sw');  
                    $( "#passwordDialog" ).dialog({
                        autoOpen: true,
                        modal:true,
                        show: "blind",
                        hide: "explode",
                        title: "Change password",
                        position: "top", //added by VAN 12-19-2012 
                        buttons: {
                                OK: function() {
                                    password = $("#password").val()
                                    durationTime = $("#durationTime").val() // added by: syboy 03/30/2016 : meow
                                    xajax_setChangePassword(personell_nr,password,durationTime);
                                },
                                Cancel: function() {
                                    $( this ).dialog( "close" );
                                }
                        },
                        close: function() {
                         $( this ).dialog( "close" );
                    }
                        
                    });
			        
					        
		        }
	}

	function showPermission(personell_nr,with_access,userid,username){
			var location;

			if (with_access==1){
				location ='../../modules/system_admin/edv_user_access_edit.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&mode=edit&userid='+userid;
			}else{
				location ='../../modules/system_admin/edv_user_access_edit.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_employee=1&personell_nr='+personell_nr+'&username='+username+'&userid='+userid;
			}

			return overlib(
					OLiframeContent(location,
																	800, 440, 'fGroupTray', 0, 'auto'),
																	WIDTH,800, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="ReloadWindow();">',
																 CAPTIONPADDING,2, CAPTION,'User Permission',
																 MIDX,0, MIDY,0,
																 STATUS,'User Permission');

	}



// start - added by Mark Ryan Guerra 3-15-2018
    function accessMe(){
        var acc_Perm = <?php echo $cheker?>;
      
        if(acc_Perm==true){
             return true;
        }
        else{
            return false;
        }
    }
    // end  by Mark Ryan Guerra
	function showOrientation(personell_nr,with_access,userid,username,seePermission){
			var location;
            if(seePermission == 0){
                alert('Permission is locked.');
            }else{
			if (1==1){
				location ='../../modules/personell_admin/orientation.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&mode=edit&personell_nr='+personell_nr;
			}else{
				location ='../../modules/personell_admin/orientation.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_employee=1&personell_nr='+personell_nr+'&username='+username+'&userid='+userid;
			}

			return overlib(
					OLiframeContent(location,
												800, 440, 'fGroupTray', 0, 'auto'),
												WIDTH,800, TEXTPADDING,0, BORDER,0,
													STICKY, SCROLL, CLOSECLICK, MODAL,
													CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="ReloadWindow();">',
											 CAPTIONPADDING,2, CAPTION,'Orientation',
											 MIDX,0, MIDY,0,
											 STATUS,'Orientation');
        }
	}


    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function openwebexdialog(personell_nr,create){
        if(create == 1)
            title = "Create Webex Account";
        else title = "Update Webex Account";

        $("#webexDialog").dialog({
            autoOpen: true,
            modal:true,
            show: "blind",
            hide: "explode",
            title: title,
            position: "top", 
            buttons: {
                OK: function() {
                    webexUser = $("#webexEmail").val()
                    webexpass = $("#webexPass").val() 
                    docPath = $('#url_webex').val()

                    confirmmsg = confirm('Are you sure you want to save changes?');

                    if(confirmmsg){
                        if(webexUser.trim()!='' && webexpass.trim()!=''){
                            if(validateEmail(webexUser.trim())){
                                xajax_setWebexAccount(personell_nr,webexUser,webexpass,create);
                                $.ajax({  
                                    type: 'GET',
                                    url: docPath,
                                    data: {
                                        webexUser : webexUser,
                                        webexpass : webexpass,
                                        personnel_id : personell_nr
                                    },
                                    dataType: 'json',
                                    success: function(data){
                                        console.log(data);
                                        if(data.success == true){
                                            alert("Successfully saved changes");
                                        }else{
                                            alert("There was an error saving your changes. "+data.errors);
                                        }
                                        ReloadWindow();
                                    }   
                                });

                            }else{
                                alert("Please input correct email address");
                                return false;
                            }
                      
                        }else{
                           alert("Please input email address or password");
                           return false;
                        }
                    }
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                $( this ).dialog( "close" );
            }
                        
        });
    }

    function openMessengerIdInputDialog(personell_nr,create) {
        if(create == 1)
            title = "Add FB Messenger ID";
        else title = "Update FB Messenger ID";

        $("#dialogInputFbMsngrId").dialog({
            autoOpen: true,
            modal:true,
            show: "blind",
            hide: "explode",
            title: title,
            position: "top", 
            buttons: {
                OK: function() {
                    fbUserId = $("#fbUserId").val()
                    confirmmsg = confirm('Are you sure you want to save changes?');

                    if(confirmmsg){
                        if(fbUserId.trim()!=''){
                            let loc = window.location;
                            $.ajax({  
                                type: 'POST',
                                url: '/' + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/online/updatePersonnelChatId',
                                data: {
                                    fb_userid : fbUserId,
                                    personnel_id : personell_nr
                                },
                                dataType: 'json',
                                success: function(data){
                                    console.log(data);
                                    if(data.success == true){
                                        alert("Successfully saved changes");
                                    }else{
                                        alert("There was an error saving your changes. "+data.errors);
                                    }
                                    ReloadWindow();
                                }   
                            });                      
                        }else{
                           alert("Please input valid FB Messenger ID!");
                           return false;
                        }
                    }
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                $( this ).dialog( "close" );
            }
                        
        });
    }        

	function ReloadWindow(){
			location.reload();
	}


</script>

<?php
/* Load the GUI page */
#echo "van = $thisfile";
require('./gui_bridge/default/gui_'.$thisfile);
?>
<!--Added by jarel    -->
<input type="hidden" id="durationTime2" name="durationTime2" />
<div class="segPanel" id="passwordDialog" style="display:none" align="left">

   <h3><span>Enter New Password</span></h3>
    <div align="center" style="overflow:hidden">
       <input type="password" name="password" id="password" value="">
         <br/>
         <br/>
        <!-- added by: syboy 03/29/2016 : meow --> 
	 	<div align="right">
	 		<input type="hidden" id="durationTime" name="durationTime" />
	 		<u style="margin-right:55px;"><span name="sw_h" id="sw_h">00</span>:<span name="sw_m" id="sw_m">00</span>:<span name="sw_s" id="sw_s">00</span> <span name="sw_ms" id="sw_ms">00</span></u>
	 		<br />
	 		<span style="margin-right:50px;">Time Duration</span>
	 	</div>
	 	<!-- ended syboy -->
    </div>
</div>
<?php 
$sqldeacrem = "SELECT * FROM seg_deactivate_remarks WHERE is_deleted <> 1 ORDER BY name ASC";
$exec = $db->Execute($sqldeacrem); 

 ?>
<div class="segPanel" id="remarksDialog" style="display:none" align="left">
   <h3><span>Enter Remarks</span></h3>

    <div align="center" style="overflow:hidden">
       <select id="remarks">
            <!-- <option value="Awol">Awol</option>
            <option value="Deceased">Deceased</option>
            <option value="Dismissal">Dismissal</option>
            <option value="Double Entry/Error">Double Entry/Error</option>
            <option value="End of Contract">End of Contract</option>
            <option value="End of Duty">End of Duty</option>
            <option value="Residency Completed/Graduated">Residency Completed/Graduated</option>
            <option value="Resignation">Resignation</option>
            <option value="Retired">Retired</option>    
            <option value="Terminal Leave">Terminal Leave</option>
            <option value="Termination">Termination</option> -->
            <?php 
                while($data = $exec->FetchRow()){
            ?>
                    <option value="<?=$data['code']?>"><?php echo $data['name'] ?></option>
            <?php
                }
             ?>
        </select>
         <br/>
         <br/>
        <textarea id="txtRemarks" style="width: 240px;" placeholder="Your remarks here"></textarea>
        <br/>
    </div>

    <div class="segPanel" id="remarksDialogActivate" style="display:none" align="left">
   <h3><span>Enter Remarks</span></h3>
    <div align="center" style="overflow:hidden">
       <input type="text" name="activateRemarks" id="activateRemarks" value="">
        </select>
         <br/>
         <br/>
    </div>
</div>

<?php 
$sqlWebex = "SELECT * from seg_doctor_meeting as sdm WHERE sdm.doctor_id='".$personell_nr."'";
$rsWeb = $db->Execute($sqlWebex);
$row_web = $rsWeb->FetchRow();
?>

<input type="hidden" id="url_webex" name="url_webex" value="<?php echo $root_path."index.php?r=onlineConsult/doctor/CreateWebexDoctor" ?>" />
<div class="segPanel" id="webexDialog" style="display:none" align="left">
   <h4><span>Enter Webex Details</span></h4>
    <div align="center" style="overflow:hidden">
     <span>E-mail:&nbsp;&nbsp;&nbsp;&nbsp;</span>
     <input type="text" name="webexEmail" id="webexEmail" value="<?php echo ($row_web['webex_id'] ? $row_web['webex_id'] : '')?>">
     <br></br>
     <span>Password: </span> <input type="password" name="webexPass" id="webexPass" value="">
        </div>
</div>

<div class="segPanel" id="dialogInputFbMsngrId" style="display:none" align="left">
   <h4><span>Enter FB Messenger ID</span></h4>
    <div align="center" style="overflow:hidden">
        <span>User ID:&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <input type="text" name="fbUserId" id="fbUserId" value="<?php echo ($fb_userid ? $fb_userid : '')?>">
    </div>
</div>