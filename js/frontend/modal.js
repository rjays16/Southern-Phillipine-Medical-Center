/**
 * TRACER modal loader component
 *
 * requires: jQuery.blockUI
 */

if ('object' !== typeof window.tracer) {
	window.tracer = {};
}
if ('object' !== typeof window.tracer.modal) {

	// window.tracer.modal = {
	// 	/**
	// 	* loads content via GET request
	// 	*/
	// 	load: function(obj, url, options) {
	// 		var $obj = $(obj),
	// 			defaultOptions = {
	// 				url: url,
	// 				beforeSend: function(xhr) {
	// 					$obj.block();
	// 				},
	// 				success: function(data) {
	// 					$obj.html(data).unblock();
	// 				},
	// 				error: function(xhr, textStatus, errorThrown) {
	// 					var error = xhr.responseText ? xhr.responseText : textStatus;
	// 					$obj.html('<div class="alert alert-error">' +   + '</div>').unblock();
	// 				}
	// 			};
	// 		options = $.extend(defaultOptions, options);
	// 		$.ajax(options);
	// 	}
	// };

	// $(function() {
	// 	$('a[data-toggle=modal]').click(function(e){
	// 		var $this=$(this);
	// 		e.preventDefault();
	// 		tracer.modal.load($this.data('target'), $this.attr('href'), [], function(e){
	// 			$this.unblock()});
	// 	});
	// });
}