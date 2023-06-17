{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div>

{{$sFormStart}}

<style type="text/css">
<!--
	.tabFrame {
		padding:5px;
		min-height:150px;
	}

-->
</style>

{{$sHiddenInputs}}

<div style="width:98%">
	
	<!-- updated by: syboy 03/15/2016 -->
	<table id="" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px">
		<tbody>
			<tr>
				<td ><span style="margin:2px;font:bold 12px Tahoma">Total NET Amount</span></td>
				<!-- <td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td> -->
				<td align="right" width="15%" nowrap="nowrap" style="color:#000060;font:bold 14px Arial" >{{$sNet}}</td>
			</tr>
            <tr>
                <td width="*"><span style="margin:2px;font:bold 12px Tahoma">Less Collection Grant</span></td>
              <!--   <td align="center" width="15%" nowrap="nowrap"></td>
                <td align="center" width="15%" nowrap="nowrap"></td>
                <td align="center" width="15%" nowrap="nowrap"></td>
                <td align="center" width="15%" nowrap="nowrap"></td> -->
                <td align="right" width="15%" nowrap="nowrap" style="color:#000060;font:bold 14px Arial" >{{$sLess}}</td>
            </tr>
            <tr class="alt">
				<td width="*"><span style="color:#000060;margin:2px;font:bold 12px Tahoma">Running Balance</span></td>
				<!-- <td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td> -->
				<td align="right" width="15%" nowrap="nowrap" style="color:#000060;font:bold 14px Arial" >{{$sBalance}}</td>
			</tr>
		</tbody>
	</table>

	<!-- <table id="" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;">
		<tbody>
			
		</tbody>
	</table> -->
	<!-- ended syboy -->



	<table id="collectionsTable" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:8px;margin-top:8px">
		<thead>
			<tr id="">
				<th align="center" width="25%" nowrap="nowrap">Details of Collection Grants</th>
				<th align="center" width="15%" nowrap="nowrap">Amount</th>
<!--removed by jasper 05/13/2013 <th align="center" width="15%" nowrap="nowrap">Discount</th> -->
				<!-- removed by: syboy 03/15/2016 <th align="center" width="15%" nowrap="nowrap">Total (Discounted)</th>
				<th align="center" width="15%" nowrap="nowrap">Insurance/PHIC</th>
				<th align="center" width="15%">Excess</th> -->
			</tr>
		</thead>
		<tbody>
<!-- {{$sBillDetails}} removed by: syboy 03/15/2016 -->
		</tbody>
	</table>
</div>
{{$sFormEnd}}
{{$sTailScripts}}
