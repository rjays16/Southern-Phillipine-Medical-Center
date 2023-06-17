<?php
/**
* @package SegHIS_api
*
* Class containing all properties and methods related to an encounter's billing For New PHIC Circular.
*
* Note this class should be instantiated only after a "$db" adodb  connector object
* has been established by an adodb instance.
*
* @author     : Jarel Q. Mamac
* @version    : 1.0
* @Created on : November 10, 2013
*   
*
***/

require_once('roots.php');
require_once($root_path.'include/care_api_classes/billing/class_coverage.php');
require_once($root_path.'include/care_api_classes/billing/class_accommodation.php');
require_once($root_path.'include/care_api_classes/billing/class_medicine.php');
require_once($root_path.'include/care_api_classes/billing/class_supply.php');
require_once($root_path.'include/care_api_classes/billing/class_services.php');
require_once($root_path.'include/care_api_classes/billing/class_bill_ops.php');
require_once($root_path.'include/care_api_classes/billing/class_prof_fees.php');
require_once($root_path.'include/care_api_classes/billing/class_payment.php');
require_once($root_path.'include/care_api_classes/billing/class_actual_coverage.php');
require_once($root_path.'include/care_api_classes/billing/class_pf_claim.php');
require_once($root_path.'include/care_api_classes/billing/class_msc_chrg.php');
require_once($root_path.'include/care_api_classes/billing/class_billing_discount.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_credit_collection.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');

define('ER_PATIENT', 1);
define('OUT_PATIENT', 2);
define('DIALYSIS_PATIENT', 5);
define('WELLBABY', 12); 
define('DEFAULT_PCF', 40);
define('CHARITY', 'CHARITY');
define('CHARITYWARD', 1);
define('NOBALANCEBILLING','NBB');
define('INFIRMARY', 'PHS');
define('SENIORCITIZEN', 'SENIOR'); 
define('OBANNEX', 'OB-ANNEX'); 
define('SERVICEWARD', 'SERVICE'); 
define('ANNEXWARD', 'ANNEX'); 
define('ICUWARD','ICU'); 
define('NEWBORN_A', 24);     
define('NEWBORN_B', 27);       
define('DEFAULT_NBPKG_RATE', 1750);  
define('SKED_EFFECTIVITY','2010-10-07');    
define('ISSRVD_EFFECTIVITY', '2012-10-09');
define('PHIC_ID', 18);
define('OBANNEX', 'OB-ANNEX');
define('infirmary_effectivity', '2015-02-01 00:00:01');
define('infirmary_effectivity_per_encounter', date('Y-m-d H:i:s', strtotime('2015-03-17 00:00:01')));
define('BSKED_ID_MEDS',77);
define('BSKED_ID_XLO',78);
# added by: syboy 10/11/2015
define('evisceration_without_implant', 65091);
define('evisceration_implant', 65093);
define('enucleation_without_implant', 65101);
define('enucleation_implant_not_attached', 65103);
define('enucleation_implant_attached', 65105);
define('exenteration_orbit_without_skin_graft_content_only', 65110);
define('exenteration_orbit_without_skin_graft', 65112);
define('exenteration_orbit_without_skin_graft_muscle_flap', 65114);
define('confNbb', 7);
# ended

// Added by Gervie 03-19-2017
define('HIGH_FLUX', '201400002960');

// Unknown
define('MINDANAO_DIALYSIS_CENTER', 144);
define('RADIOLOGY_ONCOLOGY', 253);

class Billing extends Core {

    const SPONSORED_MEMBER = 5,
          HOSPITAL_SPONSORED_MEMBER = 9,
          KASAM_BAHAY = 11,
          LIFETIME_MEMBER = 6,
          SENIOR_CITIZEN = 10,
          POINT_OF_SERVICE = 13,
          NBB_EFFECTIVE_DATE = '4/20/2015',

          HEMO_DIALYSIS_PROCEDURE = '90935';

    var $encounter_nr;
    var $prev_encounter = '';
    var $prev_encounter_no = ''; //added by poliam 01/04/2014
    var $old_bill_nr;
    var $bill_dte;
    var $bill_frmdte;
    var $death_date;
    var $is_died;
    var $is_final;
    var $pkgamountlimit = 0;
    var $cutoff_hrs = 0;
    var $is_coveredbypkg;
    var $confinetype_id;
    var $accomm_typ_nr;
    var $accomm_typ_desc;
    var $accomm_ward_name;
    var $charged_date;
    var $accommodation_hist; //added by nick 01/06/2014
    // Added by James 1/6/2014
    var $error_no;
    var $error_msg;
    var $error_sql;
    //var $error_final;
    // End James
    var $memcategory_id;
    var $accomodation_type; 
    var $caseTypeHist,$memCatHist; //added by nick 05/06/2014
    var $cur_billdate;
    var $greater_accom_effec; //added by carriane 03/26/19

    function setBillArgs($enc,$bill_dte,$bill_frmdte,$death_date ='',$bill_nr='')
    {

        $this->death_date = $death_date;
        $this->bill_frmdte = $bill_frmdte;
        $this->encounter_nr = $enc;
        $this->current_enr = $enc;
        $this->old_bill_nr = $bill_nr;
        $this->bill_dte = $bill_dte;
        $this->charged_date = strftime("%Y-%m-%d %H:%M:%S");
        // $this->charged_date = $bill_dte;

        $this->getPrevEncounterNr();    // Get parent encounter no., if there is ...

        if($this->prev_encounter != '') {
            $this->bill_frmdte = $this->getEncounterDte();
        }

        $this->is_final = $this->isFinal();

        $this->is_coveredbypkg = 0;
        $n_id = $this->getConfinementType();


        if ($old_billnr != '') {
            $ncutoff = $this->getAppliedHrsCutoff();
            $this->correctBillDates();
        }
        else
            $ncutoff = -1;

        $hosp_obj = new Hospital_Admin();
        $this->cutoff_hrs = ($ncutoff == -1) ? $hosp_obj->getCutoff_Hrs() : $ncutoff;
        $this->pcf = $hosp_obj->getDefinedPCF();
        $this->pcf = ($this->pcf == 0) ? DEFAULT_PCF : $this->pcf;

    }


    function getError()
    {
        return($this->error_msg);
    }


    function setBillInfo()
    {
        global $db;

        $this->sql = "SELECT bill_nr, is_final, bill_dte, bill_frmdte ".
                     "FROM seg_billing_encounter WHERE encounter_nr = ".$db->qstr($this->encounter_nr)." ".
                     "AND is_deleted IS NULL";

        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }

    }

    function getBillInfo()
    {
        $bill_info = array();
        $bill_info->bill_dte = $this->bill_dte;
        $bill_info->bill_frmdte = $this->bill_dte;

        return $bill_info;
    }

    
    function getPrevEncounterNr() 
    {
        global $db;

        $strSQL = "SELECT parent_encounter_nr
                   FROM care_encounter
                   WHERE encounter_nr = ".$db->qstr($this->encounter_nr);
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $prev_encounter = $row['parent_encounter_nr'];
                $this->prev_encounter = $prev_encounter;
            }
        }

        return($prev_encounter);
    }

    function getPrevEncounter($enc) 
    {
        global $db;

        $strSQL = "SELECT parent_encounter_nr
                   FROM care_encounter
                   WHERE encounter_nr = ".$db->qstr($enc);
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $prev_encounter = $row['parent_encounter_nr'];
                $this->prev_encounter = $prev_encounter;
            }
        }

        return($prev_encounter);
    }

    function isERPatient($enc) 
    {
        global $db;
        $enc_type = 0;
        $strSQL = "SELECT encounter_type FROM care_encounter WHERE encounter_nr = ".$db->qstr($enc);

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = ($row['encounter_type']);
            }
        }

        return ($enc_type == ER_PATIENT);
    }

    //added by nick 1/6/2014
    function getAccHist($result){
        return($this->accommodation_hist);
    }
    //enc nick
