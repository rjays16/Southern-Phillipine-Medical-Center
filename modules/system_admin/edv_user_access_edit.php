<?php
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');

$lang_tables[] = 'access.php';
define('LANG_FILE', 'edp.php');
define('NO_2LEVEL_CHK', 1);
$local_user = 'ck_edv_user';
require_once($root_path . 'include/inc_front_chain_lang.php');

#end Jesther
require($root_path . 'include/inc_special_functions_permission.php');
/**
 * The following require loads the access areas that can be assigned for
 * user permissions.
 */
require($root_path . 'include/inc_accessplan_areas_functions.php');
require_once($root_path . 'include/care_api_classes/API/curl_api.php');

require_once $root_path . 'include/care_api_classes/doctors_mobility/DoctorService.php';

include($root_path . 'include/care_api_classes/class_access.php');
$objAccess = new Access;

$breakfile = 'edv-system-admi-welcome.php' . URL_APPEND;
$returnfile = $_SESSION['sess_file_return'] . URL_APPEND;
$_SESSION['sess_file_return'] = basename(__FILE__);

$popUp = 0;
if ($_GET['popUp'])
	$popUp = $_GET['popUp'];
elseif ($_POST['popUp'])
	$popUp = $_POST['popUp'];

if ($_GET['userid'])
	$userid = $_GET['userid'];
elseif ($_POST['userid'])
	$userid = $_POST['userid'];

$edit = 0;
$error = 0;
$isAllDept = '';

if (!isset($mode)) $mode = '';
if (!isset($errorname)) $errorname = '';
if (!isset($erroruser)) $erroruser = '';
if (!isset($username)) $username = '';
if (!isset($userid)) $userid = '';
if (!isset($errorpass)) $errorpass = '';
if (!isset($pass)) $pass = '';
if (!isset($errorbereich)) $errorbereich = '';


