<?php
	# created by VAN 01-12-2012
	# using HL7 approach
	# parse a HL7 message for lab result that fetch from LIS
    
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	
	class seg_parse_msg_HL7{

        var $delimiter;
        
		# constructor
		function seg_parse_msg_HL7(){
		    $this->delimiter = "\015";
		    $this->prefix = "HIS";
		    $this->ORC_STATUS_CHANGE = "SC"; //marks status change in HL7 (ORC 1)
		    $this->ORC_SCAN_COMPLETED = "CM"; //service completed scanning. mark as served (ORC 5)
		    $this->ORC_SCAN_START = "A"; //service started scanning (ORC 5)
		    $this->ORC_CANCEL = "CA"; //cancel request (ORC 1)
		    $this->ORC_RESULT = "RE"; //encode result
		    $this->COMPONENT_SEPARATOR = "^";
		    $this->REPETITION_SEPARATOR = "~";
            $this->PAX_URL_INDEX = 7; //FOR OBX URL SEGMENT NUMBER
		}

        function parse(){
			#$fp = file_get_content('file.hl7');
			$fp = fopen('myfile.hl7', 'r'); 
			while (!feof($fp)) { 
				$line = fread($fp, 10240); 
				$data = explode('|', trim($line)); 
				/* 
				Now $data contains one record's worth of data; 
				you can write it into your database 
				*/ 
				print_r($data);
			} 
		}


		#Message Header
		/*sample
            MSH|^~\&|SEGHIS|SPMC|HCLAB|SPMC|20120131202201| |ORM^O01|HIS00001|P|2.3<cr>
                  1    2     3     4     5     6           7    8      9      10 11 12
	    */
		function segment_msh($data){
			$msg_type = explode($this->COMPONENT_SEPARATOR, $data[8]);
		    $msh['msg_type_id'] = $msg_type[0];
		    $msh['event_id'] = $msg_type[1];
		    $msh['hclab_msg_control_nr'] = $data[9];
		    $msh['date_reported'] = $data[6];
		    
		    return $msh;
		}


		#Message Acknowledgment Segment
		/*sample
            #with error    
            MSA|AE|HIS00004|   |   |    |^Invalid Birth date format<cr>
                 1    2      3   4    5         6
                 
            #accepted
            MSA|AA|HCL10021|   |    |    |
                 1     2     3   4     5
            
            #rejected
            MSA|AR|HIS10008|    |    |    |^Order already exist. Request rejected.     
                1     2       3    4    5       6
        */
		function segment_msa($data){
			$msa['ack_code'] = $data[1];
		    $start_pos = strrpos($data[2], $this->prefix);
		    $start = strrpos($data[2], $this->prefix) + strlen($this->prefix);
		    $msa['msg_control_id'] = substr($data[2], $start);
		    
			return $msa;
		}

		/*sample
            PID|1|   |2000005|   |JOHNNY^LEE|SY|19800919000000|M|   |    |^BAJADA^DAVAO CITY^8000^DAVAO DEL SUR |   |    |   |    |SINGLE|    |    |    |<cr>
                1  2     3     4      5      6      7          8  9   10             11                           12  13   14  15    16    17   18   19
        */
		function segment_pid($data){
			$pid['pid'] = $data[3];
			$pid['name'] = $data[5];
			$pid['middle_name'] = $data[6];
			$pid['bdate'] = date('Y-m-d H:i:s',strtotime($data[7]));
			$pid['sex'] = $data[8];

			return $pid;
		}

		/*sample
             	[0] => OBR
			    [1] => 1
			    [2] => 2014000043
			    [3] => 2014000043
			    [4] => CPA^Chest PA (ADULT)
			    [5] => 
			    [6] => 
			    [7] => 20141009000248
			    [8] => 
			    [9] => 
			    [10] => 
			    [11] => 
			    [12] => 
			    [13] => 
			    [14] => 
			    [15] => 
			    [16] => 
			    [17] => 
			    [18] => 
			    [19] => 
			    [20] => 
			    [21] => 
			    [22] => 
			    [23] => 
			    [24] => 
			    [25] => F (final), P (preliminary)
			    [26] => 

             if ORU^R01, only one test code is returned    
        */
		function segment_obr($data){
			$obr['pacs_order_no'] = $data[2];
			$obr['lab_no'] = $data[3];
			$obr['physician'] = $data[32];
			$obr['physician_transcribe'] = $data[35];
			$obr['location'] = $data[18];
			
			$obr['test'] = $data[4];
			$obr['result_status'] = $data[25];

			$obr['date_received'] = $data[7];

			return $obr;
		}


		/*sample
             	[0] => OBX
			    [1] => 1
			    [2] => RP
			    [3] => 
			    [4] => 
			    [5] => FINDINGS
			    [6] => IMPRESSION
			    [7] => http://192.168.11.4/Portal/Login.aspx?patient_id=1533889&accession_number=2014000020
			    [8] =>

			    RP = reference pointer
        */
		#multiple
        function segment_obx($data){
        	// echo "<pre>" . print_r($data,true) . "</pre>";exit();
        	$obx['ordering'] = $data[1];
        	$obx['reftype'] = $data[2];
        	$obx['testservice'] = $data[3];
        	$obx['result_findings'] = $data[5];
        	$obx['result_impression'] = $data[6];
        	$obx['url'] = $data[$this->PAX_URL_INDEX]; //change value for PAX_URL_INDEX at constructor
        	$obx['units'] = $data[6];
        	$obx['reference_range'] = $data[7];
        	$obx['result_flag'] = $data[8];
        	$obx['result_status'] = $data[11];
        	$obx['date_observed'] = $data[14];
        	$obx['medtech'] = $data[16];

        	if($obx['ordering'] != 1)
        		$obx['url'] = $data[6];

        	return $obx;
        }

        /*sample
        	notes and comments
			NTE|1|   |This Patient is suspect to have dengue<cr>
			    1  2     3	 

        	NTE|1||\\\\.br\\\\CONVERSION FACTOR: uL TO HPF (X0.18) , uL TO LPF (X2.9)

        */     
        function segment_nte($data, $index){
        	$nte['comment'] = $data[3];
        	$nte['index'] = $index;
        	return $nte;
        }

        /*sample
             	[0] => PV1
			    [1] => 1
			    [2] => I
			    [3] => 
			    [4] => 
			    [5] => 
			    [6] => 
			    [7] => 250^Mosqueda^Alain Tracy
			    [8] => 248^ARNAIZ, EVANGELINE P.
			    [9] => 
			    [10] => 
			    [12] => 
			    [13] => 
			    [14] => 
			    [15] => 
			    [16] => 
			    [17] => 
			    [18] => 
			    [19] => 2015001399
        */
		#multiple
        function segment_pv1($data){
        	$pv1['encoder'] = $data[7];

        	return $pv1;
        }

        /*sample
             	[0] => ORC
			    [1] => CA (cancelled), SC (scan startded), RE (result)
			    [2] => 2015205767
			    [3] => 
			    [4] => 
			    [5] => A (start), CM (complete)
			    [6] => 
			    [7] => ^^5^20151118223100.0000+08:00^20151118223600.0000+08:00
			    [8] =>
			    [9] =>20151130165305.0000+08:00
			    [10] =>134^Mag-aso^Zhernan
			    [11] =>
			    [12] => ^TOMINDUG^SAIBAH
        */
		#multiple
        function segment_orc($data){
        	$orc['status'] = $data[1];
        	$orc['status_info'] = $data[5];
        	$orc['datetime'] = $data[9];
        	$orc['rad_tech'] = $data[10];
        	$orc['pacs_order_no'] = $data[2];

        	return $orc;
        }


		function parse_segment($hl7_msg){
			$segments = explode($this->delimiter, trim($hl7_msg));

			foreach($segments as $segment) {
			    $data = explode('|', trim($segment));
			    
			    if (in_array("MSH", $data)) {

			    }
			    	
			    #save segment to table
			}
		}

    }
    
    #------- end of class--------

?>
