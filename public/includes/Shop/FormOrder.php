<?php
class MerchSys_Form_Order extends MerchSys_Form
{
    public $view_path = MerchSysStore_Public_Settings::SHOP_VIEWS_FOLDER;

    public function __construct($view = 'place_order', $fields = array())
    {
        if (empty($fields)) {
            $fields = MerchSysStore_Public_Settings::$place_order_fields;
        }
        parent::__construct($view, $fields);
        $this->title = __('Your order', MerchSysStore_Settings::PLUGIN_NAME);
        $this->view_path = MerchSysStore_Public_Settings::SHOP_VIEWS_FOLDER;
        $this->form_action_url = '';
        $this->action_field = array('name' => MerchSys_Settings::ACTION_FIELD, 'value' => MerchSysStore_Public_Settings::PLACE_ORDER_ACTION);
        $this->submit_button = __('Confirm order', MerchSysStore_Settings::PLUGIN_NAME);

        $this->return_success_URL = MerchSysStore_Public::$shop_URL . MerchSysStore_Public_Settings::PLACE_ORDER_SUCCESS_TEMPLATE;
        $this->return_cancel_URL = MerchSysStore_Public::$shop_URL . MerchSysStore_Public_Settings::PLACE_ORDER_FAIL_TEMPLATE;
    }
}
