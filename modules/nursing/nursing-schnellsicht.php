<?php
 error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
 require('./roots.php');
 require($root_path.'include/inc_environment_global.php');
 require_once($root_path.'include/care_api_classes/class_ward.php');
 $ward_obj = new Ward;
 /**
 * CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
 * GNU General Public License
 * Copyright 2002,2003,2004,2005 Elpidio Latorilla
 * elpidio@care2x.org,
 *
 * See the file "copy_notice.txt" for the licence notice
 */
 define('USE_PIE_CHART',1); // define to 1 if pie chart is preferred as display, define to 0 if tiny person icons
 define('PIE_CHART_USED_COLOR','red'); // define the color of the used bed portion of the graph

 $lang_tables=array('date_time.php');
 define('LANG_FILE','nursing.php');
 define('NO_2LEVEL_CHK',1);
 require_once($root_path.'include/inc_front_chain_lang.php');

 $breakfile='nursing.php'.URL_APPEND;
 $thisfile=basename(__FILE__);

 $today = date('Y-m-d');

 // Let us make some interface for calendar class
 if($from=='arch'){
	 if($pday=='') $pday=date('d');
	 if($pmonth=='') $pmonth=date('m');
	 if($pyear=='') $pyear=date('Y');
	 $currDay=$pday;
	 $currMonth=$pmonth;
	 $currYear=$pyear;
 } else {
	 if($currDay=='') $currDay=date('d');
	 if($currMonth=='') $currMonth=date('m');
	 if($currYear=='') $currYear=date('Y');
	 $pday=$currDay;
	 $pmonth=$currMonth;
	 $pyear=$currYear;
 }

 $s_date=$pyear.'-'.$pmonth.'-'.$pday;

 if($s_date==date('Y-m-d')) $is_today=true;
	else $is_today=false;

//$db->debug=1;

 $dbtable='care_ward';

  /* Load date formatter */
  include_once($root_path.'include/inc_date_format_functions.php');

	# Get the wards´ info
    $sql="SELECT w.nr,w.ward_id,w.name,w.room_nr_start,w.room_nr_end
				FROM $dbtable as w
					INNER JOIN care_room cr 
    					ON cr.`ward_nr` = w.`nr` 
				WHERE w.is_temp_closed IN ('',0)
							AND w.status NOT IN ('hide','delete','void','inactive')
							AND cr.status IN ('')
							AND w.date_create<='$s_date'
				GROUP BY w.nr 
				ORDER BY w.nr";
		#echo $sql.'<p>';
	# query edited by: syboy 10/30/2015 : meow
	if($wards=$db->Execute($sql)){
		$rows=$wards->RecordCount();
	}else{echo "$sql<br>$LDDbNoRead";}


	# Get the rooms´ info
  $sql="SELECT SUM(r.nr_of_beds) AS maxbed
			FROM $dbtable AS w LEFT JOIN care_room AS r   ON r.ward_nr=w.nr
			WHERE w.is_temp_closed IN ('',0)
				AND w.status NOT IN ('hide','delete','void','inactive')
				AND r.status IN ('') # added by: syboy 10/28/2015 : meow
				AND w.date_create<='$s_date'
			GROUP BY w.nr
			ORDER BY w.nr";
		#echo $sql.'<p>';
	if($rooms=$db->Execute($sql)){
		$roomcount=$rooms->RecordCount();
	}else{echo "$sql<br>$LDDbNoRead";}
	# Get the today´s occupancy
