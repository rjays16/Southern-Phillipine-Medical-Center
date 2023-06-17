<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    #require_once($root_path.'include/inc_environment_global.php');
    
    global $db;
    
    $report = $_GET['reportid'];
    $repformat = $_GET['repformat'];
    $report_name = $report;
    $dept = $_GET['dept_nr'];
    $admissionDt = $_GET['admissionDt'];
    $admissionformat = date('Y-m-d H:m:s', strtotime($admissionDt)); 

    $fromdte = $_GET['from_date'];

    $todte = $_GET['to_date'];
    $from_date = strftime("%Y-%m-%d", $fromdte);
    $to_date   = strftime("%Y-%m-%d", $todte);

    $from_date_format = strftime("%Y-%m-%d", $fromdte);
    $to_date_format   = strftime("%Y-%m-%d", $todte);
    include($root_path.'modules/reports/render_report.php');
?>
