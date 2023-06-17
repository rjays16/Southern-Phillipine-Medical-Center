<!-- Vaccination Certificate if patient is new born
	 Medical Records ('Dialog box').
	 Comment by: borj 2014-05-06
-->  
<div id="dlgVaccination" style="display: none" align="center">
    <table>
        <tr>
            <td>Details:</td>
            <td><input id="vdetails" type="text"/></td>
        </tr>
        <tr>
            <td>Date:</td>
            <td><input id="vdate" type="text"/></td>
        </tr>
    </table>
</div>
<!--End
-->
<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
    <tr>
      <td>{{include file="registration_admission/reg_tabs.tpl"}}</td>
    </tr>
	<!--added by VAN 02-28-08 -->
	{{if $is_discharged}}
				<tr>
					<td bgcolor="red" colspan="3">
						&nbsp;
						{{$sWarnIcon}}
						<font color="#ffffff">
						<b>
						{{$sDischarged}}
						</b>
						</font>
					</td>
				</tr>
		{{/if}}
    <tr>
      <td>
			<table cellspacing="0" cellpadding="0" width=800>
			<tbody>
				<tr valign="top">
					<td>{{$sRegForm}}</td>
					<td>{{$sRegOptions}}</td>
				</tr>
			</tbody>
			</table>
	  </td>
    </tr>
    
	<tr>
      <td valign="top">
	  {{$pbNewSearch}} {{$pbUpdateData}} {{$pbShowAdmData}} {{$pbAdmitInpatient}} {{$pbAdmitOutpatient}} {{$pbRegNewPerson}}
<!--  Edited by Bong 2/21/2007 <span class="reg_input">{{$sOtherNr}}</span> --></td>
    </tr>

    <tr>
      <td>
		{{$sSearchLink}}
		<br>
		{{$sArchiveLink}}
		<p>
		{{$pbCancel}}
		</td>
    </tr>

  </tbody>
</table>
