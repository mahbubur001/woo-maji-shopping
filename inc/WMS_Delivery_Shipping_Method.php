<?php
if (!defined('WPINC')) {
    die('Keep Quite');
}

if (!class_exists('WMS_Delivery_Shipping_Method')) {

    class WMS_Delivery_Shipping_Method extends WC_Shipping_Method
    {
        public function __construct($instance_id = 0) {

            $this->id = 'wms_delivery_shipping';
            $this->instance_id = absint($instance_id);
            $this->method_title = __('Delivery', "woo-maji-shopping");
            $this->method_description = __('Local delivery within the lower mainland', "woo-maji-shopping");
            $this->supports = array(
                'shipping-zones',
//                'settings', //use this for separate settings page
                'instance-settings',
                'instance-settings-modal',
            );

            $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
            $this->title = __('WMS Delivery Shipping', "woo-maji-shopping");
            $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Delivery', 'woo-maji-shopping');
            $this->init();
        }

        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init() {
            // Load the settings API
            $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
            $this->init_settings(); // This is part of the settings API. Loads settings you previously init.
            // Save settings in admin if you have any defined
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        function init_form_fields() {

            $this->form_fields = array(

                'title' => array(
                    'title'       => __('Delivery', 'woo-maji-shopping'),
                    'type'        => 'text',
                    'description' => __('Title to be display on site', 'woo-maji-shopping'),
                    'default'     => __('Delivery', 'woo-maji-shopping')
                ),

            );
        }

        /**
         * calculate_shipping function.
         *
         * @access      public
         *
         * @param mixed $package
         *
         * @return void
         */
        public function calculate_shipping($package = array()) {
            $cost = 0; //its free shipping!
            $regularCartValue = 0;
            $country = $package["destination"]["country"]; // CA for canada
            $state = isset($package["destination"]["state"]) ? $package["destination"]["state"] : '';
            $city = isset($package["destination"]["city"]) ? sanitize_title($package["destination"]["city"]) : '';
            $rate = array(
                'id'       => $this->id,
                'label'    => $this->title,
                'cost'     => $cost,
                'package'  => $package,
                'calc_tax' => 'per_order'
            );

            //count item value
            foreach ($package['contents'] as $key => $value) {
                if ($value['data']->get_price() === $value['data']->get_regular_price()) {
                    $regularCartValue += $value['data']->get_price() * $value['quantity'];
                }
            }

            if (($country === 'CA' || $country === 'Canada') && $state == 'BC' && $city && in_array($city, array_keys(WMS_CONFIG['rate']))) {
                if ($regularCartValue > WMS_CONFIG['max_limit']) {
                    $rate['label'] = $this->title . ($cost == 0 ? " " . __('(free)', 'woocommerce') : '');
                    $rate['cost'] = 0;
                } else {
                    $rate['cost'] = WMS_CONFIG['rate'][$city];
                }
            } else {
                $rate = []; // Return empty array for not located in region
            }

            // Register the rate
            $this->add_rate($rate);
        }
    }
}
