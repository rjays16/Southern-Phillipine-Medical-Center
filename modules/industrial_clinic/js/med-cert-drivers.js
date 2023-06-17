var $J = jQuery.noConflict();

$J(document).ready(function(){

			$J('#abnormality').hide();
			$J('#contagious_diseases').hide();

			var condition = $J('input:radio[name="comment_drive"]:checked').val();
			if(condition == 3 ){
				$J('#get_conditions').show();
			}else{
				$J('#get_conditions').hide();
			}

			var val = $J('input:radio[name="with_disease"]:checked').val();
			if (val == 1) {
				$J('#contagious_diseases').show();
			}else{
				$J('#contagious_diseases').hide();
			}
			
			var val = $J('input:radio[name="general_physique"]:checked').val();
			if (val == 'Abnormal') {
				$J('#abnormality').show();
			}else{
				$J('#abnormality').hide();
			}

});

$J(function () {

	/* show/hide conditions */
	$J('input:radio[name="comment_drive"]').click( function(){
		var val = $J('input:radio[name="comment_drive"]:checked').val();
		if (val == 3) {
			$J('#get_conditions').show();
		}else{
			$J('#get_conditions').hide();
			$J('INPUT[name="conditions[0]"]').prop('checked', false);
			$J('INPUT[name="conditions[1]"]').prop('checked', false);
			$J('INPUT[name="conditions[2]"]').prop('checked', false);
			$J('INPUT[name="conditions[3]"]').prop('checked', false);
			$J('INPUT[name="conditions[4]"]').prop('checked', false);
		}
	});
	/* end show/hide */

	/* show/hide gen-physique abnormality*/
	$J('input:radio[name="general_physique"]').click( function(){
		var val = $J('input:radio[name="general_physique"]:checked').val();
		if (val == 'Abnormal') {
			$J('#abnormality').show();
		}else{
			$J('#abnormality').hide();
		}
	});
	/* end */

	/* show/hide contagious */
	$J('input:radio[name="with_disease"]').click( function(){
		var val = $J('input:radio[name="with_disease"]:checked').val();
		if (val == 1) {
			$J('#contagious_diseases').show();
		}else{
			$J('#contagious_diseases').hide();
		}
	});
	/* end */

});