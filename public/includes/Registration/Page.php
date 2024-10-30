<?php
class MerchSys_Registration_Page extends MerchSysStore_Common_Page
{
    private $plugin_name;
    private $version;
    private $locale;
    private $user;

    public function __construct($view = 'page')
    {
        parent::__construct($view);
        $this->plugin_name = MerchSysStore_Settings::PLUGIN_NAME;
        $this->version = MerchSysStore_Settings::PLUGIN_VERSION;
        $this->load_dependencies();
        $this->locale = MerchSys_Helper::get_locale();
        $this->page_init();
    }

    /* Client methods */
    public function get_countries()
    {
        if (MerchSys_Public::$client == null) {
            return;
        }

        try {
            return MerchSys_Public::$client->getCountries();
        } catch (Exception $e) {
        }
    }

    public function set_template($template = null, $default = null)
    {
        if (MerchSysStore_Public::$user != null) {
            $default = MerchSysStore_Public_Settings::REGISTER_TEMPLATE;
        } else {
            $default = MerchSysStore_Public_Settings::LOGIN_TEMPLATE;
        }
        parent::set_template($template, $default);
    }

    /* View/render methods */
    public function set_content()
    {
        switch ($this->template) {
            case 'register':
                $this->content = new MerchSys_Form_Register();
                break;
            case 'login':
                $this->content = new MerchSys_Form_Login();
                break;
            default:
                $this->content = new MerchSys_Form_Register();
                break;
        }
    }

    /* Action methods */
    public function val_register()
    {
        $form = new MerchSys_Form_Register();
        $errors = $form->validate_form();
        if ($errors != null) {
            $this->response = $errors;
        } else {
            $success = $form->complete_registration();
            if ($success === true) {
                $this->response = __('Registration complete. Please log-in', MerchSysStore_Settings::PLUGIN_NAME);
                $referrer = isset($_GET[MerchSys_Settings::REFERRER_FIELD]) ? '&' . MerchSys_Settings::REFERRER_FIELD . '=' . $_GET[MerchSys_Settings::REFERRER_FIELD] : '';
                MerchSysStore_Public::$redirect_URL = MerchSysStore_Public::$shop_URL . MerchSysStore_Settings::CONTEXT_REGISTRATION_PAGE . '/' . MerchSysStore_Public_Settings::LOGIN_TEMPLATE . '?' . MerchSys_Settings::MESSAGE_FIELD . '=' . MerchSysStore_Public_Settings::SUCCESS_MESSAGE_REG . $referrer;
            } else {
                $this->response = __('There was an error. Please try again', MerchSysStore_Settings::PLUGIN_NAME);
            }
        }
    }

    public function do_action($classname = __CLASS__)
    {
        parent::do_action($classname);
    }

    private function load_dependencies()
    {
        foreach (glob(dirname(__FILE__) . "/Registration/*") as $class_file) {
            require_once $class_file;
        }
    }
}
