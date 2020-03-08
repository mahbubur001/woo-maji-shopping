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
        add_action('woocommerce_checkout_process', [$this, 'validate_checkout_field_process']);
        //Save the order meta with field value
        add_action('woocommerce_checkout_update_order_meta', [$this, 'checkout_field_update_order_meta']);
        //Display field value on the order edit page
        add_action('woocommerce_admin_order_data_after_shipping_address', [$this, 'checkout_field_display_admin_user_order_meta'], 10, 1);
        // Display order meta in order details section
        //add_action('woocommerce_order_details_after_order_table_items', [$this, 'checkout_field_display_admin_user_order_meta'], 10, 1);
        add_action('woocommerce_order_details_after_order_table', [$this, 'checkout_field_display_admin_user_order_meta'], 10, 1);
        //include the custom order meta to woocommerce mail
        add_action("woocommerce_email_after_order_table", [$this, "checkout_field_display_admin_user_order_meta"], 10, 1);

        // Add a special product at checkout page
        //add_action('woocommerce_checkout_before_customer_details', [$this, 'add_special_product_at_cart']);
        // Validate the custom field.
        //add_filter('woocommerce_shipping_fields', [$this, 'shipping_state_fields'], 900, 1);
        //add_filter('woocommerce_billing_fields', [$this, 'billing_state_fields'], 900, 1);
        add_action('woocommerce_review_order_after_shipping', [$this, 'add_delivery_date']);
        add_action('wp_enqueue_scripts', [$this, 'maji_enqueue_scripts']);
    }

    function add_special_product_at_cart() {
        if ($bag_id = WMS_CONFIG['bag_id']) {
            $product_cart_id = WC()->cart->generate_cart_id($bag_id);
            $cart_item_key = WC()->cart->find_product_in_cart($product_cart_id);
            if ($cart_item_key) {
                $a = WC()->cart->get_cart_item($cart_item_key);
                if ($a['quantity'] > 1) {
                    WC()->cart->set_quantity($cart_item_key, 1);
                }
            } else {
                WC()->cart->add_to_cart($bag_id, 1);
            }
        }
    }


    function shipping_state_fields($fields) {
        WC()->customer->set_shipping_country('CA'); // Set shipping country
        WC()->customer->set_shipping_state('BC'); // Set shipping state

        $fields['shipping_state']['type'] = 'select';
        $fields['shipping_state']['options'] = ['BC' => __('British Columbia', 'woocommerce')];
        $fields['shipping_state']['default'] = 'BC';
        $fields['shipping_state']['input_class'] = [];
        $fields['shipping_state']['custom_attributes'] = ['disabled' => 'disabled'];

        return $fields;
    }

    function billing_state_fields($fields) {
        WC()->customer->set_billing_country('CA'); // Set shipping country
        WC()->customer->set_billing_state('BC'); // Set shipping state

        $fields['billing_state']['type'] = 'select';
        $fields['billing_state']['options'] = ['BC' => __('British Columbia', 'woocommerce')];
        $fields['billing_state']['default'] = 'BC';
        $fields['billing_state']['input_class'] = [];
        $fields['billing_state']['custom_attributes'] = ['disabled' => 'disabled'];

        return $fields;
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
                'public_holidays' => WMS_CONFIG['public_holidays'],
                'cities_of_bc'    => WMS_CONFIG['cities_of_bc'],
                'zone_1'          => WMS_CONFIG['zone_1'],
                'zone_2'          => WMS_CONFIG['zone_2']
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

    function validate_checkout_field_process() {
        $chosen_shipping = WC()->session->get('chosen_shipping_methods')[0];
        $chosen_shipping = explode(':', $chosen_shipping);
        $chosen_shipping = $chosen_shipping[0];
        if (!empty($chosen_shipping) && in_array($chosen_shipping, ['wms_pickup_shipping', 'wms_delivery_shipping'])) {

            if ('wms_pickup_shipping' === $chosen_shipping && empty($_POST['wms-pickup-date'])) {
                wc_add_notice(__('<strong>Pickup date</strong> is a required field.', 'woo-maji-shopping'), 'error');
                return;
            }
            if ('wms_delivery_shipping' === $chosen_shipping && empty($_POST['wms-delivery-date'])) {
                wc_add_notice(__('<strong>Delivery date</strong> is a required field.', 'woo-maji-shopping'), 'error');
                return;
            }

            $shipping_type = '';
            $shipping_label = '';
            $shipping_raw_date = '';
            if ('wms_delivery_shipping' === $chosen_shipping && !empty($_POST['wms-delivery-date'])) {
                $shipping_raw_date = sanitize_text_field($_POST['wms-delivery-date']);
                $shipping_label = __("Delivery", "woo-maji-shopping");
                $shipping_type = 'delivery';
            }

            if ('wms_pickup_shipping' === $chosen_shipping && !empty($_POST['wms-pickup-date'])) {
                $shipping_raw_date = sanitize_text_field($_POST['wms-pickup-date']);
                $shipping_label = __("Pickup", "woo-maji-shopping");
                $shipping_type = 'pickup';
            }
            if ($shipping_raw_date) {
                $shipping_date = DateTime::createFromFormat('l - M n, Y', $shipping_raw_date);
                if (!$shipping_date) {
                    wc_add_notice(sprintf(__('<strong>%s</strong> date (%s) format is not validate.', 'woo-maji-shopping'), $shipping_label, $shipping_raw_date), 'error');
                    return;
                }
//                $selectedDate = $shipping_date->format('d-m-Y');
//                $lastValidateDate = $shipping_date->modify('+60 day');

            }
        }
    }

    function checkout_field_update_order_meta($order_id) {
        $chosen_shipping = WC()->session->get('chosen_shipping_methods')[0];
        $chosen_shipping = explode(':', $chosen_shipping);
        $chosen_shipping = $chosen_shipping[0];
        if ('wms_pickup_shipping' === $chosen_shipping && !empty($_POST['wms-pickup-date'])) {
            update_post_meta($order_id, 'wms_pickup_date', sanitize_text_field($_POST['wms-pickup-date']));
        }
        if ('wms_delivery_shipping' === $chosen_shipping && !empty($_POST['wms-delivery-date'])) {
            update_post_meta($order_id, 'wms_delivery_date', sanitize_text_field($_POST['wms-delivery-date']));
        }
    }

    /**
     * @param $order WC_Order
     */
    function checkout_field_display_admin_user_order_meta($order) {
        $shipping = $order->get_items('shipping');
        $shipping_method_id = reset($shipping)->get_method_id();
        $shipping_type = '';
        $shipping_date = '';
        if ('wms_delivery_shipping' === $shipping_method_id) {
            $shipping_type = __('Delivery', "woo-maji-shopping");
            $shipping_date = get_post_meta($order->get_id(), 'wms_delivery_date', true);
        } else if ('wms_pickup_shipping' === $shipping_method_id) {
            $shipping_type = __('Pickup', "woo-maji-shopping");
            $shipping_date = get_post_meta($order->get_id(), 'wms_pickup_date', true);
        }

        if ($shipping_type && $shipping_date) {
            echo sprintf('<p><strong>%s</strong> %s %s %s</p>',
                __("Shipping:", "woo-maji-shopping"),
                $shipping_type,
                __("on", "woo-maji-shopping"),
                $shipping_date);
        }
    }

    function add_delivery_date() {
        ?>
        <tr class="wms-pickup-date-tr">
            <th><?php esc_html_e('Choose pickup date', 'woo-maji-shopping'); ?></th>
            <td>
                <div class="wms-pickup-date-wrap">
                    <input type="text" readonly="readonly" class="wms-date wms-pickup-date" autocomplete="off"
                           name="wms-pickup-date">
                </div>
            </td>
        </tr>
        <tr class="wms-delivery-date-tr">
            <th><?php esc_html_e('Choose delivery date', 'woo-maji-shopping'); ?></th>
            <td>
                <div class="wms-delivery-date-wrap">
                    <input type="text" readonly="readonly" class="wms-date wms-delivery-date" autocomplete="off"
                           name="wms-delivery-date">
                </div>
            </td>
        </tr>
        <?php
    }

}

new WMS_Hooks();
