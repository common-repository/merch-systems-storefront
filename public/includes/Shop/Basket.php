<?php
class MerchSys_Shop_Basket extends MerchSys_Shop_Base
{
    public $empty_message;
    public $has_items = false;
    public $items = array();
    public $is_main_page = true;

    public function __construct($items = null, $view = 'basket')
    {
        parent::__construct($view);
        $this->title = __('Basket', MerchSys_Settings::PLUGIN_NAME);
        if ($items == null || empty($items)) {
            $this->empty_message = __('The basket is empty', MerchSysStore_Settings::PLUGIN_NAME);
            $items = false;
        } else {
            $this->has_items = true;
            $this->subtotal = 0;
            foreach ($items as $index => &$item) {
                $css_classes = "";
                $item['css_classes'] = $css_classes;
                $product = MerchSysStore_Public::$shop->get_product($item[MerchSysStore_Public_Settings::PRODUCT_ID_BASKET]);
                $item['link'] = MerchSysStore_Public::$shop_URL . MerchSysStore_Public_Settings::PRODUCT_TEMPLATE . '/' . $product['product_group_id'] . '/' . $item[MerchSysStore_Public_Settings::PRODUCT_ID_BASKET];
                $this->subtotal += $item['subtotal'];
                $this->items[] = $item;
            }

            $this->form = array(
                'action' => MerchSysStore_Public::$shop_URL . MerchSysStore_Public_Settings::BASKET_TEMPLATE,
                'action_field' => MerchSysStore_Settings::ACTION_FIELD,
                'product_amount_change' => MerchSysStore_Public_Settings::PRODUCT_AMOUNT_MODIFY_FIELDNAME_BASKET,
                'action_reduce' => MerchSysStore_Public_Settings::REDUCE_BASKET_AMOUNT_ACTION,
                'action_add' => MerchSysStore_Public_Settings::ADD_BASKET_AMOUNT_ACTION,
                'action_update' => MerchSysStore_Public_Settings::MODIFY_PRODUCT_BASKET_ACTION,
                'action_remove' => MerchSysStore_Public_Settings::DELETE_BASKET_AMOUNT_ACTION,
                'action_redeem' => MerchSysStore_Public_Settings::REDEEM_VOUCHER_ACTION,
                'voucher' => MerchSysStore_Public_Settings::VOUCHER_FIELDNAME_BASKET,
                'product_amount' => MerchSysStore_Public_Settings::PRODUCT_AMOUNT_FIELDNAME_BASKET,
                'product_id' => MerchSysStore_Public_Settings::PRODUCT_ID_BASKET,
                'item_id' => MerchSysStore_Public_Settings::PRODUCT_ITEM_ID_FIELDNAME_BASKET,
                'referrer' => array('field' => MerchSys_Settings::REFERRER_FIELD, 'value' => MerchSysStore_Public_Settings::CHECKOUT_TEMPLATE),
                'context' => array('field' => MerchSys_Settings::CONTEXT_FIELD, 'value' => MerchSysStore_Settings::CONTEXT_REGISTRATION),
                'checkout_action' => MerchSysStore_Public::$shop_URL . MerchSysStore_Settings::CONTEXT_REGISTRATION_PAGE,
            );
        }
    }
}