/*
  $sql="SELECT  COUNT(l.location_nr) AS maxoccbed, w.nr AS ward_nr
          FROM $dbtable AS w
     LEFT JOIN care_encounter_location AS l ON l.group_nr=w.nr AND l.type_nr=5 ";
*/
#edited by VAN 04-080-08
  if($is_today)
   $filter = " AND l.date_from <= DATE('$s_date') AND l.date_to = '$dbf_nodate'"; # edited by: syboy 10/28/2015 : meow; remove <
  else
   $filter = " AND l.date_from <= DATE('$s_date') AND l.date_to = '$dbf_nodate'"; # added by: syboy 10/30/2015 : meow
   #$filter = " AND l.date_from <= DATE('$s_date') AND DATE('$s_date') <= DATE('".date('Y-m-d')."') AND (DATE('$s_date') <= l.date_to OR l.date_to = '$dbf_nodate')"; # commented by: syboy 10/30/2015 : meow

  $sql="SELECT
			    (SELECT COUNT(l.encounter_nr)
			    FROM care_encounter_location l
			    		INNER JOIN care_encounter ce 
      						ON ce.`encounter_nr` = l.`encounter_nr`
			         WHERE
			            l.type_nr = 5 AND l.location_nr NOT IN ('', 0)
							  $filter
					  AND l.`create_time` NOT IN ('0000-00-00 00:00:00')
					  AND ce.`admission_dt` IS NOT NULL 
    				  AND ce.`is_discharged` NOT IN (1) 
			          AND l.group_nr = w.nr) maxoccbed,
  				w.nr AS ward_nr
          FROM $dbtable AS w 
          		INNER JOIN care_room cr 
    				ON cr.`ward_nr` = w.`nr`";
  # query edited by: syboy 10/30/2015 : meow; $sql
   
  $sql.=" WHERE  w.is_temp_closed IN ('',0) AND w.status NOT IN ('hide','delete','void','inactive') AND cr.status IN ('') AND w.date_create <= DATE('$s_date') ";
  $sql.="	GROUP BY w.nr ORDER BY w.nr";
	if($occbed=$db->Execute($sql)){
	 	#echo $sql;
		$bedcount=$occbed->RecordCount();
	}else{echo "$sql<br>$LDDbNoRead";}

   /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

  $sTemp = $sid.'&lang='.$lang.'&pday='.$pday.'&pmonth='.$pmonth.'&pyear='.$pyear;
 $smarty->assign('SID_Parameter',$sTemp);
 $smarty->assign('aufnahme_user',$aufnahme_user);

 #$smarty->assign('sToolbarTitle',$LDNursing );
 $smarty->assign('sToolbarTitle',"$LDNursing :: Quick View");
 #$smarty->assign('Subtitle',$LDQuickView ); // Nursing-Subtitle (header_toblock.tpl)
 $smarty->assign('sWindowTitle',"$LDNursing :: Quick View");

 # Added for the common header top block
 $smarty->assign('pbHelp','javascript:gethelp(\'nursing_how2search.php\',\'\','.$rows.',\'quick\',\'\')');

 /*generate the calendar */
 include($root_path.'classes/calendar_jl/class.calendar.php');
 /** CREATE CALENDAR OBJECT **/
 $Calendar = new Calendar;
 $Calendar->deactivateFutureDay();


 /** WRITE CALENDAR **/
 ob_start();
 $Calendar -> mkCalendar ($currYear, $currMonth, $currDay);
 $sTemp = ob_get_contents();
 ob_end_clean();
 // $smarty->assign('tblCalendar',$sTemp); //removed by Kenneth 04-18-2016

 $smarty->assign('gifVarrow',createComIcon($root_path,'varrow.gif','0') );

 // ECHO "DATE".date(Ymd);
 $smarty->assign('LDOld',$LDOld);
 $smarty->assign('LDTodays',$LDTodays);
 $smarty->assign('LDOccupancy',$LDOccupancy);
 $smarty->assign('formatDate2Local',@formatDate2Local($pyear.'-'.$pmonth.'-'.$pday,$date_format));

 /**
 * wards count
 */
 if($rows) {

  /* Load the common icons */
  $img_mangr=createComIcon($root_path,'man-gr.gif','0');
  $img_mans_gr=createComIcon($root_path,'mans-gr.gif','0','absmiddle');
  $img_mans_red=createComIcon($root_path,'mans-red.gif','0','absmiddle');
  $img_statbel=createComIcon($root_path,'statbel2.gif','0','absmiddle');


  $randombett=0;
  $randommaxbett=0;
  $frei=0;

  // srand(time());

  $sWardrows = "";
  while ($result=$wards->FetchRow())
  {
  	$patient = array();
  	$occu_bed = 0;

  	if($is_today){
  		$patients_obj = &$ward_obj->getDayWardOccupants($result['nr']);	
  	} else {
  		$patients_obj = &$ward_obj->getDayWardOccupants($result['nr'],$room_nr = null ,$s_date);
  	}

	if(is_object($patients_obj)){
		while($buf = $patients_obj->FetchRow()){
			$patient[$buf['room_nr']][$buf['bed_nr']] = $buf;
		}
		$patients_ok = true;
	}else{
		$patients_ok = false;
	}

	$room_obj = $ward_obj->getRoomsData($result['nr']);
	 
	if(is_object($room_obj)) {
		$room_ok=true;
	}else{
		$room_ok=false;
	}

	$wardRoom = $ward_obj->getRoomsData($result['nr']);
	
	while ($perRoom = $wardRoom->FetchRow()) {
		$i = $perRoom['room_nr'];
		// echo $i.' -- ';
		if($room_ok){
			$room_info = $room_obj->FetchRow();
		}else{
			$room_info['nr_of_beds'] = 1;
		}
// var_dump($patients_ok);
		if ($patients_ok) {
			for ($w=1; $w <= $room_info['nr_of_beds']; $w++) { 
					if(isset($patient[$i][$w])){
						// $bed = $patient[$i][$w];
						// var_dump($bed);
						$occu_bed += 1;
					}
					/*else{
						$bed = NULL;
						$occu_bed = $occu_bed + 0;
					}*/
			}
		}
		
	}
	
	// echo $result['nr'].' ==> '.$room_info['nr_of_beds'].' : '.$occu_bed.'<br/>';
   $maxbed=$result['room_nr_end']-$result['room_nr_start'];

   $roomrow=$rooms->FetchRow();
   $bedrow=$occbed->FetchRow();
   $freebeds=$roomrow['maxbed']-$occu_bed;
   // Patch 2004-05-15
   if($roomrow['maxbed']){
		$frei=floor(($freebeds/$roomrow['maxbed'])*10);
	}else{
		$frei=0;
	}
   if ($toggler==0) {
    //$bgc='ffffcc';
	$toggler=1;
	$sStatListClass='wardlistrow1';
   } else {
    //$bgc='dfdfdf';
	$toggler=0;
	$sStatListClass='wardlistrow2';
   }

   /**
   * collect the hole ward block into $sWardrows
   */

   ob_start();
   echo '
     <tr class="'.$sStatListClass.'">';
   echo '
        <td align=center><a href="javascript:statbel(\'1\',\''.$result['nr'].'\',\''.$result['ward_id'].'\')"  title="'.$LDClk2Show.'">';
   echo strtoupper($result['name']).'
        </a>';
   echo '</td>
        <td align=center>
        '.$freebeds.'&nbsp;&nbsp;&nbsp;</td>
        <td align=center><font  color="'.PIE_CHART_USED_COLOR.'">
        ';
	#edited by VAN 04-08-08
    # updated by: syboy 01/19/2015 : meow; $bedrow['maxoccbed']
   if($occu_bed)
		echo $occu_bed;
	else
		echo '0';
   echo '&nbsp;&nbsp;&nbsp;</td>
        ';
   echo '
        <td align="center">';
	if($is_today){
		echo '<a href="javascript:statbel(\'1\',\''.$result['nr'].'\',\''.$result['ward_id'].'\')" title="'.str_replace("~station~",$result['name'],$LDEditStation).'" >';
	}
   if(defined('USE_PIE_CHART')&&USE_PIE_CHART){
    echo '<img src="occupancy_pie_chart.php?qouta='.($roomrow['maxbed']-$occu_bed).'&used='.$occu_bed.'&bgc='.$bgc.'&uc='.PIE_CHART_USED_COLOR.'" border="0">';
   }else{
    for ($n=0;$n<(10-$frei);$n++) echo '<img '.$img_mans_red.'>';
    for ($n=0;$n<$frei;$n++) echo '<img '.$img_mans_gr.'>';
   }
 	if($is_today){
		echo '</a>';
	}
	echo '
			</td><td align=center>'.$roomrow['maxbed'].'
			</td>';
	echo "\r\n";
	echo '
			</td><td align=center>';

	if($is_today){
		echo '
			<a href="javascript:statbel(\'1\',\''.$result['nr'].'\',\''.$result['ward_id'].'\')" title="'.str_replace("~station~",$result['name'],$LDEditStation).'" >
			<img '.$img_statbel.' alt="'.str_replace("~station~",$result['name'],$LDEditStation).'" border="0"></a>';
	}
 	if($is_today){
		echo '</a>';
	}
	echo '
			</td></tr>
	 <tr><td class="thinrow_vspacer" colspan="7"><img src="../../gui/img/common/default/pixel.gif" border=0 width=1 height=1></td></tr>
	 ';


   $sWardrows = $sWardrows . ob_get_contents();
   ob_end_clean();


  } // end While
 } // if ($rows)

 /**
 * ========= start with GUI - Block =======================
 */

 $smarty->assign('LDNrUnocc',$LDNrUnocc);
 $smarty->assign('gifImg_mangr',$img_mangr);
 $smarty->assign('LDStation',$LDStation);
 $smarty->assign('LDFreeBed',$LDFreeBed);
 $smarty->assign('PIE_CHART_USED_COLOR',PIE_CHART_USED_COLOR);
 $smarty->assign('LDOccupied',$LDOccupied);
 $smarty->assign('LDOccupancy',$LDOccupancy);
 $smarty->assign('LDBedNr',$LDBedNr);
 $smarty->assign('LDOptions',$LDOptions);

 $smarty->assign('WardRows',$sWardrows);

