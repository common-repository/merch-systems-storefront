<?php
/*
Plugin Name: merch.systems Storefront
Version: 1.0.10
Description: Fully integrates your merch.systems online store into your Wordpress website
Plugin URI: https://merch.systems
Author: anti-design.com GmbH & Co. KG
Author URI: https://anti-design.com
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!defined('ABSPATH')) {
    die;
}

require_once plugin_dir_path(__FILE__) . 'includes/WPS_Extend_Plugin.php';

/**
 * The code that runs during plugin activation.
 */
function activate_merchsys_store()
{
    require_once plugin_dir_path(__FILE__) . 'includes/MerchsysStore_Activator.php';
    MerchSysStore_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_merchsys_store()
{
    require_once plugin_dir_path(__FILE__) . 'includes/MerchsysStore_Deactivator.php';
    MerchSysStore_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_merchsys_store');
register_deactivation_hook(__FILE__, 'deactivate_merchsys_store');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/Merchsys_Store.php';

/**
 * Begins execution of the plugin.
 *
 */

// Extend MerchSys
$response = new WPS_Extend_Plugin('merch-systems/merchsys.php', __FILE__, '1.0.4', 'merchsys_store');
function run_merchsys_store()
{
    $plugin = new MerchSys_Store();
    $plugin->run();
}
run_merchsys_store();
