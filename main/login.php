<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'include/care_api_classes/notification/Notification.php');
require($root_path . 'include/care_api_classes/telemed/Telemed.php');
// require($root_path . 'include/care_api_classes/API/curl_api.php');
/**
 * CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
 * GNU General Public License
 * Copyright 2002,2003,2004,2005 Elpidio Latorilla
 * elpidio@care2x.org,
 *
 * See the file "copy_notice.txt" for the licence notice
 */


define('LANG_FILE', 'stdpass.php');
define('NO_2LEVEL_CHK', 1);
define('_INIT_REPORT_FILE','loadingreport');

require_once($root_path . 'include/inc_front_chain_lang.php');
// reset all 2nd level lock cookies
require($root_path . 'include/inc_2level_reset.php');

$ptoken = isset($_REQUEST['ptoken']) ? $_REQUEST['ptoken'] : '';
$no_encryption = false;

if ($_REQUEST['forward']){
	$fileforward = $root_path . $_REQUEST['forward'];
}else if ($_SESSION['loading_report_link']){
        require_once($root_path . 'include/care_api_classes/class_user_token.php');
        $_personnel_nr = $_REQUEST['personnel_nr'];
        $_token = $ptoken;

        $userToken = new UserToken($_personnel_nr,$_token);

        if($userToken->verifyUserToken()){
            require_once($root_path . 'include/care_api_classes/class_user.php');
            $_user = new SegUser;
            $pass = "check";
            $_userInfo = $_user->getUserInfo($_personnel_nr);
            if ($_userInfo){
                $userid = $_userInfo['login_id'];
                $keyword = $_userInfo['password'];
                $no_encryption = true;
            }

            $fileforward = $_SESSION['loading_report_link'];

        }

}else
	$fileforward = 'login-pc-config.php' . URL_REDIRECT_APPEND;

$thisfile = 'login.php';

if ($_REQUEST['break'])
	$breakfile = $root_path . $_REQUEST['break'];
else
	$breakfile = 'startframe.php' . URL_APPEND;

if (!isset($pass)) $pass = '';
if (!isset($keyword)) $keyword = '';
if (!isset($userid)) $userid = '';

if (!session_is_registered('sess_login_userid')) session_register('sess_login_userid');
if (!session_is_registered('sess_login_username')) session_register('sess_login_username');
if (!session_is_registered('sess_login_pw')) session_register('sess_login_pw');
if (!session_is_registered('sess_onesignal_id')) session_register('sess_onesignal_id');

function logentry($userid, $key, $report)
{
	$logpath = 'logs/access/' . date('Y') . '/';
	if (file_exists($logpath)) {
		$logpath = $logpath . date('Y_m_d') . '.log';
		$file = fopen($logpath, 'a');
		if ($file) {
			if ($userid == '') $userid = 'blank';
			$line = date('Y-m-d H:i:s') . ' ' . 'Main Login: ' . $report . '  Username=' . $userid . '  UserID=' . $key;
			fputs($file, $line);
			fputs($file, "\r\n");
			fclose($file);
		}
	}
}

function checkPermission($permission){
	if (
		in_array('_a_0_all', $permission) ||
		in_array('_a_1_dietary_clinical_sudomanage', $permission) ||
		in_array('_a_1_dietary_administrative_sudomanage', $permission) ||
		in_array('_a_1_dietary_counseling_sudomanage', $permission) ||
		in_array('_a_1_dietary_report_launcher_sudomanage', $permission) ||
		in_array('_a_2_dietary_diet_tags_sudomanage', $permission) ||
		in_array('_a_2_dietary_diet_list_sudomanage', $permission) ||
		in_array('_a_2_dietary_meal_census_sudomanage', $permission) ||
		in_array('_a_2_dietary_census_summary_sudomanage', $permission) ||
		in_array('_a_2_dietary_daily_patient_census_sudomanage', $permission)
		) {
		return true;
	}

	return false;
	// print_r($permission);die();
}

