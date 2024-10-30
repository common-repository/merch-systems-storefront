<?php
class MerchSys_Shop_Product extends MerchSys_Shop_Base
{
    public $item = null;
    public $has_images = false;
    public $has_more_images = false;
    public $show_form = false;
    public $template;
    public $amount;
    public $navigation;
    public $prev_product = array();
    public $next_product = array();

    public function __construct($product = null, $view = 'product')
    {
        parent::__construct($view);
        if ($product != null) {
            if (in_array($product['state_id'], array(1, 5))) { // Make constants for product states
                $this->show_form = true;
            }
            $product['main_image'] = array();
            if (!empty($product['images'])) {
                foreach ($product['images'] as $index => &$image) {
                    $image['index'] = $index;
                    if ($index == 0) {
                        $product['main_image'][] = $image;
                        continue;
                    }
                }
            }
            $product['images'] = $product['images'];
            if ($product['images'] != null) {
                $this->has_images = true;
            }

            $product['more_images'] = $product['images'];
            array_shift($product['more_images']);
            if (count($product['images']) > 1) {
                $this->has_more_images = true;
            }
            foreach ($product as $key => &$value) {
                $hasKey = 'has'.$key;
                if (empty($value)) {
                    $value = false;
                    $this->$hasKey = false;
                } else {
                    $this->$hasKey = true;
                }
            }
            $this->title = $product['name'];
            $this->has_tracks = false;
            if (isset($product['tracks']) && is_array($product['tracks']) && count($product['tracks']) > 0) {
                $this->has_tracks = false;
            }
            $this->amount = array();
            $max_amount = (($ma = get_option('merchsys_maxamount')) != null && $ma >= 1) ? intval($ma) : 10;
            for ($i = 1; $i <= $max_amount; $i++) {
                $this->amount[] = array('index' => $i, 'selected' => ($i == 1 ? 'selected' : ''));
            }
        } else {
            $product = false;
            $this->empty_message = __("The product doesn't exist", MerchSysStore_Settings::PLUGIN_NAME);
        }
        $this->item[] = $product;
        $this->id = $product['id'];
        $this->category_id = isset(MerchSysStore_Public::$query_vars[MerchSysStore_Public_Settings::CATEGORY_ID_FIELDNAME]) ? MerchSysStore_Public::$query_vars[MerchSysStore_Public_Settings::CATEGORY_ID_FIELDNAME] : null;
        $this->form = array(
            'action_field' => MerchSys_Settings::ACTION_FIELD,
            'add_basket' => MerchSysStore_Public_Settings::ADD_TO_BASKET_ACTION,
            'product_id_field' => MerchSysStore_Public_Settings::PRODUCT_ID_FORM,
            'size_id_field' => MerchSysStore_Public_Settings::SIZE_ID_FORM,
        );
    }

    public function get_main_image_view()
    {
        if (!empty($this->item)) {
            $images = isset($this->item[0]['images'][0]) ? $this->item[0]['images'][0] : null;
            $items_obj = new MerchSys_Shop_Items('main_image', $images);
            return MerchSys_Helper::get_view($items_obj);
        }
        return;
    }

    public function get_products($cat_id)
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        try {
            return MerchSys_Public::$client->getProducts($cat_id);
        } catch (Exception $e) {
        }
    }

    public function set_product_navigation($categories)
    {
        $products = $this->get_products($this->category_id);
        $prev = null;
        $next = null;
        if (!empty($products)) {
            $current_product_index = $this->get_index_by_id($this->id, $products);
            $prev = isset($products[$current_product_index - 1]) ? $products[$current_product_index - 1] : null;
            $next = isset($products[$current_product_index + 1]) ? $products[$current_product_index + 1] : null;
        }
        if ($next == null || $prev == null) {
            $current_cat_index = null;
            $cats = array();
            foreach ($categories as $key => $cat) {
                $cats[] = $cat['id'];
                if ($cat['id'] == $this->category_id) {
                    $current_cat_index = $key;
                }
            }
            if ($next === null) {
                $next = $this->get_next_cat_product($current_cat_index, $cats);
            }
            if ($prev === null) {
                $prev = $this->get_prev_cat_product($current_cat_index, $cats);
            }
        }
        if ($prev != null) {
            $prev['link'] = MerchSys_Shop_Page::get_link(MerchSysStore_Public_Settings::PRODUCT_TEMPLATE, $prev);
            if (!empty($prev['images'])) {
                $prev['main_image'] = $prev['images'][0];
            }
        }
        if ($next != null) {
            $next['link'] = MerchSys_Shop_Page::get_link(MerchSysStore_Public_Settings::PRODUCT_TEMPLATE, $next);
            if (!empty($next['images'])) {
                $next['main_image'] = $next['images'][0];
            }
        }
        $this->main_link[] = array('link' => MerchSysStore_Public::$shop_URL, 'image' => MerchSys_Utilities::merchsys_get_back_to_shop_navigation_image(), 'text' => __('Back to shop homepage', MerchSys_Helper::$plugin_name));
        $this->prev_product = $prev == null ? false : $prev;
        $this->next_product = $next == null ? false : $next;
        return;
    }

    public function get_next_cat_product($current_cat_index, $cats)
    {
        if ($current_cat_index === null) {
            return null;
        }

        $index = intval($current_cat_index) + 1;
        if (isset($cats[$index])) {
            $products = $this->get_products($cats[$index]);
            if (!empty($products)) {
                return $products[0];
            } else {
                return $this->get_next_cat_product($index, $cats);
            }
        }
        return null;
    }

    public function get_prev_cat_product($current_cat_index, $cats)
    {
        if ($current_cat_index === null) {
            return null;
        }

        $index = intval($current_cat_index) - 1;
        if (isset($cats[$index])) {
            $products = $this->get_products($cats[$index]);
            if (!empty($products)) {
                return end($products);
            } else {
                return $this->get_prev_cat_product($cats[$index], $cats);
            }
        }
        return null;
    }

    public function get_index_by_id($id, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['id'] === $id) {
                return intval($key);
            }
        }
        return null;
    }
}
