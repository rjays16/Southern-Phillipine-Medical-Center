{{* used in transferring patient to another machine *}}
{{* machien_transfer_patient.tpl  2014-02-19 Jayson Garcia-OJT*}}
{{* Table frame for the machine list *}}
<fieldset>
   <legend> <h3> Patient Details</h3></legend>
    <table>
        <tbody>
        {{foreach from=$detailView key=k item=v}}
            <tr>
                <td><b>{{$k}}</b></td>
                <td>{{$v}}</td>
            </tr>
        {{/foreach}}
            <tr>
                <td><b>Is PHIC Transaction:</b></td>
                <td><input type="checkbox" name="is_phic" id="is_phic" {{$is_phic}}></td>
            </tr>
        </tbody>
    </table>
</fieldset>
<form id = 'dialyzerForm' name = 'dialyzerForm' method = 'POST' action = {{$formAction}}>
<input type="hidden" name ="dialyzer_serial_nr" id="dialyzer_serial_nr" value="{{$serialNo}}">
<input type="hidden" name ="current_serial_nr" id="current_serial_nr" value="{{$currentSerialNo}}">
<input type="hidden" name ="current_dialyzer_type" id="current_dialyzer_type" value="{{$currentDialyzerType}}">
<input type="hidden" name ="update" id="update" value="{{$isUpdate}}">
<input type="hidden" name ="tnr" id="tnr" value="{{$tnr}}">
<input type="hidden" name ="pid" id="pid" value="{{$pid}}">
<input type="hidden" name ="has-dialyzer" id="has-dialyzer" value="{{$hasReusableDialyzer}}">
    <fieldset>
        <legend><h3> Dialyzer Information</h3></legend>
        <table>
            <tbody>
            <tr>
                <td><b>Date:</b></td>
                <td>
                    <input type="text" id="datefrom" name="datefrom" value="{{$date_accom}}" size=10 maxlength=10/>
                    {{$sDateMiniCalendar}}
                    <select id="timefromHours" name="timefromHours">
                        {{section name=timefromHours start=1 loop=13 step=1}}
                            <option value="{{$smarty.section.timefromHours.index}}"
                                    {{if $smarty.section.timefromHours.index == $timeHours}}selected{{/if}}>
                                {{if $smarty.section.timefromHours.index < 10 }}0{{/if}}{{$smarty.section.timefromHours.index}}
                            </option>
                        {{/section}}
                    </select>
                    <select id="timefromMins" name="timefromMins">
                        {{section name=timefromMins start=0 loop=60 step=1}}
                            <option value="{{$smarty.section.timefromMins.index}}"
                                    {{if $smarty.section.timefromMins.index == $timeMins}}selected{{/if}}>
                                {{if $smarty.section.timefromMins.index < 10 }}0{{/if}}{{$smarty.section.timefromMins.index}}
                            </option>
                        {{/section}}
                    </select>

                    <select id="selAMPM" name="selAMPM">
                        <option value="AM" {{if $meridiem == 'AM'}}selected{{/if}}>AM</option>
                        <option value="PM" {{if $meridiem == 'PM'}}selected{{/if}}>PM</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Machine No:</b></td>
                <td><input type="text" name="machine_nr" data-placeholder="{{$machineNr}}" id="machine_nr" value="{{$machineNr}}"></td>
            </tr>
            <tr>
                <td><b>New:</b></td>
                <td><input type="checkbox" title="New Dialyzer" name="new_dialyser_id"
                           id="new_dialyser_id" {{$checked}} {{$disabled}}></td>
            </tr>
            <tr>
                <td><b>Dialyzer:</b></td>
                <td>
                    <select id="dialyzer_type" name="dialyzer_type" {{$dialyzerTypeHidden}}>
                        {{foreach from=$dialyzerList key=k item=v}}
                            <option value="{{$k}}">{{$v}}</option>
                        {{/foreach}}
                    </select>
                    <span id="plainDialyzerType" {{$hidePlainDialyzerType}}>{{$dialyzerType}}</span>
                </td>
            </tr>
            <tr>
                <td><b>No of Reuse:</b></td>
                <td>
                    <input type="number" id="reuse" name="reuse" value="{{$noOfReuse}}" title="Number of re-use" min="0" {{if $hasReusableDialyzer == 1}}readonly{{/if}}/>
                    <span id="defaultReuse" style="display:none">0</span>
                </td>
            </tr>
            </tbody>
        </table>
    </fieldset>
    <br>
    <table width="15%">
        <tr>
            <td><a href="#" id="saveDialyzer">
                    <img {{$saveButtonImg}}></a></td>
            <td><a href='#' onclick = 'closeWindow()'><img {{$closeButtonImg}}></a></td>
        </tr>
    </table>
</form>
{{$jsCalendarSetup}}
{{$sysErrorMessage}}

