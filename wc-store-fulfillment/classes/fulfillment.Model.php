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

    public static function getPublishedProductsSimplified($prod_id = 0)
    {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'status' => array( 'private', 'publish' ),
            'orderby' => 'ID',
            'order' => 'DESC'
        );

        $posts = get_posts($args);
        $newProds = [];
        foreach ($posts as $p) {
            // Get the product type
            $terms = get_the_terms($p->ID, 'product_type');
            $p->p_type = (!empty($terms)) ? sanitize_title(current($terms)->name) : 'simple';
            // echo "<pre>" . var_export($p->p_type, true) . "</pre>";
            $prod = wc_get_product($p->ID);
            $p->prod = $prod;

            if ($prod_id != 0 && $prod_id == $p->ID) {
                $newProds[] = ['id' => $p->ID, 'name' => $prod->name, 'selected' => 'selected'];
            } else {
                $newProds[] = ['id' => $p->ID, 'name' => $prod->name, 'selected' => ''];
            }

            if ($p->p_type == 'variable' || $p->p_type == 'variable-subscription') {
                $p->variations = $prod->get_available_variations();
                // echo "<pre>" . var_export($p->variations, true) . "</pre>";
                foreach ($p->variations as $v) {
                    if ($prod_id != 0 && $prod_id == $v['variation_id']) {
                        $s = 'selected';
                    } else {
                        $s = '';
                    }

                    if ($v['attributes']['attribute_segment']) {
                        $newProds[] = ['id' => $v['variation_id'], 'name' => " -- " . $v['attributes']['attribute_segment'], 'selected' => $s];
                    } else if ($v['attributes']['attribute_variants']) {
                        $newProds[] = ['id' => $v['variation_id'], 'name' => " -- " . $v['attributes']['attribute_variants'], 'selected' => $s];
                    }
                }
            }
        }
        return $newProds;
    }

    public static function getUserOrders($uid)
    {
        $customer_orders = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $uid,
            'post_type'   => wc_get_order_types(),
            'post_status' => array_keys( wc_get_order_statuses() ),
        )
    );

        return $customer_orders;
    }

    public static function getOrdersWithProducts($product)
    {
        global $wpdb;

        return $wpdb->get_col( "
            SELECT DISTINCT woi.order_id
            FROM {$wpdb->prefix}woocommerce_order_itemmeta as woim, 
                 {$wpdb->prefix}woocommerce_order_items as woi, 
                 {$wpdb->prefix}posts as p
            WHERE  woi.order_item_id = woim.order_item_id
            AND woi.order_id = p.ID
            AND p.post_status = 'wc-processing'
            AND woim.meta_key IN ( '_product_id', '_variation_id' )
            AND woim.meta_value LIKE '$product'
            ORDER BY woi.order_item_id DESC"
        );
    }

}
