<div align="center" style="font:bold 12px Tahoma; color:#990000; margin-top: 10px;">{{$sWarning}}</div><br />

{{$sFormStart}}

<table  border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
	<tbody>
		<tr>
            <td class="segPanelHeader" width="*">
                Patient ER Location Details
            </td>
        </tr>
        <tr>
        	<td class="segPanel" align="center" valign="top">
        		<table width="95%" border="0" cellpadding="2" cellspacing="0">
        			<tr>
        				<td valign="center" width="28%" style="text-align: right;"><strong>Location</strong></td>
        				<td style="padding: 5px;">{{$sERLocation}}</td>
        			</tr>
        			<tr>
        				<td valign="center" style="text-align: right;"><strong>Section</strong></td>
        				<td style="padding: 5px;">{{$sERLobby}}</td>
        			</tr>
        		</table>

        		<br>
			    <div align="left" style="width:95%">
			        <table width="100%">
			            <tr>
			                <td align="right">
			                    {{$sContinueButton}}
			                </td>
			            </tr>
			        </table>
			    </div>
        	</td>
        </tr>
	</tbody>
</table>

{{$sFormEnd}}
{{$sHiddenInputs}}