// sample to push branch
    function getAccomodationList()
    {
        global $db;
        
        $filter = array('','');

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);

        if ($prev_encounter != '') $filter[0] = " or cel.encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounter != '') $filter[1] = " or sel.encounter_nr = ".$db->qstr($prev_encounter);

        if($this->greater_accom_effec){
            $end_date = $this->bill_dte;
            $deathcondi = '';
            $deathcondiUnion = '';

            
            if($this->death_date != '0000-00-00 00:00:00' && $this->death_date != NULL){
                $end_date = $this->death_date;

                $deathcondiUnion = "AND (IF(sel.is_per_hour, str_to_date(concat(date_format(occupy_date_from, '%Y-%m-%d'), ' ', date_format(occupy_time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
                " and str_to_date(concat(date_format(occupy_date_from, '%Y-%m-%d'), ' ', date_format(occupy_time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= " . $db->qstr($end_date) . ", str_to_date(date_format(occupy_date_from, '%Y-%m-%d'), '%Y-%m-%d') >= " .$db->qstr(date("Y-m-d",strtotime($this->bill_frmdte))). " " .
                " and str_to_date(date_format(occupy_date_from, '%Y-%m-%d'), '%Y-%m-%d') <= " . $db->qstr(date("Y-m-d",strtotime($end_date))) . "))";
            }

            $deathcondi = "AND (STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
                "and STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= " . $db->qstr($end_date).")";
            
            $this->sql = "select cel.encounter_nr, location_nr, cr.type_nr, location_nr AS room, cw.nr AS ward, cw.accomodation_type,cel.status, cw.nr AS ward_id, concat(ctr.name,' (',cw.name,')') as name, ".
            "      (case when not (isnull(selr.rate) OR selr.rate=0)  then selr.rate else ctr.room_rate end) as rm_rate, 0 as days_stay, IF(ctr.is_per_hour = 1, TIMESTAMPDIFF(HOUR, CONCAT(date_from, ' ', time_from), CONCAT(IF(date_to = '0000-00-00', DATE_FORMAT(NOW(), '%Y-%m-%d'), date_to),' ', IF(time_to IS NULL, DATE_FORMAT(NOW(), '%H:%i:%s'),time_to))), NULL) as hrs_stay, ctr.is_per_hour, NULL AS per_hour, ".
            "      date_from, date_to, time_from, time_to, 'AD' as source, mandatory_excess, cel.create_id,
                cel.create_time as create_dt".
            "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr INNER JOIN care_encounter AS ce ON ce.encounter_nr = cel.encounter_nr) ".
            "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr) ".
            "      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) ".
                    "      on cel.location_nr = cr.room_nr and cel.group_nr = cr.ward_nr ".
            "        LEFT JOIN seg_encounter_location_addtl `sela` ON cel.encounter_nr = sela.encounter_nr " .
            "   where (cel.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") AND cel.is_deleted <> 1 ".
            "      and exists (select nr ".
            "                     from care_type_location as ctl ".
            "                        where upper(type) = 'ROOM' and ctl.nr = cel.type_nr) ".
            "      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
            "         and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= " . $db->qstr($end_date) . ") " .
            #commented by darryl 03/30/2017
            #for backtracking billdate
            "             or ".
            "       (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " . $db->qstr($this->bill_frmdte) . " ".
            "         and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($end_date) . ") ".
            "          or (".
            "        str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ". $deathcondi . ")".
            #end darryl
            " union ".
            "select sel.encounter_nr, cr.room_nr, cr.type_nr, cr.room_nr, sel.group_nr, cw.accomodation_type,sel.occupy_date_from, sel.group_nr as ward_id, concat(ctr.name,' (',cw.name,')') as name, ".
                    "      (case when not (isnull(sel.rate) OR sel.rate=0)  then sel.rate else ctr.room_rate end) as rm_rate, days_stay, hrs_stay, ctr.is_per_hour, sel.is_per_hour AS per_hour, ".
            "      sel.occupy_date_from as date_from, sel.occupy_date_to as date_to, '00:00:00' as time_from, '00:00:00' as time_to, 'BL' as source, mandatory_excess, sel.create_id, sel.create_dt ".
            "   from (seg_encounter_location_addtl as sel inner join care_ward as cw on sel.group_nr = cw.nr) ".
                    "      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) on sel.room_nr = cr.nr and sel.group_nr = cr.ward_nr ".
            "   where (sel.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[1].") AND sel.is_deleted <> 1 ".$deathcondiUnion.
            "   order by STR_TO_DATE(CONCAT(date_from, ' ', time_from),'%Y-%m-%d %H:%i:%s') ASC, STR_TO_DATE(CONCAT(IF(date_to = '0000-00-00',DATE_FORMAT(NOW(), '%Y-%m-%d'), date_to),' ',DATE_FORMAT(IFNULL(time_to, '00:00:00'),'%H:%i:%s')),'%Y-%m-%d %H:%i:%s') ASC";
        }else{
            $this->sql = "select cel.encounter_nr, location_nr, cr.type_nr, ce.current_room_nr AS room, ce.current_ward_nr AS ward, cw.accomodation_type,cel.status, concat(ctr.name,' (',cw.name,')') as name, ".
                "      (case when not (isnull(selr.rate) OR selr.rate=0)  then selr.rate else ctr.room_rate end) as rm_rate, 0 as days_stay, 0 as hrs_stay, ".
                "      date_from, date_to, time_from, time_to, 'AD' as source, mandatory_excess ".
                "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr INNER JOIN care_encounter AS ce ON ce.encounter_nr = cel.encounter_nr) ".
                "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr) ".
                "      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) ".
                        "      on cel.location_nr = cr.room_nr and cel.group_nr = cr.ward_nr ".
                "        LEFT JOIN seg_encounter_location_addtl `sela` ON cel.encounter_nr = sela.encounter_nr " .
                "   where (cel.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") ".
                "      and exists (select nr ".
                "                     from care_type_location as ctl ".
                "                        where upper(type) = 'ROOM' and ctl.nr = cel.type_nr) ".
                "      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
                "         and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->bill_dte) . ") " .
                #commented by darryl 03/30/2017
                #for backtracking billdate
                "             or ".
                "       (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " . $db->qstr($this->bill_frmdte) . " ".
                "         and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->bill_dte) . ") ".
                "          or ".
                "        str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
                #end darryl
                " union ".
                "select sel.encounter_nr, cr.room_nr, cr.type_nr, sel.room_nr, sel.group_nr, cw.accomodation_type,sel.occupy_date_from, concat(ctr.name,' (',cw.name,')') as name, ".
                        "      (case when not (isnull(sel.rate) OR sel.rate=0)  then sel.rate else ctr.room_rate end) as rm_rate, days_stay, hrs_stay, ".
                "      sel.occupy_date_from as date_from, sel.occupy_date_to as date_to, '00:00:00' as time_from, '00:00:00' as time_to, 'BL' as source, mandatory_excess ".
                "   from (seg_encounter_location_addtl as sel inner join care_ward as cw on sel.group_nr = cw.nr) ".
                        "      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) on sel.room_nr = cr.nr and sel.group_nr = cr.ward_nr ".
                "   where (sel.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[1].") ".
                #commented by art 09/24/2014
                #"      and (str_to_date(sel.create_dt, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                #"      and str_to_date(sel.create_dt, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->bill_dte) . ") ".
                #end art
                "   order by source, date_from, time_from";
        }

       #echo $this->sql;
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
            }else{
            return false; 
                }

    }

    
    function getXLOList()
    {
        global $db;

        $filter = array('','');
        $this->services_list = array();

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);

        /*if ($this->old_bill_nr != '' && $this->is_final) {
            $cond_lab = " AND lh.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='LB' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_rad = " AND rh.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='RD' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_ph =  " AND ph.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='SU' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_mph = " AND mph.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='MS' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_misc = " AND m.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='OA' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
        }else{*/
            
        $cond_poc = " AND cbg.reading_dt BETWEEN ".$db->qstr($this->bill_frmdte)." AND DATE_SUB(".$db->qstr($this->charged_date).", INTERVAL 1 second) ";
            $cond_lab = " AND (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
                        " AND str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") " ;
            $cond_rad = " AND (str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
                        " AND str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") " ;
            $cond_ph =  " AND (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                        " AND str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") ";
            $cond_mph = " AND (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                        " AND str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") ".
                        " AND mphd.is_deleted = 0";
           $cond_misc = " AND (str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                        " AND str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") ".
                        " AND md.is_deleted = 0";
        //}

        if ($prev_encounter != '') $filter[0] = " or encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounter != '') $filter[1] = " or sos.encounter_nr = ".$db->qstr($prev_encounter);
        #edited by art 07/28/2014 added encoder and time_encoded
        
        $this->sql = "SELECT DISTINCT (CASE WHEN @N IS NULL THEN @N := 0 ELSE @N := @N +1 END) refno, DATE(cbg.reading_dt) serv_dt, TIME(cbg.reading_dt) serv_tm, o.service_code, 
                        s.name service_desc, s.group_code, sg.name group_desc, 1 qty, ((o.unit_price * o.quantity) - IFNULL(oh.discount, 0))/o.quantity serv_charge, 'POC' source, 
                        IF(cbg.readby_name IS NULL OR cbg.readby_name = '', (SELECT UCASE(a.name) FROM care_users AS a WHERE login_id = oh.`create_id`), UCASE(cbg.readby_name)) encoder, DATE_FORMAT(reading_dt,'%M %d, %Y %r' ) time_encoded 
                        FROM (seg_cbg_reading cbg INNER JOIN seg_hl7_message_log hl7 ON cbg.`log_id` = hl7.`log_id`) 
                        LEFT JOIN ((seg_poc_order_detail o INNER JOIN seg_poc_order oh ON o.refno = oh.refno) INNER JOIN seg_lab_services s ON o.`service_code` = s.`service_code` 
                        INNER JOIN seg_lab_service_groups sg ON s.`group_code` = sg.`group_code`) ON o.`refno` = hl7.`ref_no`
                        WHERE (cbg.`encounter_nr` = ".$db->qstr($this->encounter_nr)." or cbg.encounter_nr = ".$db->qstr($prev_encounter).") AND (oh.is_cash = 0 OR oh.is_cash IS NULL) "
                        .$cond_poc."
                    UNION ALL ";
        $this->sql .= "select lh.refno, serv_dt, serv_tm, ld.service_code, ls.name as service_desc, ls.group_code, " .
                    "   lsg.name as group_desc, ld.quantity as qty, ld.price_charge as serv_charge, 'LB' as source, " .
                    "IFNULL((SELECT a.name FROM care_users AS a WHERE login_id = lh.`create_id`),lh.`create_id`) AS encoder,".
                    "DATE_FORMAT(lh.`create_dt`,'%M %d %Y %r' ) AS time_encoded".
                    "   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
                    "          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
                    "          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
                    "      WHERE (CASE WHEN serv_dt >= DATE('".ISSRVD_EFFECTIVITY."') THEN ld.is_served ELSE 1 END) AND ".
                    "         UPPER(TRIM(ld.STATUS)) <> 'DELETED' AND lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
                    "         and (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and upper(trim(lh.status)) <> 'DELETED' " .
                    $cond_lab.
                    "   group by lh.refno, serv_dt, serv_tm, ld.service_code, ls.name, ls.group_code, lsg.name ".
                    " UNION ALL ".

                    "select rh.refno, rh.request_date as serv_dt, rh.request_time as serv_tm, rd.service_code, rs.name as service_desc, rs.group_code, " .
                    "   rsg.name as group_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, 'RD' as source, " .
                    "(SELECT a.name FROM care_users AS a WHERE login_id = rh.create_id) AS encoder,".
                    "DATE_FORMAT(rh.`create_dt`,'%M %d %Y %r' ) AS time_encoded".
                    "   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
                    "          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
                    "          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
                    "      WHERE (CASE WHEN rh.request_date >= DATE('".ISSRVD_EFFECTIVITY."') THEN rd.is_served ELSE 1 END) AND ".
                    "      UPPER(TRIM(rd.STATUS)) <> 'DELETED' AND rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
                    "         and (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and upper(trim(rh.status)) <> 'DELETED' and upper(trim(rd.status)) <> 'DELETED' " .
                    $cond_rad.
                            "   group by rh.refno, rh.request_date, rh.request_time, rd.service_code, rs.name, rs.group_code, rsg.name ".
                    " UNION ALL ".

                    "select ph.refno, date(ph.orderdate) as serv_dt, time(ph.orderdate) as serv_tm, pd.bestellnum as service_code, artikelname as service_desc, 'SU' as group_code, ".
                            "      'Supplies' as group_desc, pd.quantity - ifnull(spri.quantity, 0) as qty, pricecharge as serv_charge, 'SU' as source, ".
                    "(SELECT a.name FROM care_users AS a WHERE login_id = ph.`create_id`) AS encoder,".
                    "DATE_FORMAT(ph.`create_time`,'%M %d %Y %r' ) AS time_encoded".
                    "   from ((seg_pharma_orders as ph inner join
                                                 seg_pharma_order_items pd on ph.refno = pd.refno and pd.serve_status <> 'N' and pd.request_flag is null) ".
                    "      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
                    "      left join
                            (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
                                                                    FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
                                                                         ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].")
                                                                    WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") AND rd.ref_no = oh.refno)
                                        GROUP BY rd.ref_no, rd.bestellnum) as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum 
                                        LEFT JOIN seg_type_charge_pharma stcp ON ph.`charge_type`=stcp.`id` ".
                    "   where (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and is_cash = 0 AND stcp.is_excludedfrombilling='0' ".
                    $cond_ph.
                            "      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".

                    " UNION ALL ".

                    "select mph.refno, date(mph.chrge_dte) as serv_dt, time(mph.chrge_dte) as serv_tm, mphd.bestellnum as service_code, artikelname as service_desc, 'MS' as group_code, ".
                            "      'Supplies' as group_desc, sum(quantity) as qty, unit_price as serv_charge, 'MS' as source, ".
                    "mph.create_id AS encoder,".
                    "DATE_FORMAT(mph.`create_dt`,'%M %d %Y %r' ) AS time_encoded".
                    "   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
                    "      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S' ".
                    "   where (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") ".
                    $cond_mph.
                            "   group by mph.refno, mph.chrge_dte, mphd.bestellnum, artikelname ".
                    " UNION ALL ".

                    "select sos.refno, date(eqh.order_date) as serv_dt, time(eqh.order_date) as serv_tm, eqd.equipment_id, artikelname, '' as group_code,
                         'Equipment' as group_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as uprice, 'OE' as source,
                          eqh.created_id AS encoder,
                         DATE_FORMAT(eqh.created_date,'%M %d %Y %r' ) AS time_encoded
                         from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
                         left join seg_ops_serv as sos on sos.refno = eqh.request_refno) inner join care_pharma_products_main as
                         cppm on cppm.bestellnum = eqd.equipment_id
                         where (sos.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[1].")
                            and (str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). "
                            and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ")
                                 group by sos.refno, eqh.order_date, eqd.equipment_id, artikelname ".
                    " UNION ALL ".

                    "select m.refno, date(m.chrge_dte) as serv_dt, time(m.chrge_dte) as serv_tm, md.service_code, ms.name as service_desc, '' as group_code, ".
                    "      'Others' as group_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as serv_charge, 'OA' as source, ".
                    " m.create_id AS encoder,".
                    "DATE_FORMAT(m.`create_dt`,'%M %d %Y %r' ) AS time_encoded".
                    "   from (seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
                    "      inner join seg_other_services as ms on md.service_code = ms.alt_service_code ".
                    "   where (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and md.request_flag is null AND m.is_cash = 0".
                    $cond_misc.
                    "   group by m.refno, m.chrge_dte, md.service_code, ms.name";

        #echo $this->sql;
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
         
    }

    function getXLOList_v2()
    {
        global $db;

        $filter = array('','');
        $this->services_list = array();

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);

        /*if ($this->old_bill_nr != '' && $this->is_final) {
            $cond_lab = " AND lh.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='LB' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_rad = " AND rh.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='RD' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_ph =  " AND ph.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='SU' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_mph = " AND mph.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='MS' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
            $cond_misc = " AND m.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='OA' AND bill_nr = ".$db->qstr($this->old_bill_nr).")";
        }else{*/
            $cond_poc = " AND cbg.reading_dt BETWEEN ".$db->qstr($this->bill_frmdte)." AND DATE_SUB(".$db->qstr($this->charged_date).", INTERVAL 1 second) ";
            $cond_lab = " AND (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
                        " AND str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") " ;
            $cond_rad = " AND (str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
                        " AND str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") " ;
            $cond_ph =  " AND (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                        " AND str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") ";
            $cond_mph = " AND (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                        " AND str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") ".
                        " AND mphd.is_deleted = 0";
           $cond_misc = " AND (str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                        " AND str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") ".
                        " AND md.is_deleted = 0";
        //}

        if ($prev_encounter != '') $filter[0] = " or encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounter != '') $filter[1] = " or sos.encounter_nr = ".$db->qstr($prev_encounter);
        #edited by art 07/28/2014 added encoder and time_encoded
        $this->sql = "SELECT DISTINCT (CASE WHEN @N IS NULL THEN @N := 0 ELSE @N := @N +1 END) refno, DATE(cbg.reading_dt) serv_dt, TIME(cbg.reading_dt) serv_tm, o.service_code, 
                        s.name service_desc, s.group_code, sg.name group_desc, 1 qty, ((o.unit_price * o.quantity) - IFNULL(oh.discount, 0))/o.quantity serv_charge, 'POC' source,                         
                        IF(cbg.readby_name IS NULL OR cbg.readby_name = '', (SELECT UCASE(a.name) FROM care_users AS a WHERE login_id = oh.`create_id`), UCASE(cbg.readby_name)) encoder, DATE_FORMAT(reading_dt,'%M %d, %Y %r' ) time_encoded 
                        FROM (seg_cbg_reading cbg INNER JOIN seg_hl7_message_log hl7 ON cbg.`log_id` = hl7.`log_id`) 
                        LEFT JOIN ((seg_poc_order_detail o INNER JOIN seg_poc_order oh ON o.refno = oh.refno) INNER JOIN seg_lab_services s ON o.`service_code` = s.`service_code` 
                        INNER JOIN seg_lab_service_groups sg ON s.`group_code` = sg.`group_code`) ON o.`refno` = hl7.`ref_no`
                        WHERE (cbg.`encounter_nr` = ".$db->qstr($this->encounter_nr)." or cbg.encounter_nr = ".$db->qstr($prev_encounter).") AND (oh.is_cash = 0 OR oh.is_cash IS NULL) ".
                        $cond_poc.
                     "UNION ALL ";
        $this->sql .= "select lh.refno, serv_dt, serv_tm, ld.service_code, ls.name as service_desc, ls.group_code, " .
                    "   lsg.name as group_desc, ld.quantity as qty, ld.price_charge as serv_charge, 'LB' as source, " .
                    "IFNULL((SELECT a.name FROM care_users AS a WHERE login_id = lh.`create_id`),lh.`create_id`) AS encoder,".
                    "DATE_FORMAT(lh.`create_dt`,'%M %d %Y %r' ) AS time_encoded".
                    "   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
                    "          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
                    "          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
                    "      WHERE (CASE WHEN serv_dt >= DATE('".ISSRVD_EFFECTIVITY."') THEN ld.is_served ELSE 1 END) AND ls.`name` NOT LIKE '%VENTILATOR%' AND ".
                    "         UPPER(TRIM(ld.STATUS)) <> 'DELETED' AND lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
                    "         and (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and upper(trim(lh.status)) <> 'DELETED' " .
                    $cond_lab.
                    "   group by lh.refno, serv_dt, serv_tm, ld.service_code, ls.name, ls.group_code, lsg.name ".
                    " UNION ALL ".

                    "select rh.refno, rh.request_date as serv_dt, rh.request_time as serv_tm, rd.service_code, rs.name as service_desc, rs.group_code, " .
                    "   rsg.name as group_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, 'RD' as source, " .
                    "(SELECT a.name FROM care_users AS a WHERE login_id = rh.create_id) AS encoder,".
                    "DATE_FORMAT(rh.`create_dt`,'%M %d %Y %r' ) AS time_encoded".
                    "   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
                    "          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
                    "          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
                    "      WHERE (CASE WHEN rh.request_date >= DATE('".ISSRVD_EFFECTIVITY."') THEN rd.is_served ELSE 1 END) AND ".
                    "      UPPER(TRIM(rd.STATUS)) <> 'DELETED' AND rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
                    "         and (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and upper(trim(rh.status)) <> 'DELETED' and upper(trim(rd.status)) <> 'DELETED' " .
                    $cond_rad.
                            "   group by rh.refno, rh.request_date, rh.request_time, rd.service_code, rs.name, rs.group_code, rsg.name ".
                    " UNION ALL ".

                    "select ph.refno, date(ph.orderdate) as serv_dt, time(ph.orderdate) as serv_tm, pd.bestellnum as service_code, artikelname as service_desc, 'SU' as group_code, ".
                            "      'Supplies' as group_desc, pd.quantity - ifnull(spri.quantity, 0) as qty, pricecharge as serv_charge, 'SU' as source, ".
                    "(SELECT a.name FROM care_users AS a WHERE login_id = ph.`create_id`) AS encoder,".
                    "DATE_FORMAT(ph.`create_time`,'%M %d %Y %r' ) AS time_encoded".
                    "   from ((seg_pharma_orders as ph inner join
                                                 seg_pharma_order_items pd on ph.refno = pd.refno and pd.serve_status <> 'N' and pd.request_flag is null) ".
                    "      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
                    "      left join
                            (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
                                                                    FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
                                                                         ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].")
                    
                                                                    WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") AND rd.ref_no = oh.refno)
                                        GROUP BY rd.ref_no, rd.bestellnum) as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum 
                                        LEFT JOIN seg_type_charge_pharma stcp ON ph.`charge_type`=stcp.`id`".
                    "   where pd.is_fs='0' AND (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and is_cash = 0  AND stcp.is_excludedfrombilling='0' ".
                    $cond_ph.
                            " and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".

                    " UNION ALL ".

                    "select mph.refno, date(mph.chrge_dte) as serv_dt, time(mph.chrge_dte) as serv_tm, mphd.bestellnum as service_code, artikelname as service_desc, 'MS' as group_code, ".
                            "      'Supplies' as group_desc, sum(quantity) as qty, unit_price as serv_charge, 'MS' as source, ".
                    "mph.create_id AS encoder,".
                    "DATE_FORMAT(mph.`create_dt`,'%M %d %Y %r' ) AS time_encoded".
                    "   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
                    "      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S' ".
                    "   where mphd.is_fs='0' AND (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") ".
                    $cond_mph.
                            "   group by mph.refno, mph.chrge_dte, mphd.bestellnum, artikelname ".
                    //CONSIGNED START
                    " UNION ALL ".

                    "select ph.refno, date(ph.orderdate) as serv_dt, time(ph.orderdate) as serv_tm, pd.bestellnum as service_code, artikelname as service_desc, 'SU_CON' as group_code, ".
                            "      'Supplies_Consigned' as group_desc, pd.quantity - ifnull(spri.quantity, 0) as qty, pricecharge as serv_charge, 'SU_CON' as source, ".
                    "(SELECT a.name FROM care_users AS a WHERE login_id = ph.`create_id`) AS encoder,".
                    "DATE_FORMAT(ph.`create_time`,'%M %d %Y %r' ) AS time_encoded".
                    "   from ((seg_pharma_orders as ph inner join
                                                 seg_pharma_order_items pd on ph.refno = pd.refno and pd.serve_status <> 'N' and pd.request_flag is null) ".
                    "      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
                    "      left join
                            (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
                                                                    FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
                                                                         ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].")
                                                                    WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") AND rd.ref_no = oh.refno)
                                        GROUP BY rd.ref_no, rd.bestellnum) as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum 
                                        LEFT JOIN seg_type_charge_pharma stcp ON ph.`charge_type`=stcp.`id` ".
                    "   where pd.is_fs='1' AND (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and is_cash = 0 AND stcp.is_excludedfrombilling='0' ".
                    $cond_ph.
                            "      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".

                    " UNION ALL ".

                    "select mph.refno, date(mph.chrge_dte) as serv_dt, time(mph.chrge_dte) as serv_tm, mphd.bestellnum as service_code, artikelname as service_desc, 'MS_CON' as group_code, ".
                            "      'Supplies_Consigned' as group_desc, sum(quantity) as qty, unit_price as serv_charge, 'MS_CON' as source, ".
                    "mph.create_id AS encoder,".
                    "DATE_FORMAT(mph.`create_dt`,'%M %d %Y %r' ) AS time_encoded".
                    "   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
                    "      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S' ".
                    "   where mphd.is_fs='1' AND (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") ".
                    $cond_mph.
                            "   group by mph.refno, mph.chrge_dte, mphd.bestellnum, artikelname ".
                    //CONSIGNED END
                    " UNION ALL ".

                    "select sos.refno, date(eqh.order_date) as serv_dt, time(eqh.order_date) as serv_tm, eqd.equipment_id, artikelname, '' as group_code,
                         'Equipment' as group_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as uprice, 'OE' as source,
                          eqh.created_id AS encoder,
                         DATE_FORMAT(eqh.created_date,'%M %d %Y %r' ) AS time_encoded
                         from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
                         left join seg_ops_serv as sos on sos.refno = eqh.request_refno) inner join care_pharma_products_main as
                         cppm on cppm.bestellnum = eqd.equipment_id
                         where (sos.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[1].")
                            and (str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). "
                            and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ")
                                 group by sos.refno, eqh.order_date, eqd.equipment_id, artikelname ".
                    " UNION ALL ".

                    "select m.refno, date(m.chrge_dte) as serv_dt, time(m.chrge_dte) as serv_tm, md.service_code, ms.name as service_desc, '' as group_code, ".
                    "      'Others' as group_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as serv_charge, 'OA' as source, ".
                    " m.create_id AS encoder,".
                    "DATE_FORMAT(m.`create_dt`,'%M %d %Y %r' ) AS time_encoded".
                    "   from (seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
                    "      inner join seg_other_services as ms on md.service_code = ms.alt_service_code ".
                    "   where (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter[0].") and md.request_flag is null AND m.is_cash = 0".
                    $cond_misc.
                    "   group by m.refno, m.chrge_dte, md.service_code, ms.name";

        #echo $this->sql;
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
         
    }

    function getVentilatorList()
    {
        global $db;

        $filter = array('','');
        $this->services_list = array();

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);
        if ($prev_encounter != '') $filter[0] = " or encounter_nr = ".$db->qstr($prev_encounter);
         $cond_lab = " AND (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " " .
                        " AND str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < " . $db->qstr($this->charged_date) . ") " ;

          $this->sql = "select lh.refno,serv_dt, serv_tm, ld.service_code, ls.name as service_desc, ls.group_code, " .
                    "   lsg.name as group_desc, ld.quantity as quant, ld.price_charge as serv_price_charge, 'LB' as source, " .
                    "IFNULL((SELECT a.name FROM care_users AS a WHERE login_id = lh.`create_id`),lh.`create_id`) AS encoder,".
                    "DATE_FORMAT(lh.`create_dt`,'%M %d %Y %r' ) AS time_encoded".
                    "   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
                    "          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
                    "          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
                    "      WHERE (CASE WHEN serv_dt >= DATE('".ISSRVD_EFFECTIVITY."') THEN ld.is_served ELSE 1 END) AND ".
                    "         UPPER(TRIM(ld.STATUS)) <> 'DELETED' AND lh.status NOT IN('deleted','void') AND ls.`name`  LIKE '%VENTILATOR%'
                    AND lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
                    "         and (encounter_nr = ".$db->qstr($this->encounter_nr).$filter[0].") and upper(trim(lh.status)) <> 'DELETED' " .
                    $cond_lab.
                "   group by lh.refno, serv_dt, serv_tm, ld.service_code, ls.name, ls.group_code, lsg.name ";
              #  echo $this->sql;
                        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }
    
    #edited by art 07/30/2014
    #added encoder and time encoded
    function getMedsList()
    {
        global $db;

        $toDate = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->charged_date)));

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);

        /*if ($this->old_bill_nr != '' && $this->is_final) {
            $cond_pharma = " AND pd.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='PH' AND bill_nr = ".$db->qstr($this->old_bill_nr).")\n";
            $cond_order = " AND mpd.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='OR' AND bill_nr = ".$db->qstr($this->old_bill_nr).")\n";
        }else{*/
            $cond_pharma = " AND (ph.orderdate BETWEEN CAST(" .$db->qstr($this->bill_frmdte). " AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME))\n";
            $cond_order =  "AND (mph.chrge_dte BETWEEN CAST(" .$db->qstr($this->bill_frmdte). " AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME))\n";
        //}

        if ($prev_encounter != '') $filter1 = " OR ph.encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounter != '') $filter2 = " OR mph.encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounterr != '') $filter3 = " OR encounter_nr = ".$db->qstr($prev_encounter);
        
        $this->sql = /*"SELECT refno, bestellnum, unused_flag, artikelname, MAX(flag) AS flag, SUM(qty) AS qty,\n".
                "(SUM(price * qty)/SUM(qty)) AS price,\n".
                "SUM(itemcharge) AS itemcharge, source\n".
            "FROM (\n".*/

            "SELECT pd.refno, 0 AS flag, 'Pharma' AS source, pd.bestellnum,\n".
                "pd.is_unused as unused_flag, pd.unused_qty,\n".
                "(CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname,\n".
                "SUM(pd.quantity - IFNULL(spri.quantity, 0)) AS qty,\n".
                "(SUM(pricecharge * (pd.quantity - IFNULL(spri.quantity, 0)))/SUM(pd.quantity - IFNULL(spri.quantity, 0))) AS price,\n".
                "SUM((pd.quantity - IFNULL(spri.quantity, 0)) * pricecharge) AS itemcharge,\n".
                "(SELECT a.name FROM care_users AS a WHERE login_id = ph.`create_id`) AS encoder,\n".
                "DATE_FORMAT(ph.`create_time`,'%M %d %Y %r' ) AS time_encoded \n".
            "FROM seg_pharma_order_items AS pd\n".
                "INNER JOIN seg_pharma_orders AS ph ON ph.refno = pd.refno\n".
                "LEFT JOIN seg_type_charge_pharma AS stc ON ph.`charge_type`=stc.`id`\n".
                "INNER JOIN care_pharma_products_main AS p ON pd.bestellnum = p.bestellnum \n".
                "LEFT JOIN (SELECT rd.ref_no, 'Return' AS source, rd.bestellnum, SUM(quantity) AS quantity, rh.`pharma_area`
            FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
                 ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter3.")
            WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter3.") AND rd.ref_no = oh.refno)
            GROUP BY rd.ref_no, rd.bestellnum) AS spri ON pd.refno = spri.ref_no AND pd.bestellnum = spri.bestellnum\n".
            "AND CASE WHEN spri.`pharma_area` = '' THEN 1 ELSE (CASE WHEN pd.`pharma_area` = '' THEN ph.`pharma_area` = spri.`pharma_area` ELSE pd.`pharma_area` = spri.`pharma_area` END) END\n".
            "WHERE\n".
                "pd.serve_status <> 'N' AND ph.`charge_type` NOT IN ('EP') AND pd.request_flag IS NULL AND !ph.is_cash AND p.prod_class = 'M'\n".
                "AND (ph.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter1.")\n".
                $cond_pharma.
                "AND (pd.quantity - IFNULL(spri.quantity, 0)) > 0  AND stc.`is_excludedfrombilling`=0 \n".
            "GROUP BY pd.refno , pd.bestellnum\n".

            "UNION ALL\n".

            "SELECT mpd.refno, 1 AS flag, 'Order' AS source, mpd.bestellnum,\n".
                " 0 as unused_flag, 0 as unused_qty,\n".
                "(CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname,\n".
                "SUM(quantity) AS qty,\n".
                "(SUM(unit_price * quantity)/SUM(quantity)) AS price,\n".
                "SUM(quantity * unit_price) AS itemcharge,\n".
                "IFNULL(mpd.`create_id`, mph.`create_id`) AS encoder,\n".
                "IFNULL(DATE_FORMAT(mpd.`create_dt`,'%M %d %Y %r' ), DATE_FORMAT(mph.`create_dt`,'%M %d %Y %r' )) AS time_encoded\n".
            "FROM seg_more_phorder AS mph\n".
                "INNER JOIN seg_more_phorder_details AS mpd ON mph.refno = mpd.refno\n".
                "INNER JOIN care_pharma_products_main AS p ON mpd.bestellnum = p.bestellnum AND p.prod_class = 'M'\n".
            "WHERE\n".
                "(mph.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter2.")\n".
                 $cond_order.
                 " AND mpd.is_deleted != '1'".
            "GROUP BY mpd.bestellnum\n";
            /*") AS t\n".
            "GROUP BY bestellnum, artikelname ORDER BY artikelname\n";*/

        
       /* if ($_SESSION['sess_temp_userid']=='medocs')
            echo $this->sql;*/
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }

    function getMedsList_v2()
    {
        global $db;

        $toDate = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->charged_date)));

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);

        /*if ($this->old_bill_nr != '' && $this->is_final) {
            $cond_pharma = " AND pd.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='PH' AND bill_nr = ".$db->qstr($this->old_bill_nr).")\n";
            $cond_order = " AND mpd.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='OR' AND bill_nr = ".$db->qstr($this->old_bill_nr).")\n";
        }else{*/
            $cond_pharma = " AND (ph.orderdate BETWEEN CAST(" .$db->qstr($this->bill_frmdte). " AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME))\n";
            $cond_order =  "AND (mph.chrge_dte BETWEEN CAST(" .$db->qstr($this->bill_frmdte). " AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME))\n";
        //}

        if ($prev_encounter != '') $filter1 = " OR ph.encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounter != '') $filter2 = " OR mph.encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounterr != '') $filter3 = " OR encounter_nr = ".$db->qstr($prev_encounter);
        
        $this->sql = /*"SELECT refno, bestellnum, unused_flag, artikelname, MAX(flag) AS flag, SUM(qty) AS qty,\n".
                "(SUM(price * qty)/SUM(qty)) AS price,\n".
                "SUM(itemcharge) AS itemcharge, source\n".
            "FROM (\n".*/

            "SELECT pd.refno, 0 AS flag, 'Pharma' AS source, pd.bestellnum,\n".
                "pd.is_unused as unused_flag, pd.unused_qty,\n".
                "(CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname,\n".
                "SUM(pd.quantity - IFNULL(spri.quantity, 0)) AS qty,\n".
                "(SUM(pricecharge * (pd.quantity - IFNULL(spri.quantity, 0)))/SUM(pd.quantity - IFNULL(spri.quantity, 0))) AS price,\n".
                "SUM((pd.quantity - IFNULL(spri.quantity, 0)) * pricecharge) AS itemcharge,\n".
                "(SELECT a.name FROM care_users AS a WHERE login_id = ph.`create_id`) AS encoder,\n".
                "DATE_FORMAT(ph.`create_time`,'%M %d %Y %r' ) AS time_encoded \n".
            "FROM seg_pharma_order_items AS pd\n".
                "INNER JOIN seg_pharma_orders AS ph ON ph.refno = pd.refno\n".
                "LEFT JOIN seg_type_charge_pharma AS stc ON ph.`charge_type`=stc.`id`\n".
                "INNER JOIN care_pharma_products_main AS p ON pd.bestellnum = p.bestellnum \n".
                "LEFT JOIN (SELECT rd.ref_no, 'Return' AS source, rd.bestellnum, SUM(quantity) AS quantity
            FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
                 ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter3.")
            WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE ((encounter_nr = ".$db->qstr($this->encounter_nr)." OR encounter_nr = (SELECT parent_encounter_nr FROM care_encounter WHERE encounter_nr =  ".$db->qstr($this->encounter_nr)." )) ".$filter3.") AND rd.ref_no = oh.refno)
            GROUP BY rd.ref_no, rd.bestellnum) AS spri ON pd.refno = spri.ref_no AND pd.bestellnum = spri.bestellnum\n".
            "WHERE\n".
                "pd.is_fs='0' AND pd.serve_status <> 'N' AND ph.`charge_type` NOT IN ('EP') AND pd.request_flag IS NULL AND !ph.is_cash AND p.prod_class = 'M'\n".
                "AND (ph.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter1.")\n".
                $cond_pharma.
                "AND (pd.quantity - IFNULL(spri.quantity, 0)) > 0  AND stc.`is_excludedfrombilling`=0 \n".
            "GROUP BY pd.refno , pd.bestellnum\n".

            "UNION ALL\n".

            "SELECT mpd.refno, 1 AS flag, 'Order' AS source, mpd.bestellnum,\n".
                " 0 as unused_flag, 0 as unused_qty,\n".
                "(CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname,\n".
                "SUM(quantity) AS qty,\n".
                "(SUM(unit_price * quantity)/SUM(quantity)) AS price,\n".
                "SUM(quantity * unit_price) AS itemcharge,\n".
                "IFNULL(mpd.`create_id`, mph.`create_id`) AS encoder,\n".
                "IFNULL(DATE_FORMAT(mpd.`create_dt`,'%M %d %Y %r' ), DATE_FORMAT(mph.`create_dt`,'%M %d %Y %r' )) AS time_encoded\n".
            "FROM seg_more_phorder AS mph\n".
                "INNER JOIN seg_more_phorder_details AS mpd ON mph.refno = mpd.refno\n".
                "INNER JOIN care_pharma_products_main AS p ON mpd.bestellnum = p.bestellnum AND p.prod_class = 'M'\n".
            "WHERE\n".
                "mpd.is_fs='0' AND (mph.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter2.")\n".
                 $cond_order.
                 " AND mpd.is_deleted != '1'".
            "GROUP BY mpd.bestellnum\n";
            /*") AS t\n".
            "GROUP BY bestellnum, artikelname ORDER BY artikelname\n";*/

        
       /* if ($_SESSION['sess_temp_userid']=='medocs')
            echo $this->sql;*/
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }

    function getMedsList_Consigned()
    {
        global $db;

        $toDate = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->charged_date)));

        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);

        /*if ($this->old_bill_nr != '' && $this->is_final) {
            $cond_pharma = " AND pd.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='PH' AND bill_nr = ".$db->qstr($this->old_bill_nr).")\n";
            $cond_order = " AND mpd.refno IN (SELECT refno from seg_billing_encounter_details WHERE ref_area='OR' AND bill_nr = ".$db->qstr($this->old_bill_nr).")\n";
        }else{*/
            $cond_pharma = " AND (ph.orderdate BETWEEN CAST(" .$db->qstr($this->bill_frmdte). " AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME))\n";
            $cond_order =  "AND (mph.chrge_dte BETWEEN CAST(" .$db->qstr($this->bill_frmdte). " AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME))\n";
        //}

        if ($prev_encounter != '') $filter1 = " OR ph.encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounter != '') $filter2 = " OR mph.encounter_nr = ".$db->qstr($prev_encounter);
        if ($prev_encounterr != '') $filter3 = " OR encounter_nr = ".$db->qstr($prev_encounter);
        
        $this->sql = /*"SELECT refno, bestellnum, unused_flag, artikelname, MAX(flag) AS flag, SUM(qty) AS qty,\n".
                "(SUM(price * qty)/SUM(qty)) AS price,\n".
                "SUM(itemcharge) AS itemcharge, source\n".
            "FROM (\n".*/

            "SELECT pd.refno, 0 AS flag, 'Pharma' AS source, pd.bestellnum,\n".
                "pd.is_unused as unused_flag, pd.unused_qty,\n".
                "(CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname,\n".
                "SUM(pd.quantity - IFNULL(spri.quantity, 0)) AS qty,\n".
                "(SUM(pricecharge * (pd.quantity - IFNULL(spri.quantity, 0)))/SUM(pd.quantity - IFNULL(spri.quantity, 0))) AS price,\n".
                "SUM((pd.quantity - IFNULL(spri.quantity, 0)) * pricecharge) AS itemcharge,\n".
                "(SELECT a.name FROM care_users AS a WHERE login_id = ph.`create_id`) AS encoder,\n".
                "DATE_FORMAT(ph.`create_time`,'%M %d %Y %r' ) AS time_encoded \n".
            "FROM seg_pharma_order_items AS pd\n".
                "INNER JOIN seg_pharma_orders AS ph ON ph.refno = pd.refno\n".
                "LEFT JOIN seg_type_charge_pharma AS stc ON ph.`charge_type`=stc.`id`\n".
                "INNER JOIN care_pharma_products_main AS p ON pd.bestellnum = p.bestellnum \n".
                "LEFT JOIN (SELECT rd.ref_no, 'Return' AS source, rd.bestellnum, SUM(quantity) AS quantity
            FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
                 ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter3.")
            WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = ".$db->qstr($this->encounter_nr)." OR encounter_nr = (SELECT parent_encounter_nr FROM care_encounter WHERE encounter_nr = ".$db->qstr($this->encounter_nr)." ) ".$filter3.") AND rd.ref_no = oh.refno)
            GROUP BY rd.ref_no, rd.bestellnum) AS spri ON pd.refno = spri.ref_no AND pd.bestellnum = spri.bestellnum\n".
            "WHERE\n".
                "pd.is_fs='1' AND pd.serve_status <> 'N' AND ph.`charge_type` NOT IN ('EP') AND pd.request_flag IS NULL AND !ph.is_cash AND p.prod_class = 'M'\n".
                "AND (ph.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter1.")\n".
                $cond_pharma.
                "AND (pd.quantity - IFNULL(spri.quantity, 0)) > 0  AND stc.`is_excludedfrombilling`=0 \n".
            "GROUP BY pd.refno , pd.bestellnum\n".

            "UNION ALL\n".

            "SELECT mpd.refno, 1 AS flag, 'Order' AS source, mpd.bestellnum,\n".
                " 0 as unused_flag, 0 as unused_qty,\n".
                "(CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname,\n".
                "SUM(quantity) AS qty,\n".
                "(SUM(unit_price * quantity)/SUM(quantity)) AS price,\n".
                "SUM(quantity * unit_price) AS itemcharge,\n".
                "IFNULL(mpd.`create_id`, mph.`create_id`) AS encoder,\n".
                "IFNULL(DATE_FORMAT(mpd.`create_dt`,'%M %d %Y %r' ), DATE_FORMAT(mph.`create_dt`,'%M %d %Y %r' )) AS time_encoded\n".
            "FROM seg_more_phorder AS mph\n".
                "INNER JOIN seg_more_phorder_details AS mpd ON mph.refno = mpd.refno\n".
                "INNER JOIN care_pharma_products_main AS p ON mpd.bestellnum = p.bestellnum AND p.prod_class = 'M'\n".
            "WHERE\n".
                "mpd.is_fs='1' AND (mph.encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter2.")\n".
                 $cond_order.
                 " AND mpd.is_deleted != '1'".
            "GROUP BY mpd.bestellnum\n";
            /*") AS t\n".
            "GROUP BY bestellnum, artikelname ORDER BY artikelname\n";*/

        
       /* if ($_SESSION['sess_temp_userid']=='medocs')
            echo $this->sql;*/
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }

    function getProfFeesList() {
        global $db;

        $tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->charged_date)));
        $filter = array('','','','');

        if ($this->prev_encounter_nr != '') $filter[0] = " or dm1.encounter_nr = '$this->prev_encounter_nr'";
        if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";

        if ($this->prev_encounter_nr != '') $filter[2] = " or encounter_nr = '$this->prev_encounter_nr'";
        $prev_encounter = $this->getPrevEncounterNr($this->current_enr);
        if ($prev_encounter != '') $filter[3] = " or spd.encounter_nr = '$prev_encounter' ";
        // die("xx");
       
        $issurgical  = $this->isSurgicalCase();
        //added by jasper 09/03/2013 FOR BUG#305
        if ($this->isWellBaby()) {
            $amountlimit = DEFAULT_NBPKG_RATE;
        } else {
            $amountlimit = $this->pkgamountlimit;
        }
        //added by jasper 09/03/2013 FOR BUG#305

        $hc_pf = $this->getHouseCasePCF();
        $strSQL = "select (SELECT
                              accommodation_type
                            FROM
                              seg_doctor_accommodation_type
                            WHERE encounter_nr = '$this->current_enr'
                              AND entry_no = 1
                              AND dr_nr = t.attending_dr_nr
                              AND dr_role_type_nr = 0) AS accommodation_type,attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Doctor' as role, ".
                    "   sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as num_days, daily_rate, ".
                    "   '' as opcodes, daily_rate * sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as dr_charge, ".
                    "   0 as is_excluded, role_nr, role_area, 0 as role_type_level, 0 as rvu, 0 as multiplier,'' as caserate  ,'0' AS from_ob, '0' as entry_no".
                    "   from ".
                    "      (select distinct attending_dr_nr, name_last, name_first, name_middle, attend_start, ".
                    "          subdate((select attend_start ".
                    "                      from seg_encounter_dr_mgt as dm2 ".
                    "                      where dm2.encounter_nr = dm1.encounter_nr and ".
                    "                            dm2.att_hist_no > dm1.att_hist_no ".
                    "                      order by dm2.att_hist_no asc limit 1), 1) as attend_end, fn_getdailyrate('{$this->current_enr}', date('{$this->charged_date}'), tier_nr, {$this->confinetype_id}, attending_dr_nr) as daily_rate, cpa.role_nr, fn_getDailyVisitRoleArea(tier_nr) as role_area, discharge_date,'' as caserate   ,'0' AS from_ob".
                    "          from (seg_encounter_dr_mgt as dm1 inner join (((care_personell as cpn ".
                    "             inner join care_person as cp on cpn.pid = cp.pid) inner join care_personell_assignment as cpa ".
                    "             on cpn.nr = cpa.personell_nr) inner join care_role_person as crp on ".
                    "             cpa.role_nr = crp.nr) on dm1.attending_dr_nr = cpn.nr) inner join care_encounter as ce ".
                    "             on dm1.encounter_nr = ce.encounter_nr ".
                    "          where (dm1.encounter_nr = '" . $this->current_enr. "'".$filter[0].") " .
                    "             and (str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
                    "                and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "') " .
                    "               and dm1.from_dialysis = 0".
                    "          order by att_hist_no) as t ".
                    "   group by attending_dr_nr, role_area, role_nr ".
                    " union all ".
                    "select  (SELECT
                                        accommodation_type
                                      FROM
                                        seg_doctor_accommodation_type
                                      WHERE encounter_nr = spd.encounter_nr
                                        AND entry_no = spd.entry_no
                                        AND dr_nr = spd.dr_nr
                                        AND dr_role_type_nr = spd.dr_role_type_nr) AS accommodation_type, spd.dr_nr, name_last, name_first, name_middle, concat(name, ' - private') as role, spd.days_attended as num_days, 0 as daily_rate, ".
                    "      GROUP_CONCAT(DISTINCT CONCAT(socd.ops_code, '-', IFNULL(socd.rvu,0), '(', socd.ops_entryno, ')') SEPARATOR ';') AS opcodes,".
                    " dr_charge, is_excluded, ".
                    "      spd.dr_role_type_nr, role_area, IFNULL(role_type_level, IFNULL(dr_level, tier_nr)) as role_type_level, SUM(ifnull(socd.rvu,0)) as tot_rvu, SUM(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$this->charged_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * ifnull(socd.rvu,0))/SUM(ifnull(socd.rvu,0)) as avg_multiplier ,spd.caserate as caserate  ,'0' AS from_ob ,'0' as entry_no ".
                    "   from ((seg_encounter_privy_dr as spd left join seg_ops_chrg_dr as socd on ".
                    "      spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and ".
                    "      spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn ".
                    "      inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr) ".
                    "      inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr ".
                    "   where (spd.encounter_nr = '" . $this->current_enr. "'".$filter[1].") ".
                    "      and (str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "      and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "') ".
                    "      and spd.is_deleted != 1 ".
                    "   group by spd.dr_nr, role_area, dr_role_type_nr, spd.entry_no ".
                    " union all ".
                    "select '2' AS accommodation_type, spd.dr_nr, name_last, name_first, name_middle, concat(name, ' - private') as role, spd.days_attended as num_days, 0 as daily_rate, ".
                    "     '' AS opcodes,".
                    " dr_charge, is_excluded, ".
                    "      spd.dr_role_type_nr, role_area, IFNULL(role_type_level, IFNULL(dr_level, tier_nr)) as role_type_level, SUM(ifnull(socd.rvu,0)) as tot_rvu, '0' as avg_multiplier ,spd.caserate as caserate  ,'1' AS from_ob ,spd.entry_no as entry_no".
                    "   from ((seg_encounter_pf_dr as spd left join seg_ops_chrg_dr as socd on ".
                    "      spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and ".
                    "      spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn ".
                    "      inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr) ".
                    "      inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr ".
                    "   where (spd.encounter_nr = '" . $this->current_enr. "'".$filter[3].") ".
                    "      and (str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "      and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "') ".
                    "      and spd.is_deleted != 1 ".
                    "   group by spd.dr_nr, role_area, dr_role_type_nr, spd.entry_no ".
                    " union all ".
                    "select 1 AS accommodation_type,dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role, null as num_days, 0 as daily_rate, GROUP_CONCAT(DISTINCT CONCAT(sosd.ops_code,'-',IFNULL(sosd.rvu,0)) SEPARATOR ';') AS opcodes, ".
                    "      (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(sosd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$this->charged_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$this->charged_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$this->charged_date."'))) END) + ops_charge as dr_charge, 0 as is_excluded, ".
                    "      sop.role_type_nr, role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), sum(sosd.rvu) as tot_rvu, (sum(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$this->charged_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier, '' as caserate  ,'0' AS from_ob ,'0' as entry_no ".
                    "   from (((seg_ops_personell as sop inner join (care_personell as cpn ".
                    "      inner join care_person as cp on cpn.pid = cp.pid) on sop.dr_nr = cpn.nr) ".
                    "      inner join (seg_ops_serv as sos inner join
                            (SELECT sd.refno, ops_code, rvu, multiplier, group_code
                                FROM seg_ops_servdetails AS sd INNER JOIN seg_ops_serv AS sh
                                    ON sd.refno = sh.refno
                                WHERE sh.encounter_nr = '$this->current_enr'
                                    HAVING (rvu = (SELECT MAX(rvu) AS rvumax
                                                    FROM seg_ops_servdetails AS d
                                                    WHERE d.refno = sd.refno AND d.group_code = sd.group_code)
                                         AND sd.group_code <> '') OR sd.group_code = '') as sosd ".
                    "         on sos.refno = sosd.refno) on sop.refno = sos.refno) ".
                    "      inner join care_role_person as crp on sop.role_type_nr = crp.nr) ".
                    "      inner join seg_ops_rvs as cop on sop.ops_code = cop.code ".
                    "   where (encounter_nr = '" . $this->current_enr. "'".$filter[2].") and upper(trim(sos.status)) <> 'DELETED' ".
                    "      and (str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
                    "         and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "') " .
                    "      and role_area is not null and crp.role not like '%_asst%' " .
                    "      and sosd.ops_code = sop.ops_code ".
                    "   group by dr_nr, role_area, role_type_nr";
                    #var_dump($strSQL);exit();
        if ($result = $db->Execute($strSQL)) {
            $this->proffees_list = array();

            if ($result->RecordCount()) {
        $bhasD4 = false;
        $d3indx = -1;
        $indx = 0;
                while ($row = $result->FetchRow()) {
                    $objpf = new ProfFee;
                    // print_r("x");
          if ($row['role_area'] == 'D4') $bhasD4 = true;
          if ($row['role_area'] == 'D3' && !$row['is_excluded']) $d3indx = $indx;
        
          if( !($row['dr_charge']) && ($this->old_bill_nr) ){
               $row['dr_charge'] = $this->getDoctorPFCharge($this->old_bill_nr,$row['dr_nr'],$row['role_area']);  
          }
        
                    $objpf->setAccommodationType($row['accommodation_type']);
                    $objpf->setDrNr($row['dr_nr']);
                    $objpf->setDrLast($row['name_last']);
                    $objpf->setDrFirst($row['name_first']);
                    $objpf->setDrMid((is_null($row['name_middle'])) ? '' : $row['name_middle']);
                    $objpf->setRoleNo($row['role_nr']);
                    $objpf->setRoleDesc($row['role']);
                    $objpf->setRoleBenefit($row['role_area']);
                    $objpf->setRoleLevel($row['role_type_level']);
                    $objpf->setCaserate($row['caserate']);
                    $objpf->setDaysAttended($row['num_days']);
                    $objpf->setDrDailyRate($row['daily_rate']);
                    $objpf->setDrCharge($row['dr_charge']);
                    $objpf->setRVU($row['rvu']);
                    $objpf->setMultiplier($row['multiplier']);
                    $objpf->setChrgForCoverage((($row['is_excluded'] != 0) ? 0 : $row['dr_charge']));
                    $objpf->setIsExcludedFlag(($row['is_excluded'] != 0));
                    $objpf->setOpCodes($row['opcodes']);
                    $objpf->setFromOb($row['from_ob']);
                    $objpf->setEntryNo($row['entry_no']);
                    #$objpf->setPrevEncounter($this->prev_encounter_nr);

                    //added by jasper 09/01/2013 - FOR BUG#302 SURGEON'S PF IS NOT DISCOUNTABLE
                    //FOR PATIENTS WITHOUT PHIC IN OBANNEX
                    $opcodes = $row['opcodes'];
                    if ($opcodes != '') {
                        $opcodes = explode(";", $opcodes);
                        if (is_array($opcodes)) {
                        foreach($opcodes as $v) {
                            $i = strpos($v, '-');
                            if (!($i === false)) {
                            $code = substr($v, 0, $i);
                            if ($row['role_area'] == 'D3' && $this->findOPcodeNormalDelivery($code) && !$this->isPHIC() && $this->isOBAnnex()) {
                               $this->nonDiscountablePF += $row['dr_charge'];
                            }       
                            }
                        }
                        } else {
                        $i = strpos($opcodes, '-');
                        if (!($i === false)) {
                            $code = substr($opcodes, 0, $i);
                            if ($row['role_area'] == 'D3' && $this->findOPcodeNormalDelivery($code) && !$this->isPHIC() && $this->isOBAnnex()) {
                               $this->nonDiscountablePF += $row['dr_charge'];
                            }       
                        }    
                        }
                    }
                    //added by jasper 09/01/2013 - FOR BUG#302
                    // Add new Service object in collection (array) of doctors' fees charged in this billing.
                    $this->proffees_list[] = $objpf;

          $indx++;
                }

        //Commented By Jarel set the Actual Charge from UI
        /*if (!$bhasD4 && ($d3indx != -1)) {
          $this->proffees_list[$d3indx]->setDrCharge( $this->proffees_list[$d3indx]->getDrCharge() + ($amountlimit * $this->getCaseRatePkgLimit('D4', $issurgical)) );
        }*/
            }
        }
        // die();
    }

    function isOBAnnex() {
        if ($this->accomm_typ_desc == '') {
            $this->getAccommodationType();
        }
        return (!(strpos(strtoupper($this->accomm_ward_name), OBANNEX, 0) === false));
    }

    function findOPcodeNormalDelivery($op_code) {
    global $db;
    
    $strSQL = "SELECT COUNT(ops_code) AS cnt FROM seg_ops_normaldelivery WHERE ops_code = '" . $op_code . "'";
    if ($result = $db->Execute($strSQL)) {
        if ($result->RecordCount()) {
            $row = $result->FetchRow();
            if ($row['cnt'] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
        return false;
        }
    }
    }

    function isPHIC() {
        global $db;

        $ncount = 0;
        $filter .= (($filter != "") ? "," : "(")."'{$this->current_enr}')";
        $strSQL = "SELECT ".
                  "     COUNT(*) isphic ".
                  "   FROM seg_encounter_insurance ".
                  "   WHERE encounter_nr IN {$filter} ".
                  "      AND hcare_id = ".PHIC_ID.
                  "   ORDER BY priority LIMIT 1";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $ncount = $row['isphic'];
            }
        }
        return ($ncount > 0);
    }

    function getProfFeesBenefits() {
        global $db;

        $tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->charged_date)));
        $bill_date = $this->charged_date;
        $filter = array('','','','');

        $issurgical  = $this->isSurgicalCase();

        if ($this->isWellBaby()) {
            $amountlimit = DEFAULT_NBPKG_RATE;
        } else {
            $amountlimit = $this->pkgamountlimit;
        }
        //added by jasper 09/03/2013 FOR BUG#305

        $hc_pf = $this->getHouseCasePCF();

        if ($this->prev_encounter_nr != '') $filter[0] = " or dm1.encounter_nr = '$this->prev_encounter_nr'";
        if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";
        if ($this->prev_encounter_nr != '') $filter[2] = " or encounter_nr = '$this->prev_encounter_nr'";
        $prev_encounter = $this->getPrevEncounterNr($this->current_enr);
        // die($prev_encounter);
        if ($prev_encounter != '')  $filter[3] = " or spd.encounter_nr = '$prev_encounter' ";

        $strSQL = "select dr_nr, role_area, role_type_level, opcodes, sum(num_days) as totaldays, sum(rvu) as totalrvu, (sum(multiplier * rvu)/sum(rvu)) as avgmuliplier, sum(dr_charge) as totalcharge, ".
                    "\n     sum(case when is_excluded <> 0 then 0 else dr_charge end) as chrg_for_coverage ".
                    "\n  from ".
                    "\n  (select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Doctor' as role, ".
                    "\n   sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as num_days, daily_rate, ".
                    "\n   '' as opcodes, daily_rate * sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as dr_charge, ".
                    "\n   0 as is_excluded, role_nr, role_area, 0 as role_type_level, 0 as rvu, 0 as multiplier ".
                    "\n   from ".
                    "\n      (select distinct attending_dr_nr, name_last, name_first, name_middle, attend_start, ".
                    "\n          subdate((select attend_start ".
                    "\n                      from seg_encounter_dr_mgt as dm2 ".
                    "\n                      where dm2.encounter_nr = dm1.encounter_nr and ".
                    "\n                            dm2.att_hist_no > dm1.att_hist_no ".
                    "\n                      order by dm2.att_hist_no asc limit 1), 1) as attend_end, fn_getdailyrate('{$this->current_enr}', date('{$this->bill_dte}'), tier_nr, {$this->confinetype_id}, attending_dr_nr) as daily_rate, cpa.role_nr, fn_getDailyVisitRoleArea(tier_nr) as role_area, discharge_date ".
                    "\n          from (seg_encounter_dr_mgt as dm1 inner join (((care_personell as cpn ".
                    "\n             inner join care_person as cp on cpn.pid = cp.pid) inner join care_personell_assignment as cpa ".
                    "\n             on cpn.nr = cpa.personell_nr) inner join care_role_person as crp on ".
                    "\n             cpa.role_nr = crp.nr) on dm1.attending_dr_nr = cpn.nr) inner join care_encounter as ce ".
                    "\n             on dm1.encounter_nr = ce.encounter_nr ".
                    "\n          where (dm1.encounter_nr = '" . $this->current_enr. "'".$filter[0].") " .
                    "\n             and (str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
                    "\n                and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') " .
                    "\n                 and dm1.from_dialysis = 0".
                    "\n          order by att_hist_no) as t ".
                    "\n   group by attending_dr_nr, role_area, role_nr ".
                    "\n union ".
                    "\nselect distinct spd.dr_nr, name_last, name_first, name_middle, concat(name, ' - private') as role, (case when is_excluded <> 0 then 0 else spd.days_attended end) as num_days, 0 as daily_rate, GROUP_CONCAT(DISTINCT CONCAT(socd.ops_code, '-', IFNULL(socd.rvu,0)) SEPARATOR ';') AS opcodes, ".
                    "\n      (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(socd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(is_excluded OR ".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$bill_date."'))) END) + dr_charge as dr_charge, is_excluded, ".
                    "\n      spd.dr_role_type_nr, role_area, IFNULL(role_type_level, IFNULL(dr_level, tier_nr)) as role_type_level, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier ".
                    "\n   from ((seg_encounter_privy_dr as spd left join seg_ops_chrg_dr as socd on ".
                    "\n      spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and ".
                    "\n      spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn ".
                    "\n      inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr) ".
                    "\n      inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr ".
                    "\n   where (spd.encounter_nr = '" . $this->current_enr. "'".$filter[1].") ".
                    "\n      and (str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "\n      and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') ".
                    "\n      and spd.is_deleted != 1".
                    "\n   group by spd.dr_nr, role_area, dr_role_type_nr, spd.entry_no ".
                    "\n union ".
                    "\nselect distinct spd.dr_nr, name_last, name_first, name_middle, concat(name, ' - private') as role, (case when is_excluded <> 0 then 0 else spd.days_attended end) as num_days, 0 as daily_rate, '' AS opcodes, ".
                    "\n      (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(socd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(is_excluded OR ".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$bill_date."'))) END) + dr_charge as dr_charge, is_excluded, ".
                    "\n      spd.dr_role_type_nr, role_area, IFNULL(role_type_level, IFNULL(dr_level, tier_nr)) as role_type_level, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier ".
                    "\n   from ((seg_encounter_pf_dr as spd left join seg_ops_chrg_dr as socd on ".
                    "\n      spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and ".
                    "\n      spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn ".
                    "\n      inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr) ".
                    "\n      inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr ".
                    "\n   where (spd.encounter_nr = '" . $this->current_enr. "'".$filter[3].") ".
                    "\n      and (str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "\n      and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') ".
                    "\n      and spd.is_deleted != 1".
                    "\n   group by spd.dr_nr, role_area, dr_role_type_nr, spd.entry_no ".
                    "\n union ".

                    "\n   select dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role, null as num_days, 0 as daily_rate, GROUP_CONCAT(DISTINCT CONCAT(sosd.ops_code,'-',IFNULL(sosd.rvu,0)) SEPARATOR ';') AS opcodes, ".
                    "\n         (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(sosd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$bill_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$bill_date."'))) END) + ops_charge as dr_charge, ".
                    "\n         0 as is_excluded, sop.role_type_nr, role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), sum(sosd.rvu) as tot_rvu, (sum(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier ".
                    "\n      from (((seg_ops_personell as sop inner join (care_personell as cpn ".
                    "\n         inner join care_person as cp on cpn.pid = cp.pid) on sop.dr_nr = cpn.nr) ".
                    "\n         inner join (seg_ops_serv as sos inner join
                                (SELECT sd.refno, ops_code, rvu, multiplier, group_code
                                FROM seg_ops_servdetails AS sd INNER JOIN seg_ops_serv AS sh
                                    ON sd.refno = sh.refno
                                WHERE sh.encounter_nr = '$this->current_enr'
                                    HAVING (rvu = (SELECT MAX(rvu) AS rvumax
                                                    FROM seg_ops_servdetails AS d
                                                    WHERE d.refno = sd.refno AND d.group_code = sd.group_code)
                                         AND sd.group_code <> '') OR sd.group_code = '') as sosd ".
                    "\n            on sos.refno = sosd.refno) on sop.refno = sos.refno) ".
                    "\n         inner join care_role_person as crp on sop.role_type_nr = crp.nr) ".
                    "\n         inner join seg_ops_rvs as cop on sop.ops_code = cop.code ".
                    "\n      where (encounter_nr = '" . $this->current_enr. "'".$filter[2].") and upper(trim(sos.status)) <> 'DELETED' ".
                    "\n         and (str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
                    "\n            and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') " .
                    "\n         and role_area is not null and crp.role not like '%_asst%' " .
                    "\n               and sosd.ops_code = sop.ops_code ".
                    "\n      group by dr_nr, role_area, role_type_nr) ".
                    "\n as o group by role_area, dr_nr";
                    // die($strSQL);
        if ($result = $db->Execute($strSQL)) {
            $this->hsp_pfs_benefits = array();
            $this->pfs_confine_coverage = array();

            if ($result->RecordCount()) {
                $bhasD4 = false;
                $d3indx = -1;
                $indx = 0;

                while ($row = $result->FetchRow()) {
                    $objpfc = new ProfFeeCoverage;

                    if ($row['role_area'] == 'D4') $bhasD4 = true;
                    if ($row['role_area'] == 'D3' && ($row['chrg_for_coverage'] > 0)) $d3indx = $indx;

                    $objpfc->setDrNr($row['dr_nr']);
                    $objpfc->setRoleBenefit($row['role_area']);
                    $objpfc->setRoleLevel((is_null($row['role_type_level']) ? 0 : $row['role_type_level']));
                    $objpfc->getRoleDesc();
                    if (is_null($row['totaldays']))
                        $objpfc->setDaysAttended(0);
                    else
                        $objpfc->setDaysAttended($row['totaldays']);
                  
                    $objpfc->setDrCharge($row['totalcharge']);
                    $objpfc->setRVU($row['totalrvu']);
                    $objpfc->setMultiplier($row['avgmuliplier']);
                    $objpfc->setChrgForCoverage($row["chrg_for_coverage"]);
                    $objpfc->setOpCodes($row['opcodes']);

                    // Add new object in collection (array) of doctors' fees charged in this billing.
                    $this->hsp_pfs_benefits[] = $objpfc;

                    $indx++;
                }

                if (!$bhasD4 && ($d3indx != -1)) {
                    $this->hsp_pfs_benefits[$d3indx]->setDrCharge( $this->hsp_pfs_benefits[$d3indx]->getDrCharge() + ($amountlimit * $this->getCaseRatePkgLimit('D4', $issurgical)) );
                    $this->hsp_pfs_benefits[$d3indx]->setChrgForCoverage( $this->hsp_pfs_benefits[$d3indx]->getChrgForCoverage() + ($amountlimit * $this->getCaseRatePkgLimit('D4', $issurgical)) );
                }
            }
        }