if ($mode != '') {
	if ($mode != 'edit' && $mode != 'update' && $mode != 'data_saved') {
		/* Trim white spaces off */
		$username = trim($username);
		$userid = trim($userid);
		$pass = trim($pass);

		if ($username == '') {
			$errorname = 1;
			$error = 1;
		}
		if ($userid == '') {
			$erroruser = 1;
			$error = 1;
		}
		if ($pass == '') {
			$errorpass = 1;
			$error = 1;
		}
	}


	if (($mode == 'save' && !$error) || ($mode == 'update' && !$erroruser)) {


		/* Prepare the permission codes */

		$p_areas = '';
		$personnel_areas = array();

		while (list($x, $v) = each($_POST)) {
			if (strpos($x, '_a_') === false) continue;
			if ($_POST[$x] != '') {
				$p_areas .= $v . ' ';
				array_push($personnel_areas, $v);
			}
		}


		/* If permission area is available, save it */
		if ($p_areas != '') {
			//$db->debug=true;
			$user_pid = $db->GetOne("SELECT personell_nr FROM care_users WHERE login_id = ?", $userid); # added by: syboy 03/29/2016

			if ($mode == 'save') {
				$sql = "INSERT INTO care_users (" .
					"name," .
					"login_id," .
					"password," .
					"permission," .
					"personell_nr," .
					"s_date," .
					"s_time," .
					"status," .
					"modify_id," .
					"create_id," .
					"create_time " .
					") VALUES (" .
					$db->qstr($username) . "," .
					$db->qstr($userid) . "," .
					$db->qstr((md5($pass))) . "," .
					$db->qstr($p_areas) . "," .
					$db->qstr($personell_nr) . "," .
					$db->qstr(date('Y-m-d')) . "," .
					$db->qstr(date('H:i:s')) . "," .
					"'normal'," .
					$db->qstr($_SESSION['sess_user_name']) . "," .
					$db->qstr($_SESSION['sess_user_name']) . "," .
					$db->qstr(date('YmdHis')) .
					")";

				# added by: syboy 03/29/2016
				$sql_areas = "INSERT INTO seg_areas_duration_time (" .
					"pid," .
					"areas," .
					"old_areas," .
					"duration," .
					"mode," .
					"create_id," .
					"create_dt" .
					") VALUES (" .
					$db->qstr($personell_nr) . "," .
					$db->qstr($p_areas) . "," .
					"''," .
					$db->qstr($_POST['durationTime']) . "," .
					$db->qstr($mode) . "," .
					$db->qstr($_SESSION['sess_user_name']) . "," .
					$db->qstr(date('YmdHis')) .
					")";
				// var_dump($sql_areas);die();
				# ended syboy 03/29/2016 : meow
			} else {

				$sql = "UPDATE care_users SET permission='$p_areas', modify_id=" . $db->qstr($_SESSION['sess_user_name']) . "  WHERE login_id='$userid'";

				# added by: syboy 03/29/2016
				$sql_areas = "INSERT INTO seg_areas_duration_time (" .
					"pid," .
					"areas," .
					"old_areas," .
					"duration," .
					"mode," .
					"create_id," .
					"create_dt" .
					") SELECT " .
					$db->qstr($user_pid) . "," .
					$db->qstr($p_areas) . "," .
					"cu.permission," .
					$db->qstr($_POST['durationTime']) . "," .
					$db->qstr($mode) . "," .
					$db->qstr($_SESSION['sess_user_name']) . "," .
					$db->qstr(date('YmdHis')) .
					" FROM care_users cu WHERE cu.login_id=" . $db->qstr($userid);
				# ended syboy 03/29/2016 : meow
			}

			/* Do the query */
			$db->BeginTrans();
			$ok_areas = $db->Execute($sql_areas);

			$ok = $db->Execute($sql);
			//FOR EHR 9/2/2015
			try {
				if ($ok && $mode == 'save') {
					$doctorService = new DoctorService();
					$doctorService->saveDoctor($personell_nr);
				}
			} catch (Exception $e) { }
			//END FOR EHR


			if ($ok && $ok_areas) {

				if ($personell_nr == NULL) {
					$personell_nr = $user_pid;
				}
				$getName = "SELECT
						  cp.`pid`,
						  cpl.`nr`,
						  cpl.`ptr_nr`,
						  cp.`name_first`,
						  cp.`name_middle`,
						  cp.`name_last`,
						  cpl.`tin`,
						  cpl.`job_function_title`
						from care_person cp
		       				LEFT JOIN care_personell cpl
		       					ON  cpl.pid=cp.pid
		       				LEFT JOIN care_users cu 
		       					ON cu.personell_nr = cpl.nr 
							LEFT JOIN `seg_dr_accreditation` sda
								ON sda.`dr_nr` = cpl.`nr`
		       			WHERE cu.personell_nr='" . $personell_nr . "'";

				if ($ergebnis = $db->Execute($getName)) {
					if ($ergebnis->RecordCount()) {
						$params = $ergebnis->FetchRow();
					}
				}
				$firstname = $params['name_first'];
				$middlename = $params['name_middle'];
				$lastname = $params['name_last'];
				$ptr = $params['ptr_nr'];
				$licenseNumber = $params['license_nr'];
				$phone = $params['contact_no'];

				$doctorData = $params;

				/*
	             * retrieve password hashed for EHR purposes only
	             * since EHR users are only created or updated from here
	             * and by default password are set * during permission updated.
	             * */
				$users = $db->GetAll("SELECT * FROM care_users WHERE login_id = " . $db->qstr(utf8_encode($userid)));
				foreach ($users as $key2 => $user2) {
					$pass2 = $user2['password'];
				}

				$docdata = array(
					"pid"	=> $params['pid'],
					"nr"	=>	$doctorData['nr'],
					"username"	=>	utf8_encode($userid),
					"password"	=> ($pass != '*' ? md5(utf8_encode($pass)) : $pass2),
					"modify_id"	=> $_SESSION['sess_user_name'],
					"role_catalog"	=> $params['job_function_title'],
					"permission_areas"	=> $personnel_areas
				);
				// var_dump($_SESSION); die();
				require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
				$ehr = Ehr::instance();
				$patient = $ehr->doctor_postCreateUser($docdata);
				$asd = $ehr->getResponseData();
				$EHRstatus = $patient->status;

				$curl_obj = new Rest_Curl;
				$curl_obj->save_users($username, $userid, $pass, $personell_nr, $_SESSION['sess_user_name'], $job, $p_areas);
				//                    $db->RollbackTrans();
				//                    echo "<pre>";
				//                 var_dump($docdata);
				//                 var_dump($asd); die();
				if (!$EHRstatus) {
					//                    $db->RollbackTrans();
					//                    $mode = 'ehr_error';
					//                    $ehrError2 = 'EHR error: '. $patient->msg;
				} else { }
				$db->CommitTrans();
				header('Location:edv_user_access_edit.php' . URL_REDIRECT_APPEND . '&userid=' . strtr($userid, ' ', '+') . '&mode=data_saved&popUp=' . $popUp);
				//END EHR
				#---END JESTHER ------#

				// echo "<script>window.parent.ReloadWindow();</script>"; # added by : syboy 03/29/2016 : meow

				// exit;
			} else {
				$db->RollbackTrans();
				if ($mode != 'save') $edit = 1;
				$mode = 'error_double';
			}
		} else {
			if ($mode != 'save') $edit = 1;
			$mode = 'error_noareas';
			$db->RollbackTrans();
		} // end if ($p_areas!="")
	} // end of if($mode=="save"

	// if($mode=='edit' || $mode=='data_saved' || $edit) {
	$sql = "SELECT name, login_id, permission FROM care_users WHERE login_id='$userid'";
	if ($ergebnis = $db->Execute($sql)) {
		if ($ergebnis->RecordCount()) {
			$user = $ergebnis->FetchRow();
			$edit = 1;
		}
	}
	// }
}

