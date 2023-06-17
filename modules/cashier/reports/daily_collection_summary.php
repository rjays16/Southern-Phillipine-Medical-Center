<?php

ini_set('memory_limit','1024M');
set_time_limit(0);

require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'classes/tcpdf/tcpdf.php';
define('TRAINING_FEE_CODE', '00000948');
define('TRAINING_FEE_ALT_CODE', '200900000948');

/**
 * Daily Collection Summary Report
 * @author Alvin Quinones
 */
class DailyCollectionSummary extends TCPDF {
	const FONT_FAMILY = 'helvetica';
	const FIXED_WIDTH = 7;
    const HORIZ_SCALING = 1;

	/**
	 * @var array $info 
	 */
	protected $info = array();
	/**
	 * @var string $account The account code referring to the account that will be reported on
	 */
	protected $account = null;
	/**
	 * @var int $reportDate Unix timestamp of the date that will be used as the basis for the report
	 */
	protected $reportDate = null;
	/**
	 * @var string $seriesStart Start of the OR number series to be included in the report
	 */
	protected $seriesStart = null;
	/**
	 * @var string $seriesEnd End of the OR number series to be included in the report
	 */
	protected $seriesEnd = null;

	protected $data = array();

	/**
	 * 
	 */
	public function __construct() 
	{
		global $db;
		$db->SetFetchMode(ADODB_FETCH_ASSOC);

		parent::__construct();

		$this->setMeta();
		$this->setLayout();
	}

	/**
	 * Sets the OR number series to be included in the report
	 * @param string $start 
	 * @param string $end 
	 * @return void
	 */
	public function setSeries($start, $end) 
	{
		if (!$start xor !$end) {
			throw new Exception('Please specify both START and END of OR# series');
		}
		$this->seriesStart = $start ? $start : null;
		$this->seriesEnd = $end ? $end : null;
	}

	/**
	 * Sets the date that will be the basis for the report
	 * @param string $date description
	 * @return void
	 */
	public function setReportDate($date) 
	{
		if ($date) {
			$this->reportDate = strtotime($date);
		} else {
			$this->reportDate = false;
		}
		if ($this->reportDate === false) {
			$this->info['reportDate'] = null;
		} else {
			$this->info['reportDate'] = date('F j, Y', $this->reportDate);
		}
	}

	/**
	 * 
	 * @param string $encoder
	 * @return void
	 */
	public function setEncoder($encoder)
	{
		global $db;
		if (!$encoder) {
			$this->info['encoderName'] = '-ALL ENCODERS-';
		} else {
			$sql = "SELECT name FROM care_users WHERE login_id=".$db->qstr($this->encoder);
			$this->info['encoderName'] = $db->GetOne($sql);
		}
	}

	/**
	 * 
	 * @param string $account
	 * @return void
	 */
	public function setAccount($account) 
	{
		global $db;
		if (!$account) {
			throw new Exception('Account must be specified');
		}

		$this->account = $account;
		$sql = "SELECT formal_name FROM seg_pay_accounts WHERE id=".$db->qstr($account);
		$this->info['accountName'] = $db->GetOne($sql);
		if($account=='hoi') {
			$sql = "SELECT 
					  id,
					  short_name 
					FROM
					  seg_pay_subaccounts 
					WHERE parent_account IN ('bc','hoi','rs', 'ls', 'ps', 'si', 'mhc') 
					ORDER BY 
					  CASE
					    parent_account 
					    WHEN 'bc' 
					    THEN 0
					    WHEN 'hoi' 
					    THEN 1
					    WHEN 'rs' 
					    THEN 2 
					    WHEN 'ls' 
					    THEN 3 
					    WHEN 'ps' 
					    THEN 4 
					    WHEN 'si' 
					    THEN 5 
					    WHEN 'mhc' 
					    THEN 6 
					    ELSE 7 
					  END,
					  id";
		}
		else $sql = "SELECT id,short_name FROM seg_pay_subaccounts WHERE parent_account=".$db->qstr($account);
		// die($sql);
		$rs = $db->Execute($sql);
		if ($rs !== false) {
			$this->info['subaccounts'] = $rs->GetAssoc();
		} else {
			throw new Exception('Unable to retrieve subaccounts for account [' . $account . ']');
		}
	}

