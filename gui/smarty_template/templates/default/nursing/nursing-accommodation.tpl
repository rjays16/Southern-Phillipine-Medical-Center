
<div class="container-fluid col-md-4" style="margin: 3px;">
	<div class="panel panel-primary">
      	<div class="panel-heading">Patient Details 
		{{if $hasSavedBilling}}
	      	<div style="background-color: white;
					color: red;
					font-style: italic;
					font-weight: bold;
					margin-top: -20px;
					width: 430px;
					text-align: center;
					margin-left: 230px">
				{{$hasSavedBilling}}
			</div>
		{{/if}}
		</div>
      	
      <div class="panel-body">
      	<table class="table table-hover" style="font-family: calibri;margin-bottom: -5px;">
      		<tr>
      			<td>HRN:&nbsp;&nbsp;<strong><span id="panelhrn">{{$hrn}}</span></strong></td>
      			<td>Case No.:&nbsp;&nbsp;<strong><span id="panelencounter">{{$encounter_nr}}</span></strong></td>
      		</tr>
      		<tr>
      			<td>Name:&nbsp;&nbsp;<strong><span id="panelname">{{$fullname}}</span></strong></td>
      			<td>Admission Date and Time:&nbsp;&nbsp;<strong><span id="paneladmissiondt">{{$admission_dt}}</span></strong> </td>
      		</tr>
      	</table>
      </div>
    </div>
  	<div class="panel panel-info">
      <div class="panel-body">
      	<table class="table table-striped table-responsive" id="addNewAcc" style="font-family: calibri;margin-bottom: -5px;border-radius: 3px;">
      		{{if !$isfinal && $hasPermissionAdd}}
	  			<tr>
					<th style="padding:4px;">
						<select style="width:100%;padding:2px;height: 26px" id="wardlist" onchange="jsAccOptionsChange(this, this.options[this.selectedIndex].value, this.options[this.selectedIndex].text)">
							<option value="0" >-Select Ward-</option>
							{{ foreach from=$wardlist item=ward name=wards }}
								<option value="{{$ward.nr}}" datavalue="{{$ward.name}}">
									{{$ward.name}}
								</option>
								</br>
							{{/foreach}}
							
						</select>
					</th>
					<th style="padding:3px;">
						<select style="width:100%;padding:2px;height: 26px" id="roomlist">
							<option value="0" >-Select Room-</option>
						</select>
					</th>
					<th style="padding:3px;">
						<input type="text" style="width:100%;height:26px;padding:2px" class="date_from" id="date_from" placeholder="Select Start Date">
						<input type="text" style="width:100%;height:26px;padding:2px;display:none" class="date_from_time" id="date_from_time" placeholder="Select Start Date">
					</th>
					<th style="padding:3px;" >
						<input type="text" style="width:100%;height:26px;padding:2px" class="date_to" id="date_to" placeholder="Select End Date">
						<input type="text" style="width:100%;height:26px;padding:2px;display:none" class="date_to_time" id="date_to_time" placeholder="Select End Date">
					</th>
					<th style="padding:3px;">
						<button type="button" style="font-family:calibri;height: 26px;" class="btn btn-primary" title="Add" id="addNewRow" {{$disabled}}><label style="margin-top: -2px">ADD</label></button>
					</th>
				</tr>
			{{/if}}
			<tr><th colspan="7" style="background:#d9edf7">Accommodation History</th></tr>
      	</table>

      	<table class="table table-striped table-responsive" id="accommodationinfo" style="font-family: calibri;margin-bottom: -5px;border-radius: 3px;width: 100%">
			<thead>
				<tr>
					<th></th>
					<th>Ward Name</th>
					<th>No. of Day(s)</th>
					<th>Encoder</th>
					<th>Date & Time Encoded</th>
					<th></th>
				</tr>
			</thead>
		
			<tbody id="acccom_list" style="background: white">
				{{ foreach from=$accommodations item=accommodation name=accommodations }}
					<tr id="row_{{$accommodation.ward_id}}">
						<td>
							{{if !$isfinal && !$accommodation.today && $hasPermissionDelete}}
								<img src="../../images/btn_delitem.gif" id="{{$accommodation.ward_id}}" class="imgdelete" style="border-right:hidden; cursor:pointer;" onclick="deleteAccommodation(this.id)">
							{{else}}
								<img src="../../images/btn_delitem.gif" class="imgdelete" style="border-right:hidden;opacity:0.5;" title="Unable to delete current accommodation">
							{{/if}}
						</td>
						<td>{{ $accommodation.ward_name}}</td>
						<td>{{ $accommodation.nofdays}}</td>
						<td>{{ $accommodation.create_id}}</td>
						<td>{{ $accommodation.create_dt}}</td>
						<td></td>
					</tr>
				{{/foreach}}
			</tbody>

			<tfoot>
				<tr>
					<th colspan="6">
						<button type="button" style="font-family:calibri;height: 30px" class="btn btn-info" title="Audit Trail" id="audittrailbtn"><label style="margin-top: -2px">AUDIT TRAIL</label></button>
					</th>
				</tr>
			</tfoot>
		</table>
      </div>
</div>
<div id="opening-message" title="" style="display: none">
  <p style="color:red;font-weight: bold;font-size: 12px; font-style: Tahoma">
    {{$message}}
  </p>
</div>