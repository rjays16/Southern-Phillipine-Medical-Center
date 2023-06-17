{{*created by art 09/10/2014 for dialysis module*}}
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Add Prebill Payment Details</title>
	{{$bootstrap}}
	<style>
	input[type=text], input[type=number] {
    height: 28px !important;
	}
	.center{
	   text-align: center;   
	}
	</style>
</head>
<body><form id="payform" name="payform" action="" method="POST">
	<table border="0" cellspacing=1 cellpadding=0 width="100%">
	<h4>Add Prebill Payment Details</h2>
	
	<tr>
		{{$enc_nr}}
		{{$curr_nr}}
		<td width="10%"><label>Pre-Bill No.</label></td>
		<td width="*" id="prebillno"></td>
	</tr>
	<tr>
		<td width="10%"><label>Total:</label></td>
		<td width="*"><h4 id="total"></h4></td>
	</tr>
	<tr><td width="10%"><label><!-- Less: --></label></td></tr>
	<tr>
		<td width="10%"></td>
		<td>
			<div id="data"></div>
			<div id="msg"></div>
		</td>
	</tr>
</table></form>
</body>
</html>