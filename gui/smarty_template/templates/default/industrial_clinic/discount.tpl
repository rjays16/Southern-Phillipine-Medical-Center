<table width="80%">
	<tbody>
		<tr hidden>
			<td align="right"><b>Bill Areas :</b></td>
			<td>
					<input id="billareas" type="checkbox"> Medicines </input>
				<br><input id="billareas" type="checkbox"> Services (Lab, IC Lab, Radiology) </input>
				<br><input id="billareas" type="checkbox"> Miscellaneous </input>
			</td>
		</tr>				
		<tr>
			<td align="right"><b>Remarks :</b></td>
			<td>
				<TEXTAREA id="remarks" name="remarks" COLS=30 ROWS=3></TEXTAREA>
			</td>
		</tr>
		<tr>
			<td align="right"><b>Discount (%):</b></td>
			<td><input onkeypress="return isNumberKey(event,1)" style="text-align:right" onFocus="this.select();" id="discount" name="discount" value="" /></td>
		</tr>
		<tr hidden>
			<td align="right"><b>Discount (Fixed):</b></td>
			<td><input onkeypress="return isNumberKey(event,2)" style="text-align:right" onFocus="this.select();" id="discountamnt" name="discountamnt" /></td>
		</tr>

		<tr>
			<td></td>
			<tr></tr>
		</tr>

		<tr>
			<td></td>
			<tr></tr>
		</tr>

		<tr>
			<td></td>
			<tr></tr>
		</tr>

		<tr>
			<td></td>
			<td align="right">
				<input id="save" type="button" value="Save"></input>
				<input id="cancel" type="button" value="Cancel"></input>
			</td>
		</tr>

		<tr>
			<td></td>
			<tr></tr>
		</tr>

		<tr>
			<td></td>
			<tr></tr>
		</tr>                      
	</tbody>
</table>