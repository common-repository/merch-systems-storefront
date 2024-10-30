<?php
abstract class MerchSys_Form extends MerchSysStore_Common_Base
{

    public $locale;
    public $fields = array();
    public $fields_raw = array();
    public $redirect_URL;
    public $submit_button;

    public function __construct($view = '', $fields = array())
    {
        parent::__construct($view);
        $this->form_action_url = MerchSysStore_Public::$shop_URL;
        $this->locale = MerchSys_Helper::get_locale();
        $this->fields_raw = $fields;
        $this->set_fields($fields);
    }

    public function validate_form()
    {
        global $reg_errors;
        $reg_errors = new WP_Error;
        $errors = '';
        if (isset($_POST) && !empty($_POST) && isset($_POST[MerchSys_Settings::ACTION_FIELD])) {
            foreach ($this->fields_raw as $key => &$field) {
                $valid = $this->validate_field($field);
                $field = array_merge($field, array('valid' => $valid));
            }
            if (is_wp_error($reg_errors)) {
                foreach ($reg_errors->get_error_messages() as $error) {
                    $errors .= '<span class="error">' . $error . '</span>';
                }
            }
        }
        return $errors;
    }

    public function validate_field($field)
    {
        $valid = true;
        if (isset($field['required']) && ($field['required'] === true) && ((isset($_POST[$field['name']]) && strlen($_POST[$field['name']]) < 1) || !isset($_POST[$field['name']]))) {
            $error = MerchSysStore_Public_Settings::FIELD_REQUIRED;
            if (isset($field['error'])) {
                $error = $field['error'];
            }
            $this->add_error($field['label'], $error, '%s', $field['label']);
            $valid = false;
        } else {
            if ($field['name'] == MerchSys_Settings::FIELD_USERNAME) {
                if (username_exists($_POST[$field['name']])) {
                    $this->add_error($field['label'], MerchSysStore_Public_Settings::USERNAME_EXISTS);
                    $valid = false;
                } else if (!validate_username($_POST[$field['name']])) {
                    $this->add_error($field['label'], MerchSysStore_Public_Settings::USERNAME_NOT_VALID);
                    $valid = false;
                }
            }
            if ($field['type'] == 'email') {
                if (!is_email($_POST[$field['name']])) {
                    $this->add_error($field['label'], MerchSysStore_Public_Settings::WRONG_EMAIL);
                    $valid = false;
                } else if (($field['name'] == MerchSys_Settings::FIELD_EMAIL) && (MerchSys_Public::$user == null && email_exists($_POST[$field['name']]))) {
                    $this->add_error($field['label'], MerchSysStore_Public_Settings::EMAIL_EXISTS);
                    $valid = false;
                }
            }
            if (($field['type'] == 'password')
                && ($field['name'] == MerchSys_Settings::FIELD_PASSWORD)
                && (strlen($_POST[$field['name']]) < MerchSysStore_Public_Settings::MIN_PASSWORD_LENGTH)) {
                $this->add_error(
                    $field['name'],
                    MerchSysStore_Public_Settings::PASSWORD_LENGTH,
                    array('%f', '%l'),
                    array($field['label'], MerchSysStore_Public_Settings::MIN_PASSWORD_LENGTH)
                );
                $valid = false;
            }
            if (isset($field['compare']) && ($_POST[$field['name']] != $_POST[$field['compare']])) {
                $this->add_error($field['label'], MerchSysStore_Public_Settings::DOESNT_MATCH, '%s', $field['label']);
                $valid = false;
            }
        }
        return $valid;
    }

    public function add_error($field_label, $error, $find = '%f', $replace = "")
    {
        global $reg_errors;
        $error = sprintf(__($error, MerchSysStore_Settings::PLUGIN_NAME), $replace);
        $reg_errors->add('error_' . $field_label, $error);
    }

    public function set_fields()
    {
        foreach ($this->fields_raw as $field) {
            $type = ($field['type'] == 'email') || ($field['type'] == 'password') ? 'text' : $field['type'];
            $field['value'] = "";
            if (isset($_POST[$field['name']])) {
                $field['value'] = $_POST[$field['name']];
            } else if (isset(MerchSys_Public::$user[$field['name']]) && !empty(MerchSys_Public::$user[$field['name']])) {
                $field['value'] = MerchSys_Public::$user[$field['name']][0];
            } else if (isset($field['compare']) && isset(MerchSys_Public::$user[$field['compare']]) && !empty(MerchSys_Public::$user[$field['compare']])) {
                $field['value'] = MerchSys_Public::$user[$field['compare']][0];
            }
            if (isset($field['options_list_method'])) {
                $field['options'] = $this->call_method($field['options_list_method']);
            }
            $this->fields[] = MerchSys_Helper::get_view(new MerchSys_Registration_Field($type, $field));
        }
        return;
    }

    public function call_method($method_name)
    {
        $result = null;
        if (method_exists($this, $method_name)) {
            $result = $this->$method_name();
        }
        return $result;
    }

    public function get_countries()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        return MerchSys_Public::get_countries();
    }

    public function get_user_country()
    {
        $countries = $this->get_countries();
        if (is_array($countries) && array_key_exists(MerchSys_Public::$user['country'][0], $countries)) {
            return array(MerchSys_Public::$user['country'][0] => $countries[MerchSys_Public::$user['country'][0]]);
        }
        return;
    }

    public function get_payment_methods()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        return MerchSys_Public::get_payment_methods();
    }

    public function set_basket($basket_items)
    {
        $basket = new MerchSys_Shop_Basket($basket_items);
        $basket->is_main_page = false;
        $this->basket = MerchSys_Helper::get_view($basket);
    }
}
