<?php
class MerchSys_Shop_Products extends MerchSys_Shop_Base
{
    public $empty_message;
    public $current_cat = null;
    public $has_items = false;
    public $items = array();

    public function __construct($items = null, $current_cat = null, $view = 'products')
    {
        parent::__construct($view);
        if ($current_cat != null) {
            $this->current_cat = $current_cat;
            $this->title = $current_cat['name'];
            $this->empty_message = __('There are no products for this category', MerchSysStore_Settings::PLUGIN_NAME);
        } else {
            $this->title = __('All products', MerchSysStore_Settings::PLUGIN_NAME);
            $this->empty_message = __('There are no products', MerchSysStore_Settings::PLUGIN_NAME);
        }
        if ($items == null || empty($items)) {
            $items = false;
        } else {
            $this->has_items = true;
            foreach ($items as $index => &$item) {
                $css_classes = "";
                $item['css_classes'] = $css_classes;
                $item['link'] = MerchSys_Shop_Page::get_link(MerchSysStore_Public_Settings::PRODUCT_TEMPLATE, $item, $this->current_cat[MerchSysStore_Public_Settings::CATEGORY_NAME]);
                if (empty($item['images'])) {
                    $item['has_images'] = false;
                } else {
                    $item['has_more_images'] = false;
                    $item['has_images'] = true;
                    if (count($item['images']) > 1) {
                        $item['has_more_images'] = true;
                    }
                }
                $this->items[] = $item;
            }
        }
    }

    public function get_main_image_view()
    {
        if (!empty($this->current_cat['main_image'])) {
            $items = new MerchSys_Shop_Items('main_image', array('image' => $this->current_cat['main_image']));
            return MerchSys_Helper::get_view($items);
        }
    }
}
