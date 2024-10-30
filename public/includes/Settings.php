<?php
class MerchSysStore_Public_Settings extends MerchSys_Public_Settings
{

    /* Shop */
    const SHOP_VIEWS_FOLDER = 'shop';
    const PRODUCT_TEMPLATE = 'product';
    const CATEGORIES_TEMPLATE = 'categories';
    const PRODUCTS_TEMPLATE = 'products';
    const CATEGORY_TEMPLATE = self::CATEGORIES_TEMPLATE;
    const BASKET_TEMPLATE = 'basket';
    const CHECKOUT_TEMPLATE = 'checkout';
    const DEFAULT_TEMPLATE = self::CATEGORIES_TEMPLATE;

    /* Strings definitions */
    const DEFAULT_TITLE = 'Categories';
    const BASKET_TITLE = 'Basket';

    /* URL parameters definitions */
    const PRODUCT_NAME_FIELDNAME = 'name';
    const PRODUCT_ID_FIELDNAME = 'id';
    const CATEGORY_NAME_FIELDNAME = 'cat_name';
    const CATEGORY_ID_FIELDNAME = 'cat_id';
    const BASKET_ID_FIELD = 'basket_id';

    /* Fields definitions as they come from SOAP or from shop class / Type Strings unless specified */
    const PRODUCT_ID = 'id';
    const PRODUCT_NAME = 'name';
    const PRODUCT_CATEGORY = 'product_group_id';
    const PRODUCT_IMAGES = 'images'; // Array() of Arrays with structure array('thumb' => '', 'image' => '')
    const PRODUCT_IMAGE_THUMB = 'thumb';
    const PRODUCT_IMAGE_BIG = 'image';
    const PRODUCT_IMAGE_BASKET = 'img';
    const PRODUCT_IMAGE_OVER_BASKET = 'imgover';
    const PRODUCT_SUBTOTAL_BASKET = 'imgover';
    const PRODUCT_DESCRIPTION = 'description';
    const PRODUCT_EXTENDEND_DESCRIPTION = 'extended_description';
    const PRODUCT_PRICE = 'price';
    const CATEGORY_NAME = 'name';
    const CATEGORY_MENU_NAME = 'menu_name';
    const CATEGORY_MENU_ACTIVE = 'active';
    const CATEGORY_LABEL = 'label';
    const CATEGORY_ID = 'id';
    const URL_LINK = 'link';
    const CATEGORY_MENU_IMAGE = 'menu_image'; // from shop class
    const CATEGORY_MAIN_IMAGE = 'main_image'; // from shop class
    const PRODUCT_ID_FORM = 'product_id';
    const SIZE_ID_FORM = 'size_id';
    const PRODUCT_ID_BASKET = 'product_id';
    const PRODUCT_ITEM_ID_FIELDNAME_BASKET = 'item_id';
    const BASKET_ITEM_ID = 'id';
    const PRODUCT_AMOUNT_BASKET = 'amount';
    const PRODUCT_AMOUNT_FIELDNAME_BASKET = 'product_amount';
    const PRODUCT_AMOUNT_MODIFY_FIELDNAME_BASKET = 'product_amount_change';
    const VOUCHER_FIELDNAME_BASKET = 'voucher';

    /* Form fields*/

    const ADD_TO_BASKET_ACTION = 'add_basket';
    const MODIFY_PRODUCT_BASKET_ACTION = 'modify_basket_amount';
    const ADD_BASKET_AMOUNT_ACTION = 'add_basket_amount';
    const REDUCE_BASKET_AMOUNT_ACTION = 'reduce_basket_amount';
    const DELETE_BASKET_AMOUNT_ACTION = 'delete_basket_amount';
    const REDEEM_VOUCHER_ACTION = 'redeem_voucher';

    const GET_ORDER_AMOUNT_DAYS = 180;

    /* Form */
    const FORM_VIEWS_FOLDER = 'form_fields';

    /* Registration */

    const REGISTER_TEMPLATE = 'register';
    const REGISTRATION_VIEWS_FOLDER = "registration";
    const PLACE_ORDER_TEMPLATE = 'place_order';
    const LOGIN_TEMPLATE = 'login';
    const THANKYOU_REGISTER_TEMPLATE = self::LOGIN_TEMPLATE;
    const FAILED_REGISTER_TEMPLATE = self::REGISTER_TEMPLATE;
    const FAILED_CHECKOUT_TEMPLATE = self::CHECKOUT_TEMPLATE;
    const FAILED_ORDER_TEMPLATE = self::PLACE_ORDER_TEMPLATE;
    const PLACE_ORDER_SUCCESS_TEMPLATE = 'place_order-success';
    const PLACE_ORDER_FAIL_TEMPLATE = 'place_order-fail';
    const DEFAULT_REGISTER_TEMPLATE = self::LOGIN_TEMPLATE;

