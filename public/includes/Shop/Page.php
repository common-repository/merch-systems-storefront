<?php
class MerchSys_Shop_Page extends MerchSysStore_Common_Page
{
    public $content;

    private $plugin_name;
    private $version;
    private $locale;
    private $user;

    public $categories;
    public $defaultcontent;
    public static $show_shop_carousel;
    public static $show_categories;

    public function __construct($view = 'page')
    {
        parent::__construct($view);
        $this->plugin_name = MerchSysStore_Settings::PLUGIN_NAME;
        $this->version = MerchSysStore_Settings::PLUGIN_VERSION;
        $this->load_dependencies();
        self::$show_shop_carousel = intval(get_option('merchsys_showshopcarousel')) == 1 ? true : false;
        self::$show_categories = intval(get_option('merchsys_showcategories')) == 1 ? true : false;
        $this->locale = MerchSys_Helper::get_locale();
        $this->currency = MerchSys_Public::$currency;
        $this->set_categories();
        $this->set_default_content();
        $this->page_init();
    }

    /* Client methods */
    public function get_static_menu()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        try {
            return MerchSys_Public::$client->getStaticMenu();
        } catch (Exception $e) {

        }
    }

    public function set_default_content()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        $this->set_categories();
        if (self::$show_categories && $this->categories->has_items !== false) {
            $this->defaultcontent = $this->categories;
        } else {

            try {
                $category = MerchSys_Public::$client->getCategory();
                if (!empty($category)) {
                    $catgoryDescription = MerchSys_Public::$client->getCategoryDescription($category['id']);
                    if (!empty($catgoryDescription)) {
                        unset($catgoryDescription['id']);
                        $category = array_merge($category, $catgoryDescription);
                    }
                    $categoryMainImage = MerchSys_Public::$client->getMainImage($category['id']);
                    if (!empty($categoryMainImage)) {
                        $category['main_image'] = $categoryMainImage;
                    }
                    $this->categories->current_cat_id = $category['id'];
                    $this->categories->current_cat = $category;
                }
            } catch (Exception $e) {

            }
            $this->defaultcontent = new MerchSys_Shop_Products($this->get_products(), $this->categories->current_cat);
            $this->set_template(MerchSysStore_Public_Settings::PRODUCTS_TEMPLATE);
        }
        return;
    }

    public function set_categories()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        try {
            $categories = MerchSys_Public::$client->getTree();
            if (!empty($categories)) {
                foreach ($categories as &$category) {
                    $category['main_image'] = $this->get_category_image($category[MerchSysStore_Public_Settings::PRODUCT_ID_FIELDNAME]);
                    $category['menu_image'] = $this->get_menu_image_category($category[MerchSysStore_Public_Settings::PRODUCT_ID_FIELDNAME]);
                }
            }
            $this->categories = new MerchSys_Shop_Categories('categories');
            $this->categories->set_items($categories);
            return;
        } catch (Exception $e) {

        }
    }

    public function get_products()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        try {
            $products = MerchSys_Public::$client->getProducts($this->categories->current_cat_id);
            return $products;
        } catch (Exception $e) {

        }
    }

    public function get_product($product_id = null)
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        $product_id = isset(MerchSysStore_Public::$query_vars[MerchSysStore_Public_Settings::PRODUCT_ID_FIELDNAME]) ? MerchSysStore_Public::$query_vars[MerchSysStore_Public_Settings::PRODUCT_ID_FIELDNAME] : $product_id;
        if ($product_id == null || MerchSys_Public::$client == null) {
            return null;
        }

        try {
            $product = MerchSys_Public::$client->getProduct(intval($product_id));
            if ($product != null) {
                if (strlen($product['extended_description']) > 0) {
                    $product['extended_description'] = MerchSys_Utilities::merchsys_filter_product_text($product['extended_description']);
                }
            } else {
                $product = null;
            }

            return $product;
        } catch (Exception $e) {

        }
    }

    public static function get_category($cat_id)
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        try {
            $cats = MerchSys_Public::$client->getTree();
            if (isset($cats[$cat_id])) {
                return $cats[$cat_id];
            } else {
                return null;
            }

        } catch (Exception $e) {

        }
    }

    public function get_category_image($cat_id)
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        try {
            return MerchSys_Public::$client->getMainImage($cat_id);
        } catch (Exception $e) {

        }
    }

    public function get_menu_image_category($id)
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        try {
            return MerchSys_Public::$client->getMenuImage($id);
        } catch (Exception $e) {

        }
    }

    public function get_basket()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        try {
            return MerchSys_Public::$client->getBasketItems();
        } catch (Exception $e) {

        }
    }

    private function get_orders()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        if ($this->user == null) {
            return;
        }

        try {
            return MerchSys_Public::$client->getOrders(MerchSysStore_Public_Settings::GET_ORDER_AMOUNT_DAYS);
        } catch (Exception $e) {

        }
    }

    public function add_basket()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        if (empty($_POST) && ($this->action != MerchSysStore_Public_Settings::ADD_TO_BASKET_ACTION)) {
            return;
        }

        try {
            $response = MerchSys_Public::$client->insertBasketItem($_POST[MerchSysStore_Public_Settings::PRODUCT_ID_FORM], $_POST[MerchSysStore_Public_Settings::SIZE_ID_FORM], $_POST['amount']);
            if (intval(get_option('merchsys_gobasket')) === 1) {
                MerchSysStore_Public::$redirect_URL = MerchSysStore_Public::$shop_URL . MerchSysStore_Public_Settings::BASKET_TEMPLATE;
            }
            return $response;
        } catch (Exception $e) {

        }
    }

    public function reduce_basket_amount()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        if (empty($_POST) && ($this->action != MerchSysStore_Public_Settings::MODIFY_PRODUCT_BASKET_ACTION)) {
            return;
        }

        try {
            $response = MerchSys_Public::$client->reduceBasketamount($_POST[MerchSysStore_Public_Settings::PRODUCT_ITEM_ID_FIELDNAME_BASKET], -intval($_POST[MerchSysStore_Public_Settings::PRODUCT_AMOUNT_MODIFY_FIELDNAME_BASKET]));
            return $response;
        } catch (Exception $e) {

        }
    }

    public function add_basket_amount()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        if (empty($_POST) && ($this->action != MerchSysStore_Public_Settings::MODIFY_PRODUCT_BASKET_ACTION)) {
            return;
        }

        try {
            $response = MerchSys_Public::$client->addBasketAmount($_POST[MerchSysStore_Public_Settings::PRODUCT_ITEM_ID_FIELDNAME_BASKET], intval($_POST[MerchSysStore_Public_Settings::PRODUCT_AMOUNT_MODIFY_FIELDNAME_BASKET]));
            return $response;
        } catch (Exception $e) {

        }
    }

    public function modify_basket_amount()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        if (empty($_POST) && ($this->action != MerchSysStore_Public_Settings::MODIFY_PRODUCT_BASKET_ACTION)) {
            return;
        }

        try {
            $amount = intval($_POST[MerchSysStore_Public_Settings::PRODUCT_AMOUNT_MODIFY_FIELDNAME_BASKET]) - intval($_POST[MerchSysStore_Public_Settings::PRODUCT_AMOUNT_FIELDNAME_BASKET]);
            $_POST[MerchSysStore_Public_Settings::PRODUCT_AMOUNT_MODIFY_FIELDNAME_BASKET] = $amount;
            if ($amount < 0) {
                $_POST[MerchSysStore_Public_Settings::PRODUCT_AMOUNT_MODIFY_FIELDNAME_BASKET] = -$amount;
                $response = $this->reduce_basket_amount();
            } else if ($amount > 0) {
                $response = $this->add_basket_amount();
            } else {
                $response = $this->delete_basket_amount();
            }
            return $response;
        } catch (Exception $e) {

        }
    }

    public function guest_order()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        if (MerchSys_Public::$user != null) {
            try {
                return MerchSys_Public::$client->guestOrder(
                    MerchSys_Public::$user['first_name'][0],
                    MerchSys_Public::$user['last_name'][0],
                    MerchSys_Public::$user['user_email'][0],
                    MerchSys_Public::$user['email_confirm'][0],
                    MerchSys_Public::$user['country'][0],
                    MerchSys_Public::$user['zip'][0],
                    MerchSys_Public::$user['city'][0],
                    MerchSys_Public::$user['street'][0],
                    MerchSys_Public::$user['number'][0]
                );
            } catch (Exception $e) {

            }
        }
        return;
    }

    public function delete_basket_amount()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        try {
            return MerchSys_Public::$client->deleteBasketamount($_POST[MerchSysStore_Public_Settings::PRODUCT_ITEM_ID_FIELDNAME_BASKET], -intval($_POST[MerchSysStore_Public_Settings::PRODUCT_AMOUNT_FIELDNAME_BASKET]));
        } catch (Exception $e) {

        }
    }

    public function redeem_voucher()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        if (empty($_POST) && ($this->action != MerchSysStore_Public_Settings::MODIFY_PRODUCT_BASKET_ACTION)) {
            return;
        }

        try {
            return MerchSys_Public::$client->redeemVoucher($_POST[MerchSysStore_Public_Settings::VOUCHER_FIELDNAME_BASKET]);
        } catch (Exception $e) {

        }
    }

    public function place_basket_order($success_URL, $fail_URL)
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        if (!isset($_POST[MerchSysStore_Public_Settings::PAYMENT_ID_FIELDNAME_BASKET]) || !isset($_POST[MerchSysStore_Public_Settings::TERMS_FIELDNAME_BASKET]) || !isset($_POST[MerchSysStore_Public_Settings::PRIVACY_FIELDNAME_BASKET]) || empty($success_URL) || empty($fail_URL)) {
            return;
        }

        try {
            MerchSys_Public::$client->__setCookie('MerchSysBasket', 1);
            $response = MerchSys_Public::$client->placeOrder($_POST[MerchSysStore_Public_Settings::PAYMENT_ID_FIELDNAME_BASKET], intval($_POST[MerchSysStore_Public_Settings::TERMS_FIELDNAME_BASKET]), intval($_POST[MerchSysStore_Public_Settings::PRIVACY_FIELDNAME_BASKET]), $success_URL, $fail_URL,
                $_POST['company'],
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['country'],
                $_POST['zip'],
                $_POST['city'],
                $_POST['street'],
                $_POST['number']
            );
            return $response;
        } catch (Exception $e) {

        }
        return;
    }

    /* View/render methods */

    public function get_main_menu()
    {
        return $this->categories->get_items_view('main_menu', '');
    }

    public function set_content()
    {
        parent::set_content();
        switch ($this->template) {
            case 'categories':
                $this->content = $this->defaultcontent;
                break;
            case 'products':
                $this->content = new MerchSys_Shop_Products($this->get_products(), $this->categories->current_cat);
                break;
            case 'product':
                $product = new MerchSys_Shop_Product($this->get_product());
                $product->set_product_navigation($this->categories->items);
                $this->content = $product;
                break;
            case 'basket':
                $this->content = new MerchSys_Shop_Basket($this->get_basket());
                break;
            case 'checkout':
                $basket = $this->get_basket();
                if (empty($basket)) {
                    $this->content = new MerchSys_Shop_Basket();
                } else {
                    $this->content = new MerchSys_Form_Checkout();
                    $this->content->set_basket($this->get_basket());
                }
                break;
            case 'place_order';
                $basket = $this->get_basket();
                if (empty($basket)) {
                    $this->content = new MerchSys_Shop_Basket();
                } else {
                    $this->content = new MerchSys_Form_Order();
                    $this->content->set_basket($this->get_basket());
                }
                break;
            default:
                $this->content = $this->defaultcontent;
                break;
        }
    }

    public function get_main_image_view()
    {
        if ($this->content == null) {
            $this->set_content();
        }

        if (is_object($this->content) && method_exists($this->content, 'get_main_image_view')) {
            return $this->content->get_main_image_view();
        }
    }

    public function val_checkout()
    {
        $form = new MerchSys_Form_Checkout();
        $errors = $form->validate_form();
        if ($errors != null) {
            $this->response = $errors;
        } else {
            $success = $form->complete_checkout();
            if ($success === true) {
                $register_guest_response = $this->guest_order();
                if (isset($register_guest_response['state'])) {
                    if ($register_guest_response['state'] != 2) {
                        $this->response = $register_guest_response['message'];
                    }
                }
            } else {
                $this->set_template(MerchSysStore_Public_Settings::CHECKOUT_TEMPLATE);
            }
        }
    }

    public function val_place_order()
    {
        $form = new MerchSys_Form_Order();
        $errors = $form->validate_form();
        if ($errors != null) {
            $this->response = $errors;
        } else {
            $response = $this->place_basket_order($form->return_success_URL, $form->return_cancel_URL);
            if (isset($response['state'])) {
                $this->response = $response['message'];
                if ($response['state'] == 2) {
                    if (isset($response['forward']) && !empty($response['forward'])) {
                        MerchSysStore_Public::$redirect_URL = $response['forward'];
                    } else {
                        MerchSysStore_Public::$redirect_URL = MerchSysStore_Public::$shop_URL . MerchSysStore_Public_Settings::PLACE_ORDER_SUCCESS_TEMPLATE . '?' . MerchSys_Settings::MESSAGE_FIELD . '=' . MerchSysStore_Public_Settings::SUCCESS_MESSAGE_ORD;
                    }
                } else {
                    MerchSysStore_Public::$redirect_URL = MerchSysStore_Public::$shop_URL . MerchSysStore_Public_Settings::CHECKOUT_TEMPLATE . '?' . MerchSys_Settings::MESSAGE_FIELD . '=' . MerchSysStore_Public_Settings::FAILED_MESSAGE_ORD;
                }
            }
        }
        return;
    }

    public function do_action($classname = __CLASS__)
    {
        parent::do_action($classname);
    }

    private function load_dependencies()
    {
        foreach (glob(dirname(__FILE__) . "/Shop/*") as $class_file) {
            require_once $class_file;
        }
    }

    public static function get_link($type = null, $item = null, $cat_name = "")
    {
        $link = MerchSysStore_Public::$shop_URL;
        switch ($type) {
            case MerchSysStore_Public_Settings::PRODUCT_TEMPLATE:{
                    if ($cat_name == null) {
                        $cat = self::get_category($item['product_group_id']);
                        $cat_name = $cat != null ? $cat[MerchSysStore_Public_Settings::CATEGORY_NAME] : 'cat';
                    }
                    $link .= MerchSysStore_Public_Settings::PRODUCT_TEMPLATE . '/' . $item['product_group_id'] . '/' . sanitize_title_with_dashes($cat_name) . '/' . $item[MerchSysStore_Public_Settings::PRODUCT_ID] . '/' . sanitize_title_with_dashes($item[MerchSysStore_Public_Settings::PRODUCT_NAME]);
                    break;
                }
            case MerchSysStore_Public_Settings::PRODUCTS_TEMPLATE:{
                    $link .= MerchSysStore_Public_Settings::PRODUCTS_TEMPLATE . '/' . $item[MerchSysStore_Public_Settings::CATEGORY_ID] . '/' . sanitize_title_with_dashes($item[MerchSysStore_Public_Settings::CATEGORY_NAME]);
                }
            default:
                break;
        }
        return $link;
    }
}