# Start Smarty templating here
/**
 * LOAD Smarty
 */
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path . 'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('system_admin');

# Title in toolbar
$smarty->assign('sToolbarTitle', $LDManageAccess);

# href for return button
$smarty->assign('pbBack', $returnfile);

# href for help button
$smarty->assign('pbHelp', "javascript:gethelp('edp.php','access','$mode')");

# href for close button
if ($popUp == 1)
	$smarty->assign('breakfile', '');
else
	$smarty->assign('breakfile', $breakfile);

# Window bar title
$smarty->assign('sWindowTitle', $LDManageAccess);

# Buffer page output
ob_start();
?>
<script type="text/javascript" src="<?= $root_path ?>js/shortcuts.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript">
	var $J = jQuery.noConflict();
	// added by : syboy 03/29/2016 : meow
	$J(function() {
		(function($J) {
			$J.extend({
				APP: {
					formatTimer: function(a) {
						if (a < 10) {
							a = '0' + a;
						}
						return a;
					},
					startTimer: function(dir) {
						var a;
						$J.APP.dir = dir;
						$J.APP.d1 = new Date();
						switch ($J.APP.state) {
							default:
								$J.APP.t1 = $J.APP.d1.getTime();
								break;
						}
						$J.APP.state = 'alive';
						$J.APP.loopTimer();
					},

					loopTimer: function() {
						var td;
						var d2, t2;
						var ms = 0;
						var s = 0;
						var m = 0;
						var h = 0;

						if ($J.APP.state === 'alive') {
							d2 = new Date();
							t2 = d2.getTime();
							if ($J.APP.dir === 'sw') {
								td = t2 - $J.APP.t1;
							} else {
								td = $J.APP.t1 - t2;
							}
							ms = td % 1000;
							if (ms < 1) {
								ms = 0;
							} else {
								s = (td - ms) / 1000;
								if (s < 1) {
									s = 0;
								} else {
									var m = (s - (s % 60)) / 60;
									if (m < 1) {
										m = 0;
									} else {
										var h = (m - (m % 60)) / 60;
										if (h < 1) {
											h = 0;
										}
									}
								}
							}
							ms = Math.round(ms / 100);
							s = s - (m * 60);
							m = m - (h * 60);
							$J('#' + $J.APP.dir + '_ms').html($J.APP.formatTimer(ms));
							$J('#' + $J.APP.dir + '_s').html($J.APP.formatTimer(s));
							$J('#' + $J.APP.dir + '_m').html($J.APP.formatTimer(m));
							$J('#' + $J.APP.dir + '_h').html($J.APP.formatTimer(h));
							$J.APP.t = setTimeout($J.APP.loopTimer, 1);
							$J('#durationTime').val($J.APP.formatTimer(h) + ':' + $J.APP.formatTimer(m) + ':' + $J.APP.formatTimer(s) + ' ' + $J.APP.formatTimer(ms));

						} else {
							clearTimeout($.APP.t);
							return true;
						}
					}
				}
			});
			$J.APP.startTimer('sw');
		})(jQuery);
	});
	// ended syboy

	shortcut("F2",
		function() {
			document.user.onsubmit = chkform(user);
			if (document.user.onsubmit)
				document.user.submit();
		}
	);

	function toggleTitle(obj) {
		var id = $J(obj).parent().attr('id');
		$J('[data-parent=' + id + ']').toggle();
	}

	function valCheckbox(checkbox) {
		var cnt = -1;
		var temp;

		temp = document.getElementsByName(checkbox);

		/*if (!$(checkbox))	{
			return null;
		}*/

		for (var i = temp.length - 1; i > -1; i--) {
			if (temp[i].checked) {
				cnt = i;
				i = -1;
			}
		}

		if (cnt > -1) return temp[cnt].value;
		else return null;
	}

	function chkform() {
		var mode = '<?= $_GET['mode'] ?>';
		var checkarea;

		//if (mode=="")
		//	checkarea = valCheckbox("bots[]");
		//else
		//	return true;
		//checkarea = valCheckbox("input",0);	
		//alert(checkarea);			  
		if (document.getElementById('pass').value == "") {
			alert("Please input a user password.");
			document.getElementById('pass').focus();
			return false;
			/*}else if (checkarea == null){	
				alert("You have not selected any area!");
				document.getElementById('pass').focus();
				return false;*/
		} else {
			return true;
		}
	}

