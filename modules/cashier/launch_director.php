<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

if (empty($_SESSION['sess_temp_userid'])) {
	die('Not logged in');
}

global $db;
$db->setFetchMode(ADODB_FETCH_ASSOC);

include_once($root_path."/classes/json/json.php");
include_once($root_path."include/care_api_classes/class_cashier.php");

/**
 * http://www.zend.com//code/codex.php?ozid=1540&single=1
 * Function:   convert_number
 * Arguments:  int
 * Returns:    string
 * Description:
 *   Converts a given integer (in range [0..1T-1], inclusive) into
 *   alphabetical format ("one", "two", etc.).
 */
function intToWords($number) {
    if (($number < 0) || ($number > 999999999)) {
        return "$number";
    }

    $Gn = floor($number / 1000000);  /* Millions (giga) */
    $number -= $Gn * 1000000;
    $kn = floor($number / 1000);     /* Thousands (kilo) */
    $number -= $kn * 1000;
    $Hn = floor($number / 100);      /* Hundreds (hecto) */
    $number -= $Hn * 100;
    $Dn = floor($number / 10);       /* Tens (deca) */
    $n = $number % 10;               /* Ones */

    $res = "";

    if ($Gn) {
        $res .= intToWords($Gn) . " Million";
    }

    if ($kn) {
        $res .= (empty($res) ? "" : " ") .
                intToWords($kn) . " Thousand";
    }

    if ($Hn) {
        $res .= (empty($res) ? "" : " ") .
                intToWords($Hn) . " Hundred";
    }

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
        "Nineteen");
    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
        "Seventy", "Eigthy", "Ninety");

    if ($Dn || $n) {
        if (!empty($res)) {
            $res .= " and ";
        }

        if ($Dn < 2) {
            $res .= $ones[$Dn * 10 + $n];
        } else {
            $res .= $tens[$Dn];

            if ($n) {
                $res .= "-" . $ones[$n];
            }
        }
    }

    if (empty($res)) {
        $res = "zero";
    }

    return $res;
}

$printer = $db->GetRow("SELECT printer_port, printer_model FROM seg_print_default WHERE ip_address=".$db->qstr($_SERVER['REMOTE_ADDR']));
if (!$printer) {
	die('Unable to retrieve printer settings');
}

$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
$jobs = array(
	// job #1
	array(
		'printer' => array(
			//'type' => 'EPSON-ESCP2',
			//'port' => '\\\\127.0.0.1\epsonlx'
			'type' => $printer["printer_model"],
			'port' => $printer["printer_port"]
		),
		'jobProperties' => array(
			'draftQuality' => false,
			'condensed' => true,
			'cols' => 61,
			'rows' => 68,
			'interspacing' => '1/8'
		),
		'textProperties' => array(
			'fontName' => 'SansSerif',
			'bold' => false,
			'condensed' => true,
		),
		'items' => array(
		)
	)
);

/*
for ($i=1;$i<=60;$i++) {
	$jobs[0]['items'][] = array(
		'type' => 'text',
		'x' => 1,
		'y' => $i,
		'content' => str_repeat("1234567890",6) . '123'
	);
}
*/
$ORNo = $_REQUEST['nr'];
$cClass = new SegCashier();
$info = $cClass->GetPayInfo( $ORNo, $showDetails=true );
if ($info == false) {
	die('Error in retrieving payment information...');
}

$printItems = array();

// Date
$printItems[] = array(
	'type' => 'text',
	'x' => 10,
	'y' => 15,
	'content' => date("M j, Y g:iA", strtotime($info['or_date']))
);

// Name
$printItems[] = array(
	'type' => 'text',
	'x' => 10,
	'y' => 17,
	'content' => strtoupper($info['or_name'])
);

// Items
$rsDetails = $cClass->GetPayDetails( $ORNo );
$details = $rsDetails->GetRows();
$items = array();
foreach ($details as $row) {
	$code = explode("|",$row["account_code"]);
	$items[] = 	array(
		'code' => $code[0],
		'name' => preg_replace('/\s+/', ' ', addslashes($row["service"])),
		'price' => ((float) $row['amount_due']) / ((float) $row['qty']),
		'quantity' => (int) $row['qty']
	);
}

