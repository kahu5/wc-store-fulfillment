<?php
/**
 * @package Akismet
 */
/**
 * Plugin Name: WC Store Fulfillment
 * Plugin URI: https://github.com/kahu5/wc-store-fulfillment
 * Description: Wordpress plugin for WooCommerce current inventory orders
 * Author: Jared Meidal
 * Author URI: https://github.com/kahu5/wc-store-fulfillment
 * License: GPLv3
 * Version: 0.0.5
 * Requires at least: 5.5
 * Requires PHP:      7.3
 * WC requires at least: 4.7.0
 * WC tested up to: 5.0.0
*/

require_once __DIR__ . "/classes/fulfillment.Model.php";
require_once __DIR__ . "/classes/fulfillment.View.php";

add_action( 'admin_enqueue_scripts', 'js_enqueue' );
function js_enqueue() {
    wp_enqueue_script('script1', plugin_dir_url(__FILE__) . 'js/scripts.js');
}

plugins_url( 'scripts.js', _FILE_ );

add_action('admin_menu', 'options_page');
function options_page() {
    add_menu_page(
        'WC Store Fulfillment',
        'Fulfillment',
        'manage_options',
        'wcstorefulfillment',
        'options_page_display',
        'dashicons-clipboard',
        24
    );
}

function options_page_display() {
	\fulfillmentview\Products::page_refresh();
    \fulfillmentview\Products::options_page_html();
    $data = \fulfillmentmodel\Products::getPublishedProducts();
	\fulfillmentview\Products::product_table_header();
    \fulfillmentview\Products::displayPublishedProducts($data);
}
