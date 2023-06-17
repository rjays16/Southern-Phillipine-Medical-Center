<?php
	require('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_core.php');
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    include_once($root_path . '/include/care_api_classes/class_user_token.php');
	class RepGen extends Core{
		var $tb_report='seg_rep_templates_registry';
        var $tb_rep_category='seg_reptbl_category';
		var $tb_params='seg_rep_params';
		var $tb_temp_params='seg_rep_template_params';
        var $tb_report_dept = 'seg_rep_templates_dept';
        var $tb_report_dept_params = 'seg_rep_templates_dept_params';

		/**
		* SQL query
		*/
		var $sql;

		/**
		* Constructor
		**/
		function RepGen(){
            $this->CheckLogin();
			$this->bIsConnected = false;
		}

		function getReportCategory(){
			 global $db;

			 $this->sql="SELECT * FROM $this->tb_rep_category ORDER BY name";

			 if ($this->result=$db->Execute($this->sql)) {
					if ($this->count=$this->result->RecordCount()){
						return $this->result;
					}else{
						return FALSE;
					}
			 }else{
					return FALSE;
			 }
		}
        
        function getReportParameter(){
             global $db;

             #edited by VAN 03-02-2013
             $this->sql="SELECT * FROM $this->tb_params WHERE is_active=1 
                         ORDER BY ordering, param_type, parameter";

             if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()){
                        return $this->result;
                    }else{
                        return FALSE;
                    }
             }else{
                    return FALSE;
             }
        }

        #Added by Jarel 05-03-2013
        function getReportParameter2($param_id){
             global $db;

             $this->sql="SELECT * FROM $this->tb_params WHERE is_active=1 
                         AND param_id='$param_id' ORDER BY ordering, param_type, parameter";

             if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()){
                        #added fritz 7/27/18
                        // var_dump($param_id);die;
                        
                            // // if($param_id === "OPDdeptt"){
                                
                            // //         include_once($root_path.'include/care_api_classes/class_department.php');
                            // //         $department = new department();
                                    
                            // //         $departmentList = $department->getAllActiveWithDOC(true);
                                    
                            // //         if($departmentList){
                                    
                            // //             $resultChoices = "";
                                        
                                        
                            // //             if($dept_nr != ''){
                            // //                 if(isset($departmentList[$dept_nr])){
                            // //                     if(isset($genNr[$dept_nr])){
                            // //                         $resultChoices .= "'" . $departmentList[$dept_nr]["nr"] . "-" . $departmentList[$dept_nr]["name_formal"] . "',";
                            // //                         foreach ($departmentList as $key => $value) {
                            // //                             if(in_array($key, $genNr[$dept_nr])){
                            // //                                 $resultChoices .= "'" . $value["nr"] . "-" . str_replace("-", " ", $value["name_formal"])  . "',";
                            // //                             }
                            // //                         }
                            // //                         $resultChoices = rtrim($resultChoices,",");
                            // //                     }else{
                            // //                         $resultChoices .= "'" . $departmentList[$dept_nr]["nr"] . "-" . str_replace("-", " ", $departmentList[$dept_nr]["name_formal"]) . "'";


                            // //                     }

                            // //                 }else{
                            // //                     foreach ($departmentList as $key => $value) {
                            // //                         $resultChoices .= "'" . $value["nr"] . "-" . str_replace("-", " ", $value["name_formal"])  . "',";
                            // //                     }
                            // //                     $resultChoices = rtrim($resultChoices,",");
                            // //                 }

                            // //             }else{

                            // //                 foreach ($departmentList as $key => $value) {
                            // //                     $resultChoices .= "'" . $value["nr"] . "-" . str_replace("-", " ", $value["name_formal"])  . "',";
                            // //                 }
                            // //                 $resultChoices = rtrim($resultChoices,",");
                                            
                                    
                            // //             // var_dump($resultChoices);
                            // //             }
                            // //         }
                                                       
                            // //     $this->result->fields["parameter"] = "Department";
                            // //     $this->result->fields["choices"] = $resultChoices;
                            
                            // }
                            
                        return $this->result;
                    }else{
                        return FALSE;
                    }
             }else{
                    return FALSE;
             }
        }

        #Added by Jarel 05-03-2013
        function getReportParamById($report_id){
             global $db;

             $this->sql="SELECT * FROM $this->tb_temp_params as tp INNER JOIN $this->tb_params as p ON 
             tp.param_id=p.param_id WHERE tp.report_id='$report_id' ORDER BY p.ordering, p.param_type, p.parameter";


             if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()){
                        return $this->result;
                    }else{
                        return FALSE;
                    }
             }else{
                    return FALSE;
             }
        }

         function getReportParamExistById($report_id){
             global $db;
              $this->sql="SELECT * FROM $this->tb_temp_params as tp WHERE tp.report_id='$report_id' AND tp.param_id NOT IN(SELECT param FROM $this->tb_report_dept_params as trdp WHERE trdp.status ='excluded') UNION ALL SELECT trd.report_id,trdp.param FROM $this->tb_report_dept as trd LEFT JOIN $this->tb_report_dept_params as trdp ON trd.id = trdp.id  WHERE trdp.status ='included' AND trd.report_id='$report_id'";


             if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()){
                        return $this->result;
                    }else{
                        return FALSE;
                    }
             }else{
                    return FALSE;
             }
        }

        function checkLogin(){
            $user_token = new UserToken;
            $auth = $user_token->repUserLogin();
        }

}
?>
