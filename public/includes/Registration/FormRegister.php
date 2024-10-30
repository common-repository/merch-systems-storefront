<?php
class MerchSys_Form_Register extends MerchSys_Form
{

    public function __construct($view = 'register', $fields = array())
    {
        if (empty($fields)) {
            $fields = array_merge(MerchSys_Settings::$wp_registration_fields, MerchSys_Settings::$registration_fields);
        }
        parent::__construct($view, $fields);
        $this->view_path = MerchSysStore_Public_Settings::REGISTRATION_VIEWS_FOLDER;
        $this->form_action_url = $_SERVER['REQUEST_URI'];
        $this->action_field = array('name' => MerchSys_Settings::ACTION_FIELD, 'value' => MerchSysStore_Public_Settings::REGISTER_ACTION);
        $this->submit_button = __('Complete registration', MerchSys_Settings::PLUGIN_NAME);
    }

    public function complete_registration()
    {
        global $reg_errors;

        if (count($reg_errors->get_error_messages()) < 1) {
            $userdata = array();
            foreach ($this->fields_raw as $field) {
                if (isset($_POST[$field['name']])) {
                    $userdata[$field['name']] = sanitize_text_field($_POST[$field['name']]);
                }
            }
            $user = wp_insert_user($userdata);
            if ($user) {
                foreach ($userdata as $field => $value) {
                    update_user_meta($user, $field, $value);
                }
                return true;
            }
        }
        return false;
    }
}
