<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

global $db;
include_once($root_path."/classes/json/json.php");
include_once($root_path."/classes/fpdf/pdf.class.php");
include_once($root_path."include/care_api_classes/class_cashier.php");

/**
 * Added by : Nick 05-26-2014
 * Reference : http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
 * Convert number to words
 * @param $number Integer
 * @return String
 */
function convert_number_to_words($number) {
    
    $hyphen      = ' ';//'-';
    $conjunction = ' ';// ' and ';
    $separator   = ' ';//', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'forty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
    
    if (!is_numeric($number)) {
        return false;
    }
    
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
    
    $string = $fraction = null;
    
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            //$units  = $number % 10;//commented by Nick 06-26-2014 - data loss(narrowing cast float to int), returns 8 instead of nine for 29 % 10
            $units = fmod($number,10);
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
    
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    
    return $string;
}

/**
 * Added by: Nick Alcala 05-26-2014
 * Convert number to String
 * @param  $money Int/Double
 * @return String
 */
function getMoneyInWords($money){
	if(is_numeric($money) && floor($money) != $money){
		$decimal = floor($money);
		$fraction = round($money - $decimal,2) * 100;
		$money_str = convert_number_to_words($decimal) . " & ";
		$money_str .= "$fraction/100";
	}else{
		$money_str = convert_number_to_words($money) . " Peso/s Only";
	}
	return $money_str;
}

/*
**	http://www.zend.com//code/codex.php?ozid=1540&single=1
**  Function:   convert_number
**  Arguments:  int
**  Returns:    string
**  Description:
**      Converts a given integer (in range [0..1T-1], inclusive) into
**      alphabetical format ("one", "two", etc.).
*/
function convert_number($number)
{
		if (($number < 0) || ($number > 999999999))
		{
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

		if ($Gn)
		{
				$res .= convert_number($Gn) . " Million";
		}

		if ($kn)
		{
				$res .= (empty($res) ? "" : " ") .
						convert_number($kn) . " Thousand";
		}

		if ($Hn)
		{
				$res .= (empty($res) ? "" : " ") .
						convert_number($Hn) . " Hundred";
		}

		$ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
				"Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
				"Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
				"Nineteen");
		$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
				"Seventy", "Eigthy", "Ninety");

		if ($Dn || $n)
		{
				if (!empty($res))
				{
						$res .= " and ";
				}

				if ($Dn < 2)
				{
						$res .= $ones[$Dn * 10 + $n];
				}
				else
				{
						$res .= $tens[$Dn];

						if ($n)
						{
								$res .= "-" . $ones[$n];
						}
				}
		}

		if (empty($res))
		{
				$res = "zero";
		}

		return $res;
}

$cClass = new SegCashier();
$ORNo = $_REQUEST['nr'];
$Mode = $_REQUEST['mode'];
if (!$Mode) $Mode = 'R';
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$printer = $db->GetRow("SELECT printer_port, printer_model FROM seg_print_default WHERE ip_address=".$db->qstr($_SERVER['REMOTE_ADDR']));

$info = $cClass->GetPayInfo( $ORNo );
$total = (float) $info['amount_due'];
$pinfo = array(
	'orno' => $info['or_no'],
	'date' => date("M j, Y, g:i a",strtotime($info['or_date'])),
	'name' => addslashes(strtoupper($info['or_name'])),
	'total' => $total
);
$pesos = floor($total);
$centavos = ($total-$pesos)*100 % 100;
/*
commented by Nick Nick 05-26-2014
13,154.75 = THIRTEEN THOUSAND ONE HUNDRED AND FIFTY-FOUR PESO/S AND SEVENTY-FIVE CENTAVO/S ONLY
*/
// $pinfo['total_words'] = strtoupper( convert_number($pesos) . " peso/s" .
	// ($centavos ? (" and ".convert_number($centavos)." centavo/s only") : " only"));

/*
added by Nick 05-26-2014
13,154.75 = THIRTEEN THOUSAND, ONE HUNDRED AND FIFTY-FOUR PESO/S AND SEVENTY-FIVE CENTAVO/S ONLY
*/
$pinfo['total_words'] = mb_strtoupper(getMoneyInWords($total));
$temp = $pinfo['total_words'];
if($_SESSION['sess_login_userid'] == 'medocs'){
    echo <<<js
    <script>
        alert("$temp");
    </script>
js;
}

if ($Mode == 'R') {
	$rsDetails = $cClass->GetPayDetails( $ORNo );
	$items = array();
	$i=0;
	while ($row = $rsDetails->FetchRow()) {
		$code = explode("|",$row["account_code"]);
		$items[$i] = array(
			//"collection"=>addslashes($row["service"]),
			"collection"=>preg_replace('/\s+/', ' ', addslashes($row["service"])),
			"code"=>strtoupper($code[0]),
			"quantity"=>(int)$row["qty"],
			#"price"=>number_format($row["amount_due"]/$row["qty"], 2, '.', ''),
			#"amount"=>number_format($row["amount_due"], 2, '.', '')
			"price"=>number_format($row["amount_due"]/$row["qty"], 2, '.', ',')
		);
		
		$items[$i]["amount"]=number_format($row["amount_due"], 2, '.', '');
		
		$i++;
	}
}

?>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache">
	<link rel="stylesheet" type="text/css" media="all" href="css/draft.css" />
</head>
<body>
<?php

?>
<script type='text/javascript' language='javascript'>
	function closeWindow() {
		setTimeout("window.parent.cClick()", 1500);
	}

	this.orInfo = {
		orno : "<?= $pinfo["orno"] ?>",
		date : "<?= $pinfo["date"] ?>",
		name : "<?= $pinfo["name"] ?>",
		total : "<?= number_format($pinfo["total"],2) ?>",
		total_words : "<?= $pinfo["total_words"] ?>",
		officer : "<?= $_SESSION["sess_temp_fullname"] ?>",
	};


	this.orItems = [
<?php
	$js_items = array();
	foreach ($items as $item) {
		$js_arr = array();
		foreach ($item as $i=>$v) {
			$js_arr[] = "$i:'$v'";
		}
		$js_items[] = "{".implode(", ", $js_arr)."}";
	}
	echo implode(",\n", $js_items)."\n";
?>
	];
	this.PrinterObj = {
		printerType:"<?= addslashes($printer["printer_model"]) ?>",
		printerPort:"<?= addslashes($printer["printer_port"]) ?>"
	}
</script>
	<table border="0" width="90%">
		<tr>
			<td width="50"><img id="icon" name="icon" src="<?= $root_path ?>images/print.png" border="0" title="Printing"></td>
			<td>
				<h1 name="msg" id="print-message">Printing O.R.</h1>
				<div align="center">
					<img name="bar" id="in-progress" src="<?= $root_path ?>images/ajax_bar2.gif" border="0" title="Printing" style="margin-left:10px">
				</div>
			</td>
		</tr>
	</table>
	<applet archive="cashier_printOR.jar" code="print_or.class" width="0" height="0" mayscript></applet>
</body>
</html>