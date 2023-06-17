<?php /* Smarty version 2.6.0, created on 2020-02-05 13:46:56
         compiled from ../../../modules/dashboard/dashlets/MedicalAbstract/templates/view.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../../modules/dashboard/dashlets/MedicalAbstract/templates/view.tpl', 14, false),)), $this); ?>

<div id="px-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width:100%; overflow:scroll; padding:0"></div>
<script type="text/javascript">
ListGen.create("px-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", {
	id:'px-obj-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
',
	width: "100%",
	height: "auto",
	url: "dashlets/MedicalAbstract/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	effects: true,
	dataSet: [],
	autoLoad: true,
	maxRows: <?php echo ((is_array($_tmp=@$this->_tpl_vars['settings']['pageSize'])) ? $this->_run_mod_handler('default', true, $_tmp, '5') : smarty_modifier_default($_tmp, '5')); ?>
,
	rowHeight: 32,
	layout: [
		//['<h1>My Patients</h1>'],
		['#first', '#prev', '#pagestat', '#next', '#last', '#refresh'],
		['#thead'],
		['#tbody']
	],
	columnModel:[
		{
			name: "req_date",
			label: "Date Request",
			width: 100,
			styles: {
				fontSize: "12px"
			},
			sorting: ListGen.SORTING.desc,
			sortable: true,
			visible: true
		},
		{
			name: "encounter_nr",
			label: "Case Number",
			width: 100,
			styles: {
				fontSize: "12px"
			},
			sorting: ListGen.SORTING.desc,
			sortable: true,
			visible: true
		},
		{
			name: "encounter_date",
			label: "Date Admitted",
			width: 150,
			sortable: false,
			visible: true,
			styles: {
				fontSize: "12px"
			}
		},
		{
			name: "dept",
			label: "Department",
			width: 120,
			sortable: false,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
			}
		},
		{
			name: "create_id",
			label: "Prepared by",
			width: 150,
			sortable: false,
			visible: true,
			styles: {
				fontSize: "12px"
			}
		},
		{
			name: "",
			label: '',
			width: 30,
			// sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
			
				var row = data[index];
               
                    image = "../../images/cashier_edit.gif";
                    action = 'open_medicalAbstract(\''+row["encounter_nr"]+'\',1);return false;';           
               		return '<img class="link" onclick="'+action+'" src="'+image+'">';               		
			}			
		},
		{
			name: "",
			label: '',
			width: 30,
			// sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
				var row = data[index];

                    image = "../../images/cashier_delete.gif";
                    action = 'delete_medicalAbstract();return false;';           
               		return '<img class="link" onclick="'+action+'" src="'+image+'">';               		
			}			
		}
	
	]
});


function delete_medicalAbstract(){
	alert('Cannot delete Medical Abstract. Please contact Medical Records.');
}

function MedAbstRefresh(){
	$('px-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
').list.refresh();
}
</script>