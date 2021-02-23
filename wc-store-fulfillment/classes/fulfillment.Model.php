<?php

namespace fulfillmentmodel;

defined('ABSPATH') || exit;

class Products
{
// Gets all orders that are published.
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

    /**
     * Gets all products that are published.
     */
    public static function getPublishedProducts()
    {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'status' => array( 'private', 'publish' ),
            'orderby' => 'ID',        
            'order' => 'DESC'
        );
        
        $posts = get_posts($args);

        foreach ($posts as $p) {
            // Get the product type
            $terms = get_the_terms($p->ID, 'product_type');
            $p->p_type = (!empty($terms)) ? sanitize_title(current($terms)->name) : 'simple';
            $prod = wc_get_product($p->ID);
            $p->prod = $prod;

            if ($p->p_type == 'variable' || $p->p_type == 'variable-subscription') {
                $p->variations = $prod->get_available_variations();
            }

        }
        return $posts;
    }

}