	/**
	 * Description
	 * @return void
	 */
    protected function setMeta()
    {
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('Segworks Technologies Corporation');
        $this->SetTitle('Cashier Report: Daily Collection Summary');
        $this->SetSubject('Daily collection summary per account across OR series');
        $this->SetKeywords('report,cashier,daily,collection,account,series');
        
        $info = new Hospital_Admin();
        if ($row = $info->getAllHospitalInfo()) {
            $this->info['hospitalType'] = strtoupper($row['hosp_type']);
            $this->info['hospitalName'] = strtoupper($row['hosp_name']);
            $this->info['hospitalAddress'] = strtoupper($row['hosp_addr1']);
            $this->info['country'] = $row['hosp_country'];
            $this->info['agency'] = $row['hosp_agency'];
        } else {
        	$this->info['country'] = 'Republic of the Philippines';
            $this->info['agency'] = 'DEPARTMENT OF HEALTH';
            $this->info['hospitalName'] = strtoupper("Southern Philippines Medical Center");
            $this->info['hospitalAddress'] = strtoupper("JICA Bldg. J.P. Laurel Bajada, Davao City");
        }
    }

    /**
     * 
     */
    protected function setLayout()
    {
        $dim = $this->getPageSizeFromFormat('A4');

        $this->pageWidth = $dim[1];
        $this->pageHeight = $dim[0];

        $this->setPageFormat($dim, 'L');
        $this->setFont(self::FONT_FAMILY);
        $this->setMargins(10,105.5,65,true);
        $this->setAutoPageBreak(true, 40);
    }

    /**
     * 
     */
    public function Header() 
    {
    	$this->setY(10);
    	$this->setFont(self::FONT_FAMILY, '', 22);
    	$this->Cell(0,10,$this->info['country'],0,1,'C');
    	$this->Cell(0,10,$this->info['agency'],0,1,'C');
    	$this->setFont(self::FONT_FAMILY, 'B', 22);
    	$this->Cell(0,10,$this->info['hospitalName'],0,1,'C');
    	$this->setFont(self::FONT_FAMILY, 'B', 25);
    	$this->Cell(0,10,'Cashier\'s Office',0,1,'C');
    	$this->setFont(self::FONT_FAMILY, 'B', 30);
    	$this->Cell(0,15, sprintf('Daily Collection Summary (%s)', $this->info['accountName']),0,1,'C');
    	$this->setFont(self::FONT_FAMILY, 'B', 25);
    	
    	$info = array();
    	$info[] = $this->info['encoderName'];
    	if ($this->reportDate) {
    		$info[] = $this->info['reportDate'];
		}
		if ($this->seriesStart) {
			$info[] = sprintf('OR# %s - %s', $this->seriesStart, $this->seriesEnd);
		}
		$this->Cell(0,10, implode(' / ', $info),0,1,'C');

		$this->ln(5);
		$this->renderGridHeader();
    }