// ----------------------------------------
// items
// ----------------------------------------
$line = 23;
$totalAmount = 0;
foreach ($items as $i => $item) {
	$y = $line + $i;
	// Item Code
	$printItems[] = array(
		'type' => 'text',
		'x' => 37,
		'y' => $y,
		'content' => substr($item['code'],0,10)
	);

	// Item Name
	$printItems[] = array(
		'type' => 'text',
		'x' => 3,
		'y' => $y,
		'content' => substr($item['name'],0,32)
	);
	
	
	$amount = round(round($item['price'],2) * $item['quantity'], 2);
	$sAmount = number_format($amount, 2);
	
	// Item Amount
	$printItems[] = array(
		'type' => 'text',
		'x' => 60-strlen($sAmount),
		'y' => $y,
		'content' => $sAmount
	);
	
	if ($item['quantity'] > 1) {
		// Show quantity + unit price
		$line++;
		$printItems[] = array(
			'type' => 'text',
			'x' => 4,
			'y' => $line+$i,
			'content' => sprintf('(Qty x%d   @%s)', $item['quantity'], number_format($item['price'],2))
		);
	}
	
	$totalAmount += $amount;
}

// Total Amount
$totalAmount = round($totalAmount, 2);
$sTotalAmount = number_format($totalAmount, 2);
$printItems[] = array(
	'type' => 'text',
	'x' => 60-strlen($sTotalAmount),
	'y' => 43,
	'content' => $sTotalAmount
);


// Total Amount in Words
$pesos = floor($totalAmount);
$centavos = round(($totalAmount-$pesos)*100,0);
$totalInWords = intToWords($pesos) . " peso/s";
if ($centavos) {
	$totalInWords.= ' and ' . intToWords($centavos) . ' centavo/s';
}
$totalInWords.=' only';
$linesArray = explode("\n",
	wordwrap(
		strtoupper($totalInWords), 55, "\n"
	)
);
$line = 46;
foreach ($linesArray as $i=>$aLine) {
	$printItems[] = array(
		'type' => 'text',
		'x' => 3,
		'y' => $line + $i,
		'content' => $aLine
	);
}

// Cash/Check/MoneyOrder
$type = 'CASH';
if ($info['check_no']) {
	$type = 'CHECK';
}

$y=50;
switch(strtoupper($type)) {
	case 'CHECK':
		$y=52;
		break;
	case 'MONEY_ORDER':
		$y=54;
		break;
}
$printItems[] = array(
	'type' => 'text',
	'x' => 3,
	'y' => $y,
	'content' => 'X'
);

if ($type == 'CHECK') {
	$printItems[] = array(
		'type' => 'text',
		'x' => 19,
		'y' => $y,
		'content' => $info['check_bank_name']
	);
	$printItems[] = array(
		'type' => 'text',
		'x' => 37,
		'y' => $y,
		'content' => $info['check_no']
	);
	
	$checkDate = strtotime($info['check_date']);
	$printItems[] = array(
		'type' => 'text',
		'x' => 49,
		'y' => $y,
		'content' => ($checkDate !== false) ? date('m-d-Y', $checkDate) : ''
	);
}

// Collecting Officer
$encoder = $db->GetOne("SELECT `name` FROM care_users WHERE login_id=".$db->qstr($_SESSION['sess_temp_userid']));
if (!$encoder) {
	die('Could not retrieve encoder information');
}
$designated = $db->GetOne("SELECT value FROM care_config_global WHERE type='cashier_or_designated_officer'");
if (!$designated) {
	die('Could not retrieve designated officer information');
}
$officer = strtoupper($encoder.'/'.$designated);
$printItems[] = array(
	'type' => 'text',
	'x' => 40-strlen($officer)/2,
	'y' => 57,
	'content' => $officer
);

$jobs[0]['items'] = $printItems;

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache">
	<meta charset="iso-5589-1">
	<link rel="stylesheet" media="all" href="css/draft.css" />
</head>
<script>
function closeWindow() {
	setTimeout("window.parent.cClick()", 1500);
}
</script>
<body>
	<table>
		<tr>
			<td width="50"><img id="icon" name="icon" src="<?= $root_path ?>images/print.png" border="0" title="Printing"></td>
			<td>
				<h1 name="msg" id="print-message">Printing Receipt</h1>
				<div align="center">
					<img name="bar" id="in-progress" src="<?= $root_path ?>images/ajax_bar2.gif" border="0" title="Printing" style="margin-left:10px">
				</div>
			</td>
		</tr>
	</table>
	<applet codebase="applet/" archive="DraftPrintSuite.packed.jar" code="com.segworks.draftprintsuite.PrintSuiteDirectorApplet.class" width="0" height="0" mayscript>
		<param name="name" value="Segworks Draft Printing Suite" />
		<param name="jobs" value="<?= htmlentities($json->encode($jobs)) ?>" />
		<param name="onDone" value="closeWindow" />
	</applet>
</body>
</html>