<div id="counsel-history-{{$dashlet.id}}" style="border:0; padding:0; width:100%; overflow:hidden;"></div>
<script type="text/javascript">

var temp_flag = false;

ListGen.create("counsel-history-{{$dashlet.id}}", {
	id:'counsel-hist-obj-{{$dashlet.id}}',
	width: "100%",
	height: "auto",
	url: "dashlets/CounselingNotes/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	effects: true,
	dataSet: [],
	autoLoad: true,
	maxRows: {{$settings.pageSize|default:"4"}},
	rowHeight: 32,
	layout: [
		['#first', '#prev', '#pagestat', '#next', '#last', '#refresh'],
		['#thead'],
		['#tbody']
	],
	columnModel:[
		{
			name: "caseno",
			label: "Case Number",
			width: 150,
			styles: {
				color: "#000080",
				textAlign: "center"
			},
			sorting: ListGen.SORTING.desc,
			sortable: true,
			visible: true
		},
		{
			name: "assessedby",
			label: "Assessed By",
			width: 180,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
			}
		},
		{
			name: "options",
			label: 'Options',
			width: 60,
			sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
				var row = data[index];
				return '<img class="link" src="../../images/cashier_view.gif" onclick="openCounselingView(\''+row['options']+'\')"/>';
			}
		},
		{
			name: "status",
			label: "Status",
			width: 120,
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

function openCounselingView(encounter_nr)
{
        if (window.showModalDialog){  
            window.showModalDialog("counseled-slip-pdf.php?encounter_nr="+encounter_nr+"");
        }else{
            window.open("counseled-slip-pdf.php?encounter_nr="+encounter_nr,"modal, width=600,height=1000,menubar=no,resizable=yes,scrollbars=no");
        }
}


/*
*End------------------
*/
</script>