<?php
class MerchSysStore_Admin extends MerchSys_Admin
{

    private $plugin_name;
    private $version;

    public function __construct()
    {
        $this->plugin_name = MerchSysStore_Settings::PLUGIN_NAME;
        $this->version = MerchSysStore_Settings::PLUGIN_VERSION;
    }

    public function add_merchsys_admin_page()
    {
        add_submenu_page(
            'merchsys_admin_menu',
            __('Merch Systems Store Admin', $this->plugin_name),
            __('Store Admin', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '_admin_menu',
            array($this, 'merchsys_settings_page')
        );
    }

    public function merchsys_settings_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        $pages = get_pages();
        $menus = get_registered_nav_menus();
        require_once plugin_dir_path(__FILE__) . 'partials/merchsys_settings_page.php';
    }

    public function register_merchsys_settings()
    {
        register_setting('merchsys_cart_group', 'merchsys_addmenu');
        register_setting('merchsys_cart_group', 'merchsys_showcategories');
        register_setting('merchsys_cart_group', 'merchsys_showshopcarousel');
        //register_setting('merchsys_cart_group', 'merchsys_navigationname');
        register_setting('merchsys_cart_group', 'merchsys_showloginmenu');
        register_setting('merchsys_cart_group', 'merchsys_loginmenuwrapper');
        register_setting('merchsys_cart_group', 'merchsys_basketmenuwrapper');
        register_setting('merchsys_cart_group', 'merchsys_maxamount');
        register_setting('merchsys_cart_group', 'merchsys_gobasket');
    }

    public function map_vc_shortcodes()
    {
        if (function_exists('vc_map')) {
            $all_pages = get_pages(array(
                'hierarchical' => 0,
                'post_type' => 'page',
                'post_status' => 'publish',
            ));

            $pages = array();
            $pages['Select'] = '';
            if (!empty($all_pages)) {
                foreach ($all_pages as $page) {
                    $pages[$page->post_title] = str_replace(get_site_url(), '', esc_url(get_permalink($page->ID)));
                }
            }

            vc_map(array(
                'name' => $this->plugin_name . ' Shop Shortcode',
                'base' => MerchSysStore_Settings::SHOP_SHORTCODE,
                'description' => __('Add this shortcode if you want to show a Merchsys shop in this page', $this->plugin_name),
                'group' => $this->plugin_name,
                'holder' => "div",
                'class' => $this->plugin_name,
                "params" => array(
                    array(
                        "type" => "dropdown",
                        "heading" => __("Terms page", $this->plugin_name),
                        "param_name" => "terms_page",
                        "value" => $pages,
                    ),
                    array(
                        "type" => "dropdown",
                        "heading" => __("Privacy page", $this->plugin_name),
                        "param_name" => "privacy_page",
                        "value" => $pages,
                    ),
                    array(
                        "type" => "textfield",
                        "heading" => __("Language locale", $this->plugin_name),
                        "param_name" => "locale",
                        "value" => "",
                    ),
                ),
            ));

        }
    }
}
