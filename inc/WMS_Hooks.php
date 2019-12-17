<?php
if (!defined('WPINC')) {
    die('Keep Quite');
}

/**
 * Class WMS_Hooks
 */
class WMS_Hooks
{
    public function __construct() {
        add_action('woocommerce_shipping_init', [$this, 'shipping_method']);
        add_filter('woocommerce_shipping_methods', [$this, 'add_shipping_method']);
        add_filter('woocommerce_package_rates', [$this, 'package_rates'], 9, 2);
    }

    function shipping_method() {
        require_once "WMS_Shipping_Method.php";
    }

    function add_shipping_method($methods) {
        $methods['wms_shipping'] = 'WMS_Shipping_Method';
        return $methods;
    }

    function package_rates($rates, $package) {
        global $woocommerce;
        return $rates;
    }

}

new WMS_Hooks();