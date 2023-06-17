<?php
require('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');

class CreditCollection {

	var $sql;

	var $tb_seg_credit_collection = 'seg_credit_collection_ledger';
	var $fld_credit_collection = array(
		"ref_no",
		"encounter_nr",
		"bill_nr",
		"entry_type",
		"amount",
		"pay_type",
		"control_nr",
		"description",
		"create_id",
		"modify_id",
		"create_time",
		"modify_time",
		"history",
		"is_deleted",
		"is_final"
	);

    function CreditCollection()
    {
      /* $this->coretable = $this->tb_seg_credit_collection;
        $this->ref_array = $this->fld_credit_collection;*/
    }

    /**
     * Get total amount collected filter by `encounter` and `pay_type`
     * @param $type
     * @param $nr
     * @return bool
     */
    function getTotalAmountByPayTypeAndEncnr($type, $nr)
	{
		global $db;

		$this->sql = "
			SELECT SUM(amount) AS amount FROM seg_credit_collection_ledger
			WHERE encounter_nr = '$nr' AND pay_type = '$type' AND is_deleted = '0'
		";

		$res = $db->Execute($this->sql);

		if ($res) {
			return $res->FetchRow();
		} else {
			return false;
		}
	}

    /**
     * This will return active items from credit collection ledger
     * @param $nr
     * @param $excludeSS
     * @return bool
     */
    function getTotalAmountByEncounter($nr, $excludeSS = false)
	{
		global $db;

        if (!$excludeSS) {
            $query = "
                SELECT MAX(id) AS id, is_deleted, entry_type, SUM(amount) AS amount, SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE 0 END) AS debit_amount, SUM(CASE WHEN entry_type = 'credit' THEN amount ELSE 0 END) AS credit_amount,
               SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE (CASE WHEN entry_type = 'credit' AND pay_type = 'ss' THEN 0 ELSE (CASE WHEN entry_type = 'credit' AND pay_type <> 'ss' THEN - (amount) ELSE 0 END) END) END) AS total, pay_type,
                GROUP_CONCAT(ref_no SEPARATOR '-') AS ref_no,
                GROUP_CONCAT(
                create_id,
                create_time SEPARATOR ' '
                ) AS remarks
                FROM seg_credit_collection_ledger WHERE is_deleted = '0' AND encounter_nr = '$nr' GROUP BY pay_type ORDER BY id DESC;
            ";
        } else {
            $query = "
                SELECT MAX(id) AS id, is_deleted, entry_type, SUM(amount) AS amount, SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE 0 END) AS debit_amount, SUM(CASE WHEN entry_type = 'credit' THEN amount ELSE 0 END) AS credit_amount,
                SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE (CASE WHEN entry_type = 'credit' AND pay_type = 'ss' THEN 0 ELSE (CASE WHEN entry_type = 'credit' AND pay_type <> 'ss' THEN - (amount) ELSE 0 END) END) END) AS total, pay_type,
                GROUP_CONCAT(ref_no SEPARATOR '-') AS ref_no,
                GROUP_CONCAT(
                create_id,
                create_time SEPARATOR ' '
                ) AS remarks
                FROM seg_credit_collection_ledger WHERE is_deleted = '0' AND encounter_nr = '$nr' AND pay_type NOT IN('ss') GROUP BY pay_type ORDER BY id DESC;
            ";
        }
        #echo $query; die;

        $this->sql = $query;
		$res = $db->Execute($this->sql);
		if ($res) {
			return $res->GetAll();
		} else {
			return false;
		}
	}

    /**
     * Returns social service items from credit collection ledger
     * @param $nr
     * @return bool
     */
    function getTotalGrantSSByEncounter($nr)
    {
        global $db;

        $this->sql = "
            SELECT id, MAX(description) as description, is_deleted, entry_type, encounter_nr, bill_nr, SUM(amount) AS amount, SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE 0 END) AS debit_amount, SUM(CASE WHEN entry_type = 'credit' THEN amount ELSE 0 END) AS credit_amount,
            SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE (CASE WHEN entry_type ='credit' THEN -(amount) ELSE 0 END) END) AS total, pay_type,
            GROUP_CONCAT(ref_no SEPARATOR '-') AS ref_no,
            GROUP_CONCAT(
            history,
            create_time SEPARATOR '\n'
            ) AS remarks
            FROM `seg_credit_collection_ledger` `t` WHERE is_deleted = '0' AND encounter_nr = '$nr' AND pay_type ='ss' ORDER by id DESC
        ";

        #echo $this->sql; die;

        $res = $db->Execute($this->sql);

        if ($res) {
            return $res->FetchRow();
        } else {
            return false;
        }
    }

    /**
     * Return collection items by `pay_type` and `encounter_nr`
     * @param $type
     * @param $nr
     * @return bool
     * Edited by: syboy 01/08/2016 : meow
     */
    function getTotalGrantsByTypeAndNr($type,$nr, $controlNr, $entryType = null)
    {
        global $db;

        $query = "
            SELECT id, is_deleted, entry_type, encounter_nr, bill_nr, SUM(amount) AS amount, SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE 0 END) AS debit_amount, SUM(CASE WHEN entry_type = 'credit' THEN amount ELSE 0 END) AS credit_amount,
            SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE (CASE WHEN entry_type ='credit' THEN -(amount) ELSE 0 END) END) AS total, pay_type,
            GROUP_CONCAT(ref_no SEPARATOR '-') AS ref_no,
            GROUP_CONCAT(
            history,
            create_id SEPARATOR '\n'
            ) AS remarks ";

        if (is_null($entryType)) {
            $query .= "
                FROM `seg_credit_collection_ledger` `t` WHERE is_deleted = '0' AND encounter_nr = '$nr' AND pay_type ='$type' AND control_nr LIKE '%$controlNr%'
            ";
        } else {
            $query .= "
                FROM `seg_credit_collection_ledger` `t` WHERE is_deleted = '0' AND encounter_nr = '$nr' AND pay_type ='$type' AND control_nr LIKE '%$controlNr%' AND entry_type = '$entryType'
            ";
        }

        $this->sql = $query;
        // echo $query; die;

        $res = $db->Execute($this->sql);

        if ($res) {
            return $res->FetchRow();
        } else {
            return false;
        }
    }
