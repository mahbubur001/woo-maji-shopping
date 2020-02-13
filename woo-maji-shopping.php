<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Maji Shopping
 * Version:           1.0.0
 * Author:            Restobox
 * Author URI:        www.restobox.com
 * Text Domain:       woo-maji-shopping
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    if (!defined('WMS_PLUGIN_FILE')) {
        define('WMS_PLUGIN_FILE', __FILE__);
    }

    if (!defined('WMS_PLUGIN_URI')) {
        define('WMS_PLUGIN_URI', plugins_url('', __FILE__));
    }

    if (!defined('WMS_CONFIG')) {
        define('WMS_CONFIG', [
            'max_limit' => 120,
            'rate'      => [
                'richmond'        => 9,
                'burnaby'         => 9,
                'vancouver'       => 9,
                'west-vancouver'  => 10,
                'north-vancouver' => 10,
                'surrey'          => 13,
                'delta'           => 13,
                'white-rock'      => 12,
                'coquitlam'       => 14,
                'maple-ridge'     => 16,
                'langley'         => 16,
                'langley-city'    => 16,
            ],
            'public_holidays'=>[ // day/month => 23-09
                '17-09'
            ]
        ]);
    }


    require_once("inc/Init.php");
}