<?php
/*
 * @package care_api
 */

require_once($root_path . 'include/care_api_classes/class_core.php');

class CounseledSlip extends Core
{
    /*
     * Database table for the medical certificate info.
     * @var string
     */
    var $tb_care_person = 'care_person';
    var $tb_seg_diet_order = 'seg_diet_order';
    var $tb_seg_diet_order_item = 'seg_diet_order_item';
    var $tb_care_encounter = 'care_encounter';
    var $tb_seg_counseled = 'seg_counseled';
    var $tb_seg_counseled_discharged = 'seg_counseled_discharged';
    var $tb_seg_counseled_monitoring = 'seg_counseled_monitoring';

    /*
     * Fieldnames of seg_counseled table. "encounter_nr".
     * @var array
     */

    var $fld_seg_counseled = array(
        'pid',
        'encounter_nr',
        'visited_dt',
        'assessment',
        'plan',
        'suggested_diet',
        'updated_diet',
        'in_charged',
        'followup_dt',
        'create_id',
        'create_dt',
        'modify_id',
        'modify_dt',
        'is_canceled',
    );

    var $fld_seg_counseled_discharged = array(
        'pid',
        'encounter_nr',
        'visited_dt',
        'assessment',
        'plan',
        'status',
        'isreferral',
        'suggested_diet',
        'updated_diet',
        'in_charged',
        'followup_dt',
        'create_id',
        'create_dt',
        'modify_id',
        'modify_dt',
        'is_canceled',
    );

    var $fld_seg_counseled_monitoring = array(
        'pid',
        'encounter_nr',
        'visited_dt',
        'assessment',
        'plan',
        'status',
        'isreferral',
        'suggested_diet',
        'updated_diet',
        'in_charged',
        'followup_dt',
        'create_id',
        'create_dt',
        'modify_id',
        'modify_dt',
        'is_canceled',
    );

    var $encounter_nr;

    /*
     * Class Constructor
     * @param string $encounter_nr
     */
    function __construct($encounter_nr = '')
    {
        $this->encounter_nr = $encounter_nr;
    }



    function getCouseled()
    {
        global $db;

        $encounter_nr = $this->encounter_nr;
        if (empty($encounter_nr) || (!$encounter_nr))
            return FALSE;

        $this->sql = "SELECT * FROM $this->tb_seg_counseled 
        WHERE encounter_nr=" . $db->qstr($encounter_nr) . " ORDER BY create_dt,modify_dt DESC";

        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function getCouseledDischarged($flag)
    {
        global $db;

        $encounter_nr = $this->encounter_nr;
        if (empty($encounter_nr) || (!$encounter_nr))
            return FALSE;

        $this->sql = "SELECT * FROM $this->tb_seg_counseled_discharged
         WHERE encounter_nr=" . $db->qstr($encounter_nr) . " AND 
         isreferral = " . $db->qstr($flag) .
            " AND is_canceled  = 0 ORDER BY create_dt,modify_dt DESC";

        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function getCouseledMonitoring($flag)
    {
        global $db;

        $encounter_nr = $this->encounter_nr;
        if (empty($encounter_nr) || (!$encounter_nr))
            return FALSE;

        $this->sql = "SELECT * FROM $this->tb_seg_counseled_monitoring 
        WHERE encounter_nr=" . $db->qstr($encounter_nr) . " AND 
        isreferral = " . $db->qstr($flag) .
            " AND is_canceled  = 0 ORDER BY create_dt,modify_dt DESC";

        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function getPid()
    {
        global $db;

        $encounter_nr = $this->encounter_nr;
        if (empty($encounter_nr) || (!$encounter_nr))
            return FALSE;

        $this->sql = "SELECT pid FROM $this->tb_care_encounter 
        WHERE encounter_nr=" . $db->qstr($encounter_nr);

        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function getPersonDetail($pid)
    {
        global $db;



        $this->sql = "SELECT 
        pid, 
        name_last, 
        name_first, 
        name_middle, 
        sex, 
        TIMESTAMPDIFF(YEAR,care_person.date_birth,DATE(NOW())) AS 'age'
        FROM $this->tb_care_person 
        WHERE pid=" . $db->qstr($pid);

        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function getOrderType()
    {
        global $db;

        $encounter_nr = $this->encounter_nr;
        if (empty($encounter_nr) || (!$encounter_nr))
            return FALSE;

        $this->sql = "SELECT selected_type, refno FROM $this->tb_seg_diet_order 
        WHERE encounter_nr=" . $db->qstr($encounter_nr);

        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function getDietOrder($orderType, $refno)
    {
        global $db;
        $orderType;
        if ($orderType == 'breakfast') {
            $orderType = 'b';
        } else if ($orderType == 'lunch') {
            $orderType = 'l';
        } else if ($orderType == 'dinner') {
            $orderType = 'd';
        } else {
            // $orderType = '';
        }
        $encounter_nr = $this->encounter_nr;
        if (empty($encounter_nr) || (!$encounter_nr))
            return FALSE;

        $this->sql = "SELECT $orderType FROM $this->tb_seg_diet_order_item 
        WHERE refno=" . $db->qstr($refno);

        if ($buf = $db->Execute($this->sql)) {
            if ($buf->RecordCount()) {
                return $buf->FetchRow();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
} # end class ConReferral
