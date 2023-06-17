<div id="PatientList-{{$dashlet.id}}" style="width:100%; overflow:hidden; padding:0"></div>
<script type="text/javascript">
ListGen.create("PatientList-{{$dashlet.id}}", {
	id:'PatientListObject-{{$dashlet.id}}',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientList/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	params: {
		filter: '{{$settings.filter}}'
	},
	pageStat: 'Items {from}-{to} of {total}',
	effects: true,
	autoLoad: true,
	maxRows: {{$settings.pageSize|default:"5"}},
	rowHeight: 32,
	layout: [
		['#pagestat', '#first', '#prev', '#next', '#last'],
		['<div align="left" style="padding:2px">'+
				'<input type="text" class="input" size="54" value="{{$session.search}}" onkeyup="if (event.keyCode==$J.ui.keyCode.ENTER) $J(this).next().click()" placeholder="Search HRN or Last Name, First Name or Case No."/> '+
				'<button class="lg-toolbar-button" '+
					'onclick="$(\'PatientList-{{$dashlet.id}}\').list.params.key = $(this).previous().value; $(\'PatientList-{{$dashlet.id}}\').list.refresh();return false;">'+
				'<img src="../../gui/img/common/default/magnifier.png"/>Search</button>'+
			'</div>'
		],
		['#tbody']
	],
	columnModel:[
		{
			name: "date",
			label: "Case Date",
			width: 80,
			styles: {
				color: "#000080",
				textAlign: "center"
			},
			sorting: ListGen.SORTING.desc,
			sortable: true,
			visible: true
		},
		{
			name: "name",
			label: "Patient name",
			width: 140,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			render: function(data, index)
			{
				var row = data[index], selected = (row['encounter'] == row['active']);
				return '<div style="color:#2d2d2d; font-size:11px">'+row['name']+'</div>'+
					'<div style="font-size:10px; color: #c00000">'+row['confinement']+'</div>';
			}
		},
		{
			name: "options",
			label: '',
			width: 85,
			sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
				var row = data[index];
				var selected = (row['encounter'] == row['active']);
				if (selected)
					// return '<button class="button" onclick="return false;" disabled="disabled"><img src="../../gui/img/common/default/emoticon_smile.png" />Active</button>';
					return '<button class="button" onclick="return false;" disabled="disabled" id="selected-encounter" data-encounter="'+row["encounter"]+'"><img src="../../gui/img/common/default/emoticon_smile.png" />Active</button>';
				else
					return '<button class="button" onclick="Dashboard.dashlets.sendAction(\'{{$dashlet.id}}\', \'openFile\', {file:\''+row['encounter']+'\'}); return false;"><img src="../../gui/img/common/default/accept.png" />Select</button>';
			}
		},
		// added by : syboy 06/13/2015
		{
			name : "is_discharged",
			label : 'Status',
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				textAlign: "center"
			},
			render: function(data, index)
			{
				var row = data[index], selected = (row['encounter'] == row['active']);
				if (row['is_discharged'] == 1) {
					return '<div style="color:#2d2d2d; font-size:11px">Discharged</div>';
				} else {
					return '<div style="color:#2d2d2d; font-size:11px"></div>';
				}
			}
		}
		// end
	]
});
</script>