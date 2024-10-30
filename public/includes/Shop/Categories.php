<?php
class MerchSys_Shop_Categories extends MerchSys_Shop_Base
{
    public $items = array();
    public $has_items = false;
    public $has = array();
    public $empty_message = '';
    public $current_cat;
    public $current_cat_id;

    public function __construct($view = 'categories')
    {
        parent::__construct($view);
    }

    public function set_items($items = array())
    {
        $this->set_current_cat_id();
        if (!empty($items)) {
            $this->has_items = true;
            foreach ($items as $index => &$item) {
                $css_classes = "";
                if (($item[MerchSysStore_Public_Settings::CATEGORY_MENU_ACTIVE] === true) || ($this->current_cat_id === intval($item[MerchSysStore_Public_Settings::CATEGORY_ID]))) {
                    $css_classes = MerchSysStore_Public_Settings::CATEGORY_MENU_ACTIVE;
                }
                if ($this->current_cat_id === intval($item[MerchSysStore_Public_Settings::CATEGORY_ID])) {
                    $this->current_cat = $item;
                }
                $item['css_classes'] = $css_classes;
                $item['link'] = MerchSys_Shop_Page::get_link(MerchSysStore_Public_Settings::PRODUCTS_TEMPLATE, $item);
                if (empty($item['menu_image'])) {
                    $item['has_menu_image'] = false;
                } else {
                    $item['has_menu_image'] = true;
                }
                $this->items[] = $item;
            }
        } else {
            $this->empty_message = __('There are no categories', MerchSysStore_Settings::PLUGIN_NAME);
        }
    }

    public function get_items_view($view = null, $view_path = null)
    {
        return MerchSys_Helper::get_view($this, $view, $view_path);
    }

    private function set_current_cat_id()
    {
        $this->current_cat_id = isset(MerchSysStore_Public::$query_vars[MerchSysStore_Public_Settings::CATEGORY_ID_FIELDNAME]) ? intval(MerchSysStore_Public::$query_vars[MerchSysStore_Public_Settings::CATEGORY_ID_FIELDNAME]) : null;
    }

    private function get_main_images($max_index = -1)
    {
        $images = array();
        $i = 0;
        foreach ($this->items as $key => $category) {
            if (!empty($category['main_image'])) {
                $images[] = $category['main_image'];
                $i++;
            }
            if ($i === $max_index) {
                break;
            }

        }
        return $images;
    }

    public function get_main_image_view()
    {
        $max_index = 0;
        if (MerchSys_Shop_Page::$show_shop_carousel === true) {
            $max_index = -1;
        }
        $items = new MerchSys_Shop_Items('carousel', $this->get_main_images($max_index));
        return MerchSys_Helper::get_view($items);
    }

}
