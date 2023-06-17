<div id="px-info-{{$dashlet.id}}" style="width:100%; padding:0; background-color: #a9a9a9">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="segPanel">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="*" style="" valign="top">
							<table width="100%" cellpadding="4" cellspacing="1" border="0">
								<tr>
									<td align="right" width="18%" bgcolor="#eaeaea">HRN:</td>
									<td align="left" width="32%">
										<span style="font:bold 12px Verdana">{{$pat.pid}}</span>
									</td>

									<td align="right" width="18%" bgcolor="#eaeaea">Case No.:</td>
									<td align="left" width="32%"><span style="font:bold 12px Verdana">{{$pat.encounter}}</span></td>
								</tr>
								<tr>
									<td align="right" valign="top" bgcolor="#eaeaea">Full name:</td>
									<td align="left" valign="top" ><span style="font:bold 12px Verdana">{{$pat.fullname}}</span></td>

									<td align="right" valign="top" bgcolor="#eaeaea">Address:</td>
									<td align="left" valign="top"><span style="font:bold 11px Verdana">{{$pat.address}}</span></td>
								</tr>
								<tr>
									<td align="right" bgcolor="#eaeaea">Age:</td>
									<td align="left"><span style="font:bold 12px Verdana">{{$pat.age}}</span></td>

									<td align="right" bgcolor="#eaeaea">Gender:</td>
									<td align="left"><span style="font:bold 12px Verdana">{{$pat.gender}}</span></td>
								</tr>
								<tr>
									<td align="right" bgcolor="#eaeaea">History of Smoking:</td>
									<td align="left"><span style="font:bold 12px Verdana">{{$pat.smoker}}</span></td>

									<td align="right" bgcolor="#eaeaea">Alcohol Drinker:</td>
									<td align="left"><span style="font:bold 12px Verdana">{{$pat.drinker}}</span></td>
								</tr>
								{{if $pat.er_location != ''}}
								<tr>
									<td align="right" bgcolor="#eaeaea">Area Location:</td>
									<td align="left" colspan="3"><span style="font:bold 12px Verdana">{{$pat.er_location}}</span></td>
								</tr>
								{{/if}}
							</table>
						</td>
						<td width="1" valign="top" align="center">
							<span><img src="../../fotos/photo.php?pid={{$pat.pid}}&w=96" width="96" height="96" style="-moz-border-radius:8px" /></span>
							<br/>
							<br/>
							{{$doctors}}
						</td>
					</tr>
					<tr>
						<td align="left" colspan="2" style="padding:4px">
							<!--<button class="button" onclick="PatientHistory_openClinical();return false" {{$disable}}><img src="../../gui/img/common/default/folder_explore.png"/>Clinical Sheet</button>-->
							<button class="button" onclick="PatientHistory_openRequest();return false" {{$disable}}><img src="../../gui/img/common/default/folder_explore.png"/>Requests</button>

							<button class="button" onclick="PatientHistory_openPrescription();return false;" {{$show}}{{$disable}}><img src="../../gui/img/common/default/rx.png"/>Prescription</button>

							<button class="button" onclick="PatientHistory_openDischargeSlip();return false;" {{$show_er}}{{$enable}}{{$disableRad}}><img src="../../gui/img/common/default/page_green.png"/>Discharge Slip</button>

							<button class="button" onclick="OpenPH_IpbmSlip({{$pat.encounter_type}});return false;"{{$show_ipbm}}{{$enable}}{{$disableRad}}><img src="../../gui/img/common/default/page_paste.png"/>Discharge Slip</button>

							<button class="button" onclick="open_medicalAbstract('{{$pat.encounter}}',0);return false;" {{$disableabst}}{{$hide_abstract}}><img src="../../gui/img/common/default/pdf-icon.png"/>Medical Abstract</button>					
							<!-- <button class="button" onclick="PatientHistory_IpbmDischargeSlipIPD();return false;"{{$show_ipbm}}{{$enable}}{{$disableRad}}><img src="../../gui/img/common/default/page_paste.png"/>Discharge Slip</button> -->
							
							{{$btn_untagDoctor}}
							{{$btn_tagDoctor}}
					
							{{$refer}}
							{{$updateSmokerDrinkerData}}
							<!-- uncomment by Macoy Sept. 2, 1024 -->
							{{$medcert}}
							{{$psymedcert}}
							<!-- end -->
							{{$sDeathButton}} <!-- rebranched carriane 01-19-18 from arnel's branch -->
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div class="segPanel" id="refer_patient" style="display:none" align="center">
    <div align="center" style="overflow:hidden">
	    <table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
	        <tbody>
	            <tr>
	                <td rowspan="3" class="segPanel" align="center" valign="top">
	                    <table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
	                        <tr>
	                            <td valign="top"><strong>Refer to</strong></td>
	                            </tr>
	                		<tr>
	                			<td>{{$sDept}}</td>
	                		</tr>
	                        <tr>
	                           <td valign="top"><strong>Reason for Referral</strong> </td>
	                        </tr>
	                        <tr>
	                			<td>{{$sReason}}</td>
	                		</tr>
	                    </table>
	                </td>
	            </tr>
	        </tbody>
	    </table>
    </div>
