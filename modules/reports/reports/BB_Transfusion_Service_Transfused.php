<?php
#Created by: Borj
#Date/Time: 2014-07-30
#Bloob Bank Transfusion Service Report
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');

    $sql1 ="SELECT
                sbc.`long_name` as components,
                SUM(sbrd.`blood_source` = 'DBC' AND sbrd.`dept` = 'HEART CENTER') AS dbcmhc,
                SUM(sbrd.`blood_source` = 'DBC' AND sbrd.`dept` = 'MED/GEN') AS dbcmedgen,
                SUM(sbrd.`blood_source` = 'DBC' AND sbrd.`dept` = 'OB/GYNE') AS dbcobgyne,
                SUM(sbrd.`blood_source` = 'DBC' AND sbrd.`dept` = 'SURGERY') AS dbcsurgery,
                SUM(sbrd.`blood_source` = 'DBC' AND sbrd.`dept` = 'PED/NICU') AS dbcpednicu,
                SUM(sbrd.`blood_source` = 'DBC' AND sbrd.`dept` = 'ORTHO') AS dbcortho,
                SUM(sbrd.`blood_source` = 'DBC' AND sbrd.`dept` = 'EENT') AS dbceent,
                SUM(sbrd.`blood_source` = 'DBC' AND sbrd.`dept` = 'OTHERS') AS dbcother,

                SUM(sbrd.`blood_source` = 'PRC' AND sbrd.`dept` = 'HEART CENTER') AS pnrcmhc,
                SUM(sbrd.`blood_source` = 'PRC' AND sbrd.`dept` = 'MED/GEN') AS pnrcmedgen,
                SUM(sbrd.`blood_source` = 'PRC' AND sbrd.`dept` = 'OB/GYNE') AS pnrcobgyne,
                SUM(sbrd.`blood_source` = 'PRC' AND sbrd.`dept` = 'SURGERY') AS pnrcsurgery,
                SUM(sbrd.`blood_source` = 'PRC' AND sbrd.`dept` = 'PED/NICU') AS pnrcpednicu,
                SUM(sbrd.`blood_source` = 'PRC' AND sbrd.`dept` = 'ORTHO') AS pnrcortho,
                SUM(sbrd.`blood_source` = 'PRC' AND sbrd.`dept` = 'EENT') AS pnrceent,
                SUM(sbrd.`blood_source` = 'PRC' AND sbrd.`dept` = 'OTHERS') AS pnrcother,
                
                SUM(sbrd.`blood_source` = 'PHOBB' AND sbrd.`dept` = 'HEART CENTER') AS phomhc,
                SUM(sbrd.`blood_source` = 'PHOBB' AND sbrd.`dept` = 'MED/GEN') AS phomedgen,
                SUM(sbrd.`blood_source` = 'PHOBB' AND sbrd.`dept` = 'OB/GYNE') AS phoobgyne,
                SUM(sbrd.`blood_source` = 'PHOBB' AND sbrd.`dept` = 'SURGERY') AS phosurgery,
                SUM(sbrd.`blood_source` = 'PHOBB' AND sbrd.`dept` = 'PED/NICU') AS phopednicu,
                SUM(sbrd.`blood_source` = 'PHOBB' AND sbrd.`dept` = 'ORTHO') AS phoortho,
                SUM(sbrd.`blood_source` = 'PHOBB' AND sbrd.`dept` = 'EENT') AS phoeent,
                SUM(sbrd.`blood_source` = 'PHOBB' AND sbrd.`dept` = 'OTHERS') AS phoother,
                
                SUM(t.GROUP = 'A' AND sbrd.`blood_source` = 'DBC') AS adbc,
                SUM(t.GROUP = 'A' AND sbrd.`blood_source` = 'PRC') AS apnrc,
                SUM(t.GROUP = 'A' AND sbrd.`blood_source` = 'PHOBB') AS apho,
                SUM(t.GROUP = 'A' AND sbrd.`blood_source` = 'OTHERS') AS aothers,
                
                SUM(t.GROUP = 'B' AND sbrd.`blood_source` = 'DBC') AS bdbc,
                SUM(t.GROUP = 'B' AND sbrd.`blood_source` = 'PRC') AS bpnrc,
                SUM(t.GROUP = 'B' AND sbrd.`blood_source` = 'PHOBB') AS bpho,
                SUM(t.GROUP = 'B' AND sbrd.`blood_source` = 'OTHERS') AS bothers,
                
                SUM(t.GROUP = 'O' AND sbrd.`blood_source` = 'DBC') AS odbc,
                SUM(t.GROUP = 'O' AND sbrd.`blood_source` = 'PRC') AS opnrc,
                SUM(t.GROUP = 'O' AND sbrd.`blood_source` = 'PHOBB') AS opho,
                SUM(t.GROUP = 'O' AND sbrd.`blood_source` = 'OTHERS') AS others,
                
                SUM(t.GROUP = 'AB' AND sbrd.`blood_source` = 'DBC') AS abdbc,
                SUM(t.GROUP = 'AB' AND sbrd.`blood_source` = 'PRC') AS abpnrc,
                SUM(t.GROUP = 'AB' AND sbrd.`blood_source` = 'PHOBB') AS abpho,
                SUM(t.GROUP = 'AB' AND sbrd.`blood_source` = 'OTHERS') AS abothers 
              FROM seg_blood_received_status sbrs
              LEFT JOIN seg_blood_received_details sbrd
              ON sbrd.`refno` = sbrs.`refno`
              AND sbrd.`service_code` = sbrs.`service_code` 
              AND sbrd.`ordering` = sbrs.`ordering`  
              LEFT JOIN seg_lab_serv sls 
              ON sbrd.`refno` = sls.`refno`
              LEFT JOIN seg_blood_component AS sbc 
              ON sbc.`id` = sbrd.`component` 
              LEFT JOIN seg_blood_type_patient sbtp 
              ON sls.`pid` = sbtp.`pid` 
              LEFT JOIN seg_blood_type t 
              ON sbtp.`blood_type` = t.`id` 
              WHERE DATE(sbrs.`issuance_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format))."
              GROUP BY sbrd.`component`";

     $rs1 = $db->Execute($sql1);

     #echo $sql1;
     #exit();
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs1)){
        while($row=$rs1->FetchRow()){
           $data[$rowindex] = array('components' => $row['components'],

                          'dbcmhc'     => (int) $row['dbcmhc'],
                          'dbcmedgen'  => (int) $row['dbcmedgen'],
                          'dbcobgyne'  => (int) $row['dbcobgyne'],
                          'dbcsurgery' => (int) $row['dbcsurgery'],
                          'dbcpednicu' => (int) $row['dbcpednicu'],
                          'dbcortho'   => (int) $row['dbcortho'],
                          'dbceent'    => (int) $row['dbceent'],
                          'dbcother'   => (int) $row['dbcother'],

                          'pnrcmhc'    => (int) $row['pnrcmhc'],
                          'pnrcmedgen' => (int) $row['pnrcmedgen'],
                          'pnrcobgyne' => (int) $row['pnrcobgyne'],
                          'pnrcsurgery'=> (int) $row['pnrcsurgery'],
                          'pnrcpednicu'=> (int) $row['pnrcpednicu'],
                          'pnrcortho'  => (int) $row['pnrcortho'],
                          'pnrceent'   => (int) $row['pnrceent'],
                          'pnrcother'  => (int) $row['pnrcother'],

                          'phomhc'     => (int) $row['phomhc'],
                          'phomedgen'  => (int) $row['phomedgen'],
                          'phoobgyne'  => (int) $row['phoobgyne'],
                          'phosurgery' => (int) $row['phosurgery'],
                          'phopednicu' => (int) $row['phopednicu'],
                          'phoortho'   => (int) $row['phoortho'],
                          'phoeent'    => (int) $row['phoeent'],
                          'phoother'   => (int) $row['phoother'],

                          'adbc'       => (int) $row['adbc'],
                          'apnrc'      => (int) $row['apnrc'],
                          'apho'       => (int) $row['apho'],
                          'aothers'    => (int) $row['aothers'],

                          'bdbc'       => (int) $row['bdbc'],
                          'bpnrc'      => (int) $row['bpnrc'],
                          'bpho'       => (int) $row['bpho'],
                          'bothers'    => (int) $row['bothers'],

                          'odbc'       => (int) $row['odbc'],
                          'opnrc'      => (int) $row['opnrc'],
                          'opho'       => (int) $row['opho'],
                          'others'     => (int) $row['others'],

                          'abdbc'      => (int) $row['abdbc'],
                          'abpnrc'     => (int) $row['abpnrc'],
                          'abpho'      => (int) $row['abpho'],
                          'abothers'   => (int) $row['abothers'],


                          );
           $rowindex++;
        }
         
    }else{
       $data[0]['components'] = 'No Records'; 
    }
        
