<?php
class MerchSysStore_Public extends MerchSys_Public
{

    private $plugin_name;
    private $version;
    private $shop_page_ID;
    private $shop_page_name;
    private $privacy_page_ID;
    private $terms_page_ID;

    public static $page;
    public static $shop;
    public static $registration;
    public static $custom_theme_path;
    public static $custom_theme_url_path;
    public static $query_vars;

    public static $site_URL;
    public static $shop_URL;
    public static $privacy_URL;
    public static $terms_URL;
    public static $redirect_URL;
    public $add_menu;
    public $error = '';
    public $action_response;
    public $title;

    static $user_id;
    static $user;

    private $context_type; //eg shop, registration, ecc

    public function __construct()
    {
        self::$custom_theme_path = MerchSys_Public::$custom_theme_path;
        self::$custom_theme_url_path = plugin_dir_url(__FILE__) . '../../../' . MerchSys_Settings::CUSTOM_FOLDER;
        $this->plugin_name = MerchSysStore_Settings::PLUGIN_NAME;
        $this->version = MerchSysStore_Settings::PLUGIN_VERSION;
        $this->load_dependencies();
        self::$site_URL = get_site_url();
        $this->add_menu = intval(get_option('merchsys_addmenu')) == 1 ? true : false;
        if (isset(self::$query_vars) && !empty(self::$query_vars) && isset(self::$query_vars[MerchSys_Settings::REDIRECT_FIELD])) {
            self::$redirect_URL = self::$query_vars[MerchSys_Settings::REDIRECT_FIELD];
        }
    }