$this->sql = $strSQL;
        // $this->hsp_pfs_benefits = $strSQL;
        // return $strSQL;
    }

    function isWellBaby() {
        global $db;

        $enc_type = 0;
        $strSQL = "select encounter_type ".
                            "   from care_encounter ".
                            "   where encounter_nr = ".$db->qstr($this->current_enr);
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = $row['encounter_type'];
            }
        }

        return ($enc_type == WELLBABY);
    }

    function isSurgicalCase() {
        global $db;

        $flag = 0;
        $strSQL = "select count(*) as is_surgical
                        from
                    (select 1 as tr_id, os.refno
                        from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
                        where (encounter_nr = '". $this->current_enr. "') and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
                     union
                     select 2 as tr_id, mo.refno
                        from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
                        where (encounter_nr = '". $this->current_enr. "')) as t";

        $row = $db->GetRow($strSQL);
        $flag = (is_null($row['is_surgical'])) ? 0 : $row['is_surgical'];

        return ($flag != 0);
    }

    function isCharity() {
        if ($this->accomm_typ_desc == '') {
            $this->getAccommodationType();
        }
        if (empty($this->accomm_typ_desc)) {
            return true;
        } else {
            return (!(strpos(strtoupper($this->accomm_typ_desc), CHARITY, 0) === false));
        }
    }

    function getAccommodationType() {
        global $db;

        $ntype = 0;
        $sname = '';
        $filter = array('','');

        if ($this->prev_encounter_nr != '') {
            $filter[0] = " or cel.encounter_nr = '$this->prev_encounter_nr'";
            $filter[1] = " or sel.encounter_nr = '$this->prev_encounter_nr'";
        }

            $strSQL = "select 0 AS entry_no,
                  STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') occupy_date,
                  cw.accomodation_type, accomodation_name, cw.name AS ward_name ".
                    "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
                    "      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr) ".
                    "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr ".
                    "   where (cel.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
                    "      and exists (select nr ".
                    "                     from care_type_location as ctl ".
                    "                     where upper(type) = 'WARD' and ctl.nr = cel.type_nr) ".
                    "      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "      and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
                    "         or ".
                    "      (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "      and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
                    "      or ".
                    "      str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
          " UNION ALL
            SELECT entry_no, occupy_date, cw.accomodation_type, accomodation_name, cw.name AS ward_name
              FROM (seg_encounter_location_addtl sel INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr)
                INNER JOIN seg_accomodation_type sat ON cw.accomodation_type = sat.accomodation_nr
            WHERE (sel.encounter_nr = '". $this->current_enr. "'".$filter[1].")
              AND (
                STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) >= '" . $this->bill_frmdte . "'
                AND STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) < '" . $this->bill_dte . "'
              )
            ORDER BY entry_no DESC LIMIT 1";

        $this->debugSQL = $strSQL;
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $ntype = $row['accomodation_type'];
                    $sname = $row['accomodation_name'];
                    $ward_name = $row['ward_name'];
                }
            }
        }

        $this->accomm_typ_nr = $ntype;
        $this->accomm_typ_desc = $sname;
        $this->accomm_ward_name = $ward_name;

        return($db->ErrorMsg() == '');

    }

    function getPFBenefits() {
        return($this->hsp_pfs_benefits);
    }

    function getIsCoveredByPkg() {
        return($this->is_coveredbypkg);
    }

    function getCurrentEncounterNr() {
        return($this->current_enr);
    }

    function initProfFeesCoverage($pfarea) {
        $this->pfs_confine_coverage[$pfarea] = 0.00;
        $this->pfs_confine_benefits[$pfarea] = array();
    }

    function getTotalPFCharge($pfarea = '') {
        // Compute total doctors' fees ...
        $npf      = 0;
        $ndays    = 0;
        $nrvu     = 0;
        $total_df = 0;

        // .... D1 role
        $this->getTotalPFParams($ndays, $nrvu, $npf, 'D1');
        $total_df += $npf;
        if ($pfarea == 'D1') return $npf;

        // .... D2 role
        $this->getTotalPFParams($ndays, $nrvu, $npf, 'D2');
        $total_df += $npf;
        if ($pfarea == 'D2') return $npf;

        // .... D3 role
        $this->getTotalPFParams($ndays, $nrvu, $npf, 'D3');
        $total_df += $npf;
        if ($pfarea == 'D3') return $npf;

        // .... D4 role
        $this->getTotalPFParams($ndays, $nrvu, $npf, 'D4');
        $total_df += $npf;
        if ($pfarea == 'D4') return $npf;

        $this->total_pf_charge = $total_df;
        return($total_df);
    }

    function delEncDoctors($enc){
        global $db;

        $this->sql_mgt = "DELETE FROM seg_encounter_dr_mgt
                                WHERE encounter_nr = ".$db->qstr($enc);
        $del = $db->Execute($this->sql_mgt);

        $this->sql_prv = "DELETE FROM seg_encounter_privy_dr
                                WHERE encounter_nr = ".$db->qstr($enc);

        $bSuccess = $db->Execute($this->sql_prv);
        
        return $bSuccess;

    }

    function getMiscList()
    {
        global $db;
        $filter = '';
        $thischarged_date = strftime("%Y-%m-%d %H:%M:%S");
        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);
        
        if ($prev_encounterr != '') $filter = " OR encounter_nr = ".$db->qstr($prev_encounter);

        $this->sql = "select mcd.service_code, sos.name, sos.description, mcd.refno, sum(mcd.quantity) as qty, (sum(quantity * chrg_amnt)/sum(mcd.quantity)) as avg_chrg, ".
                    "      sum(quantity * chrg_amnt) as total_chrg ".
                    "   from (seg_misc_chrg as mc inner join seg_misc_chrg_details as mcd on ".
                    "      mc.refno = mcd.refno) inner join seg_other_services as sos on ".
                    "      mcd.service_code = sos.service_code ".
                    "   where (encounter_nr = ".$db->qstr($this->encounter_nr)." ".$filter.") ".
                    "      and (str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') >= " .$db->qstr($this->bill_frmdte). " ".
                    "      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') < " . $db->qstr($thischarged_date) . ") ".
                    "   group by mcd.service_code, sos.name ".
                    "   order by sos.name";

        //echo $this->sql;
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }

    }

    function hasSavedBill($enc)
    {
        global $db;

        $this->sql = "SELECT * FROM seg_billing_encounter ".
                     "WHERE encounter_nr=".$db->qstr($enc).
                     " AND (is_deleted=0 OR is_deleted IS NULL) ".
                     " ORDER BY bill_dte DESC LIMIT 1 ";
                
        if ($this->result=$db->Execute($this->sql)) {
            $this->count=$this->result->RecordCount();
            return $this->result->FetchRow();
        } else{
            return FALSE;
        }
    }


    function caseRateInfo($code) {
        global $db;

        $this->sql = "SELECT * FROM seg_case_rate_packages WHERE code=".$db->qstr($code);

        if ($buf=$db->Execute($this->sql)){
                if($buf->RecordCount()) {
                    return $buf;
                }else { return FALSE; }
            }else { return FALSE; }

    }
    
    function setDeathData($data){
        global $db;
        if($data['enc']=='')
            $data['enc'] = "0";
        if($data['deathdate']=='')
            $data['deathdate'] = "0000-00-00 00:00:00";
        $db->BeginTrans();

        $this->sql = "UPDATE care_person SET
                        death_date = DATE_FORMAT('".$data['deathdate']."', '%Y-%m-%d'),
                        death_time = DATE_FORMAT('".$data['deathdate']."', '%H:%i:%s'),
                        history = CONCAT(history, 'Update: ', NOW(), ' ".$data['userid']."\\n'),
                        modify_id = '".$data['userid']."',
                        modify_time = NOW(),
                        death_encounter_nr = '".$data['enc']."'
                        WHERE pid = '".$data['pid']."'";
        $success1 = $db->Execute($this->sql);

        if($success){
            $fldarray = array('encounter_nr' => $db->qstr($data['enc']),
                        'result_code' => '4',
                        'modify_id' => $db->qstr($data['userid']),
                        'modify_time' => 'NOW()',
                        'create_id' => $db->qstr($data['userid']),
                        'create_time' => 'NOW()');
            $success2 = $db->Replace('seg_encounter_result', $fldarray, array('encounter_nr'));
        }

        if(!$success1 || $success2){
            $db->RollbackTrans();
            $objResponse->alert($db->ErrorMsg());
        }
        else{
            $db->CommitTrans();
        }
    }
    function getPrincipalPIDofHCare($s_pid, $nhcareid) {
        global $db;

        $sprincipal_pid = "";

        $strSQL = "select pid ".
                    "   from care_person_insurance ".
                    "      where pid = '". $s_pid ."' and hcare_id = '". $nhcareid ."' ".
                    "      and is_principal <> 0 and is_void = 0";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $sprincipal_pid = $row['pid'];
            }
        }

        return $sprincipal_pid;
    }
    //added by kenneth 12/13/2013
    function saveMiscServices($data){
        global $db;
        $refno = $this->getMiscSrvcRefNo($data['bill_frmdte'],$data['enc_nr'], $data['sess_user_name']);
        if($refno == ''){
            $this->sql = "INSERT INTO seg_misc_service (chrge_dte, encounter_nr, modify_id, create_id, create_dt)
                                VALUES (".$db->qstr($data['bill_dt']).", ".$db->qstr($data['enc_nr']).", ".$db->qstr($data['sess_user_name']).", 
                                        ".$db->qstr($data['sess_user_name']).", ".$db->qstr($data['bill_dt']).")";
            if($res=$db->Execute($this->sql))
                return true;
            else
                return false;  
        }
        else
            return true;    
    }
    function saveMiscServicesDetails($data){
        global $db;
        $refno = $this->getMiscSrvcRefNo($data['bill_frmdte'],$data['enc_nr'], $data['sess_user_name']);
        $history = $db->qstr("Added by: " . $_SESSION['sess_user_name'] . " [". date('Y-m-d H:i:s') ."] \n");
        $this->sql = "INSERT INTO seg_misc_service_details (refno, service_code, account_type, chrg_amnt, quantity, is_fs, create_id, create_dt, history) VALUES (".$db->qstr($refno).", ".$db->qstr($data['code']).",".$db->qstr($data['acct_type']).", ".$db->qstr($data['msc_charge']).",".$db->qstr($data['qty']).", ".$db->qstr($data['is_fs']).",".$db->qstr($_SESSION['sess_user_name']).", ". $db->qstr(date('Y-m-d H:i:s')) .", ".$history.")";
        if($res=$db->Execute($this->sql))
            return true;
        else
            return false;  
    }
    function savePharmaSupply($data){
        global $db;
        $refno = $this->getPharmaChrgRefNo($data['bill_frmdte'],$data['enc_nr']);
        if($refno == ''){
            $this->sql = "INSERT INTO seg_more_phorder (chrge_dte, encounter_nr, area_code, modify_id, create_id, create_dt)
                                VALUES ('".$data['bill_dt']."', '".$data['enc_nr']."', '".$data['area_code']."', '".$data['sess_user_name']."', '".$data['sess_user_name']."', '".$data['bill_dt']."')";
            if($res=$db->Execute($this->sql))
                return true;
            else
                return false;       
        }
        else
            return true;
    }
    function savePharmaSupplyDetails($data){
        global $db;
        $refno = $this->getPharmaChrgRefNo($data['bill_frmdte'],$data['enc_nr']);
        $history = $db->qstr("Added by: " . $_SESSION['sess_user_name'] . " [". date('Y-m-d H:i:s') ."] \n");
        $this->sql = "INSERT INTO seg_more_phorder_details (refno, bestellnum, quantity, unit_price,is_fs, create_id, create_dt, history)
                            VALUES ('".$refno."', '".$data['code']."', '".$data['qty']."', '".$data['msc_charge']."', '".$data['is_fs']."', '".$_SESSION['sess_user_name']."', '". date('Y-m-d H:i:s') ."', ".$history.")";
        if($res=$db->Execute($this->sql))
            return true;
        else
            return false; 
    }
    function deletePharmaSupply($data){
        global $db;
        $history = "Deleted by: " . $_SESSION['sess_user_name'] . " [". date('Y-m-d H:i:s') ."] \n";
        $this->sql = "SELECT * FROM seg_more_phorder_details AS smpd
                        WHERE bestellnum = '".$data['serv_code']."'
                        AND EXISTS (SELECT * FROM seg_more_phorder AS smp
                        WHERE smp.refno = smpd.refno
                        AND smp.encounter_nr = '".$data['encounter_nr']."'
                        AND smp.chrge_dte >= '".$data['bill_frmdte']."')
                        AND get_lock('smp_lock', 10)
                        ORDER BY entry_no desc limit 1 FOR update";
        $rs = $db->Execute($this->sql);
        if($rs){
            if($row = $rs->FetchRow()){
                $refno = $row['refno'];
                $entryno = $row['entry_no'];
                $this->sql = "UPDATE seg_more_phorder_details SET
                                is_deleted = 1,
                                modify_id = '".$_SESSION['sess_user_name']."',
                                modify_dt = '".date('Y-m-d H:i:s')."',
                                history = ".$this->ConcatHistory($history)."
                                WHERE bestellnum = '".$data['serv_code']."'
                                AND is_deleted <> 1
                                AND refno = '".$refno."'";
                               
                $success = $db->Execute($this->sql);
                $sql = "SELECT RELEASE_LOCK('smp_lock')";
                $db->Execute($sql);
                if($success){
                    $dcount = 0;
                    $this->sql = "SELECT count(*) dcount FROM seg_more_phorder_details
                                WHERE refno = '".$refno."'";
                    $rs = $db->Execute($this->sql);
                    if($rs){
                        $row = $rs->FetchRow();
                        if($row){
                            $dcount = is_null($row['dcount']) ? 0 : $row['dcount'];
                        }
                        if($dcount == 0){
                            $this->sql = "DELETE FROM seg_more_phorder
                                    WHERE refno = '".$refno."'";
                           return $db->Execute($this->sql);
                        }
                    }
                }else
                   return $msg = $db->ErrorMsg();
            }return true;
        }
        else
            return false;
    }
function deleteMiscServices($data){
        global $db;
        $history = "Deleted by: " . $_SESSION['sess_user_name'] . " [". date('Y-m-d H:i:s') ."] \n";
        $this->sql = "SELECT * FROM seg_misc_service_details AS smsd
                WHERE service_code = ".$db->qstr($data['serv_code'])." AND smsd.is_deleted <> 1
                AND EXISTS (SELECT * FROM seg_misc_service AS sms
                    WHERE sms.refno = smsd.refno AND !is_cash
                    AND sms.encounter_nr = ".$db->qstr($data['encounter_nr'])."
                    AND sms.chrge_dte >= ".$db->qstr($data['bill_frmdte']).")
                    AND get_lock('sms_lock', 10)
                    ORDER BY entry_no desc limit 1 FOR update";

        $rs = $db->Execute($this->sql);

        if($rs){
            if($row = $rs->FetchRow()){
                $refno = $row['refno'];
                $entryno = $row['entry_no'];
                $this->sql = "UPDATE seg_misc_service_details SET
                                is_deleted = 1,
                                modify_id = '".$_SESSION['sess_user_name']."',
                                modify_dt = '".date('Y-m-d H:i:s')."',
                                history = ".$this->ConcatHistory($history)."
                                WHERE service_code = ".$db->qstr($data['serv_code'])."
                                AND is_deleted <> 1
                                AND refno = ".$db->qstr($refno)."";
                $success = $db->Execute($this->sql);
                
                $sql = "SELECT RELEASE_LOCK('sms_lock')";
                $db->Execute($sql);
                if($success){
                    $dcount = 0;
                    $this->sql = "SELECT count(*) dcount FROM seg_misc_service_details
                                WHERE refno = ".$db->qstr($refno)."";
                    $rs = $db->Execute($this->sql);
                    if($rs){
                        $row = $rs->FetchRow();
                        if($row){
                            $dcount = is_null($row['dcount']) ? 0 : $row['dcount'];
                        }
                        if($dcount == 0){
                            $this->sql = "DELETE FROM seg_misc_service
                                    WHERE refno = ".$db->qstr($refno)."";
                           return $db->Execute($this->sql);
                        }
                    }
                }else
                   return $msg = $db->ErrorMsg();
            }else{
                $sql = "SELECT RELEASE_LOCK('sms_lock')";
                $db->Execute($sql);
            }
            return true;
        }
        else
            return false;
    }
    function deleteMiscCharge($data){
        global $db;
       
        $this->sql = "SELECT * FROM seg_misc_chrg_details AS smcd
           WHERE service_code = ".$db->qstr($data['code'])."
                        AND EXISTS (SELECT * FROM seg_misc_chrg AS smc
                        WHERE smc.refno = smcd.refno
                        AND smc.encounter_nr = ".$db->qstr($data['encounter_nr'])."
                        AND smc.chrge_dte >= ".$db->qstr($data['bill_frmdte']).")
                        AND get_lock('smp_lock', 10)
                        ORDER BY entry_no desc limit 1 FOR update";
        $rs = $db->Execute($this->sql);
        if($rs){
            if($row = $rs->FetchRow()){
                $refno = $row['refno'];
                $entryno = $row['entry_no'];
                $this->sql = "DELETE FROM seg_misc_chrg_details
                                WHERE service_code = ".$db->qstr($data['code'])."
                                AND refno = '".$refno."'";
                $success = $db->Execute($this->sql);
                $sql = "SELECT RELEASE_LOCK('sms_lock')";
                $db->Execute($sql);
                if($success){
                    $dcount = 0;
                    $this->sql = "SELECT count(*) dcount FROM seg_misc_chrg_details
                                WHERE refno = '".$refno."'";
                    $rs = $db->Execute($this->sql);
                    if($rs){
                        $row = $rs->FetchRow();
                       if($dcount == 0){
                            $this->sql = "DELETE FROM seg_misc_service
                                    WHERE refno = '".$refno."'";
                           return $db->Execute($this->sql);
                        }
                    }
                }else
                   return $msg = $db->ErrorMsg();
            }return true;
        }
        else
            return false;
    }
    
    function getMiscSrvcRefNo($bill_frmdte, $enc_nr, $userid) {
        global $db;

        $srefno = '';
        # Fix for MS-535 by Bong
        $strSQL = "select refno ".
                            "   from seg_misc_service ".
                            "   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= ".$db->qstr($bill_frmdte)." ".
                            "      and encounter_nr = ".$db->qstr($enc_nr)." ".
                            "      and create_id = ".$db->qstr($userid)." ".
                            "      and !is_cash ".
                            "   order by chrge_dte limit 1";

        if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                        while ($row = $result->FetchRow())
                                $srefno = $row['refno'];
                }
        }
        return($srefno);
    }
    
    function saveMiscCharge($data_misc)
    {
        global $db;

        $data_misc['bill_frmdte'] = date('Y-m-d H:i:s', strtotime($data_misc['bill_frmdte'])); 

        $refno = $this->getMiscChrgRefNo($data_misc['bill_frmdte'],$data_misc['enc_nr']);
        $this->sql = "INSERT INTO seg_misc_chrg_details (refno, service_code, account_type, quantity, chrg_amnt)
                            VALUES ('".$refno."', ".$db->qstr($data_misc['code']).",".$db->qstr($data_misc['acct_type']).", ".$db->qstr($data_misc['qty']).", ".$db->qstr($data_misc['msc_charge']).")";
        if($res=$db->Execute($this->sql))
            return true;
        else
            return false; 
    }
    
    function CreateMiscCharge($data)
    {
        global $db;
        $refno = $this->getMiscChrgRefNo($data['bill_frmdte'],$data['enc_nr']);
        
        if($refno == ''){
            $this->sql = "INSERT INTO seg_misc_chrg(chrge_dte, encounter_nr, modify_id, create_id, create_dt)
                                VALUES (".$db->qstr($data['bill_dt']).", ".$db->qstr($data['enc_nr']).", ".$db->qstr($data['sess_user_name']).", ".$db->qstr($data['sess_user_name']).", ".$db->qstr($data['bill_dt']).")";
        
            if($res=$db->Execute($this->sql))
                return true;
            else
                return false;  
        }
        else
            return true;   
    }
    
    function getMiscChrgRefNo($bill_frmdte, $enc_nr) {
        global $db;

        $srefno = '';
        # Fix for MS-535 by Bong
        $strSQL = "select refno ".
                            "   from seg_misc_chrg ".
                            "   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= ".$db->qstr($bill_frmdte)." ".
                            "      and encounter_nr = '".$enc_nr."' ".
                            "      and !is_cash ".
                            "   order by chrge_dte limit 1";

        if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                        while ($row = $result->FetchRow())
                                $srefno = $row['refno'];
                }
        }
        return($srefno);
    }
    
    function getPharmaChrgRefNo($bill_frmdte, $enc_nr) {
        global $db;

        $srefno = '';
        $strSQL = "select refno ".
                            "   from seg_more_phorder ".
                            "   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' ".
                            "      and encounter_nr = '".$enc_nr."' ".
                            "   order by chrge_dte limit 1";

        if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                        while ($row = $result->FetchRow())
                                $srefno = $row['refno'];
                }
        }

        return($srefno);
}
    function getPharmaAreas(){
        global $db;

        $this->sql = "SELECT sa.* FROM seg_areas AS sa
                        INNER JOIN care_department AS cd ON sa.dept_nr = cd.nr
                        WHERE name_formal REGEXP '.*pharma.*|.*supply.*'
                        ORDER BY name_formal";
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }
    //ended by kenneth

    function getHouseCasePCF(){
        global $db;

        $bhousecase = 0;
        $strSQL = "select fn_isHouseCaseAsOfRefDate('".$this->encounter_nr."', '".$this->bill_dte."') as casetype";
        if ($result=$db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                     $bhousecase = is_null($row["casetype"]) ? 0 : $row["casetype"];
                }
            }
        }

        if ($bhousecase)
            return DEFAULT_PCF;
        else
            return 0;
    }


    function getAppliedHrsCutoff() {
            global $db;

            $n_cutoff = -1;

            $strSQL = "select applied_hrs_cutoff ".
                                "   from seg_billing_encounter ".
                                "   where bill_nr = ".$db->qstr($this->old_bill_nr)." and is_deleted IS NULL";
            if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    $row = $result->FetchRow();
                    $n_cutoff = $row['applied_hrs_cutoff'];
                }
            }

            return($n_cutoff);
    }

    function correctBillDates() {
        global $db;

        if ($this->old_bill_nr != '') {
            $strSQL = "select bill_dte, bill_frmdte from seg_billing_encounter where bill_nr = ".$db->qstr($this->old_bill_nr)." and is_deleted IS NULL";
            if ($result=$db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    if ($row = $result->FetchRow()) {
                        $this->bill_frmdte = is_null($row["bill_frmdte"]) ? $this->bill_frmdte : $row["bill_frmdte"];
                        $this->bill_dte    = is_null($row["bill_dte"]) ? $this->bill_dte : $row["bill_dte"];
                    }
                }
            }
        }
    }



    function getCaseRatePkgLimit($sBillArea, $issurgical) {
        global $db;

        $sfield = "";
        $share = 0.00;
        if ($sBillArea == 'D3')
            $sfield = "dist_pfsurgeon share";
        elseif ($sBillArea == 'D4')
            $sfield = "dist_pfanesth share";
        elseif (in_array($sBillArea, array('D1','D2'))) {
            $sfield = "dist_pfdaily share";
        }
        else
            $sfield = "dist_hosp share";

        $strSQL = "SELECT $sfield
                    FROM seg_caseratepkgdist
                    WHERE effect_date <= DATE('".$this->bill_dte."')
                       AND case_type = '".(($issurgical) ? 'S' : 'M')."'
                    ORDER BY effect_date DESC LIMIT 1";
        if ($result=$db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    $share = (is_null($row['share'])) ? 0.00 :  $row['share'];
                }
            }
        }

        return $share;
    }

    function getTotalPFParams(&$n_days, &$n_rvu, &$n_pf, $role_area = '', $role_level = 0, $b_noexcluded = false, $drnr = '', $opcode = '') {
        $n_days = 0;
        $n_rvu = 0;
        $n_pf = 0;

        if (!empty($this->hsp_pfs_benefits) && is_array($this->hsp_pfs_benefits))
            foreach ($this->hsp_pfs_benefits as $objpf) {
                if ($objpf->getRoleBenefit() == $role_area) {
                    if ($role_level != 0) {
                        if ($role_level == $objpf->getRoleLevel()) {
                            if ($drnr != '') {
                                if ($drnr == $objpf->getDrNr()) {
                                    $n_days += $objpf->getDaysAttended();
                                    if ($opcode != '') {
                                        $opcodes = $objpf->getOpCodes();
                                        if ($opcodes != '') $opcodes = explode(";", $opcodes);
                                        if (is_array($opcodes)) {
                                            foreach($opcodes as $v) {
                                                $i = strpos($v, '-');
                                                if (!($i === false)) {
                                                    $code = substr($v, 0, $i);
                                                    if ($code == $opcode) {
                                                            $n = strpos($v, '(');
                                                            if (!($n === false))
                                                                 $n_rvu += substr($v, $i+1, $n-($i+1));
                                                            else
                                                                 $n_rvu += substr($v, $i+1);
                                                            break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    else
                                        $n_rvu  += $objpf->getRVU();
                                    $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
                                }
                            }
                            else {
                                $n_days += $objpf->getDaysAttended();
                                $n_rvu  += $objpf->getRVU();
                                $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
                            }
                        }
                    }
                    else {
                        if ($drnr != '') {
                            if ($drnr == $objpf->getDrNr()) {
                        $n_days += $objpf->getDaysAttended();
                                if ($opcode != '') {
                                    $opcodes = $objpf->getOpCodes();
                                    if ($opcodes != '') $opcodes = explode(";", $opcodes);
                                    if (is_array($opcodes)) {
                                        foreach($opcodes as $v) {
                                            $i = strpos($v, '-');
                                            if (!($i === false)) {
                                                $code = substr($v, 0, $i);
                                                if ($code == $opcode) {
                                                        $n = strpos($v, '(');
                                                        if (!($n === false))
                                                             $n_rvu += substr($v, $i+1, $n-($i+1));
                                                        else
                                                             $n_rvu += substr($v, $i+1);
                                                        break;
//                                                      $n_rvu += substr($v, $i+1);
//                                                      break;
}                                            }
                                        }
                                    }
                                }
                                else
                        $n_rvu  += $objpf->getRVU();
                        $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge() - $this->nonDiscountablePF;
                    }
                }
                        else {
                            $n_days += $objpf->getDaysAttended();
                            $n_rvu  += $objpf->getRVU();
                            $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
                        }
//                      $n_days += $objpf->getDaysAttended();
//                      $n_rvu  += $objpf->getRVU();
//                      $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
                    }
                }
            }
    }

    function getTotalOpCharge() {
        global $db;
        $ntotal = 0;
        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select sum(op_charge) as tot_charge from " .
                    "(select oah.refno, entry_no, DATE_FORMAT(oah.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(oah.chrge_dte, '%H:%i:%s') as chrgtime, ".
                    "      concat('OR-', cast(oad.room_nr as char)) as ops_code, concat((select ifnull(name, '') from care_ward where nr = oad.group_nr), '- Room ', cast(cr.room_nr as char)) as description, ".
                    "      (select ifnull(sum(rvu), 0) as trvu from seg_ops_chrgd_accommodation as soca where soca.refno = oah.refno and soca.entry_no = oad.entry_no) as rvu, ".
                    "      (select multiplier from seg_ops_chrgd_accommodation as soca2 where soca2.refno = oah.refno and soca2.entry_no = oad.entry_no limit 1) as multiplier, oad.charge as op_charge, 'RU' as provider ".
                    "   from (seg_opaccommodation as oah inner join seg_opaccommodation_details as oad on oah.refno = oad.refno) ".
                    "      inner join care_room as cr on oad.room_nr = cr.nr ".
                    "   where (encounter_nr = '". $this->current_enr ."'".$filter.") and (str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
                    "      and str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->charged_date ."')) as t";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    if (!is_null($row['tot_charge']))
                        $ntotal += $row['tot_charge'];
                }
            }
        }
        $this->total_op_charge = $ntotal;
        return($ntotal);
    }

    function initOpsConfineCoverage() {
        $this->ops_confine_benefits = array();
        $this->ops_confine_coverage = 0.00;
    }

    function getOpBenefits(){
        global $db;

        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select ops_code, opcode, description, provider, sum(rvu) as sum_rvu,
                                fn_getOPRvuRate('$this->current_enr', date('".$this->charged_date."'), sum(rvu), $this->confinetype_id) as op_multiplier,
        /* (sum(multiplier * rvu)/sum(rvu)) as op_multiplier ,*/ sum(op_charge) as tot_charge ".
                    "   from ".
                    "(select oah.refno, entry_no, DATE_FORMAT(oah.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(oah.chrge_dte, '%H:%i:%s') as chrgtime, ".
                    "      concat('OR-', cast(oad.room_nr as char)) as ops_code,
                                 fn_getopcode(oah.refno, oad.entry_no) AS opcode,
                                 concat((select ifnull(name, '') from care_ward where nr = oad.group_nr), '- Room ', cast(cr.room_nr as char)) as description, ".
                    "      (select ifnull(sum(rvu), 0) as trvu from seg_ops_chrgd_accommodation as soca where soca.refno = oah.refno and soca.entry_no = oad.entry_no) as rvu,
                                (select multiplier from seg_ops_chrgd_accommodation as soca2 where soca2.refno = oah.refno and soca2.entry_no = oad.entry_no limit 1) as multiplier, oad.charge as op_charge, 'RU' as provider ".
                    "   from (seg_opaccommodation as oah inner join seg_opaccommodation_details as oad on oah.refno = oad.refno) ".
                    "      inner join care_room as cr on oad.room_nr = cr.nr ".
                    "   where (encounter_nr = '". $this->current_enr ."'".$filter.") and (str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
                    "      and str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->charged_date ."')) as t ".
                    "group by provider, ops_code, description, opcode, entry_no
                     order by ops_code";    // modified by LST - 11.12.2011 --- Issue (from SOW 10-001)

        if ($result = $db->Execute($strSQL)) {
            $this->hsp_ops_benefits = array();

            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $objOp = new PerOpCoverage;

                    $objOp->setBillDte($this->charged_date);
                    $objOp->setCurrentEncounterNr($this->current_enr);
                    $objOp->setPrevEncounterNr($this->prev_encounter_no);
                    $objOp->setOpCode($row['ops_code']);
                    $objOp->setOpCodePerformed($row['opcode']);
                    $objOp->setOpDesc($row['description']);
                    $objOp->setOpRVU($row['sum_rvu']);
                    $objOp->setOpMultiplier($row['op_multiplier']);
                    $objOp->setOpCharge($row['tot_charge']);
                    $objOp->setOpProvider($row['provider']);

                    $objOp->computeTotalCoverage($this->getBillAreaDRate('OR'));

                    // Add new medicine object in collection (array) of the list of medicines in this billing.
                    $this->hsp_ops_benefits[] = $objOp;
                }
            }
        }
    }

    function getBillAreaDRate($sbill_area) {
        global $db;

        $n_rate = 0;
        $n_prevrate = 0;

        $area_array = array('AC', 'D1', 'D2', 'D3', 'D4');
        if (!($this->isCharity() && (in_array($sbill_area, $area_array)))) {
            // Get discount rate applicable to bill area of current encounter ...
            $strSQL = "select fn_get_bill_discount('". $this->current_enr. "', '". $sbill_area ."', '".$this->bill_dte."') as discount";
            if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    $row = $result->FetchRow();
                    if (!is_null($row['discount'])) {
                        $n_rate = $row['discount'];
                    }
                }
            }

            // .... get discount rate applied to bill area of encounter while at ER, if there is one.
            if ($this->prev_encounter_nr != '') {
                $strSQL = "select fn_get_bill_discount('". $this->prev_encounter_nr. "', '". $sbill_area ."', '".$this->bill_dte."') as discount";
                if ($result = $db->Execute($strSQL)) {
                    if ($result->RecordCount()) {
                        $row = $result->FetchRow();
                        if (!is_null($row['discount'])) {
                            $n_prevrate = $row['discount'];
                        }
                    }
                }
            }

            $n_rate = ($n_rate > $n_prevrate ? $n_rate : $n_prevrate);      // Return the highest discount applied.
        }
        return($n_rate);
    }

    function saveAdditionalAccommodation($data){
        global $db;
        //updated query by gelie 10-23-2015
        if(isset($data['before_accom'])){
            $this->sql = "INSERT INTO seg_encounter_location_addtl
                (encounter_nr,
                     room_nr,
                      group_nr,
                       days_stay,
                        hrs_stay,
                         rate,
                          occupy_date,
                           modify_id,
                           occupy_date_to,
                            create_id,
                             create_dt,
                             occupy_date_from)
                   VALUES 
                   ('".$data['encounter_nr']."',
                        '".$data['room_nr']."',
                         '".$data['ward_nr']."',
                          '".$data['days']."',
                           '0',
                            '".$data['room_rate']."',
                               NOW(),
                              '".$data['sessionID']."',           
                              '".$data['occupydateto']."',
                               '".$data['sessionUN']."', 
                               NOW(),
                               '".$data['occupydatefrom']."')";
        }else{
            $this->sql = "INSERT INTO seg_encounter_location_addtl
                (encounter_nr,
                     room_nr,
                      group_nr,
                       days_stay,
                        hrs_stay,
                         rate,
                          occupy_date,
                          occupy_date_from,
                           occupy_time_from,
                            occupy_date_to,
                             occupy_time_to,
                           modify_id,
                            create_id,
                             create_dt,
                                 is_per_hour)
                   VALUES 
                (".$db->qstr($data['encounter_nr']).",
                    ".$db->qstr($data['room_nr']).",
                     ".$db->qstr($data['ward_nr']).",
                      ".$db->qstr($data['days']).",
                       ".$db->qstr($data['hrs']).",
                        ".$db->qstr($data['room_rate']).",
                               NOW(),
                          ".$db->qstr($data['occupydatefrom']).",
                           ".$db->qstr($data['occupytimefrom']).",
                            ".$db->qstr($data['occupydateto']).",
                             ".$db->qstr($data['occupytimeto']).",
                              ".$db->qstr($data['sessionID']).",
                               ".$db->qstr($data['sessionUN']).", 
                               NOW(),
                                 ".$data['is_per_hour'].")";
        }

            if($this->result=$db->Execute($this->sql)) 
            {
                return $this->result;
            } else { 
                return false; 
            }
    }

    function deleteAccommodation($data){
        global $db;

        if($data['accom_type'] == 'BL'){
            $this->sql = "DELETE FROM seg_encounter_location_addtl 
                      WHERE encounter_nr = '".$data['encounter_nr']."'
                      AND group_nr = '".$data['room_type']."'
                      AND room_nr = '".$data['ward_type']."'
                    ORDER BY entry_no desc limit 1";
        }
        else{
            $this->sql = "DELETE FROM care_encounter_location 
                           WHERE encounter_nr = '".$data['encounter_nr']."'
                           ORDER BY nr DESC LIMIT 3";
        }

        if($this->result=$db->Execute($this->sql)) 
            {
                return $this->result;
            } else { 
                return false; 
            }

    }

