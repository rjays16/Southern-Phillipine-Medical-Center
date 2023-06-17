 {{* Toolbar - Topblock  *}}

<table cellspacing="0"  class="titlebar" style="border:1px solid #cfcfcf;margin-bottom:10px" cellpadding="0">    
 <tr valign=middle  class="titlebar" >
  <td bgcolor="{{$top_bgcolor}}" valign="middle" width="1">
    &nbsp;{{$sTitleImage}}&nbsp;<font size="3" color="{{$top_txtcolor}}" style="white-space:nowrap">{{$sToolbarTitle}}</font>
     {{if $Subtitle}}
      - {{$Subtitle}}
     {{/if}}
  </td>
{{if $QuickMenu }}
	<td class="quickmenu" bgcolor="{{$top_bgcolor}}" align=right valign="middle">
		<ul>
{{foreach from=$QuickMenu key=qmId item=qItem}}
	{{if $qItem.label ne "|"}}
		  <li>
				<a href="{{$qItem.url}}">
					<span><img {{$qItem.icon}} align="absmiddle"/></span>
					{{$qItem.label}}
				</a>
			</li>
	{{else}}
		  <li class="separator"></li>
	{{/if}}
{{/foreach}}
		</ul>
	</td>
{{else}}
  <td bgcolor="{{$top_bgcolor}}" align=right valign="middle" style="">
  	{{if $pbAux2}}
		<a href="{{$pbAux2}}"><img {{$gifAux2}} alt="" {{$dhtml}} /></a>
	{{/if}}
	{{if $pbAux1}}
		<a href="{{$pbAux1}}"><img {{$gifAux1}} alt="" {{$dhtml}} /></a>
	{{/if}}
	<!-- Hide Back buttons =)  AJMQ/Oct 03 2007
	{{if $pbBack}}
		<a href="{{$pbBack}}">
			<img {{$gifBack2}} alt="" {{$dhtml}} />
		</a>
	{{/if}}
	-->
	<!---hide for the meantime...pet, apr22,2008-----
	{{if $pbHelp}}
		<a href="{{$pbHelp}}">
			<img {{$gifHilfeR}} alt="" {{$dhtml}} />
		</a>
	{{/if}}
	---pet---------------------til here only-------->
	

	<!--
	created by JOHN PAUL SARGENTO
	date: march 28, 2017
	for printing nursing rounds
	 -->
	<!--button for printing nursing rounds -->
	{{if $gnrtRounds }}
		<a href="{{$breakfile}}" {{$sCloseTarget}}>
			<img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}} />
		</a>
	{{/if}}
	<!-- end for printing nursing rounds-->

	{{if $breakfile}}
		<!-- <a style="color: black; font-size: 1em;" href="#">Print Nursing Rounds</a> -->

		{{if $breakfile == "nursing-wardList.php?ntid=false&lang=en&key=*&pagekey=*"}}

		<form method="post" action="#" id="checkTimePrintNursing">
			<div class="blackBackground"></div> 
			<div class="chooseTime">
				<div class="rowShift">
					<label class="shiftLabel">Shift: </label>
					<select name="timeChosenPrintNursing" id="timeChosenPrintNursing">
						{{$getShift}}
					</select>
				</div>
				<div class="rowShift">
					<input class="okShift" type="button" name="submitShift" value="OK" onclick="okShiftClicked();">
					<button class="cancelShift" onclick="closeNurseRoundSelection();">Cancel</button>
				</div>
			</div>
			
			
		<div class="pClassBackground"></div>   
		{{ if isset($ward_dept) && $ward_dept eq $IPBM_DEPT }}	
		<div class="updatePatientClassification">
			<div>
				<a class="pull-right" id="mod_hist" style="border: 1px solid black;padding: 5px;">View History</a>
			</div>
			<br><br>
			<table>
				<tr>
					<td>
						<label class="classLabel">Patient Name: </label>
					</td>
					<td>
						<div id="pName" class="p-info"></div>
					</td>
				</tr>
				<tr>
					<td>
						<label class="classLabel">Admission Date: </label>
					</td>
					<td>
						<div id="admission_date" class="p-info"></div>
					</td>
				</tr>
				<tr>
					<td>
						<label class="classLabel">Confinement Days: </label>
					</td>
					<td>
						<div id="confinement_days" class="p-info" style="vertical-align:middle;"></div>
					</td>
				</tr>
			</table>
			<hr>
			<table>
				<tr>
					<td>
						<label class="classLabel">Classification: </label>
					</td>
					<td>
						<select name="patient_class" id="p_class-input">
					
						</select>
					</td>
					<td>
						<input class="okShift" type="button" name="" value="OK" onclick="updateClassification();">
					</td>
					<td>
						<button class="cancelShift" onclick="closePatientClassification();return false;">Cancel</button>
					</td>
				</tr>
				
			</table>
			<div id="pclass-warning" style="text-align: center;color: black;"></div>
		</div>
		{{ /if }}
		

		</form>
	
		<input type="hidden" name="formclicked" id="formclicked" value="">
		{{if $hidden_rounds }}
		<div class="{{$printEndorsementList}}">Endorsement Sheet</div>
		{{else}}
		<div class="{{$printRoundsClass}}">Nursing Rounds Forms</div>
		{{/if}}
		<div class="{{$dietList}}">Diet List</div>
		<div class="{{$vsmon}}">VS Monitoring</div>
		<div class="{{$mms}}">Medicine Monitoring Sheet</div>
        <div class="{{$cbgClass}}">CBG Strips Issuance</div>                
		{{/if}}

		<a href="{{$breakfile}}" {{$sCloseTarget}}>
			<img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}} />
		</a>
	{{/if}}
  </td>
{{/if}}
 </tr>
 </table>
 <script type="text/javascript">
 	var ctpn = $("#checkTimePrintNursing");

 	ctpn.onSubmit(function(){
 		alert("text/javascript");

 	});

 	function $_GET(param)
 	{
		var vars = {};
		window.location.href.replace( location.hash, '' ).replace( 
			/[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
			function( m, key, value ) { // callback
				vars[key] = value !== undefined ? value : '';
			}
		);

		if ( param ) {
		return vars[param] ? vars[param] : null;	
	}
	return vars;
	}

 	// function okShiftClicked()
 	// { 	
 	// 	var ward_name = $_GET('station');
 	// 	var ward_nr = $_GET('ward_nr');
 	// 	var timeChosen = $("#timeChosenPrintNursing option:selected").val();
	 // 	var url = "reports/nursing-print-rounds.php?time=" + timeChosen + "&ward_name=" + ward_name + "&ward_nr="+ward_nr;
	 // 	var win = window.open(url, '_blank');
		// if (win)
		//     win.focus();
		
		    
		
 	// }

 // 	$J( "#print_report").click(function() {
	// 	var HRN= $J("#hrn").text();  
	// 	var enc_nr = $J("#encounter_nr").val();
	// 	var age = $J("#age").text();
	// 	if (HRN == "") {
	// 		alert("Select a patient");
	// 	}else{
	//  	var url = "reports/waiver_report.php?pid="+HRN+"&enc="+enc_nr+"&ages="+age;	
	//  	var win = window.open(url, '_blank');
	// 	if (win)
	// 	    win.focus();
	// 	else 
	// 	    alert('Please allow popups for this website');
	// 	}
	// });
 </script>