(function ($) {
	
	$(".widget-control-save").click(function(){
			jQuery( document ).ajaxSuccess(function( event, request, settings ) {
				load_color_picker();
			});
		});
 load_color_picker();
}(jQuery));
function load_color_picker(){
		 jQuery('.my-input-class').wpColorPicker();
	}