if ((($pass == 'check') && ($keyword != '')) && ($userid != '')) {
	include_once($root_path . 'include/care_api_classes/class_access.php');
	include_once($root_path . 'include/care_api_classes/API/curl_api.php');
	$user = &new Access($userid, $keyword,$no_encryption);

	if ($user->isKnown() && $user->hasValidPassword()) {
		if ($user->isNotLocked()) {
			
			$notif = Notification::instance();
			$resp = $notif->registerUser($userid);
			$_SESSION['token'] = $resp['token'];
			$HTTP_SESSION_VARS['token']  = $resp['token'];
			$userpermissions = explode(' ', $user->PermissionAreas());
			$isregistered = 1;
			if(in_array('_a_1_opdonlinerequest', $userpermissions) || in_array('_a_0_all', $userpermissions)){
				$telemed = Telemed::instance();

				if($onesignalidLogin != '' && $onesignalidLogin != NULL){
					$telemed = Telemed::instance();
					$register = $telemed->registerPlayer($userid, $onesignalidLogin);
					$register = json_decode($register);

					if($register->status)
						$HTTP_SESSION_VARS['sess_onesignal_id'] = $onesignalidLogin;
					else{
						unset($HTTP_SESSION_VARS);
						$isregistered = 0;
					}
				}else{
					// $isregistered = 0; // optional onesignal
				}

			}

			if($isregistered){
				$HTTP_SESSION_VARS['sess_login_userid'] = $user->LoginName();
				$HTTP_SESSION_VARS['sess_login_username'] = $user->Name();
				$HTTP_SESSION_VARS['sess_user_name'] = $user->Name();
				$HTTP_SESSION_VARS['sess_login_personell_nr'] = $user->personellNr();

				$HTTP_SESSION_VARS['sess_temp_userid'] = $user->LoginName(); 	// SEGWORKS: August 3, 2006 2:56 pm, added by AJMQ
				$HTTP_SESSION_VARS['sess_user_personell_nr'] = $user->personellNr(); 	// SEGWORKS: September 14, 2007 4:13 pm, added by burn
				$HTTP_SESSION_VARS['sess_permission'] = $user->PermissionAreas(); 	// SEGWORKS: October 4, 2007 4:57 pm, added by burn

				if (!$ptoken){
					require_once($root_path . '/include/care_api_classes/class_user_token.php');
					$user_Token = new UserToken;
					$user_Token->personnel_nr = $user->personellNr();
					$HTTP_SESSION_VARS['sess_access_ptoken'] = $user_Token->createRandToken();
				}
				# Init the crypt object, encrypt the password, and store in cookie
				$enc_login = new Crypt_HCEMD5($key_login, makeRand());

				$cipherpw = $enc_login->encodeMimeSelfRand($keyword);

				$HTTP_SESSION_VARS['sess_login_pw'] = $cipherpw;
				$checkIfDietary = explode(' ', $user->PermissionAreas());
				if (
					checkPermission($checkIfDietary)
				) {
					$curl_obj = new Rest_Curl;
					$stoken = $curl_obj->storeLoginDietary($user->LoginName(), $keyword);
					$user->insertSession($HTTP_SESSION_VARS['sess_login_personell_nr'], 'login');
					$HTTP_SESSION_VARS['sess_token_type'] = $stoken->token_type;
					$HTTP_SESSION_VARS['sess_expires_in'] = $stoken->expires_in;
					$HTTP_SESSION_VARS['sess_access_token'] = $stoken->access_token;
					// if(in_array('_a_0_all', $checkIfDietary)){
					// 	$HTTP_SESSION_VARS['sess_job_type'] = 'admin';	
					// }else if(in_array('_a_1_dietary_clinical_sudomanage', $checkIfDietary)){
					// 	$HTTP_SESSION_VARS['sess_job_type'] = 'clinical';	
					// }else{
					// 	$HTTP_SESSION_VARS['sess_job_type'] = 'administrative';	
					// }
					// $url = DIETARY_URL."/#/LoginHis/sijuneroyugbenderugmatsugarielugjayveeugcarlaraangnakabaloaningaurl/$username/$password";
					// echo "<script>window.open('" . $url . "', 'popUpWindow', 'width=100,height=100')</script>";
					
		// $activeLogout = $user->checkActiveLogout($HTTP_SESSION_VARS['sess_user_personell_nr']);
					// var_dump($activeLogout);die;
					
					// print_r($HTTP_SESSION_VARS['sess_job_type']);
					// die();
				}


				# Set the login flag
				setcookie('ck_login_logged' . $sid, 'true', 0, '/');

				logentry($user->Name(), $user->LoginName(), $REMOTE_ADDR . " OK'd", "", "");
				if ($_SESSION['loading_report_link'])
					header("Location: $fileforward");
				?>
				<script type="text/javascript">
					console.log(window.parent);
					if (window.parent) {
						window.parent.$('banner').contentDocument.location.reload(true);
						window.location.href = '<?php echo addslashes($fileforward) ?>';
					}
					localStorage.setItem('seghis-login', 1);
				</script>
				<?php
				exit;
			}else{
				$passtag = 4;
			}

			#header("Location: $fileforward");
		} else {
			$passtag = 3;
		}
	} else {
		$passtag = 1;
	}
}

$errbuf = 'Log in';
$minimal = 1;
require($root_path . 'include/inc_passcheck_head.php');
?>

<?php echo setCharSet(); ?>
<!-- window.parent.document.getElementById(\'banner\').contentDocument.getElementById(\'logout_link\').style.display=\'none\';window.parent.document.getElementById(\'banner\').contentDocument.getElementById(\'login_link\').style.display=\'inline\'; -->

<BODY onLoad="window.parent.document.getElementById('banner').contentDocument.getElementById('logout_link').style.display='none';window.parent.document.getElementById('banner').contentDocument.getElementById('login_link').style.display='inline';window.parent.document.getElementById('banner').contentDocument.getElementById('login_username').innerHTML='';<?php if (isset($is_logged_out) && $is_logged_out) echo "localStorage.setItem('seghis-login', 0);window.parent.location.reload(true);"; ?>document.passwindow.userid.focus();" bgcolor=<?php echo $cfg['body_bgcolor']; ?> <?php if (!$cfg['dhtml']) {
																																																																																																																																																	echo ' link=' . $cfg['idx_txtcolor'] . ' alink=' . $cfg['body_alink'] . ' vlink=' . $cfg['idx_txtcolor'];
																																																																																																																																																} ?>>
	&nbsp;
	<p>
		<?php
		if (isset($is_logged_out) && $is_logged_out) {
			echo '<div align="center"><FONT  FACE="Arial" SIZE=+4 ><b>' . $LDLoggedOut . '</b></FONT><p><font size=4>' . $LDNewLogin . ':</font></p></div>';
		}
		?>
		<p>
			<table width=100% border=0 cellpadding="0" cellspacing="0">
				<tr>
					<td colspan=3><img <?php echo createLDImgSrc($root_path, 'login-b.gif') ?>></td>
				</tr>

				<?php require($root_path . 'include/inc_passcheck_mask.php') ?>

				<p>
					<!--
<img src="../img/small_help.gif" > <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>">Was ist login?</a><br>
<img src="../img/small_help.gif" > <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>">Wieso soll ich mich einloggen?</a><br>
<img src="../img/small_help.gif" > <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>">Was bewirkt das einloggen?</a><br>
 -->
					<p>
						<?php
						require($root_path . 'include/inc_load_copyrite.php');
						?>
						</FONT>
</BODY>

</HTML>