

var latest;

function filterGlobal () {
    $('#myTable').DataTable().search(
        $('#global_filter').val()).draw();
}
    $(document).ready(function(){
    	 $('#myTable').DataTable({
    	 	language: {
        	searchPlaceholder: "  ALL     * ",
             
    	},
        "columnDefs": [ {
                  "targets": 'no-sort',
                  "orderable": false,
            } ],
    	"sPaginationType": "full_numbers",
    	 });

    	 $('input.global_filter').on( 'change keyup paste', function () {
        filterGlobal();
    	});
 		if ($('#global_filter').val() !="") {
 			filterGlobal();
 		}
 		if (!canCreate) {
 			$('#addArea').hide();
 		}


 	});

			 function dialoGAreas(idAreaCode,Action){
                var areasForm = "seg-inventory-edit.php?area_code="+idAreaCode+"&isAction="+Action;
                var dialogAUditNote = $j('<div id="InventoryAreaDialog"></div>')
                    .html('<iframe id="pharmAreaFrame" style="border: 0px; " src="' + areasForm + '" width="100%" height="345px"></iframe>')
                    .dialog({
                        autoOpen: true,
                        closeOnEscape: false,
                        modal: true,
                        height: "auto",
                        width: "70%",
                        show: 'fade',
                        hide: 'fade',
                        resizable: false,
                        draggable: true,
                        title: 'Inventory Data: ',
                        position: "top",
                         open: function(event, ui) {
                            $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
                        },

                          buttons: {
							        Close: function() {
							         		// $j(this).dialog( "close" );
							          	var ActionTrue=$j('#pharmAreaFrame').contents().find('#hiddenActions').val();
							         	if (ActionTrue ==2 || ActionTrue !="") {
							         		$j(this).dialog( "close" );
				                         	  window.location.reload(true);

				                         }else{
				                         	$j(this).dialog( "close" );
				                         } 
						
							        }
							      }
                    });
    		}



             function inventoryDAI(APIkey){
                var APAIkey ="seg-inventory-check-dai.php?SEGAPIKEY="+APIkey;
                var dialogAUditNote = $j('<div id="inventoryDAI"></div>')
                    .html('<iframe id="modalIframe" style="border: 0px; " src="' + APAIkey + '" width="100%" height="345px"></iframe>')
                    .dialog({
                        autoOpen: true,
                        modal: true,
                        height: "auto",
                        width: "70%",
                        show: 'fade',
                        hide: 'fade',
                        resizable: false,
                        draggable: true,
                        title: 'Inventory Data: ',
                        position: "top",
                          buttons: {
                                    Close: function() {
                                         
                                            $j(this).dialog( "close" );
                                
                                    }
                                  }
                    });
            }

             function AuditTrail(areaCode){
                var AuditTrail ="seg-inventory-audittrail.php?areCode="+areaCode;
                var dialogAUditNote = $j('<div id="audittrail"></div>')
                    .html('<iframe id="modalIframe" style="border: 0px; " src="' + AuditTrail + '" width="100%" height="345px"></iframe>')
                    .dialog({
                        autoOpen: true,
                        modal: true,
                        height: "auto",
                        width: "70%",
                        show: 'fade',
                        hide: 'fade',
                        resizable: false,
                        draggable: true,
                        title: 'Inventory Data: ',
                        position: "top",
                          buttons: {
                                    Close: function() {
                                            $j(this).dialog( "close" );
                                
                                    }
                                  }
                    });
            }
