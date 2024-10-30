<?php
require_once plugin_dir_path(dirname(__FILE__)) . '../merch-systems/includes/Merchsys_Settings.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/MerchsysStore_Settings.php';
require_once plugin_dir_path(dirname(__FILE__)) . '../merch-systems/includes/Merchsys_I18n.php';
require_once plugin_dir_path(dirname(__FILE__)) . '../merch-systems/includes/Merchsys_Loader.php';

class MerchSys_Store
{

    protected $plugin_name;
    protected $version;

    public static $loader;

    public static $shop_pages = array();

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct()
    {
        $this->plugin_name = MerchSysStore_Settings::PLUGIN_NAME;
        $this->version = MerchSysStore_Settings::PLUGIN_VERSION;

        self::$loader = new MerchSys_Loader();
        $this->define_core_hooks();
    }

    private function define_core_hooks()
    {
        self::$loader->add_action('merchsys_init', $this, 'merchsysstore_init');
        $this->set_locale();
    }

    public function merchsysstore_init()
    {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->run();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - MerchSys_Loader. Orchestrates the hooks of the plugin.
     * - MerchSys_i18n. Defines internationalization functionality.
     * - MerchSys_Admin. Defines all hooks for the admin area.
     * - MerchSys_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/MerchsysStore_Admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/MerchsysStore_Public.php';

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the MerchSys_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new MerchSys_i18n($this->plugin_name);
        self::$loader->add_action('setup_theme', $this, 'set_shop_pages');
        self::$loader->add_filter('locale', $this, 'set_shop_locale');
        self::$loader->add_action('after_setup_theme', $plugin_i18n, 'load_plugin_textdomain');
        self::$loader->add_filter('load_textdomain_mofile', $plugin_i18n, 'load_plugin_custom_textdomain', 10, 2);
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new MerchSysStore_Admin();
        self::$loader->add_action('admin_menu', $plugin_admin, 'add_merchsys_admin_page');
        self::$loader->add_action('admin_init', $plugin_admin, 'register_merchsys_settings');
        self::$loader->add_action('vc_before_init', $plugin_admin, 'map_vc_shortcodes');
        self::$loader->add_filter('plugin_action_links', $plugin_admin, 'link_to_settings_page', 10, 5);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new MerchSysStore_Public();

        self::$loader->add_shortcode(MerchSysStore_Settings::SHOP_SHORTCODE, $plugin_public, 'shortcode_merchsys_shop');

        self::$loader->add_action('wp_enqueue_scripts', $plugin_public, 'init_page');
        self::$loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        self::$loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        self::$loader->add_action('admin_notices', $plugin_public, 'show_error');
        self::$loader->add_action('after_setup_theme', $plugin_public, 'set_pages_permalinks');
        self::$loader->add_action('init', $plugin_public, 'set_query_vars');
        self::$loader->add_action('init', $plugin_public, 'merchsys_public_init');
        self::$loader->add_action('template_redirect', $plugin_public, 'check_redirect');
        self::$loader->add_action('wp_login_failed', $plugin_public, 'redirect_on_login_fail');
        self::$loader->add_action('init', $plugin_public, 'merchsysstore_rewrite_vars', 10, 0);
        self::$loader->add_action('init', $plugin_public, 'merchsysstore_rewrite_rule', 10, 0);

        self::$loader->add_filter('authenticate', $plugin_public, 'check_authenticate', 30, 3);
        self::$loader->add_filter('nav_menu_css_class', $plugin_public, 'set_current_menu_item', 10, 2);
        self::$loader->add_filter('nav_menu_css_class', $plugin_public, 'add_page_id_to_menu', 10, 2);

        if ($plugin_public->add_menu === true) {
            self::$loader->add_action('wp_ajax_get_categories_menu', $plugin_public, 'get_categories_menu');
            self::$loader->add_action('wp_ajax_nopriv_get_categories_menu', $plugin_public, 'get_categories_menu');
        }
        self::$loader->add_filter('body_class', $plugin_public, 'filter_body_class');
    }

    public function set_shop_locale($locale)
    {
        $request_uri = explode('?', $_SERVER["REQUEST_URI"]);
        $request_uri = trim($request_uri[0], '/') . '/';
        foreach (self::$shop_pages as $page) {
            $uri = $page['uri'] . "/";
            if (($page['locale'] !== null) && substr($request_uri, 0, strlen($uri)) == $uri) {
                $locale = $page['locale'];
            }
        }
        return $locale;
    }

    public function set_shop_pages()
    {
        $pages = get_pages();
        self::$shop_pages = array();
        foreach ($pages as $page) {
            if (has_shortcode($page->post_content, 'merchsys_shop')) {
                preg_match_all('/' . get_shortcode_regex() . '/s', $page->post_content, $matches);
                if (isset($matches[2])) {
                    foreach ((array) $matches[2] as $key => $value) {
                        if ($key == 'merchsys_shop') {
                            $atts = shortcode_parse_atts($matches[3][$key]);
                            self::$shop_pages[] = array(
                                'id' => $page->ID,
                                'name' => $page->post_name,
                                'url' => esc_url(get_permalink($page->ID)),
                                'locale' => (isset($atts['locale']) ? $atts['locale'] : null),
                                'terms_page' => isset($atts['terms_page']) ? $atts['terms_page'] : null,
                                'privacy_page' => isset($atts['privacy_page']) ? $atts['privacy_page'] : null,
                                'uri' => get_page_uri($page->ID),
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        self::$loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    MerchSys_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return self::$loader;
    }

}
