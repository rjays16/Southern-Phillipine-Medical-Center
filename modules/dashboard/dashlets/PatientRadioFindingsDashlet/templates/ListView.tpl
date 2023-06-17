<div id="px-list-{{$dashlet.id}}" style="width:100%; overflow:hidden; padding:0"></div>
<script type="text/javascript">
ListGen.create("px-list-{{$dashlet.id}}", {
	id:'px-obj-{{$dashlet.id}}',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientRadioFindingsDashlet/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	effects: true,
	dataSet: [],
	autoLoad: true,
	maxRows: {{$settings.pageSize|default:"5"}},
	rowHeight: 32,
	layout: [
		//['<h1>My Patients</h1>'],
		['#first', '#prev', '#pagestat', '#next', '#last', '#refresh'],
		['#thead'],
		['#tbody']
	],
	columnModel:[
		{
			name: "date",
			label: "Date Request",
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
			name: "refno",
			label: "Batch Number",
			width: 150,
			sortable: false,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
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
					return '<button class="button" onclick="PatientRadio_OpenFindings(\''+row["refno"]+'\',\''+row["pid"]+'\');return false;"><img class="link" src="../../gui/img/common/default/magnifier.png" />Details</button>';
			}
		}
	]
});

function PatientRadio_OpenFindings(refno, pid) {
	Dashboard.launcher.launch({
		title:'Radiology Findings',
		href:'../../modules/radiology/seg-radio-unified-requests.php{{$URL_APPEND}}&batch_nr='+refno+'&pid='+pid,
		width: 820,
		height: 400
	})
}
</script>
