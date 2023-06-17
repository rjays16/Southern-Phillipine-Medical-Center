<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
    $params->put("transaction", $transaction);
        
    $sql = "SELECT c.long_name AS blood_component, 

              SUM(CASE WHEN (d.blood_source='DBC' AND d.dept = 'HEART CENTER')  
                THEN 1 ELSE 0 END) AS DBC_MHC,
              SUM(CASE WHEN (d.blood_source='DBC' AND d.dept = 'EENT')  
                THEN 1 ELSE 0 END) AS DBC_EENT,
              SUM(CASE WHEN (d.blood_source='DBC' AND d.dept = 'MED/GEN')  
                THEN 1 ELSE 0 END) AS DBC_MEDGEN,
              SUM(CASE WHEN (d.blood_source='DBC' AND d.dept = 'OB/GYNE')  
                THEN 1 ELSE 0 END) AS DBC_OBGYNE,
              SUM(CASE WHEN (d.blood_source='DBC' AND d.dept = 'ORTHO')  
                THEN 1 ELSE 0 END) AS DBC_ORTHO,
              SUM(CASE WHEN (d.blood_source='DBC' AND d.dept = 'PED/NICU')  
                THEN 1 ELSE 0 END) AS DBC_PEDNICU,
              SUM(CASE WHEN (d.blood_source='DBC' AND d.dept = 'SURGERY')  
                THEN 1 ELSE 0 END) AS DBC_SURGERY,
              SUM(CASE WHEN (d.blood_source='DBC' AND d.dept = 'OTHERS')  
                THEN 1 ELSE 0 END) AS DBC_OTHERS,
                
              SUM(CASE WHEN (d.blood_source='DRHBB' AND d.dept = 'HEART CENTER')  
                THEN 1 ELSE 0 END) AS DRHBB_MHC,
              SUM(CASE WHEN (d.blood_source='DRHBB' AND d.dept = 'EENT')  
                THEN 1 ELSE 0 END) AS DRHBB_EENT,
              SUM(CASE WHEN (d.blood_source='DRHBB' AND d.dept = 'MED/GEN')  
                THEN 1 ELSE 0 END) AS DRHBB_MEDGEN,
              SUM(CASE WHEN (d.blood_source='DRHBB' AND d.dept = 'OB/GYNE')  
                THEN 1 ELSE 0 END) AS DRHBB_OBGYNE,
              SUM(CASE WHEN (d.blood_source='DRHBB' AND d.dept = 'ORTHO')  
                THEN 1 ELSE 0 END) AS DRHBB_ORTHO,
              SUM(CASE WHEN (d.blood_source='DRHBB' AND d.dept = 'PED/NICU')  
                THEN 1 ELSE 0 END) AS DRHBB_PEDNICU,
              SUM(CASE WHEN (d.blood_source='DRHBB' AND d.dept = 'SURGERY')  
                THEN 1 ELSE 0 END) AS DRHBB_SURGERY,
              SUM(CASE WHEN (d.blood_source='DRHBB' AND d.dept = 'OTHERS')  
                THEN 1 ELSE 0 END) AS DRHBB_OTHERS,
                
              SUM(CASE WHEN (d.blood_source='KCBB' AND d.dept = 'HEART CENTER')  
                THEN 1 ELSE 0 END) AS KCBB_MHC,
              SUM(CASE WHEN (d.blood_source='KCBB' AND d.dept = 'EENT')  
                THEN 1 ELSE 0 END) AS KCBB_EENT,
              SUM(CASE WHEN (d.blood_source='KCBB' AND d.dept = 'MED/GEN')  
                THEN 1 ELSE 0 END) AS KCBB_MEDGEN,
              SUM(CASE WHEN (d.blood_source='KCBB' AND d.dept = 'OB/GYNE')  
                THEN 1 ELSE 0 END) AS KCBB_OBGYNE,
              SUM(CASE WHEN (d.blood_source='KCBB' AND d.dept = 'ORTHO')  
                THEN 1 ELSE 0 END) AS KCBB_ORTHO,
              SUM(CASE WHEN (d.blood_source='KCBB' AND d.dept = 'PED/NICU')  
                THEN 1 ELSE 0 END) AS KCBB_PEDNICU,
              SUM(CASE WHEN (d.blood_source='KCBB' AND d.dept = 'SURGERY')  
                THEN 1 ELSE 0 END) AS KCBB_SURGERY,
              SUM(CASE WHEN (d.blood_source='KCBB' AND d.dept = 'OTHERS')  
                THEN 1 ELSE 0 END) AS KCBB_OTHERS,
                  
              SUM(CASE WHEN (d.blood_source='PHOBB' AND d.dept = 'HEART CENTER')  
                THEN 1 ELSE 0 END) AS PHOBB_MHC,
              SUM(CASE WHEN (d.blood_source='PHOBB' AND d.dept = 'EENT')  
                THEN 1 ELSE 0 END) AS PHOBB_EENT,
              SUM(CASE WHEN (d.blood_source='PHOBB' AND d.dept = 'MED/GEN')  
                THEN 1 ELSE 0 END) AS PHOBB_MEDGEN,
              SUM(CASE WHEN (d.blood_source='PHOBB' AND d.dept = 'OB/GYNE')  
                THEN 1 ELSE 0 END) AS PHOBB_OBGYNE,
              SUM(CASE WHEN (d.blood_source='PHOBB' AND d.dept = 'ORTHO')  
                THEN 1 ELSE 0 END) AS PHOBB_ORTHO,
              SUM(CASE WHEN (d.blood_source='PHOBB' AND d.dept = 'PED/NICU')  
                THEN 1 ELSE 0 END) AS PHOBB_PEDNICU,
              SUM(CASE WHEN (d.blood_source='PHOBB' AND d.dept = 'SURGERY')  
                THEN 1 ELSE 0 END) AS PHOBB_SURGERY,
              SUM(CASE WHEN (d.blood_source='PHOBB' AND d.dept = 'OTHERS')  
                THEN 1 ELSE 0 END) AS PHOBB_OTHERS,
                
              SUM(CASE WHEN (d.blood_source='PRC' AND d.dept = 'HEART CENTER')  
                THEN 1 ELSE 0 END) AS PRC_MHC,
              SUM(CASE WHEN (d.blood_source='PRC' AND d.dept = 'EENT')  
                THEN 1 ELSE 0 END) AS PRC_EENT,
              SUM(CASE WHEN (d.blood_source='PRC' AND d.dept = 'MED/GEN')  
                THEN 1 ELSE 0 END) AS PRC_MEDGEN,
              SUM(CASE WHEN (d.blood_source='PRC' AND d.dept = 'OB/GYNE')  
                THEN 1 ELSE 0 END) AS PRC_OBGYNE,
              SUM(CASE WHEN (d.blood_source='PRC' AND d.dept = 'ORTHO')  
                THEN 1 ELSE 0 END) AS PRC_ORTHO,
              SUM(CASE WHEN (d.blood_source='PRC' AND d.dept = 'PED/NICU')  
                THEN 1 ELSE 0 END) AS PRC_PEDNICU,
              SUM(CASE WHEN (d.blood_source='PRC' AND d.dept = 'SURGERY')  
                THEN 1 ELSE 0 END) AS PRC_SURGERY,
              SUM(CASE WHEN (d.blood_source='PRC' AND d.dept = 'OTHERS')  
                THEN 1 ELSE 0 END) AS PRC_OTHERS,
                
              SUM(CASE WHEN (d.blood_source='OTHERS' AND d.dept = 'HEART CENTER')  
                THEN 1 ELSE 0 END) AS OTHERS_MHC,
              SUM(CASE WHEN (d.blood_source='OTHERS' AND d.dept = 'EENT')  
                THEN 1 ELSE 0 END) AS OTHERS_EENT,
              SUM(CASE WHEN (d.blood_source='OTHERS' AND d.dept = 'MED/GEN')  
                THEN 1 ELSE 0 END) AS OTHERS_MEDGEN,
              SUM(CASE WHEN (d.blood_source='OTHERS' AND d.dept = 'OB/GYNE')  
                THEN 1 ELSE 0 END) AS OTHERS_OBGYNE,
              SUM(CASE WHEN (d.blood_source='OTHERS' AND d.dept = 'ORTHO')  
                THEN 1 ELSE 0 END) AS OTHERS_ORTHO,
              SUM(CASE WHEN (d.blood_source='OTHERS' AND d.dept = 'PED/NICU')  
                THEN 1 ELSE 0 END) AS OTHERS_PEDNICU,
              SUM(CASE WHEN (d.blood_source='OTHERS' AND d.dept = 'SURGERY')  
                THEN 1 ELSE 0 END) AS OTHERS_SURGERY,
              SUM(CASE WHEN (d.blood_source='OTHERS' AND d.dept = 'OTHERS')  
                THEN 1 ELSE 0 END) AS OTHERS_OTHERS   
                                      
              FROM seg_blood_component c
              INNER JOIN seg_blood_received_details d ON d.component = c.id
              LEFT JOIN seg_blood_received_status s ON s.refno=d.refno
                AND s.service_code=d.service_code AND s.ordering=d.ordering       
              INNER JOIN seg_lab_serv h ON h.refno=d.refno
              LEFT JOIN seg_blood_type_patient bp ON bp.pid=h.pid
              LEFT JOIN seg_blood_type t ON t.id=bp.blood_type
              LEFT JOIN seg_blood_source bs ON bs.id = d.blood_source
              WHERE d.STATUS IN ('received')

              AND h.is_cash=0 AND (DATE($bb_based_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." )

              AND d.blood_source IS NOT NULL
              GROUP BY d.component
              ORDER BY c.long_name";        
           
    #echo $sql; 
    #exit();
    
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){

        while($row=$rs->FetchRow()){
            
            $data[$rowindex] = array(
                          'blood_component' => $row['blood_component'], 
                          
                          'DBC_MHC'        => (int) $row['DBC_MHC'],
                          'DBC_EENT'       => (int) $row['DBC_EENT'],
                          'DBC_MEDGEN'     => (int) $row['DBC_MEDGEN'],
                          'DBC_OBGYNE'     => (int) $row['DBC_OBGYNE'],
                          'DBC_ORTHO'      => (int) $row['DBC_ORTHO'],
                          'DBC_PEDNICU'    => (int) $row['DBC_PEDNICU'],
                          'DBC_SURGERY'    => (int) $row['DBC_SURGERY'],
                          'DBC_OTHERS'     => (int) $row['DBC_OTHERS'],

                          'DRHBB_MHC'      => (int) $row['DRHBB_MHC'],
                          'DRHBB_EENT'     => (int) $row['DRHBB_EENT'],
                          'DRHBB_MEDGEN'   => (int) $row['DRHBB_MEDGEN'],
                          'DRHBB_OBGYNE'   => (int) $row['DRHBB_OBGYNE'],
                          'DRHBB_ORTHO'    => (int) $row['DRHBB_ORTHO'],
                          'DRHBB_PEDNICU'  => (int) $row['DRHBB_PEDNICU'],
                          'DRHBB_SURGERY'  => (int) $row['DRHBB_SURGERY'],
                          'DRHBB_OTHERS'   => (int) $row['DRHBB_OTHERS'],

                          'KCBB_MHC'       => (int) $row['KCBB_MHC'],
                          'KCBB_EENT'      => (int) $row['KCBB_EENT'],
                          'KCBB_MEDGEN'    => (int) $row['KCBB_MEDGEN'],
                          'KCBB_OBGYNE'    => (int) $row['KCBB_OBGYNE'],
                          'KCBB_ORTHO'     => (int) $row['KCBB_ORTHO'],
                          'KCBB_PEDNICU'   => (int) $row['KCBB_PEDNICU'],
                          'KCBB_SURGERY'   => (int) $row['KCBB_SURGERY'],
                          'KCBB_OTHERS'    => (int) $row['KCBB_OTHERS'],
                          
                          'PHOBB_MHC'      => (int) $row['PHOBB_MHC'],
                          'PHOBB_EENT'     => (int) $row['PHOBB_EENT'],
                          'PHOBB_MEDGEN'   => (int) $row['PHOBB_MEDGEN'],
                          'PHOBB_OBGYNE'   => (int) $row['PHOBB_OBGYNE'],
                          'PHOBB_ORTHO'    => (int) $row['PHOBB_ORTHO'],
                          'PHOBB_PEDNICU'  => (int) $row['PHOBB_PEDNICU'],
                          'PHOBB_SURGERY'  => (int) $row['PHOBB_SURGERY'],
                          'PHOBB_OTHERS'   => (int) $row['PHOBB_OTHERS'],

                          'PRC_MHC'        => (int) $row['PRC_MHC'],
                          'PRC_EENT'       => (int) $row['PRC_EENT'],
                          'PRC_MEDGEN'     => (int) $row['PRC_MEDGEN'],
                          'PRC_OBGYNE'     => (int) $row['PRC_OBGYNE'],
                          'PRC_ORTHO'      => (int) $row['PRC_ORTHO'],
                          'PRC_PEDNICU'    => (int) $row['PRC_PEDNICU'],
                          'PRC_SURGERY'    => (int) $row['PRC_SURGERY'],
                          'PRC_OTHERS'     => (int) $row['PRC_OTHERS'],

                          'OTHERS_MHC'     => (int) $row['OTHERS_MHC'],
                          'OTHERS_EENT'    => (int) $row['OTHERS_EENT'],
                          'OTHERS_MEDGEN'  => (int) $row['OTHERS_MEDGEN'],
                          'OTHERS_OBGYNE'  => (int) $row['OTHERS_OBGYNE'],
                          'OTHERS_ORTHO'   => (int) $row['OTHERS_ORTHO'],
                          'OTHERS_PEDNICU' => (int) $row['OTHERS_PEDNICU'],
                          'OTHERS_SURGERY' => (int) $row['OTHERS_SURGERY'],
                          'OTHERS_OTHERS'  => (int) $row['OTHERS_OTHERS'],

                          );
                          
           $rowindex++;
        }  

          #print_r($data);
          #exit();
    }else{
        $data[0]['blood_component'] = NULL; 
    }  
