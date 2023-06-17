<!-- added by Macoy 23, 2014 -->
<div id="previous_cases" style="width:100%; overflow:hidden; padding:0;" align="center">
    <div id="radio-result1-{{$dashlet.id}}" style="width:100%; overflow:hidden; padding:0"></div>
</div>
<!-- end -->
<div id="px-history-{{$dashlet.id}}" style="border:0; padding:0; width:100%; overflow:hidden;"></div>
<script type="text/javascript">

var temp_flag = false;

ListGen.create("px-history-{{$dashlet.id}}", {
	id:'px-hist-obj-{{$dashlet.id}}',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientHistory/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	effects: true,
	dataSet: [],
	autoLoad: true,
	maxRows: {{$settings.pageSize|default:"5"}},
	rowHeight: 32,
	layout: [
		['#first', '#prev', '#pagestat', '#next', '#last', '#refresh'],
		['#thead'],
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
			name: "admission",
			label: "Admission",
			width: 90,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				fontSize: "11px",
				textAlign: "center"
			},
			render: function(data, index)
			{
				var row=data[index];
				return '<div>'+row['admission']+'</div>'+
					'<div style="font:normal 11px Tahoma; color:#0000c4">'+row['encounter']+'</div>';
			}
		},
		{
			name: "department",
			label: "Department",
			width: 120,
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
			label: 'Notes',
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
				return '<img class="link" src="../../images/cashier_view.gif" onclick="openDrNotesView(\''+row['encounter']+'\')"/>';
			}
		},
		{
			name: "previous",
			label: 'Radiology',
			width: 90,
			sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
				var row = data[index];

				image = "../../gui/img/common/default/findnew.gif";
                action = 'openRadioResult(\''+row["encounter"]+'\');return false;';

				return '<button class="button" onclick="'+action+'"><img class="link" src="'+image+'" />Results</button>';
			}

		}
	]
});

function openDrNotesView(encounter_nr)
{
	Dashboard.launcher.launch({
			title:'Doctor\'s Notes',
			href:'../../modules/dashboard/dashlets/PatientHistory/viewDrNotes.php?encounter_nr='+encounter_nr,
			width: 700,
			height: 450
		})
}

/*
*added by Macoy 23,2014
*function for button, to view Previous Radio Results
*/

function openRadioResult(encounter_nr)
{
	if(!temp_flag){
			temp_flag = true;
			$J('#radio-result-{{$dashlet.id}}').attr('id','radio-result1-{{$dashlet.id}}');
			ListGen.create("radio-result1-{{$dashlet.id}}", {
				id:'px-obj-{{$dashlet.id}}',
				params: {
		            'encounter': encounter_nr
		        },
				width: "100%",
				height: "auto",
				url: 'dashlets/PatientHistory/PreviousRadioCases.php',		
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
						label: "Request Date",
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
						name: "service",
						label: "Service(s) requested",
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

			                if(row['permission'] == 1){
			                    image = "../../gui/img/common/default/film.png";
			                    action = 'PatientRadio_OpenResult(\''+row["refno"]+'\',\''+row["pid"]+'\');return false;';
			                }else{
			                    image = "../../gui/img/common/default/findnew.gif";
			                    action = 'PatientRadio_OpenResultHtml(\''+row["refno"]+'\',\''+row["pid"]+'\');return false;';
			                }

							return '<button class="button" onclick="'+action+'"><img class="link" src="'+image+'" />Results</button>';
						}
					}
				]
			});
	}else{
		$("radio-result-{{$dashlet.id}}").list.params = {
	        'encounter': encounter_nr
	    };
	    $("radio-result-{{$dashlet.id}}").list.refresh();
	}
		
	$J('#radio-result1-{{$dashlet.id}}').attr('id','radio-result-{{$dashlet.id}}');
	
	$J( "#previous_cases" ).dialog({
        autoOpen: true,
        modal:true,
        width: "auto",
		height: "auto",
		resizable: false,
        show: "blind",
        hide: "explode",
        title: "Radiology Results",
        position: "center"
    });
}

	function PatientRadio_OpenResult(refno, pid) {
		var options = {
			url: '../../modules/radiology/certificates/seg-radio-unified-report-pdf.php',
			data: {
				batch_nr:refno,
				pid:pid
				}
		};
		Dashboard.openWindow(options);
	}

	function PatientRadio_OpenResultHtml(refno, pid){
	    var options = {
	        url: '../../modules/radiology/seg-radio-unified-html.php',
	        data: {
	            batch_nr:refno,
	            pid:pid
	        }
	    };
	    Dashboard.openWindow(options);
	}
/*
*End------------------
*/
</script>