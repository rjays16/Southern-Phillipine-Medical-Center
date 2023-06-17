<?php
	try {
		# added by VAS 11/19/2013
		# integration to EMR starts here	
		# close case in EMR
		
		$method = 'PUT';
		$url = 'http://'.$EMR_address.'/'.$EMR_directory.'/api/patients/'.$pid.'/closeCase/'.$encounter_nr;
		$emr_obj->consumeWRITEmethodnoDATA($url, $method);
		
	} catch(Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}	
?>