function toggleMGH($enc_nr, $mgh_date, $bsetMGH){
    global $db;

    $this->sql = "UPDATE care_encounter SET
                    is_maygohome = $bsetMGH,
                     mgh_setdte   = $mgh_date
                WHERE encounter_nr = '$enc_nr'";

    if($this->result=$db->Execute($this->sql)) 
        {
            return $this->result;
        } else { 
            return false; 
                                }
                            }



function getNewBillingNr() {
        global $db;

        $s_bill_nr = "";

        $strSQL = "SELECT fn_get_new_billing_nr() AS bill_nr";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $s_bill_nr = $row['bill_nr'];
            }
        }

        return $s_bill_nr;
    }

function getConfinementType(){
        global $db;

        $n_id = 0;
        $filter = '';

        if ($this->prev_encounter != '') $filter = " or encounter_nr = '$this->prev_encounter'";

        $strSQL = "select confinetype_id,classify_dte,create_id " .
                    " from seg_encounter_confinement ".
                    "   where (encounter_nr = '". $this->encounter_nr. "'".$filter.") ".
                    // "      and str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' " .
                    "   and is_deleted = 0 order by classify_dte desc limit 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $this->caseTypeHist[0] = $row['create_id'];
                    $this->caseTypeHist[1] = $row['classify_dte'];
                    $n_id = $row['confinetype_id'];
                }
            }
                        }

        // if ($n_id == 0) {
        //     $strSQL = "select confinetype_id from seg_type_confinement_icds as stci
        //                     where exists(select * from care_encounter_diagnosis as ced0
        //                                     where substring(code, 1, if(instr(code, '.') = 0, length(code), instr(code, '.')-1)) =
        //                                         substring(stci.diagnosis_code, 1, if(instr(stci.diagnosis_code, '.') = 0, length(stci.diagnosis_code), instr(stci.diagnosis_code, '.')-1))
        //                     and ((exists(select * from care_encounter_diagnosis as ced where instr(stci.paired_codes, ced.code) > 0 and ced.code <> ced0.code and status <> 'deleted') and stci.paired_codes <> '') or stci.paired_codes = '')
        //                                      and (encounter_nr = '". $this->encounter_nr. "'".$filter.") and str_to_date(create_time, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "'
        //                                      and status <> 'deleted')
        //                     order by confinetype_id desc limit 1";

        //     if ($result = $db->Execute($strSQL)) {
        //         if ($result->RecordCount()) {
        //             while ($row = $result->FetchRow()) {
        //                 $n_id = $row['confinetype_id'];
        //             }
        //         }
        //     }

            if ($n_id == 0) {
                $strSQL = "select confinetype_id from seg_type_confinement
                                where is_default = 1";
                if ($result = $db->Execute($strSQL)) {
                    if ($result->RecordCount()) {
                        while ($row = $result->FetchRow()) {
                            $n_id = $row['confinetype_id'];
                        }
            }
        }
            }
       //}

        $this->confinetype_id = $n_id;
        return($n_id);
    }

    function getActualMedCoverage($nhcare_id) {
        return $this->getAppliedMedsCoverage($nhcare_id);
    }

    function getAppliedMedsCoverage($nhcareid = -1) {
        global $db;

        // $srefno = ($this->old_bill_nr == '') ? 'T'.$this->encounter_nr : $this->old_bill_nr;
        $srefno ='';
        if ($this->old_billnr == '') {
            $srefno = 'T'.$this->encounter_nr;
        }else{
            $srefno = $this->old_billnr;
        }
        $total  = 0;

        $firm_filter = ($nhcareid == -1) ? "" : " and hcare_id = ".$nhcareid;
        $strSQL = "select sum(coverage) as totalcoverage
                      from seg_applied_coverage
                      where ref_no = '$srefno' and source = 'M'".$firm_filter;
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow())
                    $total = $row['totalcoverage'];
            }
        }
        return($total);
    }

    function getActualSrvCoverage($nhcare_id) {
        return($this->getAppliedHSCoverage($nhcare_id));
    }

    function getAppliedHSCoverage($nhcareid = -1) {
        global $db;

        // $srefno = ($this->old_bill_nr == '') ? 'T'.$this->encounter_nr : $this->old_bill_nr;
        // modified by Mary ~ 05-31-2016
        $srefno ='';
        if ($this->old_billnr == '') {
            $srefno = 'T'.$this->encounter_nr;
        }else{
            $srefno = $this->old_billnr;
        }
        $total  = 0;

        $firm_filter = ($nhcareid == -1) ? "" : " and hcare_id = $nhcareid";
        $strSQL = "select sum(coverage) as totalcoverage
                      from seg_applied_coverage
                      where ref_no = '$srefno' and source <> 'M'".$firm_filter;

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow())
                    $total = (is_null($row['totalcoverage']) || $row['totalcoverage'] == '') ? 0 : $row['totalcoverage'];
            }
        }

        return($total);
    }

    function getPreviousPayments() {
        global $db;

        if (isset($this->total_prevpayment) && !$this->forceCompute) {
            return $this->total_prevpayment;
        }

        $total_payment = 0;

        $this->prev_payments = array();

        $filter = array('','');

        if ($this->prev_encounter_nr != '') $filter[0] = " or sp.encounter_nr = '$this->prev_encounter_nr'";
        if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";

        $strSQL = "select spr.or_no, or_date, sum(spr.amount_due) as or_amnt ".
                    "   from seg_pay as sp inner join ".
                    "      (seg_pay_request as spr left join seg_billing_encounter as sbe ".
                    "         on spr.ref_no = sbe.bill_nr and spr.ref_source = 'PP') ".
                    "      on sp.or_no = spr.or_no " .
                    "   where (sp.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
                    "         and str_to_date(or_date, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "' " .
                    "      and (spr.ref_source = 'PP' OR spr.ref_source = 'MHC') and spr.service_code <> 'OBANNEX' and cancel_date is null and sbe.is_deleted IS NULL ".
                    "   group by spr.or_no, or_date ".
                    " union ".
                    "select spd.or_no, or_date, sum(deposit) as or_amnt ".
                    "   from seg_pay as sp1 inner join seg_pay_deposit as spd ".
                    "      on sp1.or_no = spd.or_no " .
                    "   where (spd.encounter_nr = '". $this->current_enr. "'".$filter[1].") ".
                    "         and str_to_date(or_date, '%Y-%m-%d %H:%i:%s') < '" . $this->charged_date . "' " .
                    "      and cancel_date is null ".
                    "   group by spd.or_no, or_date ".
                    "   order by or_date";
        //edited by jasper 08/29/2103 -Fix for OB Annex co-payments BUG#:279
        //echo $strSQL;
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $objpay = new Payment;

                    $objpay->setORNo($row['or_no']);
                    $objpay->setORDate($row['or_date']);
                    $objpay->setAmountPaid($row['or_amnt']);

                    $this->prev_payments[] = $objpay;

                    $total_payment += $row['or_amnt'];
                }
            }
        }

        $this->total_prevpayment = $total_payment;

        return $total_payment;
    }


