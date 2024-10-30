<?php

/*
 Plugin Name: merch.systems
 Version: 1.0.0
 Description: Fully integrates your merch.systems online store into your Wordpress website
 Plugin URI: https://merch.systems
 Author: anti-design.com GmbH & Co. KG
 Author URI: http://anti-design.com
 
 @package merchsys
 @subpackage merchsys/public/includes
 */
 
class MerchSysStore_Common_Page extends MerchSysStore_Common_Base {
	public $response;
	public $template;
	public $action;
	public static $referrer;
	public static $query_vars;
	
	public $header;
	public $content;
	public $footer;
	public $title;
	
	public function __construct($view = 'page') {
		parent::__construct($view);
		if (MerchSys_Public::$client == null) {
			return;
		}
	}
	
	public function page_init() {
		$this->set_referrer();
		$this->set_template();
		$this->set_action();
	}

	public function get_header_view() {
		return MerchSys_Helper::get_view(new MerchSysStore_Common_Header);
	}
	
	public function get_footer_view($obj = null) {
		return MerchSys_Helper::get_view(new MerchSysStore_Common_Footer);
	}
	
	public function get_title($default = "") {
		if ($this->content == null) $this->set_content();
		if (is_object($this->content) && strlen($this->content->title)> 0) {
			$this->title = $this->content->title;
		}
		else {
			$this->title = $default;
		}
		$this->title = __($this->title, MerchSysStore_Settings::PLUGIN_NAME);
		return $this->title;
	}
	
	public function set_content() {
	}
	
	public function get_content_view() {
		if ($this->content == null) $this->set_content();
		return MerchSys_Helper::get_view($this->content);
	}
	
	public function display_page() {
		if (isset($this->response['message_key']) && isset(MerchSys_Settings::$message_keys[$this->response['message_key']])) {
			$this->response = __(MerchSys_Settings::$message_keys[$this->response['message_key']], MerchSys_Settings::PLUGIN_NAME);
		}
		else if (isset($this->response['message_localized'])) {
			$this->response = $this->response['message_localized'];
		}
		else if (isset($this->response['message'])) {
			$this->response = __($this->response['message'], MerchSys_Settings::PLUGIN_NAME);
		}
		else if (isset($_GET[MerchSys_Settings::MESSAGE_FIELD])) {
			$const_name = strtoupper($_GET[MerchSys_Settings::MESSAGE_FIELD]).'_TEXT';
			if (defined('MerchSysStore_Public_Settings::'.$const_name)) {
				$text = constant('MerchSysStore_Public_Settings::'.$const_name);
				$this->response = __($text, MerchSysStore_Settings::PLUGIN_NAME);
				if ($this->response === $text) {
					$this->response = __($text, MerchSys_Settings::PLUGIN_NAME);
				}
			}
		}
		$this->header = $this->get_header_view();
		$this->content = $this->get_content_view();
		$this->footer = $this->get_footer_view();
		return MerchSys_Helper::get_view($this);
	}
	
	public function set_template($template = null, $default = MerchSysStore_Public_Settings::DEFAULT_TEMPLATE) {
		$this->template = strlen($template) > 0 ? $template : (isset(MerchSysStore_Public::$query_vars[MerchSys_Settings::PAGE_FIELD]) ? MerchSysStore_Public::$query_vars[MerchSys_Settings::PAGE_FIELD] : $default);
	}
	
	public function set_action($action = null) {
		$this->action = $action != null ? $action : (isset($_POST[MerchSys_Settings::ACTION_FIELD]) ? $_POST[MerchSys_Settings::ACTION_FIELD] : (isset(MerchSysStore_Public::$query_vars[MerchSys_Settings::ACTION_FIELD]) ? MerchSysStore_Public::$query_vars[MerchSys_Settings::ACTION_FIELD] : null));
	}
	
	public function set_referrer() {
		self::$referrer = isset($_GET[MerchSys_Settings::REFERRER_FIELD]) ? $_GET[MerchSys_Settings::REFERRER_FIELD] : null;
	}
	
	public function do_action($classname = __CLASS__) {
		if ($this->action != null) {
			if (method_exists($classname, $this->action)) {
				$action = $this->action;
				$response = $this->$action();
				if ($response) {
					$this->response = $response;
				}
			}
		}
		return;
	}
}
