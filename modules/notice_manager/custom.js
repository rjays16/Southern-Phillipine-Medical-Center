$(document).ready(function(){
	$(document).on('click', '.edit', function(){
		var id=$(this).val();
	
		$('#edit').modal('show');
		$('#noticeID').val(id);

	});
});
