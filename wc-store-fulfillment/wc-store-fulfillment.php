<?php
/**
 * @package Akismet
 */
/*
Plugin Name: WC Store Fulfillment
Plugin URI: https://github.com/kahu5/wc-store-fulfillment
Description: Wordpress plugin for WooCommerce current inventory orders
Version: 0.0.1
Author: Jared Meidal
Author URI: https://github.com/kahu5/wc-store-fulfillment
License: GPLv3
*/

plugins_url( 'scripts.js', _FILE_ );

add_action( 'admin_menu', 'wporg_options_page' );
function wporg_options_page() {
    add_menu_page(
        'WC Store Fulfillment',
        'Fulfillment',
        'manage_options',
        'wcstorefulfillment',
        'wporg_options_page_html',
        'dashicons-clipboard',
        20
    );
}
