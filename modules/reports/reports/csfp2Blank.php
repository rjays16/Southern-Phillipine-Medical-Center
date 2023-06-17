<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    include_once($root_path.'include/care_api_classes/class_globalconfig.php');
    include('parameters.php');

    /**
    * Created By Jeff Ponteras
    * Created On 03/26/2018
    * @param string Blank
    * @return Blank
    **/
  
        $rowspergroup = 3;
        $addrows = ($rowspergroup - $rowindex % 3);
        $totalrows = $addrows + $rowindex;
        $rowindex++;
        while ($rowindex <= $totalrows) {
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                                     'groupidx' => $grpindex,
                                     'accreditation_nr' => "",
                                     'name_last' => "",
                                     'name_first' => "",
                                     'name_middle' => "",
                                     'suffix' => "",
                                     'date_signed' => ""
                                    );               
            $rowindex++;
        }  
  


   
    