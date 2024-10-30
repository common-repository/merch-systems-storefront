<?php
class MerchSys_Form_Checkout extends MerchSys_Form
{
    public $view_path = MerchSysStore_Public_Settings::SHOP_VIEWS_FOLDER;

    public function __construct($view = 'checkout', $fields = array())
    {
        if (empty($fields)) {
            $fields = MerchSys_Settings::$registration_fields;
        }
        parent::__construct($view, $fields);
        $this->title = __('Checkout', MerchSysStore_Settings::PLUGIN_NAME);
        $this->view_path = MerchSysStore_Public_Settings::SHOP_VIEWS_FOLDER;
        $this->form_action_url .= MerchSysStore_Public_Settings::PLACE_ORDER_TEMPLATE;
        $this->action_field = array('name' => MerchSys_Settings::ACTION_FIELD, 'value' => MerchSysStore_Public_Settings::CHECKOUT_ACTION);
        $this->submit_button = __('Confirm', MerchSysStore_Settings::PLUGIN_NAME);
    }

    public function complete_checkout()
    {
        foreach ($this->fields_raw as $field) {
            if (!isset($_POST[$field['name']]) || (isset(MerchSys_Public::$user[$field['name']]) && (MerchSys_Public::$user[$field['name']] == $_POST[$field['name']]))) {
                continue;
            }

            update_user_meta(MerchSys_Public::$user_id, $field['name'], sanitize_text_field($_POST[$field['name']]));
        }
        MerchSys_Public::set_user_info();
        if (isset($_POST['user_email']) && $_POST['user_email'] != MerchSys_Public::$user['user_email'][0]) {
            wp_update_user(array('ID' => MerchSys_Public::$user_id, 'user_email' => $_POST['user_email']));
        }
        MerchSys_Public::set_user_info();
        return true;
    }
}
