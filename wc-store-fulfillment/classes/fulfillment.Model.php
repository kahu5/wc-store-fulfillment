<?php

namespace fulfillmentmodel;

defined('ABSPATH') || exit;

class Products
{
// Gets all products that are published.
	public static function getOrders()
    {
        global $wpdb;
        $query = "SELECT p.ID AS order_id, post_date
            FROM
            {$wpdb->prefix}posts AS p
            INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
            WHERE
                p.post_type = 'shop_order'";
        $orders = $wpdb->get_results($query);
        return $orders;
    }
}
