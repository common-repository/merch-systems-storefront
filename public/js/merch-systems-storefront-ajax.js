(function( $ ) {
	'use strict';
	$(document).ready(function() {
		var menuItemId = merchsys_store_obj.shop_page_id;
		var itemFound = false;
		if ($('.item-id-'+menuItemId).length == 0) {
			merchsys_store_obj.other_shop_pages_ids = $.parseJSON(merchsys_store_obj.other_shop_pages_ids);
			$(merchsys_store_obj.other_shop_pages_ids).each(function(i, v) {
				if ($('.item-id-'+v).length > 0 && itemFound === false) {			
					menuItemId = v;
					itemFound = true;
				}
			});
		}
		if (merchsys_store_obj.add_menu == 1) {
			$('.item-id-'+menuItemId).append(merchsys_store_obj.categories_menu);
		}
		$('.item-id-'+menuItemId).closest('ul').each(function(){
			$(this).append(merchsys_store_obj.basket_item);
		});
    $('ul#footer-navigation').html(merchsys_store_obj.login_item);
	});
})( jQuery );