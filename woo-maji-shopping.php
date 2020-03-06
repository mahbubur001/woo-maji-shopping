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
                'west-van'        => 10,
                'coquitlam'       => 15,
                'delta'           => 15,
                'new-westminster' => 15,
                'new-west'        => 15,
                'north-vancouver' => 15,
                'north-van'       => 15,
                'surrey'          => 15,
                'tsawwassen'      => 15,
                'white-rock'      => 15,
                /*'maple-ridge'     => 16,*/
                /*'langley'         => 16,*/
                /*'langley-city'    => 16,*/
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
                "New West",
                "Sparwood",
                "Coquitlam",
                "North Cowichan",
                "Surrey",
                "Cranbrook",
                "North Vancouver",
                "North Van",
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
            ],
            'zone_1'          => [
                "burnaby",
                "richmond",
                "vancouver",
                "north vancouver",
                "north van",
                "west vancouver",
                "west van"
            ],
            'zone_2'          => [
                "coquitlam",
                "delta",
                "new westminster",
                "new west",
                "surrey",
                "tsawwassen",
                "white rock"
            ],
            'bag_id'          => 1530, // bag product id
        ]);
    }


    require_once("inc/Init.php");
}