</script>

<?php
//if ($mode=='data_saved' || $error ||  $mode=='error_noareas' || $mode=='data_nosave' )

if (($mode != '' || $error) && $mode != 'edit') {
	if ($error) {
		$smarty->assign("sysErrorMessage", $LDInputError);
	} elseif ($mode == 'ehr_error') {
		$smarty->assign("sysErrorMessage", $ehrError2);
	} elseif ($mode == 'data_saved') {
		$smarty->assign("sysInfoMessage", $LDUserInfoSaved);
	} elseif ($mode == 'error_save') {
		$smarty->assign("sysErrorMessage", $LDUserInfoNoSave);
	} elseif ($mode == 'error_noareas') {
		$smarty->assign("sysErrorMessage", $LDNoAreas);
	} elseif ($mode == 'error_double') {
		$smarty->assign("sysErrorMessage", $LDUserDouble);
	}
	//     if ($error) echo  $LDInputError;
	//  elseif ($mode=='data_saved') echo $LDUserInfoSaved;
	// elseif($mode=='error_save') echo $LDUserInfoNoSave;
	//   elseif($mode=='error_noareas') echo $LDNoAreas;
	//     elseif($mode=='error_double') echo $LDUserDouble;
}
?>

<div class="prompt">
	<?php

	if (($mode == "") and ($remark != 'fromlist')) {
		echo '<h2>';
		$gtime = date('H.i');
		if ($gtime < '9.00') echo $LDGoodMorning;
		if (($gtime > '9.00') and ($gtime < '18.00')) echo $LDGoodDay;
		if ($gtime > '18.00') echo $LDGoodEvening;
		echo ', ' . $_COOKIE[$local_user . $sid];
		echo '</h2>';
	}

	if ($popUp == 0) {
		?>

		<form action="edv_user_access_list.php" name="all">
			<input type="hidden" name="sid" value="<?php echo $sid; ?>">
			<input type="hidden" name="lang" value="<?php echo $lang; ?>">
		</form>

	<?php } ?>
</div>

