(function( $ ) {
	'use strict';

	$(document).ready(function(){
		//for base plugin
		$('.thumbs a').on('click', function(e){
			e.preventDefault();
			$($(this).data('image')).find('img').attr('src', $(this).attr('href'));
		});  
		
		$('input.field-different_address').on('click', function(){
			if ($(this).is(':checked')) {
				$('form.form-different_address').show();
			}
			else {
				$('form.form-different_address').hide();
			}
		});
		if ($('#main_image').length > 0) {
			var mainImg = $('#main_image').detach();
			$('#masthead').after(mainImg);
		}
	});
})( jQuery );