    public function init_page()
    {
        $this->set_query_vars();
        $this->set_context();
        $this->set_page();
    }

    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name . '-fancybox-css', plugin_dir_url(__FILE__) . 'css/jquery.fancybox.min.css', array(), $this->version, 'all');
        if (file_exists(self::$custom_theme_path . '/css/' . $this->plugin_name . '.css')) {
            wp_enqueue_style($this->plugin_name . '-css-custom', self::$custom_theme_url_path . '/css/' . $this->plugin_name . '.css', array(), $this->version, 'all');
        }
        wp_enqueue_style('wp-mediaelement');
    }

    public function enqueue_scripts()
    {
        $ajax_vars = array();
        if (MerchSys_Public::$client !== null) {
            wp_enqueue_script($this->plugin_name . '-ajax', plugin_dir_url(__FILE__) . 'js/' . $this->plugin_name . '-ajax.js', array('jquery'), $this->version);
            $wrapper = get_option('merchsys_basketmenuwrapper');
            if (strlen($wrapper) == 0) {
                $wrapper = strlen($w = get_option('merchsys_basketmenuwrapper')) > 0 ? $w : MerchSysStore_Settings::DEFAULT_BASKET_ITEM_WRAPPER;
            }
            $current_class = (isset(self::$query_vars[MerchSysStore_Settings::PAGE_FIELD]) && self::$query_vars[MerchSysStore_Settings::PAGE_FIELD] == MerchSysStore_Public_Settings::BASKET_TEMPLATE) ? 'current-menu-item' : '';
            $basket_item = "";
            $basket = self::$shop->get_basket();
            $current_class .= empty($basket) ? ' empty' : '';
            $amount = 0;
            foreach ($basket as $product) {
                $amount += intval($product[MerchSysStore_Public_Settings::PRODUCT_AMOUNT_BASKET]);
            }
            $basket_item .= str_replace(array('%l', '%t', '%c', '%a'), array(self::$shop_URL . MerchSysStore_Public_Settings::BASKET_TEMPLATE, __('Basket', $this->plugin_name), 'item-basket ' . $current_class, $amount), $wrapper);
            $login_item = "";
            if (intval(get_option('merchsys_showloginmenu')) == 1) {
                $wrapper = strlen($w = get_option('merchsys_loginmenuwrapper')) > 0 ? $w : MerchSysStore_Settings::DEFAULT_MENU_ITEM_WRAPPER;
                $current_class = (isset(self::$query_vars[MerchSys_Settings::CONTEXT_FIELD]) && self::$query_vars[MerchSys_Settings::CONTEXT_FIELD] == MerchSysStore_Settings::CONTEXT_REGISTRATION) ? 'current-menu-item' : '';
                if (is_user_logged_in()) {
                    $redirect = self::$shop_URL;
                    $current_class .= ' logout-item ';
                    $login_item .= str_replace(array('%l', '%t', '%c', '%a'), array(wp_logout_url($redirect), __('Logout', $this->plugin_name), 'item-logout ' . $current_class, ''), $wrapper);
                } else {
                    $current_class .= ' login-item ';
                    $login_item .= str_replace(array('%l', '%t', '%c', '%a'), array(self::$shop_URL . MerchSysStore_Settings::CONTEXT_REGISTRATION_PAGE . '/' . MerchSysStore_Public_Settings::LOGIN_TEMPLATE, __('Login', $this->plugin_name), 'item-login ' . $current_class, ''), $wrapper);
                }
            }
            $shop_pages_ids = array();
            if (isset(MerchSys_Store::$shop_pages) && !empty(MerchSys_Store::$shop_pages)) {
                foreach (MerchSys_Store::$shop_pages as $page) {
                    if ($page['id'] == $this->shop_page_ID) {
                        continue;
                    }

                    $shop_pages_ids[] = $page['id'];
                }
            }
            $ajax_vars = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'is_shop_page' => $this->is_shop_current() === true ? 1 : 0,
                'shop_page_id' => $this->shop_page_ID,
                'other_shop_pages_ids' => json_encode($shop_pages_ids),
                'add_menu' => ($this->add_menu === true ? 1 : 0),
                'basket_item' => $basket_item,
                'login_item' => $login_item,
                'main_image' => self::$shop->get_main_image_view(),
                'categories_menu' => $this->get_categories_menu(),
            );
            wp_enqueue_script($this->plugin_name . '-ajax', plugin_dir_url(__FILE__) . '/js/' . $this->plugin_name . '-ajax.js', array('jquery'), $this->version);
            wp_localize_script($this->plugin_name . '-ajax', 'merchsys_store_obj', $ajax_vars);
        }
        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/' . $this->plugin_name . '.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name);
        if (file_exists(self::$custom_theme_path . '/js/' . $this->plugin_name . '-ajax.js')) {
            wp_enqueue_script($this->plugin_name . '-ajax-custom', self::$custom_theme_url_path . '/js/' . $this->plugin_name . '-ajax.js', array('jquery'), $this->version);
            wp_localize_script($this->plugin_name . '-ajax-custom', 'merchsys_store_obj', $ajax_vars);
        }
        if (file_exists(self::$custom_theme_path . '/js/' . $this->plugin_name . '.js')) {
            wp_register_script($this->plugin_name . '-custom', self::$custom_theme_url_path . '/js/' . $this->plugin_name . '.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name . '-custom');
        }
        if ($this->is_shop_current() === true) {
            wp_register_script($this->plugin_name . '-fancybox', plugin_dir_url(__FILE__) . 'js/jquery.fancybox.min.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name . '-fancybox');
            wp_enqueue_script('wp-mediaelement');
        }
    }

    private function set_context()
    {
        if (isset(self::$query_vars[MerchSysStore_Settings::CONTEXT_FIELD])) {
            $this->context_type = self::$query_vars[MerchSysStore_Settings::CONTEXT_FIELD];
        } else {
            $this->context_type = MerchSysStore_Settings::CONTEXT_SHOP;
        }
    }

    private function load_dependencies()
    {
        require_once dirname(__FILE__) . '/includes/Settings.php';
        foreach (glob(dirname(__FILE__) . "/includes/Common/*") as $class_file) {
            require_once $class_file;
        }
        foreach (glob(dirname(__FILE__) . "/includes/Shop/*") as $class_file) {
            require_once $class_file;
        }
        foreach (glob(dirname(__FILE__) . "/includes/Registration/*") as $class_file) {
            require_once $class_file;
        }
    }

    public function merchsys_public_init()
    {
        if (MerchSys_Public::$client === false) {
            $this->error .= MerchSys_Public::wrap_error(__('Please make sure that a valid Storekey and Passphrase are defined in your Merchsys settings page', $this->plugin_name));
            MerchSys_Public::$client = null;
        } else if (MerchSys_Public::$client === null) {
            $this->error .= MerchSys_Public::wrap_error(__('Please add a valid SOAP Url in your Merchsys settings page', $this->plugin_name));
        } else {
            MerchSys_Public::$client->setLocale(MerchSys_Helper::get_locale());
            self::$shop = new MerchSys_Shop_Page(MerchSys_Helper::get_locale());
            self::$shop->do_action();
            if (self::$shop->response != null) {
                $this->action_response = self::$shop->response;
            }
            MerchSys_Store::$loader->add_filter('the_title', $this, 'filter_title', 10, 2);
            MerchSys_Store::$loader->add_filter('the_content', $this, 'filter_content');
        }
        /* Workaround for hooks */
        $registration = new MerchSys_Registration_Page;
        $registration->do_action();
        if ($registration->response != null) {
            $this->action_response = $registration->response;
        }
        $registration = null;unset($registration);
        if (self::$redirect_URL != null) {
            wp_redirect(self::$redirect_URL);
            exit();
        }

        MerchSys_Store::$loader->add_action('template_redirect', $this, 'redirect_rules');
        MerchSys_Store::$loader->run();
    }

    public function set_page()
    {
        switch ($this->context_type) {
            case MerchSysStore_Settings::CONTEXT_SHOP:
                self::$shop = self::$page = new MerchSys_Shop_Page();
                break;
            case MerchSysStore_Settings::CONTEXT_REGISTRATION:
                self::$page = new MerchSys_Registration_Page();
                break;
            default:
                self::$shop = self::$page = new MerchSys_Shop_Page();
                break;
        }
        self::$page->response = $this->action_response;
    }

    public function check_redirect()
    {
        if (MerchSys_Public::$user != null && isset(self::$query_vars[MerchSysStore_Settings::REDIRECT_FIELD]) && is_page($this->shop_page_ID)) {
            wp_redirect(self::$query_vars[MerchSysStore_Settings::REDIRECT_FIELD]);
            exit;
        }
    }

    private function is_shop_current()
    {
        global $post;
        if ($post != null && has_shortcode($post->post_content, 'merchsys_shop')) {
            return true;
        } else {
            return false;
        }

    }

    public function get_categories_menu()
    {
        return self::$shop->get_main_menu();
    }

    /* Wp Hooks */

    public function show_error()
    {
        if (strlen($this->error) > 0) {
            echo $this->error;
        }
    }

    public function set_pages_permalinks()
    {
        foreach (MerchSys_Store::$shop_pages as $page) {
            if ($page['locale'] == get_locale()) {
                $this->shop_page_ID = $page['id'];
                $this->shop_page_name = $page['name'];
                self::$shop_URL = $page['url'];
                self::$terms_URL = $page['terms_page'];
                self::$privacy_URL = $page['privacy_page'];
                break;
            } else if ($page['locale'] == null) {
                $default_id = $page['id'];
                $default_name = $page['name'];
                $default_shop_URL = $page['url'];
                $default_terms_URL = $page['terms_page'];
                $default_privacy_URL = $page['privacy_page'];
            }
        }

        if ($this->shop_page_ID == null && isset($default_id)) {
            $this->shop_page_ID = $default_id;
            $this->shop_page_name = $default_name;
            self::$shop_URL = $default_shop_URL;
            self::$terms_URL = $default_terms_URL;
            self::$privacy_URL = $default_privacy_URL;
        }
    }

    public function set_query_vars()
    {
        global $wp_query;
        self::$query_vars = $wp_query->query_vars;
        do_action('query_vars_set');
    }

    public function shortcode_merchsys_shop($atts)
    {
        $atts = array_change_key_case(shortcode_atts(
            array(
                'privacy_page' => self::$privacy_URL,
                'terms_page' => self::$terms_URL,
                'locale' => get_locale(),
            ),
            $atts
        ));

        global $post;
        if (self::$page != null) {
            self::$page->get_title($post->post_title);
            print self::$page->display_page();
        }
    }

    public function redirect_rules()
    {
        if (is_page($this->shop_page_ID) && isset($_GET[MerchSys_Settings::REFERRER_FIELD]) && $_GET[MerchSys_Settings::REFERRER_FIELD] == MerchSysStore_Public_Settings::CHECKOUT_TEMPLATE) {
            if (is_user_logged_in()) {
                $page_URL = self::$shop_URL . '/' . MerchSysStore_Public_Settings::CHECKOUT_TEMPLATE;
                wp_redirect($page_URL);
                exit();
            }
        }
    }

    public function merchsysstore_rewrite_vars()
    {
        add_rewrite_tag('%' . MerchSys_Settings::PAGE_FIELD . '%', '([^&]+)');
        add_rewrite_tag('%' . MerchSys_Settings::CONTEXT_FIELD . '%', '([^&]+)');
        add_rewrite_tag('%' . MerchSys_Settings::REFERRER_FIELD . '%', '([^&]+)');
        add_rewrite_tag('%' . MerchSys_Settings::REDIRECT_FIELD . '%', '([^&]+)');
        add_rewrite_tag('%' . MerchSys_Settings::MESSAGE_FIELD . '%', '([^&]+)');
        add_rewrite_tag('%' . MerchSysStore_Public_Settings::PRODUCT_NAME_FIELDNAME . '%', '([^&]+)');
        add_rewrite_tag('%' . MerchSysStore_Public_Settings::PRODUCT_ID_FIELDNAME . '%', '([^&]+)');
        add_rewrite_tag('%' . MerchSysStore_Public_Settings::CATEGORY_NAME_FIELDNAME . '%', '([^&]+)');
        add_rewrite_tag('%' . MerchSysStore_Public_Settings::CATEGORY_ID_FIELDNAME . '%', '([^&]+)');
    }

    public function merchsysstore_rewrite_rule()
    {
        foreach (MerchSys_Store::$shop_pages as $shop_page) {
            /* Registration */
            add_rewrite_rule('^' . $shop_page['name'] . '/' . MerchSysStore_Settings::CONTEXT_REGISTRATION_PAGE . '/([^/]+)/?', 'index.php?page_id=' . $shop_page['id'] . '&' . MerchSys_Settings::CONTEXT_FIELD . '=' . MerchSysStore_Settings::CONTEXT_REGISTRATION . '&' . MerchSys_Settings::PAGE_FIELD . '=$matches[1]', 'top'); // base pages
            add_rewrite_rule('^' . $shop_page['name'] . '/' . MerchSysStore_Settings::CONTEXT_REGISTRATION_PAGE . '/?', 'index.php?page_id=' . $shop_page['id'] . '&' . MerchSys_Settings::CONTEXT_FIELD . '=' . MerchSysStore_Settings::CONTEXT_REGISTRATION, 'top'); // base pages

            /* Shop */
            add_rewrite_rule('^' . $shop_page['name'] . '/([^/]+)/([^/]+)/([^/]+)/([^/]+)/?', 'index.php?page_id=' . $shop_page['id'] . '&' . MerchSys_Settings::PAGE_FIELD . '=$matches[1]&' . MerchSysStore_Public_Settings::CATEGORY_ID_FIELDNAME . '=$matches[2]&' . MerchSysStore_Public_Settings::CATEGORY_NAME_FIELDNAME . '=$matches[3]&' . MerchSysStore_Public_Settings::PRODUCT_ID_FIELDNAME . '=$matches[4]&' . MerchSysStore_Public_Settings::PRODUCT_NAME_FIELDNAME . '=$matches[5]', 'top');
            add_rewrite_rule('^' . $shop_page['name'] . '/([^/]+)/([^/]+)/([^/]+)/?', 'index.php?page_id=' . $shop_page['id'] . '&' . MerchSys_Settings::PAGE_FIELD . '=$matches[1]&' . MerchSysStore_Public_Settings::CATEGORY_ID_FIELDNAME . '=$matches[2]&' . MerchSysStore_Public_Settings::CATEGORY_NAME_FIELDNAME . '=$matches[3]', 'top'); // products
            add_rewrite_rule('^' . $shop_page['name'] . '/([^/]+)/?', 'index.php?page_id=' . $shop_page['id'] . '&' . MerchSys_Settings::PAGE_FIELD . '=$matches[1]', 'top'); // base pages
        }
    }

    public function filter_content($content)
    {
        return $content;
    }

    public function filter_body_class($classes)
    {
        if ($this->is_shop_current()) {
            $classes[] = 'merchsys-shop-page';
        }
        return $classes;
    }

    public function filter_title($title, $id = null)
    {
        global $post;
        if ($this->is_shop_current() && in_the_loop() && $id === $post->ID) {
            $title = strlen($t = self::$shop->get_title()) > 0 ? __($t, $this->plugin_name) : $title;
        }
        return $title;
    }

    public function add_page_id_to_menu($classes, $item)
    {
        $classes[] = sprintf('item-id-%d', $item->object_id);
        return $classes;
    }

    public function set_current_menu_item($classes, $item)
    {
        if (intval($item->object_id) == $this->shop_page_ID) {
            if ((isset(self::$query_vars[MerchSysStore_Settings::CONTEXT_FIELD]) && self::$query_vars[MerchSysStore_Settings::CONTEXT_FIELD] == MerchSysStore_Settings::CONTEXT_REGISTRATION)) {
                $classes = array_diff($classes, array('current-menu-item', 'current_page_item'));
            }
            if ((isset(self::$query_vars[MerchSysStore_Settings::PAGE_FIELD]) && self::$query_vars[MerchSysStore_Settings::PAGE_FIELD] == MerchSysStore_Public_Settings::BASKET_TEMPLATE)) {
                $classes = array_diff($classes, array('current-menu-item', 'current_page_item'));
            }
        }
        return $classes;
    }

    public function redirect_on_login_fail($username)
    {
        if (!empty($_SERVER['HTTP_REFERER']) && !strstr($_SERVER['HTTP_REFERER'], 'wp-login') && !strstr($_SERVER['HTTP_REFERER'], 'wp-admin')) {
            $referrer = "";
            if ($val = strstr($_SERVER['HTTP_REFERER'], MerchSys_Settings::REFERRER_FIELD)) {
                $value = explode('=', $val);
                $value = isset($value[1]) ? $value[1] : null;
                $referrer = $value != null ? '&' . MerchSys_Settings::REFERRER_FIELD . '=' . $value : '';
            }
            wp_redirect(self::$shop_URL . MerchSysStore_Settings::CONTEXT_REGISTRATION_PAGE . '/' . MerchSysStore_Public_Settings::LOGIN_TEMPLATE . '?' . MerchSys_Settings::MESSAGE_FIELD . '=' . MerchSysStore_Public_Settings::FAILED_LOGIN_ACTION . $referrer);
            exit;
        }
    }

    public function check_authenticate($user, $username, $password)
    {
        if (is_a($user, 'WP_User')) {
            return $user;
        }
        if (empty($username) || empty($password)) {
            do_action('wp_login_failed', $username);
        }
    }
}