function savebill($data, $bill_nr, $final) {
        global $db;

        $history = "Created by " . $_SESSION['sess_user_name'] . " on " . date('Y-m-d H:i:s') . "\n";
        $sql = "INSERT INTO seg_billing_encounter 
                        (bill_nr,
                        bill_dte,
                        bill_frmdte,
                        encounter_nr,
                        accommodation_type,
                        total_acc_charge,
                        total_med_charge,
                        total_srv_charge,
                        total_ops_charge,
                        total_doc_charge,
                        total_msc_charge,
                        total_prevpayments,
                        is_final,
                        modify_id,
                        create_id,
                        create_dt,
                        opd_type,
                        discount_type,
                        bill_time_started,
                        bill_time_ended,
                        history)
                VALUES 
                        (".$db->qstr($bill_nr).",
                        ".$db->qstr($data['billdate']).",
                        ".$db->qstr($data['billdatefrom']).",
                        ".$db->qstr($data['encounter']).",
                         ".$db->qstr($data['accommodation_type']).",
                        ".$db->qstr($data['save_total_acc_charge']).",
                        ".$db->qstr($data['save_total_med_charge']).",
                        ".$db->qstr($data['save_total_srv_charge']).",
                        ".$db->qstr($data['save_total_ops_charge']).",
                        ".$db->qstr($data['save_total_doc_charge']).",
                        ".$db->qstr($data['save_total_msc_charge']).",
                        ".$db->qstr($data['save_total_prevpayment']).",
                        ".$db->qstr($final).",
                        ".$db->qstr($_SESSION['sess_temp_userid']).",
                        ".$db->qstr($_SESSION['sess_temp_userid']).",
                        NOW(),
                        ".$db->qstr($data['encounter_type']).",
                        ".$db->qstr($data['isInfirmaryOrDependent']).",
                        ".$db->qstr($data['bill_time_started']).",
                        ".$db->qstr($data['bill_time_ended']).",
                        ".$db->qstr($history).")";

        #echo $sql;
        if($this->result=$db->Execute($sql)) {
            if($db->Affected_Rows()){
                $ok = true; 
            } else {
                $ok = false;  
            }
        } else { 
            $ok = false; 
                }
                
        $this->current_enr = $data['encounter'];
        $ok1 = $this->saveBillingDiscounts($data,$bill_nr, $final);
        $ok2 = $this->saveBillingInsurance($data, $bill_nr, $final);
        $ok3 = $this->saveCaseRatePackage($data, $bill_nr);
        $ok4 = $this->setDischargeName($this->current_enr,$data['pid']);
        $ok5 = $this->dischargeWellBaby($data['encounter'],$final);

        if($final)
        $ok7 = $this->updateAccommodation($data['encounter'],$data['billdate']);
        
        $hasDeleted = $db->GetOne("SELECT * FROM
                                    (SELECT * FROM seg_billing_transactions sbt 
                                        WHERE sbt.`encounter_nr` = {$db->qstr($data['encounter'])}
                                        ORDER BY sbt.`action_date` DESC
                                    ) t WHERE t.action_taken = 'deleted' GROUP BY t.bill_nr DESC");

        if($hasDeleted){
            if($final == 1){
               $rebill = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], 3);

               if($rebill){
                    $ok6 = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], 1);
               }
            }
            else if($data['isPaywardSettlement'] == 1) {
                $ok6 = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], 4);
            }
            else{
               $ok6 = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], $final);
            }
        }
        else if ($data['isPaywardSettlement'] == 1) {
            $ok6 = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], 4);
        }
        else{
            $ok6 = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], $final);
        }
            
            
        if($ok && $ok1 && $ok2 && $ok3 && $ok4 && $ok5 && $ok6){
            return true;
        } else { 
            return false; 
        }


        // End James
    }


    function saveBillingInsurance($data, $bill_nr, $final){
        global $db;

        $result = $this->getPerHCareCoverage($data['encounter']);
        $ndays = '0';
        $ok = true;
            if ($result->RecordCount()) {
                // if($final == 1) {
                    while($objhcare = $result->FetchRow()){
                      
                        $billcover_sql = "INSERT INTO seg_billing_coverage (bill_nr, hcare_id, total_services_coverage, total_acc_coverage, total_med_coverage, total_sup_coverage,
                                                                       total_srv_coverage, total_ops_coverage, total_d1_coverage, total_d2_coverage, 
                                                                       total_d3_coverage, total_d4_coverage, total_msc_coverage)
                                             VALUES (".$db->qstr($bill_nr).", ".$db->qstr($objhcare['hcare_id']).", ".$db->qstr($data['hcicoverage']).",'0', '0', '0', '0', '0', ".$db->qstr($data['d1coverage']).", 
                                                        ".$db->qstr($data['d2coverage']).", ".$db->qstr($data['d3coverage']).", ".$db->qstr($data['d4coverage']).", '0')";
                        if($ok = $db->Execute($billcover_sql))
                            $ok = true;
                        else
                            $ok =false;

                        if($ok && $final){
                            if ($this->isPersonPrincipal($objhcare['hcare_id'], $data['encounter']))
                                $principal_pid = $this->getPrincipalPIDofHCare($data['pid'], $objhcare['hcare_id']);
                            else
                                $principal_pid = "";

                            $insurance_no = $this->getInsuranceNumber($data['encounter']);

                            //added by Nick 4-27-2015,(Bug 694) well baby is said to have no accommodation/confinement
                            // if(!$this->isWellBaby()){
                            $confinementTrackerSql = "INSERT INTO seg_confinement_tracker (pid, current_year, bill_nr, hcare_id, insurance_nr, confine_days, rem_days, actual_rem_days, principal_pid)
                                                      VALUES (?,?,?,?,?,?,?,?,?)";

                            $ok = $db->Execute($confinementTrackerSql,array(
                                $data['pid'],
                                self::getEncounterDate($data['encounter']),
                                $bill_nr,
                                $objhcare['hcare_id'],
                                $insurance_no,
                                $data['ndays'],
                                $data['actual_rem_days'] ? 0 : $data['rem_days'],
                                $data['actual_rem_days'],
                                $principal_pid
                            ));
                            
                            // }
                        }
                        
                        if($ok) {
                            $refno = 'T'.$data['encounter'];
                            if(!$this->hasDoctorCoverage($bill_nr)){
                                $pf_sql = "UPDATE seg_billing_pf SET bill_nr =".$db->qstr($bill_nr)." WHERE bill_nr =".$db->qstr($refno);
                                $pf_sql2 = "UPDATE seg_billing_pf_breakdown SET bill_nr =".$db->qstr($bill_nr)." WHERE bill_nr =".$db->qstr($refno);
                                $db->Execute($pf_sql2);
                                if($ok = $db->Execute($pf_sql))
                                    $ok = true;
                                else
                                    $ok = false; 
                            }else{
                            $this->clearDoctorCoverage($refno);
                                 $ok = true;   
                            }
                        }

                        if($ok){
                            $ok = true;
                        }else{
                            $ok = false;
                            $this->error_msg =  "ERRROR: ".$pf_sql." == ".$billcover_sql.' == '.$confinementTrackerSql;
                        }
                    }

                    // Added by Gervie 04/14/2016
                    // For PHIC TEMP Transaction Monitoring
                    $temp_sql = "SELECT insurance_nr, member_type FROM seg_encounter_insurance_memberinfo WHERE encounter_nr = " . $db->qstr($data['encounter']);
                    $phic = $db->Execute($temp_sql)->FetchRow();

                    if($phic['insurance_nr'] == 'TEMP') {
                        $temp['encounter_nr'] = $data['encounter'];
                        $temp['bill_nr'] = $bill_nr;
                        $temp['member_type'] = $phic['member_type'];
                        
                        $this->savePHICTemp($temp);
                    }
                // }
            }

            if($ok){
                return true;
            }else{
                return false;
            }
    }

    /**
     * @author Nick B. Alcala 8-6-2015, transferred code from SOA.php to save the billing discounts to credit collection
     * @param $type
     * @param $encounterNr
     * @param $billNr
     * @param $discountAmount
     * @return bool
     */
    public static function saveDiscountToCreditCollection($type,$encounterNr,$billNr,$discountAmount){
        global $db;

        $nbb = array('NBB', 'HSM', 'KSMBHY', 'LM', 'SC');
        $infirmaryDependent = array('INFIRMARY','DEPENDENT');

        $userFullName = $db->GetOne("SELECT fn_get_personell_firstname_last(?)",$_SESSION['sess_temp_personell_nr']);
        if (in_array(strtoupper($type),$infirmaryDependent) || in_array(strtoupper($type),$nbb)) {

            if(in_array($type,$nbb))
                $type = 'nbb';

            $type = strtolower($type);

            $creditCollectionObj = new CreditCollection();
            $res = $creditCollectionObj->getTotalGrantsByTypeAndNr($type,$encounterNr);

            if ($res['total'] == '0.00' || $res['total'] === NULL) {
                $data = array(
                  'ref_no' => NULL,
                  'encounter_nr' => $encounterNr,
                  'bill_nr' => $billNr,
                  'entry_type' => 'debit',
                  'amount' => $discountAmount,
                  'pay_type' => $type,
                  'control_nr' => strtoupper($type),
                  'description' => strtoupper($type) .' Billing Discount',
                  'create_id' => $_SESSION['sess_temp_userid'],
                  'create_time' => date('YmdHis'),
                  'history' => strtoupper($type) . ' Billing Discount Added by ' . $userFullName . ' on ' . date('Y-m-d H:i:s A') . ' amount PHP ' . number_format($discountAmount,2)
                );
                $ok  = CreditCollection::insert($data);
                return $ok==true;
            }

        }
        return true;
    }

    function saveBillingDiscounts($data, $bill_nr, $final)
    {
        global $db;

        if (!empty($data['disc_id'])) {
            $sqlDiscount = "INSERT INTO seg_billing_discount (bill_nr, discountid, discount, discount_amnt) 
                            VALUES (" . $db->qstr($bill_nr) . ", " . $db->qstr($data['disc_id']) . ", " . $db->qstr($data['disc']) . ", " . $db->qstr($data['disc_amnt']) . ") ";
            $res = $db->Execute($sqlDiscount);

            if ($res) {
                $ok = true;
            } else {
                $ok = false;
                $this->error_msg = "ERROR: " . $db->ErrorMsg();
            }
        } else {
            $ok = true;
        }

        if ($ok) {
            $this->sql = "INSERT INTO seg_billingcomputed_discount (bill_nr, total_acc_discount, total_med_discount, total_sup_discount, 
                                                                  total_srv_discount, total_ops_discount, total_d1_discount, total_d2_discount, 
                                                                   total_d3_discount, total_d4_discount, hospital_income_discount,professional_income_discount) 
                                    VALUES (" . $db->qstr($bill_nr) . ", '0', '0', '0', '0', '0',
                                            " . $db->qstr($data['D1_discount']) . ",
                                            " . $db->qstr($data['D2_discount']) . ",
                                            " . $db->qstr($data['D3_discount']) . ",
                                            " . $db->qstr($data['D4_discount']) . ",
                                            " . $db->qstr($data['hcidiscount']) . ",
                                            '0')";
            //echo $this->sql;
            $res2 = $db->Execute($this->sql);
            if ($res2)
                $ok = true;
            else {
                $ok = false;
                $this->error_msg = "ERROR: " . $this->sql;
            }
        }

        //added by Nick 8-8-2015
        if($final){
            if(!self::saveDiscountToCreditCollection($data['disc_id_credit_collection'],$data['encounter'],$bill_nr,$data['disc_amnt_credit_collection'])){
                $ok = false;
                $this->error_msg = "ERROR: " . $db->ErrorMsg();
            }
        }

        if ($ok) {
            return true;
        } else {
            return false;
        }
    }

    function checkIfPHS($enc){
        $objEnc = new Encounter();

        $result = $objEnc->getEncounterInfo($enc);
        return ($result['discountid'] == "PHS");
    }

    function isPersonPrincipal($n_hcareid,$enc) {
        global $db;
        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select is_principal ".
                    "   from care_person_insurance as cpi inner join care_encounter as ce on cpi.pid = ce.pid ".
                    "   where (encounter_nr = '". $enc. "'".$filter.") and is_void = 0 ".
                    "      and hcare_id = ". $n_hcareid;

        if ($result = $db->Execute($strSQL))
            if ($result->RecordCount())
                while ($row = $result->FetchRow()) {
                    if ($row['is_principal'])
                        return true;
                    else
                        return false;
                }
    }

    /**
     * @author Gervie 04/16/2016
     */
    function getInsuranceNumber($enc){
        global $db;

        $ins = $db->GetOne("SELECT insurance_nr FROM seg_encounter_insurance_memberinfo WHERE encounter_nr = " . $db->qstr($enc));

        if($ins) {
            return $ins;
        }
        else {
            return false;
        }
    }

    function getPerHCareCoverage($enc){
        global $db;

        $this->hcare_coverage = array();
        $filter = '';

        if($this->prev_encounter != ''){
            $filter = " OR si.encounter_nr = '$this->prev_encounter'";}

        $this->sql = "SELECT DISTINCT ci.hcare_id, firm_id, name
                        FROM care_insurance_firm AS ci
                        WHERE EXISTS (
                            SELECT * FROM seg_encounter_insurance AS si
                            WHERE (si.encounter_nr = ".$db->qstr($enc)."".$filter.")
                            AND si.hcare_id = ci.hcare_id)";
        if($result = $db->Execute($this->sql))
            return $result;
        else
            return false;
    }

    function clearSaveData($bill_nr){
        global $db;
        $bill_nr = $db->qstr($bill_nr);
        $this->sql = "DELETE FROM seg_confinement_tracker WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        $this->sql = "DELETE FROM seg_billing_coverage WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        $this->sql = "DELETE FROM seg_billing_discount WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        $this->sql = "DELETE FROM seg_billingcomputed_discount WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        $this->sql = "DELETE FROM seg_billing_encounter_details WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);

        $this->sql = "DELETE FROM seg_billing_caserate WHERE bill_nr = ".$bill_nr;
        $db->Execute($this->sql);
        
        return true;
    }

    function setDeathDate($data, $deathdate = "0000-00-00 00:00:00", $user){
        global $db;
        if($data['encounter'] == '')
            $data['encounter'] = '0';
        $db->BeginTrans();

        $this->sql = "UPDATE care_person SET
                        death_date = DATE_FORMAT('$deathdate', '%Y-%m-%d'),
                        death_time = DATE_FORMAT('$deathdate', '%H:%i:%s'),
                        history = CONCAT(history, 'Update: ', NOW(), ' [$user]\\n'),
                        modify_id = '$user',
                        modify_time = NOW(),
                        death_encounter_nr = ".$db->qstr($data['encounter'])."
                        WHERE pid = ".$db->qstr($data['pid'])."";
        $success1 = $db->Execute($this->sql);

        if($success1){
            $fldarray = array('encounter_nr' => $db->qstr($data['encounter']),
                        'result_code' => '4',
                        'modify_id' => $db->qstr($user),
                        'modify_time' => 'NOW()',
                        'create_id' => $db->qstr($user),
                        'create_time' => 'NOW()');
            $success2 = $db->Replace('seg_encounter_result', $fldarray, array('encounter_nr'));
                }

        if(!$success1 || !$success2){
            $db->RollbackTrans();
            return $db->ErrorMsg();
              }
        else{
            $db->CommitTrans();
            return $success1;
            }
    }


    function updatebill($data, $bill_nr, $final) {
        global $db;

        $history = "Updated by " . $_SESSION['sess_user_name'] . " on " . date('Y-m-d H:i:s') . "\n";

        $sql = "UPDATE seg_billing_encounter 
                    SET bill_dte = ".$db->qstr($data['billdate']).",
                        bill_frmdte = ".$db->qstr($data['billdatefrom']).",
                        encounter_nr = ".$db->qstr($data['encounter']).",
                        accommodation_type = ".$db->qstr($data['accommodation_type']).",
                        total_acc_charge = ".$db->qstr($data['save_total_acc_charge']).", 
                        total_med_charge = ".$db->qstr($data['save_total_med_charge']).",
                        total_srv_charge = ".$db->qstr($data['save_total_srv_charge']).",
                        total_ops_charge = ".$db->qstr($data['save_total_ops_charge']).",
                        total_doc_charge = ".$db->qstr($data['save_total_doc_charge']).",
                        total_msc_charge = ".$db->qstr($data['save_total_msc_charge']).",
                        total_prevpayments = ".$db->qstr($data['save_total_prevpayment']).",
                        is_final = ".$db->qstr($final).",
                        modify_id = ".$db->qstr($_SESSION['sess_temp_userid']).",
                        modify_dt = NOW(),
                        history = CONCAT(history,". $db->qstr($history) ."),
                        opd_type = ".$db->qstr($data['encounter_type']).",
                        discount_type = ".$db->qstr($data['isInfirmaryOrDependent']).",
                        bill_time_started = ".$db->qstr($data['bill_time_started']).",
                        bill_time_ended = ".$db->qstr($data['bill_time_ended'])."
                        WHERE bill_nr = ".$db->qstr($bill_nr);

#var_dump($sql);
       if($this->result=$db->Execute($sql)) {
           $ok = true;
        }else{
            $ok = false; 
        }
         
        $this->current_enr = $data['encounter'];
        $ok1 = $this->clearSaveData($bill_nr);
        $ok2 = $this->saveBillingDiscounts($data, $bill_nr, $final);
        $ok3 = $this->saveBillingInsurance($data, $bill_nr,$final);
        $ok4 = $this->saveCaseRatePackage($data, $bill_nr);
        $ok5 = $this->setDischargeName($this->current_enr,$data['pid']);
        $ok6 = $this->dischargeWellBaby($data['encounter'],$final);

        if($final)
        $ok7 = $this->updateAccommodation($data['encounter'],$data['billdate']);

        $hasDeleted = $db->GetOne("SELECT * FROM
                                    (SELECT * FROM seg_billing_transactions sbt 
                                        WHERE sbt.`encounter_nr` = {$db->qstr($data['encounter'])}
                                        ORDER BY sbt.`action_date` DESC
                                    ) t WHERE t.action_taken = 'deleted' GROUP BY t.bill_nr DESC");

        if($hasDeleted){
            if($final == 1){
               $rebill = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], 3);

               if($rebill){
                    $ok7 = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], 1);
               }
            }
            else if($data['isPaywardSettlement'] == 1) {
                $ok7 = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], 4);
            }
            else{
                $ok7 = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], $final);
            }
        }
        else if($data['isPaywardSettlement'] == 1) {
            $ok7 = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], 4);
        }
        else{
            $ok7 = $this->saveBillingTransaction($bill_nr, $data['encounter'], $data['bill_time_started'], $final);
        }

        if($ok && $ok1 && $ok2 && $ok3 && $ok4 && $ok5 && $ok6 && $ok7){
            return true;
        }else{
            return false;
        }
    }


   function getbillnr($data) {
        global $db;

        $bill_nr = "";

        $strSQL = "SELECT bill_nr 
                    FROM seg_billing_encounter
                    WHERE ISNULL(is_deleted)
                    AND encounter_nr =". $db->qstr($data['encounter']);
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $bill_nr = $row['bill_nr'];
                            }
                        }

        return $bill_nr;
                        }
     function isMedicoLegal($enc) {
        global $db;
        $strSQL = "SELECT is_medico FROM care_encounter WHERE encounter_nr = ".$db->qstr($enc);
       
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $ismedico = ($row['is_medico']);
            }
        }
        if($ismedico==1)
        return true;
        else 
        return $ismedico;
    }

    #updated by: syboy 00/11/2015 - laterality, $data['laterality_first'], $data['laterality_second']
    function saveCaseRatePackage($data,$bill_nr){
        global $db;
        $ok = true; 
        if($data['first_rate_code']){
            $sql1 = "INSERT INTO seg_billing_caserate(bill_nr, package_id, laterality, rate_type, amount, hci_amount, pf_amount, saved_multiplier) 
                        VALUES('".$bill_nr."','".$data['first_rate_code']."','".$data['laterality_first']."','1','".$data['first_rate']."',
                               '".$data['first_hci']."','".$data['first_pf']."','".$data['first_multiplier']."')";
            if($result=$db->Execute($sql1)) 
            {
                $ok = true;
            } else { 
                $ok = false; 
                $this->error_msg = "ERROR: ".$sql1;
            } 
        }
        
        if($data['second_rate_code']){
            if($ok){
                $sql2 = "INSERT INTO seg_billing_caserate(bill_nr,package_id,laterality,rate_type,amount,hci_amount, pf_amount, saved_multiplier) 
                            VALUES('".$bill_nr."','".$data['second_rate_code']."','".$data['laterality_second']."','2','".$data['second_rate']."',
                                   '".$data['second_hci']."','".$data['second_pf']."','".$data['second_multiplier']."')";

                if($result=$db->Execute($sql2)) 
                {
                    $ok = true;
                } else { 
                    $ok = false; 
                    $this->error_msg = "ERROR: ".$sql2;
                }
            }
        }
        

        if ($ok) {
            return true;
        } else {
            return false;
        }

    }

    function hasSavedMultiplier($bill_nr,$rtype,$package_id){
        global $db;

        $this->sql = "SELECT *
                        FROM
                          seg_billing_caserate  
                        WHERE rate_type = ".$db->qstr($rtype)."
                          AND package_id = ".$db->qstr($package_id)."
                          AND bill_nr = ".$db->qstr($bill_nr);
                          
        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }
    }

    /**
     * Updated by Nick, 4/23/2014
     * Join with seg_case_rate_special
     */
    function hasSavedPackage($bill_nr,$rtype){
        global $db;

        $this->sql = "SELECT 
                          sbc.*,
                          scrs.`sp_package_id` 
                        FROM
                          seg_billing_caserate AS sbc 
                          LEFT JOIN seg_case_rate_special AS scrs
                            ON sbc.`package_id` = scrs.`sp_package_id` 
                        WHERE rate_type = $rtype
                          AND bill_nr = ".$db->qstr($bill_nr);
                          
        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }

    }

    function delSavedPackage($bill_nr,$rType){
        global $db;

        $this->sql = "DELETE FROM seg_billing_caserate WHERE rate_type=$rType AND bill_nr=".$db->qstr($bill_nr);

        if ($buf=$db->Execute($this->sql)){
            if($buf->RecordCount()) {
                return $buf->FetchRow();
            }else { return FALSE; }
        }else { return FALSE; }

    }


    function saveRefNo($data) // Edited by James 1/7/2014
    {
        global $db;

        $index = "bill_nr, refno, ref_area";
        $this->sql="INSERT INTO seg_billing_encounter_details ($index) VALUES $data";
        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) 
                return TRUE;
            else
                return FALSE;
        }else
            return FALSE;
        
    }

    //Added by EJ 11/20/2014
    function checkMembership($enc) {
        global $db;
        $enc = $db->qstr($enc);
        $this->sql = "SELECT memcategory_id FROM seg_encounter_memcategory WHERE encounter_nr = $enc";

        $result = $db->Execute($this->sql);
        if($result && $row = $result->FetchRow()) {
            if ($row['memcategory_id']) {
                return $row['memcategory_id'];
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }


    function getEncounterDte() 
    {
        global $db;

        $strSQL = "select encounter_date " .
                            "   from care_encounter " .
                            "   where (encounter_nr = '". $this->prev_encounter ."')
                                order by encounter_date limit 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $enc_dte = strftime("%Y-%m-%d %H:%M", strtotime($row['encounter_date'])).":00";
            }
        }

        return($enc_dte);
    }


    function isFinal()
    {
        global $db;

        $strSQL = "SELECT is_final FROM seg_billing_encounter WHERE bill_nr = ".$db->qstr($this->old_bill_nr);

        if ($result=$db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    if ($row["is_final"]) {
                        return TRUE;
                    }else{return FALSE;}
                }else{return FALSE;}
            }else{return FALSE;}
        }else{return FALSE;}

    }

    //added by ken 1/4/2013
    function checkInsuranceRequest($enc){
        global $db;

        $this->sql = "SELECT hcare_id FROM seg_encounter_insurance WHERE encounter_nr = '".$enc."' AND hcare_id = '18'";

        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }

    function checkAdmittingDiag($enc){
        global $db;

        $this->sql = "SELECT er_opd_diagnosis FROM care_encounter WHERE !(er_opd_diagnosis IS NULL OR er_opd_diagnosis = '') AND encounter_nr = ".$db->qstr($enc)." ";
        if ($this->result=$db->Execute($this->sql))
         {
             return $this->result;
        }else {return false;}
    }

    function checkFinalDiag($enc){
        global $db;

        $this->sql = "SELECT final_diagnosis FROM seg_soa_diagnosis WHERE !(final_diagnosis IS NULL OR final_diagnosis = '') AND encounter_nr = ".$db->qstr($enc)." ";
        if ($this->result=$db->Execute($this->sql))
         {
             return $this->result;
        }else {return false;}
    }

    function checkEditFinalDiag($enc){
        global $db;

        $this->sql = "SELECT final_diagnosis FROM seg_soa_diagnosis_new WHERE !(final_diagnosis IS NULL OR final_diagnosis = '') AND encounter_nr = ".$db->qstr($enc)." ";
        if ($this->result=$db->Execute($this->sql))
         {
             return $this->result;
        }else {return false;}
    }

    function checkOtherDiag($enc){
        global $db;

        $this->sql = "SELECT other_diagnosis FROM seg_soa_diagnosis WHERE !(other_diagnosis IS NULL OR other_diagnosis = '') AND encounter_nr = ".$db->qstr($enc)." ";
        if ($this->result=$db->Execute($this->sql))
         {
             return $this->result;
        }else {return false;}
    }

    function isPayward($enc){
        global $db;

        $this->sql = "SELECT ce.encounter_nr, ce.`current_ward_nr`,cw.accomodation_type
                        FROM care_encounter AS ce
                        INNER JOIN care_ward AS cw ON ce.current_ward_nr = cw.nr
                        WHERE ce.encounter_nr = ".$db->qstr($enc)." AND cw.`accomodation_type` = '2'
                        UNION
                        SELECT sela.encounter_nr, sela.group_nr, cw.accomodation_type
                        FROM seg_encounter_location_addtl AS sela
                        INNER JOIN care_ward AS cw ON sela.group_nr = cw.nr 
                        WHERE sela.encounter_nr = ".$db->qstr($enc)." AND cw.`accomodation_type` = '2' AND sela.is_deleted != '1'";

        if($this->result=$db->Execute($this->sql)) {
            return $this->result;
        } else { return false; }
    }

    function getRoomRate($data){
        global $db;

        $this->sql = "SELECT ctr.room_rate 
                        FROM care_room AS cr 
                        INNER JOIN care_type_room AS ctr ON cr.`type_nr` = ctr.`nr` 
                        WHERE cr.`ward_nr` = '".$data['ward_nr']."' AND cr.`nr` = '".$data['room_nr']."' ";

        if($this->result=$db->Execute($this->sql)) {
            if($row = $this->result->FetchRow())
                return $row['room_rate'];
        } else { return false; }
    }
        //ended by ken

 
    /**
    * Created By Pol 01/04/2014
    * Update by Jarel 03/05/2014
    * Add Codition for special cases 
    * And Get pid if Empty.
    * Updated by Gervie 03/16/2016
    * Changed condition of SPC rule from bill_date to encounter_date.
    */
    function GetPreviousPackage($encnr,$pid='') {
        global $db;

        $rs = $db->Execute("SELECT pid,encounter_date FROM care_encounter WHERE encounter_nr = ".$db->qstr($encnr))->FetchRow();

        if($pid=='')
            $pid = $rs['pid'];

        $encounter_date = $rs['encounter_date'];

        $SQLstr = ("SELECT scrp.`description`,
                     scrp.`code`,
                     scrp.`package`,
                     ce.`encounter_nr`,
                     DATE_FORMAT(ce.`encounter_date`,'%M %e %Y %r') AS DateAdmitted,
                     DATE_FORMAT(ce.`mgh_setdte`,'%M %e %Y %r') AS DateDischarged,
                     DATEDIFF(DATE_FORMAT('$encounter_date','%Y-%m-%d %T'),DATE_FORMAT(ce.`encounter_date`,'%Y-%m-%d %T')) AS daydifferent,
                     sca.acr_groupid,
                     smod.`laterality`
                    FROM care_encounter `ce`
                    INNER JOIN seg_billing_encounter `sbe`
                    ON ce.`encounter_nr` = sbe.`encounter_nr`
                        AND sbe.`is_deleted` IS NULL AND sbe.`is_final` = '1'
                    INNER JOIN seg_billing_caserate `sbc`
                    ON sbe.`bill_nr` = sbc.`bill_nr`
                    INNER JOIN seg_case_rate_packages `scrp`
                    ON scrp.`code` = sbc.`package_id` AND scrp.`special_case` = '0' AND (DATE(ce.`encounter_date`) BETWEEN scrp.`date_from` AND scrp.`date_to`)
                    LEFT JOIN seg_caserate_acr `sca`
                       ON sca.package_id = sbc.`package_id`
                    INNER JOIN care_person `cp`
                    ON cp.`pid` = ce.`pid`
                    LEFT JOIN seg_misc_ops `smo` ON ce.`encounter_nr` = smo.`encounter_nr`
                    LEFT JOIN seg_misc_ops_details `smod` ON smo.`refno` = smod.`refno` AND smod.`ops_code` = sbc.`package_id`
                    WHERE ce.`pid` ='".$pid."'
                    AND ce.`encounter_nr` <>'".$encnr."'
                    AND DATEDIFF(DATE_FORMAT('$encounter_date','%Y-%m-%d %T'),DATE_FORMAT(ce.`encounter_date`,'%Y-%m-%d %T')) <= '90'");
       #echo $SQLstr;
        if ($result = $db->Execute($SQLstr)) {
            if ($result->RecordCount()) {
                return $result;
            } else {
                return false;
            }
        }
    }

    function getEncounterRVS($encnr,$within_days=0,$current=0) {
        global $db;

        $rs = $db->Execute("SELECT pid,encounter_date FROM care_encounter WHERE encounter_nr = ".$db->qstr($encnr))->FetchRow();

        if($pid=='') $pid = $rs['pid'];

        $encounter_date = $rs['encounter_date'];

        $filter_days="";
        if($within_days>0){
            $filter_days=" AND DATEDIFF(DATE_FORMAT('$encounter_date','%Y-%m-%d %T'),DATE_FORMAT(ce.`encounter_date`,'%Y-%m-%d %T')) <= $within_days";
        }

        // $after_encounter="";
        // if($within_days>0){
            $after_encounter=" INNER JOIN seg_billing_caserate sbca ON sbe.`bill_nr`=sbca.`bill_nr` 
            /*AND IF(smod.`ops_code` IS NULL,TRUE,smod.`ops_code` = sbca.`package_id`)*/
            INNER JOIN seg_case_rate_packages `scrp` 
                ON scrp.`code` = sbca.`package_id` 
                AND scrp.`special_case` = '0' 
                AND (
                  DATE(ce.`encounter_date`) BETWEEN scrp.`date_from` 
                  AND scrp.`date_to`
                )  ";
        // }

        if($current==1){
            $SQLstr = ("SELECT 
                      smod.`ops_code`,
                      smod.`laterality`,srd.`is_cataract`,srd.`is_exempt_90`,srd.`is_exempt_180`,srd.`is_once`,srd.`required_rvs`
                    FROM
                      care_encounter `ce` 
                      LEFT JOIN seg_misc_ops `smo` 
                        ON ce.`encounter_nr` = smo.`encounter_nr` 
                      LEFT JOIN seg_misc_ops_details `smod` 
                        ON smo.`refno` = smod.`refno`
                      LEFT JOIN `seg_rvs_details` srd
                        ON srd.`rvs_code`=smod.`ops_code`
                    WHERE ce.`encounter_nr` = ".$db->qstr($encnr));
        }elseif($current==2){
            $SQLstr = ("SELECT sed.code AS rvs_code FROM `seg_encounter_diagnosis` sed WHERE sed.`is_deleted`=0 AND sed.`encounter_nr`=".$db->qstr($encnr));
        }else{
            $SQLstr = ("SELECT 
                          scrp.`code` AS ops_code,
                          smod.`laterality`,
                          ce.encounter_nr,
                          smod.op_date 
                        FROM
                          care_encounter `ce` 
                          INNER JOIN seg_billing_encounter `sbe` 
                            ON ce.`encounter_nr` = sbe.`encounter_nr` 
                            AND sbe.`is_deleted` IS NULL 
                            AND sbe.`is_final` = '1' 
                          INNER JOIN seg_billing_caserate `sbc` 
                            ON sbe.`bill_nr` = sbc.`bill_nr` 
                          INNER JOIN seg_case_rate_packages `scrp` 
                            ON scrp.`code` = sbc.`package_id` 
                            AND scrp.`special_case` = '0' 
                            AND (
                              DATE(ce.`encounter_date`) BETWEEN scrp.`date_from` 
                              AND scrp.`date_to`
                            ) 
                          LEFT JOIN seg_caserate_acr `sca` 
                            ON sca.package_id = sbc.`package_id` 
                          INNER JOIN care_person `cp` 
                            ON cp.`pid` = ce.`pid` 
                          LEFT JOIN seg_misc_ops `smo` 
                            ON ce.`encounter_nr` = smo.`encounter_nr` 
                          LEFT JOIN seg_misc_ops_details `smod` 
                            ON smo.`refno` = smod.`refno` 
                            AND smod.`ops_code` = sbc.`package_id` 
                        WHERE  ce.`pid` = '".$pid."'
                        AND ce.`encounter_nr` <> '".$encnr."'".$filter_days);
        }
        
        if ($result = $db->Execute($SQLstr)) {
            if ($result->RecordCount()) {
                return $result;
            } else {
                return false;
            }
        }
    }

    function isEncAfterDate($enc,$date="0000-00-00"){
        global $db;

        $strSQL="SELECT IF(DATE(encounter_date)>=".$db->qstr($date).",1,0) after_date FROM care_encounter WHERE encounter_nr = ".$db->qstr($enc);

        if($result = $db->GetRow($strSQL)){
            return ($result['after_date']==1)?true:false;
        }
        else{
            return false;
        }
    }

    function getRVSGroup($rvs_group=0){
        global $db;
        switch ($rvs_group) {
            case 0:
                $strSQL="SELECT GROUP_CONCAT(srd.rvs_code) AS rvs_code FROM seg_rvs_details srd WHERE srd.is_once='1'";
                break;
            case 1:
                $strSQL="SELECT GROUP_CONCAT(srd.rvs_code) AS rvs_code FROM seg_rvs_details srd WHERE srd.`is_cataract`='1'";
                break;
            case 2:
                $strSQL="SELECT GROUP_CONCAT(srd.rvs_code) AS rvs_code FROM seg_rvs_details srd WHERE srd.`is_exempt_90`='1'";
                break;
            case 3:
                $strSQL="SELECT GROUP_CONCAT(srd.rvs_code) AS rvs_code FROM seg_rvs_details srd WHERE srd.`is_exempt_180`='1'";
                break;
            case 4:
                $strSQL="SELECT GROUP_CONCAT(srd.rvs_code) AS rvs_code FROM seg_rvs_details srd WHERE srd.`is_vitrectomy`='1'";
                break;
            case 5:
                $strSQL="SELECT GROUP_CONCAT(srd.rvs_code) AS rvs_code FROM seg_rvs_details srd WHERE srd.`required_rvs` IS NOT NULL";
                break;
            default:
                $strSQL="SELECT GROUP_CONCAT(srd.rvs_code) AS rvs_code FROM seg_rvs_details srd WHERE srd.is_once='1'";
                break;
        }
        if($result = $db->GetRow($strSQL)){
            return explode(",",$result['rvs_code']);
        }
        else{
            return false;
        }
    }

    function getCaserateGroup($group){
        global $db;

        $strSQL = "SELECT GROUP_CONCAT(scrp.code) AS code FROM seg_case_rate_packages scrp WHERE scrp.`group` =".$db->qstr($group);

        if($result = $db->GetRow($strSQL)){
            return explode(",",$result['code']);
        }
        else{
            return false;
        }
    }


    function isClearedForRVS($rvs,$lat,$enc){
        $ONCE_A_LIFETIME_RVS = $this->getRVSGroup(0);
        $CATARACT_REMOVAL_RVS = $this->getRVSGroup(1);
        $VITRECTOMY_RVS = $this->getRVSGroup(4);
        $IS_EXEMPTED_90DAYS = $this->getRVSGroup(2);
        $IS_EXEMPTED_180DAYS = $this->getRVSGroup(3);
        $HAS_REQUIREMENT_RVS = $this->getRVSGroup(5);
        // $CURRENT_ENCOUNTER_RVS_DETAILS = $this->getEncounterRVS($enc,0,1);
        // $CURRENT_ENCOUNTER_DIAGNOSIS = $this->getEncounterRVS($enc,0,2);
        $PREVIOUS_ENCOUNTER_RVS_90DAYS=$this->getEncounterRVS($enc,90);
        $PREVIOUS_ENCOUNTER_RVS_180DAYS=$this->getEncounterRVS($enc,179);
        $PREVIOUS_ENCOUNTER_RVS=$this->getEncounterRVS($enc,0,0);
        $IS_AFTER_EFFECTIVITY_DATE_FOR_ONCE_IN_A_LIFETIME_RVS=$this->isEncAfterDate($enc,"2016-07-01");
        $IS_AFTER_EFFECTIVITY_DATE_FOR_CATARACT_RVS=$this->isEncAfterDate($enc,"2015-06-01");
        $IS_AFTER_EFFECTIVITY_DATE_FOR_REIMBURSEMENT_RVS=$this->isEncAfterDate($enc,"2015-07-15");
        $IS_AFTER_EFFECTIVITY_DATE_FOR_EXEMPTED_RVS=$this->isEncAfterDate($enc,"2015-07-15");

        $rvs_go=true;

        if($PREVIOUS_ENCOUNTER_RVS){
            if(in_array($rvs, $CATARACT_REMOVAL_RVS) && $IS_AFTER_EFFECTIVITY_DATE_FOR_CATARACT_RVS){
                foreach($PREVIOUS_ENCOUNTER_RVS as $key => $procedure){
                        if(in_array($procedure['ops_code'], $CATARACT_REMOVAL_RVS) && ($lat==$procedure['laterality'] || $procedure['laterality']=='B' || $lat=='B')) {
                            $rvs_go = false;
                        }
                    }
            }elseif(in_array($rvs, $ONCE_A_LIFETIME_RVS) && $IS_AFTER_EFFECTIVITY_DATE_FOR_ONCE_IN_A_LIFETIME_RVS){
                foreach($PREVIOUS_ENCOUNTER_RVS as $key => $procedure){
                    if(in_array($procedure['ops_code'], $ONCE_A_LIFETIME_RVS) && ($lat==$procedure['laterality'] || $procedure['laterality']=='B' || $lat=='B')) {
                        $rvs_go = false;
                    }
                }
            }else{
                if($PREVIOUS_ENCOUNTER_RVS_90DAYS){
                    foreach($PREVIOUS_ENCOUNTER_RVS_90DAYS as $key => $procedure){
                        $rvs_details = $this->caseRateInfo($procedure['ops_code']);
                        $row = $rvs_details->FetchRow();

                        $CURRENT_ENCOUNTER_GROUP_RVS = array();

                        if($row['group'])
                            $CURRENT_ENCOUNTER_GROUP_RVS = $this->getCaserateGroup($row['group']);

                        if(($procedure['ops_code']==$rvs) && ($lat==$procedure['laterality'] || $procedure['laterality']=='B' || $lat=='B')) {
                            $rvs_go = false;
                        }elseif(in_array($rvs, $CURRENT_ENCOUNTER_GROUP_RVS)){
                            $rvs_go = false;
                        }
                    }

                }
                
            }
        }
        return $rvs_go;
    }
    function isClearedForRVS_Details($rvs,$lat,$enc){
        $ONCE_A_LIFETIME_RVS = $this->getRVSGroup(0);
        $CATARACT_REMOVAL_RVS = $this->getRVSGroup(1);
        $VITRECTOMY_RVS = $this->getRVSGroup(4);
        $IS_EXEMPTED_90DAYS = $this->getRVSGroup(2);
        $IS_EXEMPTED_180DAYS = $this->getRVSGroup(3);
        $HAS_REQUIREMENT_RVS = $this->getRVSGroup(5);
        // $CURRENT_ENCOUNTER_RVS_DETAILS = $this->getEncounterRVS($enc,0,1);
        // $CURRENT_ENCOUNTER_DIAGNOSIS = $this->getEncounterRVS($enc,0,2);
        $PREVIOUS_ENCOUNTER_RVS_90DAYS=$this->getEncounterRVS($enc,90);
        $PREVIOUS_ENCOUNTER_RVS_180DAYS=$this->getEncounterRVS($enc,179);
        $PREVIOUS_ENCOUNTER_RVS=$this->getEncounterRVS($enc,0,0);
        $IS_AFTER_EFFECTIVITY_DATE_FOR_ONCE_IN_A_LIFETIME_RVS=$this->isEncAfterDate($enc,"2016-07-01");
        $IS_AFTER_EFFECTIVITY_DATE_FOR_CATARACT_RVS=$this->isEncAfterDate($enc,"2015-06-01");
        $IS_AFTER_EFFECTIVITY_DATE_FOR_REIMBURSEMENT_RVS=$this->isEncAfterDate($enc,"2015-07-15");
        $IS_AFTER_EFFECTIVITY_DATE_FOR_EXEMPTED_RVS=$this->isEncAfterDate($enc,"2015-07-15");

        $rvs_go=null;

        if($PREVIOUS_ENCOUNTER_RVS){
            if(in_array($rvs, $CATARACT_REMOVAL_RVS) && $IS_AFTER_EFFECTIVITY_DATE_FOR_CATARACT_RVS){
                foreach($PREVIOUS_ENCOUNTER_RVS as $key => $procedure){
                        if(in_array($procedure['ops_code'], $CATARACT_REMOVAL_RVS) && ($lat==$procedure['laterality'] || $procedure['laterality']=='B' || $lat=='B')) {
                            $rvs_go = array($procedure['ops_code'],$procedure['laterality'],$procedure['encounter_nr'],$procedure['op_date']);
                        }
                    }
            }elseif(in_array($rvs, $ONCE_A_LIFETIME_RVS) && $IS_AFTER_EFFECTIVITY_DATE_FOR_ONCE_IN_A_LIFETIME_RVS){
                foreach($PREVIOUS_ENCOUNTER_RVS as $key => $procedure){
                    if(in_array($procedure['ops_code'], $ONCE_A_LIFETIME_RVS) && ($lat==$procedure['laterality'] || $procedure['laterality']=='B' || $lat=='B')) {
                        $rvs_go = array($procedure['ops_code'],$procedure['laterality'],$procedure['encounter_nr'],$procedure['op_date']);
                    }
                }
            }
            // else{
            //     if($PREVIOUS_ENCOUNTER_RVS_90DAYS){
            //         foreach($PREVIOUS_ENCOUNTER_RVS_90DAYS as $key => $procedure){
            //             if($procedure['ops_code']==$rvs && ($lat==$procedure['laterality'] || $procedure['laterality']=='B' || $lat=='B')) {
            //                 $rvs_go = array($procedure['ops_code'],$procedure['laterality'],$procedure['encounter_nr'],$procedure['op_date']);
            //             }
            //         }
            //     }
            // }
        }
        return $rvs_go;
    }

     function IsChartyName($charity) {
        if ($charity == '') {
            $this->AccommodationType();
        }
        return (!(strpos(strtoupper($this->accomm_typ_desc), CHARITY, 0) === false));
    }

   

    function AccommodationType($enc, $bill_date, $bill_from, $prev_encounter) {
        global $db;
        $sname = '';
        $filter = array('','');
        if ($prev_encounter != '') {
            $filter[0] = " or cel.encounter_nr = '$prev_encounter'";
            $filter[1] = " or sel.encounter_nr = '$prev_encounter'";
        }
            $strSQL = "select 0 AS entry_no,
                  STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') occupy_date,
                  cw.accomodation_type, accomodation_name, cw.name AS ward_name ".
                    "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
                    "      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr) ".
                    "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr ".
                    "   where (cel.encounter_nr = '". $enc. "'".$filter[0].") ".
                    "      and exists (select nr ".
                    "                     from care_type_location as ctl ".
                    "                     where upper(type) = 'WARD' and ctl.nr = cel.type_nr) ".
                    "      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $bill_from . "' ".
                    "      and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') ".
                    "         or ".
                    "      (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $bill_from . "' ".
                    "      and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') ".
                    "      or ".
                    "      str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
          " UNION ALL
            SELECT entry_no, occupy_date, cw.accomodation_type, accomodation_name, cw.name AS ward_name
              FROM (seg_encounter_location_addtl sel INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr)
                INNER JOIN seg_accomodation_type sat ON cw.accomodation_type = sat.accomodation_nr
            WHERE (sel.encounter_nr = '". $enc. "'".$filter[1].")
              AND (
                STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) >= '" . $bill_from . "'
                AND STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) < '" . $bill_date . "'
              )
            ORDER BY entry_no DESC LIMIT 1";

        $this->debugSQL = $strSQL;
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $sname = $row['accomodation_name'];
                }
            }
        }

        return ($sname);

    }
    
    function isDialysisPatient($enc) {
        global $db;

        $enc_type = 0;
        $strSQL = "SELECT encounter_type
                    FROM care_encounter
                     WHERE encounter_nr = '$enc'";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = $row['encounter_type'];
            }
        }

        return ($enc_type == DIALYSIS_PATIENT);
    }
    //end by pol 01/04/2014

    #-------------------------------------------------
    function getAccomodationDesc(){
        return $this->accomm_typ_desc;
    }

    function getMemCategoryDesc() {
        global $db;
        $s_desc= "";
        $filter = '';
        if ($this->prev_encounter_nr != '') $filter = " or sem.encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "SELECT 
                      memcategory_desc,
                      sm.memcategory_id,
                      sei.modify_id,
                      sei.modify_dt 
                    FROM
                      seg_memcategory AS sm 
                      INNER JOIN seg_encounter_memcategory AS sem 
                        ON sm.memcategory_id = sem.memcategory_id 
                      INNER JOIN seg_encounter_insurance AS sei 
                        ON sem.encounter_nr = sei.encounter_nr 
                    WHERE sem.encounter_nr = " . $db->qstr($this->current_enr) . $filter; //sql updated by Nick 05-12-2014 - Tidy up + modify info

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $s_desc = $row['memcategory_desc'];
                    $this->memcategory_id = $row['memcategory_id'];
                    $this->memCatHist = array(
                                                'modify_id' => $row['modify_id'],
                                                'modify_dt' => $row['modify_dt']
                                             );//added by Nick 05-12-2014
                }
            }
        }
        return $s_desc;
    }


    function getMemCategoryID()
    {
        return $this->memcategory_id;
    }

    function getClassificationDesc($enc, $bill_dte, $charity='', $IsEr='') {
            global $db;

            $s_desc= "";
            $prev = "";

        if ($charity || $IsEr) {
          $filter = '';
          $sql = "SELECT parent_encounter_nr 
                    FROM care_encounter 
                    WHERE encounter_nr = ".$db->qstr($enc);

          if ($result1 = $db->Execute($sql)){
            if ($result1->RecordCount()){
                while($row1 = $result1->FetchRow()){
                    $prev = $row1['parent_encounter_nr'];
                }
            }
          }
          if ($prev != '') $filter = " or scg.encounter_nr = ".$db->qstr($enc);

          $strSQL = "select discountdesc ".
                "   from seg_discount as sd inner join seg_charity_grants as scg on sd.discountid = scg.discountid ".
                "   where (scg.encounter_nr = '". $db->qstr($enc). "'".$filter.") ".
                "      and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '" . $db->qstr($bill_dte) . "' " .
                "   order by grant_dte desc limit 1";

          if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
              while ($row = $result->FetchRow()) {
                $s_desc = $row['discountdesc'];
              }
            }
          }
        }
            return($s_desc);
        }

    function getCaseTypeID($enc, $bill_date, $prevenc ='') {
        global $db;
        $sdesc = '';
        $filter = '';
        if ($prevenc != '') $filter = " or encounter_nr = ".$db->qstr($prevenc);
        $strSQL = "select sec.casetype_id  ".
                    "   from seg_encounter_case as sec inner join seg_type_case as stc ".
                    "      on sec.casetype_id = stc.casetype_id ".
                    "   where (encounter_nr = ". $db->qstr($enc). "".$filter.") ".
                    "      and str_to_date(sec.modify_dt, '%Y-%m-%d %H:%i:%s') < ".$db->qstr($bill_date)."".
                    "      and !sec.is_deleted ".
                    "   order by sec.create_dt desc limit 1";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $sdesc = $row['casetype_id'];
                }
            }
        }
        return($sdesc);
    }

    //added by jasper 07/12/2013 - FIX FOR MS-728 TO ACCOMMODATE NEW ROOM RATES BASED ON CASE TYPE FROM HOSPITAL ADMINISTRATIVE ORDER NO, 18 s.2013
    function getRoomRateByCaseType($casetypeid = '', $warddesc = '') {
        global $db;

        $strSQL = "";
        if (!(strpos(strtoupper($warddesc), SERVICEWARD, 0) === false) && (strpos(strtoupper($warddesc), ICUWARD, 0) === false) || (!strpos(strtoupper($warddesc), OBANNEX, 0) === false)) {
            $strSQL = "SELECT service_ward_roomrate AS room_rate FROM seg_confinementtype_room_rate WHERE confinetype_id = " . $casetypeid;
        }
        else if (!(strpos(strtoupper($warddesc), ANNEXWARD, 0) === false)) {
            $strSQL = "SELECT annex_roomrate AS room_rate FROM seg_confinementtype_room_rate WHERE confinetype_id = " . $casetypeid;
        }

        if ($strSQL<>"") {
            if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    $row = $result->FetchRow();
                    $rm_rate = $row['room_rate'];
                }
            }
        } else {
            $rm_rate = 0;
        }
        return $rm_rate;
    }

    function getActualAdmissionDate(){
        global $db;

        $admit_dte = "0000-00-00 00:00:00";
        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
        $sql = $db->Prepare("SELECT
                                  admission_dt
                                FROM
                                  care_encounter
                                WHERE (encounter_nr = ? $filter)
                                  AND admission_dt IS NOT NULL
                                ORDER BY encounter_date
                                LIMIT 1 ;");

        if ($result = $db->Execute($sql,$this->encounter_nr)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $admit_dte = strftime("%Y-%m-%d %H:%M", strtotime($row['admission_dt'])).":00";
            }
        }

        return($admit_dte);
    }

    function getActualLastBillDte() {
        global $db;

        $lastbill_dte = "0000-00-00 00:00:00";
        $filter = '';

        if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select bill_dte " .
                    "   from seg_billing_encounter " .
                    "   where (encounter_nr = '". $this->current_enr ."'".$filter.") " .
                    "      and str_to_date(bill_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' and is_deleted IS NULL ".
                    "   order by bill_dte desc limit 1";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $lastbill_dte = $row['bill_dte'];
            }
        }

        return($lastbill_dte);
    }

    function getOBAnnexPayment() {
        global $db;
        $this->ob_payments = array();
        $total_payment = 0;
        $strSQL = "SELECT sp.or_no, sp.or_date, spr.amount_due AS ob_amt FROM seg_pay AS sp " .
                   "INNER JOIN seg_pay_request AS spr ON sp.or_no = spr.or_no  " .
                  "WHERE sp.encounter_nr = '" . $this->current_enr . "' " .
                  "AND sp.cancel_date is null AND spr.service_code = 'OBANNEX'";
    // echo $strSQL;
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $objpay = new Payment;
                    $objpay->setORNo($row['or_no']);
                    $objpay->setORDate($row['or_date']);
                    $objpay->setAmountPaid($row['ob_amt']);
                    $this->ob_payments[] = $objpay;
                    $total_payment += $row['ob_amt']; 
                }
            }
        }
        $this->total_ob_payments = $total_payment;
        return $total_payment;
    }

    //added by art 01/05/2014
    function getPrevConfinement($year){
        global $db;

        $insurance_no = $this->getInsuranceNumber($this->encounter_nr);
        $pid = $db->GetOne("SELECT pid FROM care_encounter WHERE encounter_nr = ".$db->qstr($this->encounter_nr));
        $isPrincipal = $this->isPersonPrincipal(18, $this->encounter_nr);

        if($isPrincipal)
        {
            $this->sql = "SELECT SUM(t_days) AS tdays 
                            FROM
                                (SELECT SUM(confine_days) AS t_days 
                                    FROM seg_confinement_tracker sct 
                                    WHERE sct.`insurance_nr` = ".$db->qstr($insurance_no)." 
                                    AND sct.`current_year` = ".$db->qstr($year)." 
                                    AND sct.`hcare_id` = 18
                                    AND sct.`principal_pid` = ".$db->qstr($pid)." 
                                 UNION
                                 ALL 
                                 SELECT SUM(confine_days) AS t_days 
                                    FROM seg_confinement_tracker sct 
                                    WHERE sct.`pid` = ".$db->qstr($pid)."
                                    AND sct.`current_year` = ".$db->qstr($year)." 
                                    AND sct.`hcare_id` = 18 
                                    AND (sct.`insurance_nr` IS NULL OR sct.`insurance_nr` = '0')
                                ) t";
        }
        else
        {
            $this->sql = "SELECT SUM(t_days) AS tdays 
                            FROM
                                (SELECT SUM(confine_days) AS t_days 
                                    FROM seg_confinement_tracker sct 
                                    WHERE sct.`insurance_nr` = ".$db->qstr($insurance_no)." 
                                    AND sct.`current_year` = ".$db->qstr($year)." 
                                    AND sct.`hcare_id` = 18
                                    AND sct.`principal_pid` = '' 
                                 UNION
                                 ALL 
                                 SELECT SUM(confine_days) AS t_days 
                                    FROM seg_confinement_tracker sct 
                                    WHERE sct.`pid` = ".$db->qstr($pid)."
                                    AND sct.`current_year` = ".$db->qstr($year)." 
                                    AND sct.`hcare_id` = 18 
                                    AND (sct.`insurance_nr` IS NULL OR sct.`insurance_nr` = '0')
                                ) t";
        }

        if($result = $db->Execute($this->sql)){
            if ($result->RecordCount()) {
               $row = $result->FetchRow();
                return $days = $row['tdays'];   
            }else{ return FALSE; }
        }else { return FALSE; }
    }

    function getPrevConfinementByInsurance($insurance_no,$pid,$isPrincipal,$year){
        global $db;
        if($insurance_no==''){
            if($isPrincipal){
                $this->sql = "SELECT SUM(confine_days) AS t_days 
                                        FROM seg_confinement_tracker sct 
                                        WHERE sct.`insurance_nr` = '0' 
                                        AND sct.`pid` = ".$db->qstr($pid)."
                                        AND sct.`current_year` = ".$db->qstr($year)."
                                        AND sct.`hcare_id` = 18";
            }
            else{
                $this->sql = "SELECT SUM(confine_days) AS t_days 
                                        FROM seg_confinement_tracker sct 
                                        WHERE sct.`insurance_nr` = '0' 
                                        AND sct.`pid` = ".$db->qstr($pid)."
                                        AND sct.`current_year` = ".$db->qstr($year)."
                                        AND sct.`hcare_id` = 18
                                        AND sct.`principal_pid` = ''";
            }
        }
        else{
            if($isPrincipal){
                $this->sql = "SELECT SUM(confine_days) AS t_days 
                                        FROM seg_confinement_tracker sct 
                                        WHERE sct.`insurance_nr` = ".$db->qstr($insurance_no)." 
                                        AND sct.`current_year` = ".$db->qstr($year)."
                                        AND sct.`hcare_id` = 18";
            }
            else{
                $this->sql = "SELECT SUM(confine_days) AS t_days 
                                        FROM seg_confinement_tracker sct 
                                        WHERE sct.`insurance_nr` = ".$db->qstr($insurance_no)." 
                                        AND sct.`current_year` = ".$db->qstr($year)." 
                                        AND sct.`hcare_id` = 18
                                        AND sct.`principal_pid` = ''";
            }
        }
        

        if($result = $db->Execute($this->sql)){
            if ($result->RecordCount()) {
               $row = $result->FetchRow();
                return $days = $row['t_days'];   
            }else{ return FALSE; }
        }else { return FALSE; }
    }

    function getAdmissionDate(){
        global $db;

        $this->sql ="SELECT admission_dt FROM care_encounter WHERE encounter_nr = ".$db->qstr($this->encounter_nr)."";
        
        if($result = $db->Execute($this->sql)){
            if($result->RecordCount()){
               $row = $result->FetchRow();
                return $admission_dt = $row['admission_dt'];
            }else{ return FALSE; }
        }else { return FALSE; }
    }

    function getDaysCount()
    {
        global $db;
        
        if($this->is_final)
        $bill = date('Y-m-d', strtotime($this->bill_dte));
        else $bill = date('Y-m-d', strtotime($this->cur_billdate));

        $admit = date('Y-m-d', strtotime(self::getEncounterDate($this->encounter_nr)));

        $days = 0;

        $this->sql = "SELECT DATEDIFF(" . $db->qstr($bill) . "," . $db->qstr($admit) . ") as days";
        if ($result = $db->Execute($this->sql)) {
            $row = $result->FetchRow();
            $days = $row['days'];
        }

        if ($days == 0)
            $days = 1;

        return $days;
    }


    function getConDaysFrmAdDteToYearEnd(){
        global $db;
        $year = date('Y',strtotime($this->getAdmissionDate())) . "-12-31";
        $admit = date('Y-m-d',strtotime($this->getAdmissionDate()));
        $strSQL = "SELECT DATEDIFF(".$db->qstr($year).",".$db->qstr($admit).") as days";
        if ($result = $db->Execute($strSQL)) {
            $row = $result->FetchRow();
            $days = $row['days'];
        }
       return $days;
    }

    function getConDaysFrmNwYrToBillDte(){
        global $db;
        $newyr = date('Y',strtotime($this->getAdmissionDate())) . "-12-31";
        $bill  = date('Y-m-d', strtotime($this->bill_dte));
        $strSQL = "SELECT DATEDIFF(".$db->qstr($bill).",".$db->qstr($newyr).") as days";
        if ($result = $db->Execute($strSQL)) {
            $row = $result->FetchRow();
            $days = $row['days'];
        }
        $days = ($days > 1 ? $days : 0);
        return $days;
    }

    public static function getPatientType($encounterNr)
    {
        global $db;//$db->debug = 1;
        return $db->GetOne("SELECT encounter_type FROM care_encounter WHERE encounter_nr = ?",$encounterNr);
    }
    
    public static function getFinalDiagnosis($encounterNr)
    {
        global $db;//$db->debug = 1;
        return $db->GetOne("SELECT IFNULL(ssdn.final_diagnosis,ssd.final_diagnosis) AS final_diagnosis FROM seg_soa_diagnosis AS ssd LEFT JOIN seg_soa_diagnosis_new AS ssdn ON ssd.encounter_nr = ssdn.encounter_nr WHERE ssd.encounter_nr = ?",$encounterNr);
    }

    public static function getEditedDiagnosis($encounterNr){
         global $db;//$db->debug = 1;
             return $db->GetOne("SELECT ssdn.final_diagnosis AS final_diagnosis FROM seg_soa_diagnosis_new AS ssdn  WHERE ssdn.encounter_nr = '".$encounterNr."'");

    }

    public static function getOtherDiagnosis($encounterNr)
    {
        global $db;//$db->debug = 1;
        return $db->GetOne("SELECT IFNULL(ssdn.other_diagnosis,ssd.other_diagnosis) AS final_diagnosis FROM seg_soa_diagnosis as ssd LEFT JOIN seg_soa_diagnosis_new as ssdn ON ssd.encounter_nr = ssdn.encounter_nr WHERE ssd.encounter_nr = ?",$encounterNr);
    }
      public static function getEditedOtherDiagnosis($encounterNr)
    {
        global $db;//$db->debug = 1;
         return $db->GetOne("SELECT ssdn.other_diagnosis AS final_diagnosis FROM seg_soa_diagnosis_new AS ssdn  WHERE ssdn.encounter_nr = '".$encounterNr."'");
    }


    public static function getDialysisDiagnosis($encounterNr)
    {
        global $db;//$db->debug = 1;
        return $db->GetOne("SELECT er_opd_diagnosis FROM care_encounter WHERE encounter_nr = ?",$encounterNr);
    }

    public static function getFirstCaseratePF($bill_nr)
    {
        global $db;//$db->debug = 1;
        return $db->GetOne("SELECT pf_amount FROM seg_billing_caserate WHERE rate_type='1' AND bill_nr = ".$db->qstr($bill_nr));
    }

    public static function getSecondCaseratePF($bill_nr)
    {
        global $db;//$db->debug = 1;
        return $db->GetOne("SELECT pf_amount FROM seg_billing_caserate WHERE rate_type='2' AND bill_nr = ".$db->qstr($bill_nr));
    }
    public static function getFirstCaserateHCI($bill_nr)
    {
        global $db;//$db->debug = 1;
        return $db->GetOne("SELECT hci_amount FROM seg_billing_caserate WHERE rate_type='1' AND bill_nr = ".$db->qstr($bill_nr));
    }

    public static function getSecondCaserateHCI($bill_nr)
    {
        global $db;//$db->debug = 1;
        return $db->GetOne("SELECT hci_amount FROM seg_billing_caserate WHERE rate_type='2' AND bill_nr = ".$db->qstr($bill_nr));
    }

    function fortyFiveDays(){

        $ehr = Ehr::instance();
        $ehrData = array(
                "encounter_nr"  =>  $this->encounter_nr,
        );
        $dataEhr = $ehr->billing_getRepetitivSession($ehrData);
        $ehrResult = COUNT($dataEhr->status);

        $admit_yr = date('Y', strtotime(self::getEncounterDate($this->encounter_nr)));
        $bill_yr = date('Y', strtotime($this->bill_dte));
        $limit = 45;
        $ndays = $this->getDaysCount();
        $result = $this->checkInsuranceRequest($this->encounter_nr);
        $isER = $this->isERPatient($this->encounter_nr);
        $actual = $this->getBill_nr($admit_yr);
        $actual_confine = (int)$actual[0]['confine_days'];
        $actual_rem = (int)$actual[0]['rem_days'];
        $actual_rem_days = (int)$actual[0]['actual_rem_days'];

        $encounterType = $this->getEncounterType($this->encounter_nr);
        $encounterDept = $this->getEncounterDepartment($this->encounter_nr);
        $covid_validity = strtotime($this->isCovidSeasons());
        $isEncounterDt = strtotime(self::getEncounterDate($this->encounter_nr));

        // Unknown
        // Pro naka kung usabon nimo ni!!!
        if($encounterType == OUT_PATIENT && ($encounterDept == MINDANAO_DIALYSIS_CENTER || $encounterDept == RADIOLOGY_ONCOLOGY)){
            if($result->RecordCount() != 0) {
                $prevdays = $this->getPrevConfinement($admit_yr);
                $dialysisLimit = 90;
                $is_exhausted = 0;

                if($prevdays >= $dialysisLimit) {
                    $is_exhausted = 1;
                }


                if($covid_validity>$isEncounterDt){
                      $is_exhausted = 0;
                }


              


                if($prevdays >= $limit) {
                    if($this->is_final){
                        if($actual_rem_days) {
                            if(!$actual_rem) {
                                $rdays = $this->getRemainingDays($this->old_bill_nr);
                            } else {
                                $rdays = $actual_rem_days;
                            }
                        } else {
                            $rdays = $this->getRemainingDays($this->old_bill_nr);
                        }

                        if($rdays == NULL) {
                            $rdays = ($limit > $prevdays ? $limit - $prevdays : 0);
                        }
                    }else{
                        if($prevdays == NULL){
                            $rdays = ($limit > $ehrResult ? $limit - $ehrResult : 0);
                        }else{
                            if($actual_confine == $limit && !$actual_rem) {
                                $totalCov = ($limit > $ehrResult ? $limit - $ehrResult : 0);
                                $rdays = $totalCov;
                            } else {
                                $totalCov = ((int)$actual[0]['actual_rem_days'] > $ehrResult ? (int)$actual[0]['actual_rem_days'] - $ehrResult : 0);
                                $rdays = $totalCov;
                    }
                }
                    }
                } else {
                    if($this->is_final){
                        $rdays = $this->getRemainingDays($this->old_bill_nr);

                        if($rdays == NULL)
                            $rdays = ($limit > $prevdays ? $limit - $prevdays : 0);
                    }else{

                        if($prevdays == NULL){
                            $rdays = ($limit > $ehrResult ? $limit - $ehrResult : 0);
                        }else{
                            $totalCov = $prevdays + $ehrResult;
                            $rdays = ($limit > $totalCov ? $limit - $totalCov : 0);
                        }
                    }
                    }

                $cdays = $ehrResult;
                $rem = ($limit > $prevdays ? $limit - $prevdays : 0);
                $totalCovered = ($ehrResult > $rem ? $ehrResult - $rem : null);
                $c = '';
                if($totalCovered) {
                    if($actual_rem_days) {
                        $c = ((int)$actual[0]['actual_rem_days'] > $totalCovered ? (int)$actual[0]['actual_rem_days'] - $totalCovered : '');
                    }else {
                        $c = ($limit > $totalCovered ? $limit - $totalCovered : '');
                }
                }
                return array('remaining' => $rdays, 'covered' => $cdays, 'save' => $cdays, 'is_exhausted' => $is_exhausted, 'actual_rem_days' => $c);
            }
            else {
                return false;
            }
        }elseif ($encounterType == WELLBABY) {
            $prevdays = $this->getPrevConfinement($admit_yr);

            $rdays = ($limit > $prevdays ? $limit - $prevdays : 0);
            $cdays = ($ndays >= $prevdays ? $ndays : $prevdays);
            return array('remaining' => $rdays, 'covered' => $cdays, 'save' => $cdays);
        }

        if ($result->RecordCount() != 0) {
            // if ($admit_yr == $bill_yr) {
                $prevdays = $this->getPrevConfinement($admit_yr);
                
                $is_exhausted = 0;
                if($prevdays >= $limit)
                    $is_exhausted = 1;


                   if($covid_validity>$isEncounterDt){
                      $is_exhausted = 0;
                    }
                if($this->is_final){
                    $rdays = $this->getRemainingDays($this->old_bill_nr);

                    if($rdays == NULL)
                        $rdays = ($limit > $prevdays ? $limit - $prevdays : 0);
                }else{
                    if($prevdays == NULL){
                        $rdays = ($limit > $ndays ? $limit - $ndays : 0);
                    }else{
                        $totalCov = $prevdays + $ndays;
                        $rdays = ($limit > $totalCov ? $limit - $totalCov : 0);
                    }
                }

                $cdays = $ndays;

                return array('remaining' => $rdays, 'covered' => $cdays, 'save' => $cdays, 'is_exhausted' => $is_exhausted);
            /*} else {
                $day_a = $this->getConDaysFrmAdDteToYearEnd();
                $day_b = $this->getConDaysFrmNwYrToBillDte();
                $prevdays = $this->getPrevConfinement($admit_yr);
                
                if($this->is_final){
                $limit_a = ($limit > $prevdays ? $limit - $prevdays : 0);
                }else{
                    if($prevdays == NULL){
                        $limit_a = ($limit > $day_a ? $limit - $day_a : 0);
                    }else{
                        $totalCov = $prevdays + $day_a;
                        $limit_a = ($limit > $totalCov ? $limit - $totalCov : 0);
                    }
                }

                // $covered_a = ($limit_a >= $day_a) ? $day_a : $limit_a;
                // $covered_b = ($limit >= $day_b) ? $day_b : $limit;
                // $excess_a = ($limit_a >= $day_a) ? 0 : $day_a - $limit_a;
                // $excess_b = ($limit >= $day_b) ? 0 : $day_b - $limit;

                //$rdays = $limit_a + $limit . '   (last year: ' . $limit_a . ', this year: ' . $limit . ')';
                //$cdays = $covered_a + $covered_b;

                $rdays = $limit_a;
                $cdays = $day_a;
                $remain_this = ($limit > $day_b ? $limit - $day_b : 0);

                return array('remaining'=>$rdays , 'covered'=>$cdays , 'save'=> $cdays, 'thisyearcover' => $day_b, 'thisyearremain' => $remain_this);
            }*/
        } else {
            return false;
        }
    }

    function getRemainingDays($bill_nr){
        global $db;
        return $db->GetOne("SELECT rem_days FROM seg_confinement_tracker WHERE bill_nr=".$db->qstr($bill_nr));
    }

    //end by art
    //added by poliam
    //added new function
    function Classification($enc, $bill_dte, $IsCharity='', $IsEr='', $prevenc) {
         global $db;
        $s_desc= "";
        if ($IsCharity || $IsEr) {
          $filter = '';
            if ($prevenc != '') $filter = " or scg.encounter_nr = '$prevenc'";
                $strSQL = "SELECT discountdesc ".
                    "   FROM seg_discount as sd 
                    inner join seg_charity_grants as scg 
                    on sd.discountid = scg.discountid ".
                    "   where (scg.encounter_nr = '". $enc. "'".$filter.") ".
                    "      and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '" . $bill_dte . "' " .
                    "   order by grant_dte desc limit 1";

                if ($result = $db->Execute($strSQL)) {
                    if ($result->RecordCount()) {
                        while ($row = $result->FetchRow()) {
                        $s_desc = $row['discountdesc'];
                    }
                }
            }
        }
        return($s_desc);
    }

    //end by poliam

    //added by nick, 1/6/2014
    function getCaseDate($enc){
        global $db;
        $sql = "SELECT encounter_date FROM care_encounter WHERE encounter_nr = ".$db->qstr($enc);
        $rs = $db->Execute($sql);
        if($rs){
            if($rs->RecordCount()>0){
                $row = $rs->FetchRow();
                return $row['encounter_date'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    //end nick

    // Added by James 1/6/2014
    function trapError($bill_nr){
        global $db;

        $sql = "DELETE FROM seg_billing_encounter WHERE bill_nr = ".$db->qstr($bill_nr);
        $rs = $db->Execute($sql);
        if($rs){
            return true;
        }else{
            return false;
        }
    }// End James


    function getTotalAppliedDiscounts($enc){
        global $db;

        $sql = "SELECT SUM(discount) AS total_discount FROM seg_billingapplied_discount 
                WHERE encounter_nr = ".$db->qstr($enc);

        $rs = $db->Execute($sql);
             if($rs){
            if($rs->RecordCount()>0){
                $row = $rs->FetchRow();
                return $row['total_discount'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    /**
    * Created By Jarel
    * Created On 02/20/2014
    * Get Case Type Description Returns PRIVATE CASE OR HOUSE CASE
    * @param string enc - Patient Encounter
    * @param string bill_date
    * @param string $prevenc - Parent encounter if any
    * @return string $sdesc
    **/
    function getCaseTypeDesc($enc, $bill_date, $prevenc ='') {
        global $db;
        $sdesc = '';
        $filter = '';
        if ($prevenc != '') $filter = " or encounter_nr = ".$db->qstr($prevenc);
        $strSQL = "select stc.casetype_desc  ".
                    "   from seg_encounter_case as sec inner join seg_type_case as stc ".
                    "      on sec.casetype_id = stc.casetype_id ".
                    "   where (encounter_nr = ". $db->qstr($enc). "".$filter.") ".
                    "      and str_to_date(sec.modify_dt, '%Y-%m-%d %H:%i:%s') < ".$db->qstr($bill_date)."".
                    "      and !sec.is_deleted ".
                    "   order by sec.create_dt desc limit 1";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $sdesc = $row['casetype_desc'];
                }
            }
        }
        return (($sdesc=='') ? "HOUSE CASE" : $sdesc );
    }


    /**
    * Created By Jarel
    * Created On 02/24/2014
    * Get the Name of Insurance Holder 
    * @param string pid
    * @param string hcare_id
    * @return string name
    **/
    function getInsuranceMemberName($encounter_nr,$pid,$hcare_id)
    {
        global $db;

        $strSQL = "SELECT 
                      IF(
                        cpi.is_principal = '1',
                        CONCAT(simi.member_lname, ', ' , simi.member_fname , IF(simi.`suffix` <> '', CONCAT(' ', simi.suffix),''),' ' , LEFT(TRIM(simi.member_mname),1),'.'),
                        CONCAT(
                          TRIM(member_lname),
                          IF(
                            TRIM(member_fname) <> '',
                            CONCAT(', ', TRIM(member_fname)),
                            ' '
                          ),
                          IF(simi.`suffix` <> '', CONCAT(' ', simi.suffix),''),
                          IF(
                            TRIM(member_mname) <> '',
                            CONCAT(' ', LEFT(TRIM(member_mname), 1), '.'),
                            ''
                          )
                        )
                      ) AS name
                      FROM care_person_insurance cpi
                      INNER JOIN seg_encounter_insurance_memberinfo simi
                      ON simi.`pid` = cpi.`pid` AND simi.`hcare_id` = ".PHIC_ID."\n
                    WHERE  simi.encounter_nr = ".$db->qstr($encounter_nr)." AND cpi.`hcare_id` = ".PHIC_ID."";
      // echo "".$strSQL;
                    // var_dump($strSQL);die;
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
               $row = $result->FetchRow();
                return $row['name'];
            }return false;
        }return false; 
    }

    function getLastInsuranceModifier($encounter_nr)
    {
        global $db;

        $strSQL = "SELECT 
                      IF(simi.modify_dt IS NULL,fn_get_person_lastname_first(cpnl2.pid),fn_get_person_lastname_first(cpnl.pid)) 'name',
                      simi.insurance_nr insurance_nr,
                      IF(simi.modify_dt IS NULL,simi.create_dt,simi.modify_dt) AS modify_date
                      FROM seg_encounter_insurance_memberinfo simi
                      LEFT JOIN care_users cu2
                      ON simi.create_id = cu2.login_id
                      LEFT JOIN care_personell cpnl2
                      ON cu2.personell_nr=cpnl2.nr
                      LEFT JOIN care_users cu
                      ON simi.modify_id = cu.login_id
                      LEFT JOIN care_personell cpnl
                      ON cu.personell_nr=cpnl.nr
                    WHERE  simi.encounter_nr = ".$db->qstr($encounter_nr)." LIMIT 1";
      #echo "".$strSQL;
        if ($result = $db->GetRow($strSQL)) {
            return $result;
        }else return false; 
    }



    /**
    * Created By Jarel
    * Created On 03/12/2014
    * Get Patient Death date
    * @param string enc
    * @return string death date
    **/
    function getDeathDate($enc)
    {
        global $db;
        $strSQL = $db->Prepare("SELECT CONCAT(p.death_date,' ',p.death_time) as deathdate 
                                FROM care_person p
                                WHERE death_encounter_nr = ?");
      
        if($result=$db->Execute($strSQL,$enc)) {
             $row = $result->FetchRow();
                return $row['deathdate'];
        } else { return false; }
    }

    /**
     * Created by Jarel
     * Created on 10/18/2013
     * Used to Fetch death room rate according its room type
     * @param string warddesc
     * @return string rate
     */
    function getdeathroomrate($warddesc){
        global $db;

        if (!(strpos(strtoupper($warddesc), SERVICEWARD, 0) === false) && (strpos(strtoupper($warddesc), ICUWARD, 0) === false)) {
            $strSQL = "SELECT service_rate AS room_rate FROM seg_death_room_rate";
        }
        else if (!(strpos(strtoupper($warddesc), ANNEXWARD, 0) === false)) {
            $strSQL = "SELECT annex_rate  AS room_rate FROM seg_death_room_rate";
        }

        if($result = $db->Execute($strSQL)){
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    $rate = $row['room_rate'];
                }
            }
        }

        return $rate;
    }

    /**
     * @author Nick B. Alcala
     * Created On 03/25/2014
     * Get list of Diagnosis
     * @param  string  $enc
     * @return array
     */
    //updated by Nick, 4/15/2014 - order by entry_no
    function getIcd($enc){
        global $db;
        $data = array();

        $sql = $db->Prepare("SELECT IF(sed.code_alt IS NOT NULL,sed.code_alt,sed.code) AS code,
                                    IF(scrp.alt_code != '',
                                  scrp.alt_code,
                                  scrp.alt_code
                                    ) AS alt_code,
                                   sed.description
                            FROM seg_encounter_diagnosis AS sed
                            LEFT JOIN seg_case_rate_packages AS scrp
                            ON sed.code=scrp.`code`
                            WHERE is_deleted=0 AND encounter_nr=? 
                            AND IFNULL((DATE(sed.`create_time`) BETWEEN scrp.`date_from` AND scrp.`date_to`),scrp.code IS NULL)
                            ORDER BY entry_no ASC");
        $rs = $db->Execute($sql,$enc);

        if($rs){
            if($rs->RecordCount() > 0){
                while($row = $rs->FetchRow()){
                    array_push($data, $row);
                }
            }else{
                $data = false;
            }
        }else{
            $data = false;
        }

        return $data;
    }

    /**
     * @author Nick B. Alcala
     * Created On 03/25/2014
     * Get list of Procedures
     * @param  string  $enc
     * @return array
     */
    function getRvs($enc){
        global $db;
        $data = array();

        $encounterDate = self::getEncounterDate($enc);

        $sql = $db->Prepare("SELECT 
                              smod.ops_code,
                              IF(smod.alt_code != '',
                                  smod.alt_code,
                                  scrp.alt_code
                                    ) AS alt_code,
                              IF(smod.description IS NOT NULL,
                                  smod.description,
                                  scrp.description
                                    ) AS description,
                              smod.laterality,
                              smod.op_date,
                              scrp.special_case,
                              smod.op_date AS special_dates
                            FROM
                              seg_misc_ops AS smo 
                              INNER JOIN seg_misc_ops_details AS smod 
                                ON smod.refno = smo.refno 
                              INNER JOIN seg_case_rate_packages AS scrp
                                ON smod.ops_code = scrp.code AND
                                (
                                    STR_TO_DATE(scrp.date_from,'%Y-%m-%d') <= STR_TO_DATE('{$encounterDate}','%Y-%m-%d') AND
                                    STR_TO_DATE(scrp.date_to,'%Y-%m-%d') >= STR_TO_DATE('{$encounterDate}','%Y-%m-%d')
                                )
                            WHERE smo.encounter_nr = ? ORDER BY op_date");
    
        $rs = $db->Execute($sql,$enc);

        if($rs){
            if($rs->RecordCount() > 0){
                while($row = $rs->FetchRow()){
                    array_push($data, $row);
                }
            }else{
                $data = false;
            }
        }else{
            $data = false;
        }

        return $data;
    }
    //end nick

    /**
    * Created By Jarel
    * Created On 04/04/2014
    * set accomodation type
    * @param string type
    **/
    function setAccomodationType($type)
    {
        $this->accomodation_type = $type;
    }


    /**
    * Created By Jarel
    * Created On 04/04/2014
    * get accomodation type
    * @return string type
    **/
    function getAccomodationType()
    {
       return $this->accomodation_type;
    }

    /**
     * @author Nick 6-11-2015
     * returns 'infirmary' | 'dependent'
     * @return string
     */
    public function isInfirmaryOrDependent($encounterNr){
        global $db;

        $pid = $db->GetOne("SELECT pid FROM care_encounter WHERE encounter_nr = ?",$encounterNr);

        /*if(isset($this->old_bill_nr) && $this->old_bill_nr!=''){
            $billing = $db->GetOne("SELECT discount_type FROM seg_billing_encounter WHERE bill_nr=? AND is_deleted IS NULL",$this->old_bill_nr);
            if($billing)
                return $billing;
        }*/

        $isPersonnel = $db->GetOne("SELECT personnel.pid FROM care_personell AS personnel WHERE pid=? AND status <> 'deleted'",$pid);
        if($isPersonnel)
            return 'infirmary';

        $isDependent = $db->GetOne("SELECT
                                        dependent.dependent_pid 
                                    FROM seg_dependents AS dependent 
                                    INNER JOIN care_personell AS personnel
                                        ON personnel.pid = dependent.parent_pid
                                        AND personnel.status <> 'deleted'
                                    WHERE dependent_pid=? AND dependent.status = 'member'",$pid);
        if($isDependent)
            return 'dependent';
    }

    /**
     * @author Nick B. Alcala
     * Created On 04/04/2014
     * Identify if patient is Infirmary of Dependent
     * @param  string  $enc
     * @return boolean/string
     * 
     * Commented out by Nick 6-11-2015
     */
    //update by poliam 01/19/2015
    //param encounter_nr  and current date
    // function isInfirmaryOrDependent($objData){
    //     global $db;
    //     $output = '';
    //     $enc = $objData->encounter_nr;

    //     if(!empty($objData->bill_nr)){
    //         if($objData->bill_curDate >= infirmary_effectivity_per_encounter){
    //             $this->sql = $db->Prepare("SELECT sbe.discount_type
    //                 FROM seg_billing_encounter AS sbe
    //                 WHERE sbe.bill_nr = ? AND sbe.is_deleted IS NULL
    //                 LIMIT 1");

    //             $rs = $db->Execute($this->sql,$objData->bill_nr);
    //             if($rs){
    //                 if($rs->RecordCount()){
    //                     if($row = $rs->FetchRow())
    //                         return $row['discount_type'];
    //                 }
    //             }
    //         }
    //     }

    //     if($this->isInfirmary($objData)){
    //         return 'infirmary';
    //     }

    //     //check if this is under the effective date
    //     if($objData->bill_curDate < infirmary_effectivity){
    //         //the old query
    //         $this->sql = $db->Prepare("SELECT ce.pid,
    //                                   parent_pid,
    //                                   dependent_pid,
    //                                   relationship 
    //                 FROM seg_dependents AS sd 
    //                                   INNER JOIN care_encounter AS ce 
    //                                     ON sd.parent_pid = ce.pid 
    //                                     OR sd.dependent_pid = ce.pid 
    //                                 WHERE ce.encounter_nr = ?");
    //         //execute the query
    //         $rs = $db->Execute($this->sql,$objData->encounter_nr);
    //             if($rs){ //check if has data
    //                 if($rs->RecordCount()){ //check if has count
    //                     while($row = $rs->FetchRow()){ //fetch the rows
    //                         if($row['parent_pid'] == $row['pid']){ //check if parent personell is the patient
    //                             $output = 'infirmary'; // output infirmary
    //                         }else if($row['dependent_pid'] == $row['pid']){ //check if the patient is the dependent
    //                             $output = 'dependent'; //output dependent
    //                 }
    //                     }//end of while
    //                     return $output;//return empty output
    //         }else{
    //             return false;
    //                 }//end of recordcount
    //     }else{
    //         return false;
    //             }//end of if
    //     }else{ //else for effectivity date
    //         //query for infirmary active or inactive
    //         $this->sql = "SELECT ce.pid,
    //                         parent_pid,
    //                         dependent_pid,
    //                         relationship
    //                     FROM seg_dependents AS sd
    //                     INNER JOIN care_encounter AS ce
    //                     ON sd.parent_pid = ce.pid
    //                     OR sd.dependent_pid = ce.pid
    //                     WHERE sd.status = 'member' 
    //                     AND ce.encounter_nr = ".$db->qstr($objData->encounter_nr);

    //         $result = $db->GetRow($this->sql); //execute the query and get row
        
    //         if($result){ //check if has 
    //             //query to check if the personell is still 
    //             $SecondSql = "SELECT cp.nr FROM care_personell `cp` WHERE cp.status = '' AND cp.pid = ".$db->qstr($result['parent_pid']);
    //             //check if the personell is the one admitted
    //             if($result['parent_pid'] == $result['pid']){
    //                 //execute the query if the personell is still active
    //                 $SecondResult = $db->GetOne($SecondSql);
    //                 if($SecondResult){ //check if has records found  
    //                     $output = "infirmary"; //the patient is a personell here
    //     }
    //                 //check if the patient is just a dependent on the personell here
    //             }else if($result['dependent_pid'] == $result['pid']){
    //                 //execute the query if the dependent personell is still active
    //                 $SecondResult = $db->GetOne($SecondSql);  
    //                 if($SecondResult){//check if the result has records
    //                     //output will be dependent
    //                     $output = "dependent";
    //     }
    // }
    //             return $output;//output = blank
    //         }else{
    //             return false;//has no row founds
    //         }
    //     }//end of date effectivity
    // }//end of the function isInfirmaryOrDependent

    // added by Nick 05-21-2014
    // update by poliam 01/19/2015
    // Commented out by Nick 6-11-2015
    // function isInfirmary($objData){
    //     global $db;
    //      //check if this is under the effective date
    //     if($objData->bill_curDate > infirmary_effectivity){
    //         //update query with active personell
    //         $this->sql = "SELECT cp.nr, cp.pid 
    //                   FROM care_personell AS cp 
    //                   INNER JOIN care_encounter AS ce
    //                   ON cp.pid = ce.pid 
    //                   WHERE cp.status = ''
    //                   AND ce.encounter_nr = ".$db->qstr($objData->encounter_nr);
    //         $result = $db->GetRow($this->sql);//get the row of the query
    //         if($result){//check if it capture a data
    //             return true;
    //         }else{
    //             return false;
    //         }//end of result check
    //     }else{//else of infirmary effective date
    //         //the old query so that old soa will retain
    //         $this->sql = "SELECT a.nr, b.pid 
    //                     FROM care_personell AS a 
    //                       INNER JOIN care_encounter AS b 
    //                         ON a.pid = b.pid 
    //                     WHERE b.encounter_nr = ?";

    //         $rs = $db->Execute($this->sql,$objData->encounter_nr);//execute the query
    //         if($rs){ //check has no error
    //             if($rs->RecordCount()){ // check if has even 1 records
    //                 return true;//return true
    //             }else{//count else
    //                 return false; //retrun false
    //             }//end of the if recountcount 
    //         }else{//ekse of chech if the sql has no error
    //         return false;
    //         }//end of if
    //     }//end of infirmary effectivity
    // }//end of isInfirmary

    /**
     * @author Nick B. ALcala
     * Get death date by encounter date
     * Created On 4/11/2014
     * @param  string $enc
     * @param  string $curr_enc
     * @return date string / false
     */
    function getDeathDate2($enc,$curr_enc)
    {
        global $db;
        $this->sql = $db->Prepare("SELECT CONCAT(p.death_date,' ',p.death_time) as deathdate, p.death_encounter_nr
                                FROM care_person p
                                WHERE death_encounter_nr = ?");
      
        $rs = $db->Execute($this->sql, $enc);
        if($rs){
            if($rs->RecordCount()){
                $row = $rs->FetchRow();
                if($row['death_encounter_nr'] == $curr_enc){
                    return $row['deathdate'];
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @author Nick B. Alcala
     * Identify if patient is new born
     * Created On 4/21/2014
     * @param  String $enc
     * @return boolean
     */
    function isNewBorn($enc){
        global $db;
        $this->sql = $db->Prepare("SELECT 
                                      smod.ops_code 
                                    FROM
                                      seg_misc_ops AS smo 
                                      INNER JOIN seg_misc_ops_details AS smod 
                                        ON smod.refno = smo.refno 
                                      INNER JOIN seg_case_rate_special AS scrs 
                                        ON scrs.sp_package_id = smod.ops_code 
                                    WHERE smo.encounter_nr = " . $db->qstr($enc));
        $row = $db->GetRow($this->sql);
        return (count($row)) ? true : false;
    }

    /**
    * @author EJ Ramos
    * Get the value of prenatal dates
    * Created on 12/12/2014
    * @param  String $enc
    * @return array
    */
    function getPrenatalDates($enc){
        global $db;
        $enc = $db->qstr($enc);
        $this->sql = "SELECT 
                      smod.prenatal_dates AS dates
                    FROM
                      seg_misc_ops AS smo 
                      LEFT JOIN seg_misc_ops_details AS smod
                      ON smod.refno = smo.refno
                      WHERE smo.encounter_nr = $enc";

        $result = $db->Execute($this->sql);
        if($result && $row = $result->FetchRow()) {
            $dates = explode(',', $row['dates']);
            return $dates;
        }
        else {
            return false;
        }
    }

    /**
    * @author EJ Ramos
    * Get LMP Date
    * Created on 12/12/2014
    * @param  String $enc
    * @return date
    */
    function getLmpDate($enc){
        global $db;
        $enc = $db->qstr($enc);

        $date = $db->GetOne("SELECT 
                              smod.lmp_date 
                            FROM
                              seg_misc_ops AS smo 
                              LEFT JOIN seg_misc_ops_details AS smod 
                                ON smod.refno = smo.refno 
                            WHERE smo.encounter_nr = $enc
                              AND smod.ops_code IN (
                                '59403',
                                'NSD01',
                                'MCP01',
                                'ANC01',
                                'ANC02',
                                '58600',
                                '59409',
                                '59411',
                                '59513',
                                '59514',
                                '59612',
                                '59620'
                              )");

        return $date;
   }

   function getMCP_package_details($param){
        global $db;
        #$db->debug = true;
        if ($param['first'] !='') {
            if ($param['second'] != '') {
                $in = " IN (".$db->qstr($param['first']).",".$db->qstr($param['second']).")";
            }else{
                $in = " = '".$param['first']."'";
            }
        }
        $sql = 'SELECT 
                  b.`lmp_date`,b.`prenatal_dates`
                FROM
                  seg_misc_ops a 
                  INNER JOIN seg_misc_ops_details b 
                    ON a.`refno` = b.`refno` 
                WHERE a.`encounter_nr` = '.$db->qstr($param['enc']).' 
                  AND b.lmp_date <> "0000-00-00"';
                # commented out by : syboy 12/06/2015 : meow ; AND b.`ops_code` '.$in
        if ($rs = $db->GetRow($sql)) {
            return $rs;
        } return false;
   }

    /**
     * @author Nick B. Alcala
     * Identify if patient (new born) availed the hearing test
     * Created On 4/22/2014
     * @param  String $enc
     * @return boolean
     */
    function isHearingTestAvailed($enc,$isWellBaby){
        global $db;
        /* default with hearing test */
        $this->sql = $db->Prepare("SELECT 
                                      scrs.* 
                                    FROM
                                      seg_caserate_hearing_test AS scrs 
                                    WHERE scrs.`encounter_nr` = ?");

        if($isWellBaby){
            $rs = $db->Execute($this->sql,$enc);
            if($rs){
                if($rs->RecordCount() > 0){
                    $row = $rs->FetchRow();
                    return $row['is_availed'];
                }else{
                    $this->sql = $db->Prepare("INSERT INTO seg_caserate_hearing_test (encounter_nr,is_availed) VALUES (?,0)");
                    $rs = $db->Execute($this->sql,$enc);
                    if($rs){
                        return 0;
                    }else{
                        return 2;
                    }
                }
            }else{
                return 2;
            }
        }
    }

    /**
     * @author Nick B. Alcala
     * Add or update new born patient hearing test data
     * Created On 4/22/2014
     * @param  string $enc
     * @param  int    $value
     * @return boolean
     */
    function updateHearingTest($enc,$value){
        global $db;
        $row_count = 0;
        $this->sql = $db->Prepare("SELECT * FROM seg_caserate_hearing_test WHERE encounter_nr = ?");
        $rs = $db->Execute($this->sql,$enc);
        if($rs){
            $row_count = $rs->RecordCount();
        }

        if($row_count){
            $cols = array('encounter_nr' => $enc, 'is_availed' => $value);
            $pk   = array('encounter_nr');
            $rs = $db->Replace('seg_caserate_hearing_test',$cols,$pk);
        }

        return ($rs) ? true : false;
    }
    /**
     * Added by Nick, 4/23/2014
     * Discharge well babies
     * @return Boolean
     *
     *updated by art 10/13/2014. changed parameter from bill_nr to final
     */

    function dischargeWellBaby($enc,$final){
            global $db;
       /*$isNewBorn = $db->GetOne("SELECT
                                      IFNULL(package_id,0) AS isNewBorn
                                    FROM
                                      seg_billing_caserate AS sbc
                                    WHERE sbc.bill_nr = " . $db->qstr($bill_nr) . "
                                    AND package_id = " . $db->qstr(NEWBORN_PACKAGE));*/#commented by art 10/13/2014
        #added by art 10/13/2014
        $isNewBorn  = $db->GetOne("SELECT 
                                      a.`encounter_type` 
                                    FROM
                                      care_encounter AS a 
                                    WHERE a.`encounter_nr` =  ". $db->qstr($enc) ."
                                      AND a.`encounter_type` = " . $db->qstr(WELLBABY));
        #end 
        if($isNewBorn && $final == 1){
            $this->sql = $db->Prepare("UPDATE 
                                          care_encounter 
                                        SET
                                          is_discharged = 1,
                                          discharge_date = NOW(), discharge_time = NOW() 
                                        WHERE encounter_nr = ?");
            $rs = $db->Execute($this->sql,$enc);
            if($rs){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
    * @author Jarel 
    * Created On 04/04/2014
    * get coverage details base on insurance id, area
    * and doctor number if from doctors
    * @param string hcare_id
    * @param string area
    * @return mixed result
    **/
    function getPerHCareCoverageDetails($hcare_id, $area)
    {
       global $db;

      
        $value = array($hcare_id,$area);
    

       $this->sql = $db->Prepare("SELECT * 
                                  FROM seg_billingcoverage_adjustment 
                                  WHERE hcare_id = ?
                                  AND bill_area = ?
                                  $docsql");

        if($this->result=$db->Execute($this->sql,$value)) {
            return $this->result;
        } else { return false; }
    }

    
    /**
    * @author Jarel 
    * Created On 04/04/2014
    * get doctor coverage details 
    * @param string refno = bill_nr if final else T+encounter_nr
    * @param string hcare_id
    * @param string dr_nr
    * @param string area
    * @return mixed result
    **/
    function getDoctorCoverageDetails($refno,$hcare_id,$dr_nr,$area)
    {
        global $db;
        $value = array($hcare_id,$refno,$dr_nr,$area);
        $this->sql = $db->Prepare("SELECT sbp.dr_claim,sbpb.first_claim,sbpb.second_claim
                                  FROM seg_billing_pf sbp
                                  LEFT JOIN seg_billing_pf_breakdown sbpb
                                  ON sbp.hcare_id=sbpb.hcare_id AND sbp.bill_nr=sbpb.bill_nr AND sbp.dr_nr=sbpb.dr_nr AND sbp.role_area=sbpb.role_area
                                  WHERE sbp.hcare_id = ?
                                  AND sbp.bill_nr = ?
                                  AND sbp.dr_nr = ?
                                  AND sbp.role_area = ?");
        if($this->result=$db->Execute($this->sql,$value)) {
            return $this->result;
        } else { return false; }
    }


    function setPFCoverage($value)
    {
        $this->doctor_coverage += $value;
    }


    function getPFCoverage()
    {
        return($this->doctor_coverage);
    }


    function setPFDiscount($value)
    {
        $this->doctor_discount += $value;
    }


    function getPFDiscount()
    {
        return($this->doctor_discount);
    }

    
    /**
    * @author Jarel 
    * Created On 04/04/2014
    * Save Doctors Coverage 
    * @param string value 
    * @return bool result
    **/
    function saveDoctorCoverage($value)
    {
        global $db;

        $sql = "INSERT INTO seg_billing_pf 
                (bill_nr, hcare_id, dr_nr, role_area, dr_charge, dr_claim)
                VALUES $value";
        if($result = $db->Execute($sql)){
            return true;
        }else{
            return false;
        }
    }

    function saveDoctorCoverageBreakdown($value)
    {
        global $db;

        $sql = "INSERT INTO seg_billing_pf_breakdown
                (bill_nr, hcare_id, dr_nr, role_area, dr_claim, first_claim, second_claim)
                VALUES $value";
        if($result = $db->Execute($sql)){
            return true;
        }else{
            return false;
        }
    }


    /**
    * @author Jarel 
    * Created On 04/04/2014
    * Check if has already save doctor coverage
    * @param string refno 
    * @return bool result
    **/
    function hasDoctorCoverage($refno)
    {
        global $db;
        $sql = "SELECT bill_nr FROM seg_billing_pf WHERE bill_nr =".$db->qstr($refno);
        $result = $db->Execute($sql);
        if($result){
            if($result->RecordCount())
                return true;
            else
                return false;
        }else
            return false;
    }


    /**
    * @author Jarel 
    * Created On 04/04/2014
    * Check if has already save doctor coverage
    * @param string refno 
    * @return bool result
    **/

    //added Christtian 01-20-20
    function getDoctorCoverageBreakdown($refno)
    {
        global $db;

        $doctorCoverageDetails = $db->GetAll("SELECT * FROM seg_billing_pf_breakdown WHERE bill_nr =".$db->qstr($refno));

        if($doctorCoverageDetails)
            return $doctorCoverageDetails;
        else 
            return false;
    }

    function updateclearDoctorCoverage($refno,$hcare_id,$dr_nr,$role_area,$dr_claim)
    {
        global $db;
        $value = array($dr_claim, $refno, $hcare_id, $dr_nr, $role_area);
        $sql  = $db->Prepare("UPDATE 
                  seg_billing_pf 
                SET
                  dr_claim = ?
                WHERE bill_nr = ? 
                  AND hcare_id = ? 
                  AND dr_nr = ? 
                  AND role_area = ?");

        $result = $db->Execute($sql,$value);
        if($result)
            return true;
        else
            return false;
    }

    function updateDoctorCoverageBreakdown($refno,$hcare_id,$dr_nr,$role_area,$dr_claim,$first_claim,$second_claim)
    {
        global $db;
        $value = array($dr_claim, $first_claim, $second_claim, $refno, $hcare_id, $dr_nr, $role_area);
        $sql  = $db->Prepare("UPDATE 
                                seg_billing_pf_breakdown 
                              SET
                                dr_claim = ?,
                                first_claim = ?, 
                                second_claim = ? 
                              WHERE bill_nr = ? 
                                AND hcare_id = ? 
                                AND dr_nr = ? 
                                AND role_area = ?");

        $result = $db->Execute($sql,$value);
        if($result)
            return true;
        else
            return false;
    }
    //end Christian 01-20-20

    function clearDoctorCoverage($refno)
    {
        global $db;
        $sql = "DELETE FROM seg_billing_pf WHERE bill_nr =".$db->qstr($refno);
        $result = $db->Execute($sql);
        
        if($result)
            return true;
        else
            return false;
    }
    function clearDoctorCoverageBreakdown($refno)
    {
        global $db;
        $sql = "DELETE FROM seg_billing_pf_breakdown WHERE bill_nr =".$db->qstr($refno);
        $result = $db->Execute($sql);
        
        if($result)
            return true;
        else
            return false;
    }

    //added by Nick 05/06/2014
    function getCaseTypeHist(){
        return $this->caseTypeHist;
    }

    //added by Nick 05-12-2014
    function updateOpDate($op_date, $refno, $ops_code, $entry_no){
        global $db;
        $this->sql = $db->Prepare("UPDATE 
                                      seg_misc_ops_details 
                                    SET
                                      op_date = DATE_FORMAT(".$db->qstr($op_date).",'%Y-%m-%d') 
                                    WHERE refno = ".$db->qstr($refno)." 
                                      AND ops_code = ".$db->qstr($ops_code)." 
                                      AND entry_no = ".$db->qstr($entry_no));

        $rs = $db->Execute($this->sql);
        if($rs){
            return true;
        }else{
            return false;
        }
    }

    //added by Nick 05-12-2014
    function getMemCatHist(){
        return $this->memCatHist;
    }

    //added by Nick 05-15-2014
    function getMembershipTypes(){
        global $db;
        $this->sql  = $db->Prepare("SELECT memcategory_id,memcategory_code,memcategory_desc,is_employer_info_required FROM seg_memcategory ORDER BY memcategory_desc;");
        
        $rs = $db->Execute($this->sql);
        if($rs){
            if($rs->RecordCount()){
                return $rs->GetRows();
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //added by Nick 05-15-2014
    function setMemberCategory($enc,$memcat){
        global $db;
        $data = array('memcategory_id'=>$memcat,
                      'encounter_nr'=>$enc);
        $pk = array('encounter_nr');
        $rs = $db->Replace('seg_encounter_memcategory',$data,$pk);
        if($rs){
            return true;
        }else{
            return false;
        }
    }

    //added by Nick 05-15-2014
    function getEncounterType($enc){
        global $db;
        return $db->GetOne("SELECT encounter_type FROM care_encounter WHERE encounter_nr = ?",$enc);
    }

    /**
    * Created by Jarel Q. Mamac
    * Created on 05/26/2014
    * Get saved doctor charge from seg_billing_pf
    */
    function getDoctorPFCharge($bill_nr,$dr_nr,$role_area){
        global $db;

        $value = array($bill_nr, $dr_nr, $role_area);
        $this->sql = $db->Prepare("SELECT dr_charge 
                                   FROM seg_billing_pf 
                                   WHERE hcare_id = '".PHIC_ID."' 
                                   AND bill_nr = ? 
                                   AND dr_nr = ? 
                                   AND role_area = ?");

        $rs = $db->Execute($this->sql,$value);
        if($rs){
            if($rs->RecordCount()){
                $row = $rs->FetchRow();
                return $row['dr_charge'];
            }else{
                return false;
            }
        }else{
            return false;
        }

    }


    #added by art 07/07/2014 
    #for bug 443
    function getHoursOfDeath($encounter_nr){
        global $db;
        $sql = $db->Prepare("SELECT
                              TIMESTAMPDIFF(
                                HOUR,
                                IFNULL(b.`admission_dt`, b.`encounter_date`),
                                TIMESTAMP(
                                  CONCAT(
                                    a.`death_date`,
                                    ' ',
                                    a.`death_time`
                                  )
                                )
                              ) AS hours,
                              COUNT(d.`refno`) AS icp,
                              c.`hcare_id` AS insurance_id
                            FROM
                              care_person AS a 
                              LEFT JOIN care_encounter AS b 
                                ON a.`death_encounter_nr` = b.`encounter_nr`
                              LEFT JOIN seg_encounter_insurance AS c
                              ON b.`encounter_nr` = c.`encounter_nr`
                              LEFT JOIN seg_misc_ops AS d 
                                ON c.`encounter_nr` = d.`encounter_nr` 
                            WHERE a.`death_encounter_nr` = ? ");
        
        $result = $db->Execute($sql,$encounter_nr);
        if ($row = $result->FetchRow()) {
            if ($row['icp'] == 0 && $row['hours'] < 24  && $row['hours'] > 0 && $row['insurance_id'] == 18 ) {
                return true;
            }
        }else{
            return false;
        }
    }
    # end art

    //added by Nick 06-26-2014
    function getSeriesNumber($enc){
        global $db;
        $this->sql = $db->Prepare("SELECT
                                    series_nr
                                   FROM seg_billing_series_nr
                                    WHERE encounter_nr = " . $db->qstr($enc) .
                                   "AND is_deleted = 0");
        return $db->GetOne($this->sql);
    }

    /**
     * added by Nick 08-08-2014
     * doctor's accommodation type is automatically added/updated/deleted in database
     * using triggers(seg_encounter_privy_dr)
     * @param $encounter_nr
     * @param $entry_no
     * @param $dr_nr
     * @param $accommodation_type
     * @return bool
     */
    function setDoctorAccommodationType($encounter_nr, $dr_nr,$entry_no, $accommodation_type){
        global $db;
        $this->sql = $db->Prepare("UPDATE
                                      seg_doctor_accommodation_type
                                    SET
                                      accommodation_type = ?
                                    WHERE encounter_nr = ?
                                      AND dr_nr = ?
                                      AND entry_no = ?");
        $rs = $db->Execute($this->sql,array($accommodation_type,$encounter_nr,$dr_nr,$entry_no));
        if($rs){
            return true;
        }else{
            return false;
        }
    }

    //added by Nick 08-04-2014
    function setDischargeName($encounter_nr,$pid){
        global $db;
        $row = array();
        $sql = "SELECT name_first,name_middle,name_last FROM care_person WHERE pid = ? LIMIT 1";
        $rs = $db->Execute($sql,$pid);
        if($rs){
            if($rs->RecordCount()){
                $row = $rs->FetchRow();
            }
        }

        $table = 'seg_encounter_name';
        $fields = array(
            'encounter_nr' => $db->qstr($encounter_nr),
            'pid' => $db->qstr($pid),
            'name_first' => $db->qstr($row['name_first']),
            'name_middle' => $db->qstr($row['name_middle']),
            'name_last' => $db->qstr($row['name_last']),
        );
        $pk = array(
            'encounter_nr',
            'pid'
        );

        $rs = $db->replace($table,$fields,$pk);

        if($rs){
            return true;
        }else{
            $this->error_msg =  "ERROR: ".$db->ErrorMsg();
            return false;
        }
    }
/*        function updateAccommodation($enc,$bill_dte){
           global $db;
        $row_count = 0;
        $this->sql = $db->Prepare("SELECT * FROM care_encounter_location cel WHERE cel.encounter_nr = ".$db->qstr($enc)." AND cel.status = ''");
        #var_dump($this->sql);
        $rs = $db->Execute($this->sql);
        if($rs){
            $row_count = $rs->RecordCount();
        }

        if($row_count){
            $cols = array('encounter_nr' => $db->qstr($enc), 'date_to' =>$db->qstr($bill_dte));
            $pk   = array('encounter_nr','status');
            $rs = $db->Replace('care_encounter_location',$cols,$pk);
        }

        return ($rs) ? true : false;

}*/

function updateAccommodation($enc,$bill_dte){
     global $db;
        $this->sql = "UPDATE
                          care_encounter_location cel
                        SET
                          cel.date_to = ".$db->qstr($bill_dte)."
                        WHERE cel.encounter_nr = ".$db->qstr($enc)." AND
                           cel.status NOT IN ('discharged') AND is_deleted <> 1";
      #  var_dump($this->sql);                 
                         
        if($this->result = $db->Execute($this->sql)) {
            return $this->result;
        } else {
            return false;
        }
    }
    //added by Nick 08-04-2014
    function getDischargeName($encounter_nr){
        global $db;
        $this->sql = "SELECT * FROM seg_encounter_name WHERE encounter_nr = ? LIMIT 1";
        $rs = $db->Execute($this->sql,$encounter_nr);
        if($rs){
            if($rs->RecordCount()){
                return $rs->FetchRow();
            }
        }
        return false;
    }

    //added by Nick 08-05-2014
    function setEncounterMemberInfo($data){
        global $db;

        foreach ($data as $key => $value) {
            $data[$key] = utf8_decode($value);
        }
        $row = $db->GetRow("SELECT history 
                            FROM seg_encounter_insurance_memberinfo
                            WHERE encounter_nr = " . $db->qstr($data['encounter_nr']) . " LIMIT 1");

        $table = 'seg_encounter_insurance_memberinfo';
        $fields = array(
            'encounter_nr' => $db->qstr($data['encounter_nr']),
            'pid' =>$db->qstr($data['pid']),
            'hcare_id' => $db->qstr($data['hcare_id']),
            'insurance_nr' =>$db->qstr($data['insurance_nr']),
            'patient_pin' =>$db->qstr($data['patient_pin']),
            'member_lname' =>$db->qstr($data['member_lname']),
            'member_fname' =>$db->qstr($data['member_fname']),
            'member_mname' =>$db->qstr($data['member_mname']),
            'suffix' =>$db->qstr($data['suffix']),
            'sex' => $db->qstr($data['sex']),
            'birth_date' =>$db->qstr($data['birth_date']),
            'street_name' =>$db->qstr($data['street_name']),
            'brgy_nr' =>$db->qstr($data['brgy_nr']),
            'mun_nr' =>$db->qstr($data['mun_nr']),
            'relation' =>$db->qstr($data['relation']),
            'member_type' =>$db->qstr($data['member_type']),
            'employer_no' =>$db->qstr($data['employer_no']),
            'employer_name' =>$db->qstr($data['employer_name']),
            'was_temp'=>$db->qstr($data['was_temp'])
        );

        if(isset($row) && !empty($row)){
            $fields['modify_dt'] = $db->qstr(date('Y-m-d H:i:s'));
            $fields['modify_id'] = $db->qstr($_SESSION['sess_login_userid']);
            $fields['history'] = $db->qstr($row['history'] . "Updated by " . $_SESSION['sess_user_name'] . " on " . date('Y-m-d H:i:s') . "\n" . $this->arrayToHistory($fields) . "\n\n");
        }else{
            $fields['create_dt'] = $db->qstr(date('Y-m-d H:i:s'));
            $fields['create_id'] = $db->qstr($_SESSION['sess_login_userid']);
            $fields['history'] = $db->qstr($row['history'] . "Created by " . $_SESSION['sess_user_name'] . " on " . date('Y-m-d H:i:s') . "\n" . $this->arrayToHistory($fields) . "\n\n");
        }

        $pk = array(
            'encounter_nr'
        );

        $rs = $db->Replace($table, $fields, $pk, false);

        if($rs){
            return true;
        }else{
            return false;
        }
    }//end of function setEncounterMemberInfo

    private function arrayToHistory($arr){
        $result = array();
        foreach ($arr as $key => $value) {
            $result[] = $key . "=" . $value;
        }
        return implode(',', $result);
    }

    //Created by EJ 11/10/2014
    function checkIfPbef($insurance_nr,$pid) {
        global $db;
        $insurance_nr = $db->qstr($insurance_nr);
        $pid = $db->qstr($pid);

        $is_pbef  = $db->GetOne("SELECT is_pbef FROM seg_encounter_insurance_memberinfo WHERE insurance_nr = $insurance_nr AND pid = $pid");
        
        return  $is_pbef;
    }

    //Created by EJ 11/10/2014
    function getPbefRelation($insurance_nr, $pid) {
        global $db;
        $insurance_nr = $db->qstr($insurance_nr);
        $pid = $db->qstr($pid);
        $this->sql = "SELECT 
                      CASE
                        relation 
                        WHEN 'B' 
                        THEN 'Sibling' 
                        WHEN 'C' 
                        THEN 'Child' 
                        WHEN 'O' 
                        THEN 'Other' 
                        WHEN 'P' 
                        THEN 'Parent'
                        WHEN 'S' 
                        THEN 'Spouse'
                        ELSE 'Member' 
                      END AS relation 
                    FROM
                      seg_insurance_member_info 
                    WHERE insurance_nr = $insurance_nr
                    AND pid = $pid";

        if($this->result=$db->Execute($this->sql)) {
            if($row=$this->result->FetchRow()){
                    return $row['relation'];
                }
        } else { return false; }
    }

    /**
    * @author Art 
    * Created On 11/22/2014
    * get encounter limit
    * @param string encoutner number
    * @return  array
    **/
    function getEncounterLimit($enc_nr){
        global $db;
        $limit = "1";
        
        if ($this->HasNbbInsurance($enc_nr) && !$this->hasPaywardAccom($enc_nr)) {
            $meds = $db->GetOne("SELECT amountlimit FROM seg_hcare_confinetype WHERE bsked_id = ".$db->qstr(BSKED_ID_MEDS)." AND confinetype_id=".$db->qstr(confNbb));
            $xlo = $db->GetOne("SELECT amountlimit FROM seg_hcare_confinetype WHERE bsked_id = ".$db->qstr(BSKED_ID_XLO)." AND confinetype_id=".$db->qstr(confNbb));
        }else{
            $meds = $db->GetOne("SELECT 
                                  c.`amountlimit` 
                                FROM
                                  seg_encounter_confinement a 
                                  INNER JOIN `seg_type_confinement` b 
                                    ON a.`confinetype_id` = b.`confinetype_id` 
                                  INNER JOIN seg_hcare_confinetype c 
                                    ON c.`confinetype_id` = b.`confinetype_id` 
                                WHERE a.`encounter_nr` = ".$db->qstr($enc_nr)."
                                   AND c.`bsked_id` = ".BSKED_ID_MEDS."
                                   AND a.is_deleted <> 1
                                   ORDER BY a.`create_time` DESC");

            $xlo = $db->GetOne("SELECT 
                                  c.`amountlimit` 
                                FROM
                                  seg_encounter_confinement a 
                                  INNER JOIN `seg_type_confinement` b 
                                    ON a.`confinetype_id` = b.`confinetype_id` 
                                  INNER JOIN seg_hcare_confinetype c 
                                    ON c.`confinetype_id` = b.`confinetype_id` 
                                WHERE a.`encounter_nr` = ".$db->qstr($enc_nr)."
                                   AND c.`bsked_id` = ".BSKED_ID_XLO."
                                   AND a.is_deleted <> 1
                                    ORDER BY a.`create_time` DESC");
        }
        

        $limit = array('meds' => $meds,'xlo' => $xlo);
        return $limit;
    }

    /**
     * @author Gervie
     * Created on 09/24/2015
     * Get Default Limit if there is no
     * saved encounter limit in the patient.
     * @return array
     **/
    function getDefaultLimit(){
        global $db;

        $meds = $db->GetOne("SELECT
                              c.amountlimit
                            FROM
                              seg_hcare_confinetype c
                            WHERE c.bsked_id = ".BSKED_ID_MEDS);

        $xlo = $db->GetOne("SELECT
                              c.amountlimit
                            FROM
                              seg_hcare_confinetype c
                            WHERE c.bsked_id = ".BSKED_ID_XLO);

        $limit = array('meds' => $meds,'xlo' => $xlo);
        return $limit;
    }

    /**
    * @author Art 
    * Created On 11/22/2014
    * get total additional encounter limit
    * @param string encounter number
    * @return  array
    **/
    function getTotalAdditionalLimit($enc_nr){
        global $db;
        $additional = $db->GetRow("SELECT SUM(a.`amountmed`) AS meds ,SUM(a.`amountxlo`) as xlo FROM seg_additional_limit a WHERE a.`is_deleted` IS NULL AND a.`encounter_nr` = ".$db->qstr($enc_nr));
        return $additional;
    }
    /**
    * @author Art 
    * Created On 11/22/2014
    * get total additional encounter limit
    * @param array data
    * @return  bool
    **/
    function saveAdditionalLimit($data){
        global $db;
        $param = array($data['encounter_nr'],
                       $data['amountmed'],
                       $data['amountxlo'],
                       $data['create_id']);

        $sql = $db->Prepare("INSERT INTO seg_additional_limit (encounter_nr,amountmed,amountxlo,create_id) VALUES (?,?,?,?)");
        $rs = $db->Execute($sql,$param);
        if ($rs) {
            return true;
        }else{
            return false;
        }
    }
    /**
    * @author Art 
    * Created On 11/22/2014
    * get all added additional limit details
    * @param string encounter number
    * @return  array
    **/
    function getAddedLimitDetails($enc_nr){
        global $db;
        $rs = $db->GetAll("SELECT 
                                    sal.`amountmed` AS meds,
                                    sal.`amountxlo` AS xlo,
                                    sal.`create_dt`,
                                    cu.`name`
                                  FROM
                                    seg_additional_limit sal 
                                    INNER JOIN care_users cu 
                                      ON sal.`create_id` = cu.`login_id` 
                                  WHERE sal.`is_deleted` IS NULL 
                                    AND sal.`encounter_nr` = ".$db->qstr($enc_nr));
        return $rs;
    }

    public function isPhs(){
        return $this->checkIfPHS($this->encounter_nr);
    }

    public function getBillAccommodationType(){
        global $db;
        return $db->GetOne("SELECT
                              accommodation_type
                            FROM seg_billing_encounter
                            WHERE encounter_nr=?
                            AND is_deleted IS NULL",$this->encounter_nr);
    }

    public function isSponsoredMember() {
        return /*$this->isCharity() && */$this->memcategory_id == self::SPONSORED_MEMBER;
    }

    public function isHsm() {
        return /*$this->isCharity() && */$this->memcategory_id == self::HOSPITAL_SPONSORED_MEMBER;
    }

    public function isKasamBahay(){
        return /*$this->isCharity() && */$this->memcategory_id == self::KASAM_BAHAY && $this->isEffectiveNbb();
    }

    public function isLifeTimeMember(){
        return /*$this->isCharity() && */$this->memcategory_id == self::LIFETIME_MEMBER && $this->isEffectiveNbb();
    }

    public function isSeniorCitizen(){
        return /*$this->isCharity() && */$this->memcategory_id == self::SENIOR_CITIZEN && $this->isEffectiveNbb();
    }
     public function isPointOfService(){
        return /*$this->isCharity() && */$this->memcategory_id == self::POINT_OF_SERVICE && $this->isEffectiveNbb();
    }

    private function isEffectiveNbb(){
        $billFromDate = self::getEncounterDate($this->encounter_nr);
        return strtotime(self::NBB_EFFECTIVE_DATE) <= strtotime($billFromDate);
    }

    public function getBillingEncounter()
    {
        global $db;
        return $db->GetRow("SELECT * FROM seg_billing_encounter WHERE encounter_nr = ? AND is_deleted IS NULL",$this->encounter_nr);
    }

    public function getDiscount()
    {
        global $db;
        return $db->GetRow("SELECT * FROM seg_billing_discount WHERE bill_nr = ?",$this->old_bill_nr);
    }

    public static function getNbbDiscountIds()
    {   
        global $db;
            $sql = "SELECT
            sm.`memcategory_code` AS nbb ,sm.alt_memcategory_code altnbb
            FROM
            seg_memcategory AS sm 
            WHERE sm.`isnbb` = '1' ";

        if($result=$db->Execute($sql)) {
                        if($result->RecordCount()) {
                                 return $result;
                        } else { return false; }
                } else { return false; }

       //  return array('NBB', 'HSM', 'KSMBHY', 'LM', 'SC','POS');
    }

    public function isNbb()
    {
        $discount = $this->getDiscount();
        $arr_nbb = array();
        if ($discount) {
            $setNBB = self::getNbbDiscountIds();
            if(is_object($setNBB)){
                while ($row = $setNBB->FetchRow()) {

                  array_push($arr_nbb, $row['nbb']);
                  array_push($arr_nbb, $row['altnbb']);

                }

            }
        
          

            return in_array($discount['discountid'],$arr_nbb);

        }
            return false;
//        $accommodationType = $this->getBillAccommodationType();
//
//        if($accommodationType == 2)
//            return false;
//        else
//            return $this->isSponsoredMember() || $this->isHsm() || $this->isKasamBahay() || $this->isLifeTimeMember() || $this->isSeniorCitizen();
    }

    public function getMemberCategoryInfo(){
        global $db;
        return $db->GetRow("SELECT
                              memcategory_id,
                              memcategory_desc,
                              memcategory_code
                            FROM seg_memcategory
                            WHERE memcategory_id = ?",$this->memcategory_id);
    }

    function getEncounterInsuranceInfo($encounter,$hcare_id,$fields='*'){
        global $db;
        $this->sql = "SELECT
                          $fields
                        FROM
                          seg_encounter_insurance AS sei
                          LEFT JOIN seg_insurance_remarks_options AS siro
                            ON sei.remarks = siro.id
                        WHERE encounter_nr = ?
                          AND hcare_id = ?";
        $rs = $db->GetRow($this->sql,array($encounter,$hcare_id));
        if($rs){
            return $rs;
        }else{
            return false;
        }
    }

    function CheckFinalSaveBill($encounter){
        global $db;

        $this->sql = "SELECT bill_frmdte 
                        FROM seg_billing_encounter
                        WHERE is_final = '1'
                        AND ISNULL(is_deleted)
                        AND encounter_nr = ".$db->qstr($encounter_nr);

        if($this->result = $db->GetOne($this->sql)){
            return $this->result;
        }else{
            return false;
        }
    }

    public static function getEncounterDate($encounterNr){
        global $db;
        return $db->GetOne("SELECT encounter_date FROM care_encounter WHERE encounter_nr = ?",$encounterNr);
    }

    /**
     * @author Nick 5-31-2015
     * @return mixed
     */
    public function getLaboratoryItems(){
        global $db;
        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);
        $filter = "";
        $pocfilter = "";
        
        $parameters = array(
            ISSRVD_EFFECTIVITY,
            $this->encounter_nr
        );
        $pocparams = array($this->encounter_nr);
        
        if ($prev_encounter != ''){
            $filter = " OR encounter_nr = ?";
            $parameters[] = $prev_encounter;
            
            $pocfilter = " OR cbg.encounter_nr = ?";
            $pocparams[] = $prev_encounter;
        }
        
        $parameters [] = $this->bill_frmdte;
        $pocparams[] = $this->bill_frmdte;
        
        $parameters [] = $this->charged_date;
        $pocparams[] = $this->charged_date;
        
        $parameters = array_merge($pocparams, $parameters);
                
        $sql = "SELECT DISTINCT (CASE WHEN @N IS NULL THEN @N := 0 ELSE @N := @N +1 END) refno, DATE(cbg.reading_dt) serv_dt, TIME(cbg.reading_dt) serv_tm, o.service_code, 
                    s.name service_desc, s.group_code, sg.name group_desc, 1 qty, ((o.unit_price * o.quantity) - IFNULL(oh.discount, 0))/o.quantity serv_charge, 'POC' source,                     
                    IF(cbg.readby_name IS NULL OR cbg.readby_name = '', (SELECT UCASE(a.name) FROM care_users AS a WHERE login_id = oh.`create_id`), UCASE(cbg.readby_name)) encoder, DATE_FORMAT(reading_dt,'%M %d, %Y %r' ) time_encoded 
                    FROM (seg_cbg_reading cbg INNER JOIN seg_hl7_message_log hl7 ON cbg.`log_id` = hl7.`log_id`) 
                    LEFT JOIN ((seg_poc_order_detail o INNER JOIN seg_poc_order oh ON o.refno = oh.refno) INNER JOIN seg_lab_services s ON o.`service_code` = s.`service_code` 
                    INNER JOIN seg_lab_service_groups sg ON s.`group_code` = sg.`group_code`) ON o.`refno` = hl7.`ref_no`
                    WHERE (cbg.`encounter_nr` = ?{$pocfilter}) AND (oh.is_cash = 0 OR oh.is_cash IS NULL)                          
                        AND cbg.reading_dt BETWEEN ? AND DATE_SUB(?, INTERVAL 1 second)
                 UNION ALL ";      
        $sql .= "SELECT
                  lh.refno, serv_dt, serv_tm, ld.service_code, ls.name AS service_desc, ls.group_code,
                  lsg.name AS group_desc, ld.quantity AS qty, ld.price_charge AS serv_charge, 'LB' AS source,
                  IFNULL((SELECT UPPER(a.name) FROM care_users AS a WHERE login_id = lh.create_id),UPPER(lh.create_id)) AS encoder,
                  DATE_FORMAT(lh.create_dt,'%M %d %Y %r' ) AS time_encoded
                FROM
                seg_lab_serv AS lh
                INNER JOIN seg_lab_servdetails AS ld
                  ON lh.refno = ld.refno
                INNER JOIN seg_lab_services AS ls
                  ON ld.service_code = ls.service_code
                INNER JOIN seg_lab_service_groups AS lsg ON ls.group_code = lsg.group_code
                WHERE
                (CASE WHEN serv_dt >= DATE(?) THEN ld.is_served ELSE 1 END) AND
                UPPER(TRIM(ld.status)) <> 'DELETED' AND lh.is_cash = 0
                AND (
                  ld.request_flag IS NULL OR
                  ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)
                )
                AND (encounter_nr = ? {$filter}) AND upper(trim(lh.status)) <> 'DELETED'
                AND (
                    STR_TO_DATE(CONCAT(DATE_FORMAT(serv_dt, '%Y-%m-%d'), ' ', DATE_FORMAT(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= ?
                    AND
                    STR_TO_DATE(CONCAT(DATE_FORMAT(serv_dt, '%Y-%m-%d'), ' ', DATE_FORMAT(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < ?
                )
                GROUP BY lh.refno, serv_dt, serv_tm, ld.service_code, ls.name, ls.group_code, lsg.name";
        return $db->GetAll($sql,$parameters);
    }

    /**
     * @author Nick 5-31-2015
     * @return mixed
     */
    public function getRadiologyItems(){
        global $db;
        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);
        $filter = "";
        $parameters = array(
            ISSRVD_EFFECTIVITY,
            $this->encounter_nr
        );
        if ($prev_encounter != ''){
            $filter = " OR encounter_nr = ?";
            $parameters[] = $prev_encounter;
        }
        $parameters [] = $this->bill_frmdte;
        $parameters [] = $this->charged_date;

        $sql = "SELECT
                  rh.refno, rh.request_date AS serv_dt, rh.request_time AS serv_tm,
                  rd.service_code, rs.name as service_desc, rs.group_code,
                  rsg.name AS group_desc, COUNT(rd.service_code) AS qty,
                  (SUM(rd.price_charge)/COUNT(rd.service_code)) AS serv_charge, 'RD' AS source,
                  (SELECT UPPER(a.name) FROM care_users AS a WHERE login_id = rh.create_id) AS encoder,
                  DATE_FORMAT(rh.create_dt,'%M %d %Y %r' ) AS time_encoded
                FROM
                seg_radio_serv AS rh
                INNER JOIN care_test_request_radio AS rd
                  ON rh.refno = rd.refno
                INNER JOIN seg_radio_services AS rs
                  ON rd.service_code = rs.service_code
                INNER JOIN seg_radio_service_groups AS rsg
                  ON rs.group_code = rsg.group_code
                WHERE  rh.fromdept='RD' AND 
                (CASE WHEN rh.request_date >= DATE(?) THEN rd.is_served ELSE 1 END) AND
                UPPER(TRIM(rd.status)) <> 'DELETED' AND rh.is_cash = 0
                AND (rd.request_flag IS NULL OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0))
                AND (encounter_nr = ? $filter)
                AND UPPER(TRIM(rh.status)) <> 'DELETED' AND UPPER(TRIM(rd.status)) <> 'DELETED'
                AND (
                    STR_TO_DATE(CONCAT(DATE_FORMAT(rh.request_date, '%Y-%m-%d'), ' ', DATE_FORMAT(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= ?
                    AND
                    STR_TO_DATE(CONCAT(DATE_FORMAT(rh.request_date, '%Y-%m-%d'), ' ', DATE_FORMAT(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < ?
                ) 
                GROUP BY rh.refno, rh.request_date, rh.request_time, rd.service_code, rs.name, rs.group_code, rsg.name";
        return $db->GetAll($sql,$parameters);
    }
    // Added by MAtsuu 08152018
     public function getOBGyneItems(){
         global $db;
        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);
        $filter = "";
        $parameters = array(
            ISSRVD_EFFECTIVITY,
            $this->encounter_nr
        );
        if ($prev_encounter != ''){
            $filter = " OR encounter_nr = ?";
            $parameters[] = $prev_encounter;
        }
        $parameters [] = $this->bill_frmdte;
        $parameters [] = $this->charged_date;

        $sql = "SELECT
                  rh.refno, rh.request_date AS serv_dt, rh.request_time AS serv_tm,
                  rd.service_code, rs.name as service_desc, rs.group_code,
                  rsg.name AS group_desc, COUNT(rd.service_code) AS qty,
                  (SUM(rd.price_charge)/COUNT(rd.service_code)) AS serv_charge, 'OBGUSD' AS source,
                  (SELECT UPPER(a.name) FROM care_users AS a WHERE login_id = rh.create_id) AS encoder,
                  DATE_FORMAT(rh.create_dt,'%M %d %Y %r' ) AS time_encoded
                FROM
                seg_radio_serv AS rh
                INNER JOIN care_test_request_radio AS rd
                  ON rh.refno = rd.refno
                INNER JOIN seg_radio_services AS rs
                  ON rd.service_code = rs.service_code
                INNER JOIN seg_radio_service_groups AS rsg
                  ON rs.group_code = rsg.group_code
                WHERE  rh.fromdept='OBGUSD' AND 
                (CASE WHEN rh.request_date >= DATE(?) THEN rd.is_served ELSE 1 END) AND
                UPPER(TRIM(rd.status)) <> 'DELETED' AND rh.is_cash = 0
                AND (rd.request_flag IS NULL OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0))
                AND (encounter_nr = ? $filter)
                AND UPPER(TRIM(rh.status)) <> 'DELETED' AND UPPER(TRIM(rd.status)) <> 'DELETED'
                AND (
                    STR_TO_DATE(CONCAT(DATE_FORMAT(rh.request_date, '%Y-%m-%d'), ' ', DATE_FORMAT(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= ?
                    AND
                    STR_TO_DATE(CONCAT(DATE_FORMAT(rh.request_date, '%Y-%m-%d'), ' ', DATE_FORMAT(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < ?
                ) 
                GROUP BY rh.refno, rh.request_date, rh.request_time, rd.service_code, rs.name, rs.group_code, rsg.name";
        return $db->GetAll($sql,$parameters);
    }
    // Ended here..

    /**
     * @author Nick 5-31-2015
     * @return mixed
     */
    public function getSupplyItems(){
        global $db;
        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);
        $filter = "";
        if ($prev_encounter != ''){
            $filter = " OR encounter_nr = ?";
            $parameters = array(
                $this->encounter_nr,
                $prev_encounter,
                $this->encounter_nr,
                $prev_encounter,
                $this->encounter_nr,
                $prev_encounter,
                $this->bill_frmdte,
                $this->charged_date,
                $this->encounter_nr,
                $prev_encounter,
                $this->bill_frmdte,
                $this->charged_date
            );
        }else{
            $parameters = array(
                $this->encounter_nr,
                $this->encounter_nr,
                $this->encounter_nr,
                $this->bill_frmdte,
                $this->charged_date,
                $this->encounter_nr,
                $this->bill_frmdte,
                $this->charged_date
            );
        }
        $sql = "SELECT
                  ph.refno, DATE(ph.orderdate) AS serv_dt,
                  TIME(ph.orderdate) AS serv_tm, pd.bestellnum AS service_code,
                  artikelname AS service_desc, 'SU' AS group_code,
                  'Supplies' AS group_desc, pd.quantity - IFNULL(spri.quantity, 0) as qty,
                  pricecharge AS serv_charge, 'SU' AS source,
                  (SELECT UPPER(a.name) FROM care_users AS a WHERE login_id = ph.create_id) AS encoder,
                  DATE_FORMAT(ph.create_time,'%M %d %Y %r' ) AS time_encoded
                FROM
                seg_pharma_orders AS ph
                INNER JOIN seg_pharma_order_items pd
                  ON ph.refno = pd.refno AND pd.serve_status <> 'N' AND pd.request_flag IS NULL
                LEFT JOIN `seg_type_charge_pharma` stc ON ph.`charge_type`=stc.`id`
                INNER JOIN care_pharma_products_main AS p
                  ON pd.bestellnum = p.bestellnum AND p.prod_class = 'S'
                LEFT JOIN (
                  SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
                  FROM seg_pharma_return_items AS rd
                  INNER JOIN seg_pharma_returns AS rh
                    ON rd.return_nr = rh.return_nr AND (rh.encounter_nr=? $filter)
                  WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = ? $filter) AND rd.ref_no = oh.refno)
                  GROUP BY rd.ref_no, rd.bestellnum
                ) AS spri
                ON pd.refno = spri.ref_no AND pd.bestellnum = spri.bestellnum
                WHERE (encounter_nr=? $filter) AND is_cash = 0
                AND (
                  STR_TO_DATE(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= ?
                  AND STR_TO_DATE(ph.orderdate, '%Y-%m-%d %H:%i:%s') < ?
                )
                AND (pd.quantity - ifnull(spri.quantity, 0)) > 0 AND stc.`is_excludedfrombilling`=0

                UNION ALL

                SELECT
                    mph.refno, DATE(mph.chrge_dte) AS serv_dt,
                    TIME(mph.chrge_dte) AS serv_tm, mphd.bestellnum AS service_code,
                    artikelname AS service_desc, 'MS' AS group_code,
                    'Supplies' AS group_desc, sum(quantity) AS qty, unit_price AS serv_charge, 'MS' AS source,
                    IFNULL(UPPER(mphd.create_id), UPPER(mph.create_id)) AS encoder,
                    IFNULL(DATE_FORMAT(mphd.create_dt,'%M %d %Y %r' ), DATE_FORMAT(mph.create_dt,'%M %d %Y %r' )) AS time_encoded
                FROM
                seg_more_phorder_details AS mphd
                INNER JOIN seg_more_phorder AS mph
                    ON mphd.refno = mph.refno
                INNER JOIN care_pharma_products_main AS p
                    ON mphd.bestellnum = p.bestellnum AND p.prod_class = 'S'
                WHERE (encounter_nr = ? $filter)
                AND (
                    STR_TO_DATE(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= ?
                    AND STR_TO_DATE(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < ?
                )
                AND mphd.is_deleted != 1
                GROUP BY mph.refno, mph.chrge_dte, mphd.bestellnum, artikelname";

        return $db->GetAll($sql,$parameters);
    }

    /**
     * @author Nick 5-31-2015
     * @return mixed
     */
    public function getOtherItems(){
        global $db;
        $prev_encounter = $this->getPrevEncounterNr($this->encounter_nr);
        $filter = "";
        $filter2 = "";
        if ($prev_encounter != ''){
            $filter = "OR encounter_nr=?";
            $filter2 = "OR sos.encounter_nr=?";
            $parameters = array(
                $this->encounter_nr,
                $prev_encounter,
                $this->bill_frmdte,
                $this->charged_date,
                $this->encounter_nr,
                $prev_encounter,
                $this->bill_frmdte,
                $this->charged_date,
            );
        }else{
            $parameters = array(
                $this->encounter_nr,
                $this->bill_frmdte,
                $this->charged_date,
                $this->encounter_nr,
                $this->bill_frmdte,
                $this->charged_date,
            );
        }
        $sql = "SELECT
                    m.refno, DATE(m.chrge_dte) AS serv_dt,
                    TIME(m.chrge_dte) AS serv_tm, md.service_code,
                    ms.name AS service_desc, '' AS group_code,
                    'Others' AS group_desc, SUM(md.quantity) AS qty,
                    (SUM(chrg_amnt * md.quantity)/SUM(md.quantity)) AS serv_charge,
                    'OA' AS source,
                    IFNULL((SELECT UPPER(name) FROM care_users WHERE login_id=m.create_id),IF(md.create_id IS NOT NULL, UPPER(md.create_id), UPPER(m.create_id))) AS encoder,
                    IFNULL(DATE_FORMAT(md.`create_dt`,'%M %d %Y %r' ), DATE_FORMAT(m.`create_dt`,'%M %d %Y %r' )) AS time_encoded
                FROM
                seg_misc_service AS m
                INNER JOIN seg_misc_service_details AS md
                    ON m.refno = md.refno
                INNER JOIN seg_other_services AS ms
                    ON md.service_code = ms.alt_service_code
                WHERE (encounter_nr = ? $filter) AND md.request_flag IS NULL AND m.is_cash = 0
                AND (
                    str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= ?
                    AND str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') < ?
                ) AND md.is_deleted != 1
                GROUP BY m.refno, m.chrge_dte, md.service_code, ms.name

                UNION ALL

                SELECT
                  sos.refno, DATE(eqh.order_date) AS serv_dt,
                  TIME(eqh.order_date) AS serv_tm, eqd.equipment_id,
                  artikelname, '' AS group_code,
                  'Equipment' AS group_desc, SUM(number_of_usage) AS qty,
                  (SUM(discounted_price * number_of_usage)/SUM(number_of_usage)) AS uprice,
                  'OE' AS SOURCE, eqh.created_id AS encoder,
                  DATE_FORMAT(eqh.created_date,'%M %d %Y %r' ) AS time_encoded
                FROM
                seg_equipment_orders AS eqh
                INNER JOIN seg_equipment_order_items AS eqd
                  ON eqh.refno = eqd.refno
                LEFT JOIN seg_ops_serv AS sos
                  ON sos.refno = eqh.request_refno
                INNER JOIN care_pharma_products_main AS cppm
                  ON cppm.bestellnum = eqd.equipment_id
                WHERE (sos.encounter_nr = ? $filter2)
                AND (
                  STR_TO_DATE(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= ?
                  AND STR_TO_DATE(eqh.order_date, '%Y-%m-%d %H:%i:%s') < ?
                )
                GROUP BY sos.refno, eqh.order_date, eqd.equipment_id, artikelname";
        return $db->GetAll($sql,$parameters);
    }

    /**
     * @return array|null
     */
    public function getCreditCollectionSettlements()
    {
        return CreditCollection::findCreditCollectionByEncounter(
            $this->encounter_nr,
            'ledger.amount,ledger.control_nr,IFNULL(accountType.alt_name, ledger.pay_type) AS alt_name',
            'AND entry_type="debit" AND ledger.id NOT IN (SELECT ref_no FROM seg_credit_collection_ledger WHERE entry_type = "credit" AND encounter_nr = ?)'
        );
    }

    function getTransactionPrebills($encounter_nr)
    {
        global $db;
        //$db->debug = true;

        $this->sql = $db->Prepare("SELECT 
                                        sdl.`ref_no`,
                                      sdl.`bill_nr`,
                                      sdl.`amount`,
                                      sdl.`pay_type`,
                                      sdl.`control_nr`,
                                      sdl.`description`
                                    FROM
                                      `seg_dialysis_prebill` pb 
                                      LEFT JOIN seg_dialysis_transaction t 
                                        ON t.`transaction_nr` = pb.`bill_nr` 
                                      LEFT JOIN care_encounter ce 
                                        ON ce.`encounter_nr` = pb.`encounter_nr` 
                                      LEFT JOIN seg_pay_request pr 
                                        ON pr.`service_code` = pb.`bill_nr` 
                                        AND pr.`ref_source` = 'db' 
                                      LEFT JOIN seg_pay sp 
                                        ON sp.`or_no` = pr.`or_no`
                                      LEFT JOIN seg_dialysis_ledger sdl ON pb.`bill_nr`=sdl.`bill_nr`
                                    WHERE pb.`encounter_nr` = ?
                                      AND pb.`request_flag` IN ('cmap', 'lingap', 'paid', 'manual') 
                                      AND sp.`cancel_date` IS NULL 
                                      AND sdl.`is_deleted` <> 1");

        #echo $this->sql;
        $result = $db->Execute($this->sql, $encounter_nr);

        if ($result != FALSE) {
            return $result;
        } else {
            $this->error_msg = $db->ErrorMsg();
            return FALSE;
        }
    }

    function checkCreditCollectionNBB($encounter_nr, $bill_nr) {
        global $db;

        return $db->GetOne("SELECT
                              encounter_nr,
                              bill_nr,
                              entry_type,
                              pay_type
                            FROM
                              seg_credit_collection_ledger
                            WHERE pay_type = 'nbb'
                              AND entry_type = 'debit'
                              AND is_deleted = 0
                              AND bill_nr =  ".$db->qstr($bill_nr)."
                              AND encounter_nr = ".$db->qstr($encounter_nr));
    }

    function removeInsuranceCreditCollectionNBB($encounter_nr, $bill_nr) {
        global $db;

        $this->sql = "UPDATE
                          seg_credit_collection_ledger
                        SET
                          is_deleted = 1
                        WHERE pay_type = 'nbb'
                          AND entry_type = 'debit'
                          AND is_deleted = 0
                          AND bill_nr = ".$db->qstr($bill_nr)."
                          AND encounter_nr = ".$db->qstr($encounter_nr);

        if($this->result = $db->Execute($this->sql)) {
            return $this->result;
        } else {
            return false;
        }
    }

    function checkExistingInsuranceCreditCollectionNBB($encounter_nr) {
        global $db;
        return $db->GetOne("SELECT
                              sei.encounter_nr
                            FROM
                              seg_encounter_insurance AS sei
                              INNER JOIN seg_encounter_insurance_memberinfo AS seim
                            WHERE sei.hcare_id = 18
                              AND seim.member_type IN ('HSM', 'K', 'PS', 'SC', 'I')
                              AND sei.encounter_nr = ".$db->qstr($encounter_nr));
    }

    function HasNbbInsurance($encounter_nr){
        global $db;

        return $db->GetOne("SELECT
                              sem.encounter_nr
                            FROM
                              seg_encounter_memcategory AS sem
                              INNER JOIN seg_memcategory AS smc
                              ON smc.memcategory_id = sem.memcategory_id
                              INNER JOIN seg_encounter_insurance AS sei
                              ON sem.encounter_nr = sei.encounter_nr
                            WHERE sei.hcare_id = ".$db->qstr(PHIC_ID)."
                              AND smc.isnbb = '1' AND
                              sem.encounter_nr =".$db->qstr($encounter_nr));
    }

    /**
     * @author Gervie 12/22/2015
     *
     * Delete Reasons
     */
    function getDeleteReasons(){
        global $db;

        return $db->GetAll("SELECT * FROM seg_billing_delete_reasons ORDER BY reason_description ASC");
    }

    /**
     * @author Gervie 12/29/2015
     *
     * Get Billing Encounter History
     */
    function getBillHistory($enc){
        global $db;
        $enc_nr = $db->qstr($enc);

        return $db->GetAll("SELECT bill_nr, history FROM seg_billing_encounter WHERE encounter_nr=".$enc_nr);
    }

    /**
     * @author Carriane 07/25/17
     *
     * Get Caserate History
     */
    function getCaseratelHistory($enc){
        global $db;
        $enc_nr = $db->qstr($enc);

        return $db->GetOne("SELECT history FROM seg_caserate_trail WHERE encounter_nr=".$enc_nr);
    }

    /*
    added by julius 01/09/2017

    */
        function getnotehistory($pidan){
        global $db;
        $pid_an = $db->qstr($pidan);

        return $db->GetAll("SELECT history FROM seg_billing_patient_notes WHERE pid=".$pid_an);
    } 
/*end
*/

    # added by : syboy 10/11/2015
    # return all case rate in this encounter
    public function getCaseRateRVSCode($enc){
        global $db;

        #optimize code by VAN 11/16/2016
        $rvs_code = implode("','", array(
                                    evisceration_without_implant,
                                    evisceration_implant,
                                    enucleation_without_implant,
                                    enucleation_implant_not_attached,
                                    enucleation_implant_attached,
                                    exenteration_orbit_without_skin_graft_content_only,
                                    exenteration_orbit_without_skin_graft,
                                    exenteration_orbit_without_skin_graft_muscle_flap,
                                    66840,
                                    66850,
                                    66852,
                                    66920,
                                    66930,
                                    66940,
                                    66982,
                                    66983,
                                    66984,
                                    66987
                                    )
                             );
        $rvs_code = "'".$rvs_code."'";

        $pid = $db->GetOne("SELECT pid FROM care_encounter WHERE encounter_nr = ?",$enc);

        $this->sql = "SELECT 
                      sbe.encounter_nr,
                      sbe.bill_nr,
                      sbc.package_id AS code,
                      sbc.laterality AS prevlaterality 
                    FROM
                      care_encounter ce 
                      INNER JOIN seg_billing_encounter sbe
                        ON ce.`encounter_nr` = sbe.`encounter_nr`
                      INNER JOIN seg_billing_caserate sbc 
                        ON sbc.`bill_nr` = sbe.`bill_nr`
                      INNER JOIN seg_misc_ops smp 
                        ON smp.`encounter_nr` = sbe.`encounter_nr` 
                      INNER JOIN seg_misc_ops_details smpd 
                        ON smpd.`refno` = smp.`refno` 
                    WHERE ce.`pid` = ".$db->qstr($pid)."
                      AND sbe.`is_deleted` IS NULL 
                      #AND ce.encounter_date >= STR_TO_DATE('2015-07-15 00:00:01', '%Y-%m-%d %H:%i:%s')
                      AND sbc.`package_id` = smpd.`ops_code` 
                      AND sbc.`laterality` = smpd.`laterality` 
                      AND sbc.`package_id` IN ($rvs_code)";

        if ($result = $db->Execute($this->sql)) {
            if ($result->RecordCount()) {
                return $result;
            }
        }

        return false;
    }   

    /**
     * @author Gervie 03-23-2016
     * Monitors every action done by the biller.
     * Types of Action: 0 - Temporary, 1 - Final, 2 - Deleted, 3 - Rebilled.
     */
    function saveBillingTransaction($bill_nr, $encounter_nr, $date_start, $action) {
        global $db;

        switch ($action) {
            case 0:
                $action_taken = 'tentative';
                break;
            case 1:
                $action_taken = 'final';
                break;
            case 2:
                $action_taken = 'deleted';
                break;
            case 3:
                $action_taken = 'rebilled';
                break;
            case 4:
                $action_taken = 'payward_settle';
                break;
            default:
                $action_taken = 'tentative';
                break;
        }

        $sql = "INSERT INTO seg_billing_transactions
                      (bill_nr,
                       encounter_nr,
                       biller,
                       action_taken,
                       action_date,
                       action_date_finished)
                VALUES
                      (". $db->qstr($bill_nr) . ",
                      " . $db->qstr($encounter_nr) . ",
                      " . $db->qstr($_SESSION['sess_temp_userid']) . ",
                      " . $db->qstr($action_taken) . ",
                      " . $db->qstr($date_start) . ", 
                      " . $db->qstr(date('Y-m-d H:i:s')) . ")";

        $res = $db->Execute($sql);

        if($res) {
            $ok = true;
        }
        else {
            $ok = false;
            $this->error_msg = "ERROR: " . $db->ErrorMsg();
        }

        if($ok) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @author Gervie 01/26/2016
     *
     * Saving patient note.
     */
    function savePatientNote($pid, $note="",$encr_nr){
        global $db, $HTTP_SESSION_VARS;
        $enc = $db->qstr($encr_nr);
        $hrn = $db->qstr($pid);
        $notes = $db->qstr($note);
        $create_id = $db->qstr($HTTP_SESSION_VARS['sess_user_name']);
        $create_dt = $db->qstr(date('Y-m-d'));
        $history = $db->qstr("Created by ".$HTTP_SESSION_VARS['sess_user_name']." | " . date('Y-m-d H:i:s') . "\n");

        $this->sql = "INSERT INTO seg_billing_patient_notes (pid, encounter_nr ,note, create_id, create_dt, history)
                        VALUES ($hrn, $enc ,$notes, $create_id, $create_dt, $history)";

        if($res=$db->Execute($this->sql))
            return true;
        else
            return false;
    }
    //----julz--01-10-2017
    function updatePatientNoteinempty($pid, $note){
        global $db, $HTTP_SESSION_VARS;

    
        $stataction="Created by";
        $this->sql = "UPDATE seg_billing_patient_notes SET ".
                     " note = ".$db->qstr($note).
                     ", modify_id = ".$db->qstr($HTTP_SESSION_VARS['sess_user_name']).
                     ", modify_dt = ".$db->qstr(date('Y-m-d')).
                     ", history = ".$this->ConcatHistory($stataction.$HTTP_SESSION_VARS['sess_user_name']." | ".date('Y-m-d H:i:s')."\n").
                     " WHERE pid = ".$db->qstr($pid);

        if($res=$db->Execute($this->sql))
            return true;
        else
            return false;
    }
    //----end
    function updatePatientNote($pid, $note){
        global $db, $HTTP_SESSION_VARS;

        /*modified by julz add 01-09-2017*/
        $stataction="";
        if(strlen(trim($note))==0)
        {
            $stataction="Deleted by ";
        }
        else
        {
            $stataction="Modified by ";
        }
        /**/
        $this->sql = "UPDATE seg_billing_patient_notes SET ".
                     " note = ".$db->qstr($note).
                     ", modify_id = ".$db->qstr($HTTP_SESSION_VARS['sess_user_name']).
                     ", modify_dt = ".$db->qstr(date('Y-m-d')).
                     ", history = ".$this->ConcatHistory($stataction.$HTTP_SESSION_VARS['sess_user_name']." | ".date('Y-m-d H:i:s')."\n").
                     " WHERE pid = ".$db->qstr($pid);

        if($res=$db->Execute($this->sql))
            return true;
        else
            return false;
    }

    function getPatientNote($pid){
        global $db;

        $note = $db->GetOne("SELECT note FROM seg_billing_patient_notes WHERE pid=".$db->qstr($pid));

        return $note;
    }

    /**
     * @author Gervie 04/14/2016
     *
     * Saving PHIC TEMP Transaction.
     */
    function savePHICTemp($data){
        global $db, $HTTP_SESSION_VARS;

        $bill_nr = $db->qstr($data['bill_nr']);
        $encounter_nr = $db->qstr($data['encounter_nr']);
        $phic_type = $db->qstr($data['member_type']);
        $biller = $db->qstr($HTTP_SESSION_VARS['sess_user_name']);
        $action_date = $db->qstr(date('Y-m-d H:i:s'));

        $this->sql = "INSERT INTO seg_billing_temp_phic (bill_nr, encounter_nr, phic_type, biller, action_date)
                        VALUES ($bill_nr, $encounter_nr, $phic_type, $biller, $action_date)";

        if($res=$db->Execute($this->sql))
            return true;
        else
            return false;
    }
    // Added by Joy 06-15-2016
    function getPHICNumber($enc_nr){
        global $db;
        $phic = $db->GetOne("SELECT insurance_nr FROM seg_encounter_insurance_memberinfo WHERE encounter_nr=".$db->qstr($enc_nr));
        return $phic;
    }
    // end by Joy

    /**
     * Added by Gervie 03-19-2017
     * Check if encounter used high flux machine
     */
    function hasHighFlux($encounter_nr) {
        global $db;

        $getTransactions = $db->getOne("SELECT GROUP_CONCAT(\"'\", bill_nr, \"'\") FROM
                                        seg_dialysis_prebill 
                                        WHERE encounter_nr = {$db->qstr($encounter_nr)}
                                        AND request_flag IN ('paid','manual')");

        $highFlux =  $db->getOne("SELECT dd.* FROM
                                    seg_dialysis_transaction dt 
                                  INNER JOIN seg_dialysis_dialyzer dd 
                                    ON dd.dialyzer_serial_nr = dt.dialyzer_serial_nr 
                                  WHERE dt.transaction_nr IN ({$getTransactions}) 
                                    AND dd.dialyzer_id = '".HIGH_FLUX."'");

        if($highFlux) {
            return true;
        }

        return false;
    }

    // Added by Jeff 02-14-18 for checking of doctor accreditaion in PHIC.
    function getCheckDoctorAccreditation($dr_nr)
    {
        global $db;
        $drCheck =  $db->getOne("SELECT 
                              IFNULL(
                                NOW() BETWEEN sda.`accreditation_start` 
                                AND sda.`accreditation_end`,
                                '0'
                              ) AS DrCheck 
                            FROM
                              `seg_dr_accreditation` AS sda 
                            WHERE sda.`dr_nr` =".$db->qstr($dr_nr));

        if($drCheck == 1) {
            return true;
        }
        return false;
    }

    function getExtractedAccommodationList($accommodations){
        $a = 0;
        $ward_room = array();
        foreach($accommodations as $i => $accommodation){
            if($accommodations[$i]['name'] != NULL){

                $temp_ward_room = $accommodations[$i]['ward_id']."_".$accommodations[$i]['room'];

                if(!in_array($temp_ward_room, $ward_room)){
                    $ward_room[$a] = $temp_ward_room;
                    $details[$a] = $accommodations[$i];
                    $a++;
                }else{
                    $index = array_keys($ward_room, $temp_ward_room);
                    $found = 0;
                    if(count($index) > 1){
                        foreach($index as $data){
                            if((strtotime($details[$data]['date_to']) == strtotime($accommodations[$i]['date_from'])) && ($temp_ward_room == $accommodations[$i-1]['ward_id']."_".$accommodations[$i-1]['room'])){
                                $details[$data]['date_to'] = $accommodations[$i]['date_to'];
                                $details[$data]['hrs_stay'] += $accommodations[$i]['hrs_stay'];
                                $found = 1;
                            }
                        }

                        if(!$found){
                            $details[$a] = $accommodations[$i];
                            $ward_room[$a] = $temp_ward_room;
                            $a++;
                        }
                    }else{
                        if((strtotime($details[$index[0]]['date_to']) == strtotime($accommodations[$i]['date_from'])) && ($temp_ward_room == $accommodations[$i-1]['ward_id']."_".$accommodations[$i-1]['room'])){

                                $temp_date_to = $accommodations[$i]['date_to'];
                                $temp_time_to = $accommodations[$i]['time_to'];
                                if($accommodations[$i]['date_to'] == '0000-00-00'){
                                    $temp_date_to = date("Y-m-d");
                                    $temp_time_to = date("H:m:s");
                                }
                                
                                if($temp_date_to > $details[$index[0]]['date_to']){
                                    $details[$index[0]]['date_to'] = $temp_date_to;
                                    $details[$index[0]]['time_to'] = $temp_time_to;
                                    $details[$index[0]]['hrs_stay'] += $accommodations[$i]['hrs_stay'];
                                }
                                $details[$index[0]]['source'] = $accommodations[$i]['source'];
                                $details[$index[0]]['status'] = $accommodations[$i]['status'];
                                // echo "<pre>" . print_r($accommodations[$i],true) . "</pre>";exit();
                        }else{
                            $details[$a] = $accommodations[$i];
                            $ward_room[$a] = $temp_ward_room;
                            $a++;
                        }
                    }
                }
            }
        } // end foreach
        
        return $details;
    }

    // Unknown
    function getEncounterDepartment($enc){
        global $db;
        return $db->GetOne("SELECT consulting_dept_nr FROM care_encounter WHERE encounter_nr = ?",$enc);
    }

    // Unknown
    function getBill_nr($year)
    {
        global $db;

        $insurance_no = $this->getInsuranceNumber($this->encounter_nr);
        $pid = $db->GetOne("SELECT pid FROM care_encounter WHERE encounter_nr = ".$db->qstr($this->encounter_nr));

        return $db->GetAll("SELECT * FROM seg_confinement_tracker sct 
                    WHERE sct.`pid` = ".$db->qstr($pid)."
                      AND sct.`insurance_nr`  = ".$db->qstr($insurance_no)." 
                      AND sct.`current_year` = ".$db->qstr($year)." 
                      AND sct.`hcare_id` = 18
                      ORDER BY sct.`bill_nr` DESC");


    }

    function hasPaywardAccom($enc){
        global $db;

        $this->sql = "SELECT ce.encounter_nr, ce.`current_ward_nr`,cw.accomodation_type
                        FROM care_encounter AS ce
                        INNER JOIN care_ward AS cw ON ce.current_ward_nr = cw.nr
                        WHERE ce.encounter_nr = ".$db->qstr($enc)." AND cw.`accomodation_type` = '2'
                        UNION
                        SELECT sela.encounter_nr, sela.group_nr, cw.accomodation_type
                        FROM seg_encounter_location_addtl AS sela
                        INNER JOIN care_ward AS cw ON sela.group_nr = cw.nr 
                        WHERE sela.encounter_nr = ".$db->qstr($enc)." AND cw.`accomodation_type` = '2'
                        AND sela.is_deleted != '1'
                        UNION
                        SELECT sel.encounter_nr, sel.group_nr, cw.accomodation_type
                        FROM care_encounter_location AS sel
                        INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr 
                        WHERE sel.encounter_nr = ".$db->qstr($enc)." AND cw.`accomodation_type` = '2'
                        AND sel.is_deleted != '1'
                        ";
                 
        return $db->GetRow($this->sql);
    }
    
     function isCovidSeasons(){
        global $db;
        return $db->GetOne("SELECT ccg.`value` from `care_config_global` as ccg WHERE ccg.`type`= 'covid_season'");
    }
}//end class billing