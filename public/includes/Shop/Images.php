<?php
class MerchSys_Shop_Images extends MerchSys_Shop_Base
{
    public $items = array();

    public function __construct($images_array, $view = 'images')
    {
        parent::__construct($view);
        $this->items = $images_array;
    }

    public function get_items()
    {
        $this->view = $this->view;
        return MerchSys_Helper::get_view($this);
    }
}
