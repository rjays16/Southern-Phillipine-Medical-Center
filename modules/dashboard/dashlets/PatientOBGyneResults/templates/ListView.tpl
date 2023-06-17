<div id="px-list-{{$dashlet.id}}" style="width:100%; overflow:hidden; padding:0"></div>
<script type="text/javascript">
ListGen.create("px-list-{{$dashlet.id}}", {
	id:'px-obj-{{$dashlet.id}}',
	width: "100%",
	height: "auto",
	url: "dashlets/PatientOBGyneResults/Listgen.php",
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
			name: "dicom",
			label: "With Dicom",
			width: 80,
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
                    image = "../../gui/img/common/default/ob.png";
                    action = 'PatientOBGyne_OpenResult(\''+row["refno"]+'\',\''+row["pid"]+'\'); loadPacsViewer(\''+row["url"]+'\'); return false;';
                }else{
                    image = "../../gui/img/common/default/findnew.gif";
                    action = 'PatientOBGyne_OpenResultHtml(\''+row["refno"]+'\',\''+row["pid"]+'\'); loadPacsViewer(\''+row["url"]+'\'); return false;';
                }

				return '<button class="button" onclick="'+action+'"><img class="link" src="'+image+'" />Results</button>';
			}
		}
	]
});

function PatientOBGyne_OpenResult(refno, pid) {
	var options = {
		url: '../../modules/radiology/certificates/seg-radio-unified-report-pdf.php',
		width: 700,
		height: 450,
		data: {
			batch_nr:refno,
			pid:pid
			}
	};
	Dashboard.openWindow(options);
}

function PatientOBGyne_OpenResultHtml(refno, pid){
    var options = {
        url: '../../modules/radiology/seg-radio-unified-html.php',
        width: 700,
		height: 450,
        data: {
            batch_nr:refno,
            pid:pid
        }
    };
    Dashboard.openWindow(options);
}

//added by VAN 10/10/2014
//PACS
function closePacsViewer(){
	pacsviewer.close();
}

function loadPacsViewer(url){
	if (url!='null')
		pacsviewer = window.open(url,"pacsviewer","width=800,height=550,top=150,left=200,menubar=no,resizable=yes,scrollbars=yes");
}//========================

</script>