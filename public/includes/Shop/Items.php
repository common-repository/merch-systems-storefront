<?php
class MerchSys_Shop_Items extends MerchSys_Shop_Base
{
    public $items;

    public function __construct($view, $items = null)
    {
        parent::__construct($view);
        if ($items != null && count($items) == 1) {
            $items = array($items);
        }

        $this->items = $items;
        $this->has_items = false;
        if (is_array($this->items) && count($this->items) > 0) {
            $this->has_items = true;
        }
    }
}
