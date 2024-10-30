<?php
class MerchSys_Registration_Field extends MerchSysStore_Common_Base
{
    public $items;

    public function __construct($view, $item = null)
    {
        parent::__construct($view);
        $this->view_path = MerchSysStore_Public_Settings::FORM_VIEWS_FOLDER;
        $item['valid'] = !isset($item['valid']) ? '' : ($item['valid'] === false ? 'not-valid' : 'valid');
        if (isset($item['title'])) {
            $item['title'] = __($item['title'], MerchSysStore_Settings::PLUGIN_NAME);
        }

        if (isset($item['placeholder'])) {
            $item['placeholder'] = __($item['placeholder'], MerchSysStore_Settings::PLUGIN_NAME);
        }

        if (isset($item['label'])) {
            $item['label'] = __($item['label'], MerchSysStore_Settings::PLUGIN_NAME);
        }

        if (isset($item['link'])) {
            $link_var_name = $item['link'] . '_URL';
            if (isset(MerchSysStore_Public::$$link_var_name)) {
                $item['link'] = MerchSysStore_Public::$$link_var_name;
            } else {
                $item['link'] = '';
            }

        } else {
            $item['link'] = false;
        }
        if ($item['type'] == 'radiogroup_payments' && isset($item['options'])) {
            $item['has_options'] = count($item['options']) > 0 ? true : false;
            $options = array();
            foreach ($item['options'] as $index => &$option) {
                $option['paymentcosts'] = round($option['paymentcosts'], 2);
                $option['shippingcosts'] = round($option['shippingcosts'], 2);
                $option['total'] = round($option['total'], 2);
                $new_option = $option;
                $new_option['checked'] = $option['shippingvendor'] == $item['value'] ? 'checked="checked"' : '';
                $new_option['id'] = $index;
                $options[] = $new_option;
            }
            $item['options'] = $options;
        } else if ($item['type'] == 'select') {
            $options = array();
            foreach ($item['options'] as $key => $option) {
                $selected = $key == $item['value'] ? 'selected="selected"' : '';
                $options[] = array('value' => $key, 'text' => $option, 'selected' => $selected);
            }
            $item['options'] = $options;
        }

        $this->items = array($item);
        $this->has_items = false;
        if (is_array($this->items) && count($this->items) > 0) {
            $this->has_items = true;
        }
    }
}