</div>

<div class="segPanel" id="smoker_drinker_patient" style="display:none" align="center">
    <div align="center" style="overflow:hidden">
	    <table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
	        <tbody>
	            <tr>
	                <td rowspan="3" class="segPanel" align="center" valign="top">
	                    <table width="95%" border="0" cellpadding="1" cellspacing="0" style="font-size:11px" >
	                        <tr>
								<td class="reg_item"><STRONG>History of Smoking:</STRONG></td>
								{{$sSmoker}}
							</tr>
							<tr>
								<td class="reg_item"><STRONG>Alcohol Drinker:</STRONG></td>
								{{$sDrinker}}
							</tr>
	                    </table>
	                </td>
	            </tr>
	        </tbody>
	    </table>
    </div>
</div>
<div id="history_referral" class="history_referral" style="width:100%; overflow:hidden; padding:0;" align="center">
    <div id="PatientInfo-referral-list1-{{$dashlet.id}}" style="width:100%; overflow:hidden; padding:0"></div>
</div>    
{{$jsCalendarSetup}}
{{$isinfant}}
{{$hasSaved}}
<script type="text/javascript">

//Added by EJ 11/26/2014
function PatientHistory_openDischargeSlip()
{
	Dashboard.launcher.launch({
		title:'Discharge Slip',
		href:'../../modules/dashboard/discharge-slip.php{{$URL_APPEND}}&encounter_nr={{$pat.encounter}}',
		width: 820,
		height: 450
	})
}


function open_medicalAbstract(nr,viewonly = 0)
{
	var url = '../../modules/registration_admission/seg-patient-medical_abstract.php?encounter_nr='+nr+'&from=dashboard&flag=1&viewonly='+viewonly;
	$J('<div></div>').html('<iframe style="width:100%;height:100%" src="'+url+'"></iframe>').dialog({
		title: "Medical Abstract",
		width:850,
		height:450,
		resizable: false,
		draggable: false,
		close:function(){
			MedAbstRefresh();
		}
	});

}

//Added by Jeff 05/16/2018

function OpenPH_IpbmSlip(type){
	if(type == '13'){
		PatientHistory_IpbmDischargeSlipIPDnew();
	}else{
		PatientHistory_IpbmDischargeSlip();
		
	}
}
function PatientHistory_IpbmDischargeSlip()
{
	Dashboard.launcher.launch({
		title:'IPBM Discharge Slip',
		href:'../../modules/dashboard/ipbm-discharge-slip.php{{$URL_APPEND}}&encounter_nr={{$pat.encounter}}&encounter_type={{$pat}}',
		width: 820,
		height: 450
	})
}

function PatientHistory_IpbmDischargeSlipIPD()
{
	Dashboard.launcher.launch({
		title:'IPBM Discharge Slip IPD',
		href:'../../modules/dashboard/ipbm-discharge-slip-ipd.php{{$URL_APPEND}}&encounter_nr={{$pat.encounter}}&encounter_type={{$pat}}',
		width: 820,
		height: 450
	})
}

function PatientHistory_IpbmDischargeSlipIPDnew()
{
	Dashboard.launcher.launch({
		title:'IPBM Discharge Slip IPD',
		href:'../../modules/dashboard/ipbm-discharge-slip-ipd-new.php{{$URL_APPEND}}&encounter_nr={{$pat.encounter}}&encounter_type={{$pat}}',
		width: 820,
		height: 450
	})
}