<form method="post" action="edv_user_access_edit.php" name="user" id="user" onSubmit="return chkform()">
	<input type="hidden" name="popUp" id="popUp" value="<?= $popUp ?>" />
	<!-- added by: syboy 03/29/2016 : meow -->
	<div align="right">
		<input type="hidden" id="durationTime" name="durationTime" />
		<u style="margin-right:55px;"><span name="sw_h" id="sw_h">00</span>:<span name="sw_m" id="sw_m">00</span>:<span name="sw_s" id="sw_s">00</span> <span name="sw_ms" id="sw_ms">00</span></u>
		<br />
		<span style="margin-right:50px;">Time Duration</span>
	</div>
	<!-- ended syboy -->
	<div style="padding: 10px">
		<button class="segButton" type="submit">
			<img <?= createComIcon($root_path, 'disk.png', 0) ?> />
			Save permissions
		</button>

		<?php
		if ($popUp == 0) {
			if ($mode == 'data_saved' || $edit) {
				?>
				<button class="segButton" type="button" onclick="window.location.href='edv_user_access_edit.php<?= URL_REDIRECT_APPEND ?>&remark=fromlist'">
					<img <?= createComIcon($root_path, 'user_add.png', 0) ?> /><?= $LDEnterNewUser ?>
				</button>
			<?php
		}
		?>
			<button class="segButton" type="button" onclick="window.location.href='edv_user_search_employee.php<?php echo URL_REDIRECT_APPEND; ?>&remark=fromlist'">
				<img <?= createComIcon($root_path, 'find.png', 0) ?> />Find Employee
			</button>

			<button class="segButton" type="submit" onclick="window.location.href='edv_user_access_list.php?lang={$lang}'; return false;">
				<img <?= createComIcon($root_path, 'application_view_list.png', 0) ?> /> List access permissions
			</button>
		</div>
	<?php
}
?>
	<div align="center" style="width:80%;">
		<table border=0 bgcolor="#a0a0a0" cellpadding=0 cellspacing=0 width="100%">
			<tr>
				<td>

					<table border="0" cellpadding="5" cellspacing="1" style="width:100%">

						<tr bgcolor="#dddddd">
							<td colspan="4" style="font-weight:bold">
								<?php echo $LDNewAccess ?>:
							</td>
						</tr>

						<tr bgcolor="#dddddd">
							<td>
								<input type=hidden name=route value=validroute>


								<?php if ($errorname) {
									echo "<font color=red > <b>$LDName</b>";
								} else {
									echo $LDName;
								} ?>

								<?php

								if ($edit) {
									echo '<input class="input" type="hidden" name="username" value="' . $user['name'] . '">' . '<b>' . $user['name'] . '</b>';
								} elseif (isset($is_employee) && $is_employee) {
									?>
									<input name="username" type="hidden" <?php
																			if ($username != "") echo ' value="' . $username . '"><br><b>' . $username . '</b>';
																			else echo '>';
																		} else {
																			?> <input class="input" name="username" type="text" <?php if ($username != "") echo ' value="' . $username . '"'; ?> style="width:200px">
								<?php
							}
							?>

								<br>
							</td>
							<td>
								<?php if ($erroruser) {
									echo "<font color=red > <b>$LDUserId</b>";
								} else {
									echo $LDUserId;
								} ?>

								<?php

								if ($edit) echo '<input type="hidden" name="userid" value="' . $user['login_id'] . '">' . '<b>' . $user['login_id'] . '</b>';
								else {
									?>

									<input class="input" type="text" name="userid" style="width:180px" <?php if ($userid != "") echo 'value="' . $userid . '"'; ?>>
								<?php
							}
							?>

								<br>
							</td>
							<td>
								<?php if ($errorpass) {
									echo "<font color=red > <b>$LDPassword</b>";
								} else {
									echo $LDPassword;
								} ?>

								<?php

								if ($edit) echo '<input type="hidden" name="pass" id="pass" value="*">****';
								else {
									?>
									<input class="input" type="password" name="pass" id="pass" <?php if ($pass != "") echo "value=" . $pass; ?>>
								</td>

							<?php
						}
						?>

							<br>
				</td>
			</tr>

			<tr bgcolor="#dddddd">
				<td colspan=4>
					<?php if ($errorbereich) {
						echo "<font color=red > <b>$LDAllowedArea</b> </font>";
					} else {
						echo $LDAllowedArea;
					} ?>
				</td>
			</tr>


			<tr bgcolor="#dddddd">
				<td colspan="4">

					<table border=0 cellspacing=0 width=100%>

						<!--  The list of the permissible areas are displayed here  -->

						<?php
						$areas = array();
						$currentArea = null;

						foreach ($area_opt as $key => $value) {
							if (preg_match("/^title/", $key)) {
								$currentArea = $value;
							} else {
								if (!isset($areas[$currentArea])) {
									$areas[$currentArea] = array();
								}
								$areas[$currentArea][$key] = $value;
							}
						}

						reset($area_opt);
						$area1 = array(key($areas) => current($areas));
						array_shift($areas);
						ksort($areas);
						$areas = array_merge($area1, $areas);
						// flatten areas
						$permissions = array();
						$titleCount = 0;
						foreach ($areas as $key => $subAreas) {
							$permissions['title' . $titleCount] = $key;
							foreach ($subAreas as $key => $value) {
								$permissions[$key] = $value;
							}
							$titleCount++;
						}
						# added by: syboy 08/24/2015 : dynamic access permission
						$grant_acc = array();
						global $db;
						$result = $db->GetAll("SELECT id, type_name, alt_name FROM seg_grant_account_type WHERE deleted = 0 ORDER BY id ASC");
						foreach ($result as $key => $grn_acc) {
							$grant_acc['_a_2_grant_account_' . $grn_acc['id'] . '_' . $grn_acc['type_name']] = ucwords($grn_acc['alt_name']);
						}
						// $areas_final = array_merge($permissions, $grant_acc);
						$areas_final = $objAccess->modifyAccessArray($permissions,'_a_1_ccaccount', $grant_acc);

						# end
						// var_dump($areas_final); die();
						$count = 0;
						$block = '';
						$hasAtLeastOnePermission = false;
						/* Loop through the elements of the access area tags */
						while (list($x, $v) = each($areas_final)) {

							if (strpos($x, 'title') !== false)  // If title print it out
								{
									if ($block) {
										echo strtr($block, array(
											'{hide}' => $hasAtLeastOnePermission ? '' : 'hide'
										));
										$block = '';
										$hasAtLeastOnePermission = false;
									}

									$parent = $x;
									$block .= '<tr class="permission-header" id="' . $x . '"><td class="unselectable segPanelHeader" style="cursor:pointer" onclick="toggleTitle(this)">' . $v . '</td></tr>';

									$count = 0;
								} else {
								// get the colum index
								$cindex = substr($x, 3, 1);
								$block .= '<tr class="permission {hide}" data-parent="' . $parent . '"><td valign="middle" height="20" style="border-bottom: 1px solid #ccc; background-color:' . ($count++ % 2 ? '#ededed' : '#fff') . '">';

								if (!$cindex) {
									$block .= '<img ' . createComIcon($root_path, 'redpfeil.gif', '0', 'absmiddle') . '/>';
								} else {
									$block .= '<img ' . createComIcon($root_path, 'bullet_key.png', '0', 'absmiddle') . ' style="margin-left:' . (($cindex - 1) * 20) . 'px"/>';
								}

								$hasPermission = ($edit && strpos($user['permission'], $x) !== false);
								if ($hasPermission) {
									$hasAtLeastOnePermission = true;
								}

								$block .= '<input type="checkbox" id="' . $x . '" name="' . $x . '" value="' . $x . '" ' . ($hasPermission ? 'checked="checked"' : '') . '/>';

								$block .= '<label class="unselectable" for="' . $x . '"style="cursor:pointer;line-height: 20px; margin-left: 5px;' . ($hasPermission ? 'color:#c00; font-weight:bold"' : '') .
									'">' . $v . '</label>';

								$block .= '</td></tr>';
							}
						}

						if ($block) {
							echo '<tr>';
							echo strtr($block, array(
								'{hide}' => $hasAtLeastOnePermission ? '' : 'hide'
							));
							echo '</tr>';
							$block = '';
						}


						?>

					</table>

				</td>
			</tr>

			<tr bgcolor="#dddddd">
				<td colspan=4>
					<p>
						<input type="hidden" name="personell_nr" value="<?php echo $personell_nr; ?>">
						<input type="hidden" name="itemname" value="<?php echo $itemname ?>">
						<input type="hidden" name="sid" value="<?php echo $sid; ?>">
						<input type="hidden" name="lang" value="<?php echo $lang; ?>">
						<input type="hidden" name="mode" value="<?php if ($edit || $mode == 'data_saved' || $mode == 'edit') echo 'update';
																else echo 'save'; ?>">
						<!-- <input type="reset"  value="<?php echo $LDReset ?>"> -->
				</td>
			</tr>
		</table>

		</td>
		</tr>
		</table>

</form>

<p>
	<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path, 'cancel.gif', '0') ?> alt="<?php echo $LDCancel ?>" align="middle"></a>

	</ul>

	<?php

	$sTemp = ob_get_contents();
	ob_end_clean();

	# Assign page output to the mainframe template

	$smarty->assign('sMainFrameBlockData', $sTemp);
	/**
	 * show Template
	 */
	$smarty->display('common/mainframe.tpl');

	?>