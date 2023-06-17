<?php

require("./roots.php");
require_once($root_path . 'include/care_api_classes/class_core.php');
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');

/**
 * Class that handles failed EMR Request
 * @author Justin T. Tan
 */
class Staging extends Core {
    var $sql;

    protected $type = null;
    protected $status = null;
    protected $parameter = array();

    function array_map_recursive($callback, $array) {
        foreach ($array as $key => $value) {
            if (is_array($array[$key])) {
                $array[$key] = $this->array_map_recursive($callback, $array[$key]);
            }
            else {
                $array[$key] = call_user_func($callback, $array[$key]);
            }
        }
        return $array;
    }

    /**
     * Constructor
     */
    public function __construct($options = array()) {
        $defaults = array(
            'type' => null,
            'status' => null,
            'parameter' => array(),
        );

        $options = array_merge($defaults, $options);

        $this->type = $options['type'];

        // $data = $this->array_map_recursive("utf8_encode", $options['parameter']);
        // $jsonData = json_encode($data);
        // $this->parameter = $jsonData;
        $this->parameter = $options['parameter'];

        $this->status = $options['status'];
    }

    public function exec(){
        global $db;
        
        $date_posted = date("Y-m-d H:i:s");

        $sql = "SELECT UUID() AS id";
        $id = $db->GetOne($sql);

        $result = $db->Replace('seg_emr_staging',
            array(
                'id'=>$db->qstr($id),
                'type' => $db->qstr($this->type),
                'status'=>$db->qstr($this->status),
                'parameter'=>$db->qstr($this->parameter),
                'date_posted'=>$db->qstr($date_posted),
            ),
            array('id'),
            $autoquote=FALSE
        );

        if ($result) 
            return "Successful";
        else{
            return "error : ".$db->ErrorMsg();
        } 
    }

    // function saveFailedLabRequest(){
    //     global $db;
    //     $header = "(".$db->qstr($this->data['PatientId']).','
    //         .$db->qstr($this->data['CaseNumber']).','.$db->qstr($this->data['IsCash']).','
    //         .$db->qstr($this->data['ReferenceBatchNumber']).','.$db->qstr($this->data['DoctorId']).','
    //         .$db->qstr($this->data['ClinicalImpression']).','.$db->qstr($this->data['IsUrgent']?$this->data['IsUrgent']:0).',';

    //     $footer = $db->qstr($this->message).','.$db->qstr($this->method).', 0)';
    //     $sqlValue = '';
    //     foreach ($this->data['RequisitionOrderList'] as $key => $value) {
    //         $body = $db->qstr($value['SectionCode']).','.$db->qstr($value['TestCode']).','
    //             .$db->qstr($value['TestName']).',';

    //         if($sqlValue)
    //             $sqlValue .= ", ".$header.$body.$footer;
    //         else
    //             $sqlValue = $header.$body.$footer;
    //     }

    //     $this->sql = "INSERT INTO seg_staging_lab_request
    //                     (PatientId, CaseNumber, IsCash, ReferenceBatchNumber, 
    //                         DoctorId, ClinicalImpression, IsUrgent, SectionCode,
    //                         TestCode, TestName, message, method, is_posted_emr)
    //                     VALUES ". $sqlValue;
        
    //     $db->Execute($this->sql);
    // }

    // function saveFailedRadRequest(){
    //     global $db;
    //     $header = "(".$db->qstr($this->data['PatientId']).','
    //         .$db->qstr($this->data['CaseNumber']).','.$db->qstr($this->data['IsCash']).','
    //         .$db->qstr($this->data['ReferenceBatchNumber']).','.$db->qstr($this->data['DoctorId']).','
    //         .$db->qstr($this->data['ClinicalImpression']).','.$db->qstr($this->data['IsUrgent']?$this->data['IsUrgent']:0).',';

    //     $footer = $db->qstr($this->message).','.$db->qstr($this->method).', 0)';
    //     $sqlValue = '';
    //     foreach ($this->data['RequisitionOrderList'] as $key => $value) {
    //         $body = $db->qstr($value['ReferenceNumber']).','.$db->qstr($value['SectionCode']).','
    //             .$db->qstr($value['TestCode']).',';

    //         if($sqlValue)
    //             $sqlValue .= ", ".$header.$body.$footer;
    //         else
    //             $sqlValue = $header.$body.$footer;
    //     }

    //     $this->sql = "INSERT INTO seg_staging_rad_request
    //                     (PatientId, CaseNumber, IsCash, ReferenceBatchNumber, 
    //                         DoctorId, ClinicalImpression, IsUrgent, ReferenceNumber,
    //                          SectionCode, TestCode, message, method, is_posted_emr)
    //                     VALUES ". $sqlValue;
        
    //     $db->Execute($this->sql);
    // }
}