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
            'max_limit'       => 120,
            'rate'            => [
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
            'cities_of_bc'    => [
                "Burnaby",
                "Lumby",
                "City of Port Moody",
                "Cache Creek",
                "Maple Ridge",
                "Prince George",
                "Castlegar",
                "Merritt",
                "Prince Rupert",
                "Chemainus",
                "Mission",
                "Richmond",
                "Chilliwack",
                "Nanaimo",
                "Saanich",
                "Clearwater",
                "Nelson",
                "Sooke",
                "Colwood",
                "New Westminster",
                "Sparwood",
                "Coquitlam",
                "North Cowichan",
                "Surrey",
                "Cranbrook",
                "North Vancouver",
                "Terrace",
                "Dawson Creek",
                "North Vancouver",
                "Tumbler",
                "Delta",
                "Osoyoos",
                "Vancouver",
                "Fernie",
                "Parksville",
                "Vancouver",
                "Invermere",
                "Peace River",
                "Vernon",
                "Kamloops",
                "Penticton",
                "Victoria",
                "Kaslo",
                "Port Alberni",
                "Whistler",
                "Langley",
                "Port Hardy"
            ],
            'public_holidays' => [ // day-month-year => 23-09-2020
                '17-09-2020'
            ],
            'zone_1'          => [
                "West Vancouver",
                "North Vancouver",
                "Downtown",
                "Surrey",
                "Delta",
                "White Rock",
                "Coquitlam",
                "Maple Ridge",
                "Langley"
            ],
            'zone_2'          => [
                "Richmond",
                "Vancouver",
                "Burnaby",
                "New Westminster",
                "Lougheed"
            ]
        ]);
    }


    require_once("inc/Init.php");
}