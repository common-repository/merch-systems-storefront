<div class="wrap">
    <h2>merch.systems Storefront <?php _e("Settings");?></h2>
    <form method="post" action="options.php">
	<?php settings_fields('merchsys_cart_group');
do_settings_sections('merchsys_cart_group');
?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Show carousel in shop main page?', 'merch-systems-storefront');?>:</th>
				<td>
					<label for="merchsys_showshopcarousel">
						<input type="checkbox" name="merchsys_showshopcarousel" value="1" <?php echo (intval(get_option('merchsys_showshopcarousel')) == 1 ? 'checked="checked"' : ''); ?>>
					</label>
				</td>
			</tr>
			<hr/>
			<tr valign="top">
				<th scope="row"><?php _e('Add categories to page menu as submenu?', 'merch-systems-storefront');?>:</th>
				<td>
					<label for="merchsys_addmenu">
						<input type="checkbox" name="merchsys_addmenu" value="1" <?php echo (intval(get_option('merchsys_addmenu')) == 1 ? 'checked="checked"' : ''); ?>>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Show categories on default screen?', 'merchsys-store'); ?>:</th>
				<td>
					<label for="merchsys_showcategories">
						<input type="checkbox" name="merchsys_showcategories" value="1" <?php echo (intval(get_option('merchsys_showcategories')) == 1 ? 'checked="checked"' : ''); ?>>
					</label>
				</td>
			</tr>	
			<tr valign="top">
				<th scope="row"><?php _e('Show login/logout to navigation?', 'merch-systems-storefront');?>:</th>
				<td>
					<label for="merchsys_showloginmenu">
						<input type="checkbox" name="merchsys_showloginmenu" value="1" <?php echo (intval(get_option('merchsys_showloginmenu')) == 1 ? 'checked="checked"' : ''); ?>>
					</label>
				</td>
			</tr>
			<!--<tr valign="top">
				<th scope="row"><?php _e('Navigation to add the login/logout and basket items to', 'merch-systems-storefront');?>:</th>
				<td><select name="merchsys_navigationname">
				<option><?php _e('Please select a menu', 'merch-systems-storefront');?></option>
				<?php
if ($menus != null) {
    foreach ($menus as $location => $description) {
        echo '<option value="' . $location . '" ' . (get_option('merchsys_navigationname') == $location ? 'selected="selected"' : '') . '>' . $description . '</option>';
    }
}
?>
				</select></td>
			</tr>-->
			<tr valign="top">
				<th scope="row"><?php _e('Menu item wrapper for added items (%l for the link, %c for the item css class and %t for the text)', 'merch-systems-storefront');?>:</th>
				<td><textarea name="merchsys_loginmenuwrapper" placeholder='<li class="menu-item %c"><a href="%l">%t</a></li>'><?php echo esc_attr(get_option('merchsys_loginmenuwrapper')); ?></textarea></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Menu item wrapper for the basket (%a for the amount of basket items, %l for the link, %c for the item css class and %t for the text)', 'merch-systems-storefront');?>:</th>
				<td><textarea name="merchsys_basketmenuwrapper" placeholder='<li class="menu-item %c"><a href="%l">%t (%a)</a></li>'><?php echo esc_attr(get_option('merchsys_basketmenuwrapper')); ?></textarea></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Maximum amount of basket items per product (default is 10)', 'merch-systems-storefront');?>:</th>
				<td><input type="number" name="merchsys_maxamount" value="<?php echo (get_option('merchsys_maxamount') != null ? get_option('merchsys_maxamount') : 10); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Go to basket after adding an item?', 'merch-systems-storefront');?>:</th>
				<td>
					<label for="merchsys_gobasket">
						<input type="checkbox" name="merchsys_gobasket" value="1" <?php echo (intval(get_option('merchsys_gobasket')) == 1 ? 'checked="checked"' : ''); ?>>
					</label>
				</td>
			</tr>
		</table>
	<?php submit_button();?>
	</form>
	<h3><?php _e("Set-up", 'merch-systems-storefront');?></h3>
	<h4><b><?php _e("Minimum set-up");?>:</b></h4><p>[merchsys_shop privacy_page="URL TO PRIVACY PAGE" terms_page="URL TO TERMS PAGE"]</p>
	<h4><?php _e("Options");?>:</h4>
	<ul>
		<li>privacy_page | <?php _e('The Url to the privacy page.', 'merchsys-teasers');?></li>
		<li>terms_page | <?php _e('The Url to the Terms page.', 'merchsys-teasers');?></li>
		<li>locale | <?php _e('The locale for the teaser. If not specified it takes the default for the website.', 'merchsys-teasers');?></li>
	</ul>
	<h4><b><?php _e("Example with all options");?>:</b></h4>
	<p>[merchsys_shop privacy_page="URL TO PRIVACY PAGE" terms_page="URL TO TERMS PAGE" locale="en_GB"]</p>
	<h3>Visual composer</h3>
	<p><?php _e('If you use visual composer in your theme you can add the MerchSys Shop shortcode from the shortcodes list (search for MerchSys)', 'merch-systems-storefront');?></p>
	<h2><?php _e('Important!', 'merch-systems-storefront');?></h2>
	<p><?php _e('After activating the plugin and the initial set-up please save the permalinks in the Wp settings page in order for the shop to work.', 'merch-systems-storefront');?></p>
</div>