<?php
		error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
		require('./roots.php');
		require($root_path.'include/inc_environment_global.php');
		require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');

		/*Populate the Industrial Clinic's income table daily.
			use : CRON SCHEDULER (daily)
			modules/industrial_clinic/seg-populate-ic-income-cron.php
		*/

		#Open destination connection.
	 $icObj = new SegICTransaction();

	 global $db;

	 echo "Locking bot...";

	 $icObj->startTrans();
	 $today = date("Y-m-d");
	 $prev = mktime(0,0,0,date("m"),date("d")-1,date("Y"));    
	 $previousdate = date("Y-m-d", $prev);
	 $ok = $db->Execute("CALL sp_poputale_ic_income('".$previousdate."','".$today."')");

	 if (!$ok) $icObj->FailTrans();
	 $icObj->CompleteTrans();
	 echo "<br>Lock released...";
?>