    const REGISTER_ACTION = 'val_register';
    const PLACE_ORDER_ACTION = 'val_place_order';
    const CHECKOUT_ACTION = 'val_checkout';
    const SUCCESS_ACTION = 'success';
    const FAILED_ACTION = 'failed';
    const LOGIN_ACTION = 'login';
    const FAILED_LOGIN_ACTION = 'login_failed';
    const DEFAULT_ACTION = self::REGISTER_ACTION;

    const SUCCESS_MESSAGE_REG = 'reg_success';
    const REG_SUCCESS_TEXT = 'Registration complete. Please log-in'; // Taken dynamically from the message $_GET field
    const FAILED_MESSAGE_REG = 'reg_failed';
    const REG_FAILED_TEXT = 'Registration failed. Please try again.'; // Taken dynamically from the message $_GET field
    const SUCCESS_MESSAGE_ORD = 'ord_success';
    const ORD_SUCCESS_TEXT = 'Order successful.'; // Taken dynamically from the message $_GET field
    const FAILED_MESSAGE_ORD = 'ord_failed';
    const ORD_FAILED_TEXT = 'An error occurred.'; // Taken dynamically from the message $_GET field

    const FORM_FIELDS_PREFIX = 'form-field-';
    const REGISTRATION_FORM_NAME = 'storefront_register';

    const PAYMENT_ID_FIELDNAME_BASKET = 'payment_shipping_id';
    const TERMS_FIELDNAME_BASKET = 'terms_accepted';
    const PRIVACY_FIELDNAME_BASKET = 'privacy_accepted';
    const MIN_PASSWORD_LENGTH = 5;

    /* Error messages */
    const FIELD_REQUIRED = 'The field %s is required';
    const WRONG_EMAIL = 'The email is not valid';
    const USERNAME_EXISTS = 'Sorry, that username already exists!';
    const USERNAME_NOT_VALID = 'Sorry, the username you entered is not valid';
    const DOESNT_MATCH = 'The fields for %s don\'t match';
    const PASSWORD_LENGTH = 'Password length must be greater than %l';
    const EMAIL_EXISTS = 'This Email is already in use';
    const LOGIN_FAILED = 'The login failed. Check your credentials or try again';

    public static $place_order_fields = array(
        array('name' => 'payment_shipping_id',
            'type' => 'radiogroup_payments',
            'required' => true,
            'label' => 'Payment methods',
            'error' => 'Please select a payment method',
            'options_list_method' => 'get_payment_methods'),
        array('name' => 'address',
            'type' => 'label',
            'title' => 'Delivery address'),
        array('name' => 'company',
            'type' => 'text',
            'required' => false,
            'label' => 'Company',
            'placeholder' => 'Company'),
        array('name' => 'first_name',
            'type' => 'text',
            'required' => true,
            'label' => 'First name',
            'placeholder' => 'First name'),
        array('name' => 'last_name',
            'type' => 'text',
            'required' => true,
            'label' => 'Last name',
            'placeholder' => 'Last name'),
        array('name' => 'street',
            'type' => 'text',
            'required' => true,
            'label' => 'Street',
            'placeholder' => 'Street'),
        array('name' => 'number',
            'type' => 'text',
            'required' => true,
            'label' => 'Number',
            'placeholder' => 'Number'),
        array('name' => 'zip',
            'type' => 'text',
            'required' => true,
            'label' => 'Postcode',
            'placeholder' => 'Postcode'),
        array('name' => 'city',
            'type' => 'text',
            'required' => true,
            'label' => 'City',
            'placeholder' => 'City'),
        array('name' => 'country',
            'type' => 'select',
            'required' => true,
            'label' => 'Country',
            'options_list' => array(''),
            'options_list_method' => 'get_user_country'),
        array('name' => 'terms_accepted',
            'type' => 'checkbox',
            'required' => true,
            'link' => 'terms',
            'label' => 'AGB read and accepted'),
        /*array('name' => 'privacy_accepted',
        'type' => 'checkbox',
        'required' => true,
        'link' => 'privacy',
        'label' => 'Privacy read and accepted'),*/
        array('name' => 'privacy_accepted',
            'type' => 'hidden',
            'value' => 'true',
        ),
    );
}

// For translation

__('Registration complete. Please log-in');
__('Registration failed. Please try again.');
__('Order successful.');
__('An error occurred.');
__('The field %f is required');
__('The email is not valid');
__('Sorry, that username already exists!');
__('Sorry, the username you entered is not valid');
__("The fields for %f don't match");
__('Password length must be greater than %l');
__('This Email is already in use');
__('The login failed. Check your credentials or try again');
__('Username');
__('Password');
__('Confirm password');
__('Email');
__('Confirm Email');
__('Your personal details');
__('Company');
__('First name');
__('Last name');
__('Phone');
__('Your address');
__('Street');
__('Number');
__('Postcode');
__('City');
__('Country');
__('Payment methods');
__('Please select a payment method');
__('The delivery address is different from the billing one');
__('AGB read and accepted');
__('Privacy read and accepted');
