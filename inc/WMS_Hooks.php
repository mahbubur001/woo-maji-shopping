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

        // Validate the custom field.
        add_action('woocommerce_review_order_after_shipping', [$this, 'add_delivery_date']);
        add_action('wp_enqueue_scripts', [$this, 'maji_enqueue_scripts']);
    }

    function shipping_method() {
        require_once "WMS_Pickup_Shipping_Method.php";
        require_once "WMS_Delivery_Shipping_Method.php";
    }

    function maji_enqueue_scripts() {
        if (is_checkout()) {
            wp_enqueue_style('maji-shipping', WMS_PLUGIN_URI . '/css/maji-shipping.css');
            wp_enqueue_script('maji-shipping', WMS_PLUGIN_URI . '/js/maji-shipping.js', 'jquery', time(), true);
            wp_localize_script('maji-shipping', 'maji', [
                'public_holidays' => WMS_CONFIG['public_holidays']
            ]);
        }
    }

    function add_shipping_method($methods) {
        $methods['wms_pickup_shipping'] = 'WMS_Pickup_Shipping_Method';
        $methods['wms_delivery_shipping'] = 'WMS_Delivery_Shipping_Method';
        return $methods;
    }

    function package_rates($rates, $package) {
        global $woocommerce;
        return $rates;
    }

    function add_delivery_date() {
        ?>
        <tr class="wms-pickup-date-tr">
            <th><?php esc_html_e('Choice pickup date', 'woo-maji-shopping'); ?></th>
            <td>
                <div class="wms-pickup-date-wrap">
                    <input type="text" class="wms-date wms-pickup-date" name="wms-pickup-date">
                </div>
            </td>
        </tr>
        <tr class="wms-delivery-date-tr">
            <th><?php esc_html_e('Choice delivery date', 'woo-maji-shopping'); ?></th>
            <td>
                <div class="wms-delivery-date-wrap">
                    <input type="text" class="wms-date wms-delivery-date" name="wms-delivery-date">
                </div>
            </td>
        </tr>
        <?php
    }

}

new WMS_Hooks();