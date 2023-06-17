<?php 

  /**
   * 
   */
  class Report
  {
    
    function select_global($db, $report_name, $admissionformat)
    {
      $getReportName = "SELECT value FROM care_config_global WHERE type = 'new_report_jrxml'";
        $result_global = $db->Execute($getReportName);
        $row_global = $result_global->FetchRow();
        $value = $row_global['value'];
        $reportId = explode(',', $value);

        $filter = 'eclaims_inCharge';

        $strSQL = "SELECT ccg.type, ccg.value FROM care_config_global AS ccg
                    WHERE ccg.type = '$filter'";

        $result = $db->Execute($strSQL);
        $row_index = $result->FetchRow();
        $value = $row_index['value'];
        $date_effectivity = explode(',', $value);

            if ($report_name == $reportId[6]) {
                if ($admissionformat >= $date_effectivity[7]) 
                {
                    $report_name = $reportId[6];
                }else 
                {   
                    $report_name = $reportId[7];
                }
            }elseif ($report_name == $reportId[0]) {
                if ($admissionformat >= $date_effectivity[7]) 
                {
                    $report_name = $reportId[0];
                }else 
                {
                    $report_name = $reportId[9];
                }
            }else{
                $report_name = $report_name;
            }

        return $report_name;
    }
  }

 ?>