 	// Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-25);
        // Set font
        $this->SetFont(self::FONT_FAMILY, 'I', 18);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages() . '. Segworks Hospital Information System. Checksum: ' . $this->info['hash'], 0, false, 'C', 0, '', 0, false, 'T', 'M');
        # added by: syboy 09/04/2015
        $this->ln(13);
        $this->SetFont(self::FONT_FAMILY, '', 18);
        $margins = $this->getMargins();
        $w = $this->pageWidth - $margins['left'] - $margins['right'];
        $this->Cell($w*(self::FIXED_WIDTH/18),'','SPMC-F-CAS-15',0,0,'L');
		$this->Cell($w*(self::FIXED_WIDTH/60),'','Effectivity: October 1, 2013',0,0,'C');
		$this->Cell($w*(self::FIXED_WIDTH/18),'','Rev: 0',0,0,'R');
		# end
    }

    /**
     * 
     */
    protected function fetchData()
    {
    	global $db;

    	if (!$this->reportDate && !$this->seriesStart) {
    		throw new Exception('Report date OR Series range should be specified');
    	}
    	
    	if ($this->reportDate) {
    		$where[] = "or_date LIKE '" . date('Y-m-d', $this->reportDate) . "%'";
		}

		if ($this->seriesStart) {
			$where[] = sprintf("p.or_no BETWEEN %s AND %s", 
				$db->qstr($this->seriesStart),
				$db->qstr($this->seriesEnd));
		}

		if ($this->encoder) {
			$where[] = "create_id=".$db->qstr($this->encoder);
		}

		$condition = implode(")\n AND (", $where);

    	$query = "SELECT
					p.or_date,
					p.or_no,
					r.amount_due,
					r.ref_no,
					r.service_code,
					r.ref_source,
					IF(p.cancel_date IS NOT NULL,1,0) AS is_cancelled
				  FROM seg_pay p
    				LEFT JOIN seg_pay_request r ON r.or_no=p.or_no
		          WHERE ({$condition})
			      ORDER BY r.or_no ASC";
			      // die($query);
		$rs = $db->Execute($query);
		if ($rs === false) {
			throw new Exception('Database query error');
		}

		$data = array();

		$accounts = array_keys($this->info['subaccounts']);
		while ($row = $rs->FetchRow()) {

			
				// var_dump($row);die();
				if($row['ref_source'] == 'PH'){
					$sub_sql = "SELECT 
				        o.pharma_area,oi.is_fs
				      FROM
				        seg_pharma_orders o left join seg_pharma_order_items oi on o.refno=oi.refno 
				      WHERE oi.refno = ".$db->qstr($row['ref_no']).
				        "AND oi.bestellnum = ".$db->qstr($row['service_code']);
				    $sub_sql = $db->GetRow($sub_sql);
				    if($sub_sql['is_fs']) $row['type']='consign';
				    else $row['type']='drugs';
				}else if($row['ref_source'] == 'LD'){
					$sub_sql = "SELECT 
						        IFNULL(ls.group_code, '') as group_code
						      FROM
						        seg_lab_services ls 
						      WHERE ls.service_code = ".$db->qstr($row['service_code']);
					$sub_sql = $db->GetRow($sub_sql);
					if($sub_sql['group_code']=='B') $row['type']='blood';
					else if($sub_sql['group_code']=='SPC' && !(stripos($row['service_code'], 'VEN') !== FALSE)) $row['type']='ps';
					else if($sub_sql['group_code']=='SPC' && (stripos($row['service_code'], 'VEN') !== FALSE)) $row['type']='vent';
					else $row['type']='ls';
				}else if($row['ref_source'] == 'RD'){
					$sub_sql = "SELECT 
						        rg.department_nr
						      FROM
						        seg_radio_services AS rs 
						        INNER JOIN seg_radio_service_groups AS rg 
						          ON rg.group_code = rs.group_code 
						      WHERE rs.service_code = ".$db->qstr($row['service_code']);
					$sub_sql = $db->GetRow($sub_sql);
					if($sub_sql['department_nr']=='167') $row['type']='ctscan';
					else if($sub_sql['department_nr']=='208') $row['type']='mri';
					else $row['type']='rs';
				}else if($row['ref_source'] == 'FB'){
					$sub_sql = "SELECT 
						        e.encounter_type,
						        w.prototype
						      FROM
						        seg_pay_request r 
						        INNER JOIN seg_billing_encounter b 
						          ON b.bill_nr = r.service_code 
						        INNER JOIN care_encounter e 
						          ON e.encounter_nr = b.encounter_nr 
						        LEFT JOIN care_ward w 
						          ON w.nr = e.current_ward_nr 
						      WHERE r.or_no = ".$db->qstr($row['or_no']).
						        "AND r.service_code = ".$db->qstr($row['service_code']);
					$sub_sql = $db->GetRow($sub_sql);
					if($sub_sql['encounter_type']=='1') $row['type']='hi';
					else{
						if($sub_sql['prototype']=='mhc') $row['type']='mhc';
						else if($sub_sql['prototype']=='payward') $row['type']='payw';
						else $row['type']='hi';
					}
				}else if($row['ref_source'] == 'PP'){
					$sub_sql = "SELECT 
						        w.prototype
						      FROM
						        seg_pay p 
						        LEFT JOIN care_encounter e 
						          ON e.encounter_nr = p.encounter_nr 
						        LEFT JOIN care_ward w 
						          ON w.nr = e.current_ward_nr 
						      WHERE p.or_no".$db->qstr($row['or_no']);
					$sub_sql = $db->GetRow($sub_sql);
					if($row['service_code']=='HOI') $row['type']='hi';
					else {
						if($sub_sql['prototype']=='mhc') $row['type']='mhc';
						else if($sub_sql['prototype']=='payward') $row['type']='payw';
						else $row['type']='hi';
					}
				}else if($row['ref_source'] == 'OTHER'){
					$sub_sql = "SELECT 
					        IFNULL(st.pay_account,'hi') as account
					      FROM
					        seg_other_services AS os 
					        INNER JOIN seg_cashier_account_subtypes AS st 
					          ON os.account_type = st.type_id 
					      WHERE os.service_code = SUBSTRING(".$db->qstr($row['service_code']).", 1, LENGTH(".$db->qstr($row['service_code']).") - 1)";
					$sub_sql = $db->GetRow($sub_sql);
					$row['type']=$sub_sql['account'];
				}else if($row['ref_source'] == 'MISC'){
					$sub_sql = "SELECT 
					        IFNULL(st.pay_account,'hi') as account
					      FROM
					        seg_other_services AS os 
					        INNER JOIN seg_cashier_account_subtypes AS st 
					          ON os.account_type = st.type_id 
					      WHERE os.alt_service_code =".$db->qstr($row['service_code']);
					$sub_sql = $db->GetRow($sub_sql);
					$row['type']=$sub_sql['account'];
				}else{
					$row['type']='hi';
				}
			if (in_array($row['type'], $accounts)) {
				if($row['type'] == 'blood'){//is crossmatch
					$isCrossmatch = $db->GetOne("SELECT service_code FROM seg_lab_services WHERE service_code = '{$row['service_code']}' AND name LIKE '%crossmatch%'");
					if($isCrossmatch){
						$row['type'] = 'hi';
					}
				}else if(mb_strtolower($row['ref_source']) == 'other'){//is OB and not OB ANNEX
					$isOB = $db->GetOne("SELECT
											  c.name_formal
											FROM
											  seg_other_services a
											  INNER JOIN seg_misc_depts AS b ON a.service_code = b.service_code
											  INNER JOIN care_department AS c ON b.dept_nr = c.nr
											WHERE a.service_code = '{$row['service_code']}' AND c.name_formal LIKE '%obstetrics%'");

					/*Added by Gervie 02/20/2016
					* For Training Fee
					*/

					$isTraining = $db->GetOne("SELECT
											  a.service_code
											FROM
											  seg_other_services a
											WHERE (a.service_code = SUBSTR('{$row['service_code']}', 1, 8) OR a.alt_service_code = '{$row['service_code']}')
											AND (a.service_code = ".$db->qstr(TRAINING_FEE_CODE)." OR a.alt_service_code = ".$db->qstr(TRAINING_FEE_ALT_CODE).")
											AND a.name LIKE 'Training%'");
					
					if($isOB){
						$row['type'] = 'oboplan';
					}
					
					if($isTraining){
						$row['type'] = 'training';
					}
				}
				else if(mb_strtolower($row['ref_source']) == 'misc'){
					$isTraining = $db->GetOne("SELECT
											  a.service_code
											FROM
											  seg_other_services a
											WHERE (a.service_code = SUBSTR('{$row['service_code']}', 5) OR a.alt_service_code = '{$row['service_code']}')
											AND (a.service_code = ".$db->qstr(TRAINING_FEE_CODE)." OR a.alt_service_code = ".$db->qstr(TRAINING_FEE_ALT_CODE).")
											AND a.name LIKE 'Training%'");

					if($isTraining){
						$row['type'] = 'training';
					}
				}

				if (empty($data[$row['or_no']])) {
					$data[$row['or_no']] = array('date' => strtotime($row['or_date']));
				}

				if (empty($data[$row['or_no']][$row['type']])) {
					$data[$row['or_no']][$row['type']] = 0;
				}

				$data[$row['or_no']][$row['type']] += (float) $row['amount_due'];
				$data[$row['or_no']]['is_cancelled'] += $row['is_cancelled'];
			}
		}

		$this->data = $data;
		$this->info['hash'] = sha1(json_encode($array));
    }

    /**
     *
     */
    protected function renderGridHeader()
    {

		$htmlTemplate = '<table border="1" cellpadding="4">{{html}}</table>';
		$headerTemplate = '<th width="{{width}}" align="center" valign="middle" colspan="{{colspan}}" rowspan="{{rowspan}}">{{content}}</th>';

        $headersHtml = array(
    		array(),
    		array()
    	);

        $fixedWidth = self::FIXED_WIDTH;
        $headersHtml[0][] =strtr($headerTemplate, array(
    		'{{width}}' => $fixedWidth . '%',
    		'{{content}}' => 'DATE',
    		'{{colspan}}' => 1,
    		'{{rowspan}}' => 2
    	));

		$headersHtml[0][] =strtr($headerTemplate, array(
    		'{{width}}' => $fixedWidth . '%',
    		'{{content}}' => 'OR#',
    		'{{colspan}}' => 1,
    		'{{rowspan}}' => 2
    	));

    	$headersHtml[0][] =strtr($headerTemplate, array(
    		'{{width}}' => $fixedWidth . '%',
    		'{{content}}' => 'Amount',
    		'{{colspan}}' => 1,
    		'{{rowspan}}' => 2
    	));

    	$remainderWidth = 100-($fixedWidth*2.2);

		$headersHtml[0][] =strtr($headerTemplate, array(
    		'{{width}}' => $remainderWidth . '%',
    		'{{content}}' => 'Classification',
    		'{{colspan}}' => sizeof($this->info['subaccounts']),
    		'{{rowspan}}' => 1
    	));

        foreach ($this->info['subaccounts'] as $subAccount) {
            $headersHtml[1][] = strtr($headerTemplate, array(
                '{{width}}' => $remainderWidth/sizeof($this->info['subaccounts']) . '%',
                '{{content}}' => $subAccount,
                '{{colspan}}' => 1,
                '{{rowspan}}' => 1,
            ));
        }

        $this->setFont(self::FONT_FAMILY, 'B', 20);
        $this->writeHTML(
            strtr($htmlTemplate, array(
                '{{html}}' => '<tr>'.implode('',$headersHtml[0]).'</tr><tr>'.implode('',$headersHtml[1]).'</tr>'
            )),
            false, false, false, false, ''
        );
    }

    /**
     *
     */
    protected function renderDatagrid()
    {
        $this->setFont(self::FONT_FAMILY, '', 22);
        $this->setCellPaddings(2,0,2,0);

        $margins = $this->getMargins();
        $w = $this->pageWidth - $margins['left'] - $margins['right'];

        $rowHeight = 10;
        $this->info['_oldDate'] = null;
        $this->info['_currentPage'] = $this->PageNo();

        $cellWidth = $w * (100-(self::FIXED_WIDTH*2.2))/(100*sizeof($this->info['subaccounts']));

		//var_dump($this->data);

        $totals = array();
        foreach ($this->data as $orNo => $row) {

			if($row['is_cancelled'])
				$this->SetTextColor(255,0,0);
			else
				$this->SetTextColor(0,0,0);


        	$date = date('d-M-y', $row['date']);
        	// if ($this->info['_oldDate'] == $date && $this->info['_currentPage'] == $this->PageNo()) {
        	// 	$date = '';
        	// } else {
        	// 	$this->info['_oldDate'] = $date;
        	// 	$this->info['_currentPage'] = $this->PageNo();
        	// }

        	$total = 0;
			foreach ($row as $key=>$value) {
				if ($key !== 'date' && $key !== 'is_cancelled')
					$total += $value;
			}

        	$this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,$date,1,0,'C');
			$this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,$orNo,1,0,'C');
			$this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,(!$row['is_cancelled']) ? number_format($total,2) : '0.00',1,0,'R');

			$i = 0;
    		foreach ($this->info['subaccounts'] as $id=>$label) {
    			if (empty($totals[$id])) {
    				$totals[$id] = 0.0;
    			}

				//added by Nick 3-22-20, exclude cancelled OR in totals
				if(!$row['is_cancelled']){
					$totals[$id] += $row[$id];
				}
				$i++;
				$this->Cell($cellWidth,$rowHeight,(!empty($row[$id]) ? number_format($row[$id],2) : ' '),1,$i>=count($this->info['subaccounts']),'R');
			}

    		//$this->ln($rowHeight);

    		if (empty($totals['total'])) {
    			$totals['total'] = 0.0;
    		}

			//added by Nick 3-22-20, exclude cancelled OR in totals
			if(!$row['is_cancelled'])
    			$totals['total'] += $total;

    		//var_dump($row);
        }
        //die;

		$this->SetTextColor(0,0,0);

        $this->setFont(self::FONT_FAMILY, 'B', 23);
        $this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,"CNT:".count($this->data),1,0,'C');
        $this->setFont(self::FONT_FAMILY, 'B', 23);
		$this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,'TOTALS',1,0,'C');
		$this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,number_format($totals['total'],2),1,0,'R');

		foreach ($this->info['subaccounts'] as $id=>$label) {
			$this->Cell($cellWidth,$rowHeight, number_format($totals[$id],2),1,0,'R');
		}

    }

    /**
     * Description
     * @return void
     */
	public function report() 
	{
		$this->AddPage();
		$this->setFont(self::FONT_FAMILY, '', 30);
		$this->fetchData();
		$this->renderDatagrid();

		$this->Output('claim_status.pdf', 'I');
	}

}

$report = new DailyCollectionSummary;

try {
	$report->setEncoder($_GET['encoder']);
	$report->setAccount($_GET['account']);
	$report->setSeries($_GET['orfrom'], $_GET['orto']);
	$report->setReportDate($_GET['date']);
	$report->report();
} catch(Exception $e) {
	echo $e->getMessage();
}