// $smarty->assign('maxbed',$roomrow['maxbed']);
// $smarty->assign('LINKstatbel','javascript:statbel(\'1\',\''.$result['nr'].'\',\''.$result['ward_id'].'\')' );
// $smarty->assign('altImg_statbel',str_replace("~station~",$result['name'],$LDEditStation));
// $smarty->assign('gifImg_statbel',$img_statbel);

 /**
 * IF ($is_today)
 */
 $smarty->assign('is_today',$is_today);
 $smarty->assign('gifBul_arrowgrnlrg',createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle') );
 $smarty->assign('gifMascot1_r',createMascot($root_path,'mascot1_r.gif','0','absmiddle') );
 $smarty->assign('LDNoOcc',$LDNoOcc);
 $smarty->assign('LDClk2Archive',$LDClk2Archive);

 /**
 * IF ($from == "arch")
 */
 $smarty->assign('from',$from);
 $smarty->assign('LINKArchiv','nursing-station-archiv.php'.URL_APPEND.'&pyear='.$pyear.'&pmonth='.$pmonth);
 $smarty->assign('pbBack2',createLDImgSrc($root_path,'back2.gif','0') );
 /* ELSE */
 $smarty->assign('pbClose2',createLDImgSrc($root_path,'close2.gif','0') );
 $smarty->assign('breakfile',$breakfile);

 # Assign nr of wards available

 $smarty->assign('iWardCount',$rows);

 # Assign quick view template to the mainframe block

 $smarty->assign('sMainBlockIncludeFile','nursing/nursing-schnellansicht.tpl');

 /**
 * show Template
 */

 $smarty->display('common/mainframe.tpl');

?>

