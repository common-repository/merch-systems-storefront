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
 
class MerchSysStore_Common_Footer extends MerchSysStore_Common_Base {
	public function __construct() {
		parent::__construct('footer');
	}
}