// added by gelie 10-11-2015
    /**
     * Return the collection item by `pay_type`, `encounter_nr` and 'control_nr'
     * @param $type
     * @param $nr
     * @param $controlNr
     * @return bool
     */
    function getTotalGrantsByControlNr($type,$nr,$controlNr)
    {
        global $db;

        $query = "
            SELECT id, is_deleted, entry_type, encounter_nr, bill_nr, SUM(amount) AS amount, SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE 0 END) AS debit_amount, SUM(CASE WHEN entry_type = 'credit' THEN amount ELSE 0 END) AS credit_amount,
            SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE (CASE WHEN entry_type ='credit' THEN -(amount) ELSE 0 END) END) AS total, pay_type,
            GROUP_CONCAT(ref_no SEPARATOR '-') AS ref_no,
            GROUP_CONCAT(
            history,
            create_id SEPARATOR '\n'
            ) AS remarks 
            FROM `seg_credit_collection_ledger` `t` WHERE is_deleted = '0' AND encounter_nr = '$nr' AND pay_type ='$type' 
                AND control_nr LIKE '%$controlNr%' ";
        
        $this->sql = $query;

        $res = $db->Execute($this->sql);

        if ($res) {
            return $res->FetchRow();
        } else {
            return false;
        }
    }
// end gelie

    /**
     * Return credit collection ledger details by `type`, `encounter_nr`, `entry_type` (optional)
     * @param $type
     * @param $encounterNr
     * @param string $entryType
     * @return bool
     */
    function getCollectionByType($type, $encounterNr, $entryType = 'debit', $isLatest = false)
    {
        global $db;
        $query = '';

        if (!$isLatest) {
            $query = "
                SELECT * FROM seg_credit_collection_ledger
                WHERE encounter_nr = '$encounterNr' AND pay_type = '$type' AND entry_type = '$entryType'
           ";
        } else {
            $query .= "SELECT * FROM seg_credit_collection_ledger
                WHERE encounter_nr = '$encounterNr' AND pay_type = '$type' AND entry_type = '$entryType' ORDER BY create_time DESC LIMIT 1";
        }


        $this->sql = $query;
        $res = $db->Execute($this->sql);

        if ($res) {
            if (!$isLatest)
              return $res->GetAll();
            else
                return $res->FetchRow();
        } else {
            return false;
        }
    }

    /**
     * Checker if can still add an item
     * @param $encounter
     * @param string $payType
     * @return bool
     */
    function isAllowedToCreateMSS($encounter, $payType = 'ss')
    {
        # added by michelle 04-10-2015
        $grants = $this->getTotalAmountByEncounter($encounter);
        $isMSSExist = false;
        $type = array();

        if (!empty($grants)) {
            foreach ($grants as $g) {
                $type[] = $g['pay_type'];
            }

            if ($type[0] == $payType) {
                $isMSSExist = true;
                /*if (count($type) > 1)
                    $isMSSExist = false;
                else
                    $isMSSExist = true;*/
            } else {
                $last = end($type);
                if (in_array($payType, $type)) {
                    $isMSSExist = false;
                } else {
                    $isMSSExist = true;
                }
            }
        } else {
            $isMSSExist = true;
        }

        return $isMSSExist;
    }

    /**
     * Add charity grants on collection ledger
     * @TODO use $db->Replace() on operation searching then update scenario
     * @param $encounter_nr
     */
    function addGrantToCollectionLedger($encounter_nr, $bill_nr, $payType ='ss', $status = 0, $appliedDiscountAmnt = null)
    {
        global $db, $HTTP_SESSION_VARS;
        $user = $HTTP_SESSION_VARS['sess_user_name'];

        # check discount amount / other discount details
        $netSql = "SELECT fn_billing_compute_gross_amount(b.bill_nr) AS gross, fn_billing_compute_net_amount(b.bill_nr) AS net, sbd.discount AS discount,\n".
            "b.request_flag, sbd.discount_amnt, sbd.discountid\n".
            "FROM seg_billing_encounter AS b\n".
            "INNER JOIN care_encounter AS e on e.encounter_nr=b.encounter_nr\n".
            "INNER JOIN seg_billing_discount AS sbd on sbd.bill_nr=b.bill_nr\n".
            "WHERE b.bill_nr ='$bill_nr' AND b.is_deleted IS NULL\n".
            "ORDER BY b.bill_dte DESC\n";

        $row = $db->GetRow($netSql);
        $gross = floatval($row['gross']); // amount minus the coverages and discounts
        $net = floatval($row['net']);
        $discount = floatval($row['discount']); //percent

        $totalGrantsFromLedger = $this->getTotalAmountByEncounter($encounter_nr); # get total amount grants from credit collection ledger by encounter_nr
        $totalGrantSS = $this->getTotalGrantSSByEncounter($encounter_nr); # get total grants by social service type = `qfs`
        //$totalGrantsAppliedDiscount = $this->getTotalGrantsByTypeAndNr('qfsa', $encounter_nr); # get total grants by pay_type = `qfsa` for ss applied discount

        // if (!is_null($totalGrantSS['total'])) {
        //     $refno = $totalGrantSS['ref_no'];
        //     $encounter = $totalGrantSS['encounter_nr'];
        //     $nr = $totalGrantSS['bill_nr'];
        //     $entry_type = 'credit';
        //     $amount = $totalGrantSS['total'];
        //     $payType = $totalGrantSS['pay_type'];
        //     $controlNr = $totalGrantSS['control_nr'];
        //     //$description = $totalGrantSS['description'];
        //     $description = 'Revokedvcvxvxv ' . $totalGrantSS['description'] . ' Php ' . $totalGrantSS['total'];
        //     $createId = $_SESSION['sess_user_name'];
        //     $createTime = date('YmdHis');
        //     $history = $totalGrantSS['description'] . ' Php ' . $totalGrantSS['total'] . ' Revoked on ' . date("Y-m-d h:i:s A") . ' by ' . $user;
        //     $isDel = $totalGrantSS['is_deleted'];

        //     $data = array(
        //         'ref_no' => $refno,
        //         'encounter_nr' => $encounter_nr,
        //         'bill_nr' => $nr,
        //         'entry_type' => $entry_type,
        //         'amount' => $amount,
        //         'pay_type' => $payType,
        //         'control_nr' => $controlNr,
        //         'description' => $description,
        //         'create_id' => $createId,
        //         'create_time' => $createTime,
        //         'history' => $history
        //     );

        //     self::insert($data);

        // }

        $totalGrants = 0;
        foreach ($totalGrantsFromLedger as $tot) {
            if ($tot['pay_type'] != 'ss')
              $totalGrants += $tot['total'];
        }

        if ($totalGrants) {
            $grossAmnt = $gross - floatval($totalGrants);
        } else {
            $grossAmnt = $gross;
        }

        $amount = $grossAmnt * $discount;
        $remarks = 'Classification Type ' . $row['discountid'];
        if (!is_null($appliedDiscountAmnt)) {
            $amount = $grossAmnt  - $appliedDiscountAmnt;
            $remarks = 'Applied Discount with Classification Type ' . $row['discountid'];
        }

        $history = $remarks . ' on ' . date("Y-m-d h:i:s A") . ' by ' . $user;
        $now = date('YmdHis');

        $data = array(
            'ref_no' => '',
            'encounter_nr' => $encounter_nr,
            'bill_nr' => $bill_nr,
            'entry_type' => 'debit',
            'amount' => $amount,
            'pay_type' => 'ss',
            'control_nr' => 'MSS applied',
            'description' => $remarks,
            'create_id' => $user,
            'create_time' => $now,
            'history' => $history
        );

        $res = self::insert($data);
    }


    /**
     * Handled override entries insert/update status
     * @param $encounter_nr
     * @param $bill_nr
     * @param $type
     * @param int $status
     * @param null $appliedDiscountAmnt
     * @return bool
     */
    function addOverrideAmountToCollectionLedger($encounter_nr, $bill_nr, $type, $status = 0, $appliedDiscountAmnt = null)
    {
        global $db, $HTTP_SESSION_VARS;

        # check discount amount / other discount details
        $netSql = "SELECT fn_billing_compute_gross_amount(b.bill_nr) AS gross, fn_billing_compute_net_amount(b.bill_nr) AS net, sbd.discount AS discount,\n".
            "b.request_flag, sbd.discount_amnt\n".
            "FROM seg_billing_encounter AS b\n".
            "INNER JOIN care_encounter AS e on e.encounter_nr=b.encounter_nr\n".
            "INNER JOIN seg_billing_discount AS sbd on sbd.bill_nr=b.bill_nr\n".
            "WHERE b.bill_nr ='$bill_nr' AND b.is_deleted IS NULL\n".
            "ORDER BY b.bill_dte DESC\n";

        $row = $db->GetRow($netSql);
        $gross = floatval($row['gross']); // amount minus the coverages and discounts
        $net = floatval($row['net']);
        $discount = floatval($row['discount']); //percent
        $user = $HTTP_SESSION_VARS['sess_user_name'];

        $totalGrantsFromLedger = $this->getTotalAmountByEncounter($encounter_nr); # get total amount grants from credit collection ledger by encounter_nr
        $totalCOHGrants = $this->getTotalGrantsByTypeAndNr('coh', $encounter_nr);

        $history = 'Revoked on ' . date("Y-m-d h:i:s A")  . ' by ' . $user;

        if (!is_null($totalCOHGrants)) {
            $refno = $totalCOHGrants['ref_no'];
            $encounter = $totalCOHGrants['encounter_nr'];
            $nr = $totalCOHGrants['bill_nr'];
            $entry_type = 'credit';
            $amount = $totalCOHGrants['total'];
            $payType = $totalCOHGrants['pay_type'];
            $controlNr = $totalCOHGrants['control_nr'];
            //$description = $totalGrantSS['description'];
            $description = 'Revoked';
            $createId = $_SESSION['sess_user_name'];
            $createTime = date('YmdHis');
            $history = $history;

            $data = array(
                'ref_no' => $refno,
                'encounter_nr' => $encounter,
                'bill_nr' => $nr,
                'entry_type' => $entry_type,
                'amount' => $amount,
                'pay_type' => $payType,
                'control_nr' => $controlNr,
                'description' => $description,
                'create_id' => $createId,
                'create_time' => $createTime,
                'history' => $history
            );

            self::insert($data);
        }

        $totalGrantsAmnt = 0;
        foreach ($totalGrantsFromLedger as $ledger) {
            if ($ledger['pay_type'] != 'coh')
            $totalGrantsAmnt += $ledger['total'];
        }

        if ($totalGrantsAmnt) {
            $grossAmnt = $gross - floatval($totalGrantsAmnt);
        } else {
            $grossAmnt = $gross;
        }

        $amount = $grossAmnt  - $appliedDiscountAmnt;

        # if query gross amount returns null
        if ($amount < 0) {
            $sql = "SELECT fn_billing_compute_net_amount(($bill_nr)) AS bill_amount";
            $rowDetails = $db->GetRow($sql);
            $grossAmnt = floatval($rowDetails['bill_amount']) - floatval($totalGrantsAmnt);
            $amount = floatval($grossAmnt) - floatval($appliedDiscountAmnt);
        }

        $remarks = 'Grant Amount Php ' . $amount . ' Created on ' . date("Y-m-d h:i:s A") . ' by ' . $user;
        $now = date('YmdHis');

        $data = array(
            'ref_no' => '',
            'encounter_nr' => $encounter_nr,
            'bill_nr' => $bill_nr,
            'entry_type' => 'debit',
            'amount' => $amount,
            'pay_type' => 'coh',
            'control_nr' => 'Applied COH Discount',
            'description' => $remarks,
            'create_id' => $user,
            'create_time' => $now,
            'history' => $remarks
        );

        self::insert($data);
    }

    /**
     * This holds insert/update amount paid by user on cashier.
     * @param $encounter_nr
     * @param $bill_nr
     * @param $amount
     * @param string $payType
     * @param int $status
     * @return bool
     * @author michelle 03-17-15
     */
    function addPatientPaidAmount($encounter_nr, $bill_nr, $amount, $payType ='paid', $status = 0)
    {
        global $db, $HTTP_SESSION_VARS;

        $user = $HTTP_SESSION_VARS['sess_user_name'];

        $totalGrantsFromLedger = $this->getTotalAmountByEncounter($encounter_nr); # get total amount grants from credit collection ledger by encounter_nr
        $paidEntries = $this->getTotalGrantsByTypeAndNr('paid', $encounter_nr); # paid types

        /*if (!is_null($paidEntries['total'])) {
            //$refno = $paidEntries['ref_no'];
            $encounter = $paidEntries['encounter_nr'];
            $nr = $paidEntries['bill_nr'];
            $entry_type = 'credit';
            $crAmount = $paidEntries['total'];
            $payType = $paidEntries['pay_type'];
            $controlNr = $paidEntries['control_nr'];
            //$description = $totalGrantSS['description'];
            $description = 'Revoked total amount paid Php ' . $paidEntries['total'];
            $createId = $paidEntries['create_id'];
            $modifyId = $paidEntries['modify_id'];
            $createTime = date('YmdHis');
            $modifyTime = '';
            $history = 'Revoked amount paid Php '. $paidEntries['total'] . ' on ' . date("Y-m-d h:i:s A") . ' by ' . $user;

            $creditDetailQuery = "
                INSERT INTO seg_credit_collection_ledger (
                  encounter_nr, bill_nr, entry_type, amount, pay_type, control_nr, description, create_id, create_time, history, is_deleted)
                VALUES
                  ('$encounter','$nr','$entry_type','$crAmount','$payType','$controlNr','$description','$createId','$createTime', '$history', '0') ;
            ";
            $db->Execute($creditDetailQuery);
        }*/
        
        $orno = 'OR#' . @$_POST['orno'];
        $history = 'Paid Php ' . number_format($amount,2) . ' on ' . date("Y-m-d h:i:s A") . ' by ' . $user . $orno;
        $remarks = 'Paid at cashier amounting Php ' . number_format($amount,2);
        $now = date('YmdHis');

        $data = array(
            'ref_no' => '',
            'encounter_nr' => $encounter_nr,
            'bill_nr' => $bill_nr,
            'entry_type' => 'debit',
            'amount' => $amount,
            'pay_type' => $payType,
            'control_nr' => $orno,
            'description' => $remarks,
            'create_id' => $user,
            'create_time' => $now,
            'history' => $history
        );
        // var_dump($data); die();
        $result = self::insert($data);
    }

    /**
     * Delete logically collection items by encounter
     * @param `encounter_nr` $nr
     * @return bool
     */
    function deleteSocServ($nr)
    {
        global $db;
        $q = "
            UPDATE seg_credit_collection_ledger
            SET is_deleted = '1'
            WHERE encounter_nr = '$nr' AND pay_type = 'ss' AND control_nr != '' 
        "; // modified by mary~06-29-2016
        $res = $db->Execute($q);
        if ($res)
            return true;
        else
            return false;
    }

    //added by Louie 7-30-2015
    function deleteCredCollection($nr, $pay_type)
    {
        global $db;
        $q = "
            UPDATE seg_credit_collection_ledger
            SET is_deleted = '1'
            WHERE encounter_nr = '$nr' AND pay_type = '$pay_type'
        ";
        $res = $db->Execute($q);
        if ($res)
            return true;
        else
            return false;
    }


    //added by Mary~06-28-2016
     function deleteMSSDiscount($bill_nr, $enc)
    {
   
        global $db, $HTTP_SESSION_VARS;

        $user = $HTTP_SESSION_VARS['sess_user_name'];

        $totalGrantSS = $this->getTotalGrantSSByEncounter($enc); # get total grants by social service type = `qfs`

        if (!is_null($totalGrantSS['total'])) {
            $refno = $totalGrantSS['ref_no'];
            $encounter = $totalGrantSS['encounter_nr'];
            $nr = $totalGrantSS['bill_nr'];
            $entry_type = 'credit';
            $amount = $totalGrantSS['total'];
            $payType = $totalGrantSS['pay_type'];
            $controlNr = $totalGrantSS['control_nr'];
            //$description = $totalGrantSS['description'];
            $description = 'Revoked ' . $totalGrantSS['description'] . ' Php ' . $totalGrantSS['total'];
            $createId = $_SESSION['sess_user_name'];
            $createTime = date('YmdHis');
            $history ='(DELETED) '. $totalGrantSS['description'] . ' Php ' . $totalGrantSS['total'] . ' Revoked on ' . date("Y-m-d h:i:s A") . ' by ' . $user;
            $isDel = $totalGrantSS['is_deleted'];

            $data = array(
                'ref_no' => $refno,
                'encounter_nr' => $enc,
                'bill_nr' => $nr,
                'entry_type' => $entry_type,
                'amount' => $amount,
                'pay_type' => $payType,
                'control_nr' => $controlNr,
                'description' => $description,
                'create_id' => $createId,
                'create_time' => $createTime,
                'history' => $history
            );

            self::insert($data);

        }

        if ($res)
            return true;
        else
            return false;
    }

    /**
     * Delete override applied discount
     * @param $nr
     * @return bool
     */
    function deleteOverride($nr)
    {
        global $db;
        $q = "
            UPDATE seg_credit_collection_ledger
            SET is_deleted = '1'
            WHERE encounter_nr = '$nr' AND pay_type = 'coh'
        ";

        $res = $db->Execute($q);
        if ($res)
            return true;
        else
            return false;
    }

    /**
     * Handles saving of data to ledger
     * @param $data
     * @return bool
     */
    public static function insert($data)
    {
        global $db;

        $refno = $data['ref_no'];
        $encounter_nr = $data['encounter_nr'];
        $bill_nr = $data['bill_nr'];
        $entry_type = $data['entry_type'];
        $amount = (string) $data['amount'];
        $pay_type = $data['pay_type'];
        $control_nr = $data['control_nr'];
        $remarks = $data['description'];
        $user = $data['create_id'];
        $now = $data['create_time'];
        $history = $data['history'];

        $query = "INSERT INTO seg_credit_collection_ledger(
                                         ref_no, 
                                         encounter_nr, 
                                         bill_nr, 
                                         entry_type, 
                                         amount, 
                                         pay_type, 
                                         control_nr, 
                                         description, 
                                         create_id, 
                                         create_time, 
                                         history)VALUES(
                                         '$refno',
                                         '$encounter_nr', 
                                         '$bill_nr', 
                                         '$entry_type', 
                                         '$amount', 
                                         '$pay_type', 
                                         '$control_nr', 
                                         '$remarks', 
                                         '$user', 
                                         '$now', 
                                         '$history')";

        if ($db->Execute($query)) {
            return true;
        } else { return false; }
    }

    /**
     * gelie 10-14-2015
     * Delete logically bill's partial payment
     * @param $nr - encounter no
     * @param $orNo - OR no
     * @return bool
     */
    function deletePrevPartialInLedger($nr, $orNo){
        global $db;
        $q = "
            UPDATE seg_credit_collection_ledger
            SET is_deleted = '1'
            WHERE encounter_nr = '$nr' AND control_nr LIKE '%$orNo%' AND pay_type = 'partial'
        ";
        $res = $db->Execute($q);
        if ($res)
            return true;
        else
            return false;
    }

    /**
    * Check if patient is already in seg_credit_collection_ledger
    * @author syboy : 09/13/2015
    * @return Encounter and OR number
    */ 
    public function checkORandEnc($billnr){
        global $db;
        return $db->GetOne("SELECT bill_nr FROM seg_credit_collection_ledger WHERE bill_nr = ? AND pay_type = ? ",array($billnr, 'paid'));
    }

    /**
    * @author syboy : 09/13/2015
    * @return update OR patient
    */ 
    function updatePatientPaidAmount($encounter_nr, $bill_nr, $amount, $payType ='paid', $status = 0){
        global $db, $HTTP_SESSION_VARS;

        $user = $HTTP_SESSION_VARS['sess_user_name'];

        $totalGrantsFromLedger = $this->getTotalAmountByEncounter($encounter_nr); # get total amount grants from credit collection ledger by encounter_nr
        $paidEntries = $this->getTotalGrantsByTypeAndNr('paid', $encounter_nr); # paid types

        $orno = 'OR#' . @$_POST['orno'];
        $history = 'Paid Php ' . number_format($amount,2) . ' on ' . date("Y-m-d h:i:s A") . ' by ' . $user . $orno;
        $remarks = 'Paid at cashier amounting Php ' . number_format($amount,2);
        $now = date('YmdHis');

        $data = array(
            'ref_no' => '',
            'encounter_nr' => $encounter_nr,
            'bill_nr' => $bill_nr,
            'entry_type' => 'debit',
            'amount' => $amount,
            'pay_type' => 'paid',
            'control_nr' => $orno,
            'description' => $remarks,
            'create_id' => $user,
            'create_time' => $now,
            'history' => $history
        );

        $result = self::update($data);
    }

    /**
    * update data in seg_credit_collection_ledger
    * @author syboy : 09/13/2015
    * @return update OR patient
    */ 
    public static function update($data)
    {
        global $db;

        $refno = $data['ref_no'];
        $encounter_nr = $data['encounter_nr'];
        $bill_nr = $data['bill_nr'];
        $entry_type = $data['entry_type'];
        $amount = (string) $data['amount'];
        $pay_type = $data['pay_type'];
        $control_nr = $data['control_nr'];
        $remarks = $data['description'];
        $user = $data['create_id'];
        $now = $data['create_time'];
        $history = $data['history'];

        $sql = "UPDATE seg_credit_collection_ledger SET 
                    ref_no = ".$db->qstr($refno).", 
                    entry_type = ".$db->qstr($entry_type).",
                    amount = ".$db->qstr($amount).",
                    pay_type = ".$db->qstr($pay_type).", 
                    control_nr = ".$db->qstr($control_nr).", 
                    description = ".$db->qstr($remarks).",
                    create_id = ".$db->qstr($user).",
                    create_time = ".$db->qstr($now).",
                    history = ".$db->qstr($history)."
                   WHERE encounter_nr = ".$db->qstr($encounter_nr)." 
                   AND  bill_nr = ".$db->qstr($bill_nr)."
                   ORDER BY create_time DESC LIMIT 1";

        if ($db->Execute($sql)) {
            return true;
        } else { return false; }
    }

    

    /**
     * @param $encounterNr
     * @param string $columns
     * @param string $condition
     * @return array|null
     */
    public static function findCreditCollectionByEncounter($encounterNr, $columns = "", $condition = "")
    {
        global $db;
        return $db->GetAll("SELECT
                              $columns
                            FROM
                            seg_credit_collection_ledger AS ledger
                            LEFT JOIN seg_grant_account_type AS accountType
                              ON ledger.pay_type = accountType.type_name
                            WHERE encounter_nr = ?
                            AND is_deleted = 0
                            {$condition}",
                            array($encounterNr, $encounterNr));
    }

} // end of SegCreditCollection class
