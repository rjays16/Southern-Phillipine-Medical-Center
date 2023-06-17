<div>

<div style="width:90%; margin-top:10px" align="left">
	<table border="0" cellspacing="2" cellpadding="3" align="left" width="100%">
		<tbody>
			<tr>
				<td width="15%"><strong>Agency</strong></td>
				<td width="85%"> : &nbsp;&nbsp;&nbsp; {{$sAgency}}</td>
			</tr>

			<tr>
				<td><strong>Bill Date</strong></td>
				<td> : &nbsp;&nbsp;&nbsp; {{$sDateTimePicker}}<td>
			</tr>
		</tbody>
	</table>
</div>

<br>
<br>
<br>
<br>

<div class="segContentPane" style="width:92%;">
	<div style="width:98%; height:290px; overflow-y:scroll;">
		<table id="employee-list" class="jedList" width="98%" cellspacing="0" cellpadding="0" border="1">

			<thead>
				<tr>
					<th align="left"> Bill No. </th>
					<th align="left"> Bill Date/Time </th>
					<th align="left"> Billed Amount </th>
					<th align="center"> Options </th>
				</tr>
			</thead>

			<tbody id="icRows">
				{{$sListRows}}
			</tbody>
		</table>
	</div>
</div>

</div>