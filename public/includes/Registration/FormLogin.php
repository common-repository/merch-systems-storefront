<?php
class MerchSys_Form_Login extends MerchSys_Form
{

    public function __construct($view = 'login', $fields = array())
    {
        parent::__construct($view, $fields);
        $this->view_path = MerchSysStore_Public_Settings::REGISTRATION_VIEWS_FOLDER;
        $this->form = $this->get_form_view();
        if (strlen(MerchSys_Registration_Page::$referrer) > 0) {
            $referrer = '?' . MerchSys_Settings::REFERRER_FIELD . '=' . MerchSys_Registration_Page::$referrer;
        } else {
            $referrer = "";
        }
        $this->register_link = array(
            'text_before' => __('Not registered yet?', MerchSysStore_Settings::PLUGIN_NAME),
            'url' => MerchSysStore_Public::$shop_URL . MerchSysStore_Settings::CONTEXT_REGISTRATION_PAGE . '/' . MerchSysStore_Public_Settings::REGISTER_TEMPLATE . $referrer,
            'text' => __('Click here to register', MerchSysStore_Settings::PLUGIN_NAME),
        );
    }

    public function get_form_view()
    {
        $args = array('echo' => false);
        if ($this->view == MerchSysStore_Public_Settings::LOGIN_TEMPLATE) {
            $redirect_URL = MerchSysStore_Public::$shop_URL;
            if (MerchSys_Registration_Page::$referrer != null) {
                $redirect_URL .= MerchSys_Registration_Page::$referrer . '?' . MerchSys_Settings::REFERRER_FIELD . '=' . MerchSys_Registration_Page::$referrer;
            }
            $args['redirect'] = $redirect_URL;
        }
        return wp_login_form($args);
    }

    public function complete_registration()
    {
        global $reg_errors;
        if (count($reg_errors->get_error_messages()) < 1) {
            $userdata = array();
            foreach ($this->fields as $field) {
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