function PatientHistory_openPrescription()
{
	Dashboard.launcher.launch({
		title:'Write prescription',
		href:'../../modules/prescription/seg-clinic-new-prescription.php{{$URL_APPEND}}&checkintern=1&encounter_nr={{$pat.encounter}}',
		width: 820,
		height: 450
	})
}

function PatientHistory_openRequest()
{
	Dashboard.launcher.launch({
		title:'Clinical Requests',
		href:'../../modules/clinics/seg-clinic-charges.php{{$URL_APPEND}}&checkintern=1&encounter_nr={{$pat.encounter}}&ptype=doctor'+'&pid={{$pat.pid}}'+'&doc_nr={{$pat.doc_nr}}'+'&or_no={{$pat.or}}&user_from=DOCTOR'+'&enc_accomodation={{$pat.accomodation}}', //Updated by Christian 12-03-19
		width:1100,
	})
}

function PatientHistory_setDoctors() {
	Dashboard.dashlets.sendAction("{{$dashlet.id}}", "setDoctors", {
		data:"{{$pat.encounter}}",
		or : "{{$pat.or}}"
	});	
}

function PatientHistory_unsetDoctors() {
	Dashboard.dashlets.sendAction("{{$dashlet.id}}", "unsetDoctors", {
		data:"{{$pat.encounter}}"
	});	
}

function PatientHistory_referPatient(doc_nr){
	var penc = "{{$pat.encounter}}";
	var senc = $J('#selected-encounter').attr('data-encounter');
	if(penc != senc)
		return false;

	$J( "#refer_patient" ).dialog({	
        autoOpen: true,
        modal:true,
        height: 240,
        width: 360,
        show: "blind",
        hide: "explode",
        title: "Refer Patient",
        position: "center",
        buttons: {
                Submit: function() {
                	if(validateReferral()){
                		Dashboard.dashlets.sendAction("{{$dashlet.id}}", "referPatient", {
							dept: $J('#dept').val(),
						 reason : $J('#reason').val(),
						 doc_nr : doc_nr,
							 enc: "{{$pat.encounter}}",
							 pid: "{{$pat.pid}}",
							 hs : "1"
						});
                		//$('PatientInfo-referral-list-{{$dashlet.id}}').list.refresh();
                		$J('#dept').val('');
                		$J('#reason').val('');
						$J(this).dialog( "close" ); 
					}
            		 
				},
                Cancel: function(){
					$J(this).dialog( "close" );
                }
        }
    });
}

function PatientHistory_undoReferPatient(ref){
	var msg = confirm("Are you sure you want to Delete the referral?")
	if(msg){
		Dashboard.dashlets.sendAction("{{$dashlet.id}}", "undoReferPatient", {
			ref : ref
		});
	}
}

function PatientHistory_updateSmokerDrinkerData(){
	$J( "#smoker_drinker_patient" ).dialog({
        autoOpen: true,
        modal:true,
        height: 180,
        width: 330,
        show: "blind",
        hide: "explode",
        title: "Update Smoker/Drinker Data",
        position: "center",
        buttons: {
                Submit: function() {
                		Dashboard.dashlets.sendAction("{{$dashlet.id}}", "updateSmokerDrinkerData", {
							smoker: $J('[name="smoker"]:checked').val(),
						   drinker: $J('[name="drinker"]:checked').val(),
							   enc: "{{$pat.encounter}}"
						});
            		$J(this).dialog( "close" );  
				},
                Cancel: function(){
					$J(this).dialog( "close" );
                }
        }
    });
}

function validateReferral(){
	if($J('#dept').val()==""){
		alert("Please select a department");
		$J('#department').focus();
		return false;
	}else if($J('#reason').val()==""){
		alert("Please select a reason for referral");
		$J('#referral_reason').focus();
		return false;
	}else{
		return true;
	}
}

