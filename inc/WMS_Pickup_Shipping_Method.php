<?php
if (!defined('WPINC')) {
    die('Keep Quite');
}

if (!class_exists('WMS_Pickup_Shipping_Method')) {

    class WMS_Pickup_Shipping_Method extends WC_Shipping_Method
    {
        public function __construct($instance_id = 0) {

            $this->id = 'wms_pickup_shipping';
            $this->instance_id = absint($instance_id);
            $this->method_title = __('Free Pickup (From 1:00pm - 6:30pm)', "woo-maji-shopping");
            $this->method_description = __('Pickup, Free Delivery', "woo-maji-shopping");
            $this->supports = array(
                'shipping-zones',
//                'settings', //use this for separate settings page
                'instance-settings',
                'instance-settings-modal',
            );

            $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
            $this->title = __('WMS Pickup Free Shipping', "woo-maji-shopping");
            $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Pickup (Free)', 'woo-maji-shopping');
            $this->init();
        }

        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init() {
            $this->init_form_fields();
            $this->init_settings();

            $this->title      = $this->get_option( 'title' );
            $this->tax_status = $this->get_option( 'tax_status' );
            $this->cost       = $this->get_option( 'cost' );
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        function init_form_fields() {

            $this->form_fields = array(

                'title' => array(
                    'title'       => __('Free Pickup (From 1:00pm - 6:30pm)', 'woo-maji-shopping'),
                    'type'        => 'text',
                    'description' => __('Free Pickup (From 1:00pm - 6:30pm)', 'woo-maji-shopping'),
                    'default'     => __('Free Pickup (From 1:00pm - 6:30pm)', 'woo-maji-shopping')
                ),
                'tax_status' => array(
                    'title'   => __( 'Tax status', 'woocommerce' ),
                    'type'    => 'select',
                    'class'   => 'wc-enhanced-select',
                    'default' => 'taxable',
                    'options' => array(
                        'taxable' => __( 'Taxable', 'woocommerce' ),
                        'none'    => _x( 'None', 'Tax status', 'woocommerce' ),
                    ),
                ),
                'cost'       => array(
                    'title'       => __( 'Cost', 'woocommerce' ),
                    'type'        => 'text',
                    'placeholder' => '0',
                    'description' => __( 'Optional cost for local pickup.', 'woocommerce' ),
                    'default'     => '',
                    'desc_tip'    => true,
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
            $this->add_rate(
                array(
                    'label'   => $this->title,
                    'package' => $package,
                    'cost'    => $this->cost,
                )
            );
        }
    }
}
