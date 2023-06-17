<?php

///Added By John
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/inc_front_chain_lang.php');


require($root_path.'modules/personell_admin/ajax/ajax-personnel-orientation.common.php');
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
date_default_timezone_set("Asia");
?>

<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/timepicki.js"></script>   
<script src="js/bootstrap.min.js"></script>

<html>
<head>
	<title>Orientation-</title>
	<meta name="Description" content="Hospital and Healthcare Integrated Information System - CARE2x">
	<meta name="Author" content="Elpidio Latorilla">
	<meta name="Generator" content="various: Quanta, AceHTML 4 Freeware, NuSphere, PHP Coder">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		
	<link rel="stylesheet" href="../../css/themes/default/default.css?t=1501815660" type="text/css">
	<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/timepicki.css" rel="stylesheet">

	<style type="text/css">
		A:link  {color: #000066;}
		A:hover {color: #cc0033;}
		A:active {color: #cc0000;}
		A:visited {color: #000066;}
		A:visited:active {color: #cc0000;}
		A:visited:hover {color: #cc0033;}
		#venSel{
		width: 275px;
		}
		#venSel option{
			width: :1500px;
		}
		#orientation_start_time{
		width: 70px;
		vertical-align: top;
		}
		#orientation_end_time{
		width: 70px;
		vertical-align: top;
		}
		/*.your_time div{
		  /*clear: both;*/
		  display: inline-block;
		  vertical-align: top;
		  /*overflow: hidden;*/
		  /*white-space: nowrap;*/
		}
	</style>
</head>
<body vlink="#000066" link="#000066" bgcolor="#FFFFFF" alink="#cc0000">
	<div style="width:99%; padding:2px">
		<table id="main" width="100%" height="100%" cellspacing="0" border="0">
			<tbody class="main">
				<tr>
					<td valign="top" height="35" align="center">
				 		<table class="titlebar" style="border:1px solid #cfcfcf;margin-bottom:10px" cellspacing="0" cellpadding="0">    
 							<tbody>
 								<tr class="titlebar" valign="middle">
								  <?php
								  	$emp_num=10023;
								  ?>
  									<td width="1" valign="middle" bgcolor="#e4e9f4">
	    								&nbsp;&nbsp;
	    								<font style="white-space:nowrap" size="3" color="#330066">
	    									Orientation<?php $pid=$_GET['personell_nr'];?> 
	    								</font>
       								</td>
  									<td style="" valign="middle" bgcolor="#e4e9f4" align="right"></td>
		  						 </tr>
 							</tbody>
 						</table>
 					</td>
				</tr>
				<tr>
					<td valign="top" bgcolor="#ffffff">
						<div align="center">
							<div style="width:90%;" align="center">
								<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#a0a0a0">
									<tbody>
										<tr>
									    	<td>
									    		<table style="width:100%" cellspacing="1" cellpadding="5" border="0">
									    			<tr bgcolor="#dddddd">
														<td>
															<input name="route" value="validroute" type="hidden">

															Date of Orientation:<?php echo '<span name="seldateoptions" segOption="specificdate">
																		<input onchange="" class="jedInput" name="specificdate" id="specificdate" type="text" size="8" value="'.date('Y-m-d').'"" onFocus="this.select();" disabled/>
																		<img src="../../gui/img/common/default/show-calendar.gif" id="date_trigger" align="absmiddle" style="cursor:pointer"/>
																		<script type="text/javascript">
																			Calendar.setup ({
																				inputField : "specificdate",
																				ifFormat : "%Y-%m-%d",
																				showsTime : false,
																				button : "date_trigger",
																				singleClick : true,
																				step : 1
																			});
																		</script>
																	</span>'?>

															<br>
														</td>

														<td>
															Venue:<SELECT class="input" size="1" id="venSel">
																<?php
																	$sql="SELECT orientation_venue_id, orientation_venue_name FROM seg_orientation_venue ORDER BY is_default DESC, orientation_venue_name ASC";
																	$result=$db->Execute($sql);
																	while ($venue=$result->FetchRow()){
																		echo '<option value='.$venue['orientation_venue_id'].'>'.$venue['orientation_venue_name'].'</option> ';
																	}

																?>

																</SELECT>
															<br>
														</td>
													</tr>
													<tr bgcolor="#dddddd">
														<td colspan="3">
														Time of Orientation:&nbsp;
														<input id="orientation_start_time" class="input" type="text" name="orientation_start_time" type="text" size="1" 
														 />
														&nbsp;to:&nbsp;
												 		<input id="orientation_end_time" class="input" type="text" name="orientation_end_time" type="text" size="1" 
														 />
														<!--end -->
														</td>

													</tr>

													<tr bgcolor="#dddddd">
														<td colspan="3">Module:
															<?php
															 	 $sql="SELECT cmm.`name` FROM care_menu_main AS cmm WHERE cmm.`is_visible` = 1 AND cmm.orientation_sort <> 0 ORDER BY cmm.orientation_sort ASC";
															 	 $test;
																echo' <select class="input" name="module" id="module" onchange="change(value)"> ';
																$result=$db->Execute($sql);
																	
																	while($menu=$result->FetchRow()){		
																	echo '<option value="'.$menu['name'].'">'.$menu['name'].' </option>';

																	}
																echo ' </select>';	
															 ?>	
														</td>
													</tr>

													<tr bgcolor="#dddddd">
														<td colspan="3">
															<br>Title:
															<textarea name="txtBoxTitle" class="input" id="txtBoxTitle" cols="50" rows="3"  wrap="physical"></textarea>
													 		<br>
													 	</td>
													</tr>
													<tr bgcolor="#dddddd">
														<td colspan="2">
															<div style="float: right;">
																<a href="javascript:void(0)" ><img onclick="buttonsubmit();" id="btnSave" name="saveButton" src="../../gui/img/control/default/en/en_savedisc.gif"  title="Save data"   width="72" height="23" border="0" ></a>
																<a href="javascript:void(0)"><img src="../../gui/img/control/default/en/en_cancel.gif" onclick="ReloadWindow();" title="Cancel Data" width="73" height="23" border="0"></a>
															</div>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr bgcolor="#dddddd">
									    	<td>
									    		List of Orientation:
									    		<table style="width:100%" cellspacing="1" cellpadding="5" border="0">
													<thead>
														<th>Date</th>
														<th>Starting Time</th>
														<th>End Time</th><!-- added for orientation end time (4-10-2018) -->
														<th>Module</th>
														<th>Title</th>
														<th>Venue</th>
													</thead>
												<?php 

												$sql="SELECT 
													  	orientation_list_id,
													  	employee_number,
													  	TIME_FORMAT(starting_time_of_orientation,'%h:%i %p') AS starting_time_of_orientation,
													  	TIME_FORMAT(end_time_of_orientation,'%h:%i %p') AS end_time_of_orientation,
													  	TIME_FORMAT(starting_time_of_orientation,'%h:%i %p') AS starting_time_of_orientation_list,
													  	TIME_FORMAT(end_time_of_orientation,'%h:%i %p') AS end_time_of_orientation_list,
													  	module_orientation,
													  	date_of_orientation,
													  	title_orientation,
													  	IF(
														    venue REGEXP '^[0-9]+$',
														    (SELECT 
														      orientation_venue_name
														    FROM
														      seg_orientation_venue 
														    WHERE orientation_venue_id = venue),
													    	venue
													  	) venue,
													  	venue as venue_id
													  	FROM
													  	seg_orientation_list WHERE employee_number=".$pid." AND is_deleted = 0" ;

													$result=$db->Execute($sql);
														while ($list=$result->FetchRow()){
															$val = utf8_decode($list['title_orientation']);//Paulo add(5-17-18)
															$info=$list['date_of_orientation'].'^'.$list['starting_time_of_orientation'].'^'.$list['end_time_of_orientation'].'^'.$list['module_orientation'].'^'.$val.'^'.$list['venue'].'^'.$list['orientation_list_id'];
															?><tr>
															<td align="center" style="display:none"><?=$list['employee_number']?></td>
															<td align="center"><?=$list['date_of_orientation']?></td>
															<td align="center"><?=$list['starting_time_of_orientation_list']?></td>
															<td align="center"><?=$list['end_time_of_orientation_list']?></td>
															<td align="center"><?=$list['module_orientation']?></td>
															<td align="center" style="word-wrap: break-all"><?=$val?></td>
															<td align="center"><?=$list['venue']?></td>
															<TD><img title="Delete" class="segSimulatedLink" src="../../images/cashier_delete.gif" onclick="removeFromList(<?=$list['orientation_list_id']?>)" border="0" align="absmiddle" value="<?=$list['orientation_list_id']?>" id="<?=$list['orientation_list_id']?>"></TD>
															<TD><img id="<?=$info?>" title="Update" class="segSimulatedLink" src="../../images/cashier_edit.gif" onclick="updateFromList(this, <?=$list['orientation_list_id']?>, <?=$list['venue_id']?>)" border="0" align="absmiddle" value="updatetime<?=$list['orientation_list_id']?>"></TD>
															</tr>

														<?php }
												
												
												?>



												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<tr label="copyright" valign="top">
			<td bbgcolor="#cccccc" bgcolor="ffffff">
				<table width="100%" cellspacing="0" cellpadding="1" border="0" bgcolor="#ffffff">
					<tbody>
						<tr>
							<td align="center">
  								<table style="border:1px solid #cfcfcf;margin:2px" width="100%" cellspacing="0" cellpadding="5" bgcolor="#e4e9f4">
  									<tbody><tr><td><div class="copyright">

			

<script language="JavaScript">

/*
function openCreditsWindow() {

	urlholder="../../language/en/en_credits.php?lang=en";
	creditswin=window.open(urlholder,"creditswin","width=500,height=600,menubar=no,resizable=yes,scrollbars=yes");

}*/

</script>	
	<script src="js/orientationMod.js"></script>
	<a href="http://www.segworkstech.com" target="_new">Segworks Hospital Information Systems</a> ::<br>
	<input type="hidden" id="hidn" value="<?php echo $pid ?>">
	<input type="hidden" id="hidnID" value="">
	<input name="popUp" id="popUp" value="0" type="hidden">

</body>
</html>
