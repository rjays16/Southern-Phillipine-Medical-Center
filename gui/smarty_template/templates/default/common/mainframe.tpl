{{* Smarty Template - mainframe.tpl 2004-06-11 Elpidio Latorilla *}}
{{* This is the main template that frames the main work page *}}

{{config_load file=test.conf section="setup"}}

{{include file="common/header.tpl"}}

<div style="width:auto; padding:2px">
<table id="main" width="100%" border="0" cellspacing="0" height="100%">
	<tbody class="main">
	{{if !$newArea}}
			{{if not $bHideTitleBar}}
					<tr>
						<td  valign="top" align="middle" height="35">
							{{include file="common/header_topblock.tpl"}}
						</td>
					</tr>
			{{/if}}
		{{/if}}
		<tr>
			<td bgcolor={{$body_bgcolor}} valign="top">
				<div align="center">
				{{if $sysInfoMessage ne ""}}
					<dl id="system-message">
						<dt>Information</dt>
						<dd>
							{{$sysInfoMessage}}
						</dd>
					</dl>
				{{elseif $sysErrorMessage ne ""}}
					<dl id="error-message">
						<dt>System error</dt>
						<dd>
							{{$sysErrorMessage}}
						</dd>
					</dl>
				{{/if}}

				{{* Note the ff: conditional block must always go together *}}
				{{if $sMainBlockIncludeFile ne ""}}
					{{include file=$sMainBlockIncludeFile}}
				{{/if}}
				{{if $sMainFrameBlockData ne ""}}
					{{$sMainFrameBlockData}}
				{{/if}}
				{{* end of conditional block *}}

				</div>
			</td>
		</tr>
		{{if !$newArea}}
			{{if not $bHideCopyright}}
					<tr valign=top label="copyright">
						<td bbgcolor={{$bot_bgcolor}} bgcolor="ffffff">
							{{include file="common/copyright.tpl"}}
						</td>
					</tr>
			{{/if}}
		{{/if}}
	</tbody>
</table>
</div>
<script type="text/javascript">
	window.addEventListener('storage', function (event) {
		if (event.key == 'seghis-login') {
			if(event.newValue == 0){
				var l = window.location,
	            	baseUrl = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];

	            if(window.parent)
	            	window.parent.location = baseUrl;
	            else
	            	window.location = baseUrl;
			}
		}
	});
</script>
{{include file="common/footer.tpl"}}