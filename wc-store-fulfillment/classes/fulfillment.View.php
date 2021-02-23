<?php

namespace fulfillmentview;

defined('ABSPATH') || exit;

require_once __DIR__ . '/../classes/fulfillment.Model.php';

class Products
{
	function options_page_html() {
		?>
		<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<h3>Current Products</h3>
		<br>
		</div>
		<?php
	}

	function page_refresh() {
		$output ='<div class="wrap" style="float:right">
		<button><span id="refreshIcon" onclick="refreshPage()" class="dashicons dashicons-image-rotate"></span></button>
		</div>';
		echo $output;
	}

	function product_table_header() {
		$output ='<table><tr><th>Product</th><th>Type of Product</th><th>Attributes</th><th>Orders</th></tr>';
		echo $output;
	}

	function product_table_end() {
		$output ='</table>';
		echo $output;
	}
	

	    /**
     * Displays woocommerce products in a list.
     */
    public static function displayPublishedProducts($data)
    {
        $output = "";
        foreach ($data as $d) {
            $prod = $d->prod;
            $output .= "<tr>";
                $output .= "<td><strong>" . $d->post_title . "</strong></td>";
                $output .= "<td>" . ucwords($d->p_type) . "</td>";
                $output .= "<td></td>";
            if ($d->variations) {
                foreach($d->variations as $v) {
                    if ($v['attributes']['attribute_ground']) {
                        $output .= "<td>" . $v['attributes']['attribute_ground'] . "</td>";
                    } else if ($v['attributes']['attribute_roast']) {
                        $output .= "<td>" . $v['attributes']['attribute_roast'] . "</td>";
                    } else if ($v['attributes']['attribute_weight']) {
                        $output .= "<td>" . $v['attributes']['attribute_weight'] . "</td>";
                    }
                }
            }
			$output .= "<td style='text-align:center'>#</td>";
			$output .= "</tr>";
        }
    
        echo $output;
    }

}
