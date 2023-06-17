{{* Frame template of medocs page *}}
{{* Note: this template uses a template from the /registration_admission/ *}}

{{* File using this : modules\social_service\social_service_progress_notes.php *}}
<form id="progress_notes" name="progress_notes" action="Javascript:void(null);" ENCTYPE="multipart/form-data" method="POST"> 
	<div align="left" style="width:100%" class="form-header rounded-borders-top">
	    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
	        <tr>
	            <td width="99%" nowrap="nowrap"><h1>Progress Notes</h1></td>
	        </tr>
	    </table>
	</div>
	<div id="tab_form" align="center" style="width:95%;">
		<ul id="prognotes-tabs" class="tabs-nav">
	        <li><a href="#pn_part1" onClick="" segTab="tab0" segSetMode="pn_form"><span>Progress Notes Form</span></a></li>
	        <!-- <li><a href="#pn_part2" onClick="viewpermssion();" id="tab1" segTab="tab1" segSetMode="pn_view"><span>View Progress Notes</span></a></li> -->
	    </ul>
	    <div id="pn_part1" align="center" style="margin-top:10px;width:98%">
	        <table width="98%" border="0" cellspacing="5" cellpadding="0">
	            <tr>
	                <td valign="top">
	                    <div id="notes" align="left" style="width:10%">
	                        {{$sHiddenInputs}}
	                    </div>
	                    <div id="prognotes_body" class="dashlet" align="left" style="width:100%">
	                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
	                            <tr>
	                                <td>&nbsp;</td>
	                            </tr>
	                            <tr>
	                                <td class="segPanel">
	                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Datetime </strong></td>
	                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$datetime}}{{$calendarButton}}</td>
	                                        {{$jsDatetime}}
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Ward </strong></td>
	                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$ward}}</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Diagnosis </strong></td>
	                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$diagnosis}}</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Referral <strong style="color: red">*</strong></strong></td>
	                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$referral}}{{$internal}}</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Informant <strong style="color: red">*</strong> </strong></td>
	                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$informant}}</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="*" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Relation to Patient <strong style="color: red">*</strong></strong></td>
	                                           <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$reltopatient}}</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Purpose <strong style="color: red">*</strong></strong></td>
	                                            <td width="*" colspan="3" nowrap="nowrap" class="segInput">{{$purpose}}</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Action Taken <strong style="color: red">*</strong></strong></td>
	                                            <td width="*" colspan="3" nowrap="nowrap" class="segInput">{{$action_taken}}</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="20%" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Recommendation <strong style="color: red">*</strong></strong></td>
	                                            <td width="*" colspan="3" nowrap="nowrap" class="segInput">{{$recommendation}}</td>
	                                        </tr>
	                                        <tr>
	                                            <td width="*" nowrap="nowrap" class="reg_item" style="font-size: 13px"><strong>Medical Social Worker </strong></td>
	                                           <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$med_social_worker}}</td>
	                                        </tr>
	                                    </table>
	                                </td>
	                            </tr>
	                        </table>
	                    </div>

	                    <div id ='submit_tab1' style="text-align:right;">
	                    	 {{$pn_audit_trail}}
	                    	 {{$pn_update}}
	                    	 {{$pn_submit}}
	                    	 {{$pn_print}}
	                    	 {{$progNotesbtn}}
	                    </div>
	                </td>
	            </tr>
	        </table>
	    </div>
{{if $permission_all || $permission_view}}
	    <!-- <div id="pn_part2" align="center" style="margin-top:10px;width:98% ">
	        <table width="98%" border="0" cellspacing="5" cellpadding="0">
	            <tr>
	                <td valign="top">
	                    <div id="prognotes_body" class="dashlet"  class="dashlet" align="left" style="width:100%">
	                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
	                            <tr>
	                                <tr>
	                                    <td class="segPanel">
	                                       <div class="active-area drop-shadow pre-space rounded-borders-all">
	                                           <div id="social_form">
	                                                <table class="data-grid" border="0">
	                                                    <thead>
	                                                        <tr>
	                                                            <th width="">Datetime</th>
	                                                            <th width="">Ward</th>
	                                                            <th width="">Diagnosis</th>
	                                                            <th width="">Referral</th>
	                                                            <th width="">Informant</th>
	                                                            <th width="" >Relation to Patient</th>
	                                                            <th width="">Purpose</th>
	                                                            <th width="">Action Taken</th>
	                                                            <th width="">Recommendation</th>
	                                                            <th width="">Medical Social Worker</th>
	                                                            <th width=""></th>
	                                                        </tr>
	                                                    </thead> -->
	                                                    <!-- {{$social_form_data}} -->
	                                                 <!--    <tbody id="social_form_data" ></tbody>
	                                                </table>
	                                              

	                                           </div> -->
	                                             <!--  <div id ='print_tab1' style="text-align:right;">
	                    					 		 {{$pn_print}}
	                    					 		
	                 							  </div> -->
	                                              
	                                        </div>
	                                    </td>
	                                </tr>  
	                            </tr>

	                        </table>
	                        <!-- {{$social_submit}} -->
	                    </div>
	                    </div>
	                   {{/if}}
	                </td>
	            </tr>
	        </table> 
	    </div>
	</div>
</form>
<br />
<br />

<div id="date-dialog" style="display: none;">
	<form id="phic">
		<table class="data-grid rounded-borders-bottom">
       		<tbody>
	            <tr height="55">
	                <td class="sublabel">From:</td>
	                <td width="40%"> 
	                    {{$datefrom_fld}}
	                </td>
	            </tr>
	            <tr height="55">
	                <td class="sublabel">To:</td>
	                <td width="40%"> 
	                    {{$dateto_fld}}
	                </td>
            	</tr>
        	</tbody>
			{{$jsCalendarSetup}}
    	</table>
	</form>
</div>

{{$sTailScripts}}
