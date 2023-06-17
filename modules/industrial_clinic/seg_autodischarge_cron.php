<?php 
		error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
		require('./roots.php');
		require($root_path.'include/inc_environment_global.php');


		global $db;

		$ok = $db->Execute("CALL sp_update_discharge_ic");

		if (!$ok) {
			echo "Failed";
		}else{
			echo "<br>Discharged IC transactions... ";
		}

 ?>