function viewCertMed(pid,encounter,is_ipbm=0)
{
	var ipbmExtend = is_ipbm == 1 ? '&isipbm=1' : '';
	var url = '../../modules/registration_admission/certificates/cert_med_interface.php?encounter_nr='+encounter+'&from=dashboard&flag=1'+ipbmExtend;
	$J('<div></div>').html('<iframe style="width:100%;height:100%" src="'+url+'"></iframe>').dialog({
		title: "Medical Certificate",
		width:850,
		height:450,
		resizable: false,
		draggable: false,
		close:function(){
			MedCertRefresh();
		}
	});
}

function showHistoryReferral(doc_nr){
	var hist_ref = $J('.history_referral');
	if(hist_ref.length > 1){
		hist_ref[1].parentNode.remove();
	}
	var penc = "{{$pat.encounter}}";
	var senc = $J('#selected-encounter').attr('data-encounter');
	if(penc != senc)
		return false;

	$J('#PatientInfo-referral-list-{{$dashlet.id}}').attr('id','PatientInfo-referral-list1-{{$dashlet.id}}');
		ListGen.create("PatientInfo-referral-list1-{{$dashlet.id}}", {
			id:'PatientInfo-referral-listgen-{{$dashlet.id}}',
			width: "100%",
			height: "auto",
			url: "dashlets/PatientInformation/ReferralHistory.php",
			showFooter: true,
			iconsOnly: true,
			effects: true,
			dataSet: [],
			autoLoad: true,
			maxRows: 5,
			rowHeight: 32,
			layout: [
				//['<h1>My Patients</h1>'],
				['#first', '#prev', '#pagestat', '#next', '#last', '#refresh'],
				['#thead'],
				['#tbody']
			],
			columnModel:[
				{
					name: "",
					label: '',
					width: 30,
					sortable: false,
					visible: true,
					styles: {
						textAlign: "center",
						whiteSpace: "nowrap"
					},
					render: function(data, index)
					{
						var row = data[index];
							return '<img class="link" src="../../images/cashier_delete_small.gif" onclick="PatientHistory_undoReferPatient(\''+row["referral_nr"]+'\');return false;"/>';
					}
				},
				{
					name: "date",
					label: "Date",
					width: 100,
					styles: {
						color: "#000080",
						textAlign: "center"
					},
					sorting: ListGen.SORTING.desc,
					sortable: true,
					visible: true
				},
				{
					name: "department",
					label: "Department",
					width: 150,
					sorting: ListGen.SORTING.none,
					sortable: true,
					visible: true,
					styles: {
						fontSize: "12px",
						color: "#c00000"
					}
				},
				{
					name: "reason",
					label: "Reason for Referral",
					width: 150,
					sorting: ListGen.SORTING.none,
					sortable: true,
					visible: true,
					styles: {
						fontSize: "12px",
						color: "#c00000"
					}
				}
			]
		});
		
	$J('#PatientInfo-referral-list1-{{$dashlet.id}}').attr('id','PatientInfo-referral-list-{{$dashlet.id}}');
	
	$J( "#history_referral" ).dialog({
        autoOpen: true,
        modal:true,
        width: "auto",
		height: "auto",
        show: "blind",
        hide: "explode",
        title: "Referral History",
        position: "center",
        buttons: {
                Create_Referral: function() {
                		PatientHistory_referPatient(doc_nr);
				},
                Cancel: function(){
					$J(this).dialog( "close" );
                }
        }
    });
}

function PatientHistory_printDeathCert(id){
	var isinfant = $J('#isinfant').val();

	window.open("./../registration_admission/certificates/cert_death_adult.php?id="+id+"&isinfant="+isinfant,"deathCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
}

/* rebranched carriane 01-19-18 from rnel's branch */

function PatientInformation_deathCertificate() {
	var isinfant = $J("#isinfant").val();

	Dashboard.launcher.launch({
		title:'Death Certificate',
		href:'../../modules/dashboard/DeathCertificate.php{{$URL_APPEND}}&encounter_nr={{$pat.encounter}}&pid={{$pat.pid}}&isinfant='+isinfant,
		width: 1195,
		height: 400
	});

}

/* end carriane */

function refreshReferral(){
	$('PatientInfo-referral-list-{{$dashlet.id}}').list.refresh(); 
}

function assignValue(elem,val){
	if(elem==1){
		$J('#dept').val(val);
	}else{
		$J('#reason').val(val);
	}
}

</script>