{{* Template for displaying basic data of patient/person *}}
{{* Used by /dialysis/machine-list-edit.php *}}
{{* Added: Jayson-OJT 2/13/2014 * }}

		<table border="0" cellspacing=1 cellpadding=0 width="100%">

				<tr>
					<td  {{$sClassItem}}>
						{{$LDTransactionNR}} :
					</td>
					<td {{$sClassInput}}>
						{{$sTransactionNR}} 
					</td>
				
				</tr>

				<tr>
					<td  {{$sClassItem}}>
						{{$LDPatientID}} :
					</td>
					<td {{$sClassInput}}>
						{{$sPatientID}} 
					</td>
				
				</tr>

				<tr>
					<td {{$sClassItem}}>
						{{$LDLastName}}, {{$LDFirstName}}{{$LDTitle}} :
					</td>
					<td {{$sClassInput}}>
						<font  class="vi_data">{{$sLastName}} {{$sFirstname}} {{$sMiddleName}}</font>
					</td>
				</tr>

				<tr>
					<td {{$sClassItem}}>
						{{$LDBday}}:
					</td>
					<td bgcolor="#ffffee" class="vi_data">
						{{$sBdayDate}}
					</td>
				</tr>

				<tr>
					<td {{$sClassItem}}>
						{{$LDSex}}:
					</td>
					<td {{$sClassInput}}>
						{{$sSexType}}
					</td>
				</tr>

				{{$LDCurrentMachineNr}}
				{{$sCurrentMachineNr}}
				
				<!-- tr>
					<td {{$sClassItem}}>
						{{$LDProcedure}}:
					</td>
					<td {{$sClassInput}} colspan="2">
						{{$sProcedure}}
					</td>
				</tr>
				
				<tr>
					<td {{$sClassItem}}>
						{{$LDRemarks}}:
					</td>
					<td {{$sClassInput}} colspan="2">
						{{$sRemarks}}
					</td>
				</tr>
 -->
				<!-- <tr>
					<td {{$sClassItem}}>
						{{$LDCurrentMachine}}:
					</td>
					<td bgcolor="#ffffee" class="vi_data">
						<u>{{$sCurrentMachine}}</u>
					</td>
				</tr>


				<tr>
					<td {{$sClassItem}}>
						{{$LDPrevDialyser}}:
					</td>
					<td {{$sClassInput}}>
						{{$sPrevDialyser}}
					</td>
				</tr>

				<tr>
					<td {{$sClassItem}}>
						{{$LDCurrentDialyser}}:
					</td>
					<td {{$sClassInput}} colspan="2">
						{{$sCurrentDialyser}}
					</td>
				</tr> -->

		</table>
