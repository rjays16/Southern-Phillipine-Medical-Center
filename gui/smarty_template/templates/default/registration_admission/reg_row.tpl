{{if $sItem == "Date of Birth"}}
	<span {{$sOverLib}}>{{$sNotifier}} {{$sInput}}</span>
{{else}}
	<tr {{$segClassName}}>
	  <td class="reg_item" {{$sColSpan1}}>{{$sItem}}</td>
	  <td class="reg_input" {{$sOverLib}} {{$sColSpan2}}>{{$sNotifier}} {{$sInput}}</td>
	</tr>
{{/if}}