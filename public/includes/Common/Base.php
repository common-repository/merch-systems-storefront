<?php

/*
 Plugin Name: merch.systems
 Version: 1.0.0
 Description: Fully integrates your merch.systems online store into your Wordpress website
 Plugin URI: https://merch.systems
 Author: anti-design.com GmbH & Co. KG
 Author URI: http://anti-design.com
 
 @package merchsys
 @subpackage merchsys/public/includes/Shop
 */
 
abstract class MerchSysStore_Common_Base extends MerchSys_Common_Base {
	public $view_path;
	public $base_path;
	public $empty_message;
	
	public function __construct($view, $title = "") {
		$this->title = $title;
		$this->view = $view;
		$this->base_path = MerchSys_Public::$base_installation_path.'/plugins/'.MerchSysStore_Settings::PLUGIN_NAME.'/public/views';
		$this->common = array(
			'currency' => MerchSys_Public::$currency,
			'add_basket' => __('Add to basket', MerchSysStore_Settings::PLUGIN_NAME),
			'price' => __('Price', MerchSysStore_Settings::PLUGIN_NAME),
			'sizes' => __('Sizes', MerchSysStore_Settings::PLUGIN_NAME),
			'amount' => __('Amount', MerchSysStore_Settings::PLUGIN_NAME),
			'shipping_costs' => __('Shipping costs', MerchSysStore_Settings::PLUGIN_NAME),
			'payment_costs' => __('Payment costs', MerchSysStore_Settings::PLUGIN_NAME),
			'reduce_basket_amount' => __('Reduce', MerchSysStore_Settings::PLUGIN_NAME),
			'add_basket_amount' => __('Add', MerchSysStore_Settings::PLUGIN_NAME),
			'update_basket_amount' => __('Update', MerchSysStore_Settings::PLUGIN_NAME),
			'remove_basket_amount' => __('Remove', MerchSysStore_Settings::PLUGIN_NAME),
			'subtotal' => __('Subtotal', MerchSysStore_Settings::PLUGIN_NAME),
			'total' => __('Total', MerchSysStore_Settings::PLUGIN_NAME),
			'go_checkout' => __('Go to checkout', MerchSysStore_Settings::PLUGIN_NAME),
			'redeem_voucher' => __('Gutschein einlösen', MerchSysStore_Settings::PLUGIN_NAME), // TODO Translate
			'text_required' => __('* this is a mandatory field', MerchSysStore_Settings::PLUGIN_NAME),
		);
	}
}
