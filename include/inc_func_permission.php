<?php 
	//FRITZ 7-22-20
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	define('OPEN_ENCOUNTER_TEXT', "open");
	define('CLOSE_ENCOUNTER_TEXT', "close");
	$enc_obj = new Encounter;
	if ($enc_obj->Is_Discharged($encounter_nr)) {
		$enc_stat = CLOSE_ENCOUNTER_TEXT;
	}else{
		$enc_stat = OPEN_ENCOUNTER_TEXT;
	}
	
	$ipdPermissions = array(
		"_a_1_manageipdpatientencounter" => array(
			"_a_2_manageipdopenencounter" => array(
					"_a_3_ipdviewpatientopenencounter",
					"_a_3_ipdupdatepatientopenencounter" => array(
						"_a_4_ipdupdatevitalsignsopenencounter",
						"_a_4_ipdupdateoutsidemedsopenencounter",
						"_a_4_ipdupdateclinicalchargesopenencounter"
					),
				),
			"_a_2_manageipdcloseencounter" => array(
				"_a_3_ipdviewpatientcloseencounter",
				"_a_3_ipdupdatepatientcloseencounter" => array(
					"_a_4_ipdupdatevitalsignscloseencounter",
					"_a_4_ipdupdateoutsidemedscloseencounter"
				)
			)
		)

	);

	$opdPermissions = array(
		"_a_1_manageopdpatientencounter" => array(
			"_a_2_manageopdopenencounter" => array(
					"_a_3_opdviewpatientopenencounter",
					"_a_3_opdupdatepatientopenencounter" => array(
						"_a_4_opdupdatevitalsignsopenencounter",
						"_a_4_opdupdateoutsidemedsopenencounter",
						"_a_4_opdupdateclinicalchargesopenencounter"
					),
				),
			"_a_2_manageopdcloseencounter" => array(
				"_a_3_opdviewpatientcloseencounter",
				"_a_3_opdupdatepatientcloseencounter" => array(
					"_a_4_opdupdatevitalsignscloseencounter",
					"_a_4_opdupdateoutsidemedscloseencounter"
				)
			)
		)
	);

	$erPermissions = array(
		"_a_1_manageerpatientencounter" => array(
			"_a_2_manageeropenencounter" => array(
					"_a_3_erviewpatientopenencounter",
					"_a_3_erupdatepatientopenencounter" => array(
						"_a_4_erupdatevitalsignsopenencounter",
						"_a_4_erupdateoutsidemedsopenencounter",
						"_a_4_erupdateclinicalchargesopenencounter"
					),
				),
			"_a_2_manageercloseencounter" => array(
				"_a_3_erviewpatientcloseencounter",
				"_a_3_erupdatepatientcloseencounter" => array(
					"_a_4_erupdatevitalsignscloseencounter",
					"_a_4_erupdateoutsidemedscloseencounter"
				)
			)
		)
	);
	
	$ipbmPermissions = array(
		"_a_1_manageipbmpatientencounter" => array(
			'_a_2_accessipbmopdencounter',
			'_a_2_accessipbmipdencounter',
			"_a_2_manageipbmopenencounter" => array(
					"_a_3_ipbmviewpatientopenencounter",
					"_a_3_ipbmupdatepatientopenencounter" => array(
						"_a_4_ipbmupdatevitalsignsopenencounter",
						"_a_4_ipbmupdateoutsidemedsopenencounter",
						"_a_4_ipbmupdateclinicalchargesopenencounter"
					),
				),
			"_a_2_manageipbmcloseencounter" => array(
				"_a_3_ipbmviewpatientcloseencounter",
				"_a_3_ipbmupdatepatientcloseencounter" => array(
					"_a_4_ipbmupdatevitalsignscloseencounter",
					"_a_4_ipbmupdateoutsidemedscloseencounter"
				)
			)
		)
	);

    $opdPatientManagementPermission = array(
        "_a_1_opdpatientmanage" => array(
            "_a_2_opdpatientregister",
            "_a_2_opdpatientupdate",
            "_a_2_opdpatientview",
            "_a_2_opdonlineconsultrequest"
        )
    );

	function getChildPermissions($haystack,$needle,$is_child = false) {

		$pList = array();
		$tmpList = array();
		$tmp = $is_child;
		if (is_array($haystack) || is_object($haystack)){
			foreach ($haystack as $key => $value) {
		    	
		        if (is_array($value)) {
		        	
		        	if (is_string($key)) {
	  
		        		if ($key == $needle || $is_child) {
			            	$is_child = true;
			            	array_push($pList, $key);	
			            } 
		        	}
		        	$tmpList[] = getChildPermissions($value, $needle, $is_child);
		        	if (!$tmp && $is_child) {
		        		break;
		        	}  
		        }else if ($value == $needle || $is_child) {
		            array_push($pList,$value);
		        }
		       
		    }
		}
	    
	    if ($tmpList && is_array($tmpList)) {
	    	foreach ($tmpList as $key => $value) {
	    		if (is_array($value)) {
	    			$pList = array_merge($pList,$value);
	    		}else{
	    			array_push($pList,$value);
	    		}
	    	} 	
	    }	
	   
	    return $pList;
	}

	function getParent($array, $needle, $parent = null) {

		if (is_array($array) || is_object($array)){
		    foreach ($array as $key => $value) {
		        if (is_array($value)) {
		            $pass = $parent;
		            if (is_string($key)) {
		            	if ($needle == $key) {
		            		return $parent;
		            	}
		                $pass = $key;
		            }
		            $found = getParent($value, $needle, $pass);
		            if ($found) {
		                return $found;
		            }
		        } 
		        else if ($value == $needle) {

		            return $parent;
		        }
		    }
		}
	}

	function hasParentOnlyPermission($array, $needle) {
		$pp = true;
	    if ($cList = getChildPermissions($array,$needle)) {
	    	foreach ($cList as $key => $value) {
	    		if (ereg($value,$_SESSION['sess_permission']) && ($value !== $needle)) {
	    			$pp = false;
	    		}
	    	}
	    
	    }
	    return $pp;
	}


	function getAllowedPermissions($allowedarea,$needle){

		$pList = array();
		

		if (hasParentOnlyPermission($allowedarea,$needle) && $cL = getChildPermissions($allowedarea,$needle)) {
			$pList = array_merge($cL,$pList);
		}
		if (!(ereg($needle,$_SESSION['sess_permission']))) {
			while($parent = getParent($allowedarea,$needle)){
				
				if (hasParentOnlyPermission($allowedarea,$parent)) {
					array_push($pList, $parent);
				}
				$needle = $parent;
			}
		}

		return array_merge($pList,array("System_Admin","_a_0_all"));
	}

	//END
?>